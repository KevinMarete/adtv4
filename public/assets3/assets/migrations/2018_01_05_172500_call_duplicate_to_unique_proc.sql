CALL `proc_duplicate_to_unique`('testadt', 'temp_regimen_category', 'regimen_category', 'name', 'regimen', 'category')//
CALL `proc_duplicate_to_unique`('testadt', 'temp_drug_classification', 'drug_classification', 'name', 'drugcode', 'classification')//
CALL `proc_duplicate_to_unique`('testadt', 'temp_drug_unit', 'drug_unit', 'name', 'drugcode', 'unit')//
CALL `proc_duplicate_to_unique`('testadt', 'temp_drug_instructions', 'drug_instructions', 'name', 'drugcode', 'instructions')//
CALL `proc_duplicate_to_unique`('testadt', 'temp_generic_name', 'generic_name', 'name', 'drugcode', 'generic_name')//
CALL `proc_duplicate_to_unique`('testadt', 'temp_prep_reason', 'prep_reason', 'name', 'patient_prep_test', 'prep_reason_id')//
CALL `proc_duplicate_to_unique`('testadt', 'temp_pep_reason', 'pep_reason', 'name', 'patient', 'pep_reason')//