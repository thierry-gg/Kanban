<?php
class Connexion_vue{
    public function __construct(){}

    // Formulaire d'accueil par default
    public function accueil(){
        echo'
        <h1>Bienvenue sur Kanban</h1>
        ';
    }

    // un formulaire d'inscription
    public function form_inscription($erreur = ""){
        echo'<br>
        <form action="index.php?action=inscription" method="POST">
            <label>Nom :</label>
            <input type="text" name="nom" placeholder="Jean" required>
            <label>Mot de passe :</label>
            <input type="text" name="mdp" required>
            <label>Confiramtion du mot de passe :</label>
            <input type="password" name="mdpverif" placeholder="Veuillez saisir à nouveau votre mot de passe" required>';
            if ($erreur != "") {
        echo '<p style="color:red;">'.$erreur.'</p>';
    }
        echo'
            <a href="index.php?">
            <button type="button">Retour</button></a>
            <input type="submit" name="inscription" value="S\'inscrire">
        </form>';
    }

    // un formulaire de connexion
    public function form_connexion($erreur = false){
        echo'<br>
        <form action="index.php?action=connexion" method="POST">
            <label>Nom :</label>
            <input type="text" name="nom" placeholder="Jean" required>
            <label>Mot de passe :</label>
            <input type="password" name="mdp" required>';
            if ($erreur) {
                echo '<p style="color:red;">Nom ou mot de passe incorrect.</p>';
            }
        echo '
            <a href="index.php?">
            <button type="button">Retour</button></a>
            <input type="submit" name="connexion" value="Se connecter">
        </form>';
    }

}