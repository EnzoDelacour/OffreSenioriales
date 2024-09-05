<?php
include 'includes/init.php';

/**
 *  ---------- Paramètre ----------
 */
$page = 'home';
if(isset($_GET['p'])){
    $page = $_GET['p'];
}


/**
 *  ---------- Validation des Formulaire ----------
 */



/**
 *  ---------- Initialisation des données ----------
 */
$notifications = ['success' => [], 'error' => [], 'warning' => []];


$canShowMenuTemplate = false;
if($_GET['p'] === 'template' && $_GET['o'] === 'ajouter' && $_GET['e'] === '3'){
    $canShowMenuTemplate = true;
}

?>

<!doctype html>
<html lang="fr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link rel="icon" type="image/png" href="assets/images/logo_senioriales/Fond%20transparent/Logo%20Senioriales%20bleu.png" />
    <link rel="stylesheet" type="text/css" href="library/bootstrap/bootstrap.min.css">
    <link href="library/fontawesome/css/all.css" type="text/css" rel="stylesheet">
    <link href="library/jquery-ui/jquery-ui.min.css" type="text/css" rel="stylesheet">
    <link href="library/chosen-1.8.7/chosen.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css?<?= time();?>">


    <title><?= ucfirst($page) ?> - Offre Com - Senioriales</title>
</head>
<body>

<?php
if(!isset($_GET['iframe'])){
    include 'includes/header.php';

    if($page == 'home'){
        include 'includes/pages/home.php';
    }
    elseif($page == 'stats_com'){
        ?>
        <div class="container-main" id="main">
            <?php
            include 'includes/pages/stats_com.php';
            ?>
        </div>
        <?php
    }
    elseif ($page == 'template' && $isAdmin){
        ?>
        <div class="container-main" id="main">
            <?php
            include 'includes/pages/template.php';
            ?>
        </div>
        <?php
    }
    elseif ($page == 'typeoffre'){
        ?>
        <div class="container-main" id="main">
            <?php
            include 'includes/pages/typeoffre.php';
            ?>
        </div>
        <?php
    }
    elseif ($page == 'create_offre'){
        ?>
        <div class="container-main" id="main">
            <?php
            include 'includes/pages/create_offre.php';
            ?>
        </div>
        <?php
    }
    elseif ($page == 'offre'){
        ?>
        <div class="container-main" id="main">
            <?php
            include 'includes/pages/offre.php';
            ?>
        </div>
        <?php
    }
    elseif ($page == 'stats' && $isAdmin){
        ?>
        <div class="container-main" id="main">
            <?php
            include 'includes/pages/stats.php';
            ?>
        </div>
        <?php
    }
    else{
        ?>
        <div class="container-main" id="main">
            <div></div>
            <?php
            echo '<h1>Page introuvable !</h1>';
            ?>
            <div></div>
        </div>
        <?php
    }
}
else{
    if($_GET['iframe'] == 'liste_offre' && $isAdmin){
        include 'includes/pages/offre/iframe-liste-offre.php';
    }
    else{
        ?>
        <div class="container-main" id="main">
            <div></div>
            <?php
            echo '<h1>Page introuvable !</h1>';
            ?>
            <div></div>
        </div>
        <?php
    }
}
?>
<div id="result-ajax"></div>

<?php
if($canDebug){
    $storedProcedure->showAllSPCalled();
}
?>

<?php include 'includes/footer.php' ?>


<script src="library/jquery/jquery-3.5.1.min.js"></script>
<script src="library/jquery-ui/jquery-ui.min.js"></script>
<script src="library/chosen-1.8.7/chosen.jquery.js"></script>
<script src="library/barcode-svg-master/barcode.min.js"></script>
<script src="library/pdf-lib/pdf-lib.min.js" crossorigin="anonymous"></script>
<script src="library/pdf-lib/downloadjs.js" crossorigin="anonymous"></script>
<script src="library/pdfjs-2.4.456-dist/build/pdf.js"></script>
<script src="library/crypto-js/crypto-js.js"></script>
<script>const user = <?= json_encode(array("id"=>$util_id, "name" => $util_login)) ?>;</script>



<!-- Librairy highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="assets/js/main.js?<?= time(); ?>"></script>
<script src="assets/js/offre.js?<?= time(); ?>"></script>
<?php
if($page == 'template'){
    echo '<script src="assets/js/template.js?'. time().'"></script>';
}
elseif ($page == 'typeoffre'){
    echo '<script src="assets/js/type_offre.js?'. time().'"></script>';
}
?>

</body>


</html>
