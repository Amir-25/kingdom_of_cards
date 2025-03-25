-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 17 mars 2025 à 10:46
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
-- Base de données : `kingdom_of_cards`
--

-- --------------------------------------------------------

--
-- Structure de la table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Monstre','Fusion') NOT NULL,
  `attack` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `ability` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `fusion_card_1` int(11) DEFAULT NULL,
  `fusion_card_2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cards`
--

INSERT INTO `cards` (`id`, `name`, `type`, `attack`, `defense`, `ability`, `image`, `fusion_card_1`, `fusion_card_2`) VALUES
(1, 'Gobelin Pyromane', 'Monstre', 1200, 800, NULL, 'assets/Cartes/gobelin_pyromane.jpg', NULL, NULL),
(2, 'Serpent des Sables', 'Monstre', 1000, 900, NULL, 'assets/Cartes/serpent_des_sables.jpg', NULL, NULL),
(3, 'Golem Mécanique', 'Monstre', 1600, 2000, NULL, 'assets/Cartes/golem_mecanique.jpg', NULL, NULL),
(4, 'Chimère sanglante', 'Monstre', 1800, 1200, NULL, 'assets/Cartes/chimere_sanglante.jpg', NULL, NULL),
(5, 'Gardien Spectral', 'Monstre', 1400, 1700, NULL, 'assets/Cartes/gardien_spectral.jpg', NULL, NULL),
(6, 'Dragon du Néant', 'Monstre', 2000, 1500, NULL, 'assets/Cartes/dragon_du_neant.jpg', NULL, NULL),
(7, 'Chevalier de la Faille', 'Monstre', 1500, 1300, NULL, 'assets/Cartes/chevalier_de_la_faille.jpg', NULL, NULL),
(8, 'Roi des Profondeurs', 'Monstre', 1700, 1800, NULL, 'assets/Cartes/roi_des_profondeurs.jpg', NULL, NULL),
(9, 'Titan du Néant', 'Monstre', 1900, 1900, NULL, 'assets/Cartes/titan_du_neant.jpg', NULL, NULL),
(10, 'Seigneur du Chaos Abyssal', 'Monstre', 2100, 1600, NULL, 'assets/Cartes/seigneur_du_chaos_abyssal.jpg', NULL, NULL),
(11, 'Béhémoth des Abysses', 'Fusion', 2600, 2300, NULL, 'assets/Cartes/behemoth_des_abysses.jpg', 8, 9),
(12, 'Golem Apocalypse', 'Fusion', 3000, 2700, NULL, 'assets/Cartes/golem_apocalypse.jpg', 3, 5),
(13, 'Dragon Eclipse Infernale', 'Fusion', 3200, 2900, NULL, 'assets/Cartes/dragon_eclipse_infernale.jpg', 6, 10),
(14, 'Roi de la Destruction Totale', 'Fusion', 3400, 3000, NULL, 'assets/Cartes/roi_destruction_totale.jpg', 4, 7),
(15, 'Chaos Céleste', 'Fusion', 4000, 3500, NULL, 'assets/Cartes/chaos_celeste.jpg', 13, 14);

-- --------------------------------------------------------

--
-- Structure de la table `deck`
--

CREATE TABLE `deck` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `money` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'Kaneki', '$2y$10$UTCJ48g9/D7ZmtfzGytt3uGfLPBa6k9K5gWsVwzsr/8IuvGLOmPUy', '2025-03-11 10:17:24');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `deck`
--
ALTER TABLE `deck`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Index pour la table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `deck`
--
ALTER TABLE `deck`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `deck`
--
ALTER TABLE `deck`
  ADD CONSTRAINT `deck_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `deck_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`);

--
-- Contraintes pour la table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
