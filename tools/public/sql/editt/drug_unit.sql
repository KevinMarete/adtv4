SELECT
	 name as Name,
	{destination_store_id} AS ccc_store_sp
FROM dispensing_unit
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}// 