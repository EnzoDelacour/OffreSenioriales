<?php
function iframeFilAriane($liste, $etape_num){
    ?>
    <ul id="fil-ariane-template">
        <?php
        for($i = 1; $i < count($liste); $i++){
            $class = '';
            $link = '';
            if($etape_num > $i){
                $class = 'etape-fini';
            }
            elseif ($etape_num == $i){
                $class = 'etape-en-cours';
            }
            ?>
            <li class="<?= $class ?>"><a href="<?= $link ?>"><?= $liste[$i] ?></a></li>
            <li><i class="fas fa-chevron-right"></i></li>
            <?php
        }
        ?>
    </ul>
    <?php
}

function iFrameDefaultMenuLeft(){
    ?>
    <div class="box-side-left"></div>
    <?php
}

function iFrameDefaultMenuRight(){
    ?>
    <div class="box-side-right"></div>
    <?php
}


function iFrameTemplateMenuLeft($libelle, $description, $template, $type, $champs = []){
    ?>
    <div class="box-side-left" id="left-menu-main">
        <div id="left-menu-editing-template">
            <div style="display: flex; justify-content: center; margin-bottom: 15px;">
                <button type="button" class="pattern-button" id="save-template">Enregistrer</button>
            </div>
            <strong style="display: inline-block; margin-bottom: 5px "><?= $type['typ_lib'] ?></strong>
            <div class="" style="display: flex; flex-direction: column; justify-content: flex-start; align-self: flex-start">
                <label>Libellé</label>
                <input type="text" id="libelle" name="libelle" class="input-text " value="<?= $template['tmp_lib'] ?>" style="margin: 0;">
            </div>
            <div class="" style="display: flex; flex-direction: column; justify-content: flex-start; align-self: flex-start">
                <label>Description</label>
                <textarea><?= $template['tmp_description'] ?></textarea>
            </div>

            <div id="container-btn-form-template">
                <button id="btn-popup-remplir-formulaire" class="pattern-button-border-icon">Remplir formulaire PDF</button>
            </div>

            <b style="display: inline-block; margin-top: 20px; ">Champs :</b>
            <div id="container-liste-champs">
                <?php
                foreach ($champs as $key => $champ){
                    ?>
                    <div class="box-champ-to-add" data-champ-id="<?= $champ['id'] ?>">
                        <div class="box-champ-ordre">
                            <?= $key + 1 ?>
                        </div>
                        <div class="box-champ-value">
                            <span class="pattern-not-selectable champ-draggable"><?= $champ['name'] ?></span>
                            <?php
                            setToolTip('<div class="box-positionner-in-form pattern-tooltip-element" data-in-form="0"><i class="fas fa-file-signature"></i></div>', 'Placé dans formulaire', ['white-space' => 'nowrap', 'position' => 'top']);
                            setToolTip('<div class="box-positionner-in-file pattern-tooltip-element" data-in-file="0"><i class="fas fa-file"></i></div>', 'Placé dans document', ['white-space' => 'nowrap', 'position' => 'top']);
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

function iFrameTemplateMenuRight(){
    ?>
    <div class="box-side-right">
        <div id="right-menu-editing-template">

            <div style="display: flex; align-items: center; margin-bottom: 10px">
                <span>Afficher grille :</span>
                <div class="box-template-option" id="btn-show-grid" style="margin-left: 10px">
                    <i class="fas fa-th"></i>
                </div>
            </div>

            <div>
                <span>Couleur :</span>
                <input type="color">
            </div>
            <div style="margin-top: 10px">
                <span style="margin-bottom: 5px; display:block;">Alignement :</span>
                <div style="display:flex;">
                    <div class="box-template-option">
                        <i class="fas fa-align-right"></i>
                    </div>
                    <div class="box-template-option">
                        <i class="fas fa-align-center"></i>
                    </div>
                    <div class="box-template-option">
                        <i class="fas fa-align-left"></i>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: center; margin-top: 15px;">
<!--                <button type="button" class="pattern-button" id="save-template">Voir aperçu</button>-->
            </div>
        </div>
    </div>
    <?php
}


function iFrameOffreMenuLeft($offre){
    ?>
    <div class="box-side-left" id="left-menu-main">
        <div id="left-menu-editing-template">

            <div class="box-center-button">
                <button class="pattern-button">Validation de l'offre</button>
            </div>

            <div class="box-element-left-menu-offre">
                <span><b>Statut : </b> <?= ($offre['ost_lib']) ?></span>
            </div>

            <div class="box-element-left-menu-offre">
                <b>Prospect n°<?= $offre['p_id'] ?> : </b>
                <span><?= $offre['p_nom'] . ' ' . $offre['p_prenom'] ?></span>
                <span><?= $offre['p_email'] ?></span>
                <span><?= $offre['p_telephone'] ?></span>
            </div>
            <div class="box-element-left-menu-offre">
                <b>Lot n°<?= $offre['lot_code'] ?> : </b>
                <span><?= $offre['prog_liblong'] ?></span>
                <span><?= $offre['lot_numero_usuel'] ?></span>
                <span><?= $offre['adr_adresse'] ?></span>
                <span><?= $offre['adr_codepostal'] ?></span>
                <span><?= $offre['adr_localite'] ?></span>
            </div>

            <div class="box-element-left-menu-offre ">
                <span class="grid-2-fr"><b>Date début : </b> <?= convertDate($offre['ofr_datedebut']) ?></span>
                <span class="grid-2-fr"><b>Date fin : </b> <?= convertDate($offre['ofr_datefin']) ?></span>
            </div>

            <div class="box-element-left-menu-offre">
                <b>Description :</b>
                <p><?= $offre['ofr_description'] ?></p>
            </div>

            <div class="box-element-left-menu-offre">
                <b>Condition :</b>
                <p><?= $offre['ofr_condition'] ?></p>
            </div>
        </div>
    </div>
    <?php
}

function iFrameOffreMenuRight($offre){
    ?>
    <div class="box-side-right">
        <div id="right-menu-editing-template">
            <div class="box-center-button">
                <a target="_blank" href="document.php?doc=<?= $offre['doc_filepath'] ?>" class="pattern-button-border-icon" id="btn-download-offre"><i class="fas fa-download"></i> Télecharger</a>
            </div>
            <div class="box-center-button">
                <button class="pattern-button-border-icon" id="btn-import-offre"><i class="fas fa-file-upload"></i> Importer</button>
            </div>
        </div>
    </div>
    <?php
}