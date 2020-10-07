UPDATE regimen r 
INNER JOIN regimen_category rc ON rc.Name = r.category AND rc.ccc_store_sp = {destination_store_id}
SET r.category = rc.id
WHERE r.ccc_store_sp = {destination_store_id}//

UPDATE regimen r 
INNER JOIN regimen_service_type rst ON rst.name = r.type_of_service AND rst.ccc_store_sp = {destination_store_id}
SET r.type_of_service = rst.id
WHERE r.ccc_store_sp = {destination_store_id}//