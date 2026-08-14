<?php
include_once "connexion.php";

class Projet_modele extends Connexion {

    public function __construct() {}

    public function ajoutProjet(){

        $titreProjet = $_POST['titre_projet'];
        $descriptionProjet = $_POST['description_projet'];
        if(empty($_POST['date_projet'])){
            $datefinal = NULL;
        }else{
            $datefinal = $_POST['date_projet'];
        }
        $date = date('Y-m-d H:i:s');

        $requete = self::$bdd->prepare('
        INSERT INTO projet(nom_projet, cree_le, fini_le ,deadline ,description_projet) VALUES (?, ?, ?, ?, ?)');
        $requete->execute([$titreProjet, $date, NULL , $datefinal, $descriptionProjet]);
        return self::$bdd->lastInsertId();
    }

    public function autoAttributionUtilisateur($idprojet){
        $requete = self::$bdd->prepare('INSERT INTO est_responsable_de (id_projet, id_utilisateur, role) VALUES (?, ?, ?)');
        $requete->execute([$idprojet, $_SESSION['id_utilisateur'], 'admin']);
    }

    public function autoAttributionColonne($idprojet){
        $requete = self::$bdd->prepare('
        INSERT INTO colonne (libelle, id_projet) VALUES (?, ?)');
        $requete->execute(["A faire", $idprojet]);
        $requete->execute(["En cours", $idprojet]);
        $requete->execute(["Terminé", $idprojet]);
    }

    public function editerProjet($idprojet, $titre_projet){
        $requete = self::$bdd->prepare("UPDATE projet SET nom_projet=? WHERE id_projet=?");
        $requete->execute([$titre_projet,$idprojet]);
    }

    public function toutesCartesTerminees($idProjet){
        $requete = self::$bdd->prepare("SELECT COUNT(*) as nb FROM carte WHERE id_projet = ? AND fini_le IS NULL");
        $requete->execute([$idProjet]);
        $resultat = $requete->fetch();
        return $resultat['nb'] == 0;
    }

    public function terminerProjet($idProjet){
        if (!$this->toutesCartesTerminees($idProjet)) {
            return false;
        }
        $date = date('Y-m-d H:i:s');
        $requete = self::$bdd->prepare("UPDATE projet SET fini_le = ? WHERE id_projet = ? AND fini_le IS NULL");
        $requete->execute([$date, $idProjet]);
        return true;
    }

    public function getUtilisateursProjet($idProjet){
        $requete = self::$bdd->prepare("
        SELECT u.id_utilisateur, u.nom FROM utilisateur u 
        INNER JOIN est_responsable_de erd ON u.id_utilisateur = erd.id_utilisateur 
        WHERE erd.id_projet = ?");
        $requete->execute([$idProjet]);
        return $requete->fetchAll();
    }

    public function getProjets($id_utilisateur){
        $requete = self::$bdd->prepare("
            SELECT p.* FROM projet p INNER JOIN est_responsable_de est ON p.id_projet = est.id_projet 
            WHERE est.id_utilisateur = ?");
        $requete->execute([$id_utilisateur]);

        return $requete->fetchAll();
    }

    public function getIdProjet($id_utilisateur){
        $requete = self::$bdd->prepare("SELECT id_projet FROM est_responsable_de WHERE id_utilisateur = ?");
        $requete->execute([$id_utilisateur]);
        return $requete->fetch();
    }

    public function getProjet($id_projet){
        $requete = self::$bdd->prepare("SELECT * FROM projet WHERE id_projet = ?");
        $requete->execute([$id_projet]);
        return $requete->fetch();
    }

}