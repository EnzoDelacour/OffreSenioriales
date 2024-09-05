<?php

$destinations = liste_destination($pdoConnection, $util_id);

$type_id = null;
$destination_id = 1;
$type_lib = '';
$valeur = 0;
$categorie = 0;
$description = '';
$condition = '';
$actif = 1;
$formDisabled = '';

$commands = liste_command();
$liste_command = $commands[0];
$liste_command_value = $commands[1];

$liste_categorie = $storedProcedure->call('liste_type_categorie', [
    ['value'=>$util_id, 'type'=>'INT'],
]);

if(isset($_GET['id'])){
    $type_id = $_GET['id'];

    $type = $storedProcedure->call('infos_typeoffre', [
        ['value'=>$type_id, 'type'=>'INT'],
        ['value'=>$util_id, 'type'=>'INT'],
    ], false, PDO::FETCH_ASSOC, 1);

    if(isset($type['typ_id'])){
        $destination_id = $type['typ_destination_id'];
        $type_lib = $type['typ_lib'];
        $valeur = $type['typ_attend_valeur'];
        $cout = $type['typ_cout'];
        $categorie = $type['typ_categorie_id'];
        $description = $type['typ_description'];
        $condition = $type['typ_condition'];
        $actif = $type['typ_actif'];

        $formDisabled = '';
    }
    else{
        $type_id = null;
        $notifications['error'][]= 'Le type d\'offre sélectionné n\'existe pas !';
    }
}



if(isset($_POST['type'])){
    $destination_id = $_POST['destination'];
    $type_lib = $_POST['type'];
    $valeur = isset($_POST['valeur']) ? 1 : 0;
    $description = $_POST['description'];
    $condition = $_POST['condition'];
    $actif = isset($_POST['actif']) ? 1 : 0;
    $categorie = is_numeric($_POST['categorie']) && strlen($_POST['categorie']) > 0 ? $_POST['categorie'] : null;
    $cout = $_POST['cout'];

    $result = $storedProcedure->call('creamaj_typeoffre', [
        ['value'=>$type_id, 'type'=>'INT'],
        ['value'=>$util_id, 'type'=>'INT'],
        ['value'=>$destination_id, 'type'=>'INT'],
        ['value'=>$type_lib, 'type'=>'TEXT'],
        ['value'=>$valeur, 'type'=>'INT'],
        ['value'=>$description, 'type'=>'TEXT'],
        ['value'=>$condition, 'type'=>'TEXT'],
        ['value'=>$actif, 'type'=>'INT'],
        ['value'=>$cout, 'type'=>'TEXT'],
        ['value'=>$categorie, 'type'=>'INT'],
    ], false, PDO::FETCH_ASSOC, 1);


    if(isset($result['result'])){
        $notifications['success'][]= 'Le type d\'offre a bien été enregistré.';
        header('Location: index.php?p=typeoffre&o=edit&n=1&id='.$result['id']);
    }
    else{
        $notifications['error'][]= 'Le formulaire comporte une erreur.';
    }

}



if(isset($_GET['n']) && $_GET['n'] == 1){

    include 'includes/notification.php';
}
?>


<form method="post" id="form-type">

    <div class="box-tile-button">
        <h1>Créer un type d'offre</h1>
        <div class="box-btn-title">
            <button class="pattern-button" type="button" id="btn-create-type" name="create_offre"><i class="fas fa-save"></i>Enregistrer</button>
        </div>
    </div>

    <?php
    if(!is_null($type_id) && ($type['typ_template_id'] <= 0)){
        echo '<p><b style="color:#c0392b;">Ce type d\'offre est en attente de template</b></p>';
    }
    ?>

    <div class="container-part-form">
        <div class="box-part-form">
            <h3>Informations générales</h3>
            <?php
            if(is_null($type_id)){
                echo '<input type="hidden" name="actif" value="1">';
            }
            else{
                ?>
                <div class="box-form">
                    <label for="actif" class="label-input-100">Actif</label>
                    <div class="container-inputs">
                        <div class="box-inputs">
                            <input type="checkbox" id="actif" name="actif" value="1"  <?= $actif == 1 ? 'checked' : '' ?>>
                        </div>
                        <div class="exemple-placeholder">
                            <span>Si décoché, le type d'offre ne sera plus visible</span>
                            <span>Exemple pour bon d'achat : 1000 €</span>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="box-form">
                <label for="destination" class="label-input-100">Destination<sup class="required-icon">*</sup></label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <select id="destination" name="destination" <?= $formDisabled ?>>
                            <?php
                            foreach ($destinations as $destination){
                                $selected = $destination_id == $destination["dst_id"] ? 'selected' : '';
                                ?>
                                <option <?= $selected ?> value="<?= $destination["dst_id"] ?>"><?= $destination["dst_lib"] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <span class="exemple-placeholder">Sélectionner la destinaton de l'offre.</span>
                </div>
            </div>
            <div class="box-form">
                <label for="type" class="label-input-100">Type<sup class="required-icon">*</sup></label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <input type="text" id="type" name="type" class="input-text input-text-320" value="<?= $type_lib ?>" <?= $formDisabled ?>>
                    </div>
                    <span class="exemple-placeholder">Définir le nom du type d'offre.</span>
                </div>
            </div>


            <div class="box-form">
                <label for="categorie" class="label-input-100">Catégorie</label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <select id="categorie" name="categorie">
                            <option value="" <?= $categorie == 0 ? 'selected' : '' ?>>Aucune</option>
                            <?php
                            foreach ($liste_categorie as $cat){
                                ?>
                                <option value="<?= $cat['tyca_id'] ?>" <?= $categorie == $cat['tyca_id'] ? 'selected' : '' ?>><?= $cat['tyca_lib'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <span class="exemple-placeholder">Définir la catégorie du type d'offre.</span>
                </div>
            </div>

            <div class="box-form">
                <label for="valeur" class="label-input-100">Valeur</label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <input type="checkbox" id="valeur" name="valeur" value="1" <?= $valeur == 1 ? 'checked' : '' ?> <?= $formDisabled ?>>
                    </div>
                    <div class="exemple-placeholder">
                        <span>Permet de savoir si l'offre à une valeur à définir (en euros)</span>
                        <span>Exemple pour bon d'achat : 1000 €</span>
                    </div>
                </div>
            </div>


            <div class="box-form" >
                <label for="cout" class="label-input-100">Coût :</label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <input type="number" step="0.1" id="cout" name="cout" value="<?= $cout ?>" class="input-text input-text-320">
                    </div>
                    <span class="exemple-placeholder">Coût de l'offre</span>
                </div>
            </div>

            <div class="box-form box-form-textarea">
                <label for="description" class="label-input-100">Description</label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <?php
                        if(strlen($formDisabled) == 0){
                            ?>
<!--                            <div contenteditable="true" class="textarea-editable-code" id="description-editor">--><?//= $description ?><!--</div>-->
                            <?php
                        }
                        ?>
<!--                        <textarea id="description" name="description" cols="50" rows="3" class="textarea-result-editable" --><?//= strlen($formDisabled) == 0 ? 'style="display: none"' : '' ?><!-- --><?//= $formDisabled ?><!--<?//= $description ?></textarea>-->
                        <textarea id="description" name="description" cols="50" rows="3" class="" <?= $formDisabled ?>><?= $description ?></textarea>
                    </div>
                    <div class="exemple-placeholder">
                        <span>(optionnel) Décrire les details de l'offre</span>
                        <span>(exemple : décrire le montant d'un bon d'achat)</span>
                    </div>
                </div>
            </div>
            <div class="box-form box-form-textarea">
                <label for="condition" class="label-input-100">Condition</label>
                <div class="container-inputs">
                    <div class="box-inputs">
                        <?php
                        if(strlen($formDisabled) == 0){
                            ?>
<!--                            <div contenteditable="true" class="textarea-editable-code" id="condition-editor">--><?//= $condition ?><!--</div>-->
                            <?php
                        }
                        ?>
<!--                        <textarea id="condition" name="condition" cols="50" rows="3" class="textarea-result-editable" --><?//= strlen($formDisabled) == 0 ? 'style="display: none"' : '' ?><!-- --><?//= $formDisabled ?><!--<?//= $condition ?></textarea>-->
                        <textarea id="condition" name="condition" cols="50" rows="3" class=""<?= $formDisabled ?>><?= $condition ?></textarea>
                    </div>
                    <div class="exemple-placeholder">
                        <span>(optionnel) Décrire les condition de l'offre</span>
                        <span>Si vide, l'offre est sans condition</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="separateur-part-form"></div>
        <div class="box-part-form">
            <h3>Commandes Importation d'information</h3>
            <p class="description-form">Vous trouverez ci dessous les différentes commandes permettant d'utiliser les données importés. Pour utiliser une commande, il suffit de mettre le mot clé entre des accolads, sans coller des lettres avant et après, sans espace à l'intérieur :
                <br><b>{{</b>command<b>}}</b>
                <br><u>Exemple d'utilisation : </u>
                <img src="assets/images/exemple_command.png">
                <br/><u>Liste des commandes : </u></p>
            <div id="liste-command">
                <strong>Command</strong>
                <strong>Description</strong>
                <?php
                foreach ($liste_command as $key => $command){
                    ?>
                    <span><?= $command ?></span>
                    <span><?= $liste_command_value[$key] ?></span>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <div id="popup-search-prospect" class="popup-container-resultat"  style="display: none;">
        <div class="popup-content-resultat">
            <div class="popup-resultat" id="container-popup-search-prospect">
                <button class="btn-cancel-popup pattern-shadow" type="button"><i class="fas fa-times"></i></button>
                <h2>Rechercher un prospect</h2>
                <div class="container-form-centre-defaut" method="post">
                    <div style="display: flex; align-items: center">
                        <div class="box-form box-form-textarea" style="display: flex; flex-direction: column">
                            <label class="label-input">Taper votre recherche de mots clé ici :</label>
                            <div class="container-inputs">
                                <div class="box-inputs">
                                    <input type="text" id="recherche_prospect" class="input-text input-text-320">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="contain-result-prospect">
                        <p id="commentaire-result-prospect"></p>

                        <div id="container-table-prospect">
                            <table id="table-prospect">
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Nom, Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Date création</th>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="box-center-button" style="">
                        <button class="pattern-button-icon" type="button" id="btn-add-champ"><i class="fas fa-check"></i> Sélectionner</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



</form>

<script>
    const offreType = <?= json_encode($types) ?>;
    const listeCommand = <?= json_encode($liste_command) ?>;
</script>