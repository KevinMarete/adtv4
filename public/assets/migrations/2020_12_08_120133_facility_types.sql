-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'//

SET NAMES utf8mb4//

DROP TABLE IF EXISTS `facility_types`;
CREATE TABLE `facility_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4//

TRUNCATE `facility_types`//
INSERT INTO `facility_types` (`id`, `Name`) VALUES
(1,	'Laboratory (Stand-alone)'),
(2,	'Medical Clinic - Nurse/ Midwife'),
(3,	'Medical Clinic - Other'),
(4,	'Medical Clinic - Clinical office'),
(5,	'Dispensary'),
(6,	'Health Centre'),
(7,	'Rehabilitation Centre'),
(8,	'Medical Clinic - Medical special'),
(9,	'Nursing home with Maternity'),
(10,	'Eye Centre'),
(11,	'Medical Clinic - General practitioner'),
(12,	'Primary Hospital'),
(13,	'Other Health Facility'),
(14,	'Other Hospital'),
(15,	'Nursing home without Maternity'),
(16,	'Dental Clinic'),
(17,	'Radiology Unit'),
(18,	'Unknown'),
(19,	'Training Institution in Health (Stand-alone)'),
(20,	'Secondary Hospital'),
(21,	'VCT Centre (Stand-Alone)'),
(22,	'Tertiary Hospital'),
(23,	'Health Programme'),
(24,	'Blood Bank'),
(25,	'Level 4'),
(26,	'Level 5'),
(27,	'DICE')//

-- 2020-12-08 09:00:58
