<?php
include_once "connexion.php";

class Connexion_modele extends Connexion{

    // fonction ajout d'un utilisateur à la base de donnée
    public function inscription(){
        $nom = $_POST['nom'];

        $sql = "INSERT INTO utilisateur (nom) VALUES (?)";

        $requete = self::$bdd->prepare($sql);
        $requete->execute([$nom]);
        header("Location: index.php");
    }

    // fonction de connexion si l'utilisateur a saisie le bon login
    public function connexion(){
        $nom = $_POST['nom'];

        $sql = self::$bdd->prepare('SELECT * FROM utilisateur WHERE nom = ?');
        $sql->execute([$nom]);
        $user = $sql->fetch();

        if($user){
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            header("Location: index.php");
            exit;
        }else{
            return false;
        }
    }

    // avoir le nom de l'utilisateur dans la base de donnée
    public function getNom($nom){
        $sql = self::$bdd->prepare('SELECT * from utilisateur where nom = ?');
        $sql->execute([$nom]);
        return $sql->fetch();
    }


}