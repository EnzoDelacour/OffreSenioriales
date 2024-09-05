<?php
$onglet = 'liste';
$listeOngletPossible = ['ajouter', 'liste'];
if(isset($_GET['o'])){
    if(in_array($_GET['o'], $listeOngletPossible)){
        $onglet = $_GET['o'];
    }
}

if($onglet == 'ajouter'){
    include 'includes/pages/template/template_ajouter.php';
}
elseif ($onglet == 'liste'){
    include 'includes/pages/template/template_liste.php';
}
else{
    echo '<h2>Page introuvable !</h2>';
}


