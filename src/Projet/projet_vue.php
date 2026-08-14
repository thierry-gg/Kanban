<?php

class Projet_vue{

    public function accueilProjet($projets, $utilisateursParProjet, $rolesParProjet){

        echo'<h1>Bonjour '.$_SESSION['nom'].' !</h1>
         <h2>Liste des projets</h2>';

        if (count($projets) === 0) {
            echo'<p>Vous ne possédez aucun projet</p>
                <a href="index.php?module=projet&action=formProjet">Créé un projet</a><br><br>';
        } else {
            echo '<div class="listeProjets">';
            foreach ($projets as $projet) {

                $roleProjet = $rolesParProjet[$projet['id_projet']] ?? null;
                $estAdmin = ($roleProjet === 'admin');

                if ($projet['fini_le'] !== NULL) {
                    $statut = "Terminé";
                } elseif ($projet['deadline'] !== NULL && strtotime($projet['deadline']) < time()) {
                    $statut = "En retard";
                } else {
                    $statut = "En cours";
                }
                $estTermine = $projet['fini_le'] !== NULL;

                $dateDebut = date("d/m/Y", strtotime($projet['cree_le']));
                $dateFin = $projet['deadline'] === NULL ? "Aucune date de prévue" : date("d/m/Y", strtotime($projet['deadline']));

                $listeUtilisateurs = "";
                if (isset($utilisateursParProjet[$projet['id_projet']])) {
                    foreach ($utilisateursParProjet[$projet['id_projet']] as $utilisateur) {
                        $listeUtilisateurs .= htmlspecialchars($utilisateur['nom']).', ';
                    }
                    $listeUtilisateurs = rtrim($listeUtilisateurs, ', ');
                }

                echo'
                <div class="projet">
                    <form action="index.php?module=projet&action=editerProjet" method="POST">
                        <input type="hidden" name="id_projet" value="'.$projet['id_projet'].'">
    
                        <h3>
                            <a href="index.php?module=projet&action=afficherProjet&id='.$projet['id_projet'].'" id="titreProjetAfficher'.$projet['id_projet'].'">
                            '.htmlspecialchars($projet['nom_projet']).'</a>
                            
                            <span id="labelTitre'.$projet['id_projet'].'" style="display:none;"><strong>Titre :</strong></span>
                            <input type="text" id="titreProjetInput'.$projet['id_projet'].'" name="titreProjet" 
                            value="'.htmlspecialchars($projet['nom_projet']).'" style="display:none;">
                            <button type="button" id="btnInfo'.$projet['id_projet'].'" onclick="toggleInfo('.$projet['id_projet'].')">Infos</button>
                        </h3>
                        <p>                        
                            <span id="labelDescription'.$projet['id_projet'].'" style="display:none;"><strong>Description :</strong></span>
                            <input type="text" id="descriptionProjetInput'.$projet['id_projet'].'" name="description_projet" 
                            value="'.htmlspecialchars($projet['description_projet']).'" style="display:none;">    
                        </p>   
                        
                        <p>Statut : '.$statut.' </p>
    
                        <div id="blocInfo'.$projet['id_projet'].'" style="display:none; border:1px solid #ccc; padding:10px; margin-top:5px; margin-bottom:10px">
                            <p><strong>Description :</strong> '.($projet['description_projet'] !== '' ? htmlspecialchars($projet['description_projet']) : "Aucune description").'</p>
                            <p><strong>Créé le :</strong> '.$dateDebut.'&emsp; <strong>Date limite :</strong> '.$dateFin.'</p>
                            <p><strong>Responsables :</strong> '.($listeUtilisateurs !== "" ? $listeUtilisateurs : "Aucun autre utilisateur").'</p>
                    </form>
                </div>';

                if (!$estTermine && $estAdmin) {
                    echo '
                    <button type="button" id="btnEditerProjet'.$projet['id_projet'].'" onclick="editer('.$projet['id_projet'].')">Éditer</button>
                    <button type="button" id="btnEditerAnnuler'.$projet['id_projet'].'" style="display:none;" 
                    data-titre="'.htmlspecialchars($projet['nom_projet'], ENT_QUOTES).'"
                    data-description="'.htmlspecialchars($projet['description_projet'], ENT_QUOTES).'"
                    onclick="annuler('.$projet['id_projet'].', this )">Annuler</button>
                    <input type="submit" id="btnEditerValider'.$projet['id_projet'].'" value="Valider" style="display:none;" name="validerEditerProjet">
                ';
                }
                echo '</form>';

                if (!$estTermine && $estAdmin) {
                    echo '<br><br>
                    <form action="index.php?module=utilisateur&action=formAjoutUtilisateur" method="POST" style="display:inline;">
                        <input type="hidden" name="id_projet" value="'.$projet['id_projet'].'">                    
                        <input type="submit" value="Ajouter des utilisateurs">
                    </form>
                    <br><br>
                    <form action="index.php?module=projet&action=terminerProjet" method="POST" style="display:inline;">
                        <input type="hidden" name="id_projet" value="'.$projet['id_projet'].'">
                        <button type="submit" id="btnTerminerProjet'.$projet['id_projet'].'" style="display:none;" onclick="return confirm(\'Mettre ce projet en terminé ? \n Il ne sera plus possible de le modifier par la suite !\')">Terminé</button>
                    </form>
                ';
                }
                echo '</div>';
            }
            echo'</div>';
            echo'<br><a href="index.php?module=projet&action=formProjet">Créé un nouveau projet</a>';
            echo'
                <script>
                    function toggleInfo(id) {
                        var bloc = document.getElementById("blocInfo" + id);
                        bloc.style.display = (bloc.style.display === "none") ? "block" : "none";
                    }
        
                    function editer(id) {
                        document.getElementById("titreProjetAfficher" + id).style.display = "none";
                        document.getElementById("labelTitre" + id).style.display = "inline";
                        document.getElementById("titreProjetInput" + id).style.display = "inline";
                        
                        document.getElementById("labelDescription" + id).style.display = "inline";
                        document.getElementById("descriptionProjetInput" + id).style.display = "inline";

                        document.getElementById("btnEditerProjet" + id).style.display = "none";
                        
                        document.getElementById("btnEditerValider" + id).style.display = "inline";
                        document.getElementById("btnEditerAnnuler" + id).style.display = "inline";
                        
                        var btnTerminer = document.getElementById("btnTerminerProjet" + id);
                        if (btnTerminer) btnTerminer.style.display = "inline";
                    }
        
                    function annuler(id, bouton) {
                        document.getElementById("titreProjetAfficher" + id).style.display = "inline";
                        document.getElementById("labelTitre" + id).style.display = "none";
                        document.getElementById("titreProjetInput" + id).style.display = "none";
                        
                        document.getElementById("labelDescription" + id).style.display = "none";
                        document.getElementById("descriptionProjetInput" + id).style.display = "none";
                        
                        document.getElementById("titreProjetInput" + id).value = bouton.dataset.titre;
                        document.getElementById("descriptionProjetInput" + id).value = bouton.dataset.description;
                        
                        
                        document.getElementById("btnEditerProjet" + id).style.display = "inline";
                        document.getElementById("btnEditerValider" + id).style.display = "none";
                        
                        document.getElementById("btnEditerAnnuler" + id).style.display = "none";
                        
                        var btnTerminer = document.getElementById("btnTerminerProjet" + id);
                        if (btnTerminer) btnTerminer.style.display = "none";
        
                    }
                </script>';
        }
    }

    public function formProjet(){
        echo'<br>
           <form action="index.php?module=projet&action=projet" method="POST">
                <label>Titre :</label><br>
                <input type="text" name="titre_projet" required><br><br>
                <label>Description :</label><br>
                <input type="text" name="description_projet"><br><br>
                <label>Date de fin:</label><br>
                <input type="date" name="date_projet"><br><br>
                <a href="index.php"><button type="button">Retour</button></a>
                <input type="submit" name="valider_projet" value="Valider">
           </form> 
        ';
    }

    public function afficherProjet($projets, $cartes, $role, $id_utilisateur){

        $estTermine = isset($projets['fini_le']) && $projets['fini_le'] !== NULL;
        $modeLectureSeule = ($role === 'modeLecture');
        $lectureSeule = $estTermine || $modeLectureSeule;
        $estAdmin = ($role === 'admin');
        $colonnes = [
            "A faire" => "",
            "En cours" => "",
            "Terminé" => ""
        ];
        echo'<div class="actions-projet">';
        if (!$lectureSeule) {
            echo'<form action="index.php?module=carte&action=formCarte&id_projet='.$projets['id_projet'].'" method="POST">
                    <input type="hidden" name="id_projet" value="'.$projets['id_projet'].'">
                    <button type="submit" value="creeCarte">Créé une carte</button>
                  </form>';
        }
        if ($estAdmin) {
            echo '<form action="index.php?module=utilisateur&action=formGestionDroit&id_projet='.$projets['id_projet'].'" method="POST">
                    <input type="hidden" name="gestionDroit" value="'.$projets['id_projet'].'">
                    <button type="submit" value="gestionDroit">Gestion des droits</button>
                  </form><br>';
        }
        echo'</div>';

        foreach ($cartes as $carte) {

            $peutEditerCarte = !$lectureSeule
                && ($estAdmin || ($role === 'editeur' && $carte['id_utilisateur'] == $id_utilisateur));

            $boutonsEdition = '';
            $boutonsAssignerDependanceCarte = '';
            $boutonsDeplacementCarte = '';
            $classeDeadline = '';
            $badgeDeadline = '';
            $boutonSupprimer = '';

            $dependances = (new Carte_modele())->getDependancesCarte($carte['id_carte']);
            $texteDependances = "Aucune";
            if(count($dependances) > 0){
                $liste = [];
                foreach($dependances as $dep){
                    $liste[] = htmlspecialchars($dep['titre_carte']);
                }
                $texteDependances = implode(", ", $liste);
            }
            $infosCarte = '
                <div id="blocInfoCarte'.$carte['id_carte'].'" 
                    style="display:none; border:3px solid #ccc; padding:10px; margin-top:5px; margin-bottom:10px">
                    <p><strong>Description : </strong>'.htmlspecialchars($carte['description_carte']).'</p>
                    <p><strong>Créé le :</strong> '.date("d/m/Y H:i", strtotime($carte['cree_le'])).' &emsp; <strong>Date limite :</strong> 
                    '.($carte['deadline'] !== NULL ? date("d/m/Y", strtotime($carte['deadline'])) : "Aucune date de prévue") .'</p>
                    <p><strong>Responsable :</strong> '.($carte['nom'] !== NULL ? htmlspecialchars($carte['nom']) : "Aucun responsable").'</p>
                    <p><strong>Dependes de :</strong> '.$texteDependances.'</p>
                </div>';

            if (!$estTermine) {
                if ($carte['deadline'] !== null && $carte['fini_le'] === null) {
                    $aujourdHui = new DateTime('today');
                    $deadline = new DateTime($carte['deadline']);

                    $joursRestants = (int)$aujourdHui->diff($deadline)->format('%r%a');

                    if ($joursRestants < 0) {
                        $badgeDeadline = "<p style='color:red;font-weight:bold;'>Deadline dépassée !</p>";

                    } elseif ($joursRestants === 0) {
                        $badgeDeadline = "<p style='color:orange;font-weight:bold;'>À terminer aujourd'hui !</p>";

                    } elseif ($joursRestants <= 2) {
                        $badgeDeadline = "<p style='color:orange;font-weight:bold;'>Deadline proche !</p>";
                    }
                }
            }

            if ($peutEditerCarte) {
                $boutonsEdition = '
                    <button type="button" id="btnEditerCarte'.$carte['id_carte'].'" onclick="editer('.$carte['id_carte'].')">Éditer</button>
                    
                    <button type="button" id="btnEditerAnnuler'.$carte['id_carte'].'" style="display:none;"
                    data-titre="'.htmlspecialchars($carte['titre_carte'], ENT_QUOTES).'"
                    data-description="'.htmlspecialchars($carte['description_carte'], ENT_QUOTES).'"
                    data-deadline="'.htmlspecialchars($carte['deadline'], ENT_QUOTES).'"                 
                    onclick="annuler('.$carte['id_carte'].',this)">Annuler</button>
                    <input type="submit" id="btnEditerValider'.$carte['id_carte'].'" value="Valider" style="display:none;" name="validerEditerCarte"><br><br>            
                ';

                $boutonsDeplacementCarte = '
                    <div class="deplacementCarte">
                        <form action="index.php?module=carte&action=changerColonne" method="POST">
                            <input type="hidden" name="id_carte" value="'.$carte['id_carte'].'">
                            <input type="hidden" name="id_colonne" value="'.$carte['id_colonne'].'">
                            <button name="direction" value="gauche">←</button>                    
                            <button name="direction" value="droite">→</button>
                        </form>
                    </div>';
            }

            if ($estAdmin && !$lectureSeule) {
                $boutonsAssignerDependanceCarte = '
                <div class="boutonCarteAssigner">
                    <form action="index.php?module=carte&action=formUtilisateurCarte" method="POST">
                        <input type="hidden" name="id_carte" value="'.$carte['id_carte'].'">
                        <input type="hidden" name="id_projet" value="'.$projets['id_projet'].'">
                        <input type="submit" value="Assigner un responsable">
                    </form>
                </div>
                <div class="boutonCarteDependance">
                    <form action="index.php?module=carte&action=formDependanceCarte" method="POST">
                            <input type="hidden" name="id_carte" value="'.$carte['id_carte'].'">
                            <input type="hidden" name="id_projet" value="'.$projets['id_projet'].'">
                            <input type="submit" value="Déclarer une dépendance">
                    </form>
                </div>
                <div class="boutonCarteSupprimer">
                    <form action="index.php?module=carte&action=supprimerCarte" method="POST">
                        <input type="hidden" name="id_carte" value="'.$carte['id_carte'].'">
                        <button type="submit" id="btnEditerSupprimer'.$carte['id_carte'].'" style="display:none;" onclick="return confirm(\'Supprimer cette carte définitivement ?\')">Supprimer</button>
                    </form>
                </div>
                ';
            }

            $colonnes[$carte['libelle']] .= '
            <div class="carte">
                '.$boutonsDeplacementCarte.'
                <form action="index.php?module=carte&action=editerCarte" method="POST">
                    <input type="hidden" name="id_carte" value="'.$carte['id_carte'].'">
                        
                    <h3>
                        <span id="labelTitre'.$carte['id_carte'].'" style="display:none;"><strong>Titre :</strong></span>
                        <span id="titreCarteAfficher'.$carte['id_carte'].'">
                        '.htmlspecialchars($carte['titre_carte']).'
                        </span>
                        <input type="text" id="titreCarteInput'.$carte['id_carte'].'" name="titre_carte" 
                        value="'.htmlspecialchars($carte['titre_carte']).'" style="display:none;">
                        <button type="button" onclick="toggleInfoCarte('.$carte['id_carte'].')">Infos</button>
                        
                        '.$badgeDeadline.'
                        </h3>
                    
                    <p>
                        <span id="labelDescription'.$carte['id_carte'].'" style="display:none;"><strong>Description :</strong></span>
                        <input type="text" id="descriptionCarteInput'.$carte['id_carte'].'" name="description_carte" 
                        value="'.htmlspecialchars($carte['description_carte']).'" style="display:none;">    
                    </p>
                    
                    <p>
                        <span id="deadlineCarteAfficher'.$carte['id_carte'].'" style="display:none"><strong>Date limite :</strong></span>
                        <input type="date" id="deadlineCarteInput'.$carte['id_carte'].'" name="deadline_carte" 
                        value="'.($carte['deadline'] !== null ? date("Y-m-d", strtotime($carte['deadline'])) : "").'" style="display:none;">
                    </p>
                    '.$infosCarte.'
                    '.$boutonsEdition.'
                </form>
                
                '.$boutonsAssignerDependanceCarte.'
            </div>';
        }

        if ($estTermine) {
            echo '<p><em>Ce projet est terminé - plus aucune modification est possible.</em></p>';
        } elseif ($modeLectureSeule) {
            echo '<p><em>Vous êtes en spéctateur sur ce projet.</em></p>';
        }

        echo '           
        <div class="kanban">
            <div class="colonne">
                <h2>À faire</h2>             
                '.($colonnes["A faire"] !== "" ? $colonnes["A faire"] : "<p>Aucune carte</p>").'
            </div>
            <div class="separateur"></div>
            
            <div class="colonne">
                <h2>En cours</h2>
                '.($colonnes["En cours"] !== "" ? $colonnes["En cours"] : "<p>Aucune carte</p>").'
            </div>
            <div class="separateur"></div>
            
            <div class="colonne">
                <h2>Terminé</h2>
                '.($colonnes["Terminé"] !== "" ? $colonnes["Terminé"] : "<p>Aucune carte</p>").'
            </div>
        </div><br>
        ';
        echo'
        <script>
            function toggleInfoCarte(id){
                var bloc = document.getElementById("blocInfoCarte" + id);
                if(bloc.style.display === "none"){
                    bloc.style.display = "block";
                }else{
                    bloc.style.display = "none";
                }
            }
            
            function editer(id){
                document.getElementById("labelTitre" + id).style.display = "inline";
                document.getElementById("titreCarteAfficher" + id).style.display = "none";
                document.getElementById("titreCarteInput" + id).style.display = "inline";
                
                document.getElementById("labelDescription" + id).style.display = "inline";
                document.getElementById("descriptionCarteInput" + id).style.display = "inline";
                
                document.getElementById("deadlineCarteAfficher" + id).style.display = "inline";
                document.getElementById("deadlineCarteInput" + id).style.display = "inline";
                
                document.getElementById("btnEditerCarte" + id).style.display = "none";
                document.getElementById("btnEditerValider" + id).style.display = "inline";
                
                var btnSupprimer = document.getElementById("btnEditerSupprimer" + id);
                if (btnSupprimer) btnSupprimer.style.display = "inline";
                
                document.getElementById("btnEditerAnnuler" + id).style.display = "inline"; 
            }
            
            function annuler(id, bouton){
                document.getElementById("labelTitre" + id).style.display = "none";
                document.getElementById("titreCarteAfficher" + id).style.display = "inline";
                document.getElementById("titreCarteInput" + id).style.display = "none";
                
                document.getElementById("labelDescription" + id).style.display = "none";
                document.getElementById("descriptionCarteInput" + id).style.display = "none";
                
                document.getElementById("deadlineCarteAfficher" + id).style.display = "none";
                document.getElementById("deadlineCarteInput" + id).style.display = "none";
    
                document.getElementById("titreCarteInput" + id).value = bouton.dataset.titre;
                document.getElementById("descriptionCarteInput" + id).value = bouton.dataset.description;
                document.getElementById("deadlineCarteInput" + id).value = bouton.dataset.deadline;
                
                document.getElementById("btnEditerCarte" + id).style.display = "inline";
                document.getElementById("btnEditerValider" + id).style.display = "none";
                
                var btnSupprimer = document.getElementById("btnEditerSupprimer" + id);
                if (btnSupprimer) btnSupprimer.style.display = "none";
                
                document.getElementById("btnEditerAnnuler" + id).style.display = "none";
            }
        </script>
        ';
    }
}