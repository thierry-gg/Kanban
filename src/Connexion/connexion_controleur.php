<?php
include_once ("connexion_vue.php");
include_once ("connexion_modele.php");

class Connexion_controleur{

    private $vue;
    private $modele;
    private $action;

    public function __construct(){
        $this->vue = new Connexion_vue();
        $this->modele = new Connexion_modele();
        $this->action = isset($_GET["action"]) ? $_GET["action"]: "default";
    }

    public function exec(){
        switch ($this->action){

            case "formInscription":
                $this->vue->form_inscription();
                break;

            case "inscription":
                $this->modele->inscription();
                break;

            case "formConnexion":
                $this->vue->form_connexion();
                break;

            case "connexion":
                if($this->modele->connexion() === false){
                    echo "<br>Erreur lors de la connexion<br>";
                    $this->vue->form_connexion();
                }
                $this->modele->connexion();
                break;

            default:
                $this->vue->accueil();
                break;
        }
    }
}