SELECT
	d.name AS drug,
	DATE_FORMAT(t.date, '%Y-%m-%d') AS transaction_date,
	ti.batch_no AS batch_number,
	tt.name AS transaction_type,
	{destination_facility_code} AS source,
	{destination_facility_code} AS destination,
	0 AS Source_Destination,
	DATE_FORMAT(bti.date_of_expiry, '%Y-%m-%d') expiry_date,
	CASE WHEN bti.no_of_packs > 0 THEN bti.no_of_packs
	ELSE CASE WHEN ti.units_in > 0 THEN ROUND(ti.units_in/d.pack_size) 
	ELSE ROUND(ti.units_out/d.pack_size) END END AS packs,
	ti.units_in AS quantity,
	ti.units_out AS quantity_out, 
	CASE WHEN ti.units_in > 0 THEN ti.units_in
	ELSE ti.units_out END AS balance,
	ti.unit_cost,
	ti.total_cost AS amount,
	t.id AS remarks,
	u.username AS operator,
	t.reference_no AS order_number,
	{destination_facility_code} AS facility,
	UNIX_TIMESTAMP(t.date) AS timestamp,
	{destination_store_id} AS ccc_store_sp
FROM transaction t
LEFT JOIN transaction_item ti ON t.id = ti.transaction_id
LEFT JOIN batch_transaction_item bti ON bti.transaction_item_id = ti.id
LEFT JOIN transaction_type tt ON tt.id = t.transaction_type_id
LEFT JOIN drug d ON d.id = ti.drug_id
LEFT JOIN user u ON u.id = t.created_by
WHERE t.{migration_flag_column} = {migration_flag_default}
GROUP BY t.id
LIMIT {migration_limit}
OFFSET {migration_offset}//