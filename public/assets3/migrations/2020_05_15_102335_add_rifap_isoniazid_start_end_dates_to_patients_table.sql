ALTER TABLE `patient`
ADD `rifap_isoniazid_start_date` varchar(20) COLLATE 'latin1_swedish_ci' NULL AFTER `isoniazid_end_date`,
ADD `rifap_isoniazid_end_date` varchar(20) COLLATE 'latin1_swedish_ci' NULL AFTER `rifap_isoniazid_start_date`//