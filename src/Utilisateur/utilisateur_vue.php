<?php

class Utilisateur_vue{

    public function formProfile()
    {
        echo '
        <h3>Profil</h3>

        <form action="index.php?module=utilisateur&action=profile" method="POST">
            <p>
                <strong>Nom :</strong>
                <span id="nomAffiche">' . htmlspecialchars($_SESSION['nom']) .'</span>
              
                <input type="text" id="nomInput" name="nom" 
                value="' . htmlspecialchars($_SESSION['nom']) . '" 
                style="display:none;">
    
                <button type="button" id="btnModifier" onclick="modifier()">Modifier</button>
                <input type="submit" id="btnValider" value="Valider" style="display:none;" name="valider_utilisateur">
                
            </p>
        </form>
        <form action="index.php?module=utilisateur&action=supprimerProfile" method="POST">
            <button type="submit" id="btnSupprimer">Supprimer le compte</button>
            
        </form><br>  
        <a href="index.php"><button type="button">Retour</button></a>
    
    <script>
        function modifier() {
            document.getElementById("nomAffiche").style.display = "none";
            document.getElementById("nomInput").style.display = "inline";

            document.getElementById("btnModifier").style.display = "none";
            document.getElementById("btnValider").style.display = "inline";}
    </script>
        ';
    }

    public function formAjouterUtilisateurProjet($idProjet){
        echo'<br>
            <form action="index.php?module=utilisateur&action=ajouterUtilisateurProjet" method="POST">
            <input type="hidden" name="id_projet" value="'.$idProjet.'">
                <label>Nom :</label>
                <input type="text" name="nom" placeholder="Jean" required><br><br>
                <a href="index.php"><button type="button">Retour</button></a>
                <input type="submit" name="ajouterUtilisateur" value="Ajouter">
            </form>
        ';
    }

    public function formGestionDroit($utilisateurs, $idProjet){
        echo '<h1>Gestion des droits</h1>';
        echo '<form action="index.php?module=utilisateur&action=modifierRoles" method="POST">';
        echo '<input type="hidden" name="id_projet" value="'.$idProjet.'">';
        foreach ($utilisateurs as $utilisateur) {
            echo '
                <div class="utilisateur">
                    <strong>'.htmlspecialchars($utilisateur['nom']).'</strong>
                    <select name="roles['.$utilisateur['id_utilisateur'].']">
                        <option value="admin" '
                        .($utilisateur['role'] === 'admin' ? 'selected' : '').'>
                            Administrateur
                        </option>
        
                        <option value="editeur" '
                        .($utilisateur['role'] === 'editeur' ? 'selected' : '').'>
                            Éditeur
                        </option>
        
                        <option value="modeLecture" '
                        .($utilisateur['role'] === 'modeLecture' ? 'selected' : '').'>
                            Observateur
                        </option>
                    </select>
                </div>';
        }

        echo '
                <a href="index.php?module=projet&action=afficherProjet&id='.$idProjet.'"><button type="button">Retour</button></a>
                <input type="submit" name="validerRoles" Value="Valider">
        </form>';
    }
}