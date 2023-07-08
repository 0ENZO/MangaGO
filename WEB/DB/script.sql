-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2023 at 06:11 PM
-- Server version: 5.7.24
-- PHP Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `MangaGO`
--

-- --------------------------------------------------------

--
-- Table structure for table `compte`
--

CREATE TABLE `compte` (
  `ID` int(11) NOT NULL,
  `PSEUDO` varchar(255) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `TYPE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `compte`
--

INSERT INTO `compte` (`ID`, `PSEUDO`, `EMAIL`, `PASSWORD`, `TYPE`) VALUES
(1, 'fabien', 'fabien@fabien.fr', 'fabien', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oeuvre`
--

CREATE TABLE `oeuvre` (
  `ID` int(11) NOT NULL,
  `TITRE` varchar(255) NOT NULL,
  `AUTEUR` varchar(255) NOT NULL,
  `IMAGE_COUVERTURE` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oeuvre`
--

INSERT INTO `oeuvre` (`ID`, `TITRE`, `AUTEUR`, `IMAGE_COUVERTURE`) VALUES
(1, 'One Piece', 'Eiichirō Oda', './assets/images/one_piece_couverture.jpg'),
(2, 'Naruto', 'Masashi Kishimoto', './assets/images/naruto_couverture.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `page_bulles`
--

CREATE TABLE `page_bulles` (
  `ID` int(11) NOT NULL,
  `ID_PAGE` int(11) NOT NULL,
  `TEXT_VO` text NOT NULL,
  `TEXT_TRAD` text NOT NULL,
  `TEXT_CO` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page_bulles`
--

INSERT INTO `page_bulles` (`ID`, `ID_PAGE`, `TEXT_VO`, `TEXT_TRAD`, `TEXT_CO`) VALUES
(1, 1, 'JDHS', 'HELLO', '');

-- --------------------------------------------------------

--
-- Table structure for table `scan`
--

CREATE TABLE `scan` (
  `ID` int(11) NOT NULL,
  `ID_OEUVRE` int(11) NOT NULL,
  `TITRE` varchar(255) NOT NULL,
  `NUMERO` varchar(255) NOT NULL,
  `TOTAL_PAGE` int(11) NOT NULL,
  `LAST_RELEASE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `scan`
--

INSERT INTO `scan` (`ID`, `ID_OEUVRE`, `TITRE`, `NUMERO`, `TOTAL_PAGE`, `LAST_RELEASE`) VALUES
(1, 1, '', '1069', 5, '2023-04-02'),
(2, 2, '', '699', 21, '2023-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `scan_pages`
--

CREATE TABLE `scan_pages` (
  `ID` int(11) NOT NULL,
  `ID_SCAN` int(11) NOT NULL,
  `NUMERO` varchar(255) NOT NULL,
  `LIEN_IMAGE` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `scan_pages`
--

INSERT INTO `scan_pages` (`ID`, `ID_SCAN`, `NUMERO`, `LIEN_IMAGE`) VALUES
(1, 1, '1', 'https://raw.senmanga.com/viewer/One_Piece/1069/1'),
(2, 1, '2', 'https://raw.senmanga.com/viewer/One_Piece/1069/2'),
(3, 1, '3', 'https://raw.senmanga.com/viewer/One_Piece/1069/3'),
(4, 1, '4', 'https://raw.senmanga.com/viewer/One_Piece/1069/4'),
(5, 1, '5', 'https://raw.senmanga.com/viewer/One_Piece/1069/5'),
(6, 2, '1', 'https://raw.senmanga.com/viewer/Naruto/699/1'),
(7, 2, '2', 'https://raw.senmanga.com/viewer/Naruto/699/2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `compte`
--
ALTER TABLE `compte`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `oeuvre`
--
ALTER TABLE `oeuvre`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `page_bulles`
--
ALTER TABLE `page_bulles`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `asa` (`ID_PAGE`);

--
-- Indexes for table `scan`
--
ALTER TABLE `scan`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `aa` (`ID_OEUVRE`);

--
-- Indexes for table `scan_pages`
--
ALTER TABLE `scan_pages`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FOREIGN KEY` (`ID_SCAN`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `compte`
--
ALTER TABLE `compte`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `oeuvre`
--
ALTER TABLE `oeuvre`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `page_bulles`
--
ALTER TABLE `page_bulles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scan`
--
ALTER TABLE `scan`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `scan_pages`
--
ALTER TABLE `scan_pages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `page_bulles`
--
ALTER TABLE `page_bulles`
  ADD CONSTRAINT `asa` FOREIGN KEY (`ID_PAGE`) REFERENCES `scan_pages` (`ID`);

--
-- Constraints for table `scan`
--
ALTER TABLE `scan`
  ADD CONSTRAINT `aa` FOREIGN KEY (`ID_OEUVRE`) REFERENCES `oeuvre` (`ID`);

--
-- Constraints for table `scan_pages`
--
ALTER TABLE `scan_pages`
  ADD CONSTRAINT `FOREIGN KEY` FOREIGN KEY (`ID_SCAN`) REFERENCES `scan` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
