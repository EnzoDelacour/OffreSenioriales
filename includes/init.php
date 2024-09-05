<?php
session_start();

$localVersion = false;
$prodEnv = true;
$devEnv = false;

if(($localVersion || $devEnv) && !$prodEnv){
    ini_set("display_errors","On");
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}


include_once 'functions/connexionDB.php';
include 'functions/function.php';
include 'functions/StoredProcedure.php';
include 'functions/iframe.php';



/**
 *  ---------- Connexion BDD (PDO) ----------
 */
$pdoConnection = getPdoConnexionUTF8();



if($prodEnv || $devEnv){
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        exit;
    }

    $rootDir     = "https://offres.senioriales.com/";
    $includesDir = "/includes";
    $url = 'https://offres.senioriales.com/' . basename($_SERVER['PHP_SELF']);

    require_once("includes/ApiSenioriales/ApiSenioriales.class.php");
    $apiSenioriales = new ApiSenioriales();
    $apiSenioriales->userCheckLogin("https://offres.senioriales.com/index.php");


    $util_id = $apiSenioriales->authUser['id'];
    $util_login = $apiSenioriales->authUser['login'];
    $util_prenom = $apiSenioriales->authUser['prenom'];
    $util_nom = $apiSenioriales->authUser['nom'];
    $util_picture = '';

    // Deconnexion
    if(isset($_GET['q'])){
        if($_GET['q'] === 'quit'){
            $apiSenioriales->logout();
        }
    }
}
else{
    $util_id = 2385;
    $util_login = "Danid SAID";
    $util_prenom = "Danid";
    $util_nom = "SAID";
    $util_picture = '';
    $util_profile = 'admin';
    $rootDir     = "http://localhost/offres_commerciales/";
}

$isAdmin = false;
if(in_array($util_id, [2385, 35, 4316, 4474, 17, 2933, 206, 4019, 24, 79, 2427, 1277, 125, 4940, 5086, 222])){
    $isAdmin = true;
}

$canDebug = false;
if(in_array($util_id, [2385, 35])){
    $canDebug = true;
}

$storedProcedure = new StoredProcedure($pdoConnection, $canDebug);