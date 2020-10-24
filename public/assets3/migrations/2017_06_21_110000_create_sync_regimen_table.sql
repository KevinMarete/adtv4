CREATE TABLE IF NOT EXISTS `sync_regimen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(5) DEFAULT NULL,
  `old_code` varchar(45) DEFAULT NULL,
  `description` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8//