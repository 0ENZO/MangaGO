import os
import requests
from bs4 import BeautifulSoup
import boto3


def get_manga_chapter(manga_name="", chapter="", link="", multiple=False):
    try:
        if link != "":
            mangalink = f"https://{link}/{manga_name}/{chapter}"
        else:
            mangalink = f'https://raw.senmanga.com/{manga_name}/{chapter}'
        response = requests.get(mangalink)
        if response.status_code == 404:
            return False
        response_html = response.text
        soup = BeautifulSoup(response_html, 'lxml')
        num_page = 1
        tmp_folder = f"./tmp/"
        pages = []
        while True:
            image_tag = soup.find("img", {"class": "picture"})
            if not image_tag:
                break
            image_url = image_tag['src']
            if 'https' not in image_url:
                image_url = 'https:' + image_url
            pages.append({'image_url': image_url})

            # Download and save the image
            img_response = requests.get(image_url)
            if not os.path.exists(tmp_folder):
                os.makedirs(tmp_folder)
            img_filename = os.path.join(tmp_folder, f"{num_page}.jpg")
            folder_name = f"scan_brut/{manga_name.lower()}/{chapter}/{num_page}.jpg"
            num_page += 1
            with open(img_filename, "wb") as img_file:
                img_file.write(img_response.content)

            send_to_bucket(img_filename, folder_name)
            next_link_div = soup.find("div", {"class": "next"})
            if not next_link_div:
                break
            next_link = next_link_div.find("a")
            if not next_link:
                break
            next_url = next_link['href']
            close_url = f'https://raw.senmanga.com/{manga_name}/{int(chapter) + 1}'
            if multiple:
                if next_url == close_url:
                    chapter += 1
                if next_url == f'https://raw.senmanga.com/{manga_name}':
                    break
            else:
                if next_url == close_url:
                    break
            next_full_url = f'{next_url}'
            next_response = requests.get(next_full_url)
            soup = BeautifulSoup(next_response.text, 'lxml')

        if os.path.exists(tmp_folder):
            for fichier in os.listdir(tmp_folder):
                chemin_fichier = os.path.join(tmp_folder, fichier)
                os.remove(chemin_fichier)
            os.rmdir(tmp_folder)
        return True

    except (AttributeError, requests.exceptions.ConnectionError) as e:
        return {"error": str(e)}


def send_to_bucket(image_path_input, image_path_output):
    client = boto3.client("s3")
    # client.upload_file(image_path_input, 'bucket-mangago-esgi', image_path_output)
    client.upload_file(image_path_input, os.environ['AWS_NAME_BUCKET'], image_path_output)
    print(f'Enregistrer dans :{image_path_output}')