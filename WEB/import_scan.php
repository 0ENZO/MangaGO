<?php include_once('fonctions.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>PA | Import</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-onix-digital.css">
    <link rel="stylesheet" href="assets/css/animated.css">
    <link rel="stylesheet" href="assets/css/owl.css">

    <!-- Favicon -->
    <link rel="icon" href="assets/images/logo.ico" type="image/x-icon">
</head>
<body>
<?php
    $tab_user = verification_status_client();
?>

<script>
    function updateProgressBar(idScan) {
        const progressBar = document.querySelector('.progress-bar-p');
        const progressText = document.querySelector('.progress-bar-p-text');
        let progress = 0;
        const interval = setInterval(function () {
            progress += 1;
            progressBar.style.width = progress + '%';
            progressText.innerHTML = progress + '%';
            if (progress > 98) {
                progressBar.style.width = progress + 10 + '%';
            }
            if (progress > 100) {
                progressText.innerHTML = 100 + '%';
            }
            if (progress >= 110) {
                window.location.replace("./affichage_scan.php?s="+idScan.Resultat+"&p=1");
                clearInterval(interval);
            }
        }, 600);
    }
</script>

<style>
    .progress-bar-p-container {
        margin-top: 10px;
        width: 500px;
        height: 30px;
        background-color: #ff695f;
        border-radius: 15px;
        overflow: hidden;
    }

    .progress-bar-p {
        height: 100%;
        background-color: #03a4ed;
        width: 0;
        transition: width 0.5s ease;
        border-radius: 15px;
        margin-left: -15px; /* Décalage négatif pour compenser le rayon de bordure */
    }

    .progress-bar-p-text {
        transform: translate(0%, -103%);
        color: #fff;
    }
</style>

<!-- ***** Preloader Start ***** -->
<div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>
<!-- ***** Preloader End ***** -->

<!-- ***** Header Area Start ***** -->
<header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.php" class="logo">
                        <img src="assets/images/logo.png" alt="">
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li class="scroll-to-section"><a href="index.php#top">Accueil</a></li>
                        <li class="scroll-to-section"><a href="import_scan.php">Importer vos scans</a></li>
                        <li class="scroll-to-section"><a href="index.php#about">A propos</a></li>
                        <li class="scroll-to-section"><a href="index.php#portfolio">Dernières sorties</a></li>
                        <li class="scroll-to-section">
                            <div class="main-red-button-hover"><a
                                        href="dashboard.php"><?php echo $tab_user["PSEUDO"]; ?></a></div>
                        </li>
                    </ul>
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->
<div id="portfolio" class="our-portfolio section">
    <div class="portfolio-left-dec">
        <img src="assets/images/services-right-dec2.png" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading">
                    <h2>Importer vos <em>scans</em></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row" style="flex-direction: column;">
            <div class="col-lg-6 align-self-center">
                <form id="upload_scan" action="" method="post" class="connexion" enctype="multipart/form-data">
                    <div class="row">
                        <?php if(isset($_GET['link'])):?>
                            <fieldset>
                                <label for="link_oeuvre"></label><input type="text" name="link_oeuvre" id="link_oeuvre" placeholder="raw.senmanga.com/One_Piece/1060" required>
                            </fieldset>
                        <?php else: ?>
                            <fieldset>
                                <label for="name_oeuvre"></label><input type="text" name="name_oeuvre" id="name_oeuvre" placeholder="Nom de l'oeuvre" required>
                            </fieldset>
                            <fieldset>
                                <label for="number_chapter"></label><input type="number" name="number_chapter" id="number_chapter" placeholder="N° Chapître" required>
                            </fieldset>
                            <fieldset>
                                <input style="border-bottom: 0;" type="file" name="fileToUpload[]" id="fileToUpload" multiple required>
                            </fieldset>
                        <?php endif; ?>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" name="submit_upload_scan" id="submit_upload_scan" class="main-button">Importer</button>
                                <div class="main-red-button-hover" style="float: right;"><a href="dashboard.php">Retour</a></div>
                                <?php if(!isset($_GET['link'])):?>
                                    <div class="main-red-button-hover" style="float: right;margin-right: 12%"><a href="import_scan.php?link=yes">Scrapper</a></div>
                                <?php else: ?>
                                    <div class="main-red-button-hover" style="float: right;margin-right: 14%"><a class="scroll-to-section" href="import_scan.php">Local</a></div>
                                <?php endif; ?>
                            </fieldset>
                        </div>
                        <?php
                        if(isset($_POST['submit_upload_scan']) && !isset($_GET["link"])){
                            import_chapter();
                            $id_scan = get_id_scan(str_replace(" ", "_", $_POST['name_oeuvre']), $_POST['number_chapter']);
                            $id_scan_json = json_encode($id_scan);
                            ?>
                            <div class="progress-bar-p-container">
                                <div class="progress-bar-p"></div>
                                <div class="progress-bar-p-text">0%</div>
                            </div>
                            <script>updateProgressBar(<?php echo $id_scan_json; ?>)</script><?php
                        }
                        if(isset($_POST['submit_upload_scan']) && isset($_GET["link"])){
                            $result = import_via_url();
                            $link_split = explode("/", $_POST['link_oeuvre']);
                            $id_scan = get_id_scan(str_replace(" ", "_", strtolower($link_split[3])), $link_split[4]);
                            $id_scan_json = json_encode($id_scan);
                            ?>
                            <div class="progress-bar-p-container">
                                <div class="progress-bar-p"></div>
                                <div class="progress-bar-p-text">0%</div>
                            </div>
                            <script>updateProgressBar(<?php echo $id_scan_json; ?>)</script><?php
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="footer-dec">
    <img src="assets/images/footer-dec.png" alt="">
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="copyright">
                    <p>Designed by <a rel="nofollow" href="">Moi <3</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/imagesloaded.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>