-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 27 mai 2020 à 15:45
-- Version du serveur :  5.7.24
-- Version de PHP : 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `jeu_combat`
--

-- --------------------------------------------------------

--
-- Structure de la table `personnages`
--

CREATE TABLE `personnages` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `nom` varchar(50) NOT NULL,
  `degats` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `niveau` int(11) DEFAULT '1',
  `experience` int(11) DEFAULT '0',
  `strength` int(11) DEFAULT NULL,
  `type` enum('magicien','guerrier','archer') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `personnages`
--

INSERT INTO `personnages` (`id`, `nom`, `degats`, `niveau`, `experience`, `strength`, `type`) VALUES
(1, 'toto', 70, 4, 75, NULL, 'magicien'),
(4, 'tatatoto', 40, 1, 0, NULL, 'magicien'),
(6, 'Pinot', 95, 3, 0, 3, 'magicien'),
(7, 'tata', 40, 0, 0, NULL, 'magicien'),
(8, 'bloup', 85, 0, 0, NULL, 'magicien'),
(9, 'salade', 0, NULL, NULL, NULL, 'magicien'),
(10, 'courge', 15, 0, 0, NULL, 'magicien'),
(11, 'moutmout', 0, NULL, NULL, NULL, 'guerrier'),
(12, 'falcon', 0, 0, 75, NULL, 'archer');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `personnages`
--
ALTER TABLE `personnages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `personnages`
--
ALTER TABLE `personnages`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
