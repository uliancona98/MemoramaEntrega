<?php

/**
 * Created by IntelliJ IDEA.
 * User: jonathaneduardo
 * Date: 05/05/2016
 * Time: 07:09 PM
 */
require_once("DataBaseManager.php");

class PuntajesManajer {

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
        /*
         * Falla cuando se llama a la funcion close();
         * */
        //$this->dbManager->close();
        self::$_instance = null;
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new PuntajesManajer();
        }
        return self::$_instance;
    }

    public function setPuntaje($idUsuario,$idMateria,$fecha,$dificultad,$puntaje,$foundPeers){
        if(is_null($idUsuario)|| is_null($idMateria) || is_null($fecha)||is_null($dificultad) || is_null($puntaje)|| is_null($foundPeers)){
            return null;
        }else{
            $query = "INSERT INTO puntajes (id_usuario,id_materia,fecha,dificultad,puntaje,parejas_encontradas) VALUES('$idUsuario','$idMateria','$fecha','$dificultad',$puntaje,$foundPeers)";
            $resultado = $this->dbManager->insertQuery($query);
            return $resultado;
        }
    }

    public function deletePuntaje($idUsuario,$idMateria,$fecha,$dificultad){
        if(is_null($idUsuario)|| is_null($idMateria) || is_null($fecha)||is_null($dificultad)){
            return null;
        }else{
            $query = "DELETE FROM puntajes WHERE id_usuario = '$idUsuario' AND id_materia = '$idMateria' AND fecha='$fecha' AND '$dificultad'";
            $resultado = $this->dbManager->insertQuery($query);
            return $resultado;
        }
    }

    public function getAllPuntajeForUsuario($idUsuario) {
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario'";
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

    public function getAllPuntajeForMateria($idMateria) {
        $query = "SELECT * FROM puntajes WHERE id_materia='$idMateria'";
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

    public function getAllPuntajeForUsuarioAndMateria($idUsuario, $idMateria) {
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario' AND id_materia='$idMateria'";
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

    public function getAllPuntajeForMateriaAndDificultad($idMateria,$dificultad){
        $query = "SELECT * FROM puntajes WHERE id_materia='$idMateria' AND dificultad='$dificultad'";

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

    public function getAllPuntajeForUsuarioAndMateriaAndDificultad($idUsuario,$idMateria,$dificultad){
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario' AND id_materia='$idMateria' AND dificultad='$dificultad'";

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
}