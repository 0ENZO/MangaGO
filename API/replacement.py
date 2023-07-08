import os
import textwrap
import boto3
import requests
from PIL import Image, ImageDraw, ImageFont


def fit_text_to_box(draw, position, text, font_path, max_width, max_height):
    font_size = max_width // 3
    font = ImageFont.truetype(font_path, font_size)

    # Calculate the average character width and maximum characters per line
    avg_char_width = sum(font.getlength(char) for char in text) / len(text)
    max_chars_per_line = max(1, int(max_width / avg_char_width))
    max_lines = max(1, int(max_height / (font.getlength(text))))

    # Wrap the text based on the maximum characters per line
    lines = textwrap.wrap(text, width=max_chars_per_line)
    ascent, descent = font.getmetrics()

    # Adjust the font size until the text fits within the bounding box
    while len(lines) > max_lines:
        font_size -= 1
        font = ImageFont.truetype(font_path, font_size)
        avg_char_width = sum(font.getlength(char) for char in text) / len(text)
        max_chars_per_line = max(1, int(max_width / avg_char_width))
        lines = textwrap.wrap(text, width=max_chars_per_line)
        ascent, descent = font.getmetrics()
        text_height = font.getmask(text).getbbox()[3] + descent
        max_lines = max(1, int(max_height / text_height))

    y = position[1]
    for line in lines:
        line_height = font.getmask(line).getbbox()[3] + descent
        draw.text((position[0], y), line, font=font, fill="black")
        y += line_height


def translate_whole_external_scan(path, original_texts, boxes):
    img_data = requests.get(path).content
    filename = path.split("/")[-1]
    name = os.path.splitext(filename)[0]
    extension = os.path.splitext(filename)[1]
    path_temp = '/tmp/' + filename
    with open(path_temp, 'wb') as handler:
        handler.write(img_data)

    font_path = "fonts/animeace2_reg.ttf"
    font_size = 20
    font = ImageFont.truetype(font_path, font_size)

    text_padding = 0

    image = Image.open(path_temp)
    draw = ImageDraw.Draw(image)

    for idx in range(len(original_texts)):
        text = original_texts[idx]
        coordinates = boxes[idx]

        # translated_text = translate_ja_to_fr(text)
        translated_text = text

        text_width = coordinates[2] - coordinates[0] - 2 * text_padding
        text_height = coordinates[3] - coordinates[1] - 2 * text_padding

        draw.rounded_rectangle([(coordinates[0], coordinates[1]), (coordinates[2], coordinates[3])], fill="#ffffff", radius=8)
        fit_text_to_box(draw, (coordinates[0] + text_padding, coordinates[1] + text_padding), translated_text,
                        font_path, text_width, text_height)
    image.save(f"{name}{extension}")
    elements = path.split("/")[-4:]
    new_path = "/".join(elements)
    path_output = new_path.replace("scan_brut", "scan_end")
    send_to_bucket(f"{name}{extension}", path_output)


def send_to_bucket(image_path_input, image_path_output):
    client = boto3.client("s3")
    client.upload_file(image_path_input, os.environ['AWS_NAME_BUCKET'], image_path_output)
    print(f'Enregistrer dans :{image_path_output}')
    # os.remove("./" + image_path_input)
