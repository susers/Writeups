# Host: 127.0.0.1  (Version: 5.5.53)
# Date: 2018-10-20 19:40:25
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `username` varchar(30) NOT NULL,
  `phonenumber` varchar(20) DEFAULT NULL,
  `QQ` varchar(20) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `reward` varchar(255) DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `sex` int(1) NOT NULL,
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `age` int(3) NOT NULL,
  `password` varchar(50) NOT NULL,
  `birthday` varchar(255) DEFAULT '19970911',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "users"
#

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('admin',NULL,NULL,NULL,'./upload/admin_xf.jpg','装13一等奖！抄写部长名字一等奖',NULL,0,1,19,'23e38db370b570dbd07added13dfc001','19970911');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
