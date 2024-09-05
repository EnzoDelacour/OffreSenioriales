<?php
$liste_type = liste_type($pdoConnection, $util_id);


if(isset($_POST['set_actif'])){
    $type_id = $_POST['type_id'];
    var_dump($_POST['actif']);
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
    <h1>Catalogue des types d'offre</h1>
    <div class="box-btn-title">
        <button type="button" class="pattern-button btn-export-excel" id="btn-export"><i class="fas fa-file-excel"></i> Export</button>
        <a href="index.php?p=typeoffre&o=create" class="pattern-button"><i class="fas fa-plus"></i>Ajouter Type</a>
    </div>
</div>

<table>
    <tr>
        <th>Actif</th>
        <th>Template</th>
        <th>Destination</th>
        <th>Libellé</th>
        <th>Description</th>
        <th>Condition</th>
        <th>Actions</th>
    </tr>
    <?php
    foreach ($liste_type as $type){
        ?>
        <tr class="row-typeoffre-liste" data-type-id="<?= $type['typ_id'] ?>">
            <td>
                <form method="post">
                    <input type="hidden" name="set_actif" value="1">
                    <input type="hidden" name="type_id" value="<?= $type['typ_id'] ?>">
                    <input class="input-set-actif" type="checkbox" name="actif" value="1" <?=  $type['typ_actif'] == 1 ? 'checked' : '' ?> <?= !$isAdmin ? 'disabled' : '' ?>>
                </form>
            </td>
            <td>
                <?php
                if(strlen($type['tmp_filepath']) > 0){
                    ?>
                    <div class="wrapper-box-image-template-liste">
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
                    <?php
                }
                ?>
            </td>
            <td><?= $type['dst_lib'] ?></td>
            <td><?= $type['typ_lib'] ?></td>
            <td class="has-long-text-hidden" data-show="0" data-column="typ_description"><?= custom_echo($type['typ_description'], 300) ?></td>
            <td class="has-long-text-hidden" data-show="0" data-column="typ_condition"><?= custom_echo($type['typ_condition'], 300) ?></td>
            <td>
                <div style="display: flex">
                    <a href="index.php?p=typeoffre&o=edit&id=<?= $type['typ_id'] ?>" class="pattern-button-icon"><i class="fas fa-pen"></i></a>
                    <a href="index.php?p=template&o=ajouter&e=3&id=<?= $type['typ_template_id'] ?>" style="margin-left: 7px; <?= $type['typ_template_id'] > 0 ? '' : 'background: red;' ?>" class="pattern-button-icon" title="<?= $type['typ_template_id'] > 0 ? 'Éditer template' : 'Pas de templete : Cliquer pour ajouter' ?>"><i class="fas fa-file"></i></a>
                </div>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<script>
    const listeType = <?= json_encode($liste_type) ?>;
</script>