FROM python:3.8-slim-buster
WORKDIR /app
COPY . .
RUN pip install -r requirements.txt
COPY API.py API.py
EXPOSE 5000
ENV FLASK_APP=API.py
ENV FLASK_DEBUG=1
ENV FLASK_PORT=5000
ENV FLASK_RUN_HOST=0.0.0.0
ENTRYPOINT [ "python" ]
CMD [ "API.py" ]
