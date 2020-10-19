CREATE TABLE IF NOT EXISTS `drug_unit` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) NOT NULL,
  `ccc_store_sp` int(11) NOT NULL DEFAULT '2',
  `Active` varchar(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`Name`),
  KEY `ccc_store_sp` (`ccc_store_sp`),
  CONSTRAINT `drug_unit_ibfk_1` FOREIGN KEY (`ccc_store_sp`) REFERENCES `ccc_store_service_point` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1//

TRUNCATE TABLE `drug_unit` //

INSERT INTO `drug_unit` (`id`, `Name`, `ccc_store_sp`, `Active`) VALUES
(1, 'Bottle', 2,  '1'),
(2, 'Capsule',  2,  '1'),
(3, 'Pack', 2,  '1'),
(4, 'Tablet', 2,  '1'),
(5, 'Vial', 2,  '1')//
