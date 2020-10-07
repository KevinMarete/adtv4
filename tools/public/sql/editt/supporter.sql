SELECT 
	name AS Name,
	1 AS active
FROM supporting_organization
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//