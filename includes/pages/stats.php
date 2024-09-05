<?php
iFrameDefaultMenuLeft();

?>
    <div class="box-side-middle pattern-shadow" style="background: white; padding: 5px 14px; margin: 0 60px; grid-column-start: 1; grid-column-end: 4; min-height: 100vh">
        <?php
        $dateNow = new DateTime();
        $periode = '1' . $dateNow->format('YW');
        if(isset($_GET['per'])){
            $periode = $_GET['per'];
        }


        $temps = 1;
        if(isset($_GET['tem'])){
            $temps = $_GET['tem'];
        }


        $indicateur = 1;
        if(isset($_GET['ind'])){
            $indicateur = $_GET['ind'];
        }

        $graphique = 'line';
        if(isset($_GET['gra'])){
            $graphique = $_GET['gra'];
        }


        $destination = null;
        if(isset($_GET['des'])){
            $destination = $_GET['des'];
        }

        $residence = null;
        if(isset($_GET['res'])){
            $residence = $_GET['res'];
        }

        $type = null;
        if(isset($_GET['typ'])){
            $type = $_GET['typ'];
        }

        $statut = null;
        if(isset($_GET['sta'])){
            $statut = $_GET['sta'];
        }

//        var_dump($_GET);



        $param_stats = $storedProcedure->call('liste_param_stats', [
            ['value'=>$util_id, 'type'=>'INT'],
        ], true, PDO::FETCH_GROUP|PDO::FETCH_ASSOC, 1);

        $countRowSet = 2;
        if(in_array($indicateur, [2, 3, 4, 5, 6])){
            $countRowSet = 3;
        }

        $result = $storedProcedure->call('liste_statistique', [
            ['value'=>$util_id, 'type'=>'INT'],
            ['value'=>$indicateur, 'type'=>'INT'],
            ['value'=>$periode, 'type'=>'TEXT'],
            ['value'=>$temps, 'type'=>'INT'],
            ['value'=> is_array($destination) ? json_encode($destination) : null, 'type'=>'TEXT'],
            ['value'=> is_array($type) ? json_encode($type) : null, 'type'=>'TEXT'],
            ['value'=> is_array($residence) ? json_encode($residence) : null, 'type'=>'TEXT'],
            ['value'=> is_array($statut) ? json_encode($statut) : null, 'type'=>'TEXT'],
        ], true, PDO::FETCH_GROUP|PDO::FETCH_ASSOC, $countRowSet);

        $liste_budget =  $storedProcedure->call('liste_statistique_budget', [
            ['value'=>$util_id, 'type'=>'INT'],
            ['value'=>$indicateur, 'type'=>'INT'],
            ['value'=>$periode, 'type'=>'TEXT'],
            ['value'=>$temps, 'type'=>'INT'],
            ['value'=> is_array($destination) ? json_encode($destination) : null, 'type'=>'TEXT'],
            ['value'=> is_array($type) ? json_encode($type) : null, 'type'=>'TEXT'],
            ['value'=> is_array($residence) ? json_encode($residence) : null, 'type'=>'TEXT'],
            ['value'=> is_array($statut) ? json_encode($statut) : null, 'type'=>'TEXT'],
        ], true, PDO::FETCH_GROUP|PDO::FETCH_ASSOC);



        $liste_statut = $storedProcedure->call('liste_statut', [['value' => $util_id, 'type' => 'INT']], true, PDO::FETCH_ASSOC, 1);
        $liste_type = liste_type($pdoConnection, $util_id);
        $liste_residence = $storedProcedure->call('liste_residence', [['value' => $util_id, 'type' => 'INT']]);



        $series = [];
        $xAxis = array_keys($result[0]);
        $firstVal = true;



        $series = [];
        $budget = [];
        if(in_array($indicateur, [2,3,4,5,6])){
            $yAxis = array_keys($result[2]);
            $liste_stats = $result[1];
//            var_dump($result[1]);

            $index = 0;
            foreach ($yAxis as $y){
                $series[$index] = ['name' => $y, 'data' => []];
                foreach ($xAxis as $x){
                    if(isset($liste_stats[$y])){
                        $keySearch = array_search($x, array_column($liste_stats[$y], 'x'));
                        if($keySearch !== false){
                            $series[$index]['data'][] = intval($liste_stats[$y][$keySearch]['y']);

                        }
                        else{
                            $series[$index]['data'][] = 0;
                            $budget[$x]['cout'][] = 0;
                        }
                    }
                    else{
                        $series[$index]['data'][] = 0;
                    }
                }
                $index++;
            }


        }
        else{
            foreach ($result[1] as $name => $data){
                $series[] = ['name' => $name, 'data' => array_map('intval', array_column($data, 'y'))];

                if($firstVal){
                    $firstVal = false;
                    $xAxis = array_column($data, 'x');
                }
            }
        }


        ?>

        <div class="box-tile-button">
            <h1>Tableau de bord</h1>

            <a href="export.php?exp=1&periode=<?= $periode ?>&dest=<?= urlencode(json_encode($destination, JSON_UNESCAPED_UNICODE)) ?>&type=<?= urlencode(json_encode($type, JSON_UNESCAPED_UNICODE)) ?>&residence=<?= urlencode(json_encode($residence, JSON_UNESCAPED_UNICODE)) ?>&statut=<?= urlencode(json_encode($statut, JSON_UNESCAPED_UNICODE)) ?>" target="_blank" type="button" class="pattern-button btn-export-excel" id="btn-export"><i class="fas fa-file-excel"></i> Export période</a>
        </div>
        <form method="get">
            <input type="hidden" name="p" value="stats">
            <div id="grid-param-stats">
                <div>
                    <b>Périmètre :</b>
                </div>
                <div class="box-form-stats">
                    <label>Période : </label>
                    <select class="select-stats select-choosen" id="periode_stats" name="per">
                        <option value="">- Choisissez une période -</option>
                        <?php
                        $tempGroupe = '';
                        foreach ($param_stats as $k => $tabdate){
                            ?>
                            <optgroup label="<?= $k ?>">
                                <?php
                                foreach ($tabdate as $date){
                                    $selected = $periode == $date['k'] ? 'selected' : '';
                                    ?>
                                    <option <?= $selected ?> value="<?= $date['k'] ?>"><?= $date['v'] ?></option>
                                    <?php
                                }
                                ?>
                            </optgroup>
                            <?php
                        }
                        ?>

                    </select>
                </div>
                <div class="box-form-stats">
                    <label>Temps : </label>
                    <select class="select-stats select-choosen" id="temps_stats" name="tem">
                        <option <?= $temps == '1' ? 'selected' : ''; ?> value="1">Jour</option>
                        <option <?= $temps == '2' ? 'selected' : ''; ?> value="2">Semaine</option>
                        <option <?= $temps == '3' ? 'selected' : ''; ?> value="3">Mois</option>
                        <option <?= $temps == '4' ? 'selected' : ''; ?> value="4">Trimestre</option>
                        <option <?= $temps == '5' ? 'selected' : ''; ?> value="5" >Année</option>
                        <option <?= $temps == '6' ? 'selected' : ''; ?> value="6" >Exercice</option>
                    </select>
                </div>
                <div class="box-form-stats">
                    <label>Indicateurs : </label>
                    <select class="select-stats select-choosen" id="indicateur_stats" name="ind">
                        <option value="1" <?= $indicateur == '1' ? 'selected' : ''; ?>>Nb offres</option>
                        <option value="2" <?= $indicateur == '2' ? 'selected' : ''; ?>>Nb statuts</option>
                        <option value="3" <?= $indicateur == '3' ? 'selected' : ''; ?>>Nb destinations</option>
                        <option value="4" <?= $indicateur == '4' ? 'selected' : ''; ?>>Nb résidences</option>
                        <option value="5" <?= $indicateur == '5' ? 'selected' : ''; ?>>Nb commerciales</option>
                        <?php
                        if(in_array($util_id, [2385, 35, 17])){
                            ?>
                            <option value="6" <?= $indicateur == '6' ? 'selected' : ''; ?>>Activité Commerciales</option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="box-form-stats">
                    <label>Type graphique : </label>
                    <select class="select-stats select-choosen" id="graphique_stats" name="gra">
                        <option <?= $graphique == 'line' ? 'selected' : ''; ?> value="line">Courbes</option>
                        <option <?= $graphique == 'spline' ? 'selected' : ''; ?> value="spline">Courbes arrondies</option>
                        <option <?= $graphique == 'column' ? 'selected' : ''; ?> value="column">Histogramme</option>
                        <option <?= $graphique == 'area' ? 'selected' : ''; ?> value="area">Aires</option>
                        <option <?= $graphique == 'areaspline' ? 'selected' : ''; ?> value="area">areaspline arrondies</option>
                    </select>
                </div>

                <div>
                    <b>Filtre :</b>
                </div>
                <div class="box-form-stats">
                    <label>Destination : </label>
                    <select class="select-stats select-choosen" multiple id="destination_stats" name="des[]">
                        <option value="1" <?= is_array($destination) && in_array(1, $destination) ? 'selected' : '' ?>>Vente</option>
                        <option value="2" <?= is_array($destination) && in_array(2, $destination) ? 'selected' : '' ?>>Location</option>
                    </select>
                </div>
                <div class="box-form-stats">
                    <label>Type d'offre : </label>
                    <select class="select-stats select-choosen" name="typ[]" multiple id="type_stats" style="width: 100%;">
                        <?php
                        foreach ($liste_type as $type_v){
                            $selected = is_array($type) && in_array($type_v['typ_id'], $type) ? 'selected' : '';
                            ?>
                            <option value="<?= $type_v['typ_id'] ?>" <?= $selected ?>><?= $type_v['typ_lib'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="box-form-stats">
                    <label>Résidence : </label>
                    <select class="select-stats select-choosen" multiple id="residence_stats" name="res[]">
                        <?php
                        foreach ($liste_residence as $residence_v){
                            $selected = is_array($residence) && in_array($residence_v['prog_code'], $residence) ? 'selected' : '';
                            ?>
                            <option value="<?= $residence_v['prog_code'] ?>" <?= $selected ?>><?= $residence_v['prog_lib'] ?> (<?= $residence_v['nb_offre'] ?>)</option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="box-form-stats">
                    <label>Statut : </label>
                    <select class="select-stats select-choosen" multiple id="statut_stats" name="sta[]">
                        <?php
                        foreach ($liste_statut as $statut_v){
                            if($statut_v['ost_id'] >= 4){
                                $selected = is_array($statut) && in_array($statut_v['ost_id'], $statut) ? 'selected' : '';
                                ?>
                                <option value="<?= $statut_v['ost_id'] ?>" <?= $selected ?>><?= $statut_v['ost_lib'] ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; color: white; margin-top: 10px">
                <button type="submit" id="link-stats" class="pattern-button">Valider</button>
            </div>
        </form>

        <div id="container-graphique" ></div>


        <h2>Tableau suivi budget</h2>
        <table style="width: auto">
            <tr>
                <th>Date</th>
                <th>Coût</th>
                <th>Valeur</th>
                <th>Valeur réel</th>
            </tr>
            <?php
            foreach ($xAxis as $x){
                $cout = 0;
                $valeur = 0;
                $valeurReel = 0;

                if(isset($liste_budget[$x])){
                    $cout = $liste_budget[$x][0]['total_cout'];
                    $valeur = $liste_budget[$x][0]['total_valeur'];
                    $valeurReel = $liste_budget[$x][0]['total_valeur_reel'];
                }


                ?>
                <tr>
                    <td><span style="color: #172B4D; font-weight: bold"><?= $x ?></span></td>
                    <td><?= (!is_numeric($cout) || in_array($cout,array(""," ","-","0"))) ? ('-') : (number_format($cout, 2, ',', ' ')." €") ?></td>
                    <td><?= (!is_numeric($valeur) || in_array($valeur,array(""," ","-","0"))) ? ('-') : (number_format($valeur, 2, ',', ' ')." €") ?></td>
                    <td><?= (!is_numeric($valeurReel) || in_array($valeurReel,array(""," ","-","0"))) ? ('-') : (number_format($valeurReel, 2, ',', ' ')." €") ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>

    <div id="popup-detail-stats" class="popup-container-resultat"  style="display: none;">
        <div class="popup-content-resultat">
            <div class="popup-resultat" id="container-popup-search-prospect">
                <button class="btn-cancel-popup pattern-shadow" type="button"><i class="fas fa-times"></i></button>

                <iframe id="iframe-detail-stat" src="" title="detail statistique" style="width: 100%; height: 100%"></iframe>

            </div>
        </div>
    </div>

<script>
    const rootDir = '<?= $rootDir ?>';
    const arrXAxis = <?= json_encode($xAxis) ?>;
    const series = <?= json_encode($series) ?>;
    const customColor = [<?= $indicateur == 2 ? "'#0081ff', '#23a472', '#e51c60'" : '' ?>]
    const indicateurStat = '<?= $indicateur ?>';
    const tempsStat = '<?= $temps ?>';
    const destinationStat = '<?= json_encode($destination) ?>';
    const $typeOffreStat = '<?= json_encode($type) ?>';
    const residenceStat = '<?= json_encode($residence) ?>';
    const statut = '<?= json_encode($statut) ?>';
</script>
<?php
iFrameDefaultMenuRight();