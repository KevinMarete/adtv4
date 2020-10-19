CREATE TABLE `temp_regimen_service_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `active` varchar(2) NOT NULL DEFAULT '1',
  `ccc_store_sp` int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1//

INSERT INTO `temp_regimen_service_type` (`name`, `active`, `ccc_store_sp`) VALUES
('HEI',	'1',	2)//


INSERT INTO `regimen_service_type` 
(`name`, `active`, `ccc_store_sp`)
select t1.name, t1.active, t1.ccc_store_sp
from temp_regimen_service_type t1
WHERE NOT EXISTS (select name 
                    from regimen_service_type t2
                    WHERE t2.name=t1.name) //

 drop TABLE temp_regimen_service_type //