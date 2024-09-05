<?php

include("ApiSenioriales.class.php");

if(isset($_GET['k']) && $_GET['k'] == "H!Uj0,89ij") {

    $ch = curl_init("https://manager.senioriales.com/api/getclienteapicurrentversion.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($httpcode >= 200 && $httpcode < 300) {
        if(!property_exists("ApiSenioriales", "version") || (property_exists("ApiSenioriales", "version") && $data != ApiSenioriales::$version)) {

            $currentVersion = (property_exists("ApiSenioriales", "version"))?ApiSenioriales::$version:"inconnue";

            if(!isset($_GET['go']))
                echo "Nouvelle version de l'api disponible (disponible ".$data." / installée ".$currentVersion.")<br><br><a href=\"updater.php?k=".$_GET['k']."&go=1\">Mettre à jour</a>";

            //Récupération
            if(isset($_GET['go'])) {
                echo "Installation version ".$data."...<br><br>";
                $ch = curl_init("https://manager.senioriales.com/api/getclienteapicurrentdata.php");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($httpcode >= 200 && $httpcode < 300) {
                    $data = unserialize(urldecode($data));
                    $currentApplicationKey = ApiSenioriales::$applicationKey;
                    @chmod("ApiSenioriales.class.php", 0777);
                    file_put_contents("ApiSenioriales.class.php", str_replace("XXXXXXXXXXXX",$currentApplicationKey,$data));
                    echo "Mise à jour terminée.";
                }
                else {
                    echo "erreur lors de la récupération des données.";
                }
            }
        }
        else
            echo "API cliente à jour";
    }
    else {
        echo "impossible de vérifier la présence d'une mise à jour (serveur injoignable)";
    }
}

?>