<?php
$etape_ajout_template = 0;
$liste_etape = ['start', 'Sélectionner type d\'offre', 'Importer PDF', 'Edition Template', 'Validation'];
if(isset($_GET['e'])){
    $etape_ajout_template = intval($_GET['e']);
}

$type_id = null;
if(isset($_GET['t'])){
    $type_id = $_GET['t'];
}

if(isset($_POST['upload_template_file'])){
//    $result = create_template($pdoConnection, $util_id, $type_id, 'Template Bon de Parrainage', 'desciption', $_FILES['uploadFile']['name'], 2);

    $type = $storedProcedure->call('infos_typeoffre', [
        ['value'=>$type_id, 'type'=>'INT'],
        ['value'=>$util_id, 'type'=>'INT'],
    ], false, PDO::FETCH_ASSOC, 1);

    $result = $storedProcedure->call('create_template', [
        ['value'=>$util_id, 'type'=>'INT'],
        ['value'=>$type_id, 'type'=>'INT'],
        ['value'=>'Template ' . $type['typ_lib'], 'type'=>'TEXT'],
        ['value'=>'', 'type'=>'TEXT'],
        ['value'=>$_FILES['uploadFile']['name'], 'type'=>'TEXT'],
        ['value'=>$_FILES['uploadFile']['size'], 'type'=>'TEXT'],
    ], false, PDO::FETCH_ASSOC, 1);

    if(isset($result['id'])){
        if(strlen($_FILES['uploadFile']['name']) > 0){
            $target_file = 'documents/' . $result['filepath'];
            if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_file)) {
//                echo "The file ". basename( $_FILES["uploadFile"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        try{

            header('Location: index.php?p=template&o=ajouter&e=3&id=' . $result['id']);
        }
        catch (Exception $e){

        }
    }
    else{
        $notifications['error'][] = 'Requête non éxécuté !';
    }
}




// Spécifique menu gauche à l'etape 3
if($etape_ajout_template === 3){

    $template = $storedProcedure->call('infos_template', [
        ['value'=>$util_id, 'type'=>'INT'],
        ['value'=>$_GET['id'], 'type'=>'INT'],
    ], false, PDO::FETCH_ASSOC, 1);


    $type = $storedProcedure->call('infos_typeoffre', [
        ['value'=>$template['typ_id'], 'type'=>'INT'],
        ['value'=>$util_id, 'type'=>'INT'],
    ], false, PDO::FETCH_ASSOC, 1);

//    $liste_champs = liste_champ_offre($pdoConnection, $util_id, $template['typ_id']);

//    $liste_champs = $storedProcedure->call('liste_champ_type_offre', [
//        ['value'=>$util_id, 'type'=>'INT'],
//        ['value'=>$template['typ_id'], 'type'=>'INT'],
//    ], true, PDO::FETCH_ASSOC, 1);



    $default_champs = [
        ['id'=> 1,  'name'=>'titre'                , 'value' => 'offre.ofr_lib'            , 'show'=>$type['typ_lib']],
        ['id'=> 2,  'name'=>'destination'          , 'value' => 'destination.dst_lib'      , 'show'=>$type['typ_description']],
        ['id'=> 3,  'name'=>'description'          , 'value' => 'offre.ofr_description'    , 'show'=>$type['typ_description']],
        ['id'=> 4,  'name'=>'condition'            , 'value' => 'offre.ofr_condition'      , 'show'=>$type['typ_condition']],
        ['id'=> 5,  'name'=>'valeur'               , 'value' => 'offre.ofr_valeur'         ,' show'=>$type['typ_condition']],
        ['id'=> 6,  'name'=>'date début'           , 'value' => 'offre.offre_date_debut'      ,' show'=>$type['typ_condition']],
        ['id'=> 7,  'name'=>'date fin'             , 'value' => 'offre.offre_date_fin'        ,' show'=>$type['typ_condition']],
        ['id'=> 8,  'name'=>'numéro prospect'      , 'value' => 'ext_Prospect.p_id'        ,' show'=>$type['typ_condition']],
        ['id'=> 9,  'name'=>'nom prospect'         , 'value' => 'ext_Prospect.p_nom'       ,' show'=>$type['typ_condition']],
        ['id'=> 10,  'name'=>'prenom prospect'      , 'value' => 'ext_Prospect.p_prenom'    ,' show'=>$type['typ_condition']],
        ['id'=> 11,  'name'=>'email prospect'       , 'value' => 'ext_Prospect.p_email'     ,' show'=>$type['typ_condition']],
        ['id'=> 12,  'name'=>'telephone prospect'   , 'value' => 'ext_Prospect.p_telephone' ,' show'=>$type['typ_condition']],
        ['id'=> 13,  'name'=>'code programme'       , 'value' => 'ext_Lot.prog_code'        ,' show'=>$type['typ_condition']],
        ['id'=> 14,  'name'=>'code libelle'         , 'value' => 'ext_Lot.prog_lib'         ,' show'=>$type['typ_condition']],
        ['id'=> 15,  'name'=>'code libelle long'    , 'value' => 'ext_Lot.prog_liblong'        ,' show'=>$type['typ_condition']],
        ['id'=> 16,  'name'=>'adresse programme'    , 'value' => 'ext_Lot.adr_adresse'        ,' show'=>$type['typ_condition']],
        ['id'=> 17,  'name'=>'codepostale programme', 'value' => 'ext_Lot.adr_codepostal'        ,' show'=>$type['typ_condition']],
        ['id'=> 18,  'name'=>'ville programme'      , 'value' => 'ext_Lot.adr_localite'        ,' show'=>$type['typ_condition']],
        ['id'=> 19,  'name'=>'code lot'             , 'value' => 'ext_Lot.lot_code'        ,' show'=>$type['typ_condition']],
        ['id'=> 20,  'name'=>'numero lot'           , 'value' => 'ext_Lot.lot_numero_usuel'        ,' show'=>$type['typ_condition']],
        ['id'=> 21,  'name'=>'bâtiment lot'         , 'value' => 'ext_Lot.lot_batiment'        ,' show'=>$type['typ_condition']],
        ['id'=> 22,  'name'=>'Nom prénom commercial'         , 'value' => 'ext_Utilisateurs.u_login'        ,' show'=>$type['typ_condition']],
        ['id'=> 23,  'name'=>'Email commercial'         , 'value' => 'ext_Utilisateurs.u_mail'        ,' show'=>$type['typ_condition']],
        ['id'=> 24,  'name'=>'télephone commercial'         , 'value' => 'ext_Utilisateurs.u_tel_fixe'        ,' show'=>$type['typ_condition']],
        ['id'=> 25,  'name'=>'Partenaire'         , 'value' => 'ext_Prescripteur.Partenaire'        ,' show'=>$type['typ_condition']],
        ['id'=> 26,  'name'=>'Animateur partenaire'         , 'value' => 'ext_Prescripteur.animateur'        ,' show'=>$type['typ_condition']],
        ['id'=> 27,  'name'=>'Réseau partenaire'         , 'value' => 'ext_Prescripteur.Reseau'        ,' show'=>$type['typ_condition']],
        ['id'=> 28,  'name'=>'Titre partenaire'         , 'value' => 'ext_Prescripteur.Titre'        ,' show'=>$type['typ_condition']],
        ['id'=> 29,  'name'=>'Ville partenaire'         , 'value' => 'ext_Prescripteur.pre_ville'        ,' show'=>$type['typ_condition']],
        ['id'=> 30,  'name'=>'Code postal partenaire'         , 'value' => 'ext_Prescripteur.pre_codepostal'        ,' show'=>$type['typ_condition']],
        ['id'=> 31,  'name'=>'Email partenaire'         , 'value' => 'ext_Prescripteur.Email'        ,' show'=>$type['typ_condition']],
    ];
    iFrameTemplateMenuLeft($template['tmp_lib'], $template['tmp_description'], $template, $type, $default_champs);
}
else{
    iFrameDefaultMenuLeft();
}

?>


<div class="box-side-middle " style="background: transparent;">
<!--<h1>Ajouter un template PDF</h1>-->
<?php
if($etape_ajout_template === 0){
    ?>
    <p>Cette page permet d'ajout un template PDF qui sera utilisé plus tard dans la création d'une offre.</p>
    <p>Ce template peut être créer pour une future offre ou pour une offre existante (en attente de template).*</p>
    <p>Voici les étapes à suivre : </p>
    <ul style="margin-left: 40px">
        <li>Importer le PDF</li>
        <li>Sélectionner une offre (Afin d'avoir les diffrents éléments à positionner)</li>
        <li>Placer les différent éléments attendus dans le PDF</li>
        <li>Customiser le style si besoin (taille, police, couleur)</li>
        <li>Valider le template</li>
    </ul>
    <br/>

    <a href="index.php?p=template&o=ajouter&e=1" id="btn-start-template" type="button" class="pattern-button">Commencer</a>
    <?php
}
elseif ($etape_ajout_template === 1){
    iframeFilAriane($liste_etape, $etape_ajout_template);
    $listeType = liste_type($pdoConnection, $util_id);

    if(count($listeType) > 0){
        ?>
        <select class="select-choosen" id="type-template">
            <?php
            foreach ($listeType as $type){
                ?>
                <option value="<?= $type['typ_id'] ?>">[<?= $type['dst_lib'] ?>] <?= $type['typ_lib'] ?></option>
                <?php
            }
            ?>
        </select>
        <button href="" id="btn-start-template-type" type="button" class="pattern-button">Suivant</button>
        <?php
    }
    else{

        ?>
        <b>Aucun type d'offre disponible</b>
        <?php
    }
}
elseif ($etape_ajout_template === 2){


    iframeFilAriane($liste_etape, $etape_ajout_template);


    ?>
    <form id="form-upload-file" method="post" enctype="multipart/form-data">
        <p><strong><u>Attention :</u></strong> Le fichier ne doit contenir une seul page*</p>
        <div class="container-input-file">
            <div class="box-input-file">
                <div class="box-icon hide-after-valid">
                    <div class="upload-area">
                        <i class="fas fa-file-pdf icon-upload"></i>
                        <span class="title-drop">FAITES GLISSER VOTRE FICHIER PDF ICI</span>
                    </div>
                </div>

                <span class="picto hide-after-valid">Ou</span>

                <div class="box-input-button hide-after-valid">
                    <label class="custom-file-upload">
                        <input name="uploadFile" id="uploadFile" type="file" accept="application/pdf">
                        <i class="fa fa-cloud-upload"></i> SÉLECTIONNEZ UN FICHIER
                    </label>
                </div>
            </div>
        </div>

        <div id="wrapper-template-file-pre-uploaded">
            <div id="template-file-pre-uploaded">
                <div id="liste-info-file-template">
                    <ul>
                        <li><b>Nom :</b> <span id="filename-upload-template"></span></li>
                        <li><b>Taille :</b> <span id="filesize-upload-template"></span></li>
                    </ul>
                </div>
                <div>
                    <button type="submit" id="btn-start-template" name="upload_template_file" type="button" class="pattern-button">Étape suivante</button>
                </div>
            </div>
        </div>

    </form>
    <?php
}
//elseif ($etape_ajout_template === 2){
//    iframeFilAriane($liste_etape, $etape_ajout_template);
//}
elseif ($etape_ajout_template === 3){

    ?>
        <div id="wrapper-pdf-viewer">
            <div id="wrapper-container-pdf-viewer">
                <div id="pdfViewer"></div>
            </div>
        </div>

    <div id="popup-remplir-formulaire-template" class="popup-container-resultat" style="display: none">
        <div class="popup-content-resultat">
            <div class="popup-resultat">
                <button class="btn-cancel-popup pattern-shadow" type="button"><i class="fas fa-times"></i></button>

                <h2>Remplissage du formulaire</h2>
                <div style="display: flex; justify-content: flex-end; margin-bottom: 20px">
                    <button id="btn-continuer-saved-annotation" class="pattern-button">Valider</button>
                </div>
                <div id="container-remplir-formulaire">

                </div>
            </div>
        </div>
    </div>
    <?php
    $template = get_template($pdoConnection, $util_id, $_GET['id']);
    $type_id = intval($type['typ_id']);

    echo '<script>const type_id = '. $type_id .';</script>';
    echo '<script>const champsOffre = '. json_encode($default_champs) .';</script>';
    echo '<script>let canImportPdf = true;</script>';
    echo "<script>let importPdfLink = 'documents/".$template['tmp_filepath']."';</script>";
}
?>
</div>
<?php
if($etape_ajout_template === 3){
    iFrameTemplateMenuRight();
}
else{
    iFrameDefaultMenuRight();
}
?>
