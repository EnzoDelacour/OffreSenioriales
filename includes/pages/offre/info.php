<?php


if(isset($_POST['set_offre_admin'])){
    $offre_id = $_GET['id'];
    $valeur_reel = $_POST['valeur_reel'];

    $result = $storedProcedure->call('set_offre_admin', [['value' => $util_id, 'type' => 'INT'],['value' => $offre_id, 'type' => 'INT'],['value' => $valeur_reel, 'type' => 'TEXT']], false);

    header('Location: index.php?p=offre&id=' . $offre_id);
}

if(isset($_POST['set_offre_statut'])){
    $offre_id = $_GET['id'];
    $statut = $_POST['set_offre_statut'];

    $result = $storedProcedure->call('set_offre_statut', [['value' => $util_id, 'type' => 'INT'],['value' => $offre_id, 'type' => 'INT'],['value' => $statut, 'type' => 'INT']], false);

    header('Location: index.php?p=offre&id=' . $offre_id);
}


$apercu = false;
$template_id = null;
$type_offre_id = null;
$liste_command = [];
$liste_command_bd = [];
if(isset($_GET['a']) && is_numeric($_GET['a']) && $_GET['a'] == 1 && isset($_GET['to'])){
    $apercu = true;
    $type_offre_id = $_GET['to'];

    $commands = liste_command();
    $liste_command = $commands[0];
    $liste_command_value = $commands[1];
    $liste_command_bd = $commands[2];
}

// Si on veut afficher un apeçu, il faut :
/*
    Créer une offre avec aperçu = 1
    Créer le document en BD et le générer
    Afficher cette offre
 */

if($apercu){
    // Récupération des infos du type d'offre
    $type_offre = $storedProcedure->call('infos_typeoffre', [['value' => $type_offre_id, 'type' => 'INT'],['value' => $util_id, 'type' => 'INT']], false, PDO::FETCH_ASSOC, 1);

    // Prospect test : Jean-Jacques ASSEMAT (proposé par Carnine)
    $prospect_id = 279553;

    // Lot test
    $lot_id = 'LTPJA66';

    $date_debut = new DateTime();
    $date_fin = new DateTime();
    $date_fin->add(new DateInterval('P1M'));

    $apercu = 1;
    $actif = 1;


    if($type_offre !== false){
        // Création de l'offre fictive
        $params = [
            ['value' => null, 'type' => 'INT'],
            ['value' => $util_id, 'type' => 'INT'],
            ['value' => $type_offre['typ_destination_id'], 'type' => 'INT'],
            ['value' => $type_offre_id, 'type' => 'INT'],
            ['value' => $type_offre['typ_lib'], 'type' => 'TEXT'],
            ['value' => 500, 'type' => 'TEXT'],
            ['value' => $type_offre['typ_description'], 'type' => 'TEXT'],
            ['value' => $type_offre['typ_condition'], 'type' => 'TEXT'],
            ['value' => $prospect_id, 'type' => 'INT'], // Pro
            ['value' => $lot_id, 'type' => 'TEXT'],
            ['value' => $date_debut->format('Y-m-d'), 'type' => 'TEXT'],
            ['value' => $date_fin->format('Y-m-d'), 'type' => 'TEXT'],
            ['value' => $apercu, 'type' => 'INT'],
            ['value' => $actif, 'type' => 'INT'],
        ];
        $result = $storedProcedure->call('creamaj_offre', $params, false, PDO::FETCH_ASSOC, 1);

        if($result['result']){
            $offre_id = $result['id'];
        }
    }
    else{
        die('Error, le template est introuvable');
    }


}

$offre = $storedProcedure->call('infos_offre', [['value' => $offre_id, 'type' => 'INT'],['value' => $util_id, 'type' => 'INT']], false, PDO::FETCH_ASSOC, 1);
$liste_champs = $storedProcedure->call('liste_champ_type_offre', [['value' => $util_id, 'type' => 'INT'],['value' => $offre['typ_id'], 'type' => 'INT']], true, PDO::FETCH_ASSOC, 1);

$hasToGenerateDocument = true;
if(strlen($offre['doc_id']) > 0){
    $hasToGenerateDocument = false;
}

iFrameDefaultMenuLeft();

?>

<?php

if($hasToGenerateDocument){
    $notifications['success'][]= 'L\'offre a bien été enregistré.';
    ?>
    <div id="popup-generate-offre" class="popup-container-resultat"  style="display: block;">
        <div class="popup-content-resultat">
            <form class="popup-resultat" id="container-popup-form-generate" method="post" enctype="multipart/form-data">
                <?php

                include 'includes/notification.php';
                ?>
                <h2>Générer le document PDF</h2>
                <p>L'offre <b><?= $offre['ofr_lib'] ?></b> doit être générée afin de pouvoir le télecharger.</p>

                <div class="box-center-button">
                    <button type="submit" class="pattern-button" id="btn-generate">Générer PDF</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

?>

<div class="box-side-middle pattern-shadow" style="background: white; padding: 5px 14px;  margin: 0 30px; grid-column-start: 1; grid-column-end: 5;">
    <div id="wrapper-container-fiche-offre">
        <div id="container-fiche-offre">
            <div id="wrapper-pdf-viewer">
                <div id="wrapper-container-pdf-viewer">
                    <div id="pdfViewer"></div>
                </div>
            </div>
            <div id="container-descriptif-fiche" style=" width: 100%">
                <h1><?= $offre['p_nom'] . ', ' . $offre['p_prenom'] ?> : <?= $offre['ofr_lib'] ?></h1>
                <p>Par <b class="color-main-color"><?= $offre['u_login'] ?></b></p>
                <?php
                $class = 'offre-en-cours';
                if($offre['ost_id'] == 5){
                    $class = 'offre-valide';
                }
                elseif ($offre['ost_id'] == 6){
                    $class = 'offre-expire';
                }
                ?>

                <div id="container-change-statut">
                    <div id="box-status-actuel">
                        <span data-statut-id="1" id="btn-fiche-statut" class="fiche-statut <?= $class ?>" title="<?= $offre['ost_description'] ?>"><?= $offre['ost_lib'] ?><i class="fas fa-pen"></i></span>
                    </div>
                    <?php
                    $canShow = false;
                    if($offre['ost_id'] != 6){
                        $canShow = true;
                    }
                    else{
                        if($isAdmin){
                            $canShow = true;
                        }
                    }
                    ?>
                    <form method="post" id="box-select-statut">
                        <button name="set_offre_statut" value="4" data-statut-id="1" title="L'offre est lancé." class="fiche-statut offre-en-cours btn-select-statut">En cours <i class="fas fa-dot-circle"></i></button>
                        <button name="set_offre_statut" value="5" data-statut-id="2" title="L'offre à été validée par le prospect concerné." class="fiche-statut offre-valide btn-select-statut">Validé <i class="fas fa-dot-circle"></i></button>
                        <button name="set_offre_statut" value="6" data-statut-id="3" title="La date de fin de l'offre a expiré." class="fiche-statut offre-expire btn-select-statut">Expiré <i class="fas fa-dot-circle"></i></button>
                    </form>
                </div>
                <br>


                <p style="margin-top: 15px"><u><b>Description</b></u> : <?= $offre['ofr_description'] ?></p>
                <p style="margin-top: 10px"><u><b>Condition</b></u> : <?= $offre['ofr_condition'] ?></p>
                <p style="margin-top: 20px"><u><b>Date début</b></u> : <?= ($offre['ofr_datedebut']) ?></p>
                <p style="margin-top: 10px"><u><b>Date fin</b></u> : <?= ($offre['ofr_datefin']) ?></p>



                <div style="display: grid; grid-template-columns: 1fr 1px 1fr; margin-top: 30px; grid-gap: 10px">
                    <div class="box-element-left-menu-offre">
                        <a target="_blank" class="link-crm" href="https://crm.senioriales.com/prod/prospect/<?= $offre['p_id'] ?>"><span>CRM</span><b>Prospect n°<?= $offre['p_id'] ?></b></a>
                        <span><?= $offre['p_nom'] . ' ' . $offre['p_prenom'] ?></span>
                        <span><?= $offre['p_email'] ?></span>
                        <span><?= $offre['p_telephone'] ?></span>
                    </div>
                    <div style="background: #bdc3c7"></div>
                    <div class="box-element-left-menu-offre">
                        <a target="_blank" class="link-stock" href="https://stock.senioriales.com/prod/editResidence.php?code=<?= $offre['prog_code'] ?>&tab=3&lot=<?= $offre['lot_code'] ?>"><span>CRM</span><b>Lot n°<?= $offre['lot_code'] ?></b></a>
                        <b>Lot n°<?= $offre['lot_code'] ?></b>
                        <span><?= $offre['prog_liblong'] ?></span>
                        <span><?= $offre['lot_numero_usuel'] ?></span>
                        <span><?= $offre['adr_adresse'] ?></span>
                        <span><?= $offre['adr_codepostal'] ?></span>
                        <span><?= $offre['adr_localite'] ?></span>
                    </div>
                </div>

                <a target="_blank" href="document.php?doc=<?= $offre['doc_filepath'] ?>" class="pattern-button" id="btn-download-offre" ><i class="fas fa-download"></i> Télecharger PDF</a>

                <?php
                if($isAdmin){
                    ?>
                    <form method="post" style="margin-top: 20px; border-top: 1px solid black">
                        <input type="hidden" name="offre_id" value="<?= $offre['ofr_id'] ?>">

                        <div class="box-tile-button">
                            <h4>Section admin</h4>
                            <div class="box-btn-title">
                                <button class="pattern-button" type="submit" name="set_offre_admin"><i class="fas fa-save"></i>Enregistrer</button>
                            </div>
                        </div>


                        <div class="box-form" >
                            <label for="valeur_reel" class="label-input-100">Valeur réelle :</label>
                            <div class="container-inputs">
                                <div class="box-inputs">
                                    <input type="number" step="0.1" id="valeur_reel" name="valeur_reel" class="input-text input-text-320" value="<?= $offre['ofr_valeur_reel'] ?>">
                                </div>
                                <span class="exemple-placeholder">Valeur réelle de l'offre</span>
                            </div>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    let hasToGenerate = <?= $hasToGenerateDocument ? 'true' : 'false' ?>;
    let templateURL = 'documents/<?= $offre['tmp_filepath'] ?>';
    let offreURL = 'documents/<?= $offre['doc_filepath'] ?>';
    let offre = <?= json_encode($offre) ?>;
    let champsOffre = <?= json_encode($liste_champs) ?>;
    let page = 'show_offre';
    const listeCommand = <?= json_encode($liste_command) ?>;
    const listeCommandBD = <?= json_encode($liste_command_bd) ?>;
</script>

<?php

iFrameDefaultMenuRight();


