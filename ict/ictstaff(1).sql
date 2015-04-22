-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 14, 2014 at 05:50 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tams3`
--

-- --------------------------------------------------------

--
-- Table structure for table `ictstaff`
--

DROP TABLE IF EXISTS `ictstaff`;
CREATE TABLE IF NOT EXISTS `ictstaff` (
  `stfid` varchar(8) NOT NULL,
  `title` enum('Prof','Dr','Mr','Mrs','Miss') NOT NULL,
  `lname` varchar(25) NOT NULL,
  `fname` varchar(25) NOT NULL,
  `mname` varchar(25) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `addr` text,
  `sex` char(1) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `password` varchar(45) NOT NULL,
  `profile` text,
  `dob` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`stfid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ictstaff`
--

INSERT INTO `ictstaff` (`stfid`, `title`, `lname`, `fname`, `mname`, `phone`, `email`, `addr`, `sex`, `access`, `password`, `profile`, `dob`) VALUES
('ICT0001', 'Dr', 'ALABA', 'A', 'A', '0801000000', 'change@yourmail.com', 'nill', 'F', 1, 'c8b23cc3438de8680688b021de450da4', 'nill', '2014-2-26'),
('ICT0002', 'Mr', 'Femi', 'Alade', 'M', '08022202112', 'nill@ymail.c', 'addn d  ', 'M', 2, '5c8e82f0bdac09f37b0111c79f1fe0fa', 'nulll', '2001-4-4');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
