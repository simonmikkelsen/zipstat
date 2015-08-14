-- MySQL dump 10.10
--
-- Host: localhost    Database: zipstat
-- ------------------------------------------------------
-- Server version       5.0.18-Debian_3.dotdeb.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `zs20_cache`
--

DROP TABLE IF EXISTS `zs20_cache`;
CREATE TABLE `zs20_cache` (
  `category` char(32) NOT NULL,
  `id` char(128) NOT NULL,
  `contents` text NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `expires` datetime default NULL,
  `generating` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`category`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `zs20_main`
--

DROP TABLE IF EXISTS `zs20_main`;
CREATE TABLE `zs20_main` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(64) character set latin1 collate latin1_bin default NULL,
  `data` text character set latin1 NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_danish_ci;

--
-- Table structure for table `zs20_visits`
--

DROP TABLE IF EXISTS `zs20_visits`;
CREATE TABLE `zs20_visits` (
  `day` date NOT NULL default '0000-00-00',
  `data_type` enum('day','browser','os','color','screenres','searchEngine','searchWord','java','js','language','topdom') NOT NULL default 'day',
  `hits` bigint(20) NOT NULL default '0',
  `isUnique` tinyint(1) NOT NULL default '0',
  `value` char(128) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UK_visits` (`day`,`data_type`,`isUnique`,`value`),
  KEY `Index_visits` (`day`,`data_type`,`isUnique`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `zs20_visits_storage`
--

DROP TABLE IF EXISTS `zs20_visits_storage`;
CREATE TABLE `zs20_visits_storage` (
  `day` date NOT NULL default '0000-00-00',
  `data_type` enum('day','browser','os','color','screenres','searchEngine','searchWord','java','js','language','topdom') NOT NULL default 'day',
  `hits` bigint(20) NOT NULL default '0',
  `isUnique` tinyint(1) NOT NULL default '0',
  `value` char(128) default NULL,
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;