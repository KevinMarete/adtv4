SELECT
	name AS Name,
	value,
	frequency,
	{destination_store_id} AS ccc_store_sp
FROM dosage
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//