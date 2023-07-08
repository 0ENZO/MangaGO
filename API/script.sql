-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : mar. 06 juin 2023 à 10:21
-- Version du serveur :  5.7.34
-- Version de PHP : 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `MangaGO`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`admin_pa`@`%` PROCEDURE `CountTotalPages` (IN `input_id` INT)  BEGIN
    DECLARE countResult INT;

SELECT COUNT(*) INTO countResult FROM PAGES WHERE ID_SCAN = input_id;

UPDATE SCAN SET TOTAL_PAGE=countResult WHERE ID = input_id;

END$$

CREATE DEFINER=`admin_pa`@`%` PROCEDURE `InsertOrUpdateBulles` (IN `p_TITRE_OEUVRE` VARCHAR(255), IN `p_TITRE_SCAN` VARCHAR(255), IN `p_NUMERO_SCAN` INT, IN `p_LAST_RELEASE` DATETIME, IN `p_NUMERO_PAGE` INT, IN `p_LIEN_IMAGE` VARCHAR(255), IN `p_TEXT_VO` TEXT, IN `p_POSITION` TEXT)  BEGIN

START TRANSACTION;

-- OEUVRE
SELECT ID INTO @oeuvre_id FROM OEUVRE WHERE TITRE = p_TITRE_OEUVRE;
IF @oeuvre_id IS NULL THEN
    INSERT INTO OEUVRE (TITRE) VALUES (p_TITRE_OEUVRE);
    SET @oeuvre_id = LAST_INSERT_ID();
END IF;

-- SCAN
SELECT ID INTO @scan_id FROM SCAN WHERE ID_OEUVRE = @oeuvre_id AND NUMERO = p_NUMERO_SCAN;
IF @scan_id IS NULL THEN
    INSERT INTO SCAN (ID_OEUVRE, TITRE, NUMERO, LAST_RELEASE)
    VALUES (@oeuvre_id, p_TITRE_SCAN, p_NUMERO_SCAN, p_LAST_RELEASE);
    SET @scan_id = LAST_INSERT_ID();
END IF;

-- PAGES
SELECT ID INTO @page_id FROM PAGES WHERE ID_SCAN = @scan_id AND NUMERO = p_NUMERO_PAGE;
IF @page_id IS NULL THEN
    INSERT INTO PAGES (ID_SCAN, NUMERO, LIEN_IMAGE)
    VALUES (@scan_id, p_NUMERO_PAGE, p_LIEN_IMAGE);
    SET @page_id = LAST_INSERT_ID();
END IF;

-- BULLES
INSERT INTO BULLES (ID_PAGE, TEXT_VO, POSITION)
VALUES (@page_id, p_TEXT_VO, p_POSITION);

COMMIT;

END$$

CREATE DEFINER=`admin_pa`@`%` PROCEDURE `maxID` (IN `Titre` VARCHAR(255), IN `Numero` VARCHAR(255))  BEGIN
    DECLARE Resultat INT;

    -- Exécuter la requête SELECT
    SELECT SCAN.ID INTO Resultat
    FROM OEUVRE
    INNER JOIN SCAN ON OEUVRE.ID = SCAN.ID_OEUVRE
    WHERE OEUVRE.TITRE = Titre AND SCAN.NUMERO = Numero
    LIMIT 1;

    -- Vérifier si le résultat est NULL
    IF Resultat IS NULL THEN
        -- Si c'est NULL, obtenir le MAX(ID) à partir de la table SCAN
        SELECT MAX(ID) + 1 INTO Resultat
        FROM SCAN;
    END IF;

    -- Afficher le résultat
    SELECT Resultat AS Resultat;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `BULLES`
--

CREATE TABLE `BULLES` (
                          `ID` int(11) NOT NULL,
                          `ID_PAGE` int(11) NOT NULL,
                          `TEXT_VO` text NOT NULL,
                          `TEXT_TRAD` text,
                          `TEXT_CO` text,
                          `POSITION` text NOT NULL,
                          `ALREADY_USED` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `BULLES`
--

INSERT INTO `BULLES` (`ID`, `ID_PAGE`, `TEXT_VO`, `TEXT_TRAD`, `TEXT_CO`, `POSITION`, `ALREADY_USED`) VALUES
    (1, 1, 'すうみちまのせ伺ん子:でがしたあぁ/4', 'Instructeur de Suumichima : Degashitaa', '', '(219, 179, 279, 439)', 0);

-- --------------------------------------------------------

--
-- Structure de la table `COMPTE`
--

CREATE TABLE `COMPTE` (
                          `ID` int(11) NOT NULL,
                          `PSEUDO` varchar(255) NOT NULL,
                          `EMAIL` varchar(255) NOT NULL,
                          `PASSWORD` varchar(255) NOT NULL,
                          `TYPE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `COMPTE`
--

INSERT INTO `COMPTE` (`ID`, `PSEUDO`, `EMAIL`, `PASSWORD`, `TYPE`) VALUES
    (1, 'fabien', 'a@a.fr', 'a', 1);

-- --------------------------------------------------------

--
-- Structure de la table `OEUVRE`
--

CREATE TABLE `OEUVRE` (
                          `ID` int(11) NOT NULL,
                          `TITRE` varchar(255) NOT NULL,
                          `AUTEUR` varchar(255) DEFAULT NULL,
                          `IMAGE_COUVERTURE` varchar(255) DEFAULT './assets/images/weekly_shonen_jump_cover.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `OEUVRE`
--

INSERT INTO `OEUVRE` (`ID`, `TITRE`, `AUTEUR`, `IMAGE_COUVERTURE`) VALUES
                                                                       (1, 'one_piece', 'Eiichirō Oda', './assets/images/one_piece_couverture.jpg'),
                                                                       (2, 'naruto', 'Masashi Kishimoto', './assets/images/naruto_couverture.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `PAGES`
--

CREATE TABLE `PAGES` (
                         `ID` int(11) NOT NULL,
                         `ID_SCAN` int(11) NOT NULL,
                         `NUMERO` varchar(255) NOT NULL,
                         `LIEN_IMAGE` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `PAGES`
--

INSERT INTO `PAGES` (`ID`, `ID_SCAN`, `NUMERO`, `LIEN_IMAGE`) VALUES
                                                                  (1, 1, '1', '/scan_brut/one_piece/1069/1.jpeg'),
                                                                  (2, 1, '2', '/scan_brut/one_piece/1069/2.jpeg'),
                                                                  (3, 1, '3', '/scan_brut/one_piece/1069/3.jpeg'),
                                                                  (4, 1, '4', '/scan_brut/one_piece/1069/4.jpeg'),
                                                                  (5, 1, '5', '/scan_brut/one_piece/1069/5.jpeg'),
                                                                  (6, 2, '1', '/scan_brut/Naruto/699/1.jpeg'),
                                                                  (7, 2, '2', '/scan_brut/Naruto/699/2.jpeg'),
                                                                  (8, 2, '3', '/scan_brut/Naruto/699/3.jpeg'),
                                                                  (9, 2, '4', '/scan_brut/Naruto/699/4.jpeg'),
                                                                  (10, 2, '5', '/scan_brut/Naruto/699/5.jpeg');

-- --------------------------------------------------------

--
-- Structure de la table `SCAN`
--

CREATE TABLE `SCAN` (
                        `ID` int(11) NOT NULL,
                        `ID_OEUVRE` int(11) NOT NULL,
                        `TITRE` varchar(255) NOT NULL,
                        `NUMERO` varchar(255) NOT NULL,
                        `TOTAL_PAGE` int(11) NOT NULL DEFAULT '20',
                        `LAST_RELEASE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `SCAN`
--

INSERT INTO `SCAN` (`ID`, `ID_OEUVRE`, `TITRE`, `NUMERO`, `TOTAL_PAGE`, `LAST_RELEASE`) VALUES
                                                                                            (1, 1, '', '1069', 5, '2023-04-02'),
                                                                                            (2, 2, '', '699', 5, '2023-04-10');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `BULLES`
--
ALTER TABLE `BULLES`
    ADD PRIMARY KEY (`ID`),
  ADD KEY `asa` (`ID_PAGE`);

--
-- Index pour la table `COMPTE`
--
ALTER TABLE `COMPTE`
    ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `OEUVRE`
--
ALTER TABLE `OEUVRE`
    ADD PRIMARY KEY (`ID`,`TITRE`);

--
-- Index pour la table `PAGES`
--
ALTER TABLE `PAGES`
    ADD PRIMARY KEY (`ID`,`ID_SCAN`,`NUMERO`),
  ADD KEY `FOREIGN KEY` (`ID_SCAN`);

--
-- Index pour la table `SCAN`
--
ALTER TABLE `SCAN`
    ADD PRIMARY KEY (`ID`,`ID_OEUVRE`,`TITRE`),
  ADD KEY `aa` (`ID_OEUVRE`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `BULLES`
--
ALTER TABLE `BULLES`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT pour la table `COMPTE`
--
ALTER TABLE `COMPTE`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT pour la table `OEUVRE`
--
ALTER TABLE `OEUVRE`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT pour la table `PAGES`
--
ALTER TABLE `PAGES`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT pour la table `SCAN`
--
ALTER TABLE `SCAN`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `BULLES`
--
ALTER TABLE `BULLES`
    ADD CONSTRAINT `asa` FOREIGN KEY (`ID_PAGE`) REFERENCES `PAGES` (`ID`);

--
-- Contraintes pour la table `PAGES`
--
ALTER TABLE `PAGES`
    ADD CONSTRAINT `FOREIGN KEY` FOREIGN KEY (`ID_SCAN`) REFERENCES `SCAN` (`ID`);

--
-- Contraintes pour la table `SCAN`
--
ALTER TABLE `SCAN`
    ADD CONSTRAINT `aa` FOREIGN KEY (`ID_OEUVRE`) REFERENCES `OEUVRE` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
