INSERT INTO patient_appointment(patient,appointment,facility)
SELECT pv.patient_id, DATE_FORMAT(pv.machine_code, '%Y-%m-%d'), {destination_facility_code} 
FROM patient_visit pv
WHERE pv.machine_code != 0
AND pv.ccc_store_sp = {destination_store_id}//

UPDATE patient_visit pv
LEFT JOIN visit_purpose v ON pv.visit_purpose = v.name AND v.ccc_store_sp = {destination_store_id}
LEFT JOIN regimen cr ON pv.regimen = cr.regimen_code AND cr.ccc_store_sp = {destination_store_id}
LEFT JOIN regimen_change_purpose rcp ON pv.regimen_change_reason = rcp.name AND rcp.ccc_store_sp = {destination_store_id}
LEFT JOIN drugcode dc ON pv.drug_id = dc.drug AND dc.ccc_store_sp = {destination_store_id}
LEFT JOIN regimen lr ON pv.last_regimen = lr.regimen_code AND lr.ccc_store_sp = {destination_store_id}
SET pv.visit_purpose = v.id, pv.regimen = cr.id, pv.regimen_change_reason = rcp.id, pv.drug_id = dc.id, pv.last_regimen = lr.id, pv.machine_code = 0
WHERE pv.ccc_store_sp = {destination_store_id}//