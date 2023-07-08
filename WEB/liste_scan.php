<?php include_once('fonctions.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <title>PA | Liste scan</title>

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

$oeuvre = get_oeuvre_by_id($_GET["m"]);
?>

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
        <img src="assets/images/portfolio-left-dec2.png" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading">
                    <h2>Liste des scans <em><?php echo ucwords(str_replace("_", " ", $oeuvre["TITRE"])); ?></em></h2>
                </div>
            </div>
        </div>

    </div>
</div>
<div style="padding-top: 0" id="contact" class="contact-us section">
    <div class="container">
        <div class="row">
            <div style="width: 100%" class="col-lg-6 align-self-center">
                <div class="connexion">
                    <?php foreach (get_scan($_GET["m"]) as $scan): ?>
                        <a href="affichage_scan.php?s=<?php echo $scan["ID"] . '&p=1' ?>"><p>
                                Chapitre <?php echo $scan['NUMERO'] ?></p></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="contact-dec">
        <img src="assets/images/contact-dec.png" alt="">
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