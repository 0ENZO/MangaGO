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
    <title>PA | Affichage scan</title>

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
$oeuvre = get_oeuvre_by_scan_id($_GET["s"]);
update_total_pages($_GET["s"]);
$scan = get_scan_by_id_fecth($_GET["s"]);
$page = get_page_by_id($_GET["s"], $_GET["p"]);
$tab_bulles = get_bulle($page['ID']);
$i = 1;
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
    <div class="main-red-button-hover" style="z-index: 5;position: absolute"><a href="dashboard.php">Retour</a></div>
    <div class="portfolio-left-dec">
        <img src="assets/images/portfolio-left-dec2.png" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="section-heading" style="margin-bottom: 20px;">
                    <h2>Chapitre <em><?php echo $scan["NUMERO"] ?></em></h2>
                    <span><?php echo $_GET["p"] . "/" . $scan["TOTAL_PAGE"] ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="padding-top: 0;margin-bottom: 50px;" id="contact" class="contact-us section">
    <div class="container">
        <div class="row" style="width: 55%;justify-content: space-between;flex-wrap: nowrap;">
            <div style="width: 100%;margin-left: 40%;" class="col-lg-6 align-self-center">
                <div id="touch" class="touch-controls"
                     style="width: 37%;height: 80%;position: fixed;z-index: 9;display: flex;box-sizing: border-box;justify-content: center;">
                    <div onclick="scan_left()" class="cursor-pointer" style="height: 100%;width: 50%;"></div>
                    <div onclick="scan_right()" class="cursor-pointer" style="height: 100%;width: 50%;"></div>
                </div>
                <a <?php if ($_GET["p"] < $scan["TOTAL_PAGE"]): ?>href="affichage_scan.php?s=<?php echo $_GET["s"] . ('&p=' . ($_GET["p"] + 1));
                endif; ?>"><img style="width: 100%;"
                                src="<?php echo "https://" . getenv('S3_BUCKET_PATH') . $page["LIEN_IMAGE"] ?>" alt=""></a>
            </div>
            <form
                <?php if ($_GET["p"] < $scan["TOTAL_PAGE"]): ?>
                    action="affichage_scan.php?s=<?php echo $_GET["s"] . ('&p=' . ($_GET["p"] + 1)."&b=".count($tab_bulles));?>"
                        <?php else:?>
                        action="affichage_scan.php?s=<?php echo $_GET["s"] . ('&p=' .$_GET["p"]."&b=".count($tab_bulles)); endif; ?>" method="post">
                <div class="col-lg-6 align-self-center">
                    <div class="connexion"
                         style="height: 1000px;padding: 40px 20px 20px;background-image: none;overflow:auto">
                        <?php
                        foreach ($tab_bulles as $bulle): ?>
                            <span>Bulle N°<?php echo $i; ?></span>
                            <input type="hidden" name="id_bulle_<?php echo $i; ?>" value="<?php echo $bulle["ID"]; ?>"/>
                            <textarea oninput="ajusterTailleTextareas()" name="txt_bulle_<?php echo $i; ?>"
                                      id="txt_bulle_<?php echo $i; ?>"
                                      rows="1"
                                      placeholder="Bulle N°<?php echo $i ?>"><?php if ($bulle["TEXT_CO"] == null) {echo $bulle["TEXT_TRAD"];} else {echo $bulle["TEXT_CO"];} ?></textarea>
                            <?php $i++; endforeach; ?>
                        <?php if ($_GET["p"] != $scan["TOTAL_PAGE"]): ?>
                            <input name="save_bulle" style="border: solid black" type="submit"
                                   value="Sauvegarder et page suivante">
                        <?php else: ?>
                            <input name="end_chapitre" style="border: solid black" type="submit"
                                   value="Générer le(s) scan(s)">
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if (isset($_POST['save_bulle'])) {
    for ($y = 1; $y <= $_GET['b']; $y++) {
        update_correction_bulle($_POST['id_bulle_' . $y], $_POST['txt_bulle_' . $y]);
    }
}
if (isset($_POST['end_chapitre'])) {
    for ($z = 1; $z <= $_GET['b']; $z++) {
        update_correction_bulle($_POST['id_bulle_' . $z], $_POST['txt_bulle_' . $z]);
    }
    replacement($_GET["s"]);
    sleep(15);
    ?><script>document.location.replace("dl.php?o=<?php echo $oeuvre['TITRE']."&n=".$scan['NUMERO']."&p=".$_GET["p"]; ?>")</script>
    <?php
}
?>

<!-- Scripts -->
<script>
    function scan_left() {
        const page_actuel = <?php echo($_GET["p"]); ?>;
        const scan_actuel = <?php echo($_GET["s"]); ?>;
        if (page_actuel > 1) {
            document.location.replace("affichage_scan.php?s=" + scan_actuel + "&p=" + (page_actuel - 1));
        }
    }

    function scan_right() {
        const page_actuel = <?php echo($_GET["p"]); ?>;
        const TOTAL_PAGE = <?php echo($scan["TOTAL_PAGE"]); ?>;
        const scan_actuel = <?php echo($_GET["s"]); ?>;
        if (page_actuel < TOTAL_PAGE) {
            document.location.replace("affichage_scan.php?s=" + scan_actuel + "&p=" + (page_actuel + 1));
        }
    }

    function ajusterTailleTextareas() {
        const textareas = document.getElementsByTagName('textarea');
        for (let i = 0; i < textareas.length; i++) {
            const textarea = textareas[i];
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    }

    window.addEventListener('load', ajusterTailleTextareas);

</script>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/imagesloaded.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>