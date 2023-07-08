# export FLASK_APP=API
# flask run
from datetime import datetime
import json
import mysql.connector
from flask import Flask, request
import pymysql
import os
import scrapper
import replacement
import boto3
import zipfile
import io

pymysql.install_as_MySQLdb()

app = Flask(__name__)


def connexion_db():
    engine = mysql.connector.connect(host=os.environ['DB_HOST'],
                                     database=os.environ['DB_NAME'],
                                     user=os.environ['DB_USER'],
                                     password=os.environ['DB_PASSWORD'])
    return engine


# def connexion_db():
#     engine = mysql.connector.connect(host="rds-pa.c6mihiou3qzx.us-east-1.rds.amazonaws.com",
#                                      database="MangaGO",
#                                      user="admin_pa",
#                                      password="azertyuiop1")
#     return engine


# def connexion_db():
#     engine = mysql.connector.connect(host="localhost",
#                                      database="MangaGO",
#                                      user="root",
#                                      password="root")
#     return engine


@app.route('/healthcheck')
def health_check():
    return "OK"


@app.route('/connexion')
def connexion_client():
    connection = connexion_db()
    email = request.args.get('m')
    password = request.args.get('p')
    select = "SELECT * FROM COMPTE WHERE EMAIL='" + email + "' and PASSWORD='" + password + "'"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select)
    resultat = cursor.fetchall()
    return json.dumps(resultat)


@app.route('/inscription')
def inscription_client():
    connection = connexion_db()
    pseudo = request.args.get('u')
    email = request.args.get('m')
    password = request.args.get('p')
    type_compte = request.args.get('t')
    select = "INSERT INTO COMPTE(`PSEUDO`, `EMAIL`, `PASSWORD`, `TYPE`) VALUES (%s, %s, %s, %s)"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (pseudo, email, password, type_compte))
    connection.commit()
    return "OK"


@app.route('/info_client/<id_user>')
def get_info_client(id_user=int):
    connection = connexion_db()
    select = "SELECT * FROM COMPTE WHERE ID=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_user,))
    resultat = cursor.fetchall()
    return json.dumps(resultat)


@app.route('/oeuvre/last_release')
def get_derniere_sortie():
    connection = connexion_db()
    select = "SELECT OEUVRE.ID, OEUVRE.TITRE, OEUVRE.AUTEUR,OEUVRE.IMAGE_COUVERTURE FROM OEUVRE INNER JOIN SCAN ON " \
             "OEUVRE.ID = SCAN.ID_OEUVRE ORDER BY `LAST_RELEASE` DESC;"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select)
    resultat = cursor.fetchall()
    return json.dumps(resultat)


@app.route('/oeuvre/import/<name_scan>/<numero_scan>')
def get_id_scan(name_scan=str, numero_scan=int):
    connection = connexion_db()
    select = "CALL maxID(%s, %s)"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (name_scan, numero_scan))
    resultat = cursor.fetchall()
    return json.dumps(resultat)


@app.route('/oeuvre/<id_oeuvre>')
def get_oeuvre_by_id(id_oeuvre=int):
    connection = connexion_db()
    select = "SELECT * FROM OEUVRE WHERE ID=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_oeuvre,))
    resultat = cursor.fetchall()
    return json.dumps(resultat)


@app.route('/scan/oeuvre/<id_scan>')
def get_oeuvre_by_scan_id(id_scan=int):
    connection = connexion_db()
    select = "SELECT OEUVRE.TITRE, SCAN.NUMERO FROM SCAN INNER JOIN OEUVRE ON SCAN.ID_OEUVRE = OEUVRE.ID WHERE SCAN.ID =%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_scan,))
    resultat = cursor.fetchall()
    return json.dumps(resultat)


@app.route('/oeuvre/scan/id/<id_oeuvre>')
def get_scan(id_oeuvre=int):
    connection = connexion_db()
    select = "SELECT * FROM SCAN WHERE ID_OEUVRE=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_oeuvre,))
    resultat = cursor.fetchall()
    return json.dumps(resultat, default=str)


@app.route('/oeuvre/scan/<id_scan>')
def get_scan_by_id(id_scan=int):
    connection = connexion_db()
    select = "SELECT * FROM SCAN WHERE ID=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_scan,))
    resultat = cursor.fetchall()
    return json.dumps(resultat, default=str)


@app.route('/oeuvre/<id_scan>/<id_page>')
def get_page_by_id(id_scan=int, id_page=int):
    connection = connexion_db()
    select = "SELECT * FROM PAGES WHERE ID_SCAN=%s AND NUMERO=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_scan, id_page))
    resultat = cursor.fetchall()
    return json.dumps(resultat, default=str)


@app.route('/oeuvre/scan/page/<id_page>')
def get_bulle_by_id_page(id_page=int):
    connection = connexion_db()
    select = "SELECT * FROM BULLES WHERE ID_PAGE=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_page,))
    resultat = cursor.fetchall()
    return json.dumps(resultat, default=str)


@app.route('/correction')
def update_correction_bulle():
    connection = connexion_db()
    id_bulle = request.args.get('b')
    txt_correction = request.args.get('txt')
    select = "UPDATE BULLES SET TEXT_CO = %s WHERE ID=%s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (txt_correction, id_bulle))
    connection.commit()
    return "OK"


@app.route('/correction/pages')
def update_correction_total_pages():
    connection = connexion_db()
    id_scan = request.args.get('id_scan')
    select = "CALL CountTotalPages(%s)"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (id_scan,))
    connection.commit()
    return "OK"


@app.route('/scrapper/<name_oeuvre>/<number_chapter>')
def scrap_scan(name_oeuvre=str, number_chapter=int):
    if Scrapper.get_manga_chapter(name_oeuvre, number_chapter):
        return json.dumps("OK", default=str)
    else:
        return json.dumps("KO", default=str)


@app.route('/scrapper/<link>/<name_oeuvre>/<number_chapter>')
def scrap_scan_url(link=str, name_oeuvre=str, number_chapter=int):
    if Scrapper.get_manga_chapter(name_oeuvre, number_chapter, link):
        return json.dumps("OK", default=str)
    else:
        return json.dumps("KO", default=str)


@app.route('/replacement/<id_scan>')
def replacement_pages(id_scan=int):
    connection = connexion_db()
    x = 0

    select_total_pages = "SELECT SCAN.TOTAL_PAGE FROM SCAN WHERE SCAN.ID = %s;"
    cursor_total_pages = connection.cursor(dictionary=True)
    cursor_total_pages.execute(select_total_pages, (id_scan,))
    total_pages = cursor_total_pages.fetchone()['TOTAL_PAGE']

    while x < total_pages:
        lien_img = ""
        tab_text = []
        tab_postion = []
        x += 1
        select = "SELECT BULLES.TEXT_TRAD, BULLES.TEXT_CO, BULLES.POSITION, PAGES.LIEN_IMAGE FROM BULLES INNER JOIN PAGES ON BULLES.ID_PAGE = PAGES.ID INNER JOIN SCAN ON PAGES.ID_SCAN = SCAN.ID WHERE SCAN.ID = %s AND PAGES.NUMERO = %s;"
        cursor = connection.cursor(dictionary=True)
        cursor.execute(select, (id_scan, x))
        resultat = cursor.fetchall()
        for row in resultat:
            lien_img = row['LIEN_IMAGE']
            tab_postion.append(row['POSITION'])
            if row['TEXT_CO'] is None:
                text = row['TEXT_TRAD']
            else:
                text = row['TEXT_CO']
            tab_text.append(text)
        print(tab_postion)
        tab_postion = [eval(pos) for pos in tab_postion]
        replacement.translate_whole_external_scan(
            "https://" + os.environ['S3_BUCKET_PATH'] + lien_img.replace("scan_preprocess", "scan_brut"), tab_text,
            tab_postion)

    # select_title = "SELECT OEUVRE.TITRE, SCAN.NUMERO FROM SCAN INNER JOIN OEUVRE ON SCAN.ID_OEUVRE = OEUVRE.ID WHERE SCAN.ID = %s"
    # cursor_title = connection.cursor(dictionary=True)
    # cursor_title.execute(select_title, (id_scan,))
    # title = cursor_title.fetchone()
    # zip_file(title['TITRE'], title['NUMERO'])
    return "OK"


@app.route('/create/<name_oeuvre>/<number_scan>/<number_page>/<txt_bulle>')
def insert_or_update_bulle(name_oeuvre=str, number_scan=int, number_page=int, txt_bulle=str):
    connection = connexion_db()
    lien = request.args.get('lien').replace("$", "/")
    position = request.args.get('p')
    select = "CALL InsertOrUpdateBulles(%s, %s, %s, %s, %s, %s, %s, %s)"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (name_oeuvre, "", number_scan, datetime.now(), number_page, lien, txt_bulle, position))
    connection.commit()
    return "OK"


@app.route('/texts/<name_oeuvre>/<number_scan>/<number_page>')
def get_bulle_translate(name_oeuvre=str, number_scan=int, number_page=int):
    connection = connexion_db()
    select = "SELECT BULLES.ID, BULLES.TEXT_VO FROM OEUVRE INNER JOIN SCAN ON OEUVRE.ID = SCAN.ID_OEUVRE INNER JOIN PAGES ON SCAN.ID = PAGES.ID_SCAN INNER JOIN BULLES ON PAGES.ID = BULLES.ID_PAGE WHERE OEUVRE.TITRE = %s AND SCAN.NUMERO = %s AND PAGES.NUMERO = %s ORDER BY BULLES.ID ASC"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (name_oeuvre, number_scan, number_page))
    resultat = cursor.fetchall()
    return json.dumps(resultat, default=str)


@app.route('/translate/<id_bulles>/<txt_bulle>')
def update_bulle_translate(id_bulles=int, txt_bulle=str):
    connection = connexion_db()
    select = "UPDATE BULLES SET TEXT_TRAD=%s WHERE BULLES.ID = %s"
    cursor = connection.cursor(dictionary=True)
    cursor.execute(select, (txt_bulle, id_bulles))
    connection.commit()
    return "OK"


@app.errorhandler(404)
def page_not_found(error):
    return json.dumps("KO", default=str)


def zip_file(name_oeuvre, numero_scan):
    try:
        # Nom du bucket S3 et chemin du dossier contenant les fichiers
        bucket_name = os.environ['AWS_NAME_BUCKET']
        dossier_s3 = f'scan_end/{name_oeuvre}/{numero_scan}'

        # Création d'un objet S3 client
        s3_client = boto3.client('s3')

        # Création d'un objet zip en mémoire
        archive = zipfile.ZipFile(io.BytesIO(), 'w', zipfile.ZIP_DEFLATED)

        # Récupération des objets du dossier S3
        objects = s3_client.list_objects(Bucket=bucket_name, Prefix=dossier_s3)['Contents']
        for obj in objects:
            # Vérification si l'objet correspond à un fichier
            if obj['Key'] != dossier_s3 + '/':
                # Téléchargement du fichier depuis S3
                fichier = s3_client.get_object(Bucket=bucket_name, Key=obj['Key'])['Body'].read()
                # Ajout du fichier à l'archive
                nom_fichier = obj['Key'].replace(dossier_s3 + '/', '')
                archive.writestr(nom_fichier, fichier)

        # Enregistrement de l'archive dans un fichier zip
        nom_fichier_zip = f'{name_oeuvre}_{numero_scan}.zip'
        with open(nom_fichier_zip, 'wb') as f:
            f.write(archive.fp.getvalue())

        s3_client.upload_file(nom_fichier_zip, bucket_name, dossier_s3 + "/" + nom_fichier_zip)

        # Fermeture de l'archive
        archive.close()

    except Exception as e:
        print("Une erreur s'est produite lors de la création de l'archive zip :", str(e))


if __name__ == '__main__':
    app.run(host='0.0.0.0', debug=True, port=5000)
