CREATE TABLE temp_sync_regimen (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) CHARACTER SET utf8 NOT NULL,
  code varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  old_code varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  description text CHARACTER SET utf8 NOT NULL,
  category_id int(11) unsigned DEFAULT NULL,
  Active varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
)//
insert  INTO temp_sync_regimen (name, code, old_code, description, category_id, Active) VALUES
('AZT + 3TC + LPV/r',	'AF1E',	NULL,	'AZT + 3TC + LPV/r',	2,	'1'),
('AZT + 3TC + ATV/r',	'AF1F',	NULL,	'AZT + 3TC + ATV/r',	2,	'1'),
('AZT + 3TC + DTG',	'AS1C',	NULL,	'AZT + 3TC + DTG',	3,	'1'),
('TDF + 3TC + DTG',	'AS2B',	NULL,	'TDF + 3TC + DTG',	3,	'1'),
('ABC + 3TC + DTG',	'AS5C',	NULL,	'ABC + 3TC + DTG',	3,	'1'),
('TDF+3TC+DTG+DRV+RTV',	'AT2D',	NULL,	'TDF+3TC+DTG+DRV+RTV',	12,	'1'),
('TDF+3TC+RAL+DRV+RTV',	'AT2E',	NULL,	'TDF+3TC+RAL+DRV+RTV',	12,	'1'),
('TDF+3TC+DTG+ETV+DRV+RTV',	'AT2F',	NULL,	'TDF+3TC+DTG+ETV+DRV+RTV',	12,	'1'),
('Adult patients (=>15 Yrs) newly started on IPT in the month',	'ATPT1A',	NULL, 'Adult patients (=>15 Yrs) newly started on IPT in the month',	11,	'1'),
('Adult patients (=>15 Yrs) newl started on 3HP in the month',	'ATPT1B',	NULL, 'Adult patients (=>15 Yrs) newly started on 3HP in the month',	11,	'1'),
('ABC+3TC+DTG',	'CF2G',	NULL,	'ABC+3TC+DTG',	5,	'1'),
('AZT + 3TC + DRV+RTV+RAL',	'CS1C',	NULL,	'AZT + 3TC + DRV+RTV+RAL',	6,	'1'),
('ABC+3TC+DTG',	'CS2B',	NULL,	'ABC+3TC+DTG',	6,	'1'),
('ABC + 3TC + DRV+RTV+RAL',	'CS2D',	NULL,	'ABC + 3TC + DRV+RTV+RAL',	6,	'1'),
('AZT + 3TC + DRV+RTV+RAL',	'CT1H',	NULL,	'AZT + 3TC + DRV+RTV+RAL',	13,	'1'),
('ABC + 3TC + DRV+RTV+RAL',	'CT2D',	NULL,	'ABC + 3TC + DRV+RTV+RAL',	13,	'1'),
('Paed patients (<15 Yrs) newly started on IPT in the month',	'CTPT1A',	NULL, 'Paed patients (<15 Yrs) newly started on IPT in the month',	11,	'1'),
('Paed patients (<15 Yrs) newlys',	'CTPT1B ',	NULL,	'Paed patients (<15 Yrs) newly started on 3HP in the month',	11,	'1'),
('Adult patients (=>15 Yrs) on Amphotericin B treatment in the month',	'OI6A',	NULL,	'Adult patients (=>15 Yrs) on Amphotericin B treatment in the month',	11,	'1'),
('TDF + 3TC + DTG (Adult PEP)',	'PA3D',	NULL,	'TDF + 3TC + DTG (Adult PEP)',	8,	'1'),
('ABC + 3TC + RAL (Paed PEP)',	'PC3B',	NULL,	'ABC + 3TC + RAL (Paed PEP)',	9,	'1'),
('PMTCT HAART: TDF + 3TC + DTG',	'PM12',	NULL,	'PMTCT HAART: TDF + 3TC + DTG',	1,	'1'),
('PMTCT HAART: AZT + 3TC + DTG',	'PM13',	NULL,	'PMTCT HAART: AZT + 3TC + DTG',	1,	'1'),
('PMTCT HAART: ABC + 3TC + DTG',	'PM14',	NULL,	'PMTCT HAART: ABC + 3TC + DTG',	1,	'1'),
('PMTCT HAART: ABC + 3TC + EFV',	'PM15',	NULL,	'PMTCT HAART: ABC + 3TC + EFV',	1,	'1')//

insert into sync_regimen (name, code, old_code, description, category_id, Active) select name, code, old_code, description, category_id, Active from temp_sync_regimen where code not in (select code from sync_regimen)//

drop table temp_sync_regimen//