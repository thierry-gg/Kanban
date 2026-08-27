<?php
include_once "connexion.php";

class Projet_modele extends Connexion {

    public function __construct() {}

    // Quand l'utilisateur créé un projet, on va récuperer son titre, sa description, sa deadline
    // et on va l'implémenter dans la base de données
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

    // Quand le projet est créé, l'utilisateur responsable du projet est automatiquement admin dans la base de donnée
    public function autoAttributionUtilisateur($idprojet){
        $requete = self::$bdd->prepare('INSERT INTO est_responsable_de (id_projet, id_utilisateur, role) VALUES (?, ?, ?)');
        $requete->execute([$idprojet, $_SESSION['id_utilisateur'], 'admin']);
    }

    // A la création du projet, les 3 colonne principales sont créé
    public function autoAttributionColonne($idprojet){
        $requete = self::$bdd->prepare('
        INSERT INTO colonne (libelle, id_projet) VALUES (?, ?)');
        $requete->execute(["A faire", $idprojet]);
        $requete->execute(["En cours", $idprojet]);
        $requete->execute(["Terminé", $idprojet]);
    }

    // Quand l'utilisateur édite un projet et valide, cette fonction modifie les différents changement apporter
    public function editerProjet($idprojet, $titre_projet, $description_projet){
        $requete = self::$bdd->prepare("UPDATE projet SET nom_projet = ?, description_projet = ? WHERE id_projet=?");
        $requete->execute([$titre_projet, $description_projet, $idprojet]);
    }

    // Quand l'utilisateur veux terminé un projet, une requete sql fait le compte de toute les cartes
    // et dans le controleur, refuse la cloturation, si il y a au moins une carte non terminé
    public function toutesCartesTerminees($idProjet){
        $requete = self::$bdd->prepare("SELECT COUNT(*) as nb FROM carte WHERE id_projet = ? AND fini_le IS NULL");
        $requete->execute([$idProjet]);
        $resultat = $requete->fetch();
        return $resultat['nb'] == 0;
    }

    // Quand toute les conditions de cloturation d'un projet est validé, une requête met a jour le tuple fini_le dans la base de donnée
    public function terminerProjet($idProjet){
        if (!$this->toutesCartesTerminees($idProjet)) {
            return false;
        }
        $date = date('Y-m-d H:i:s');
        $requete = self::$bdd->prepare("UPDATE projet SET fini_le = ? WHERE id_projet = ? AND fini_le IS NULL");
        $requete->execute([$date, $idProjet]);
        return true;
    }

    // Permet d'avoir tous les utilisateurs d'un projet
    public function getUtilisateursProjet($idProjet){
        $requete = self::$bdd->prepare("
        SELECT u.id_utilisateur, u.nom FROM utilisateur u 
        INNER JOIN est_responsable_de erd ON u.id_utilisateur = erd.id_utilisateur 
        WHERE erd.id_projet = ?");
        $requete->execute([$idProjet]);
        return $requete->fetchAll();
    }

    // Permet d'avoir tous les projets d'un utilisateur
    public function getProjets($id_utilisateur){
        $requete = self::$bdd->prepare("
            SELECT p.* FROM projet p INNER JOIN est_responsable_de est ON p.id_projet = est.id_projet 
            WHERE est.id_utilisateur = ?");
        $requete->execute([$id_utilisateur]);

        return $requete->fetchAll();
    }

    // Permet d'avoir tous les informations d'un projet
    public function getProjet($id_projet){
        $requete = self::$bdd->prepare("SELECT * FROM projet WHERE id_projet = ?");
        $requete->execute([$id_projet]);
        return $requete->fetch();
    }

    // Permet de supprimer un projet, en le supprimant des différents tableau associer
    public function supprimerProjet($id_projet){
        $requete = self::$bdd->prepare(
            'DELETE FROM est_responsable_de WHERE id_projet = ?'
        );
        $requete->execute([$id_projet]);

        $requete = self::$bdd->prepare(
            'DELETE FROM colonne WHERE id_projet = ?'
        );
        $requete->execute([$id_projet]);

        $requete = self::$bdd->prepare(
            'DELETE FROM projet WHERE id_projet = ?'
        );
        $requete->execute([$id_projet]);
    }
/*
    public function getIdProjet($id_utilisateur){
        $requete = self::$bdd->prepare("SELECT id_projet FROM est_responsable_de WHERE id_utilisateur = ?");
        $requete->execute([$id_utilisateur]);
        return $requete->fetch();
    }
*/
}