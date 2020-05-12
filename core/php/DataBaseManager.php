<?php

/**
 * Created by PhpStorm.
 * User: Andre
 * Date: 07/02/2016
 * Time: 07:55 PM
 */
define('SERVER', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DB', 'memorama');

class DataBaseManager {

    private $mysqli;
    private static $_instance = null;

    /**
     * DataBaseManager constructor.
     * @param $mysqli
     */
    public function __construct(mysqli $mysqli = null) {
        if(is_null($mysqli)){
            $this->mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DB);
        }else{
            $this->mysqli = $mysqli;
        }

        if ($this->mysqli->connect_errno) {
            echo "Fallo al conectar a MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
        }

        if (!$this->mysqli->set_charset('utf8')) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", $this->mysqli->error);
            exit;
        }
    }


    public function __destruct() {
        self::$_instance = null;
        $this->mysqli = null;
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            $mysqli = new mysqli;
            self::$_instance = new DataBaseManager($mysqli);
        }
        return self::$_instance;
    }

    final public function __clone() {
        throw new Exception('Only one instance is allowed');
    }

    /*1*/ 
    public function insertQuery($query) { 
        return $this->mysqli->query($query);
    }
    /*2*/ 

    public function realizeQuery($query) {
        if ($result = $this->mysqli->query($query)) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            return $result;
        } else {
            return false;
        }
    }
    /*3*/ 

    public function close() {
        return $this->mysqli->close();
    }

}
