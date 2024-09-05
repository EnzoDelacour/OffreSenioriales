<?php

$destination = 1;
if(isset($_GET['dest'])){
    $destination = $_GET['dest'];
}



$all = 1;
if($isAdmin){
    $all = null;
}

$numPage = 1;
if(isset($_GET['num'])){
    $numPage = $_GET['num'];
}




$result = $storedProcedure->call('liste_catalogue', [
    ['value'=>$util_id, 'type'=>'INT'],
    ['value'=>$destination, 'type'=>'INT'],
    ['value'=>$all, 'type'=>'INT'],
    ['value'=>$numPage, 'type'=>'INT'],
], true, PDO::FETCH_ASSOC, 2);
$templiste = $result[0];
$stats = $result[1];

$liste = [];
foreach ($templiste as $l){
    $groop = strlen($l['tyca_lib']) > 0 ? $l['tyca_lib'] : '';
    $liste[$groop][] = $l;
}

if(isset($_POST['set_actif'])){
    $type_id = $_POST['type_id'];
//    var_dump($_POST['actif']);
    $actif = $_POST['actif'] == 1 ? 1 : 0;

    $result = $storedProcedure->call('set_typeoffre_actif', [
        ['value'=>$util_id, 'type'=>'INT'],
        ['value'=>$type_id, 'type'=>'INT'],
        ['value'=>$actif, 'type'=>'INT'],
    ], false, PDO::FETCH_ASSOC, 1);

    if(isset($result['result'])){
        $notifications['success'][]= 'La modification a bien été enregistrée.';

        header('Location: index.php?p=typeoffre');
    }
}
?>


<div class="box-tile-button">
    <h1>Catalogue des offres commerciales</h1>
    <div class="box-btn-title">
        <?php
        if($isAdmin){
            ?>
<!--            <button type="button" class="pattern-button btn-export-excel" id="btn-export"><i class="fas fa-file-excel"></i> Export</button>-->
            <a href="index.php?p=typeoffre&o=create" class="pattern-button"><i class="fas fa-plus"></i>Ajouter Type</a>
            <?php
        }
        ?>
    </div>
</div>
<div id="menu-typeoffre-destiniation" style="border-collapse: collapse; margin-bottom: 20px">
    <a href="index.php?p=typeoffre&dest=1" class="<?= $destination == 1 ? 'pattern-button' : 'pattern-button-border' ?>">Vente</a>
    <a href="index.php?p=typeoffre&dest=2" class="<?= $destination == 2 ? 'pattern-button' : 'pattern-button-border' ?>">Location</a>
</div>

    <?php

    $all_type = [];
    foreach ($liste as $cat => $tabtype){
        ?>
            <h2 style="border-bottom: 1px solid gray"><?= $cat ?></h2>
<div id="container-grid-liste-offre">
    <?php
    foreach ($tabtype as $type){
        $all_type[] = $type;
        ?>
        <div class="affiche-offre" style="">
            <div class="wrapper-box-image-template-liste" data-type-id="<?= $type['typ_id'] ?>" data-path="documents/<?= $type['tmp_filepath'] ?>">
                <div class="contrainer-action-type-admin">
                    <div  class="box-action-type-admin" style="<?= $type['typ_actif'] == 0 ? 'border: 2px solid red";' : '' ?>" <?= $type['typ_actif'] == 0 ? 'title="Offre non visible"' : '' ?> >
                        <div class="box-icon-destination2" >
                            <?php
                            if($type['typ_destination_id'] == 1){
                                ?>
                                <i title="Location" class="fas fa-building" <?= $type['typ_actif'] == 0 ? 'style="color: red"' : '' ?>></i>
                                <?php
                            }
                            elseif($type['typ_destination_id'] == 2){
                                ?>
                                <i title="Vente" class="fas fa-home" <?= $type['typ_actif'] == 0 ? 'style="color: red"' : '' ?>></i>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="container-list-icon-type class-not-trigger-popup">
                            <?php
                            if($isAdmin){
                                ?>
                                <div class="box-icon-destination class-not-trigger-popup">
                                    <a class="class-not-trigger-popup" title="Créer une offre" href="index.php?p=create_offre&sid=<?= $type['typ_id'] ?>"><i class="fas fa-plus class-not-trigger-popup"></i></a>
                                </div>
                                <div class="box-icon-destination class-not-trigger-popup" style="border-left: 1px solid white; border-right: 1px solid white;">
                                    <a class="class-not-trigger-popup" title="Éditer ce type d'offre" href="index.php?p=typeoffre&o=edit&id=<?= $type['typ_id'] ?>" class=""><i class="fas fa-pen class-not-trigger-popup"></i></a>
                                </div>
                                <div class="box-icon-destination class-not-trigger-popup">
                                    <?php
                                    if($type['typ_template_id'] > 0){
                                        ?>
                                        <a class="class-not-trigger-popup" title="Éditer le template" href="index.php?p=template&o=ajouter&e=2&t=<?= $type['typ_id'] ?>" style="<?= $type['typ_template_id'] > 0 ? '' : 'background: red;' ?>" ><i class="fas fa-file class-not-trigger-popup"></i></a>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <a class="class-not-trigger-popup" title="Ajouter le template" href="index.php?p=template&o=ajouter&e=2&t=<?= $type['typ_id'] ?>" style="<?= $type['typ_template_id'] > 0 ? '' : 'background: red;' ?>" ><i class="fas fa-file class-not-trigger-popup"></i></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            else{
                                ?>
                                <div class="box-icon-destination class-not-trigger-popup" style="grid-column: 1/4">
                                    <a class="class-not-trigger-popup" title="Créer une offre" href="index.php?p=create_offre&sid=<?= $type['typ_id'] ?>"><i class="fas fa-plus class-not-trigger-popup"></i></a>
                                </div>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
                <div class="box-image-template-liste" data-path="documents/<?= $type['tmp_filepath'] ?>">
                    <div class="loader loader--style3 loader-canvas-offre">
                        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                          <path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
                              <animateTransform attributeType="xml"
                                                attributeName="transform"
                                                type="rotate"
                                                from="0 25 25"
                                                to="360 25 25"
                                                dur="0.6s"
                                                repeatCount="indefinite"/>
                          </path>
                      </svg>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: center; padding: 7px">
                <b style="text-align: center"><?= $type['typ_lib'] ?></b>
            </div>
        </div>
        <?php
    }
    ?>
</div>
        <?php
    }
    ?>

<div id="popup-show-type-offre" class="popup-container-resultat"  style="display: none;">
    <div class="popup-content-resultat" style="overflow-y: auto">
        <div class="popup-resultat" style="width: 80%; height: auto;">
            <button class="btn-cancel-popup pattern-shadow" type="button"><i class="fas fa-times"></i></button>

            <div style="display:flex; flex-direction: column; height: 95vh; overflow-y: auto;">
                <div style="display:flex; flex-direction: column; ">
                    <p><b>Conditions :</b> <span id="text-condition-offre"></span></p>
                    <p><b>Descriptions :</b> <span id="text-description-offre"></span></p>
                </div>
                <div style="width: 100%; display: flex; justify-content: center">
                    <div id="appercu-type-offre" style="width: 600px; border: 1px solid #d6d6d6; ">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const listeType = <?= json_encode($liste) ?>;
    const listeType2 = <?= json_encode($all_type) ?>;
</script>