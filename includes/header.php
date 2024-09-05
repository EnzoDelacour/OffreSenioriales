<header class="">
    <div class="container-main header">
        <div id="box-left-header" class="box-side-left">
            <a class="home" href="index.php"><img src="assets/images/logo_senioriales/Logo blanc.png"></a>
        </div>
        <div id="box-right-header" class="box-side-right">
            <div id="box-btn-header">
                <a class="btn-header  <?= $page == 'home' ? 'btn-header-actif' : '' ?>" href="index.php?"><div style="display: flex; align-items: center"><i class="fas fa-home" style="margin-right: 5px "></i> Accueil</div><div class="barre-btn-header"></div></a>
                <?php
                if($isAdmin){
                    ?>

                    <?php
                }
                ?>
                <a class="btn-header <?= $page == 'typeoffre' ? 'btn-header-actif' : '' ?>" href="index.php?p=typeoffre"><div style="display: flex; align-items: center"><i class="fas fa-list" style="margin-right: 5px "></i> Catalogue</div><div class="barre-btn-header"></div></a>
                <a class="btn-header  <?= $page == 'create_offre' ? 'btn-header-actif' : '' ?>" href="index.php?p=create_offre"><div style="display: flex; align-items: center"><i class="fas fa-plus-circle" style="margin-right: 5px "></i> Créer offre</div><div class="barre-btn-header"></div></a>
                <a class="btn-header <?= $page == 'offre' ? 'btn-header-actif' : '' ?>" href="index.php?p=offre"><div style="display: flex; align-items: center"><i class="fas fa-file-image" style="margin-right: 5px "></i> Suivi offres</div><div class="barre-btn-header"></div></a>
                <?php
                if($isAdmin){
                    ?>
                    <a class="btn-header <?= $page == 'stats' ? 'btn-header-actif' : '' ?>" href="index.php?p=stats"><div style="display: flex; align-items: center"><i class="fas fa-chart-line" style="margin-right: 5px "></i> Tableau de bord</div><div class="barre-btn-header"></div></a>
                    <a class="btn-header <?= $page == 'stats_com' ? 'btn-header-actif' : '' ?>" href="index.php?p=stats_com"><div style="display: flex; align-items: center"><i class="fas fa-chart-line" style="margin-right: 5px "></i> TdB Ind</div><div class="barre-btn-header"></div></a>
                    <?php
                }
                else{
                    ?>
                    <a class="btn-header <?= $page == 'stats_com' ? 'btn-header-actif' : '' ?>" href="index.php?p=stats_com"><div style="display: flex; align-items: center"><i class="fas fa-chart-line" style="margin-right: 5px "></i> Tableau de bord</div><div class="barre-btn-header"></div></a>
                    <?php
                }
                ?>
            </div>
            <div id="box-profil" class="pattern-not-selectable">
                <div id="box-image-profile">
                    <?php
                    if(strlen($util_picture) > 0){
                        echo '<img src="'.$util_picture.'">';
                    }
                    else{
                        echo '<span id="letter-image-profil">'.$util_prenom[0].'</span>';
                    }
                    ?>
                </div>
                <span id="login-profil"><?= $util_login ?></span>
                <div id="wrapper-box-menu-sup" class="pattern-shadow">
                    <div id="btn-deconnect" class="pattern-button-cancel" style="text-align: center">
                        <i class="fas fa-power-off"></i>
                        Se déconnecter
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>