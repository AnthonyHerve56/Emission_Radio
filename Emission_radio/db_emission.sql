-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  mer. 25 mars 2026 à 14:22
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `db_emission`
--

-- --------------------------------------------------------

--
-- Structure de la table `emissions`
--

CREATE TABLE `emissions` (
  `emission_id` int(11) NOT NULL,
  `emission_heure_debut` datetime(6) NOT NULL,
  `emission_heure_fin` datetime(6) NOT NULL,
  `sujets` varchar(100) NOT NULL,
  `evenement_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `evenement_id` int(11) NOT NULL,
  `evenement_lieu` varchar(50) NOT NULL,
  `evenement_date` date NOT NULL,
  `evenement_description` text NOT NULL,
  `menace_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `menace`
--

CREATE TABLE `menace` (
  `menace_id` int(11) NOT NULL,
  `menace_nom` varchar(30) NOT NULL,
  `menace_taille` int(7) NOT NULL,
  `menace_poids` int(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `victime`
--

CREATE TABLE `victime` (
  `victime_id` int(11) NOT NULL,
  `victime_nom` varchar(30) NOT NULL,
  `victime_prenom` varchar(30) NOT NULL,
  `victime_ecole` varchar(30) NOT NULL,
  `evenement_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `emissions`
--
ALTER TABLE `emissions`
  ADD PRIMARY KEY (`emission_id`),
  ADD KEY `fk_evenement_id` (`evenement_id`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`evenement_id`),
  ADD KEY `fk_menace_id` (`menace_id`);

--
-- Index pour la table `menace`
--
ALTER TABLE `menace`
  ADD PRIMARY KEY (`menace_id`);

--
-- Index pour la table `victime`
--
ALTER TABLE `victime`
  ADD PRIMARY KEY (`victime_id`),
  ADD KEY `fk_evenement_id` (`evenement_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `emissions`
--
ALTER TABLE `emissions`
  MODIFY `emission_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `evenement_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `menace`
--
ALTER TABLE `menace`
  MODIFY `menace_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `victime`
--
ALTER TABLE `victime`
  MODIFY `victime_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
