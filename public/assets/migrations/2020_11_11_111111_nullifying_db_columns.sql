ALTER TABLE `api_patient_matching`
CHANGE `internal_id` `internal_id` varchar(200) COLLATE 'utf8mb4_general_ci' NULL AFTER `id`,
CHANGE `external_id` `external_id` varchar(200) COLLATE 'utf8mb4_general_ci' NULL AFTER `internal_id`,
CHANGE `identifier_type` `identifier_type` char(200) COLLATE 'utf8mb4_general_ci' NULL AFTER `external_id`,
CHANGE `assigning_authority` `assigning_authority` varchar(200) COLLATE 'utf8mb4_general_ci' NULL AFTER `identifier_type`//

ALTER TABLE `api_patient_matching`
DROP INDEX `external_id`//