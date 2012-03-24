-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 13, 2012 at 01:31 AM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vidsearch`
--

-- --------------------------------------------------------

--
-- Table structure for table `vs_actors`
--

CREATE TABLE IF NOT EXISTS `vs_actors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `actor_name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `actor_name` (`actor_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vs_actors`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_genre`
--

CREATE TABLE IF NOT EXISTS `vs_genre` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `genre` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `genre` (`genre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vs_genre`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_links`
--

CREATE TABLE IF NOT EXISTS `vs_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `movie_id` bigint(20) unsigned NOT NULL,
  `link_url` varchar(200) NOT NULL,
  `report_count` int(10) unsigned DEFAULT '0',
  `not_found` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`link_id`),
  KEY `link_url` (`link_url`),
  KEY `movie_id` (`movie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vs_links`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_movies`
--

CREATE TABLE IF NOT EXISTS `vs_movies` (
  `movie_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `movie_name` varchar(120) NOT NULL,
  `movie_channel_link` text NOT NULL,
  `movie_release_date` datetime NOT NULL,
  `movie_release_countries` text NOT NULL,
  PRIMARY KEY (`movie_id`),
  KEY `movie_name` (`movie_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vs_movies`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_movies_actors`
--

CREATE TABLE IF NOT EXISTS `vs_movies_actors` (
  `movie_id` bigint(20) NOT NULL,
  `actor_id` bigint(20) NOT NULL,
  PRIMARY KEY (`movie_id`,`actor_id`),
  KEY `actor_id` (`actor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vs_movies_actors`
--


-- --------------------------------------------------------

--
-- Table structure for table `vs_movies_genre`
--

CREATE TABLE IF NOT EXISTS `vs_movies_genre` (
  `movie_id` bigint(20) NOT NULL,
  `genre_id` bigint(20) NOT NULL,
  PRIMARY KEY (`movie_id`,`genre_id`),
  KEY `genre_id` (`genre_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vs_movies_genre`
--

