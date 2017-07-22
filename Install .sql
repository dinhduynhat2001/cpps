-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2017 at 03:23 AM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sweater`
--

-- --------------------------------------------------------

--
-- Table structure for table `puffles`
--

CREATE TABLE `puffles` (
  `ID` mediumint(8) UNSIGNED NOT NULL COMMENT 'Puffle ID',
  `Owner` int(11) UNSIGNED NOT NULL COMMENT 'Owner''s player ID',
  `Name` tinytext NOT NULL COMMENT 'Puffle name',
  `Type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Puffle type',
  `Hunger` tinyint(3) UNSIGNED NOT NULL DEFAULT '100' COMMENT 'Puffle''s hunger status',
  `Health` tinyint(3) UNSIGNED NOT NULL DEFAULT '100' COMMENT 'Puffle''s health status',
  `Rest` tinyint(3) UNSIGNED NOT NULL DEFAULT '100' COMMENT 'Puffle''s rest status',
  `Walking` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Puffle''s walking status'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Contains data regarding user puffles';

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `ID` tinyint(3) UNSIGNED NOT NULL COMMENT 'Server ID',
  `Population` smallint(5) UNSIGNED NOT NULL COMMENT 'Server''s Population'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Server statistics';

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`ID`, `Population`) VALUES
(100, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(10) NOT NULL,
  `Username` char(12) NOT NULL COMMENT 'Nickname',
  `Password` char(40) NOT NULL COMMENT 'Password hash',
  `LoginKey` char(40) DEFAULT NULL COMMENT 'Used for logging into the game sevrer',
  `Active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `Status` tinytext NOT NULL COMMENT 'Contains the player''s online status',
  `RegisteredTime` int(11) DEFAULT NULL COMMENT 'Unix timestamp',
  `Coins` int(10) UNSIGNED NOT NULL DEFAULT '10000' COMMENT 'Player coins',
  `Color` smallint(5) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Current color item',
  `Head` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current head item',
  `Face` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current face item',
  `Neck` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current neck item',
  `Body` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current body item',
  `Hand` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current hand item',
  `Feet` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current feet item',
  `Photo` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current photo item',
  `Flag` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current flag item',
  `Buddies` text NOT NULL COMMENT 'Player buddies',
  `Ignores` text NOT NULL COMMENT 'Player''s ignored list',
  `Inventory` text COMMENT 'Player inventory',
  `Igloo` tinyint(2) UNSIGNED DEFAULT NULL COMMENT 'Current igloo ID',
  `Igloos` tinytext NOT NULL COMMENT 'Player''s owned igloos',
  `Music` tinyint(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current music ID',
  `Floor` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Current flooring ID',
  `RoomFurniture` text NOT NULL COMMENT 'Igloo furniture',
  `Furniture` text NOT NULL COMMENT 'Furniture inventory',
  `Postcards` text NOT NULL COMMENT 'Player postcards',
  `Moderator` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Player''s moderator status',
  `Rank` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Player''s rank (badge when multiplied by 146)',
  `LastLogin` int(11) UNSIGNED DEFAULT NULL COMMENT 'Unix timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This is the table in which all player data is stored.';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Username`, `Password`, `LoginKey`, `Active`, `Status`, `RegisteredTime`, `Coins`, `Color`, `Head`, `Face`, `Neck`, `Body`, `Hand`, `Feet`, `Photo`, `Flag`, `Buddies`, `Ignores`, `Inventory`, `Igloo`, `Igloos`, `Music`, `Floor`, `RoomFurniture`, `Furniture`, `Postcards`, `Moderator`, `Rank`, `LastLogin`) VALUES
(1, 'Flake', '30f3f483bf961c26e2c468794830fa8e', '2491b874a13095dadceedd84042f74b4', 1, '', 0, 10000, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', NULL, 1, '', 0, 0, '', '', '', 6, 6, 1496096525);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `puffles`
--
ALTER TABLE `puffles`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `puffles`
--
ALTER TABLE `puffles`
  MODIFY `ID` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Puffle ID';
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
