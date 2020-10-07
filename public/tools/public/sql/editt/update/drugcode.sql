UPDATE drugcode d
INNER JOIN drug_unit du ON du.Name = d.unit AND du.ccc_store_sp = {destination_store_id}
SET d.unit = du.id
WHERE d.ccc_store_sp = {destination_store_id}//

UPDATE drugcode d
INNER JOIN generic_name g ON g.name = d.generic_name AND g.ccc_store_sp = {destination_store_id}
SET d.generic_name = g.id
WHERE d.ccc_store_sp = {destination_store_id}//

UPDATE drugcode d
INNER JOIN supporter s ON s.Name = d.supported_by
SET d.supported_by = s.id
WHERE d.ccc_store_sp = {destination_store_id}//