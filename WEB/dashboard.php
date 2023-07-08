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
    <title>PA | Dashboard</title>

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
                        <li class="scroll-to-section"><div class="main-red-button-hover"><a href="deconnexion.php">déconnexion</a></div></li>
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
                    <h2>Votre bibliothèque</h2>
                    <span>Vos Mangas</span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="owl-carousel owl-portfolio">
                    <?php foreach(get_derniere_sortie() as $oeuvre): ?>
                        <div class="item" style="width: 75%;">
                            <a href="liste_scan.php?m=<?php echo $oeuvre["ID"]?>">
                                <div class="thumb">
                                    <img  src="<?php echo $oeuvre["IMAGE_COUVERTURE"] ?>" alt="">
                                    <div class="hover-effect">
                                        <div class="inner-content">
                                            <a rel="sponsored" href="#" target="_parent"><h4><?php echo ucwords(str_replace("_", " ", $oeuvre["TITRE"])); ?></h4></a>
                                            <span><?php echo $oeuvre["AUTEUR"] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
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