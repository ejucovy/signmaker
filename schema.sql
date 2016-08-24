CREATE TABLE `submissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL, 
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `zip` int(10) unsigned NOT NULL,
  `phone` varchar(255) NOT NULL,
  `template` varchar(32) DEFAULT NULL,
  `generated` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6463 DEFAULT CHARSET=latin1;
