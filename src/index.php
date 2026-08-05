<?php session_start();
include_once ("connexion.php");

// connexion a la base de donnée phpmyadmin
$connexion = new Connexion();
$connexion->initConnexion();

$module = isset($_GET["module"]) ? $_GET["module"]: "default";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Kanban</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

        <?php if(isset($_SESSION['nom'])): ?>
        <li>
            <a href="index.php">Retour</a> ||
            <a href="index.php?module=default&action=deconnexion">Déconnexion</a> ||
            <a href="index.php?module=utilisateur&action=formProfile">Profil</a>
        </li>

        <?php else: ?>
        <li>
            <a href="index.php?module=default&action=formConnexion">Se connecter</a> ||
            <a href="index.php?module=default&action=formInscription">S'inscrire</a>
        </li>
        <?php endif;

        if(isset($_GET['action']) && $_GET['action'] == "deconnexion"){
            session_destroy();
            header("Location: index.php");
            exit;

        }

        switch($module){
            case "utilisateur":
                include_once ("Utilisateur/utilisateur_controleur.php");
                $controleur = new Utilisateur_controleur();
                break;

            case "projet":
                include_once ("Projet/projet_controleur.php");
                $controleur = new Projet_controleur();
                break;

            case "carte":
                include_once ("Carte/carte_controleur.php");
                $controleur = new Carte_controleur();
                break;

            default:

            if(isset($_SESSION['id_utilisateur'])) {
                include_once("Projet/projet_controleur.php");
                $controleur = new Projet_controleur();

            }else{
                include_once("Connexion/connexion_controleur.php");
                $controleur = new Connexion_controleur();
            }
                break;
        }
        $controleur->exec();
        ?>

    </body>
</html>


