<?php

function getPdoConnexion(){

//    $mysql_host = "87.98.176.73";
//    $mysql_user = "App_imedia";
//    $mysql_pass = "imdM,26fe21JDUa";
//    $mysql_db = "intranet";


    $mysql_host = "51.178.217.17";
    $mysql_user = "root";
    $mysql_pass = "rns2cifpa.";
    $mysql_db = "offre_com";

    return new PDO('mysql:dbname='.$mysql_db.';host='.$mysql_host, $mysql_user, $mysql_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
function getPdoConnexionUTF8($prod = true){
    if($prod){
        $mysql_host = "10.30.10.30";
        $mysql_user = "App_actis";
        $mysql_pass = "ac7MW9fb9c0YJXw";
        $mysql_db = "offre_com";
    }
    else{
        $mysql_host = "51.178.217.17";
        $mysql_user = "root";
        $mysql_pass = "rns2cifpa.";
        $mysql_db = "offre_com";
    }

    return new PDO('mysql:dbname='.$mysql_db.';host='.$mysql_host, $mysql_user, $mysql_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
}

function getMySqliConnection(){
    $mysql_host = "51.178.217.17";
    $mysql_user = "root";
    $mysql_pass = "rns2cifpa.";
    $mysql_db = "offre_com";
    return new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
}
