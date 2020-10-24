CREATE TABLE temp_sync_regimen (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  code varchar(10) DEFAULT NULL,
  old_code varchar(45) DEFAULT NULL,
  description text NOT NULL,
  category_id int(11) unsigned DEFAULT NULL,
  Active varchar(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 //

INSERT INTO temp_sync_regimen (name, code, old_code, description, category_id, Active) VALUES
('TDF + 3TC + DTG (<15 Yrs)',	'CF4E',	'',	'Tenefovir + Lamivudine + Dolutegravir',	7,	'1') //

INSERT INTO sync_regimen 
(name, code, old_code, description, category_id, Active)
select t1.name, t1.code, t1.old_code, t1.description, t1.category_id, t1.Active
from temp_sync_regimen t1
WHERE NOT EXISTS (select code 
                    from sync_regimen t2
                    WHERE t2.code=t1.code) //

 drop TABLE temp_sync_regimen //