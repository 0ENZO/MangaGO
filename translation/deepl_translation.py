import manga109api
import os
import json
import requests
import textwrap
import manga109api
from conf import *
import pandas as pd
from tqdm import tqdm
import xml.etree.ElementTree as ET


class APILimits(Exception):
    def __init__(self, message="Please be careful. API uses fre limits will soon be reached."):
        self.message = message
        super().__init__(self.message)


def deepl_translation(text):
    url = "https://api-free.deepl.com/v2/translate"
    headers = {"Content-Type": "application/x-www-form-urlencoded"}
    data = {
        "text": text,
        "source_lang": source_lang,
        "target_lang": target_lang,
        "auth_key": DEEPL_API_KEY,
    }

    response = requests.post(url, headers=headers, data=data)

    if response.status_code == 200:
        json_data = response.json()
        translated_text = json_data["translations"][0]["text"]
        return translated_text
    else:
        raise Exception(f"DeepL API request failed with status code {response.status_code}: {response.text}")


def check_usage():
    url = "https://api-free.deepl.com/v2/usage"
    headers = {"Content-Type": "application/x-www-form-urlencoded"}
    data = {"auth_key": DEEPL_API_KEY}

    response = requests.post(url, headers=headers, data=data)
    used_characters = response.json()["character_count"]

    # if used_characters > 485000:
    #   raise APILimits(f"used_characters: {used_characters}")

    return used_characters


def estimate_characters_use(file_path):
    count = 0
    try:
        tree = ET.parse(file_path)
        root = tree.getroot()
        for text in root.iter('text'):
            count += len(text.text)

        return count

    except Exception as e:
        print(f"Error: {e}")
        return None


def translate_whole_xml(file_path, output_filename):
    used_characters = check_usage()
    characters_use = estimate_characters_use(file_path)

    print(f"\nused_characters: {used_characters} & estimate_characters_use: {characters_use}")

    if character_limit - (used_characters + 15000) > characters_use:
        try:
            tree = ET.parse(file_path)
            root = tree.getroot()

            # Find the last translated text tag, if any
            last_translated_text = None
            for text in root.iter('text'):
                if 'translation' in text.attrib:
                    last_translated_text = text

            # Find the index of the last translated text tag
            if last_translated_text is not None:
                start_index = list(root.iter('text')).index(last_translated_text) + 1
            else:
                start_index = 0

            for text in list(root.iter('text'))[start_index:]:
                original_text = text.text
                try:
                    translated_text = deepl_translation(original_text)
                except APILimits as e:
                    print(f"\nAPILimits exception {e} while translating text: {original_text}")
                    break
                except Exception as e:
                    print(f"\nError during translation: {e} while translating text: {original_text}")

                # print(f"Translated text: {translated_text}")
                text.set("translation", translated_text)

                # print(ET.tostring(root, encoding="unicode", xml_declaration=True))

                with open(output_filename, "wb") as output_file:
                    output_file.write(ET.tostring(root, encoding="utf-8", xml_declaration=True))

        except Exception as e:
            print(f"Error: {e}")

    else:
        return (
            f"DeepL API - Insufficient characters available. Current use: {used_characters} & max use: {character_limit}")


def gather_dataset(root_path):
    files = sorted(os.listdir(root_path))
    translated_books = [item for item in files if item.endswith("_translated.xml")]
    data = []

    for book in translated_books:
        path = os.path.join(root_path, book)

        try:
            tree = ET.parse(path)
            root = tree.getroot()

            for text in list(root.iter('text'))[0:]:
                original_text = text.text
                translated_text = text.attrib.get('translation')
                data.append({'text': original_text, 'translation': translated_text})

        except Exception as e:
            print(f"Error: {e}")

    df = pd.DataFrame(data)
    output_filename = 'deepl_translated_df_' + str(len(translated_books)) + '.csv'
    df.to_csv(output_filename, index=False, sep=';', encoding="utf-8")

    return output_filename


def open_dataset_fully_translated(file_path):
    text_en_list = []
    text_ja_list = []

    with open(file_path, 'r') as f:
        data = json.load(f)

    for book in data:
        for page in book['pages']:
            for text in page['text']:
                if 'text_en' in text:
                    text_en_list.append(text['text_en'])
                if 'text_ja' in text:
                    text_ja_list.append(text['text_ja'])

    source_lang = "EN"
    target_lang = "FR"
    en_fr_translations = [deepl_translation(text) for text in text_en_list]

    source_lang = "JA"
    ja_fr_translations = [deepl_translation(text) for text in text_ja_list]

    data = {'ja': text_ja_list, 'ja_to_fr': ja_fr_translations, 'en': text_en_list, 'en_to_fr': en_fr_translations}
    df = pd.DataFrame(data)

    df_filename = 'open_mantra_translated_df.csv'
    df.to_csv(df_filename, sep=';', encoding="utf-8", index=False)

    return df_filename


if __name__ == "__main__":
    source_lang = "JA"
    target_lang = "FR"

    used_characters = 500
    character_limit = 500000

    root_path = os.path.join(DATA_PATH, "annotations/")
    books = sorted(os.listdir(root_path))

    for i in tqdm(range(55, 60)):
        print(f"\ntranslating {books[i]}")
        path = os.path.join(root_path, books[i])
        output_filename = os.path.splitext(books[i])[0] + '_translated' + os.path.splitext(books[i])[1]
        output_path = os.path.join(root_path, output_filename)

        translate_whole_xml(path, output_path)
