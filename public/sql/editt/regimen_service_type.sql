SELECT 
	name,
	1 AS active,
	{destination_store_id} AS ccc_store_sp
FROM service_type
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//