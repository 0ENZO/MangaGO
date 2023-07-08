import requests
import cv2
import pytesseract
from PIL import ImageDraw, ImageFont
import PIL.Image
import easyocr
import boto3
import os
from collections import deque

def is_close(r1, r2, threshold):
    x1, y1, x2, y2 = r1
    x3, y3, x4, y4 = r2

    return not (x2 < x3 - threshold or x1 > x4 + threshold or y2 < y3 - threshold or y1 > y4 + threshold)


def merge_all_coords(all_coords, threshold=20):
    # Convert all_coords to a deque for efficient removal of elements
    all_coords = deque(all_coords)
    merged_coords = []

    while all_coords:
        # Take a rectangle from all_coords
        current = all_coords.popleft()
        # Remember the index of the rectangle to potentially remove it later
        index = 0

        # Loop through the rest of the rectangles to find any that need to be merged
        for other in all_coords:
            print(current, other)
            if is_close(current, other, threshold):
                x1, y1, x2, y2 = current
                x3, y3, x4, y4 = other
                # Merge the rectangles
                current = (min(x1, x3), min(y1, y3), max(x2, x4), max(y2, y4))
                # Remove the merged rectangle from all_coords
                del all_coords[index]
                # Add the merged rectangle to the start of all_coords to check for further merges
                all_coords.appendleft(current)
                break
            index += 1
        else:
            # If no merge happened in the loop, add the rectangle to merged_coords
            merged_coords.append(current)

    return merged_coords


def detect_text_zones(image_path):
    image = cv2.imread(image_path)
    reader = easyocr.Reader(['ja'], gpu=False, model_storage_directory='/tmp', user_network_directory='/tmp')
    result = reader.readtext(image_path, detail=1)
    # text_coords = [(int(item[0][0][0]), int(item[0][0][1]), int(item[0][2][0]), int(item[0][2][1])) for item in result]
    text_coords = []
    for item in result:
        coordinates = item[0]
        x_min, y_min = int(coordinates[0][0]), int(coordinates[0][1])
        x_max, y_max = int(coordinates[2][0]), int(coordinates[2][1])        
        text_coords.append((x_min, y_min, x_max, y_max))
    print(f'text_coords: {text_coords}')
    text = merge_all_coords(text_coords)
    return text
    image = cv2.imread(image_path)
    reader = easyocr.Reader(['ja'], gpu=False, model_storage_directory='/tmp', user_network_directory='/tmp')
    result = reader.readtext(image_path, detail=1)

    text_zone_images = []
    text_coords = []
    for item in result:
        coordinates = item[0]
        x_min, y_min = int(coordinates[0][0]), int(coordinates[0][1])
        x_max, y_max = int(coordinates[2][0]), int(coordinates[2][1])
        text_zone_image = image[y_min:y_max, x_min:x_max]

        # Ensure that text_zone_image is grayscale
        if text_zone_image.ndim == 3:
            text_zone_image = cv2.cvtColor(text_zone_image, cv2.COLOR_BGR2GRAY)

        _, threshold_image = cv2.threshold(text_zone_image, 0, 255, cv2.THRESH_BINARY | cv2.THRESH_OTSU)

        text_zone_images.append(threshold_image)
        text_coords.append((x_min, y_min, x_max, y_max))

    return text_zone_images, text_coords


def add_text_boxes(image_path, output_path, boxes):
    global extracted_text
    image_pil = PIL.Image.open(image_path).convert('RGBA')
    draw = ImageDraw.Draw(image_pil)
    text = {}
    font = ImageFont.truetype("arial.ttf", size=18)

    row_threshold = 50
    # boxes.sort(key=lambda box: box[1])
    boxes.sort(key=lambda box: (box[1], -box[0]))

    rows = []
    current_row = []

    for box in boxes:
        if not current_row:
            current_row.append(box)
        else:
            last_box_y = current_row[-1][1]
            if abs(box[1] - last_box_y) <= row_threshold:
                current_row.append(box)
            else:
                rows.append(current_row)
                current_row = [box]

    if current_row:
        rows.append(current_row)

    for row in rows:
        row.sort(key=lambda box: box[0], reverse=True)

    sorted_boxes = [box for row in rows for box in row]

    for i, box in enumerate(sorted_boxes):
        x_min, y_min, x_max, y_max = box

        image = cv2.imread(image_path)
        text_zone_image = image[y_min:y_max, x_min:x_max]

        gray_image = cv2.cvtColor(text_zone_image, cv2.COLOR_BGR2GRAY)
        _, threshold_image = cv2.threshold(gray_image, 0, 255, cv2.THRESH_BINARY | cv2.THRESH_OTSU)

        extracted_text = pytesseract.image_to_string(cv2.cvtColor(threshold_image, cv2.COLOR_GRAY2RGB),
                                                     config='-l jpn --oem 3 --psm 6')
        extracted_text = extracted_text.replace('\n', '')
        text[i] = extracted_text

        draw.rectangle((x_min, y_min, x_max, y_max), outline="red")

        text_width, text_height = font.getsize(str(i + 1))
        draw.text((x_max + 7, y_min + 5), str(i + 1), font=font, fill="red")

    image_pil_rgb = image_pil.convert('RGB')

    image_pil_rgb.save(f"/tmp/{output_path}")
    return text


def send_to_bucket(image_path_input, image_path_output):
    client = boto3.client("s3")
    client.upload_file(image_path_input, os.environ['NAME_BUCKET_S3'], image_path_output)
    print(f'Enregistrer dans :{image_path_output}')


def request_api(manga_name, num_chapter, num_page, text, link, position):
    app = os.environ['URL_API']
    url = f"http://{app}/create/{manga_name}/{num_chapter}/{num_page}/{text.replace('?', '%3F').replace('#', '')}?lien={link}&p={position}"

    response = requests.get(url)
    print(url)
    print(response.status_code)
    print("all is ok in the api")


def lambda_handler(event, context):
    key = event['Records'][0]['s3']['object']['key']
    image_path_input = f"https://s3.amazonaws.com/{os.environ['NAME_BUCKET_S3']}/{key}"
    image_path_output = key.replace("scan_brut", "scan_preprocess")
    name_file = key.split("/")[3]
    link = f"/{image_path_output.replace('/', '$')}"
    img_data = requests.get(image_path_input).content
    with open('/tmp/temp.jpg', 'wb') as handler:
        handler.write(img_data)
    text_coords = detect_text_zones('/tmp/temp.jpg')
    print("Texte coordonées :", text_coords)
    text = add_text_boxes('/tmp/temp.jpg', name_file, text_coords)
    print("Texte extrait :", text)
    send_to_bucket(f"/tmp/{name_file}", image_path_output)
    for t in range(0, len(text)):
        request_api(key.split("/")[1], key.split("/")[2], key.split("/")[3].split(".")[0],
                    text[t].replace("/x0c", "").replace("/", ""),
                    link, text_coords[t])


if __name__ == "__main__":
    print(0)
