CREATE TABLE IF NOT EXISTS pv_designations (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table pv.designations: ~4 rows (approximately)
TRUNCATE TABLE pv_designations;
/*!40000 ALTER TABLE pv_designations DISABLE KEYS */;
INSERT INTO pv_designations (id, name, created, modified) VALUES
	(1, 'physician', '2012-06-01 12:22:09', '2012-06-01 12:22:09'),
	(2, 'pharmacist', '2012-06-01 12:22:26', '2012-06-01 12:22:26'),
	(3, 'other professional', '2012-06-01 12:22:40', '2012-06-01 12:22:40'),
	(5, 'consumer or other non health professional', '2012-06-01 12:23:00', '2012-06-01 12:23:00');
