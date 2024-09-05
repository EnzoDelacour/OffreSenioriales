<?php
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

include_once 'functions/connexionDB.php';
if(!function_exists("array_column"))
{

    function array_column($array,$column_name)
    {

        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);

    }

}

function custom_echo($x, $length)
{
    if(strlen($x)<=$length)
    {
        return $x;
    }
    else
    {
        $y=substr($x,0,$length) . '...';
        return $y;
    }
}

function affiche_array($arr = array()){
    echo '<br>Size : ' . count($arr);
    echo '<br>';
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function affiche_text($str = ''){
    echo '<br<h1><u>text :</u> ' . $str . '</h1>';
    echo '<br>';
}

function afficher_query(String $query, array $parameter = []){
    echo '<br><div style="padding: 10px; border: 2px solid black; background-color: wheat; display: flex; flex-direction: column;">';
    echo $query . '(';
    echo implode(', ', $parameter);
    echo ');';
    echo '</div><br>';
}

function convertDate($date, $format = 'Y-m-d', $toFormat = 'd/m/Y'){
    $objDate = DateTime::createFromFormat($format, $date);
    return $objDate->format($toFormat);
}

function init_importation(PDO $pdoConnection, $utilisateur_id, $type_id, $nom_fichier, $extention, $taille, $total_ligne){
    try{
        $query = 'CALL init_importation(?,?,?,?,?,?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $type_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $nom_fichier, PDO::PARAM_STR);
        $stmt->bindParam(4, $extention, PDO::PARAM_STR);
        $stmt->bindParam(5, $taille, PDO::PARAM_INT);
        $stmt->bindParam(6, $total_ligne, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}



function finalisation_importation(PDO $pdoConnection, $utilisateur_id, $fichier_id, $current_ligne){
    try{
        $query = 'CALL finalise_importation(?,?,?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $fichier_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $current_ligne, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}

function liste_destination(PDO $pdoConnection, $utilisateur_id){
    try{
        $query = 'CALL liste_destination(?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}

function liste_type(PDO $pdoConnection, $utilisateur_id, $type_id = 0, $actif = 0){
    try{
        $query = 'CALL liste_type(?,?,?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $type_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $actif, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}

function liste_champ_offre(PDO $pdoConnection, $utilisateur_id, $offre_id = 1){
    try{
        $query = 'CALL liste_champ_offre(?,?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $type_id, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}

function update_champ_position(PDO $pdoConnection, $utilisateur_id, $champ_id, $posX, $posY, $page =1){
    try{
        $query = 'CALL update_champ_position(?,?,?,?,?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $champ_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $page, PDO::PARAM_INT);
        $stmt->bindParam(4, $posX, PDO::PARAM_INT);
        $stmt->bindParam(5, $posY, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}
function create_template(PDO $pdoConnection, $utilisateur_id, $type_offre_id, $lib, $description, $fileName, $fileSize){
    try{
        $query = 'CALL create_template(?,?,?,?,?,?)';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $type_offre_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $lib, PDO::PARAM_STR);
        $stmt->bindParam(4, $description, PDO::PARAM_STR);
        $stmt->bindParam(5, $fileName, PDO::PARAM_STR);
        $stmt->bindParam(6, $fileSize, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}


function get_template(PDO $pdoConnection, $utilisateur_id, $template_id){
    try{
        $query = 'SELECT * FROM template WHERE tmp_id = ?';
        $stmt = $pdoConnection->prepare($query);
        $stmt->bindParam(1, $template_id, PDO::PARAM_INT);
//        $stmt->bindParam(2, $template_id, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success){
            return  $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $exception){
        echo $exception->getMessage();
    }
}


function liste_command(){

    $liste_command = [
        'p_numero',
        'p_nom',
        'p_prenom',
        'p_email',
        'p_tel',
        'prog_code',
        'prog_nom',
        'prog_nomlong',
        'prog_adr',
        'prog_cp',
        'prog_localite',
        'l_code',
        'l_num',
        'l_bat',
        'part_partenaire',
        'part_animateur',
        'part_reseau',
        'part_titre',
        'part_ville',
        'part_cp',
        'part_email',
        ];
    // https://prm.senioriales.com/prod/list-partenaires.php

    $liste_command_value = [
        'N° du prospect',
        'Nom du prospect',
        'Prénom du prospect',
        'Email du prospect',
        'N° de télephone du prospect',
        'Code programme',
        'Nom programme',
        'Nom long programme',
        'Adresse programme',
        'Code postal programme',
        'Localité programme',
        'Code lot',
        'Numéro usuel lot',
        'Bâtiment lot',
        'Partenaire',
        'Animateur partenaire',
        'Réseau partenaire',
        'Titre partenaire',
        'Ville partenaire',
        'Code postal partenaire',
        'Email partenaire',
        ];

    $liste_command_bd = [
        ['champs' => 'p_id', 'table' => 'prospect'],
        ['champs' =>'p_nom', 'table' => 'prospect'],
        ['champs' =>'p_prenom', 'table' => 'prospect'],
        ['champs' =>'p_email', 'table' => 'prospect'],
        ['champs' =>'p_telephone', 'table' => 'prospect'],
        ['champs' =>'prog_code', 'table' => 'lot'],
        ['champs' =>'prog_lib', 'table' => 'lot'],
        ['champs' =>'prog_liblong', 'table' => 'lot'],
        ['champs' =>'adr_adresse', 'table' => 'lot'],
        ['champs' =>'adr_codepostal', 'table' => 'lot'],
        ['champs' =>'adr_localite', 'table' => 'lot'],
        ['champs' =>'lot_code', 'table' => 'lot'],
        ['champs' =>'lot_numero_usuel', 'table' => 'lot'],
        ['champs' =>'lot_batiment', 'table' => 'lot'],
        ['champs' =>'Partenaire', 'table' => 'prescripteur'],
        ['champs' =>'animateur', 'table' => 'prescripteur'],
        ['champs' =>'Reseau', 'table' => 'prescripteur'],
        ['champs' =>'Titre', 'table' => 'prescripteur'],
        ['champs' =>'pre_ville', 'table' => 'prescripteur'],
        ['champs' =>'pre_codepostal', 'table' => 'prescripteur'],
        ['champs' =>'Email', 'table' => 'prescripteur'],
    ];

    return [$liste_command, $liste_command_value, $liste_command_bd];
}


function buildLink(string $page, array $params = []) : string{
    $url = "index.php?p=$page";


    foreach ($params as $key => $value){
        if(!is_null($key) && !is_null($value)){
            $url .= "&$key=$value";
        }
    }

    return $url;
}


/**
 * @param string $element l'objet qui affichera la tootip. Important : ne pas oublier de mettre la class pattern-tooltip-element
 * @param string $message le message de la tootip
 * @param array $params width=>200px; position=>top | bottom | left | right - (default : top);
 * @throws Exception
 */
function setToolTip(string $element, string $message, $params = array()){
//    width = '200px', position = 'top'
    if(strpos($element, 'pattern-tooltip-element') === false){
        throw new Exception("Il manque la class pattern-tooltip-element dans l'élement sélectionné");
    }

    if(!isset($params['width']) || strlen($params['width']) == 0){
        $params['width'] = 'auto';
    }
    if(!isset($params['position']) || strlen($params['position']) == 0){
        $params['position'] = 'bottom';
    }
    if(!isset($params['word-break']) || strlen($params['word-break']) == 0){
        $params['word-break'] = 'normal';
    }
    if(!isset($params['white-space']) || strlen($params['white-space']) == 0){
        $params['white-space'] = 'normal';
    }
    ?>
    <div class="pattern-tooltip">
        <?= $element; ?>
        <div class="pattern-tooltip-message pattern-shadow" style="width: <?= $params['width'] ?>; word-break:  <?= $params['word-break'] ?>; white-space:  <?= $params['white-space'] ?>;">
            <?= $message ?>
        </div>
    </div>
    <?php
}
