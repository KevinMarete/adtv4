<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*EDITT(Source) database configuration*/
$config['source_hostname'] = 'localhost';
$config['source_port'] = 3306;
$config['source_username'] = 'root';
$config['source_password'] = 'root';
$config['source_database'] = 'fdt';
$config['source_driver'] = 'mysqli';

/*ADT(target) database configuration*/
$config['target_hostname'] = 'localhost';
$config['target_port'] = 3306;
$config['target_username'] = 'root';
$config['target_password'] = 'root';
$config['target_database'] = 'testadt';
$config['target_driver'] = 'mysqli';

/*Migration flags*/
$config['migration_flag_column'] = 'is_migrated';
$config['migration_flag_type'] = 'boolean';
$config['migration_flag_default'] = 0;

/*Migration parameters*/
$config['migration_limit'] = 1000;
$config['migration_offset'] = 0;

/*Migration destination to source tables*/
$config['tables'] = array(
	'dose' => 'dosage', 
	'drug_classification' => 'drug_category',
	'drug_unit' => 'dispensing_unit',
	'family_planning' => 'family_planning_method',
	'generic_name' => 'generic_name',
	'opportunistic_infection' => 'indication',
	'patient_source' => 'patient_source',
	'patient_status' => 'patient_status',
	'regimen_category' => 'regimen_type',
	'regimen_service_type' => 'service_type',
	'regimen_change_purpose' => 'regimen_change_reason',
	'regimen' => 'regimen',
	'drugcode' => 'drug',
	'supporter' => 'supporting_organization',
	'transaction_type' => 'transaction_type',
	'users' => 'user',
	'visit_purpose' => 'visit_type',
	'regimen_drug' => 'regimen_drug',
	'patient' => 'patient',
	'drug_stock_movement' => 'transaction',
	'patient_visit' => 'visit'
);

/*Migration query parameters*/
$config['query_delimiter'] = '//';
$config['query_filetype'] = array('sql');

/*Migration source queries*/
$config['dose_query'] = 'public/sql/editt/dose.sql';
$config['drug_classification_query'] = 'public/sql/editt/drug_classification.sql';
$config['drug_stock_movement_query'] = 'public/sql/editt/drug_stock_movement.sql';
$config['drug_unit_query'] = 'public/sql/editt/drug_unit.sql';
$config['drugcode_query'] = 'public/sql/editt/drugcode.sql';
$config['family_planning_query'] = 'public/sql/editt/family_planning.sql';
$config['generic_name_query'] = 'public/sql/editt/generic_name.sql';
$config['opportunistic_infection_query'] = 'public/sql/editt/opportunistic_infection.sql';
$config['patient_query'] = 'public/sql/editt/patient.sql';
$config['patient_source_query'] = 'public/sql/editt/patient_source.sql';
$config['patient_status_query'] = 'public/sql/editt/patient_status.sql';
$config['patient_visit_query'] = 'public/sql/editt/patient_visit.sql';
$config['regimen_query'] = 'public/sql/editt/regimen.sql';
$config['regimen_category_query'] = 'public/sql/editt/regimen_category.sql';
$config['regimen_change_purpose_query'] = 'public/sql/editt/regimen_change_purpose.sql';
$config['regimen_drug_query'] = 'public/sql/editt/regimen_drug.sql';
$config['regimen_service_type_query'] = 'public/sql/editt/regimen_service_type.sql';
$config['supporter_query'] = 'public/sql/editt/supporter.sql';
$config['transaction_type_query'] = 'public/sql/editt/transaction_type.sql';
$config['users_query'] = 'public/sql/editt/users.sql';
$config['visit_purpose_query'] = 'public/sql/editt/visit_purpose.sql';

/*Migration update matching indices*/
$config['dose_indices'] = array('Name' => 'name', 'value' => 'value', 'frequency' => 'frequency');
$config['drug_classification_indices'] = array('name' => 'name');
$config['drug_stock_movement_indices'] = array('remarks' => 'id');
$config['drug_unit_indices'] = array('Name' => 'name');
$config['drugcode_indices'] = array('drug' => 'name', 'pack_size' => 'pack_size');
$config['family_planning_indices'] = array('name' => 'name');
$config['generic_name_indices'] = array('name' => 'name');
$config['opportunistic_infection_indices'] = array('name' => 'name', 'indication' => 'legacy_pk');
$config['patient_indices'] = array('medical_record_number' => 'person_id');
$config['patient_source_indices'] = array('name' => 'name');
$config['patient_status_indices'] = array('Name' => 'name');
$config['patient_visit_indices'] = array('migration_id' => 'id');
$config['regimen_indices'] = array('regimen_code' => 'code', 'regimen_desc' => 'name', 'line' => 'line');
$config['regimen_category_indices'] = array('Name' => 'name');
$config['regimen_change_purpose_indices'] = array('name' => 'name');
$config['regimen_drug_indices'] = array('Merged_From' => 'regimen_id');
$config['regimen_service_type_indices'] = array('name' => 'name');
$config['supporter_indices'] = array('Name' => 'name');
$config['transaction_type_indices'] = array('name' => 'name', 'desc' => 'description');
$config['users_indices'] = array('Username' => 'username', 'Time_Created' => 'created_on');
$config['visit_purpose_indices'] = array('name' => 'name');

/*Migration update queries*/
$config['drug_stock_movement_update'] = 'public/sql/editt/update/drug_stock_movement.sql';
$config['drugcode_update'] = 'public/sql/editt/update/drugcode.sql';
$config['family_planning_update'] = 'public/sql/editt/update/family_planning.sql';
$config['patient_update'] = 'public/sql/editt/update/patient.sql'; 
$config['patient_visit_update'] = 'public/sql/editt/update/patient_visit.sql';
$config['regimen_update'] = 'public/sql/editt/update/regimen.sql';
$config['regimen_drug_update'] = 'public/sql/editt/update/regimen_drug.sql';
$config['users_update'] = 'public/sql/editt/update/users.sql';