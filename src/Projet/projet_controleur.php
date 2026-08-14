<?php
include_once ("projet_vue.php");
include_once ("projet_modele.php");
include_once ("Carte/carte_modele.php");
include_once ("Utilisateur/utilisateur_modele.php");

class Projet_controleur{

    private $vue;
    private $modele;
    private $carteModele;
    private $utilisateurModele;
    private $action;

    public function __construct(){
        $this->vue = new Projet_vue();
        $this->modele = new Projet_modele();
        $this->carteModele = new Carte_modele();
        $this->utilisateurModele = new Utilisateur_modele();
        $this->action = isset($_GET["action"]) ? $_GET["action"]: "default";
    }

    public function exec(){
        switch($this->action){

            case "default":
                $projets = $this->modele->getProjets($_SESSION["id_utilisateur"]);
                $utilisateursParProjet = [];
                $rolesParProjet = [];
                foreach ($projets as $projet) {
                    $utilisateursParProjet[$projet['id_projet']] = $this->modele->getUtilisateursProjet($projet['id_projet']);
                    $rolesParProjet[$projet['id_projet']] = $this->utilisateurModele->getRole($projet['id_projet'], $_SESSION['id_utilisateur']);
                }
                $this->vue->accueilProjet($projets, $utilisateursParProjet, $rolesParProjet);
                break;

            case "afficherProjet":
                $idprojet = $_GET['id'];
                $_SESSION['id_projet'] = $idprojet;
                $projet = $this->modele->getProjet($idprojet);
                $cartes = $this->carteModele->getCartes($idprojet);
                $role = $this->utilisateurModele->getRole($idprojet, $_SESSION['id_utilisateur']);
                $this->vue->afficherProjet($projet, $cartes, $role, $_SESSION['id_utilisateur']);
                break;

            case "editerProjet":
                $role = $this->utilisateurModele->getRole($_POST['id_projet'], $_SESSION['id_utilisateur']);
                if ($role !== 'admin') {
                    echo "<script>alert('Seul l\\'administrateur peut modifier le projet.');window.location.href='index.php';</script>";
                    exit;
                }
                $nvTitreProjet = $_POST["titreProjet"];
                $this->modele->editerProjet($_POST['id_projet'], $nvTitreProjet);
                header('Location: index.php');
                break;

            case "terminerProjet":
                $idProjet = $_POST['id_projet'];
                $role = $this->utilisateurModele->getRole($idProjet, $_SESSION['id_utilisateur']);
                if ($role !== 'admin') {
                    echo "<script>alert('Seul l\\'administrateur peut terminer le projet.');window.location.href='index.php';</script>";
                    exit;
                }
                $terminer = $this->modele->terminerProjet($idProjet);
                if(!$terminer){
                    echo "<script>
                        alert('Impossible de terminer ce projet : toutes les cartes ne sont pas encore terminées.');
                        window.location.href='index.php';
                        </script>";
                    exit;
                }
                header("Location: index.php");
                break;

            case "formProjet":
                $this->vue->formProjet();
                break;

            case "projet":
                $idprojet = $this->modele->ajoutProjet();
                $this->modele->autoAttributionUtilisateur($idprojet);
                $this->modele->autoAttributionColonne($idprojet);
                header("Location: index.php");
                break;
        }
    }
}