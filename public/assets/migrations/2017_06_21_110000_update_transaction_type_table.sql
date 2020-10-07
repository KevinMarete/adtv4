UPDATE transaction_type t
SET name = 'Adjustment (+)'
WHERE name LIKE '%Ajustment (+)%'//
UPDATE transaction_type t
SET name = 'Adjustment (-)'
WHERE name LIKE '%Ajustment (-)%'//
UPDATE transaction_type t
INNER JOIN  (
	SELECT MAX(id)id , name, COUNT(*) c FROM transaction_type WHERE active = '1' GROUP BY name HAVING c > 1
) t1 ON t1.id = t.id
SET t.active = '0'//