<?php
include("utilisateur_modele.php");
include("utilisateur_vue.php");


class Utilisateur_controleur {

    private $vue;
    private $modele;
    private $action;

    public function __construct(){
        $this->vue = new Utilisateur_vue();
        $this->modele = new Utilisateur_modele();
        $this->action = isset($_GET["action"]) ? $_GET["action"]: "default";
    }

    public function exec(){
        switch($this->action) {
            case "formProfile":
                $this->vue->formProfile();
                break;

            case "profile":
                $nvNom = $_POST['nom'];
                $this->modele->editProfile($_SESSION['id_utilisateur'], $nvNom);
                $_SESSION['nom'] = $nvNom;
                header('Location: index.php');
                break;

            case "supprimerProfile":
                $this->modele->supprimerProfile($_SESSION['id_utilisateur']);
                if (session_destroy()) {
                    header('Location: index.php');
                }
                break;

            case "formAjoutUtilisateur":
                $idProjet = $_POST['id_projet'];
                $this->vue->formAjouterUtilisateurProjet($idProjet);
                break;

            case "ajouterUtilisateurProjet":
                $nom = $_POST['nom'];
                $idProjet = $_POST['id_projet'];
                $ajouterUtilisateur = $this->modele->ajouterUtilisateurProjet($nom, $idProjet);

                if ($ajouterUtilisateur === false) {
                    echo '<br>Aucun utilisateur trouvé<br>';
                    $this->vue->formAjouterUtilisateurProjet($idProjet);
                } else {
                    header("Location: index.php?");
                }
                break;

            case "formGestionDroit":
                $idProjet = $_SESSION['id_projet'];
                $utilisateurs = $this->modele->getUtilisateursAvecRole($idProjet);
                $this->vue->formGestionDroit($utilisateurs, $idProjet);
                break;

            case "modifierRoles":
                $idProjet = $_POST['id_projet'];
                $this->modele->modifierRoles($idProjet);
                header("Location: index.php?module=projet&action=afficherProjet&id='.$idProjet.'");
                break;
        }
    }

}