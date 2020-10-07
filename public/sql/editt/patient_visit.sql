SELECT 
	v.id AS migration_id,
	pi.identifier AS patient_id,
	vt.name AS visit_purpose,
	v.height AS current_height,
	v.weight AS current_weight,
	r.code AS regimen,
	rc.name AS regimen_change_reason,
	d.name AS drug_id,
	ti.batch_no AS batch_number,
	i.legacy_pk AS indication,
	0 AS pill_count,
	v.comments AS comment,
	v.created_on AS timestamp,
	u.username AS user,
	{destination_facility_code} AS facility,
	pti.dosage_name AS dose,
	DATE_FORMAT(t.date, '%Y-%m-%d') AS dispensing_date,
	UNIX_TIMESTAMP(t.date) AS dispensing_date_timestamp,
	ti.units_out AS quantity,
	r.code AS last_regimen,
	pti.duration,
	v.pill_count AS months_of_stock,
	v.adherence,
	0 AS missed_pills,
	next_appointment_date AS machine_code,
	{destination_store_id} AS ccc_store_sp
FROM visit v
LEFT JOIN patient p ON p.id = v.patient_id
LEFT JOIN patient_identifier pi ON pi.patient_id = p.id
LEFT JOIN visit_type vt ON vt.id = v.visit_type_id
LEFT JOIN regimen r ON r.id = v.regimen_id
LEFT JOIN regimen_change_reason rc ON rc.id = v.regimen_change_reason_id
INNER JOIN transaction t On t.visit_id = v.id
LEFT JOIN transaction_item ti ON ti.transaction_id = t.id
LEFT JOIN drug d ON d.id = ti.drug_id
LEFT JOIN user u ON u.id = v.created_by
LEFT JOIN patient_transaction_item pti ON pti.transaction_item_id = ti.id
LEFT JOIN indication i ON i.id = t.indication_id
WHERE v.{migration_flag_column} = {migration_flag_default}
GROUP BY v.id
LIMIT {migration_limit}
OFFSET {migration_offset}//