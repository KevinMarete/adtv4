CREATE TABLE IF NOT EXISTS pv_counties (
  id int(11) NOT NULL AUTO_INCREMENT,
  county_name varchar(50) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
)//


-- Dumping data for table pv.counties: ~47 rows (approximately)
TRUNCATE TABLE pv_counties//
/*!40000 ALTER TABLE pv_counties DISABLE KEYS */;
INSERT INTO pv_counties (id, county_name, created, modified) VALUES
	(1, 'Mombasa', '2012-05-31 16:15:11', '2012-07-09 13:06:23'),
	(2, 'Kwale', '2012-05-31 16:15:21', '2012-05-31 16:15:21'),
	(3, 'Kilifi', '2012-05-31 16:15:49', '2012-05-31 16:15:49'),
	(4, 'Tana River', '2012-05-31 16:15:57', '2012-05-31 16:15:57'),
	(5, 'Lamu', '2012-05-31 16:16:04', '2012-05-31 16:16:04'),
	(6, 'Taita Taveta', '2012-05-31 16:16:22', '2012-05-31 16:16:22'),
	(7, 'Garissa', '2012-05-31 16:16:29', '2012-05-31 16:16:29'),
	(8, 'Wajir', '2012-06-15 10:22:58', '2012-06-15 10:22:58'),
	(9, 'Mandera', '2012-06-15 10:23:07', '2012-06-15 10:23:07'),
	(10, 'Marsabit', '2012-06-15 10:23:14', '2012-06-15 10:23:14'),
	(11, 'Isiolo', '2012-06-15 10:23:21', '2012-06-15 10:23:21'),
	(12, 'Meru', '2012-06-15 10:23:27', '2012-06-15 10:23:27'),
	(13, 'Tharaka Nithi', '2012-06-15 10:23:35', '2012-06-15 10:23:35'),
	(14, 'Embu', '2012-06-15 10:23:42', '2012-06-15 10:23:42'),
	(15, 'Kitui', '2012-06-15 10:23:48', '2012-06-15 10:23:48'),
	(16, 'Machakos', '2012-06-15 10:23:55', '2012-06-15 10:23:55'),
	(17, 'Makueni', '2012-06-15 10:24:02', '2012-06-15 10:24:02'),
	(18, 'Nyandarua', '2012-06-15 10:24:09', '2012-06-15 10:24:09'),
	(19, 'Nyeri', '2012-06-15 10:24:16', '2012-06-15 10:24:16'),
	(20, 'Kirinyaga', '2012-06-15 10:24:22', '2012-06-15 10:24:22'),
	(21, 'Murang\'a', '2012-06-15 10:24:31', '2012-06-15 10:24:31'),
	(22, 'Kiambu', '2012-06-15 10:24:37', '2012-06-15 10:24:37'),
	(23, 'Turkana', '2012-06-15 10:24:43', '2012-06-15 10:24:43'),
	(24, 'West Pokot', '2012-06-15 10:24:52', '2012-06-15 10:24:52'),
	(25, 'Samburu', '2012-06-15 10:24:58', '2012-06-15 10:24:58'),
	(26, 'Trans Nzoia', '2012-06-15 10:25:05', '2012-06-15 10:25:05'),
	(27, 'Uasin Gishu', '2012-06-15 10:25:15', '2012-06-15 10:25:15'),
	(28, 'Elgeyo/Marakwet', '2012-06-15 10:25:27', '2012-06-15 10:25:27'),
	(29, 'Nandi', '2012-06-15 10:25:33', '2012-06-15 10:25:33'),
	(30, 'Baringo', '2012-06-15 10:25:39', '2012-06-15 10:25:39'),
	(31, 'Laikipia', '2012-06-15 10:25:46', '2012-06-15 10:25:46'),
	(32, 'Nakuru', '2012-06-15 10:25:52', '2012-06-15 10:25:52'),
	(33, 'Narok', '2012-06-15 10:26:02', '2012-06-15 10:26:02'),
	(34, 'Kajiado', '2012-06-15 10:26:09', '2012-06-15 10:26:09'),
	(35, 'Kericho', '2012-06-15 10:26:16', '2012-06-15 10:26:16'),
	(36, 'Bomet', '2012-06-15 10:26:23', '2012-06-15 10:26:23'),
	(37, 'Kakamega', '2012-06-15 10:26:29', '2012-06-15 10:26:29'),
	(38, 'Vihiga', '2012-06-15 10:26:37', '2012-06-15 10:26:37'),
	(39, 'Bung\'oma', '2012-06-15 10:26:45', '2012-06-15 10:26:45'),
	(40, 'Busia', '2012-06-15 10:26:51', '2012-06-15 10:26:51'),
	(41, 'Siaya', '2012-06-15 10:26:56', '2012-06-15 10:26:56'),
	(42, 'Kisumu', '2012-06-15 10:27:02', '2012-06-15 10:27:02'),
	(43, 'Homa Bay', '2012-06-15 10:27:10', '2012-06-15 10:27:10'),
	(44, 'Migori', '2012-06-15 10:27:16', '2012-06-15 10:27:16'),
	(45, 'Kisii', '2012-06-15 10:27:25', '2012-06-15 10:27:25'),
	(46, 'Nyamira', '2012-06-15 10:27:32', '2012-06-15 10:27:32'),
	(47, 'Nairobi City', '2012-06-15 10:27:40', '2012-06-15 10:27:40')//
/*!40000 ALTER TABLE counties ENABLE KEYS */;

