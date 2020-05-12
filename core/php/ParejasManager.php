<?php

/**
 * Created by IntelliJ IDEA.
 * User: jonathaneduardo
 * Date: 09/04/2016
 * Time: 03:01 PM
 */
require_once("DataBaseManager.php");

class ParejasManager {

    private $dbManager;
    private static $_instance;

    public function __construct(DataBaseManager $dataBaseManager = null) {
        if(is_null($dataBaseManager)){
            $this->dbManager = DataBaseManager::getInstance();
        }else{
            $this->dbManager = $dataBaseManager;
        }    
    }

    public function __destruct() {
        $this->dbManager->close();
        self::$_instance = null;
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new ParejasManager();
        }
        return self::$_instance;
    }

    public function getPareja($idMateria, $id) {
        $query = "SELECT concepto,descripcion, FROM parejas WHERE id='$id' AND idmateria= '$idMateria'";

        $resultado = $this->dbManager->realizeQuery($query);
        if($resultado){
            if(count($resultado)==0){
                return 0;
            }
            return json_encode($resultado);
        }else{
            return false;
        }
    }

    public function setPareja($idmateria, $concepto, $descripcion) {
        if(is_null($idmateria)|| is_null($concepto) || is_null($descripcion)){
            return null;
        }else{
            $query = "INSERT INTO parejas (concepto,descripcion,idmateria) VALUES('$concepto','$descripcion','$idmateria')";
            $resultado = $this->dbManager->insertQuery($query);
            return $resultado;
        }
    }

    public function updatePareja($id, $idMatter, $concept, $definition) {
        if(is_null($id)|| is_null($idMatter) || is_null($concept)|| is_null($definition)){
            return null;
        }else{
            $query = "UPDATE parejas set idmateria = '$idMatter' , concepto = '$concept' , descripcion = '$definition' WHERE id=" . intval($id);
            $resultado = $this->dbManager->insertQuery($query);
            return $resultado;
        }
    }

    public function deletePareja($id, $idMateria) {
        if(is_null($id)|| is_null($idMateria)){
            return null;
        }else{
            $query = "DELETE FROM parejas WHERE id='$id' AND idmateria='$idMateria'";
            $resultado = $this->dbManager->insertQuery($query);
            return $resultado;
        }

    }

    public function getAllParejasTheMateria($idMateria) {
        
        $query = "SELECT concepto,descripcion FROM parejas WHERE idmateria = $idMateria";
        $resultado = $this->dbManager->realizeQuery($query);
        if($resultado){
            if(count($resultado)==0){
                return 0;
            }
            return json_encode($resultado);
        }else{
            return false;
        }
    }

    public function getAllParejas() {
        $query = "SELECT * FROM parejas";

        $resultado = $this->dbManager->realizeQuery($query);
        if($resultado){
            if(count($resultado)==0){
                return 0;
            }
            $coupleList[] = $this->setValuesToResult($resultado);
            return json_encode($coupleList);
        }else{
            return false;
        }
    }

    private function setValuesToResult($result) {
        $couple = array();
        for ($i = 0; $i < count($result); $i++) {
            $couple['id'] = $result[$i]['id'];
            $couple['idMatter'] = $result[$i]['id_materia'];
            $couple['concept'] = $result[$i]['concepto'];
            $couple['definition'] = $result[$i]['descripcion'];

            $coupleList[] = $couple;
        }

        return $coupleList;
    }

}

