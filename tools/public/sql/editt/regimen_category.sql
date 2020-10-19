SELECT 
	name AS Name,
	1 AS Active,
	{destination_store_id} AS ccc_store_sp
FROM regimen_type
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//