<?php
include_once "carte_modele.php";
include_once "carte_vue.php";
include_once ("Utilisateur/utilisateur_modele.php");

class Carte_controleur {

    private $vue;
    private $modele;
    private $utilisateurModele;
    private $action;

    public function __construct() {
        $this->vue = new Carte_vue();
        $this->modele = new Carte_modele();
        $this->utilisateurModele = new Utilisateur_modele();
        $this->action = isset($_GET['action']) ? $_GET['action'] : "default";
    }
    private function peutEditerCarte($idCarte){
        $idProjet = $_SESSION['id_projet'];
        $role = $this->utilisateurModele->getRole($idProjet, $_SESSION['id_utilisateur']);
        $responsable = $this->modele->getResponsableCarte($idCarte);

        if ($role === 'admin') return true;
        if ($role === 'editeur' && $responsable == $_SESSION['id_utilisateur']) return true;
        return false;
    }

    public function exec() {
        switch ($this->action) {

            case "formCarte":
                $idProjet = $_SESSION['id_projet'];
                $role = $this->utilisateurModele->getRole($idProjet, $_SESSION['id_utilisateur']);
                if ($role === 'modeLecture') {
                    echo "<script>alert('Vous êtes en mode lecture seule.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }
                $this->vue->form_carte($idProjet);
                break;

            case "carte":
                $idProjet = $_SESSION['id_projet'];
                $role = $this->utilisateurModele->getRole($idProjet, $_SESSION['id_utilisateur']);
                if ($role === 'modeLecture') { exit; } // sécurité si quelqu'un force l'appel
                $this->modele->creerCarte($idProjet);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$idProjet);
                break;

            case "editerCarte":
                $idCarte = $_POST['id_carte'];
                $idProjet = $_SESSION['id_projet'];

                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Vous n\\'avez pas les droits pour modifier cette carte.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }

                $nvTitreCarte = $_POST['titre_carte'];
                $nvDescriptionCarte = $_POST['description_carte'];
                $nvDeadLine = $_POST['deadline_carte'];
                $this->modele->editerCarte($idCarte, $nvTitreCarte, $nvDescriptionCarte, $nvDeadLine);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$idProjet);
                break;

            case "changerColonne":
                $idCarte = $_POST['id_carte'];
                $idProjet = $_SESSION['id_projet'];

                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Vous n\\'avez pas les droits pour déplacer cette carte.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }

                $direction = $_POST['direction'];
                $resultat = $this->modele->changerColonne($idCarte, $direction);

                if ($resultat === "dependance_non_terminee") {
                    echo "<script>alert('Impossible de clôturer cette carte : une dépendance n\\'est pas encore terminée.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }
                if ($resultat === false) {
                    echo "<script>alert('Impossible de déplacer cette carte !');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }

                if ($resultat === "Terminé") {
                    $this->modele->etatCarteTerminer($idCarte);
                } else {
                    $this->modele->etatCartePasTerminer($idCarte);
                }
                header("Location: index.php?module=projet&action=afficherProjet&id=".$idProjet);
                break;

            case "formUtilisateurCarte":
                $idCarte = $_POST['id_carte'];
                $idProjet = $_SESSION['id_projet'];
                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Accès refusé.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }
                $membreActuel = $this->modele->getAllUtilisateurCarte($idProjet);
                $this->vue->formUtilisateurCarte($idProjet, $idCarte, $membreActuel);
                break;

            case "assignerUtilisateurCarte":
                $idCarte = $_POST['id_carte'];
                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Accès refusé.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']."';</script>";
                    exit;
                }
                $idUtilisateur = $_POST['id_utilisateur'];
                $this->modele->assignerUtilisateurCarte($idCarte, $idUtilisateur);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            case "formDependanceCarte":
                $idCarte = $_POST['id_carte'];
                $idProjet = $_SESSION['id_projet'];
                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Accès refusé.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }
                $autresCartes = $this->modele->getAllCartes($idCarte, $idProjet);
                $dependances = $this->modele->getDependancesCarte($idCarte);
                $this->vue->formDependanceCarte($idProjet, $idCarte, $autresCartes, $dependances);
                break;

            case "ajouterDependanceCarte":
                $idCarte = $_POST['id_carte'];
                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Accès refusé.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']."';</script>";
                    exit;
                }
                $idCarteDependante = $_POST['id_carte_dependante'];
                $this->modele->ajouterDependance($idCarte, $idCarteDependante);
                header("Location: index.php?module=projet&action=afficherProjet&id=".$_SESSION['id_projet']);
                break;

            case "supprimerDependanceCarte":
                $idCarte = $_POST['id_carte'];
                $idProjet = $_SESSION['id_projet'];
                if (!$this->peutEditerCarte($idCarte)) {
                    echo "<script>alert('Vous n\\'avez pas les droits pour cette action.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }
                break;

            case "supprimerCarte":
                $idCarte = $_POST['id_carte'];
                $idProjet = $_SESSION['id_projet'];
                $role = $this->utilisateurModele->getRole($idProjet, $_SESSION['id_utilisateur']);

                if ($role !== 'admin') {
                    echo "<script>alert('Seul l\\'administrateur peut supprimer une carte.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }

                $succes = $this->modele->supprimerCarte($idCarte);
                if (!$succes) {
                    echo "<script>alert('Impossible de supprimer cette carte : d\\'autres cartes en dépendent.');window.location.href='index.php?module=projet&action=afficherProjet&id=".$idProjet."';</script>";
                    exit;
                }
                header("Location: index.php?module=projet&action=afficherProjet&id=".$idProjet);
                break;

            default:
                break;
        }
    }
}