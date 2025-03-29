-- Kingdom of Cards - Version Fusionnée
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- TABLES
-- --------------------------------------------------------

-- 1. Table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `money` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Table `cartes`
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

-- 3. Table `fusions`
CREATE TABLE `fusions` (
  `id` int(11) NOT NULL,
  `id_carte_resultat` int(11) NOT NULL,
  `id_carte_1` int(11) NOT NULL,
  `id_carte_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Table `joueur_cartes`
CREATE TABLE `joueur_cartes` (
  `id` int(11) NOT NULL,
  `id_joueur` int(11) NOT NULL,
  `id_carte` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Table `deck`
CREATE TABLE `deck` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Table `inventory`
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Table `password_resets`
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- DONNÉES
-- --------------------------------------------------------

-- Utilisateurs
INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `money`, `email`) VALUES
(1, 'Kaneki', '$2y$10$UTCJ48g9/D7ZmtfzGytt3uGfLPBa6k9K5gWsVwzsr/8IuvGLOmPUy', '2025-03-11 10:17:24', 13000, NULL),
(2, 'aya', '$2y$10$J48MuOUYLHnIdVIi857NiuO3qILe7UVdLRx04g17HsDsLYAdUmwme', '2025-03-14 18:22:47', 150000, 'aya.azizi.1@ens.etsmtl.ca');

-- Cartes
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
(11, 'Béhémoth des Abysses', 'Épique', 3200, 2800, 'Colère des Profondeurs : peut attaquer une 2ème fois si destruction', 0, '/kingdom_of_cards/assets/CARTES/11.Fusion 8-9.Béhémoth des Abysses.png'),
(12, 'Golem d’Apocalypse', 'Épique', 3000, 3000, 'Indestructible : revient avec 1500 DEF après destruction', 0, '/kingdom_of_cards/assets/CARTES/12.Fusion 3-5.Golem d’Apocalypse.png'),
(13, 'Dragon Éclipse Infernale', 'Légendaire', 3500, 2700, 'Flammes d’Éclipse : bannit les monstres détruits', 0, '/kingdom_of_cards/assets/CARTES/13.Fusion 6-10.Dragon Éclipse Infernale.png'),
(14, 'Roi de la Destruction Totale', 'Légendaire', 3400, 2900, 'Onde de Ruine : inflige 200 dégâts aux ennemis et 100 aux alliés', 0, '/kingdom_of_cards/assets/CARTES/14.Fusion 4-7.Roi de la Destruction Totale.png'),
(15, 'Dieu du Chaos Céleste', 'Mythique', 4500, 4000, 'Jugement du Chaos : inflige 300 dégâts par carte ennemie en jeu à chaque tour', 0, '/kingdom_of_cards/assets/CARTES/15.Fusion+ 13-14.Chaos Céleste.png');

-- Fusions
INSERT INTO `fusions` (`id`, `id_carte_resultat`, `id_carte_1`, `id_carte_2`) VALUES
(1, 11, 8, 9),
(2, 12, 3, 5),
(3, 13, 6, 10),
(4, 14, 7, 4),
(5, 15, 13, 14);

-- Cartes des joueurs
INSERT INTO `joueur_cartes` (`id`, `id_joueur`, `id_carte`, `quantite`) VALUES
(85, 2, 1, 1),
(86, 2, 2, 2),
(87, 2, 3, 1),
(88, 2, 4, 1),
(89, 2, 5, 1);

-- --------------------------------------------------------
-- INDEXES
-- --------------------------------------------------------

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `cartes` ADD PRIMARY KEY (`id`);
ALTER TABLE `fusions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_carte_resultat` (`id_carte_resultat`),
  ADD KEY `id_carte_1` (`id_carte_1`),
  ADD KEY `id_carte_2` (`id_carte_2`);

ALTER TABLE `joueur_cartes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_joueur` (`id_joueur`),
  ADD KEY `id_carte` (`id_carte`);

ALTER TABLE `deck`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT,
ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `card_id` (`card_id`);

ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `card_id` (`card_id`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `cartes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `fusions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `joueur_cartes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

-- --------------------------------------------------------
-- CONTRAINTES
-- --------------------------------------------------------

ALTER TABLE `fusions`
  ADD CONSTRAINT `fusions_ibfk_1` FOREIGN KEY (`id_carte_resultat`) REFERENCES `cartes` (`id`),
  ADD CONSTRAINT `fusions_ibfk_2` FOREIGN KEY (`id_carte_1`) REFERENCES `cartes` (`id`),
  ADD CONSTRAINT `fusions_ibfk_3` FOREIGN KEY (`id_carte_2`) REFERENCES `cartes` (`id`);

ALTER TABLE `joueur_cartes`
  ADD CONSTRAINT `joueur_cartes_ibfk_1` FOREIGN KEY (`id_joueur`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `joueur_cartes_ibfk_2` FOREIGN KEY (`id_carte`) REFERENCES `cartes` (`id`) ON DELETE CASCADE;

ALTER TABLE `deck`
  ADD CONSTRAINT `deck_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `deck_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cartes` (`id`);

ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cartes` (`id`);

COMMIT;