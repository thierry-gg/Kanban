<?php
class Carte_vue {

    public function form_carte($idProjet){

        // Formulaire de création de carte
        echo'<br>
            <form action="index.php?module=carte&action=carte" method="post">
                <label>Titre de la carte :</label>
                <input type="text" name="titre_carte"/><br><br>
                <label>Description :</label>
                <input type="text" name="description_carte"/><br><br>
                <label>Définir une date limite :</label>
                <input type="date" name="date_projet"><br><br>
                <a href="index.php?module=projet&action=afficherProjet&id='.$idProjet.'"><button type="button">Retour</button></a>
                <input type="submit" name="valider_carte" value="Créé"/>
            </form>
        ';
    }

    // Formulaire d'assignation d'un responsable pour une carte
    public function formUtilisateurCarte($idProjet, $idCarte, $membreActuel){
        echo '<br><h3>Assigner un responsable</h3>';
        if (count($membreActuel) > 0) {
            echo '<p>Sélectionnez un responsable :</p>';
        }
        echo '
            <form action="index.php?module=carte&action=assignerUtilisateurCarte" method="POST">
                <input type="hidden" name="id_carte" value="'.$idCarte.'">        
                <label>Responsable :</label>        
                <select name="id_utilisateur" required>';
                foreach ($membreActuel as $a) {
                    echo '<option value="'.$a['id_utilisateur'].'">'.$a['nom'].'</option>';
                }
                echo '
                </select>
                <a href="index.php?module=projet&action=afficherProjet&id='.$idProjet.'"><button type="button">Retour</button></a>
                <input type="submit" value="Assigner">
            </form>';
    }

    // Formulaire de déclaration d'une dépendance
    public function formDependanceCarte($idProjet, $idCarte, $autresCartes, $dependances){
        echo '<br><h3>Déclarer une dépendance</h3>';
        if (count($autresCartes) === 0) {
            echo '<p>Aucune autre carte disponible dans ce projet.</p>
                <a href="index.php?module=projet&action=afficherProjet&id='.$idProjet.'"><button type="button">Retour</button></a>
              ';
            return;
        }
        echo '
            <form action="index.php?module=carte&action=ajouterDependanceCarte" method="POST">
                <input type="hidden" name="id_carte" value="'.$idCarte.'">
                <label>Cette carte dépend de :</label>
                <select name="id_carte_dependante" required>';
                foreach ($autresCartes as $c) {
                    echo '<option value="'.$c['id_carte'].'">'.htmlspecialchars($c['titre_carte']).'</option>';
                }
                echo '</select>
                <a href="index.php?module=projet&action=afficherProjet&id='.$idProjet.'">
                <button type="button">Retour</button></a>
                <input type="submit" value="Valider">
            </form>';

        echo '<br><h3>Dépendances existantes</h3>';
        if (count($dependances) === 0) {
            echo '<p>Aucune dépendance.</p>';
        } else {
            echo'<div class="liste-dependances">';
            foreach ($dependances as $d) {
                echo '
                <div class="dependance">
                    <span>'.htmlspecialchars($d['titre_carte']).'</span>
                    <form action="index.php?module=carte&action=supprimerDependanceCarte" method="POST">
                        <input type="hidden" name="id_carte" value="'.$idCarte.'">
                        <input type="hidden" name="id_carte_dependante" value="'.$d['id_carte'].'">
                        <button type="submit">Supprimer</button><br><br>
                    </form>
                </div>';
            }
        }
    }
}