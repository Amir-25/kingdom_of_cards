-- Création de la table des cartes
CREATE TABLE cartes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    rarete ENUM('Commune', 'Rare', 'Très Rare', 'Épique', 'Légendaire', 'Mythique') NOT NULL,
    attaque INT NOT NULL,
    defense INT NOT NULL,
    effet TEXT DEFAULT NULL,
    fusionnable BOOLEAN DEFAULT FALSE,
    image_path VARCHAR(255) NOT NULL
);

-- Création de la table des fusions
CREATE TABLE fusions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_carte_resultat INT NOT NULL,
    id_carte_1 INT NOT NULL,
    id_carte_2 INT NOT NULL,
    FOREIGN KEY (id_carte_resultat) REFERENCES cartes(id),
    FOREIGN KEY (id_carte_1) REFERENCES cartes(id),
    FOREIGN KEY (id_carte_2) REFERENCES cartes(id)
);

-- Insertion des cartes normales
INSERT INTO cartes (nom, rarete, attaque, defense, effet, fusionnable, image_path)
VALUES
('Gobelin Pyromane', 'Commune', 1400, 1000, NULL, FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/1.Gobelin Pyromane.png'),
('Serpent des Sables', 'Commune', 1200, 1600, NULL, FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/2.Serpent des Sables.png'),
('Golem Mécanique', 'Commune', 1700, 800, NULL, TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/3.Golem Mécanique.png'),
('Chimère Sanglante', 'Rare', 1800, 1300, NULL, TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/4.Chimère sanglante.png'),
('Gardien Spectral', 'Rare', 1500, 2000, NULL, TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/5.Gardien Spectral.png'),
('Dragon du Néant', 'Très Rare', 2000, 1800, 'Voracité du Néant : gagne +500 ATK quand il détruit un monstre', TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/6.Dragon du Néant.png'),
('Chevalier de la Faille', 'Très Rare', 1900, 2100, 'Rupture Dimensionnelle : bannit les monstres qu’il détruit', TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/7.Chevalier de la Faille.png'),
('Roi des Profondeurs', 'Épique', 2400, 2000, 'Marée Déferlante : -300 ATK aux ennemis', TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/8.Roi des Profondeurs.png'),
('Titan du Néant', 'Épique', 2500, 2200, 'Dévoreur d’Âmes : régénère 500 PV en détruisant un monstre', TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/9.Titan du Néant.png'),
('Seigneur du Chaos Abyssal', 'Légendaire', 2800, 2500, 'Marque du Néant : si une carte marquée est détruite, son propriétaire perd 500 PV', TRUE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/10.Seigneur du Chaos Abyssal.png');

-- Insertion des cartes fusionnées
INSERT INTO cartes (nom, rarete, attaque, defense, effet, fusionnable, image_path)
VALUES
('Béhémoth des Abysses', 'Épique', 3200, 2800, 'Colère des Profondeurs : peut attaquer une 2ème fois si destruction', FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/11.Fusion 8-9.Béhémoth des Abysses.png'),
('Golem d’Apocalypse', 'Épique', 3000, 3000, 'Indestructible : revient avec 1500 DEF après destruction', FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/12.Fusion 3-5.Golem d’Apocalypse.png'),
('Dragon Éclipse Infernale', 'Légendaire', 3500, 2700, 'Flammes d’Éclipse : bannit les monstres détruits', FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/13.Fusion 6-10.Dragon Éclipse Infernale.png'),
('Roi de la Destruction Totale', 'Légendaire', 3400, 2900, 'Onde de Ruine : inflige 200 dégâts aux ennemis et 100 aux alliés', FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/14.Fusion 4-7.Roi de la Destruction Totale.png'),
('Dieu du Chaos Céleste', 'Mythique', 4500, 4000, 'Jugement du Chaos : inflige 300 dégâts par carte ennemie en jeu à chaque tour', FALSE, '/Kingdom-of-Cards/kingdom_of_cards/assets/CARTES/15.Fusion+ 13-14.Chaos Céleste.png');

-- Insertion des liens de fusion
INSERT INTO fusions (id_carte_resultat, id_carte_1, id_carte_2)
VALUES
((SELECT id FROM cartes WHERE nom = 'Béhémoth des Abysses'),
 (SELECT id FROM cartes WHERE nom = 'Roi des Profondeurs'),
 (SELECT id FROM cartes WHERE nom = 'Titan du Néant')),

((SELECT id FROM cartes WHERE nom = 'Golem d’Apocalypse'),
 (SELECT id FROM cartes WHERE nom = 'Golem Mécanique'),
 (SELECT id FROM cartes WHERE nom = 'Gardien Spectral')),

((SELECT id FROM cartes WHERE nom = 'Dragon Éclipse Infernale'),
 (SELECT id FROM cartes WHERE nom = 'Dragon du Néant'),
 (SELECT id FROM cartes WHERE nom = 'Seigneur du Chaos Abyssal')),

((SELECT id FROM cartes WHERE nom = 'Roi de la Destruction Totale'),
 (SELECT id FROM cartes WHERE nom = 'Chevalier de la Faille'),
 (SELECT id FROM cartes WHERE nom = 'Chimère Sanglante')),

((SELECT id FROM cartes WHERE nom = 'Dieu du Chaos Céleste'),
 (SELECT id FROM cartes WHERE nom = 'Dragon Éclipse Infernale'),
 (SELECT id FROM cartes WHERE nom = 'Roi de la Destruction Totale'));



-- Création de la table des cartes des joueurs
CREATE TABLE joueur_cartes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_joueur INT NOT NULL,
    id_carte INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_joueur) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_carte) REFERENCES cartes(id) ON DELETE CASCADE
);
