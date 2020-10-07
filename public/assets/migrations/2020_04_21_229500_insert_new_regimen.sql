CREATE TABLE temp_regimen (
  id int(4) NOT NULL AUTO_INCREMENT,
  regimen_code varchar(20) NOT NULL,
  regimen_desc text NOT NULL,
  category int(11) DEFAULT NULL,
  line varchar(4) NOT NULL,
  type_of_service varchar(20) NOT NULL,
  remarks varchar(30) NOT NULL,
  enabled varchar(4) NOT NULL DEFAULT '1',
  source varchar(10) NOT NULL DEFAULT '0',
  optimality varchar(10) NOT NULL DEFAULT '1',
  Merged_To varchar(50) NOT NULL,
  map int(11) NOT NULL,
  ccc_store_sp int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (id)
)//

INSERT INTO temp_regimen (regimen_code, regimen_desc, category, line, type_of_service, remarks, enabled, source, optimality, Merged_To, map, ccc_store_sp) VALUES
('CF4E',  'TDF + 3TC + DTG (<15 Yrs)',  5,  '1',  '1',  '', '1',  '0',  '1',  '', 356,  2) //

INSERT INTO regimen 
(regimen_code, regimen_desc, category, line, type_of_service, remarks, enabled, source, optimality, Merged_To, map, ccc_store_sp)
select t1.regimen_code, t1.regimen_desc, t1.category, t1.line, t1.type_of_service, t1.remarks, t1.enabled, t1.source, t1.optimality, t1.Merged_To, t1.map, t1.ccc_store_sp
from temp_regimen t1
WHERE NOT EXISTS (select regimen_code 
                    from regimen t2
                    WHERE t2.regimen_code=t1.regimen_code) //

 drop TABLE temp_regimen //