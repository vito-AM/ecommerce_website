-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 13 déc. 2024 à 10:07
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
-- Base de données : `chrono_co`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id_art` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` float NOT NULL,
  `url_photo` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ID_STRIPE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id_art`, `nom`, `quantite`, `prix`, `url_photo`, `description`, `ID_STRIPE`) VALUES
(1, 'Montre Santos de Cartier', 0, 7650, '../images/Santos_de_Cartier.png', 'Montre Santos de Cartier, moyen modèle, mouvement mécanique à remontage automatique 1847 MC. Boîte en acier, couronne à 7 pans ornée d\'un spinelle bleu de synthèse facetté, cadran vert dégradé , aiguilles en forme de glaive en acier poli et matière luminescente, glace saphir. Bracelet acier avec système de mise à taille « Smartlink ». Deuxième bracelet en alligator vert, avec boucle déployante interchangeable en acier. Les deux bracelets sont équipés du système d\'interchangeabilité QuickSwitch. Largeur de la boîte : 35,1 mm, épaisseur : 8,83 mm. Etanche jusqu\'à 10 bars (~100 mètres).', 'price_1QQU4PJQlT88Kz0tFMbCMTWN'),
(2, 'Montre Tank Américaine', 4, 6750, '../images/Tank_Américaine.png', 'Montre Tank Américaine, grand modèle, mouvement mécanique à remontage automatique. Boîte en acier, couronne à pans ornée d\'un spinelle bleu de synthèse facetté. Cadran argenté satiné, aiguilles en forme de glaive en acier bleui, glace saphir. Bracelet en alligator semi-mat bleu marine, boucle déployante interchangeable en acier. Taille de la boîte : 44,4 mm x 24,4 mm, épaisseur : 8,6 mm. Étanche à 3 bars (~30 mètres).', 'price_1QQU53JQlT88Kz0tCz7EilAq'),
(3, 'Montre Ballon Bleu de Cartier', 6, 32000, '../images/Ballon_Bleu_de_Cartier.png', 'Montre Ballon Bleu de Cartier, 40 mm, mouvement mécanique à remontage automatique 1847 MC. Boîte en or rose 750/1000 sertie de 52 diamants taille brillant pour un total de 1,06 carat, couronne cannelée ornée d\'un saphir cabochon, cadran argenté soleillé, aiguilles en forme de glaive en acier bleui, glace saphir, bracelet en alligator bordeaux, boucle ardillon en or rose 750/1000, quantième à guichet à 3 heures. Épaisseur : 12,4 mm. Étanche jusqu\'à 3 bar (~30 mètres).', 'price_1QQU5xJQlT88Kz0t0LPFGCLg');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse` text DEFAULT NULL,
  `numero` varchar(15) DEFAULT NULL,
  `mail` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `ID_STRIPE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `adresse`, `numero`, `mail`, `mdp`, `ID_STRIPE`) VALUES
(58, 'compte', 'b', '12 rue compte b', '0714131211', 'compte.b@mail.com', '$2y$10$dah/jIAl8OdmEzVuZahSiepEMxA3B0kNnk7xiXR6Cee9txeszlaGS', 'cus_RO1V6RMKOWDlwg'),
(71, 'compte', 'a', '12 rue compte a', '0711121314', 'compte.a@mail.com', '$2y$10$WX9hZNa4kEj5lXLvhwaDE.ecDRGlnr3ItYB1GenpjSINj9k8zh0GK', 'cus_RO3GU48bin8JjI');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `id_art` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `envoi` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_art`, `id_client`, `quantite`, `envoi`) VALUES
(51, 2, 71, 1, 0),
(52, 3, 71, 1, 0),
(53, 1, 71, 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `message` varchar(256) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_art`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_art` (`id_art`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_art`) REFERENCES `articles` (`id_art`),
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`);

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
