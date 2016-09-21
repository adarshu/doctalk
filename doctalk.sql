-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: pivotle.cloudapp.net    Database: airmeet
-- ------------------------------------------------------
-- Server version	5.5.50-0ubuntu0.14.04.1

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
-- Table structure for table `doctalk_messages`
--

DROP TABLE IF EXISTS `doctalk_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctalk_messages` (
  `uuid` int(16) NOT NULL AUTO_INCREMENT,
  `version` int(10) DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `src_pid` varchar(100) NOT NULL,
  `dest_pid` varchar(100) NOT NULL,
  `message` text,
  `read` varchar(45) DEFAULT NULL,
  `aimessage` varchar(255) DEFAULT NULL,
  `priority` varchar(45) DEFAULT NULL,
  `picmsg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctalk_messages`
--

LOCK TABLES `doctalk_messages` WRITE;
/*!40000 ALTER TABLE `doctalk_messages` DISABLE KEYS */;
INSERT INTO `doctalk_messages` (`uuid`, `version`, `last_modified`, `src_pid`, `dest_pid`, `message`, `read`, `aimessage`, `priority`, `picmsg`) VALUES (1,1,'2016-09-11 10:49:15','1','0','Doc, I was playing football and my friend elbowed me in the face and my front tooth got knocked out. What do I do??','yes',NULL,NULL,NULL),(3,1,'2016-09-11 10:49:15','0','1','send me a picture of that area of your mouth','yes',NULL,NULL,NULL),(4,1,'2016-09-11 10:49:15','3','0','Hey Doc, the wire you placed last week came loose and it\'s poking my cheek. Is there anything I can do before I see you next week?','yes',NULL,NULL,NULL),(75,1,'2016-09-11 10:51:02','2','0','Hi doctor, My son needs a letter signed by his dentist for school. Can I email you the form so you can fill it and email it back to me?','yes',NULL,NULL,NULL),(76,1,'2016-09-11 11:04:15','0','2','Ok','yes',NULL,NULL,NULL),(132,1,'2016-09-11 20:13:47','0','1','Watson automated message: Please bring it with you to the next earliest appointment. If your tooth feels sensitive, use Sensodyne toothpaste until your appointment.','yes','',NULL,NULL),(135,1,'2016-09-11 20:35:19','1','0','Hey doc, my tooth got knocked out','yes','{\"result\":\"status_1\"}','yes','/doctalk/pics/tooth.jpg'),(211,1,'2016-09-21 17:24:21','0','2','bye','yes',NULL,NULL,NULL),(212,1,'2016-09-21 17:24:42','2','0','Yo','yes','{\"result\":\"status_1\"}','yes',NULL),(213,1,'2016-09-21 17:25:12','2','0','Hi','yes','{\"result\":\"status_1\"}','yes',NULL),(214,1,'2016-09-21 17:43:27','2','0','Ok','yes','{\"result\":\"status_1\"}','yes',NULL),(215,1,'2016-09-21 17:44:58','2','0','Hii','yes','{\"result\":\"status_1\"}','yes',NULL);
/*!40000 ALTER TABLE `doctalk_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctalk_users`
--

DROP TABLE IF EXISTS `doctalk_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctalk_users` (
  `uuid` int(16) NOT NULL AUTO_INCREMENT,
  `version` int(10) DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pid` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `pic` text,
  `twilio_phone` varchar(100) NOT NULL,
  `desc` text,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctalk_users`
--

LOCK TABLES `doctalk_users` WRITE;
/*!40000 ALTER TABLE `doctalk_users` DISABLE KEYS */;
INSERT INTO `doctalk_users` (`uuid`, `version`, `last_modified`, `pid`, `name`, `phone`, `pic`, `twilio_phone`, `desc`) VALUES (1,1,'2016-09-11 18:23:45','0','Dr. Anuhya','15105855120','/doctalk/pics/anuhya.jpg','5101234567','doctor'),(2,1,'2016-09-11 18:02:48','1','Sameep Sheth','16177179668','/doctalk/pics/sameep.jpg','5101234567','trauma'),(3,1,'2016-09-11 18:02:48','2','Adarsh Uppula','15105855123','/doctalk/pics/adarsh.jpg','5101234567','poking'),(4,1,'2016-09-11 18:02:48','3','Richard Smith','15105855121','/doctalk/pics/richard.jpg','5101234567','other');
/*!40000 ALTER TABLE `doctalk_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-09-21 13:26:57
