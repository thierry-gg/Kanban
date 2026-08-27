<?php
include_once "connexion.php";
class Utilisateur_modele extends Connexion{
    public function __construct(){}

    // Quand l'utilisateur modifie son nom, elle se met a jour dans la base de donnéee
    public function editProfile($id, $nom){
        $requete = self::$bdd->prepare('UPDATE utilisateur SET nom = ? WHERE id_utilisateur = ?');
        $requete->execute([$nom, $id]);
    }

    // Supprime l'utilisateur
    public function supprimerProfile($id){
        $requete = self::$bdd->prepare('DELETE FROM utilisateur WHERE id_utilisateur = ?');
        $requete->execute([$id]);
    }

    // Quand un nouvelle utilisateur est ajouter dans un projet, il est automatiquement éditeur
    public function ajouterUtilisateurProjet($nom, $idProjet){

        $requeteUser = self::$bdd->prepare('SELECT id_utilisateur FROM utilisateur WHERE nom = ?');
        $requeteUser->execute([$nom]);
        $utilisateur = $requeteUser->fetch();

        if ($utilisateur === false) {
            return false;
        }
        $idUtilisateur = $utilisateur['id_utilisateur'];

        $requeteVerif = self::$bdd->prepare('SELECT * FROM est_responsable_de WHERE id_projet = ? AND id_utilisateur = ?');
        $requeteVerif->execute([$idProjet, $idUtilisateur]);
        if ($requeteVerif->fetch() !== false) {
            return false;
        }

        $requete = self::$bdd->prepare('INSERT INTO est_responsable_de (id_projet, id_utilisateur, role) VALUES (?, ?, ?)');
        $requete->execute([$idProjet, $utilisateur['id_utilisateur'], 'editeur']);
        return true;
    }

    // Permet d'obtenir le role de chaque utilisateur selon le projet
    public function getUtilisateursAvecRole($idProjet){
        $requete = self::$bdd->prepare('SELECT u.id_utilisateur, u.nom, er.role
         FROM utilisateur u INNER JOIN est_responsable_de er ON u.id_utilisateur = er.id_utilisateur
         WHERE er.id_projet = ? ORDER BY u.nom');

        $requete->execute([$idProjet]);
        return $requete->fetchAll();
    }

    // Quand on modifie le role d'un utilisateur, sa le met a jour dans la base de donnée
    public function modifierRoles($idProjet){
        if (!isset($_POST['validerRoles']) || !isset($_POST['roles'])) {
            return false;
        }
        $roles = $_POST['roles'];
        $rolesAutorises = ['admin', 'editeur', 'modeLecture'];
        foreach ($roles as $role) {
            if (!in_array($role, $rolesAutorises, true)) {
                return false;
            }
        }
        if (!in_array('admin', $roles, true)) {
            return false;
        }
        foreach ($roles as $idUtilisateur => $role) {
            $requete = self::$bdd->prepare('UPDATE est_responsable_de SET role = ? WHERE id_projet = ? AND id_utilisateur = ?');
            $requete->execute([$role, $idProjet, $idUtilisateur]);
        }
        return true;
    }

    // Permet d'obtenir le role d'un utilisateur dans un projet
    public function getRole($idProjet, $idUtilisateur){
        $requete = self::$bdd->prepare("SELECT role FROM est_responsable_de WHERE id_projet = ? AND id_utilisateur = ?");
        $requete->execute([$idProjet, $idUtilisateur]);
        $resultat = $requete->fetch();
        return $resultat ? $resultat['role'] : null;
    }

}