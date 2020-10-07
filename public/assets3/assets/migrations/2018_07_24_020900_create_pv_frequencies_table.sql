CREATE TABLE IF NOT EXISTS pv_frequencies (
  id int(11) NOT NULL AUTO_INCREMENT,
  value varchar(100) DEFAULT NULL,
  name varchar(100) DEFAULT NULL,
  icsr_code varchar(100) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
)//
TRUNCATE TABLE pv_frequencies//
INSERT INTO pv_frequencies (id, value, name, icsr_code, created, modified) VALUES
  (2, 'OD', 'OD (once daily)', NULL, NULL, NULL),
  (3, 'BD', 'BD (twice daily)', NULL, NULL, NULL),
  (4, 'TID.', 'TID. (three times a day)', NULL, NULL, NULL),
  (5, 'QID|QDS', 'QID|QDS (four times a day)', NULL, NULL, NULL),
  (6, 'PRN', 'PRN PRN (as needed)', NULL, NULL, NULL),
  (7, 'MANE', 'MANE (in the morning)', NULL, NULL, NULL),
  (8, 'NOCTE', 'NOCTE (at night)', NULL, NULL, NULL),
  (9, 'STAT', 'STAT (immediately)', NULL, NULL, NULL)//
