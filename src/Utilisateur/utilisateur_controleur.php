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

            // Est appelé par formProfile dans utilisateur_vue
            case "formProfile":
                $this->vue->formProfile();
                break;

            // Est appelé par profile dans utilisateur_modele
            case "profile":
                $nvNom = $_POST['nom'];
                $this->modele->editProfile($_SESSION['id_utilisateur'], $nvNom);
                $_SESSION['nom'] = $nvNom;
                header('Location: index.php');
                break;

            // Est appelé par supprimerProfile dans utilisateur_modele
            case "supprimerProfile":
                $this->modele->supprimerProfile($_SESSION['id_utilisateur']);
                if (session_destroy()) {
                    header('Location: index.php');
                }
                break;

            // Est appelé par formAjoutUtilisateur dans utilisateur_vue
            case "formAjoutUtilisateur":
                $idProjet = $_POST['id_projet'];
                $this->vue->formAjouterUtilisateurProjet($idProjet);
                break;

            // Est appelé par ajouterUtilisateurProjet dans utilisateur_modele
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

            // Est appelé par formGestionDroit dans utilisateur_vue
            case "formGestionDroit":
                $idProjet = $_GET['id_projet'];
                $utilisateurs = $this->modele->getUtilisateursAvecRole($idProjet);
                $this->vue->formGestionDroit($utilisateurs, $idProjet);
                break;

            // Est appelé par modifierRoles dans utilisateur_modele
            case "modifierRoles":
                $idProjet = $_POST['id_projet'];
                $modification = $this->modele->modifierRoles($idProjet);
                if ($modification === false) {
                    echo "Impossible, au moins une personne doit être administrateur.";
                    $utilisateurs = $this->modele->getUtilisateursAvecRole($idProjet);
                    $this->vue->formGestionDroit($utilisateurs, $idProjet);
                    break;
                }
                header("Location: index.php?module=projet&action=afficherProjet&id=".$idProjet);
                exit;
        }
    }

}