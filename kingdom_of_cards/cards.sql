CREATE TABLE cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type ENUM('Monstre', 'Fusion') NOT NULL,
    attack INT NOT NULL,
    defense INT NOT NULL,
    ability TEXT DEFAULT NULL,
    image VARCHAR(255) NOT NULL,
    fusion_card_1 INT DEFAULT NULL,  -- ID de la première carte nécessaire pour fusion
    fusion_card_2 INT DEFAULT NULL -- ID de la carte obtenue après fusion
);

INSERT INTO cards (name, type, attack, defense, image) VALUES
('Gobelin Pyromane', 'Monstre', 1200, 800, 'assets/Cartes/gobelin_pyromane.jpg'),
('Serpent des Sables', 'Monstre', 1000, 900, 'assets/Cartes/serpent_des_sables.jpg'),
('Golem Mécanique', 'Monstre', 1600, 2000, 'assets/Cartes/golem_mecanique.jpg'),
('Chimère sanglante', 'Monstre', 1800, 1200, 'assets/Cartes/chimere_sanglante.jpg'),
('Gardien Spectral', 'Monstre', 1400, 1700, 'assets/Cartes/gardien_spectral.jpg'),
('Dragon du Néant', 'Monstre', 2000, 1500, 'assets/Cartes/dragon_du_neant.jpg'),
('Chevalier de la Faille', 'Monstre', 1500, 1300, 'assets/Cartes/chevalier_de_la_faille.jpg'),
('Roi des Profondeurs', 'Monstre', 1700, 1800, 'assets/Cartes/roi_des_profondeurs.jpg'),
('Titan du Néant', 'Monstre', 1900, 1900, 'assets/Cartes/titan_du_neant.jpg'),
('Seigneur du Chaos Abyssal', 'Monstre', 2100, 1600, 'assets/Cartes/seigneur_du_chaos_abyssal.jpg');

INSERT INTO cards (name, type, attack, defense, image, fusion_card_1, fusion_card_2) VALUES
('Béhémoth des Abysses', 'Fusion', 2600, 2300, 'assets/Cartes/behemoth_des_abysses.jpg', 8, 9),
('Golem Apocalypse', 'Fusion', 3000, 2700, 'assets/Cartes/golem_apocalypse.jpg', 3, 5),
('Dragon Eclipse Infernale', 'Fusion', 3200, 2900, 'assets/Cartes/dragon_eclipse_infernale.jpg', 6, 10),
('Roi de la Destruction Totale', 'Fusion', 3400, 3000, 'assets/Cartes/roi_destruction_totale.jpg', 4, 7),
('Chaos Céleste', 'Fusion', 4000, 3500, 'assets/Cartes/chaos_celeste.jpg', 13, 14);

