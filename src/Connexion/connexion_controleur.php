<?php
include_once ("connexion_vue.php");
include_once ("connexion_modele.php");
include_once ("ldap.php");

class Connexion_controleur{

    private $vue;
    private $modele;
    private $action;
//    private $ldap;

    public function __construct(){
        $this->vue = new Connexion_vue();
        $this->modele = new Connexion_modele();
        $this->action = isset($_GET["action"]) ? $_GET["action"]: "default";
//        $this->ldap = new LDAP();
    }

    public function exec(){
        switch ($this->action){

            case "formInscription":
                $this->vue->form_inscription();
                break;

            case "inscription":
                $mdp = $_POST['mdp'];
                $mdpverif = $_POST['mdpverif'];

                $erreurCaractere = "Le mot de passe doit contenir au moins 12 caractères.";
                $erreurMaj = "Le mot de passe doit contenir une majuscule.";
                $erreurChiffre = "Le mot de passe doit contenir un chiffre.";
                $erreurCaractereSpecial = "Le mot de passe doit contenir un caractère spécial.";
                $erreurMdpVerif = "Les mots de passe ne correspondent pas.";

                if (strlen($mdp) < 12) {
                    $this->vue->form_inscription($erreurCaractere);
                } elseif (!preg_match('/[A-Z]/', $mdp)) {
                    $this->vue->form_inscription($erreurMaj);
                } elseif (!preg_match('/[0-9]/', $mdp)) {
                    $this->vue->form_inscription($erreurChiffre);
                } elseif (!preg_match('/[^a-zA-Z0-9]/', $mdp)) {
                    $this->vue->form_inscription($erreurCaractereSpecial);
                } elseif ($mdp !== $mdpverif) {
                    $this->vue->form_inscription($erreurMdpVerif);
                } else {
                    $this->modele->inscription();
                }
                break;

            case "formConnexion":
                $this->vue->form_connexion();
                break;

            case "connexion":
                $resultat = $this->modele->connexion();
                if($resultat === false){
                    $this->vue->form_connexion(true);
                }
                break;
/*
            case "connexionLDAP":
                $identifiant = $_POST["nom"];
                $motDePasse = $_POST["mdp"];
                if ($this->ldap->connexion_ldap($identifiant, $motDePasse)) {
                    $_SESSION['nom'] = $identifiant;
                } else {
                    $this->vue->form_connexion(true);
                }
*/
            default:
                $this->vue->accueil();
                break;
        }
    }
}