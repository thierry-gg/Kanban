<?php
include_once "connexion.php";
class Carte_modele extends Connexion {


    public function creerCarte($idProjet) {
        $titre = $_POST['titre_carte'];
        $description = $_POST['description_carte'];
        if(empty($_POST['date_projet'])){
            $datefinal = NULL;
        }else{
            $datefinal = $_POST['date_projet'];
        }
        $date = date('Y-m-d H:i:s');

        $requeteColonne = self::$bdd->prepare(
            "SELECT id_colonne FROM colonne WHERE id_projet = ? AND libelle = 'A faire'");
        $requeteColonne->execute([$idProjet]);
        $idColonne = $requeteColonne->fetch();

        $requete = self::$bdd->prepare("
        INSERT INTO 
        carte (titre_carte, cree_le, fini_le, deadline, description_carte, id_colonne, id_utilisateur, id_projet) 
        VALUES (?, ?, ?, ? ,?, ?, ?, ?)");
        $requete->execute([$titre, $date , NULL , $datefinal, $description, $idColonne['id_colonne'], NULL, $idProjet]);
        return self::$bdd->lastInsertId();
    }

    public function etatCarteTerminer($idCarte){
        $date = date('Y-m-d H:i:s');

        $requete = self::$bdd->prepare("UPDATE carte SET fini_le = ? WHERE id_carte = ?");
        $requete->execute([$date, $idCarte]);
    }

    public function etatCartePasTerminer($idCarte){
        $requete = self::$bdd->prepare("UPDATE carte SET fini_le = NULL WHERE id_carte = ?");

        $requete->execute([$idCarte]);
    }


    public function editerCarte($idCarte, $titre, $description, $dateLimite){
        if ($dateLimite == "") {
            $dateLimite = NULL;
        }
        $requete = self::$bdd->prepare("
        UPDATE carte c INNER JOIN projet p ON c.id_projet = p.id_projet
        SET c.titre_carte = ?, c.description_carte = ?, c.deadline = ?
        WHERE c.id_carte = ? AND p.fini_le IS NULL");
        $requete->execute([$titre, $description, $dateLimite, $idCarte]);
    }

    public function getCartes($idProjet){
        $requete = self::$bdd->prepare("SELECT c.*, col.libelle, u.nom AS nom FROM carte c
        INNER JOIN colonne col ON c.id_colonne = col.id_colonne
        LEFT JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur WHERE c.id_projet = ?
        ORDER BY c.id_colonne, c.id_carte");
        $requete->execute([$idProjet]);
        return $requete->fetchAll();
    }

    public function getUtilisateurCarte($idCarte){
        $requete = self::$bdd->prepare("
        SELECT u.id_utilisateur, u.nom 
        FROM utilisateur u
        INNER JOIN carte c ON u.id_utilisateur = c.id_utilisateur
        WHERE c.id_carte = ?");
        $requete->execute([$idCarte]);
        return $requete->fetch();
    }

    public function assignerUtilisateurCarte($idCarte, $idUtilisateur){
        $requete = self::$bdd->prepare("UPDATE carte SET id_utilisateur = ? WHERE id_carte = ?");
        $requete->execute([$idUtilisateur, $idCarte]);
    }

    public function getAllUtilisateurCarte($idProjet){
        $requete = self::$bdd->prepare("
        SELECT u.id_utilisateur, u.nom FROM utilisateur u
        INNER JOIN est_responsable_de erd ON u.id_utilisateur = erd.id_utilisateur
        WHERE erd.id_projet = ?");

        $requete->execute([$idProjet]);

        return $requete->fetchAll();
    }

    public function getAllCartes($idCarte, $idProjet){
        $requete = self::$bdd->prepare("SELECT id_carte, titre_carte FROM carte WHERE id_projet = ? AND id_carte != ?");
        $requete->execute([$idProjet, $idCarte]);
        return $requete->fetchAll();
    }

    public function ajouterDependance($idCarte, $idCarteDependante){
        $requeteVerif = self::$bdd->prepare("SELECT * FROM depend_de WHERE id_carte = ? AND id_carte_dependante = ?");
        $requeteVerif->execute([$idCarte, $idCarteDependante]);
        if ($requeteVerif->fetch() !== false) return false;

        $requete = self::$bdd->prepare("INSERT INTO depend_de (id_carte, id_carte_dependante) VALUES (?, ?)");
        $requete->execute([$idCarte, $idCarteDependante]);
        return true;
    }

    public function verifDependance($idCarte){

        $requete = self::$bdd->prepare("SELECT col_dep.libelle AS colonne_dependante FROM depend_de d
        INNER JOIN carte c_dep ON d.id_carte_dependante = c_dep.id_carte
        INNER JOIN colonne col_dep ON c_dep.id_colonne = col_dep.id_colonne
        WHERE d.id_carte = ?");
        $requete->execute([$idCarte]);

        foreach ($requete->fetchAll() as $dep) {
            if ($dep['colonne_dependante'] !== "Terminé") {
                return false;
            }
        }
        return true;
    }
    public function cartesQuiDependentDe($idCarte){
        $requete = self::$bdd->prepare("SELECT c.id_carte, c.titre_carte FROM depend_de d
        INNER JOIN carte c ON d.id_carte = c.id_carte WHERE d.id_carte_dependante = ?");
        $requete->execute([$idCarte]);
        return $requete->fetchAll();
    }

    public function getDependancesCarte($idCarte){
        $requete = self::$bdd->prepare("SELECT c.id_carte, c.titre_carte FROM depend_de d
        INNER JOIN carte c ON d.id_carte_dependante = c.id_carte WHERE d.id_carte = ?");
        $requete->execute([$idCarte]);
        return $requete->fetchAll();
    }

    public function supprimerCarte($idCarte){
        $dependants = $this->cartesQuiDependentDe($idCarte);
        if (count($dependants) > 0) {
            return false;
        }

        self::$bdd->prepare("DELETE FROM depend_de WHERE id_carte = ?")->execute([$idCarte]);
        $requete = self::$bdd->prepare("DELETE FROM carte WHERE id_carte = ?");
        $requete->execute([$idCarte]);
        return true;
    }

    public function supprimerDependanceCarte($idCarte, $idCarteDependante){
        $requete = self::$bdd->prepare("DELETE FROM depend_de WHERE id_carte = ? AND id_carte_dependante = ?");
        return $requete->execute([$idCarte, $idCarteDependante]);
    }

    public function changerColonne($idCarte, $direction){
        $requete = self::$bdd->prepare("SELECT c.id_colonne, c.id_projet, col.libelle FROM carte c
        INNER JOIN colonne col ON c.id_colonne = col.id_colonne WHERE c.id_carte = ?");
        $requete->execute([$idCarte]);
        $carte = $requete->fetch();
        if ($carte === false) return false;

        $requete = self::$bdd->prepare("SELECT id_colonne, libelle FROM colonne WHERE id_projet = ? ORDER BY id_colonne");
        $requete->execute([$carte['id_projet']]);
        $colonnes = $requete->fetchAll();

        $position = 0;
        foreach ($colonnes as $index => $colonne) {
            if ($colonne['id_colonne'] == $carte['id_colonne']) { $position = $index; break; }
        }

        $nouvellePosition = $direction === "droite" ? $position + 1 : $position - 1;
        if (!isset($colonnes[$nouvellePosition])) return false;

        $nouvelleColonne = $colonnes[$nouvellePosition];

        if ($nouvelleColonne['libelle'] === "Terminé" && !$this->verifDependance($idCarte)) {
            return "dependance_non_terminee";
        }
        $requete = self::$bdd->prepare("UPDATE carte SET id_colonne = ? WHERE id_carte = ?");
        $requete->execute([$nouvelleColonne['id_colonne'], $idCarte]);
        return $nouvelleColonne['libelle'];
    }
    public function getResponsableCarte($idCarte){
        $requete = self::$bdd->prepare("SELECT id_utilisateur FROM carte WHERE id_carte = ?");
        $requete->execute([$idCarte]);
        $resultat = $requete->fetch();
        return $resultat ? $resultat['id_utilisateur'] : null;
    }

}