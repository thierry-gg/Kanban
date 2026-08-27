<?php
include_once "connexion.php";

class Connexion_modele extends Connexion{

    // fonction ajout d'un utilisateur à la base de donnée
    public function inscription(){
        $nom = $_POST['nom'];
        $mdp = $_POST['mdp'];
        $hashMdp = password_hash($mdp, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateur (nom, mdp) VALUES (?,?)";

        $requete = self::$bdd->prepare($sql);
        $requete->execute([$nom, $hashMdp]);
        header("Location: index.php");
        exit;
    }

    // fonction de connexion si l'utilisateur a saisie le bon login
    public function connexion(){
        $nom = $_POST['nom'];
        $mdp = $_POST['mdp'];

        $sql = self::$bdd->prepare('SELECT * FROM utilisateur WHERE BINARY nom = ?');
        $sql->execute([$nom]);
        $users = $sql->fetchAll();

        foreach ($users as $user) {
            if ($user && password_verify($mdp, $user['mdp'])) {
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
                header("Location: index.php");
                exit;
            }
        }
        return false;

    }

    // avoir le nom de l'utilisateur dans la base de donnée
    public function getNom($nom){
        $sql = self::$bdd->prepare('SELECT * from utilisateur where nom = ?');
        $sql->execute([$nom]);
        return $sql->fetch();
    }


}