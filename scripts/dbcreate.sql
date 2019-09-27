-- MySQL dump 10.13  Distrib 5.6.45-86.1, for Linux (x86_64)
--
-- Host: localhost    Database: tickets_responses
-- ------------------------------------------------------
-- Server version	5.6.45-86.1

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
-- Table structure for table `bar_responses`
--

DROP TABLE IF EXISTS `bar_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bar_responses` (
  `RID` int(11) NOT NULL,
  `offby` int(11) DEFAULT '0',
  `category` varchar(100) DEFAULT NULL,
  `category_index` int(11) NOT NULL DEFAULT '0',
  `response` int(11) NOT NULL,
  `phase` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `repeat_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `responses` (
  `RID` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `points_phase0` int(11) DEFAULT '0',
  `points_phase1` int(11) DEFAULT '0',
  `age` int(11) NOT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `tries` int(11) DEFAULT '1',
  `during` varchar(100) DEFAULT NULL,
  `worker_id` varchar(256) DEFAULT NULL,
  `assignment_id` varchar(256) DEFAULT NULL,
  `bonus` int(11) NOT NULL DEFAULT '0',
  `bonus_paid` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`RID`)
) ENGINE=InnoDB AUTO_INCREMENT=1121 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_responses`
--

DROP TABLE IF EXISTS `test_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_responses` (
  `RID` int(11) NOT NULL,
  `sequence` int(11) DEFAULT '0',
  `phase` int(11) DEFAULT '0',
  `response` int(11) NOT NULL,
  `place` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `next_num` int(11) NOT NULL DEFAULT '0',
  `prices` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testing_orders`
--

DROP TABLE IF EXISTS `testing_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testing_orders` (
  `RID` int(11) NOT NULL,
  `phase` int(11) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL DEFAULT '0',
  `order_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testing_times`
--

DROP TABLE IF EXISTS `testing_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testing_times` (
  `RID` int(11) NOT NULL,
  `phase` int(11) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL DEFAULT '0',
  `place` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `training_responses`
--

DROP TABLE IF EXISTS `training_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_responses` (
  `RID` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `response` int(11) NOT NULL,
  `avg` int(11) NOT NULL,
  `phase` int(11) NOT NULL DEFAULT '0',
  `repeat_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-27  1:53:49
