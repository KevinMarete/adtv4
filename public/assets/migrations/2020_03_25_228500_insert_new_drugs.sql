CREATE TABLE temp_drugcode (
  id int(4) NOT NULL AUTO_INCREMENT,
  drug varchar(100) NOT NULL,
  unit varchar(150) DEFAULT NULL,
  pack_size varchar(10) NOT NULL,
  safety_quantity varchar(4) NOT NULL,
  generic_name varchar(150) DEFAULT NULL,
  supported_by varchar(30) NOT NULL,
  classification varchar(150) DEFAULT NULL,
  none_arv varchar(1) NOT NULL,
  tb_drug varchar(1) NOT NULL,
  drug_in_use varchar(1) NOT NULL,
  comment varchar(100) NOT NULL,
  dose varchar(20) NOT NULL,
  duration varchar(4) NOT NULL,
  quantity varchar(4) NOT NULL,
  source varchar(10) NOT NULL DEFAULT '0',
  type varchar(1) NOT NULL DEFAULT '0',
  supplied varchar(1) NOT NULL DEFAULT '0',
  enabled int(11) NOT NULL DEFAULT '1',
  strength varchar(20) NOT NULL,
  merged_to varchar(50) NOT NULL,
  map int(11) NOT NULL,
  ccc_store_sp int(11) NOT NULL DEFAULT '2',
  instructions varchar(150) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ccc_store_sp (ccc_store_sp)
) //
INSERT INTO temp_drugcode (drug, unit, pack_size, safety_quantity, generic_name, supported_by, classification, none_arv, tb_drug, drug_in_use, comment, dose, duration, quantity, source, type, supplied, enabled, strength, merged_to, map, ccc_store_sp, instructions)
VALUES ('TDF/3TC/DTG 300/300/50mg Tabs (90)', '4', '90', '', 'Tenofovir/Lamivudine/Dolutegravir', '1', '1', '0', '0', '0', '', '1OD', '90', '90', '0', '0', '1', '1', '1', '', '271', '2', NULL),
('TDF/3TC/EFV 300/300/400mg Tabs (90)', '4', '90', '', 'Tenofovir/Lamivudine/Efavirenz', '1', '1', '0', '0', '0', '', '1OD', '90', '90', '0', '0', '1', '1', '1', '', '272', '2', NULL) //
INSERT INTO drugcode 
(drug, unit, pack_size, safety_quantity, generic_name, supported_by, classification, none_arv, tb_drug, drug_in_use, comment, dose, duration, quantity, source, type, supplied, enabled, strength, merged_to, map, ccc_store_sp, instructions)
select t1.drug, t1.unit, t1.pack_size, t1.safety_quantity, t1.generic_name, t1.supported_by, t1.classification, t1.none_arv, t1.tb_drug, t1.drug_in_use, t1.comment, t1.dose, t1.duration, t1.quantity, t1.source, t1.type, t1.supplied, t1.enabled, t1.strength, t1.merged_to, t1.map, t1.ccc_store_sp, t1.instructions
from temp_drugcode t1
WHERE NOT EXISTS (select drug 
                    from drugcode t2
                    WHERE t2.drug=t1.drug) //
 drop TABLE temp_drugcode //