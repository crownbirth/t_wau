-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2014 at 03:17 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tamswauu`
--

-- --------------------------------------------------------

--
-- Table structure for table `accfee_transactions`
--

CREATE TABLE IF NOT EXISTS `accfee_transactions` (
  `matric_no` varchar(20) NOT NULL,
  `can_no` varchar(11) DEFAULT NULL,
  `can_name` varchar(100) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `amt` text,
  `resp_code` varchar(4) DEFAULT NULL,
  `resp_desc` text NOT NULL,
  `ordid` varchar(20) NOT NULL,
  `auth_code` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `pan` varchar(20) NOT NULL,
  `status` text NOT NULL,
  `name` text NOT NULL,
  `date_time` varchar(40) NOT NULL,
  `sessionid` varchar(500) DEFAULT NULL,
  `gatewayurl` varchar(500) DEFAULT NULL,
  `xml` text NOT NULL,
  `admission_type` varchar(20) NOT NULL,
  `percentPaid` int(3) NOT NULL,
  `balance` int(7) DEFAULT NULL,
  UNIQUE KEY `reference` (`reference`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE IF NOT EXISTS `appointment` (
  `appid` int(11) NOT NULL AUTO_INCREMENT,
  `lectid` char(8) NOT NULL,
  `postid` tinyint(2) NOT NULL,
  `sdate` int(3) NOT NULL,
  `edate` int(3) DEFAULT NULL,
  `remark` text,
  PRIMARY KEY (`appid`),
  KEY `fkposition_idx` (`postid`),
  KEY `fllecturer_idx` (`lectid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE IF NOT EXISTS `audit_log` (
  `audit` int(11) NOT NULL AUTO_INCREMENT,
  `initiator` varchar(8) NOT NULL,
  `entityid` varchar(11) NOT NULL,
  `entitytype` enum('student','lecturer','ictstaff','payment','prospective','cepep') NOT NULL,
  `action` enum('create','edit') NOT NULL,
  `content` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('failed','suceeded') NOT NULL DEFAULT 'suceeded',
  PRIMARY KEY (`audit`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1159 ;

-- --------------------------------------------------------

--
-- Table structure for table `cadre`
--

CREATE TABLE IF NOT EXISTS `cadre` (
  `cdrid` int(2) NOT NULL AUTO_INCREMENT,
  `cdrname` varchar(50) NOT NULL,
  PRIMARY KEY (`cdrid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `calid` int(11) NOT NULL AUTO_INCREMENT,
  `caltitle` text NOT NULL,
  `calbody` text NOT NULL,
  PRIMARY KEY (`calid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `card`
--

CREATE TABLE IF NOT EXISTS `card` (
  `crdid` int(10) NOT NULL,
  `pin` varchar(8) NOT NULL,
  `amount` smallint(4) NOT NULL,
  `status` enum('Unused','Used') NOT NULL,
  `useby` char(11) DEFAULT NULL,
  `gdate` date NOT NULL,
  `udate` date DEFAULT NULL,
  PRIMARY KEY (`crdid`),
  UNIQUE KEY `useby_UNIQUE` (`useby`),
  KEY `useby_idx` (`useby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `catid` tinyint(2) NOT NULL,
  `catname` varchar(100) NOT NULL,
  `type` char(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `college`
--

CREATE TABLE IF NOT EXISTS `college` (
  `colid` int(2) NOT NULL AUTO_INCREMENT,
  `colname` varchar(255) NOT NULL,
  `colcode` char(2) NOT NULL,
  `coltitle` varchar(10) DEFAULT NULL,
  `remark` text,
  `page_up` text,
  `page_down` text,
  `special` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`colid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `csid` char(7) NOT NULL,
  `csname` varchar(255) NOT NULL,
  `semester` char(1) NOT NULL,
  `type` varchar(20) NOT NULL,
  `catid` tinyint(2) NOT NULL,
  `deptid` int(3) NOT NULL,
  `cscont` text,
  `unit` tinyint(1) NOT NULL,
  `status` enum('Compulsory','Elective','Required') NOT NULL,
  `level` tinyint(1) NOT NULL,
  PRIMARY KEY (`csid`),
  KEY `catid_idx` (`catid`),
  KEY `deptid_idx` (`deptid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `deptid` int(3) NOT NULL AUTO_INCREMENT,
  `deptname` varchar(255) NOT NULL,
  `deptcode` char(2) DEFAULT NULL,
  `colid` int(2) NOT NULL,
  `remark` text,
  `page_up` text,
  `page_down` text,
  PRIMARY KEY (`deptid`),
  KEY `colid_idx` (`colid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `department_course`
--

CREATE TABLE IF NOT EXISTS `department_course` (
  `progid` int(3) NOT NULL DEFAULT '0',
  `deptid` int(3) DEFAULT NULL,
  `csid` char(7) NOT NULL,
  `status` enum('Compulsory','Elective','Required') NOT NULL,
  `unit` int(1) NOT NULL,
  `level` int(1) NOT NULL,
  PRIMARY KEY (`progid`,`csid`),
  KEY `fkprog_idx` (`progid`),
  KEY `fkcourse_idx` (`csid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `disciplinary`
--

CREATE TABLE IF NOT EXISTS `disciplinary` (
  `disid` int(6) NOT NULL AUTO_INCREMENT,
  `sesid` int(3) NOT NULL,
  `stdid` varchar(11) NOT NULL,
  `status` enum('Suspended','Withdrawn') NOT NULL,
  `tearm` varchar(20) NOT NULL,
  `logout` varchar(16) NOT NULL,
  `login` varchar(16) NOT NULL,
  PRIMARY KEY (`disid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

CREATE TABLE IF NOT EXISTS `grade` (
  `grdid` int(2) NOT NULL AUTO_INCREMENT,
  `grdname` varchar(2) NOT NULL,
  PRIMARY KEY (`grdid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `grade_exceptions`
--

CREATE TABLE IF NOT EXISTS `grade_exceptions` (
  `expid` int(11) NOT NULL AUTO_INCREMENT,
  `csid` char(7) CHARACTER SET utf8 NOT NULL,
  `sesid` int(11) NOT NULL,
  `unitid` int(11) NOT NULL,
  `passmark` tinyint(2) NOT NULL,
  `type` enum('College','Department') NOT NULL,
  PRIMARY KEY (`expid`),
  KEY `sesid` (`sesid`),
  KEY `unitid` (`unitid`),
  KEY `csid` (`csid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `grading`
--

CREATE TABLE IF NOT EXISTS `grading` (
  `gradid` int(11) NOT NULL AUTO_INCREMENT,
  `sesid` int(2) NOT NULL,
  `colid` int(2) NOT NULL,
  `gradeA` tinyint(2) NOT NULL,
  `gradeB` tinyint(2) NOT NULL,
  `gradeC` tinyint(2) NOT NULL,
  `gradeD` tinyint(2) NOT NULL,
  `gradeE` tinyint(2) NOT NULL,
  `gradeF` tinyint(2) NOT NULL,
  `passmark` tinyint(2) DEFAULT '40',
  PRIMARY KEY (`gradid`),
  KEY `colid` (`colid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `graduation_condition`
--

CREATE TABLE IF NOT EXISTS `graduation_condition` (
  `sesid` int(3) NOT NULL,
  `colid` int(3) NOT NULL,
  `catid` tinyint(2) NOT NULL,
  `gtnumin` tinyint(2) DEFAULT NULL,
  `gtnumax` tinyint(2) DEFAULT NULL,
  UNIQUE KEY `sesid_UNIQUE` (`sesid`),
  UNIQUE KEY `colid_UNIQUE` (`colid`),
  UNIQUE KEY `catid_UNIQUE` (`catid`),
  KEY `sesid_idx` (`sesid`),
  KEY `colid_idx` (`colid`),
  KEY `catid_idx` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ictstaff`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE IF NOT EXISTS `lecturer` (
  `lectid` char(8) NOT NULL,
  `title` enum('Prof','Dr','Mr','Mrs','Miss') NOT NULL,
  `lname` varchar(25) NOT NULL,
  `fname` varchar(25) NOT NULL,
  `mname` varchar(25) DEFAULT NULL,
  `deptid` int(3) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `addr` text,
  `sex` char(1) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `password` varchar(45) NOT NULL,
  `profile` text,
  `dob` varchar(10) DEFAULT NULL,
  `cdrid` int(2) DEFAULT NULL,
  `salary` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`lectid`),
  KEY `deptid_idx` (`deptid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `msgid` bigint(11) NOT NULL AUTO_INCREMENT,
  `sndid` varchar(11) NOT NULL,
  `rcvid` varchar(11) NOT NULL,
  `body` text NOT NULL,
  `status` enum('Read','Unread') NOT NULL,
  `date` varchar(15) NOT NULL,
  `subject` varchar(30) NOT NULL,
  PRIMARY KEY (`msgid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` text NOT NULL,
  `title` text NOT NULL,
  `article` text NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `olevel`
--

CREATE TABLE IF NOT EXISTS `olevel` (
  `olevelid` int(11) NOT NULL AUTO_INCREMENT,
  `jambregid` varchar(12) DEFAULT NULL,
  `examtype` varchar(20) NOT NULL,
  `examyear` int(4) NOT NULL,
  `examnumber` varchar(11) NOT NULL,
  `sitting` enum('first','second') NOT NULL DEFAULT 'first',
  PRIMARY KEY (`olevelid`),
  KEY `pstdid` (`jambregid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4378 ;

-- --------------------------------------------------------

--
-- Table structure for table `olevelresult`
--

CREATE TABLE IF NOT EXISTS `olevelresult` (
  `resultid` int(11) NOT NULL AUTO_INCREMENT,
  `olevelid` int(11) NOT NULL,
  `subject` int(4) NOT NULL,
  `grade` int(2) NOT NULL,
  PRIMARY KEY (`resultid`),
  KEY `olevelid` (`olevelid`),
  KEY `subject` (`subject`),
  KEY `grade` (`grade`),
  KEY `grade_2` (`grade`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34481 ;

-- --------------------------------------------------------

--
-- Table structure for table `olevelverifee_transactions`
--

CREATE TABLE IF NOT EXISTS `olevelverifee_transactions` (
  `matric_no` varchar(20) DEFAULT NULL,
  `can_no` varchar(11) DEFAULT NULL,
  `can_name` varchar(100) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `amt` text,
  `resp_code` varchar(4) DEFAULT NULL,
  `resp_desc` text NOT NULL,
  `ordid` varchar(20) NOT NULL,
  `auth_code` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `pan` varchar(20) NOT NULL,
  `status` text NOT NULL,
  `name` text NOT NULL,
  `date_time` varchar(40) NOT NULL,
  `sessionid` varchar(500) DEFAULT NULL,
  `gatewayurl` varchar(500) DEFAULT NULL,
  `xml` text NOT NULL,
  `admission_type` varchar(20) NOT NULL,
  `percentPaid` int(3) NOT NULL,
  `balance` int(7) DEFAULT NULL,
  `pay_used` enum('Yes','No') DEFAULT 'No',
  `card_submit` enum('Yes','No') NOT NULL DEFAULT 'No',
  UNIQUE KEY `reference` (`reference`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `olevel_veri_data`
--

CREATE TABLE IF NOT EXISTS `olevel_veri_data` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `stdid` varchar(11) NOT NULL,
  `exam_type` varchar(25) NOT NULL,
  `exam_year` int(4) NOT NULL,
  `exam_no` varchar(10) NOT NULL,
  `card_no` varchar(15) NOT NULL,
  `card_pin` varchar(15) NOT NULL,
  `approve` enum('Yes','No') NOT NULL DEFAULT 'No',
  `treated` enum('Yes','No') DEFAULT 'No',
  `date` varchar(10) DEFAULT NULL,
  `return_msg` text,
  `ordid` varchar(20) DEFAULT NULL,
  `date_treated` varchar(10) DEFAULT NULL,
  `who` varchar(8) DEFAULT NULL,
  `level` varchar(5) DEFAULT NULL,
  `sesid` int(3) DEFAULT NULL,
  `progid` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=606 ;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE IF NOT EXISTS `password_reset` (
  `resetid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lectid` varchar(8) DEFAULT NULL,
  `stdid` varchar(11) DEFAULT NULL,
  `pstdid` bigint(11) DEFAULT NULL,
  `param` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`resetid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=830 ;

-- --------------------------------------------------------

--
-- Table structure for table `payhistory`
--

CREATE TABLE IF NOT EXISTS `payhistory` (
  `stdid` bigint(11) NOT NULL,
  `sesid` int(3) NOT NULL,
  `amount` double NOT NULL,
  `status` varchar(4) NOT NULL,
  UNIQUE KEY `stdid_2` (`stdid`),
  KEY `stdid` (`stdid`,`sesid`),
  KEY `sesid` (`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payschedule`
--

CREATE TABLE IF NOT EXISTS `payschedule` (
  `sesid` int(3) NOT NULL,
  `level` enum('0','1','2','3','4','5','6','7','8') NOT NULL DEFAULT '0',
  `amount` double NOT NULL,
  `minpay` double NOT NULL,
  KEY `sesid` (`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE IF NOT EXISTS `position` (
  `postid` tinyint(2) NOT NULL,
  `postname` varchar(30) NOT NULL,
  PRIMARY KEY (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `programme`
--

CREATE TABLE IF NOT EXISTS `programme` (
  `progid` int(3) NOT NULL AUTO_INCREMENT,
  `progname` varchar(255) NOT NULL,
  `deptid` int(3) NOT NULL,
  `duration` tinyint(1) NOT NULL,
  `progcode` char(2) NOT NULL,
  `remark` text,
  `page_up` text,
  `page_down` text,
  `continued` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`progid`),
  KEY `deptid` (`deptid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Table structure for table `prospective`
--

CREATE TABLE IF NOT EXISTS `prospective` (
  `pstdid` bigint(11) NOT NULL AUTO_INCREMENT,
  `formnum` varchar(20) DEFAULT NULL,
  `lname` varchar(30) DEFAULT NULL,
  `fname` varchar(25) DEFAULT NULL,
  `mname` varchar(25) DEFAULT NULL,
  `jambregid` varchar(11) DEFAULT NULL,
  `jambyear` varchar(4) DEFAULT NULL,
  `jambsubj1` int(2) DEFAULT '-1',
  `jambsubj2` int(2) DEFAULT NULL,
  `jambsubj3` int(2) DEFAULT NULL,
  `jambsubj4` int(2) DEFAULT NULL,
  `jambscore1` int(3) DEFAULT NULL,
  `jambscore2` int(3) DEFAULT NULL,
  `jambscore3` int(3) DEFAULT NULL,
  `jambscore4` int(3) DEFAULT NULL,
  `deschname` varchar(300) DEFAULT NULL,
  `degradyear` varchar(4) DEFAULT NULL,
  `degrade` int(1) DEFAULT NULL,
  `Sex` varchar(6) NOT NULL,
  `DoB` varchar(10) NOT NULL,
  `stid` int(3) NOT NULL,
  `healthStatus` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `Religion` varchar(15) NOT NULL,
  `examsitnum` int(4) DEFAULT NULL,
  `email` varchar(30) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `progid1` int(2) DEFAULT NULL,
  `progid2` int(2) DEFAULT NULL,
  `progofferd` int(2) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `score` int(3) DEFAULT NULL,
  `adminstatus` enum('Yes','No') NOT NULL DEFAULT 'No',
  `sesid` int(3) DEFAULT NULL,
  `admtype` enum('UTME','DE') DEFAULT NULL,
  `formsubmit` enum('Yes','No') NOT NULL DEFAULT 'No',
  `formpayment` enum('Yes','No') NOT NULL DEFAULT 'No',
  `acceptance` enum('Yes','No') NOT NULL DEFAULT 'No',
  `lga` varchar(30) DEFAULT 'Null',
  `sponsorname` varchar(50) DEFAULT NULL,
  `sponsorphn` varchar(11) DEFAULT NULL,
  `sponsoradrs` text,
  `access` tinyint(1) NOT NULL DEFAULT '11',
  `regtype` enum('regular','coi') DEFAULT 'regular',
  `batch` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`pstdid`),
  KEY `sesid` (`sesid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4467 ;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE IF NOT EXISTS `registration` (
  `stdid` char(11) NOT NULL,
  `sesid` int(3) NOT NULL,
  `status` varchar(12) DEFAULT NULL,
  `course` varchar(12) DEFAULT NULL,
  `approved` enum('FALSE','TRUE') NOT NULL DEFAULT 'FALSE',
  `level` int(1) NOT NULL,
  PRIMARY KEY (`stdid`,`sesid`),
  KEY `fkstudent_idx` (`stdid`),
  KEY `fksession_idx` (`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `resultid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stdid` char(11) NOT NULL,
  `csid` char(7) NOT NULL,
  `sesid` int(3) NOT NULL,
  `tscore` tinyint(2) DEFAULT NULL,
  `escore` tinyint(2) DEFAULT NULL,
  `cleared` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `approved` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `edited` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`resultid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=203744 ;

-- --------------------------------------------------------

--
-- Table structure for table `resultold`
--

CREATE TABLE IF NOT EXISTS `resultold` (
  `stdid` char(11) NOT NULL,
  `csid` char(7) NOT NULL,
  `sesid` int(3) NOT NULL,
  `tscore` tinyint(2) DEFAULT '0',
  `escore` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`stdid`,`csid`,`sesid`),
  KEY `fkstudent_idx` (`stdid`),
  KEY `fkcourse_idx` (`csid`),
  KEY `fksession_idx` (`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `result_error`
--

CREATE TABLE IF NOT EXISTS `result_error` (
  `stdid` char(11) NOT NULL,
  `csid` char(7) NOT NULL,
  `tscore` tinyint(2) NOT NULL,
  `escore` tinyint(2) NOT NULL,
  `sesid` int(3) NOT NULL,
  `lectid` char(8) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stdid`,`csid`,`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `result_log`
--

CREATE TABLE IF NOT EXISTS `result_log` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `stdid` char(11) CHARACTER SET utf8 NOT NULL,
  `lectid` char(8) CHARACTER SET utf8 NOT NULL,
  `sesid` int(11) NOT NULL,
  `csid` char(7) CHARACTER SET utf8 NOT NULL,
  `old_test` tinyint(2) unsigned DEFAULT NULL,
  `old_exam` tinyint(2) unsigned DEFAULT NULL,
  `new_test` tinyint(2) unsigned DEFAULT NULL,
  `new_exam` tinyint(2) unsigned DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`logid`),
  KEY `lectid` (`lectid`),
  KEY `sesid` (`sesid`),
  KEY `csid` (`csid`),
  KEY `stdid` (`stdid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `schfees`
--

CREATE TABLE IF NOT EXISTS `schfees` (
  `stdid` varchar(20) NOT NULL,
  `can_name` varchar(100) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `amt` text,
  `resp_code` varchar(4) DEFAULT NULL,
  `resp_desc` text NOT NULL,
  `ordid` varchar(20) NOT NULL,
  `auth_code` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `pan` varchar(20) NOT NULL,
  `status` text NOT NULL,
  `name` text NOT NULL,
  `date_time` varchar(40) NOT NULL,
  `sessionid` varchar(500) DEFAULT NULL,
  `gatewayurl` varchar(500) DEFAULT NULL,
  `xml` text NOT NULL,
  `level` int(3) NOT NULL,
  UNIQUE KEY `reference` (`reference`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schfee_transactions`
--

CREATE TABLE IF NOT EXISTS `schfee_transactions` (
  `matric_no` varchar(20) NOT NULL,
  `can_no` varchar(11) DEFAULT NULL,
  `can_name` varchar(100) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `amt` text,
  `resp_code` varchar(4) DEFAULT NULL,
  `resp_desc` text NOT NULL,
  `ordid` varchar(20) NOT NULL,
  `auth_code` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `pan` varchar(20) NOT NULL,
  `status` text NOT NULL,
  `name` text NOT NULL,
  `date_time` varchar(40) NOT NULL,
  `sessionid` varchar(500) DEFAULT NULL,
  `gatewayurl` varchar(500) DEFAULT NULL,
  `xml` text NOT NULL,
  `admission_type` varchar(20) NOT NULL,
  `percentPaid` int(3) NOT NULL,
  `balance` int(7) DEFAULT NULL,
  UNIQUE KEY `reference` (`reference`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schfee_utme`
--

CREATE TABLE IF NOT EXISTS `schfee_utme` (
  `matric_no` varchar(20) NOT NULL,
  `can_no` int(7) DEFAULT NULL,
  `can_name` varchar(100) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `amt` text,
  `resp_code` varchar(4) DEFAULT NULL,
  `resp_desc` text NOT NULL,
  `ordid` varchar(20) NOT NULL,
  `auth_code` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `pan` varchar(20) NOT NULL,
  `status` text NOT NULL,
  `name` text NOT NULL,
  `date_time` varchar(40) NOT NULL,
  `sessionid` varchar(500) DEFAULT NULL,
  `gatewayurl` varchar(500) DEFAULT NULL,
  `xml` text NOT NULL,
  `admission_type` varchar(20) NOT NULL,
  UNIQUE KEY `reference` (`reference`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `sesid` int(3) NOT NULL AUTO_INCREMENT,
  `sesname` char(9) NOT NULL,
  `tnumin` tinyint(2) DEFAULT NULL,
  `tnumax` tinyint(2) DEFAULT NULL,
  `status` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `registration` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`sesid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `staff_adviser`
--

CREATE TABLE IF NOT EXISTS `staff_adviser` (
  `lectid` char(8) DEFAULT NULL,
  `sesid` int(3) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `deptid` int(3) NOT NULL,
  KEY `lectid` (`lectid`,`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `stid` int(2) NOT NULL AUTO_INCREMENT,
  `stname` varchar(50) NOT NULL,
  PRIMARY KEY (`stid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `stdid` char(11) NOT NULL,
  `lname` varchar(25) NOT NULL,
  `fname` varchar(25) NOT NULL,
  `mname` varchar(25) DEFAULT NULL,
  `progid` int(3) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `addr` text,
  `sex` char(1) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `sesid` int(3) NOT NULL,
  `disciplinary` enum('True','False') NOT NULL DEFAULT 'False',
  `level` tinyint(1) NOT NULL,
  `admode` enum('UTME','DE') NOT NULL DEFAULT 'UTME',
  `password` varchar(35) NOT NULL,
  `status` enum('Undergrad','Graduated','Suspended','Withdrawn') NOT NULL DEFAULT 'Undergrad',
  `access` tinyint(1) NOT NULL DEFAULT '10',
  `credit` int(4) NOT NULL DEFAULT '0',
  `profile` text,
  `payment` varchar(4) DEFAULT NULL,
  `stid` int(2) NOT NULL,
  PRIMARY KEY (`stdid`),
  KEY `progid_idx` (`progid`),
  KEY `sesid_idx` (`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE IF NOT EXISTS `subject` (
  `subjid` int(4) NOT NULL AUTO_INCREMENT,
  `subjname` varchar(30) NOT NULL,
  PRIMARY KEY (`subjid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaching`
--

CREATE TABLE IF NOT EXISTS `teaching` (
  `lectid1` char(8) NOT NULL,
  `lectid2` char(8) DEFAULT NULL,
  `csid` char(7) NOT NULL,
  `deptid` int(3) DEFAULT NULL,
  `sesid` int(3) NOT NULL,
  `ttable` varchar(50) DEFAULT NULL,
  `upload` enum('yes','no') DEFAULT 'no',
  `approve` enum('yes','no') DEFAULT 'no',
  UNIQUE KEY `csid` (`csid`,`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `trsid` int(10) NOT NULL AUTO_INCREMENT,
  `stdid` char(11) NOT NULL,
  `amount` smallint(4) NOT NULL,
  `type` varchar(30) NOT NULL,
  `sesid` int(3) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `detail` varchar(60) NOT NULL,
  PRIMARY KEY (`trsid`),
  UNIQUE KEY `stdid_UNIQUE` (`stdid`),
  UNIQUE KEY `sesid_UNIQUE` (`sesid`),
  KEY `stdid_idx` (`stdid`),
  KEY `sesid_idx` (`sesid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE IF NOT EXISTS `verification` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `stdid` char(11) NOT NULL,
  `sesid` int(3) NOT NULL,
  `type` int(1) NOT NULL,
  `ver_code` char(13) NOT NULL,
  `verified` enum('TRUE','FALSE') DEFAULT 'FALSE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2613 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`catid`) REFERENCES `category` (`catid`),
  ADD CONSTRAINT `course_ibfk_2` FOREIGN KEY (`deptid`) REFERENCES `department` (`deptid`);

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`colid`) REFERENCES `college` (`colid`);

--
-- Constraints for table `grade_exceptions`
--
ALTER TABLE `grade_exceptions`
  ADD CONSTRAINT `grade_exceptions_ibfk_1` FOREIGN KEY (`sesid`) REFERENCES `session` (`sesid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `grade_exceptions_ibfk_2` FOREIGN KEY (`csid`) REFERENCES `course` (`csid`) ON UPDATE CASCADE;

--
-- Constraints for table `grading`
--
ALTER TABLE `grading`
  ADD CONSTRAINT `grading_ibfk_1` FOREIGN KEY (`colid`) REFERENCES `college` (`colid`) ON UPDATE CASCADE;

--
-- Constraints for table `graduation_condition`
--
ALTER TABLE `graduation_condition`
  ADD CONSTRAINT `graduation_condition_ibfk_2` FOREIGN KEY (`colid`) REFERENCES `college` (`colid`),
  ADD CONSTRAINT `graduation_condition_ibfk_3` FOREIGN KEY (`catid`) REFERENCES `category` (`catid`);

--
-- Constraints for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD CONSTRAINT `lecturer_ibfk_1` FOREIGN KEY (`deptid`) REFERENCES `department` (`deptid`);

--
-- Constraints for table `result_log`
--
ALTER TABLE `result_log`
  ADD CONSTRAINT `result_log_ibfk_1` FOREIGN KEY (`sesid`) REFERENCES `session` (`sesid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `result_log_ibfk_2` FOREIGN KEY (`lectid`) REFERENCES `lecturer` (`lectid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `result_log_ibfk_4` FOREIGN KEY (`csid`) REFERENCES `course` (`csid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `result_log_ibfk_5` FOREIGN KEY (`stdid`) REFERENCES `student` (`stdid`) ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`progid`) REFERENCES `programme` (`progid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
