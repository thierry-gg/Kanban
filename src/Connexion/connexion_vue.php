<?php
class Connexion_vue{
    public function __construct(){}

    public function accueil(){
        echo'
        <h1>Bienvenue sur Kanban</h1>
        ';
    }

    // un formulaire d'inscription
    public function form_inscription(){
        echo'<br>
        <form action="index.php?action=inscription" method="POST">
            <label>Nom :</label>
            <input type="text" name="nom" placeholder="Jean" required>
            <input type="submit" name="inscription" value="S\'inscrire">
        ';
    }

    // un formulaire de connexion
    public function form_connexion(){
        echo'<br>
        <form action="index.php?action=connexion" method="POST">
            <label>Nom :</label>
            <input type="text" name="nom" placeholder="Jean" required>
            <input type="submit" name="connexion" value="Se connecter">
        ';
    }

}