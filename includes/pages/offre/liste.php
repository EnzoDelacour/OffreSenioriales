<?php
iFrameDefaultMenuLeft();

$numeroPage = 1;
if(isset($_GET['pa']) && is_numeric($_GET['pa']) && $_GET['pa'] > 0){
    $numeroPage = $_GET['pa'];
}

$ligne = 20;
$lignePossible = [10, 20, 50, 100, 200];
if(isset($_GET['li']) && is_numeric($_GET['li']) && $_GET['li'] > 0 && in_array($_GET['li'], $lignePossible)){
    $ligne = $_GET['li'];
}



$visuel = 2;
if(isset($_GET['vi']) && in_array($_GET['vi'], [1, 2])){
    $visuel = $_GET['vi'];
}

$destination_id = null;
if(isset($_GET['d']) && in_array($_GET['d'], [1, 2])){
    $destination_id = $_GET['d'];
}

$type_id = null;
if(isset($_GET['t']) && is_numeric($_GET['t']) && $_GET['t'] > 0){
    $type_id = $_GET['t'];
}

$statut_id = null;
if(isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 0){
    $statut_id = $_GET['s'];
}

$commercial_id = null;
if(isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] > 0){
    $commercial_id = $_GET['c'];
}

$residence_id = null;
if(isset($_GET['r']) && strlen($_GET['r']) == 4){
    $residence_id = $_GET['r'];
}

$prospect_id = null;
if(isset($_GET['pr']) && is_numeric($_GET['pr']) && $_GET['pr'] > 0){
    $prospect_id = $_GET['pr'];
}

$requete_liste = $storedProcedure->call('liste_offre', [
    ['value' => $util_id, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => $destination_id, 'type' => 'INT'],
    ['value' => $type_id, 'type' => 'INT'],
    ['value' => $statut_id, 'type' => 'INT'],
    ['value' => $commercial_id, 'type' => 'INT'],
    ['value' => $residence_id, 'type' => 'TEXT'],
    ['value' => $prospect_id, 'type' => 'INT'],
    ['value' => $numeroPage, 'type' => 'INT'],
    ['value' => $ligne, 'type' => 'INT'],
], true, PDO::FETCH_ASSOC, 2);

$liste_offre = $requete_liste[0];
$pagination_offre = $requete_liste[1][0];


$liste_statut = $storedProcedure->call('liste_statut', [['value' => $util_id, 'type' => 'INT']], true, PDO::FETCH_ASSOC, 1);


$liste_type = liste_type($pdoConnection, $util_id);

$liste_commercial = $storedProcedure->call('liste_commercials', [['value' => $util_id, 'type' => 'INT']]);
$liste_residence = $storedProcedure->call('liste_residence', [['value' => $util_id, 'type' => 'INT']]);
$liste_prospect_offre = $storedProcedure->call('liste_prospect_offre', [['value' => $util_id, 'type' => 'INT']]);
?>

<div class="box-side-middle pattern-shadow" style="background: white; padding: 5px 14px">
    <?php
//    var_dump($liste_offre);
    ?>
    <h1>Liste des offres</h1>

    <div id="container-filtre-offres">
        <div id="box-filtre-offre">
            <span class="span-filtre">Filtres :</span>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; grid-gap: 30px">
                <div style="display: flex; flex-direction: column;">
                    <label>Destination : </label>
                    <select class="select-choosen" onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d='+this.options[this.selectedIndex].value+'&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>&r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>'">
                        <option value="0" <?= is_null($destination_id) ? 'selected' : '' ?>>- Tous -</option>
                        <option value="1" <?= $destination_id == 1 ? 'selected' : '' ?>>Vente</option>
                        <option value="2" <?= $destination_id == 2 ? 'selected' : '' ?>>Location</option>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Type d'offre : </label>
                    <select class="select-choosen" style="width: 100%" onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=' + this.options[this.selectedIndex].value + '&s=<?= $statut_id ?>&c=<?= $commercial_id ?>&r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>'">
                        <option value="0">- Tous -</option>
                        <?php
                        foreach ($liste_type as $type){
                            $selected = $type_id == $type['typ_id'] ? 'selected' : '';
                            ?>
                            <option value="<?= $type['typ_id'] ?>" <?= $selected ?>><?= $type['typ_lib'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Statut : </label>
                    <select class="select-choosen" onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=' + this.options[this.selectedIndex].value + '&c=<?= $commercial_id ?>&r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>' ">
                        <option value="0">- Tous</option>
                        <?php
                        foreach ($liste_statut as $statut){
                            if($statut['ost_id'] >= 4){
                                $selected = $statut_id == $statut['ost_id'] ? 'selected' : '';
                                ?>
                                <option value="<?= $statut['ost_id'] ?>" <?= $selected ?>><?= $statut['ost_lib'] ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Commercial : </label>
                    <select class="select-choosen" onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=' +this.options[this.selectedIndex].value + '&r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>'">
                        <option value="0">- Tous -</option>
                        <?php
                        foreach ($liste_commercial as $commercial){
                            $selected = $commercial_id == $commercial['u_id'] ? 'selected' : '';
                            ?>
                            <option value="<?= $commercial['u_id'] ?>" <?= $selected ?>><?= $commercial['u_nom'] . ' ' . $commercial['u_prenom'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Résidence : </label>
                    <select class="select-choosen" onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>&r=' +this.options[this.selectedIndex].value + '&pr=<?= $prospect_id ?>&li=<?= $ligne ?>'">
                        <option value="0">- Tous -</option>
                        <?php
                        foreach ($liste_residence as $residence){
                            $selected = $residence_id == $residence['prog_code'] ? 'selected' : '';
                            ?>
                            <option value="<?= $residence['prog_code'] ?>" <?= $selected ?>><?= $residence['prog_lib'] ?> (<?= $residence['nb_offre'] ?>)</option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Prospect : </label>
                    <select class="select-choosen" onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>&r=<?= $residence_id ?>&pr=' +this.options[this.selectedIndex].value+'&li=<?= $ligne ?>'">
                        <option value="0">- Tous -</option>
                        <?php
                        foreach ($liste_prospect_offre as $prospect){
                            $selected = $prospect_id == $prospect['p_id'] ? 'selected' : '';
                            ?>
                            <option value="<?= $prospect['p_id'] ?>" <?= $selected ?>><?= $prospect['p_nom'] . ' ' . $prospect['p_prenom'] ?> </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div id="contain-box-visuel-offre" style="justify-content: flex-end">
        <span>Visuel :</span>
        <div id="box-visuel-offre" >
            <a href="<?= buildLink('offre', ['vi' => 1, 'd' => $destination_id, 't' => $type_id, 's' => $statut_id]) ?>" class="btn-visuel <?= $visuel == 1 ? 'btn-visuel-selected' : '' ?>" >
                <i class="fas fa-list-ul"></i>
            </a>
            <a href="<?= buildLink('offre', ['vi' => 2, 'd' => $destination_id, 't' => $type_id, 's' => $statut_id]) ?>" class="btn-visuel <?= $visuel == 2 ? 'btn-visuel-selected' : '' ?>">
                <i class="fas fa-table"></i>
            </a>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center">
        <p>Nombre de résultats : <b><?= $pagination_offre['ligne_total'] ?></b></p>
        <div class="container-pagination-select">
            <label>Page : </label>
            <a href="index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>&pa=<?= $numeroPage - 1 ?>"><i class="fas fa-angle-left"></i></a>
            <select   onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>&r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>&pa=' +this.options[this.selectedIndex].value">
                <?php
                for($i = 0; $i < intval($pagination_offre['ligne_total_page']); $i++){
                    ?>
                    <option value="<?= $i + 1 ?>" <?= $numeroPage == ($i + 1) ? 'selected' : '' ?>><?= $i + 1 ?></option>
                    <?php
                }
                ?>
            </select>
            <a href="index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=<?= $ligne ?>&pa=<?= ($numeroPage + 1) > $pagination_offre['ligne_total_page'] ? $numeroPage : $numeroPage + 1 ?>"><i class="fas fa-angle-right"></i></a>

            <label for="select-nombre-ligne">Par :</label>
            <select id="select-nombre-ligne"  onchange="window.location.href= 'index.php?p=offre&vi=<?= $visuel ?>&d=<?= $destination_id ?>&t=<?= $type_id ?>&s=<?= $statut_id ?>&c=<?= $commercial_id ?>&r=<?= $residence_id ?>&pr=<?= $prospect_id ?>&li=' +this.options[this.selectedIndex].value">
                <?php
                foreach ($lignePossible as $nbLigne){
                    ?>
                    <option value="<?= $nbLigne ?>" <?= $ligne == $nbLigne ? 'selected' : '' ?>><?= $nbLigne ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>

    <?php


    if($visuel == 1){
        ?>
        <table id="table-offre">
            <tr>
                <th>ID</th>
                <th>Destination</th>
                <th>Libellé</th>
                <th>Prospect</th>
                <th>Lot</th>
                <th>Statut</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Expiration</th>
            </tr>
            <?php
            $i = 0;
            foreach ($liste_offre as $offre){
                ?>
                <tr onclick="window.location.href = 'index.php?p=offre&id=<?= $offre['ofr_id'] ?>'">
                    <td><?= $offre['ofr_id'] ?></td>
                    <td><?= $offre['dst_lib'] ?></td>
                    <td><?= $offre['ofr_lib'] ?></td>
                    <td><?= $offre['p_nom'] . ' ' . $offre['p_prenom'] . ' (P' . str_pad($offre['p_id'], 6, 0, STR_PAD_LEFT) .')' ?></td>
                    <td><?= $offre['prog_lib'] . ' (' . $offre['lot_code'] . ')' ?></td>
                    <td>
                        <?php
                        $colorStatut = $offre['ost_id'] == 4 ? '#0081ff' : ($offre['ost_id'] == 5 ? '#23a472' : '#e51c60') ;
                        $classStatut = $offre['ost_id'] == 4 ? 'offre-en-cours' : ($offre['ost_id'] == 5 ? 'offre-valide' : 'offre-expire') ;
                        ?>
                        <b class="<?= $classStatut ?>" style="padding: 0 5px; border-radius: 3px; font-size: 0.86em; white-space: nowrap; width: 60px; display: inline-block; text-align: center"><?= $offre['ost_lib']  ?></b>

                    </td>
                    <td><?= convertDate($offre['ofr_datedebut'])  ?></td>
                    <td><?= convertDate($offre['ofr_datefin'])  ?></td>
                    <td><?= intval($offre['expiration']) < 0 ? '<b style="color: #e51c60">Expirée</b>' : $offre['expiration'] . ' jours' ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
        <?php
    }
    else{
        ?>
        <div id="container-grid-offre">
            <?php
            $i = 0;
            foreach ($liste_offre as $offre){
                ?>
                <a href="index.php?p=offre&id=<?= $offre['ofr_id'] ?>" class="box-offre-grid pattern-shadow pattern-not-selectable">
                    <span class="title-offre-grid"><?= $offre['ofr_lib'] ?></span>

                    <div class="box-img-offre-grid"  data-path="documents/<?= $offre['tmp_filepath'] ?>">
                        <img alt="image offre" loading="lazy" decoding="async" src="documents/offres/image/chargement.png">
                    </div>
                    <div class="box-infos-offre-grid" style="font-size: 0.875rem; line-height: 1.25rem;">
                        <div>
                            <span>Prospect : <?= $offre['p_nom'] . ' ' . $offre['p_prenom'] . ' (P' . str_pad($offre['p_id'], 6, 0, STR_PAD_LEFT) .')'  ?></span>
                        </div>
                        <div>
                            <span>Lot : <?= $offre['prog_lib'] . ' (' . $offre['lot_code'] . ')' ?></span>
                        </div>
                        <div>
                            <span>Statut : <?= $offre['ost_lib'] ?></span>
                        </div>
                        <div>
                            <span>Expiration : <?= intval($offre['expiration']) < 0 ? '<b style="color: #e51c60">Expirée</b>' : $offre['expiration'] . ' jours' ?></span>
                        </div>
                    </div>
                </a>
                <?php
            }
            ?>

        </div>
        <?php
    }
    ?>

</div>

<?php
iFrameDefaultMenuRight();
?>
