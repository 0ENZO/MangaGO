<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set("display_errors", 1);
require "aws/aws-autoloader.php";
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

function format_url($chaine): array|string{
    $search  = array(' ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à');
    $replace = array('%20', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a');
    return str_replace($search, $replace, $chaine);
}

function encodePourURL($chaine): string{
    $encodageUTF8 = mb_convert_encoding($chaine, 'UTF-8');
    return rawurlencode($encodageUTF8);
}

function connect_API(): string{
//    return "http://127.0.0.1:5000";
    return "http://".getenv('API_ADRESSE');
}

function connexion_client(){
    $url_api_yougo = connect_API();
    $email = format_url($_POST['email_sign_in']);
    $password = format_url($_POST['password_sign_in']);
    $json = json_decode(file_get_contents($url_api_yougo."/connexion?m=".encodePourURL($email)."&p=".encodePourURL($password)), true)[0];
    if($json){
        $_SESSION['ID_USER'] = $json['ID'];
        $_SESSION['USER'] = $json['PSEUDO'];
        $_SESSION['EMAIL'] = $json['EMAIL'];
        $_SESSION['TYPECOMPTE'] = $json['TYPE'];
        return $json;
    }
    return null;
}

function inscription_client(): bool{
    $url_api_yougo = connect_API();
    $user = format_url($_POST['pseudo_sign_up']);
    $email = format_url($_POST['email_sign_up']);
    $password = format_url($_POST['password_sign_up']);
    $type_compte = 1;
    $success = false;
    $json = file_get_contents($url_api_yougo."/inscription?u=".encodePourURL($user)."&m=".encodePourURL($email)."&p=".encodePourURL($password)."&t=".encodePourURL($type_compte));
    if($json == "OK"){
        $success = true;
        $_SESSION['USER'] = $_POST['pseudo_sign_up'];
        $_SESSION['EMAIL'] = $_POST['email_sign_up'];
        $_SESSION['TYPECOMPTE'] = 1;
    }
    return $success;
}

function get_info_client(){
    $url_api_yougo = connect_API();
    $id_user = format_url($_SESSION['ID_USER']);
    return json_decode(file_get_contents($url_api_yougo."/info_client/".$id_user), true)[0];
}

function get_derniere_sortie(){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/last_release"), true);
}

function get_oeuvre_by_id($ID_OEUVRE){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/".$ID_OEUVRE), true)[0];
}

function get_oeuvre_by_scan_id($ID_SCAN){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/scan/oeuvre/".$ID_SCAN), true)[0];
}

function get_scan($ID_OEUVRE){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/scan/id/".$ID_OEUVRE), true);
}

function get_scan_by_id_fecth($ID_SCAN){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/scan/".$ID_SCAN), true)[0];
}

function get_page_by_id($ID_SCAN, $ID_PAGE){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/".$ID_SCAN."/".$ID_PAGE), true)[0];
}

function verification_status_client(){
    $tab_user = null;
    if(!isset($_SESSION['USER_ON']) || !$_SESSION['USER_ON']){
        echo '<script>document.location.replace("connexion.php");</script>';
    }
    else{
        $tab_user = get_info_client();
    }
    return $tab_user;
}

function get_bulle($ID_PAGE){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/scan/page/".$ID_PAGE), true);
}

function update_correction_bulle($ID_BULLE, $TXT): void{
    $url_api_yougo = connect_API();
    json_decode(file_get_contents($url_api_yougo."/correction?b=".$ID_BULLE."&txt=".encodePourURL($TXT)), true);
}

function get_id_scan($name_oeuvre, $numero_scan){
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/oeuvre/import/".encodePourURL($name_oeuvre)."/".$numero_scan), true)[0];
}

function import_chapter(): bool{
    $name_oeuvre = strtolower(str_replace(" ", "_", $_POST['name_oeuvre']));
    $number_chapter = $_POST['number_chapter'];

    $s3Client = new S3Client([
        'region' => getenv('AWS_REGION'),
        'version' => '2006-03-01',
        'credentials' => [
            'key' => getenv('AWS_ACCES_KEY'),
            'secret' => getenv('AWS_SECRET_KEY'),
        ]
    ]);
    $bucket = getenv('AWS_NAME_BUCKET');
    if (isset($_FILES["fileToUpload"])) {
        $fileCount = count($_FILES["fileToUpload"]["tmp_name"]);

        for ($i = 0; $i < $fileCount; $i++) {
            $file = $_FILES["fileToUpload"]["tmp_name"][$i];
            $key = "scan_brut/" . $name_oeuvre . "/" . $number_chapter . "/" . basename($_FILES["fileToUpload"]["name"][$i]);

            try {
                $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $key,
                    'SourceFile' => $file
                ]);
            } catch (AwsException $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    return true;
}

function update_total_pages($ID_SCAN): void{
    $url_api_yougo = connect_API();
    json_decode(file_get_contents($url_api_yougo."/correction/pages?id_scan=".$ID_SCAN), true);
}

function replacement($id_scan): void{
    $url_api_yougo = connect_API();
    json_decode(file_get_contents($url_api_yougo."/replacement/".$id_scan), true);
}

function import_via_url(){
    $link = $_POST['link_oeuvre'];
    $link = str_replace("https://", "", $link);
    $url_api_yougo = connect_API();
    return json_decode(file_get_contents($url_api_yougo."/scrapper/".$link), true);
}

function dl_scan_end($name_oeuvre, $numero_scan): void{
    $nomFichierZip = $name_oeuvre."_".$numero_scan.'.zip';
    $cheminFichierZip = 'scan_end/'.$name_oeuvre.'/'.$numero_scan.'/'.$nomFichierZip;

    try {
        // Création de l'objet S3Client
        $s3Client = new S3Client([
            'region' => getenv('AWS_REGION'),
            'version' => '2006-03-01',
            'credentials' => [
                'key' => getenv('AWS_ACCES_KEY'),
                'secret' => getenv('AWS_SECRET_KEY'),
            ]
        ]);
        $bucketName = getenv('AWS_NAME_BUCKET');

        // Téléchargement du fichier zip depuis S3
        $fichierZip = $s3Client->getObject([
            'Bucket' => $bucketName,
            'Key' => $cheminFichierZip
        ]);

        // Envoi du fichier zip au client pour le téléchargement
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$nomFichierZip.'"');
        header('Content-Length: ' . $fichierZip['ContentLength']);

        // Affichage du contenu du fichier ZIP
        echo $fichierZip['Body'];
        exit;
    } catch (AwsException $e) {
        echo "Une erreur s'est produite lors de l'interaction avec Amazon S3 : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Une erreur s'est produite : " . $e->getMessage();
    }
}