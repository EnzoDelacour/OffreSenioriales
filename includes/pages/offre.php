<?php
$offre_id = null;
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $offre_id = $_GET['id'];
}

if(!is_null($offre_id)){
    include 'offre/info.php';
}
else{
    include 'offre/liste.php';
}