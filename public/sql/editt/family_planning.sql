SELECT 
	name 
FROM family_planning_method
WHERE {migration_flag_column} = {migration_flag_default}
LIMIT {migration_limit}
OFFSET {migration_offset}//