SELECT
	d.name AS drug,
	du.name as unit,
	d.pack_size,
	0 AS safety_quantity,
	g.name AS generic_name,
	s.name AS supported_by,
	0 AS classification,
	0 AS tb_drug,
	0 AS drug_in_use,
	dos.name as dose,
	d.duration,
	d.quantity,
	d.strength,
	{destination_store_id} AS ccc_store_sp
FROM drug d 
LEFT JOIN dispensing_unit du ON du.id = d.drug_form_id
LEFT JOIN generic_name g ON g.id = d.generic_name_id
LEFT JOIN dosage dos ON dos.id = d.dosage_id
LEFT JOIN drug_supporting_organization ds ON ds.drug_id = d.id
LEFT JOIN supporting_organization s ON s.id = ds.supporting_organization_id
WHERE d.{migration_flag_column} = {migration_flag_default}
GROUP BY d.id
LIMIT {migration_limit}
OFFSET {migration_offset}//