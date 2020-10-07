UPDATE regimen r 
LEFT JOIN sync_regimen sr ON sr.code = r.regimen_code
SET r.map = sr.id
WHERE r.map = ''//

UPDATE regimen r
SET r.type_of_service = (SELECT id FROM regimen_service_type WHERE name LIKE '%ART%')
WHERE (r.regimen_code LIKE 'A%' OR r.regimen_code LIKE 'C%')//

UPDATE regimen r
SET r.type_of_service = (SELECT id FROM regimen_service_type WHERE name LIKE '%PEP%')
WHERE (r.regimen_code LIKE 'PA%' OR r.regimen_code IN ('PC1A', 'PC3A', 'PC4X'))//

UPDATE regimen r
SET r.type_of_service = (SELECT id FROM regimen_service_type WHERE name LIKE '%PMTCT%')
WHERE (r.regimen_code LIKE 'PM%' OR r.regimen_code IN ('PC6', 'PC7', 'PC8', 'PC9', 'PC1X'))//

UPDATE regimen r
SET r.type_of_service = (SELECT id FROM regimen_service_type WHERE name LIKE '%OI%')
WHERE (r.regimen_code LIKE 'OC%' OR r.regimen_code LIKE 'OI%')//