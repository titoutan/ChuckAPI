<?php

class ConnexionDB {

    private static $_instance = null;
    private PDO $linkpdo;

    private function __construct() {
        try {
            $this->linkpdo = new PDO("mysql:host=127.0.0.1;dbname=r401_api;port=3308", "default",'password');
            }
        catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if(is_null(self::$_instance)) {
            self::$_instance = new ConnexionDB();
        }
        return self::$_instance;
    }

    public function getPDO() : PDO {
        return $this->linkpdo;
    }
}