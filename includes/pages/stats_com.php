<?php iFrameDefaultMenuLeft() ?>

<?php
$liste_offre = $storedProcedure->call('liste_offre', [
    ['value' => $util_id, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => 4, 'type' => 'INT'],
    ['value' => $util_id, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => 1, 'type' => 'INT'],
    ['value' => 4, 'type' => 'INT'],
], true, PDO::FETCH_ASSOC, 1);

$liste_stat_offre = $storedProcedure->call('liste_statistique_commerciale', [['value' => $util_id, 'type' => 'INT'],], false);
?>

<div class="box-side-middle " style="background: transparent;">
    <h2 class="">Tableau de bord offres commerciales</h2>
    <p>Bonjour <?= $util_prenom ?>, voici les statistiques de vos offres commerciales générées.</p>

    <div id="container-grid-dashboard-com">
        <div class="box-stat-com pattern-shadow" style="">
            <div class="box-col-stat-com" style=" background: #182139;"></div>
            <div class="box-content-stat-com" style="">
                <b style=""><?= $liste_stat_offre['total'] ?></b>
                <p>Offres totales</p>
            </div>
        </div>
        <div class="box-stat-com pattern-shadow">
            <div class="box-col-stat-com" style="background: #0081ff;"></div>
            <div class="box-content-stat-com">
                <b style="font-size: 1.2em"><?= $liste_stat_offre['en_cours'] ?></b>
                <p>Offres en cours</p>
            </div>
        </div>
        <div class="box-stat-com pattern-shadow">
            <div class="box-col-stat-com" style="background: #23a472;"></div>
            <div class="box-content-stat-com">
                <b style="font-size: 1.2em"><?= $liste_stat_offre['en_valide'] ?></b>
                <p>Offres validées</p>
            </div>
        </div>
        <div class="box-stat-com pattern-shadow">
            <div class="box-col-stat-com" style="background: #e51c60;"></div>
            <div class="box-content-stat-com" >
                <b style="font-size: 1.2em"><?= $liste_stat_offre['en_expire'] ?></b>
                <p>Offres expirées</p>
            </div>
        </div>
    </div>

    <h4 class="">Liste des offres en cours</h4>

    <div id="container-grid-offre">
        <?php
        if(count($liste_offre) > 0){
            $i = 0;
            foreach ($liste_offre as $offre){
                if($i == 4){
                    break;
                }
                ?>
                <a href="index.php?p=offre&id=<?= $offre['ofr_id'] ?>" class="box-offre-grid pattern-shadow pattern-not-selectable">
                    <span class="title-offre-grid"><?= $offre['ofr_lib'] ?></span>

                    <div class="box-img-offre-grid" data-path="documents/<?= $offre['tmp_filepath'] ?>">
                        <!--                    <img alt="image offre" src="documents/offres/image/Flyer 1.png">-->
                    </div>
                    <div class="box-infos-offre-grid">
                        <div>
                            <span>Prospect : <?= $offre['p_nom'] . ' ' . $offre['p_prenom'] ?></span>
                        </div>
                        <div>
                            <span>Lot : <?= $offre['prog_lib'] ?></span>
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
                $i++;
            }
        }
        else{
            echo '<b>Aucune offre en cours</b>';
        }

        ?>

    </div>

    <div class="box-center-button" style="margin-top: 15px">
        <a href="index.php?p=offre" class="pattern-button-icon" type="button" id="btn-add-champ"><i class="fas fa-list"></i></i> Voir plus</a>
    </div>
</div>
<?php iFrameDefaultMenuRight(); ?>

