-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 21 août 2026 à 18:47
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `kanban_schema`
--

-- --------------------------------------------------------

--
-- Structure de la table `carte`
--

CREATE TABLE `carte` (
  `id_carte` int(11) NOT NULL,
  `titre_carte` varchar(50) DEFAULT NULL,
  `cree_le` datetime DEFAULT NULL,
  `fini_le` datetime DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `description_carte` varchar(250) NOT NULL,
  `id_colonne` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_projet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `colonne`
--

CREATE TABLE `colonne` (
  `id_colonne` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `id_projet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `depend_de`
--

CREATE TABLE `depend_de` (
  `id_carte` int(11) NOT NULL,
  `id_carte_dependante` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `est_responsable_de`
--

CREATE TABLE `est_responsable_de` (
  `id_projet` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projet`
--

CREATE TABLE `projet` (
  `id_projet` int(11) NOT NULL,
  `nom_projet` varchar(50) DEFAULT NULL,
  `cree_le` datetime DEFAULT NULL,
  `fini_le` datetime DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `description_projet` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `carte`
--
ALTER TABLE `carte`
  ADD PRIMARY KEY (`id_carte`),
  ADD KEY `id_colonne` (`id_colonne`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_projet` (`id_projet`);

--
-- Index pour la table `colonne`
--
ALTER TABLE `colonne`
  ADD PRIMARY KEY (`id_colonne`),
  ADD KEY `id_projet` (`id_projet`);

--
-- Index pour la table `depend_de`
--
ALTER TABLE `depend_de`
  ADD PRIMARY KEY (`id_carte`,`id_carte_dependante`),
  ADD KEY `id_carte_dependante` (`id_carte_dependante`);

--
-- Index pour la table `est_responsable_de`
--
ALTER TABLE `est_responsable_de`
  ADD PRIMARY KEY (`id_projet`,`id_utilisateur`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id_projet`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `carte`
--
ALTER TABLE `carte`
  MODIFY `id_carte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `colonne`
--
ALTER TABLE `colonne`
  MODIFY `id_colonne` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `projet`
--
ALTER TABLE `projet`
  MODIFY `id_projet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `carte`
--
ALTER TABLE `carte`
  ADD CONSTRAINT `carte_ibfk_1` FOREIGN KEY (`id_colonne`) REFERENCES `colonne` (`id_colonne`),
  ADD CONSTRAINT `carte_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `carte_ibfk_3` FOREIGN KEY (`id_projet`) REFERENCES `projet` (`id_projet`);

--
-- Contraintes pour la table `colonne`
--
ALTER TABLE `colonne`
  ADD CONSTRAINT `colonne_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `projet` (`id_projet`);

--
-- Contraintes pour la table `depend_de`
--
ALTER TABLE `depend_de`
  ADD CONSTRAINT `depend_de_ibfk_1` FOREIGN KEY (`id_carte`) REFERENCES `carte` (`id_carte`),
  ADD CONSTRAINT `depend_de_ibfk_2` FOREIGN KEY (`id_carte_dependante`) REFERENCES `carte` (`id_carte`);

--
-- Contraintes pour la table `est_responsable_de`
--
ALTER TABLE `est_responsable_de`
  ADD CONSTRAINT `est_responsable_de_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `projet` (`id_projet`),
  ADD CONSTRAINT `est_responsable_de_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
