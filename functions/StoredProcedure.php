<?php


class StoredProcedure
{
    private PDO $conn;
    private bool $debug;
    private array $spCalled = [];

    public function __construct(PDO $pdoConnexion, $debug = false)
    {
        $this->conn = $pdoConnexion;
        $this->debug = $debug;
        $this->spCalled = [];
    }

    /**
     * @param $sp
     * @param array $param
     *      Tableau des parametres. Exemple : [['value' => 1, 'type' => 'INT'], ['value' => 'Test', 'type' => 'STR']]
     *      Possibilité de ne pas mettre des details (clé - valeur). Exemple : [1, 'test valeur', '2021-04-30']
     * @param bool $fetchAll
     * @param int $fetchMode
     * @return mixed
     */
    public function call($sp, array $param = [], bool $fetchAll = true, int $fetchMode = PDO::FETCH_ASSOC, $count_rowset = 1){
        try{
            $query = 'CALL '.$sp.'('.implode(', ', array_fill(0, count($param), '?')).');';
            $stmt = $this->conn->prepare($query);

            if(count($param) > 0){
                if(isset($param[0])){
                    if(is_array($param[0]) && array_key_exists('value', $param[0])){
                        for($i = 0; $i<count($param); $i++){
                            $pdoParam = PDO::PARAM_STR;
                            if($param[$i]['type'] == 'INT'){
                                $pdoParam = PDO::PARAM_INT;
                            }
                            $stmt->bindParam($i + 1, $param[$i]['value'], $pdoParam);
                        }
                    }
                    else{
                        for($i = 0; $i<count($param); $i++){
                            $stmt->bindParam($i + 1, $param[$i], PDO::PARAM_STR);
                        }
                    }
                }
            }

            if($this->debug){
                $this->spCalled[] = $this->addSPDebug($sp, $param);
            }

            $success = $stmt->execute();

            $result = [];
            if($success){
                if($fetchAll){
                    $result[] = $stmt->fetchAll($fetchMode);

                    if($count_rowset > 1){
                        for($i = 1; $i < $count_rowset; $i++){
                            $stmt->nextRowset();
                            $result[] = $stmt->fetchAll($fetchMode);
                        }
                    }
                    else{
                        return  $result[0];
                    }

                    return  $result;
                }
                else{
                    return  $stmt->fetch($fetchMode);
                }
            }
        }
        catch (PDOException $exception){
            echo $exception->getMessage();
        }
    }


    /**
     * @param $sp
     * @param $param
     * @return string
     */
    private function addSPDebug($sp, $param): string
    {
        $arrParam = [];
        if(count($param) > 0){
            if(isset($param[0])){
                if(is_array($param[0]) && array_key_exists('value', $param[0])){
                    for($i = 0; $i<count($param); $i++){
                        $isNumeric = false;
                        if(isset($param[$i]['type']) && $param[$i]['type'] == 'INT'){
                            $isNumeric = true;
                        }

                        if($param[$i]['value'] !== null){
                            $arrParam[] = $isNumeric ? $param[$i]['value'] : "'".$param[$i]['value']."'";
                        }
                        else{
                            $arrParam[] = 'NULL';
                        }

                    }
                }
                else{
                    for($i = 0; $i<count($param); $i++){
                        if($param[$i] !== null){
                            $arrParam[] = is_numeric($param[$i]) ? $param[$i] : "'".$param[$i]."'";
                        }
                        else{
                            $arrParam[] = 'NULL';
                        }
                    }
                }
            }
        }

        return "CALL $sp(". implode(', ', $arrParam) . ")";
    }

    public function showAllSPCalled(){
        ?>
        <div class="pattern-shadow" style="width: 100%; min-height: 200px; padding: 14px; background: whitesmoke">
            <h6>Debug :</h6>
            <?php var_dump($this->spCalled); ?>
        </div>
        <?php
    }
}