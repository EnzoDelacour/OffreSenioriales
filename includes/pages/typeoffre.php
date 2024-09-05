<?php
iFrameDefaultMenuLeft();
$onglet = 'liste';
if(isset($_GET['o'])){
    $onglet = $_GET['o'];
}
?>
    <div class="box-side-middle pattern-shadow" style="background: white; padding: 5px 14px; <?= $onglet == 'liste' ? 'margin: 0 60px;' : '' ?>; grid-column-start: <?= $onglet == 'liste' ? '1' : 'auto' ?>; grid-column-end: <?= $onglet == 'liste' ? '4' : 'auto' ?>">
        <?php

        if($onglet == 'create' || $onglet == 'edit'){
            include 'includes/pages/typeoffre/create.php';
        }
        elseif ($onglet == 'liste'){
            include 'includes/pages/typeoffre/liste.php';
        }
        ?>
    </div>
<?php
iFrameDefaultMenuRight();