-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 14 avr. 2025 à 00:57
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
-- Structure de la table `cartes`
--

CREATE TABLE `cartes` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `rarete` enum('Commune','Rare','Très Rare','Épique','Légendaire','Mythique') NOT NULL,
  `attaque` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `effet` text DEFAULT NULL,
  `fusionnable` tinyint(1) DEFAULT 0,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cartes`
--

INSERT INTO `cartes` (`id`, `nom`, `rarete`, `attaque`, `defense`, `effet`, `fusionnable`, `image_path`) VALUES
(1, 'Gobelin Pyromane', 'Commune', 1400, 1000, NULL, 0, '/kingdom_of_cards/assets/Cartes/gobelin_pyromane.jpg'),
(2, 'Serpent des Sables', 'Commune', 1200, 1600, NULL, 0, '/kingdom_of_cards/assets/Cartes/serpent_des_sables.jpg'),
(3, 'Golem Mécanique', 'Commune', 1700, 800, NULL, 1, '/kingdom_of_cards/assets/Cartes/golem_mecanique.jpg'),
(4, 'Chimère Sanglante', 'Rare', 1800, 1300, NULL, 1, '/kingdom_of_cards/assets/Cartes/chimere_sanglante.jpg'),
(5, 'Gardien Spectral', 'Rare', 1500, 2000, NULL, 1, '/kingdom_of_cards/assets/Cartes/gardien_spectral.jpg'),
(6, 'Dragon du Néant', 'Très Rare', 2000, 1800, 'Voracité du Néant : gagne +500 ATK quand il détruit un monstre', 1, '/kingdom_of_cards/assets/CARTES/dragon_du_neant.jpg'),
(7, 'Chevalier de la Faille', 'Très Rare', 1900, 2100, 'Rupture Dimensionnelle : bannit les monstres qu’il détruit', 1, '/kingdom_of_cards/assets/CARTES/chevalier_de_la_faille.jpg'),
(8, 'Roi des Profondeurs', 'Épique', 2400, 2000, 'Marée Déferlante : -300 ATK aux ennemis', 1, '/kingdom_of_cards/assets/CARTES/roi_des_profondeurs.jpg'),
(9, 'Titan du Néant', 'Épique', 2500, 2200, 'Dévoreur d’Âmes : régénère 500 PV en détruisant un monstre', 1, '/kingdom_of_cards/assets/CARTES/titan_du_neant.jpg'),
(10, 'Seigneur du Chaos Abyssal', 'Légendaire', 2800, 2500, 'Marque du Néant : si une carte marquée est détruite, son propriétaire perd 500 PV', 1, '/kingdom_of_cards/assets/CARTES/seigneur_du_chaos_abyssal.jpg'),
(11, 'Béhémoth des Abysses', 'Épique', 3200, 2800, 'Colère des Profondeurs : peut attaquer une 2ème fois si destruction', 0, '/kingdom_of_cards/assets/CARTES/behemoth_des_abysses.jpg'),
(12, 'Golem d’Apocalypse', 'Épique', 3000, 3000, 'Indestructible : revient avec 1500 DEF après destruction', 0, '/kingdom_of_cards/assets/CARTES/golem_apocalypse.jpg'),
(13, 'Dragon Éclipse Infernale', 'Légendaire', 3500, 2700, 'Flammes d’Éclipse : bannit les monstres détruits', 0, '/kingdom_of_cards/assets/CARTES/dragon_eclipse_infernale.jpg'),
(14, 'Roi de la Destruction Totale', 'Légendaire', 3400, 2900, 'Onde de Ruine : inflige 200 dégâts aux ennemis et 100 aux alliés', 0, '/kingdom_of_cards/assets/CARTES/roi_destruction_totale.jpg'),
(15, 'Dieu du Chaos Céleste', 'Mythique', 4500, 4000, 'Jugement du Chaos : inflige 300 dégâts par carte ennemie en jeu à chaque tour', 0, '/kingdom_of_cards/assets/CARTES/chaos_celeste.jpg'),
(16, 'Pierre Belisle', 'Mythique', 4500, 10000, 'Pas de commentaire t’en auras pas : Reduit les points d’attaque de toutes les cartes a zero pour un tour', 0, '/kingdom_of_cards/assets/CARTES/pierre_belisle.jpg'),
(17, 'Spectre de Givre', 'Rare', 1600, 1700, NULL, 0, '/kingdom_of_cards/assets/Cartes/spectre_de_givre.jpg'),
(18, 'Samouraï d’Ombre', 'Très Rare', 2100, 1600, NULL, 0, '/kingdom_of_cards/assets/Cartes/samourai_d’ombre.jpg'),
(19, 'Héraut de l’Apocalypse', 'Épique', 2600, 2400, NULL, 0, '/kingdom_of_cards/assets/Cartes/héraut_de_l’apocalypse.jpg');

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

--
-- Déchargement des données de la table `deck`
--

INSERT INTO `deck` (`id`, `user_id`, `card_id`, `position`) VALUES
(65, 8, 9, 0),
(66, 8, 10, 1),
(67, 8, 8, 2),
(68, 8, 6, 3),
(69, 8, 7, 4),
(70, 8, 5, 5),
(71, 8, 4, 6),
(72, 8, 3, 7),
(73, 8, 2, 8),
(74, 8, 17, 9);

-- --------------------------------------------------------

--
-- Structure de la table `fusions`
--

CREATE TABLE `fusions` (
  `id` int(11) NOT NULL,
  `id_carte_resultat` int(11) NOT NULL,
  `id_carte_1` int(11) NOT NULL,
  `id_carte_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fusions`
--

INSERT INTO `fusions` (`id`, `id_carte_resultat`, `id_carte_1`, `id_carte_2`) VALUES
(1, 11, 8, 9),
(2, 12, 3, 5),
(3, 13, 6, 10),
(4, 14, 7, 4),
(5, 15, 13, 14);

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
-- Structure de la table `joueur_cartes`
--

CREATE TABLE `joueur_cartes` (
  `id` int(11) NOT NULL,
  `id_joueur` int(11) NOT NULL,
  `id_carte` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `joueur_cartes`
--

INSERT INTO `joueur_cartes` (`id`, `id_joueur`, `id_carte`, `quantite`) VALUES
(85, 2, 1, 1),
(86, 2, 2, 2),
(87, 2, 3, 1),
(88, 2, 4, 1),
(89, 2, 5, 1),
(90, 8, 1, 59),
(91, 8, 2, 57),
(92, 8, 3, 51),
(93, 8, 4, 28),
(94, 8, 5, 11),
(95, 8, 7, 9),
(96, 8, 9, 3),
(97, 8, 8, 6),
(98, 8, 6, 10),
(99, 8, 10, 1),
(100, 8, 17, 1);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `money` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `money`) VALUES
(1, 'Kaneki', '$2y$10$UTCJ48g9/D7ZmtfzGytt3uGfLPBa6k9K5gWsVwzsr/8IuvGLOmPUy', NULL, '2025-03-11 10:17:24', 13000),
(2, 'aya', '$2y$10$J48MuOUYLHnIdVIi857NiuO3qILe7UVdLRx04g17HsDsLYAdUmwme', 'aya.azizi.1@ens.etsmtl.ca', '2025-03-14 18:22:47', 150000),
(8, 'Test', '$2y$10$rn80.j.pV0WbJe1FO.3VROoXWPjL5MdgVvfNfkMva/hTHlX2vP8tS', 'jinwo2021@gmail.com', '2025-03-29 04:45:43', 609999);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cartes`
--
ALTER TABLE `cartes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `deck`
--
ALTER TABLE `deck`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Index pour la table `fusions`
--
ALTER TABLE `fusions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_carte_resultat` (`id_carte_resultat`),
  ADD KEY `id_carte_1` (`id_carte_1`),
  ADD KEY `id_carte_2` (`id_carte_2`);

--
-- Index pour la table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Index pour la table `joueur_cartes`
--
ALTER TABLE `joueur_cartes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_joueur` (`id_joueur`),
  ADD KEY `id_carte` (`id_carte`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cartes`
--
ALTER TABLE `cartes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `deck`
--
ALTER TABLE `deck`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT pour la table `fusions`
--
ALTER TABLE `fusions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `joueur_cartes`
--
ALTER TABLE `joueur_cartes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `deck`
--
ALTER TABLE `deck`
  ADD CONSTRAINT `deck_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `deck_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cartes` (`id`);

--
-- Contraintes pour la table `fusions`
--
ALTER TABLE `fusions`
  ADD CONSTRAINT `fusions_ibfk_1` FOREIGN KEY (`id_carte_resultat`) REFERENCES `cartes` (`id`),
  ADD CONSTRAINT `fusions_ibfk_2` FOREIGN KEY (`id_carte_1`) REFERENCES `cartes` (`id`),
  ADD CONSTRAINT `fusions_ibfk_3` FOREIGN KEY (`id_carte_2`) REFERENCES `cartes` (`id`);

--
-- Contraintes pour la table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cartes` (`id`);

--
-- Contraintes pour la table `joueur_cartes`
--
ALTER TABLE `joueur_cartes`
  ADD CONSTRAINT `joueur_cartes_ibfk_1` FOREIGN KEY (`id_joueur`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `joueur_cartes_ibfk_2` FOREIGN KEY (`id_carte`) REFERENCES `cartes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
