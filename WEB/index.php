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
    <title>MangaGo | Accueil</title>

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

<?php
$tab_user = null;
if (isset($_SESSION['USER_ON']) && $_SESSION['USER_ON']) {
    $tab_user = get_info_client();
}
?>

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
                        <li class="scroll-to-section"><a href="#top" class="active">Accueil</a></li>
                        <li class="scroll-to-section"><?php if ($tab_user == null): ?>
                            <a href="#services">Services</a><?php else: ?>
                            <a href="import_scan.php">Importer vos scans</a>
                        <?php endif; ?></li>
                        <li class="scroll-to-section"><a href="#about">A propos</a></li>
                        <li class="scroll-to-section"><a href="#portfolio">Dernières sorties</a></li>
                        <li class="scroll-to-section">
                            <div class="main-red-button-hover"><?php if ($tab_user == null): ?><a href="connexion.php">Se connecter</a><?php else: ?><a
                                    href="dashboard.php"><?php echo $tab_user["PSEUDO"]; ?></a><?php endif; ?></div>
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

<div class="main-banner" id="top">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 align-self-center">
                        <div class="owl-carousel owl-banner">
                            <div class="item header-text">
                                <h6>Bienvenue sur MangaGo</h6>
                                <h2>Traduisez vos <em>scans</em> en <span>un instant</span></h2>
                                <p>Notre solution MangaGO, vous permet d'automatiser votre processus de traduction, du japonais au français, et pleins d'autres choses à venir..</p>
                                <?php if ($tab_user == null): ?>
                                    <div class="down-buttons">
                                        <div class="main-blue-button-hover">
                                            <a href="connexion.php">Se connecter</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="services" class="our-services section">
    <div class="services-right-dec">
        <img src="assets/images/services-right-dec2.png" alt="">
    </div>
    <div class="container">
        <div class="services-left-dec">
            <img src="assets/images/services-left-dec.png" alt="">
        </div>
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading">
                    <h2>Nous <em>fournissons</em> le meilleur service de <span>Traduction</span> de scans</h2>
                    <span>Nos Services</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="owl-carousel owl-services">
                    <div class="item">
                        <h4>Conception et développement UI/UX</h4>
                        <div class="icon"><img src="assets/images/service-icon-03.png" alt=""></div>
                        <p>l'interface est conviviale et facile à utiliser, avec des fonctions de recherche et
                            de navigation claires.</p>
                    </div>
                    <div class="item">
                        <h4>Traduction de qualité professionnelle</h4>
                        <div class="icon"><img src="assets/images/service-icon-04.png" alt=""></div>
                        <p> Nous proposons des traductions de haute qualité pour les mangas, afin que vous
                            puissiez profiter de l'histoire sans être distraits par des erreurs de traduction.</p>
                    </div>
                    <div class="item">
                        <h4>Large sélection de mangas</h4>
                        <div class="icon"><img src="assets/images/service-icon-01.png" alt=""></div>
                        <p>Notre site offre une large sélection de mangas pour les fans, y compris les titres populaires
                            et les nouveaux titres.</p>
                    </div>
                    <div class="item">
                        <h4>Notifications de nouveaux chapitres</h4>
                        <div class="icon"><img src="assets/images/service-icon-02.png" alt=""></div>
                        <p>Abonner-vous à vos titres préférés pour recevoir des notifications lors de la sortie de
                            nouveaux chapitres</p>
                    </div>
                    <div class="item">
                        <h4>Accès hors ligne</h4>
                        <div class="icon"><img src="assets/images/service-icon-03.png" alt=""></div>
                        <p>Fonctionnalité pratique pour ceux qui souhaitent lire lorsqu'ils n'ont pas accès à
                            Internet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="about" class="about-us section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 align-self-center">
                <div class="left-image">
                    <img src="assets/images/about-left-image.png" alt="Two Girls working together">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-heading">
                    <h2><em>Accélérez</em> le processus de traduction de scans de manga grâce à nos outils
                        d'<span>IA</span> de pointe.</h2>
                    <p>Visitez notre site et explorez les dernières nouveautés en matière de traduction de scans de
                        manga.</p>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="fact-item">
                                <div class="count-area-content">
                                    <div class="icon">
                                        <img src="assets/images/service-icon-01.png" alt="">
                                    </div>
                                    <div class="count-digit">320</div>
                                    <div class="count-title">Traducteur de scans</div>
                                    <p>Notre plateforme innovante facilite la traduction de vos scans de manga préférés
                                        avec une précision et une rapidité inégalées.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="fact-item">
                                <div class="count-area-content">
                                    <div class="icon">
                                        <img src="assets/images/service-icon-02.png" alt="">
                                    </div>
                                    <div class="count-digit">640</div>
                                    <div class="count-title">Mangas traduits</div>
                                    <p>Nous sommes fiers d'avoir traduit avec succès des centaines de mangas pour nos
                                        clients satisfaits. Découvrez comment nous pouvons vous aider à atteindre vos
                                        objectifs de traduction de manga.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="fact-item">
                                <div class="count-area-content">
                                    <div class="icon">
                                        <img src="assets/images/service-icon-03.png" alt="">
                                    </div>
                                    <div class="count-digit">120</div>
                                    <div class="count-title">Clients satisfaits</div>
                                    <p>La satisfaction de nos clients est notre priorité absolue. Nous sommes fiers de
                                        vous offrir des services de traduction de scans de manga de qualité supérieure
                                        et de vous aider à réussir dans votre projet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="portfolio" class="our-portfolio section">
    <div class="portfolio-left-dec">
        <img src="assets/images/portfolio-left-dec2.png" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading">
                    <h2>Dernière <em>sortie</em></h2>
                    <span>Vos Mangas</span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="owl-carousel owl-portfolio">
                    <?php foreach (get_derniere_sortie() as $oeuvre): ?>
                        <div class="item">
                            <a href="liste_scan.php?m=<?php echo $oeuvre["ID"] ?>">
                                <div class="thumb">
                                    <img src="<?php echo $oeuvre["IMAGE_COUVERTURE"] ?>" alt="">
                                    <div class="hover-effect">
                                        <div class="inner-content">
                                            <a rel="sponsored" href="#" target="_parent">
                                                <h4><?php echo ucwords(str_replace("_", " ", $oeuvre["TITRE"])); ?></h4></a>
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

<div id="pricing" class="pricing-tables">
    <div class="tables-left-dec">
        <img src="assets/images/tables-left-dec.png" alt="">
    </div>
    <div class="tables-right-dec">
        <img src="assets/images/tables-right-dec.png" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading">
                    <h2>Choisissez Un <em>Forfait</em> Adapté À Vos Prochains <span>Projets</span></h2>
                    <span>NOS FORFAITS</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4" style="display: inline; width: 100%;">
                <div class="item third-item">
                    <h4>Forfait Gratuit</h4>
                    <span>0$</span>
                    <ul>
                        <li>&#8734 Projet</li>
                        <li>&#8734 Go d'espace</li>
                        <li>Support de base</li>
                    </ul>
                    <div class="main-blue-button-hover">
                        <a href="#">Commencer</a>
                    </div>
                </div>
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

<script>
    $(document).on("click", ".naccs .menu div", function () {
        const numberIndex = $(this).index();

        if (!$(this).is("active")) {
            $(".naccs .menu div").removeClass("active");
            $(".naccs ul li").removeClass("active");

            $(this).addClass("active");
            $(".naccs ul").find("li:eq(" + numberIndex + ")").addClass("active");

            const listItemHeight = $(".naccs ul")
                .find("li:eq(" + numberIndex + ")")
                .innerHeight();
            $(".naccs ul").height(listItemHeight + "px");
        }
    });
</script>
</body>
</html>