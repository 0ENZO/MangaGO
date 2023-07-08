from google.cloud import translate_v2 as translate
from conf import *


def translate_text(text, target_language, source_language):
    translate_client = translate.Client()

    result = translate_client.translate(
        text, target_language=target_language, source_language=source_language)

    print(f'Text: {result["input"]}')
    print(f'Translation: {result["translatedText"]}')
    print(f'Detected source language: {result["detectedSourceLanguage"]}')


if __name__ == "__main__":
    GOOGLE_APPLICATION_CREDENTIALS = GOOGLE_KEY_PATH
    translate_text("こんにちは、世界", "fr", "ja")  # "こんにちは、世界" means "Hello, World"
