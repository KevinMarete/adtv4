CREATE TABLE IF NOT EXISTS sync_regimen_category (
  id int(2) NOT NULL AUTO_INCREMENT,
  Name varchar(100) NOT NULL,
  Active varchar(2) NOT NULL DEFAULT '1',
  ccc_store_sp int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (id),
  KEY ccc_store_sp (ccc_store_sp)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 //

TRUNCATE TABLE sync_regimen_category//

INSERT INTO sync_regimen_category (id, Name, Active, ccc_store_sp) VALUES
(4,	'Adult First Line',	'1',	2),
(5,	'Adult Second Line',	'1',	2),
(6,	'Other Adult ART',	'0',	2),
(7,	'Paediatric First Line',	'1',	2),
(8,	'Paediatric Second Line',	'1',	2),
(9,	'Other Pediatric Regimen',	'0',	2),
(10,	'PMTCT Mother',	'1',	2),
(11,	'PMTCT Child',	'1',	2),
(12,	'PEP Adult',	'1',	2),
(13,	'PEP Child',	'1',	2),
(17,	'Adult Third Line',	'1',	2),
(18,	'Paediatric Third Line',	'1',	2),
(19,	'OIs Medicines [1. Universal Prophylaxis]',	'1',	2),
(20,	'OIs Medicines [2. IPT]',	'0',	2),
(21,	'OIs Medicines {CM} and {OC} For Diflucan Donation ',	'0',	2),
(22,	'PrEP',	'1',	2),
(23,	'Hepatitis B Patients who are HIV-ve',	'1',	2),
(24,	'OIs Medicines [3.Fluconazole (treatment & prophylaxis)',	'1',	2),
(25,	'TB Preventative Therapy (TPT)',	'1',	2) //