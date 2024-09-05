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
                echo "Nouvelle version de l'api disponible (disponible ".$data." / install�e ".$currentVersion.")<br><br><a href=\"updater.php?k=".$_GET['k']."&go=1\">Mettre � jour</a>";

            //R�cup�ration
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
                    echo "Mise � jour termin�e.";
                }
                else {
                    echo "erreur lors de la r�cup�ration des donn�es.";
                }
            }
        }
        else
            echo "API cliente � jour";
    }
    else {
        echo "impossible de v�rifier la pr�sence d'une mise � jour (serveur injoignable)";
    }
}

?>