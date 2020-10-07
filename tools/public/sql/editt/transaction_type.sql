SELECT
	name, 
	name AS 'desc',
	CASE 
		WHEN name LIKE '%physical%' THEN 1
		WHEN name LIKE '%+%' THEN 1
		WHEN name LIKE '%received %' THEN 1
		WHEN name LIKE '%forward%' THEN 1
		ELSE 0 
	END AS effect,
	{destination_store_id} AS ccc_store_sp
FROM transaction_type
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//