<?php
include_once "carte_modele.php";
include_once "carte_vue.php";

class Carte_controleur {

    private $vue;
    private $modele;
    private $action;

    public function __construct() {
        $this->vue = new Carte_vue();
        $this->modele = new Carte_modele();
        $this->action = isset($_GET['action']) ? $_GET['action'] : "default";
    }

    public function exec() {

        switch ($this->action) {

            case "formCarte":
//                var_dump($_GET);
//                var_dump($_POST);
//                var_dump($_SESSION);
                $idProjet = $_SESSION['id_projet'];
                $this->vue->form_carte($idProjet);
                break;

            case "carte":
                $this->modele->creerCarte($_SESSION['id_projet']);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            case "editerCarte":
                $idProjet = $_SESSION['id_projet'];
                $nvTitreCarte = $_POST['titre_carte'];
                $nvDescriptionCarte = $_POST['description_carte'];
                $nvDeadLine = $_POST['deadline_carte'];
                $idCarte = $_POST['id_carte'];

                $this->modele->editerCarte($idCarte, $nvTitreCarte, $nvDescriptionCarte, $nvDeadLine);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$idProjet);
                break;

            case "formUtilisateurCarte":
                $idProjet = $_SESSION['id_projet'];
                $idCarte = $_POST['id_carte'];
                $membreActuel = $this->modele->getAllUtilisateurCarte($idProjet);
                $this->vue->formUtilisateurCarte($idProjet,$idCarte, $membreActuel);
                break;

            case "assignerUtilisateurCarte":
                $idCarte = $_POST['id_carte'];
                $idUtilisateur = $_POST['id_utilisateur'];
                $this->modele->assignerUtilisateurCarte($idCarte, $idUtilisateur);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            case "formDependanceCarte":
                $idProjet = $_SESSION['id_projet'];
                $idCarte = $_POST['id_carte'];
                $autresCartes = $this->modele->getAllCartes($idCarte, $_SESSION['id_projet']);
                $dependances = $this->modele->getDependancesCarte($idCarte);
//                var_dump($dependances);
//                exit;
                $this->vue->formDependanceCarte($idProjet,$idCarte, $autresCartes, $dependances);
                break;

            case "ajouterDependanceCarte":
                $idCarte = $_POST['id_carte'];
                $idCarteDependante = $_POST['id_carte_dependante'];
                $this->modele->ajouterDependance($idCarte, $idCarteDependante);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            case "supprimerDependanceCarte":
                $idCarte = $_POST['id_carte'];
                $idCarteDependante = $_POST['id_carte_dependante'];
                $this->modele->supprimerDependanceCarte($idCarte, $idCarteDependante);

                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            case "supprimerCarte":
                $idCarte = $_POST['id_carte'];
                $succes = $this->modele->supprimerCarte($idCarte);

                if (!$succes) {
                    echo "<script>
                        alert('Impossible de supprimer cette carte : d\\'autres cartes en dépendent.');
                        window.location.href='index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']."';
                        </script>";
                    exit;
                }
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;



            case "changerColonne":
                $idCarte = $_POST['id_carte'];
                $direction = $_POST['direction'];
                $resultat = $this->modele->changerColonne($idCarte, $direction);

                if ($resultat === "dependance_non_terminee") {
                    echo "
                    <script>
                        alert('Impossible de clôturer cette carte : une dépendance n\\'est pas encore terminée.');
                        window.location.href='index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']."';
                    </script>";
                    exit;
                }

                if ($resultat === false) {
                    echo "
                    <script>
                        alert('Impossible de déplacer cette carte !');
                        window.location.href='index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']."';
                    </script>";
                    exit;
                }

                if ($resultat === "Terminé") {
                    $this->modele->etatCarteTerminer($idCarte);
                } else {
                    $this->modele->etatCartePasTerminer($idCarte);
                }
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            default:
                break;
        }
    }
}