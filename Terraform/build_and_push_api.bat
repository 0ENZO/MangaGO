@echo off
FOR /F "tokens=1* delims==" %%A IN (.env) DO SET "%%A=%%B"

cd \Applications\MAMP/htdocs/Projet/MangaGO/API

REM Creation of the Docker image
docker build -t %IMAGE_NAME% -f %DOCKERFILE_PATH% .

REM Connection to ECR
FOR /F "tokens=* USEBACKQ" %%F IN (`aws ecr get-login-password --region %AWS_REGION%`) DO SET "PASSWORD=%%F"
echo %PASSWORD% | docker login --username AWS --password-stdin %AWS_ACCOUNT_ID%.dkr.ecr.%AWS_REGION%.amazonaws.com

REM Docker image tagging
docker tag %IMAGE_NAME%:latest %AWS_ACCOUNT_ID%.dkr.ecr.%AWS_REGION%.amazonaws.com/%REPO_NAME%:latest

REM Sending the Docker image to ECR
docker push %AWS_ACCOUNT_ID%.dkr.ecr.%AWS_REGION%.amazonaws.com/%REPO_NAME%:latest
