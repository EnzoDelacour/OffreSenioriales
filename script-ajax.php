<?php
include 'includes/init.php';

if(isset($_POST['action'])){

    $values = (array) json_decode(stripslashes($_POST['values']),JSON_HEX_APOS);

    if($_POST['action'] == 'recherche_prospect'){
        $values['limit'] = 300;
        $result = $storedProcedure->call('recherche_prospect', [['value'=>$util_id, 'type'=>'INT'],['value'=>$values['text_field'], 'type'=>'TEXT'],['value'=>$values['limit'], 'type'=>'INT']], true, PDO::FETCH_ASSOC, 2);
        $liste_prospect = $result[0];
        $total = $result[1];
        echo json_encode(['result' => $liste_prospect, 'total' => $total[0]['total']]);
    }
    elseif ($_POST['action'] == 'recherche_partenaire'){
        $result = $storedProcedure->call('recherche_partenaire', [['value'=>$util_id, 'type'=>'INT'],['value'=>$values['text_field'], 'type'=>'TEXT'],['value'=>$values['limit'], 'type'=>'INT']], true, PDO::FETCH_ASSOC, 2);
        $liste_partenaire = $result[0];
        $total = $result[1];
        echo json_encode(['result' => $liste_partenaire, 'total' => $total[0]['total']]);
    }
    elseif ($_POST['action'] == 'recherche_lot'){
        $values['limit'] = 400;
        $result = $storedProcedure->call('recherche_lot', [['value'=>$util_id, 'type'=>'INT'],['value'=>$values['text_field'], 'type'=>'TEXT'],['value'=>$values['limit'], 'type'=>'INT']], true, PDO::FETCH_ASSOC, 2);
        $liste_prospect = $result[0];
        $total = $result[1];
        echo json_encode(['result' => $liste_prospect, 'total' => $total[0]['total']]);
    }
    elseif ($_POST['action'] == 'create_champs'){
        $result = $storedProcedure->call('create_champ', [
            ['value'=>$util_id, 'type'=>'INT'],
            ['value'=>$values['type_id'], 'type'=>'INT'],
            ['value'=>$values['libelle'], 'type'=>'TEXT'],
            ['value'=>$values['description'], 'type'=>'TEXT'],
            ['value'=>$values['bdd'], 'type'=>'TEXT'],
            ['value'=>$values['input_name'], 'type'=>'TEXT'],
            ['value'=>$values['page'], 'type'=>'INT'],
            ['value'=>$values['posX'], 'type'=>'TEXT'],
            ['value'=>$values['posY'], 'type'=>'TEXT'],
        ], true, PDO::FETCH_ASSOC, 2);
    }
    elseif ($_POST['action'] == 'reset_champs'){
        $result = $storedProcedure->call('reset_champs_typeoffre', [
            ['value'=>$util_id, 'type'=>'INT'],
            ['value'=>$values['type_id'], 'type'=>'INT'],
        ], false, PDO::FETCH_ASSOC, 1);
    }
    elseif ($_POST['action'] == 'create_document'){
        $result = $storedProcedure->call('create_document', [
            ['value'=>$util_id, 'type'=>'INT'],
            ['value'=>$values['offre_id'], 'type'=>'INT'],
            ['value'=>$values['libelle'], 'type'=>'TEXT'],
            ['value'=>$values['description'], 'type'=>'TEXT'],
            ['value'=>$values['size'], 'type'=>'TEXT'],
            ['value'=>$values['code'], 'type'=>'TEXT'],
        ], false, PDO::FETCH_ASSOC, 1);

        echo json_encode($result);
    }
    elseif ($_POST['action'] == 'generate_offre'){
        if(strlen($_FILES['data']['name']) > 0){
            $target_file = 'documents/' . $_POST['path'];
            if (move_uploaded_file($_FILES["data"]["tmp_name"], $target_file)) {
                echo json_encode(["result"=>"The file ". basename( $_FILES["uploadFile"]["name"]). " has been uploaded."]);
            } else {
                echo json_encode(["result"=>"Sorry, there was an error uploading your file."]);
            }
        }
    }
    elseif($_POST['action'] == 'check_prospect'){
        $result = $storedProcedure->call('check_prospect_by_code', [['value'=>$util_id, 'type'=>'INT'],['value'=>$values['prospect_id'], 'type'=>'TEXT']], false);

        echo json_encode($result);
    }
    elseif($_POST['action'] == 'check_lot'){
        $result = $storedProcedure->call('check_lot_by_code', [['value'=>$util_id, 'type'=>'INT'],['value'=>$values['lot_id'], 'type'=>'TEXT']], false);

        echo json_encode($result);
    }
}