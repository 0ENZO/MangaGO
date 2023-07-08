FROM python:3.8

WORKDIR /app

COPY requirements.txt .

RUN pip install --no-cache-dir -r requirements.txt

RUN apt-get update && apt-get install -y \
    tesseract-ocr \
    tesseract-ocr-jpn

COPY . .

ENTRYPOINT [ "/usr/local/bin/python", "-m", "awslambdaric" ]

CMD ["main.lambda_handler"]