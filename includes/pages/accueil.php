<?php iFrameDefaultMenuLeft() ?>

<?php
$liste_offre = $storedProcedure->call('liste_offre', [
    ['value' => $util_id, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
    ['value' => null, 'type' => 'INT'],
], true, PDO::FETCH_ASSOC, 1);
?>

<div class="box-side-middle " style="background: transparent;">
    <h2 class="">Les offres commerciales</h2>

    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor lacus quis neque posuere, ut finibus arcu molestie. Quisque convallis condimentum magna quis maximus. Curabitur id ultrices tellus, non accumsan orci. Etiam a erat eget augue dignissim lacinia vitae at velit. Vivamus imperdiet dignissim nibh sed ultrices.</p>

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
                            <span>Expiration : <?= $offre['expiration'] ?> jours</span>
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

