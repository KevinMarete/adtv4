ALTER TABLE `drug_prescription`
ADD `height` varchar(5) NULL,
ADD `weight` varchar(5) NULL AFTER `height`,
ADD `current_regimen` text NULL AFTER `weight`//