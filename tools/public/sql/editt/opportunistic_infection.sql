SELECT
	name,
	legacy_pk AS indication,
	{destination_store_id} AS ccc_store_sp
FROM indication
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//