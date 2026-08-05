<?php
class Connexion{
    static protected $bdd;

    // connexion a ma base de donnée, XAMPP phpmyadmin
    public static function initConnexion(){
        $dsn = "mysql:host=localhost;dbname=kanban_schema;charset=utf8";
        $user = "root";
        $password = "";

        self::$bdd = new PDO($dsn, $user, $password);
    }
}