<?php
$destination_id = 1;
$destinations = liste_destination($pdoConnection, $util_id);

$type_id = -1;
$types = liste_type($pdoConnection, $util_id,0,1);

$commands = liste_command();
$liste_command = $commands[0];
$liste_command_value = $commands[1];
$liste_command_bd = $commands[2];

$type_selected = null;
if(isset($_GET['sid']) && is_numeric($_GET['sid']) && in_array($_GET['sid'], array_column($types, "typ_id"))){
    $keySearch = array_search($_GET['sid'],  array_column($types, "typ_id"));
    $type_selected = $types[$keySearch];
}


if(isset($_POST['create_offre'])){

    $offre_id = null;
    $destination_id = $_POST['destination'];
    $type_id = $_POST['type'];
    $libelle = $_POST['libelle'];
    $valeur = $_POST['valeur'];
    $description = $_POST['description'];
    $condition = $_POST['condition'];
    $prospect_id = $_POST['prospect_id'];

    $prospect_id = ltrim(str_replace('P', '', $prospect_id), '0');
    $prospect_id = ltrim(str_replace('p', '', $prospect_id), '0');

    $lot_id = $_POST['lot_id'];
    $partenaire_id = $_POST['partenaire_id'];

    $date_debut = $_POST['date_debut'];
    $date_debut = (DateTime::createFromFormat('d/m/Y', $date_debut))->format('Y-m-d');
    $date_fin = $_POST['date_fin'];
    $date_fin = (DateTime::createFromFormat('d/m/Y', $date_fin))->format('Y-m-d');

    $actif = $_POST['actif'] ?? 1;
    $apercu = 0;

    $params = [
        ['value' => $offre_id, 'type' => 'INT'],
        ['value' => $util_id, 'type' => 'INT'],
        ['value' => $destination_id, 'type' => 'INT'],
        ['value' => $type_id, 'type' => 'INT'],
        ['value' => $libelle, 'type' => 'TEXT'],
        ['value' => $valeur, 'type' => 'TEXT'],
        ['value' => $description, 'type' => 'TEXT'],
        ['value' => $condition, 'type' => 'TEXT'],
        ['value' => $prospect_id, 'type' => 'INT'],
        ['value' => $lot_id, 'type' => 'TEXT'],
        ['value' => $partenaire_id, 'type' => 'TEXT'],
        ['value' => $date_debut, 'type' => 'TEXT'],
        ['value' => $date_fin, 'type' => 'TEXT'],
        ['value' => $apercu, 'type' => 'INT'],
        ['value' => $actif, 'type' => 'INT'],
    ];
    $result = $storedProcedure->call('creamaj_offre', $params, false, PDO::FETCH_ASSOC, 1);

//    var_dump($_POST);

    if(isset($result['result'])){

        header('Location: index.php?p=offre&n=1&id='.$result['id']);
    }
    else{
        $notifications['error'][]= 'Le formulaire comporte une erreur.';
    }
}
iFrameDefaultMenuLeft();
?>

<div class="box-side-middle pattern-shadow" style="background: white; padding: 5px 14px">
    <form method="post" autocomplete="off">

        <div class="box-tile-button">
            <h1>Créer une offre : <?= !is_null($type_selected) ? $type_selected['typ_lib'] : '' ?></h1>
            <div class="box-btn-title">
                <button class="pattern-button" type="submit" name="create_offre"><i class="fas fa-save"></i>Enregistrer</button>
            </div>
        </div>
        <p>Pour la création d'une offre il faut renseigner les différents champs ci dessous.<br/>
            Vous pouvez pré-remplir le formulaire en sélectionnant le type de l'offre.</p><br/>

        <div class="container-part-form">
            <div class="box-part-form">
                <h3>Informations générales</h3>
                <div class="box-form box-form-textarea">
                    <label for="prospect_id" class="label-input-100">N° Prospect</label>
                    <div class="container-inputs">
                        <div class="box-inputs">
                            <input required type="text"  id="prospect_id" name="prospect_id" class="input-text input-dependant" data-target="lot_id">
                            <button type="button" class="pattern-button-border-icon btn-search" id="btn-search-prospect"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="exemple-placeholder" id="result-search-prospect">
                            <span>Permet de récupérer toutes les infos d'un prospect dans CRM</span>
                            <span>Cliquer dans le bouton de recherche pour selectionner un prospect</span>
                        </div>
                    </div>
                </div>
                <div id="wrapper-box-form-lot" class="wrapper-element-form-hidden" data-name="lot_id">
                    <div class="box-form box-form-textarea">
                        <label for="lot_id" class="label-input-100">N° Lot</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <input required type="text"  id="lot_id" name="lot_id" class="input-text input-dependant" data-target="infos_offre">
                                <button type="button" class="pattern-button-border-icon btn-search" id="btn-search-lot"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="exemple-placeholder"  id="result-search-lot">
                                <span>Permet de récupérer toutes les infos d'un lot dans stock</span>
                                <span>Cliquer dans le bouton de recherche pour selectionner un lot</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="wrapper-box-form-destination" class="wrapper-element-form-hidden" data-name="infos_offre" >
                    <div class="box-form">
                        <label for="destination" class="label-input-100">Destination</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <select required id="destination" name="destination" data-target="type">
                                    <option value="0">-- Selectionner type d'offre</option>
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
                </div>
                <div id="wrapper-box-form-type" class="wrapper-element-form-hidden" data-name="infos_offre">
                    <div class="box-form">
                        <label for="type" class="label-input-100">Type</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <select required id="type" name="type" class="input-dependant" data-target="last_part">
                                    <?php
                                    if(!is_null($type_selected)){
                                        ?>
                                        <option value="<?= $type_selected['typ_id'] ?>"><?= $type_selected['typ_lib'] ?></option>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <option value="0">-- Selectionner type d'offre</option>
                                        <!--                    <option data-destination-id="0" value="0">Créer nouveau type</option>-->
                                        <?php
                                        foreach ($types as $type){
                                            if(in_array($type["typ_destination_id"], [$destination_id, '-1'])){
                                                $selected = $type_id == $type["typ_id"] ? 'selected' : '';
                                                ?>
                                                <option <?= $selected ?> data-destination-id="<?= $type["typ_destination_id"] ?>" value="<?= $type["typ_id"] ?>"><?= $type["typ_lib"] ?></option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <span class="exemple-placeholder">Sélectionner le type d'offre.</span>
                        </div>
                    </div>
                </div>

                <div id="wrapper-box-form-dates" class="wrapper-element-form-hidden" data-name="last_part">
                    <div class="box-form box-form-textarea">
                        <label for="date_debut" class="label-input-100">Date de début</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <input type="text" required class="input-text datepicker input-text-320 input-dependant" id="date_debut" name="date_debut">
                            </div>
                            <div class="exemple-placeholder">
                                <span>Date de declanchement de l'offre</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="wrapper-box-form-dates" class="wrapper-element-form-hidden" data-name="last_part">
                    <div class="box-form box-form-textarea">
                        <label for="date_fin" class="label-input-100">Date de fin</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <input type="text" required class="input-text datepicker input-text-320 input-dependant" id="date_fin" name="date_fin">
                            </div>
                            <div class="exemple-placeholder">
                                <span>Date de fin de l'offre</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="wrapper-box-form-dates" class="wrapper-element-form-hidden" data-name="last_part">
                    <div id="container-partenaire-input" class="box-form box-form-textarea">
                        <label for="prospect_id" class="label-input-100">N° Partenaire</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <input type="text" id="partenaire_id" name="partenaire_id" class="input-text">
                                <button type="button" class="pattern-button-border-icon btn-search" id="btn-search-partenaire"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="exemple-placeholder">
                                <span>Permet de récupérer toutes les infos d'un partenaire vente dans PRM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrapper-element-form-hidden separateur-part-form" data-name="last_part"></div>
            <div id="wrapper-box-form-dates" class="wrapper-element-form-hidden" data-name="last_part">
                <div class="box-part-form">
                    <h3>Offre</h3>
                    <div class="box-form">
                        <label for="libelle" class="label-input-100">Libellé</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <input type="text" id="libelle" readonly name="libelle" class="input-text input-text-320">
                            </div>
                            <span class="exemple-placeholder">Définir le libellé de l'offre</span>
                        </div>
                    </div>

                    <div id="wrapper-box-valeur">
                        <div class="box-form" >
                            <label for="valeur" class="label-input-100">Valeur</label>
                            <div class="container-inputs">
                                <div class="box-inputs">
                                    <input type="text" id="valeur" name="valeur" value="" class="input-text input-text-320">
                                </div>
                                <span class="exemple-placeholder">Exemple pour bon d'achat : 1000 €</span>
                            </div>
                        </div>
                    </div>

                    <div class="box-form box-form-textarea">
                        <label for="description" class="label-input-100">Description</label>
                        <div class="container-inputs">
                            <div class="box-inputs">
                                <textarea id="description" name="description" readonly cols="50" rows="3"></textarea>
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
                                <textarea id="condition" name="condition" readonly cols="50"  rows="3"></textarea>
                            </div>
                            <div class="exemple-placeholder">
                                <span>(optionnel) Décrire les condition de l'offre</span>
                                <span>Si vide, l'offre est sans condition</span>
                            </div>
                        </div>
                    </div>
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
                                <label class="label-input">Tapez votre recherche de mots clés ici :</label>
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
                                        <th>Code</th>
                                        <th>Nom, Prénom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Date création</th>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="box-center-button" style="">
                            <button class="pattern-button-icon" type="button" id="btn-select-prospect"><i class="fas fa-check"></i> Sélectionner</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="popup-search-lot" class="popup-container-resultat"  style="display: none;">
            <div class="popup-content-resultat">
                <div class="popup-resultat" id="container-popup-search-lot">
                    <button class="btn-cancel-popup pattern-shadow" type="button"><i class="fas fa-times"></i></button>
                    <h2>Rechercher un lot</h2>
                    <div class="container-form-centre-defaut" method="post">
                        <div style="display: flex; align-items: center">
                            <div class="box-form box-form-textarea" style="display: flex; flex-direction: column">
                                <label class="label-input">Tapez votre recherche de mots clés ici :</label>
                                <div class="container-inputs">
                                    <div class="box-inputs">
                                        <input type="text" id="recherche_lot" class="input-text input-text-320">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="contain-result-lot">
                            <p id="commentaire-result-lot"></p>

                            <div id="container-table-lot">
                                <table id="table-lot">
                                    <tr>
                                        <th></th>
                                        <th>Prog code</th>
                                        <th>Prog lib</th>
                                        <th>Prog lib long</th>
                                        <th>Lot code</th>
                                        <th>Lot num</th>
                                        <th>Lot bat</th>
                                        <th>Adresse</th>
                                        <th>Code postal</th>
                                        <th>Localité</th>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="box-center-button" style="">
                            <button class="pattern-button-icon" type="button" id="btn-select-lot"><i class="fas fa-check"></i> Sélectionner</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div id="popup-search-partenaire" class="popup-container-resultat"  style="display: none;">
            <div class="popup-content-resultat">
                <div class="popup-resultat" id="container-popup-search-partenaire">
                    <button class="btn-cancel-popup pattern-shadow" type="button"><i class="fas fa-times"></i></button>
                    <h2>Rechercher un partenaire</h2>
                    <div class="container-form-centre-defaut" method="post">
                        <div style="display: flex; align-items: center">
                            <div class="box-form box-form-textarea" style="display: flex; flex-direction: column">
                                <label class="label-input">Tapez votre recherche de mots clés ici :</label>
                                <div class="container-inputs">
                                    <div class="box-inputs">
                                        <input type="text" id="recherche_partenaire" class="input-text input-text-320">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="contain-result-partenaire">
                            <p id="commentaire-result-partenaire"></p>

                            <div id="container-table-partenaire">
                                <table id="table-partenaire">
                                    <tr>
                                        <th></th>
                                        <th>Partenaire</th>
                                        <th>Réseau</th>
                                        <th>Titre</th>
                                        <th>Email</th>
                                        <th>Tels</th>
                                        <th>Ville</th>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="box-center-button" style="">
                            <button class="pattern-button-icon" type="button" id="btn-select-partenaire"><i class="fas fa-check"></i> Sélectionner</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </form>
</div>

<script>
    const offreType = <?= json_encode($types) ?>;
    const listeCommand = <?= json_encode($liste_command) ?>;
    const listeCommandBD = <?= json_encode($liste_command_bd) ?>;
</script>

<?php
iFrameDefaultMenuLeft();