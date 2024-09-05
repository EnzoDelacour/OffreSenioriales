<?php
$liste_offre = [];

$temps = $_GET['temps'];
$xAxis = $_GET['xAxis'];
$yAxis = $_GET['yAxis'];
$indicateur = $_GET['indicateur'];
$destination = null;
if(strlen($_GET['destination']) > 0 && $_GET['destination'] != 'null'){
    $destination = $_GET['destination'];
}

$type_offre = null;
if(strlen($_GET['type_offre']) > 0 && $_GET['type_offre'] != 'null'){
    $type_offre = $_GET['type_offre'];
}

$residence = null;
if(strlen($_GET['residence']) > 0 && $_GET['residence'] != 'null'){
    $residence = $_GET['residence'];
}

$statut = null;
if(strlen($_GET['statut']) > 0 && $_GET['statut'] != 'null'){
    $statut = $_GET['statut'];
}

$numeroPage = 1;
if(isset($_GET['pa'])){
    $numeroPage = intval($_GET['pa']);
}
$ligne = 100;



?>

<div class="container-main" style="background: white">
    <div style="grid-column: span 3 / span 3; padding: 50px">
        <?php

        $requete_liste = $storedProcedure->call('liste_detail_statistique', [
            ['value' => $util_id, 'type' => 'INT'],
            ['value' => $numeroPage, 'type' => 'INT'],
            ['value' => $ligne, 'type' => 'INT'],
            ['value' => $xAxis, 'type' => 'TEXT'],
            ['value' => $yAxis, 'type' => 'TEXT'],
            ['value' => $temps, 'type' => 'INT'],
            ['value' => $indicateur, 'type' => 'INT'],
            ['value' => $destination, 'type' => 'TEXT'],
            ['value' => $type_offre, 'type' => 'TEXT'],
            ['value' => $residence, 'type' => 'TEXT'],
            ['value' => $statut, 'type' => 'TEXT'],
        ], true, PDO::FETCH_ASSOC, 2);

        $liste_offre = $requete_liste[0];
        $pagination_offre = $requete_liste[1][0];

//        var_dump($liste_offre);

        ?>
        <div class="box-tile-button">
            <h1>Détail statistique</h1>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center">
            <p>Nombre de résultats : <b><?= $pagination_offre['ligne_total'] ?></b></p>
            <div class="container-pagination-select">
                <label>Page : </label>
                <a href="index.php?iframe=liste_offre&xAxis=<?= $xAxis ?>&yAxis=<?= $yAxis ?>&temps=<?= $temps ?>&indicateur=<?= $indicateur ?>&destination=<?= $destination ?>&type_offre=<?= $type_offre ?>&residence=<?= $residence ?>&statut=<?= $statut ?>&pa=<?= $numeroPage - 1 ?>"><i class="fas fa-angle-left"></i></a>
                <select   onchange="window.location.href= 'index.php?iframe=liste_offre&xAxis=<?= $xAxis ?>&yAxis=<?= $yAxis ?>&temps=<?= $temps ?>&indicateur=<?= $indicateur ?>&destination=<?= $destination ?>&type_offre=<?= $type_offre ?>&residence=<?= $residence ?>&statut=<?= $statut ?>&li=<?= $ligne ?>&pa=' +this.options[this.selectedIndex].value">
                    <?php
                    for($i = 0; $i < intval($pagination_offre['ligne_total_page']); $i++){
                        ?>
                        <option value="<?= $i + 1 ?>" <?= $numeroPage == ($i + 1) ? 'selected' : '' ?>><?= $i + 1 ?></option>
                        <?php
                    }
                    ?>
                </select>
                <a href="index.php?iframe=liste_offre&xAxis=<?= $xAxis ?>&yAxis=<?= $yAxis ?>&temps=<?= $temps ?>&indicateur=<?= $indicateur ?>&destination=<?= $destination ?>&type_offre=<?= $type_offre ?>&residence=<?= $residence ?>&statut=<?= $statut ?>&pa=<?= $numeroPage + 1 ?>"><i class="fas fa-angle-right"></i></a>

                <label for="select-nombre-ligne">Par : 100</label>
            </div>
        </div>
        <table id="table-offre">
            <tr>
                <th>ID</th>
                <th>Destination</th>
                <th>Résidence</th>
                <th>Libellé</th>
                <th>Prospect</th>
                <th>Statut</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Expiration</th>
            </tr>
            <?php
            $i = 0;
            if(count($liste_offre) > 0){
                foreach ($liste_offre as $offre){
                    ?>
                    <tr onclick="window.location.href = 'index.php?p=offre&id=<?= $offre['ofr_id'] ?>'">
                        <td><?= $offre['ofr_id'] ?></td>
                        <td><?= $offre['dst_lib'] ?></td>
                        <td><?= $offre['prog_lib'] ?></td>
                        <td><?= $offre['ofr_lib'] ?></td>
                        <td><?= $offre['p_nom'] . ' ' . $offre['p_prenom'] ?></td>
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
            }
            ?>
        </table>
    </div>
</div>