SELECT 
	name AS Name,
	{destination_store_id} AS ccc_store_sp
FROM patient_status
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//