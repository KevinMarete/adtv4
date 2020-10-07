SELECT 
	r.code AS regimen,
	d.name AS drugcode,
	rd.regimen_id AS Merged_From,
	{destination_store_id} AS ccc_store_sp 
FROM regimen_drug rd 
LEFT JOIN regimen r ON r.id = rd.regimen_id
LEFT JOIN drug d ON d.id = rd.drug_id
WHERE rd.{migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//