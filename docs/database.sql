-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 22 Janvier 2017 à 14:35
-- Version du serveur :  5.5.54-0+deb8u1
-- Version de PHP :  5.6.29-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `piconso`
--

-- --------------------------------------------------------

--
-- Structure de la table `tbl_compteur`
--

CREATE TABLE `tbl_compteur` (
`id` smallint(4) unsigned NOT NULL,
  `adresse` varchar(12) NOT NULL,
  `intensite_souscrite` smallint(4) unsigned NOT NULL,
  `tarif_abo_annuel` decimal(8,4) unsigned NOT NULL DEFAULT '0.0000',
  `tarif_kwh_base` decimal(8,4) unsigned DEFAULT NULL,
  `tarif_kwh_hp` decimal(8,4) unsigned DEFAULT NULL,
  `tarif_kwh_hc` decimal(8,4) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tbl_releve`
--

CREATE TABLE `tbl_releve` (
`id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `id_compteur` smallint(4) unsigned NOT NULL,
  `periode` varchar(4) NOT NULL COMMENT 'Période tarifaire en cours',
  `index_total` int(10) unsigned NOT NULL COMMENT 'Index total (si forfait HC : idx_hc + idx_hp)',
  `index_hp` int(10) unsigned DEFAULT NULL COMMENT 'Index en heures pleines',
  `index_hc` int(10) unsigned DEFAULT NULL COMMENT 'Index en heures creuses',
  `puissance_moyenne` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Puissance moyenne (en W) calculée depuis le dernier relevé'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tbl_statsjour`
--

CREATE TABLE `tbl_statsjour` (
  `jour` date NOT NULL,
  `id_compteur` smallint(4) unsigned NOT NULL DEFAULT '1',
  `conso_totale` int(10) NOT NULL DEFAULT '0',
  `conso_hp` int(10) DEFAULT NULL,
  `conso_hc` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tbl_statsmois`
--

CREATE TABLE `tbl_statsmois` (
  `mois` varchar(7) NOT NULL,
  `id_compteur` smallint(4) unsigned NOT NULL DEFAULT '1',
  `conso_totale` int(10) NOT NULL DEFAULT '0',
  `conso_hp` int(10) DEFAULT NULL,
  `conso_hc` int(10) DEFAULT NULL,
  `tarif_abo_annuel` decimal(8,4) unsigned NOT NULL DEFAULT '0.0000',
  `tarif_kwh_base` decimal(8,4) unsigned DEFAULT NULL,
  `tarif_kwh_hp` decimal(8,4) unsigned DEFAULT NULL,
  `tarif_kwh_hc` decimal(8,4) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `tbl_compteur`
--
ALTER TABLE `tbl_compteur`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `adresse` (`adresse`);

--
-- Index pour la table `tbl_releve`
--
ALTER TABLE `tbl_releve`
 ADD PRIMARY KEY (`id`), ADD KEY `date` (`date`,`id_compteur`);

--
-- Index pour la table `tbl_statsjour`
--
ALTER TABLE `tbl_statsjour`
 ADD PRIMARY KEY (`jour`,`id_compteur`);

--
-- Index pour la table `tbl_statsmois`
--
ALTER TABLE `tbl_statsmois`
 ADD PRIMARY KEY (`mois`,`id_compteur`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `tbl_compteur`
--
ALTER TABLE `tbl_compteur`
MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `tbl_releve`
--
ALTER TABLE `tbl_releve`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;