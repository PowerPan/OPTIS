-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.5.32 - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Datenbank Struktur für display
DROP DATABASE IF EXISTS `display`;
CREATE DATABASE IF NOT EXISTS `display` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `display`;


-- Exportiere Struktur von Tabelle display.departures
DROP TABLE IF EXISTS `departures`;
CREATE TABLE IF NOT EXISTS `departures` (
  `date` datetime NOT NULL,
  `destination` varchar(50) NOT NULL,
  `route` varchar(15) NOT NULL,
  `infotext` varchar(500) DEFAULT NULL,
  `trip_id` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle display.departures: 0 rows
/*!40000 ALTER TABLE `departures` DISABLE KEYS */;
/*!40000 ALTER TABLE `departures` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle display.notification
DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `notification_id` int(11) unsigned NOT NULL,
  `text` varchar(250) COLLATE latin1_general_ci DEFAULT NULL,
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle display.notification: 0 rows
/*!40000 ALTER TABLE `notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle display.settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `stopname` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `textsize` int(10) NOT NULL,
  `start` int(10) NOT NULL,
  `scrollamount` int(10) NOT NULL,
  `stop_ids` varchar(100) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle display.settings: 1 rows
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`stopname`, `textsize`, `start`, `scrollamount`, `stop_ids`) VALUES
	('Bla', 50, 1, 4, '12240');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
