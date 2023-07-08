import os
import manga109api
from conf import *
from replacement import *
from PIL import Image, ImageDraw, ImageFont
from transformers import MarianMTModel, MarianTokenizer


class Translator:
    def __init__(self):
        # tokenizer = MarianTokenizer.from_pretrained("Helsinki-NLP/opus-mt-ja-fr")
        # model = MarianMTModel.from_pretrained("Helsinki-NLP/opus-mt-ja-fr")

        ja_en_model_name = "Helsinki-NLP/opus-mt-ja-en"
        self.ja_en_tokenizer = MarianTokenizer.from_pretrained(ja_en_model_name)
        self.ja_en_model = MarianMTModel.from_pretrained(ja_en_model_name)

        # Load English-to-French model and tokenizer
        en_fr_model_name = "Helsinki-NLP/opus-mt-en-fr"
        self.en_fr_tokenizer = MarianTokenizer.from_pretrained(en_fr_model_name)
        self.en_fr_model = MarianMTModel.from_pretrained(en_fr_model_name)

    def translate_ja_to_fr(self, text):
        # Translate Japanese text to English
        ja_en_inputs = self.ja_en_tokenizer(text, return_tensors="pt")
        ja_en_outputs = self.ja_en_model.generate(**ja_en_inputs)
        english_text = self.ja_en_tokenizer.decode(ja_en_outputs[0], skip_special_tokens=True)

        # Translate English text to French
        en_fr_inputs = self.n_fr_tokenizer(english_text, return_tensors="pt")
        en_fr_outputs = self.en_fr_model.generate(**en_fr_inputs)
        french_text = self.en_fr_tokenizer.decode(en_fr_outputs[0], skip_special_tokens=True)

        return french_text

    def translate_whole_scan(self, book_title, page_index):
        p = manga109api.Parser(DATA_PATH)
        path = p.img_path(book=book_title, index=page_index)
        annotation = p.get_annotation(book=book_title)

        font_size = 20
        font = ImageFont.truetype(FONT_PATH, font_size)

        text_padding = 8

        image = Image.open(path)
        draw = ImageDraw.Draw(image)

        for bubble_text in range(0, len(annotation["page"][page_index]["text"])):
            text = annotation["page"][page_index]["text"][bubble_text]["#text"]
            coordinates = annotation["page"][page_index]["text"][bubble_text]

            translated_text = self.translate_ja_to_fr(text)

            text_width = coordinates["@xmax"] - coordinates["@xmin"] - 2 * text_padding
            text_height = coordinates["@ymax"] - coordinates["@ymin"] - 2 * text_padding

            # Draw the translated text over the original text region
            # filled_image = fill_text_area(image, coordinates)
            # filled_image = fill_bubble(image, coordinates)
            # draw = ImageDraw.Draw(filled_image)

            draw.rectangle([(coordinates["@xmin"], coordinates["@ymin"]), (coordinates["@xmax"], coordinates["@ymax"])],
                           fill="#ffffff")
            fit_text_to_box(draw, (coordinates["@xmin"] + text_padding, coordinates["@ymin"] + text_padding),
                            translated_text, FONT_PATH, text_width, text_height)

        image.save(f"translated_scan_{book_title}_{page_index}_3.jpg")
        image.show()


if __name__ == "__main__":
    translator = Translator()
    translator.translate_whole_scan("AisazuNihaIrarenai", 23)
