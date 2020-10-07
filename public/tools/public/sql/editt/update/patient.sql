UPDATE patient p
SET p.status_change_date = p.start_regimen_date 
WHERE p.status_change_date = ""
AND p.ccc_store_sp = {destination_store_id}//

UPDATE patient p
INNER JOIN patient_status ps ON p.current_status = ps.Name AND ps.ccc_store_sp = {destination_store_id}
SET p.current_status = ps.id
WHERE p.ccc_store_sp = {destination_store_id}//

UPDATE patient p
INNER JOIN patient_source s ON p.source = s.name AND s.ccc_store_sp = {destination_store_id}
SET p.source = s.id
WHERE p.ccc_store_sp = {destination_store_id}//

UPDATE patient p
INNER JOIN regimen r ON p.start_regimen = r.regimen_code AND r.ccc_store_sp = {destination_store_id}
SET p.start_regimen = r.id
WHERE p.ccc_store_sp = {destination_store_id}//
 	             	       	             	      
UPDATE patient p
INNER JOIN regimen r ON p.current_regimen = r.regimen_code AND r.ccc_store_sp = {destination_store_id}
SET p.current_regimen = r.id
WHERE p.ccc_store_sp = {destination_store_id}//

UPDATE patient p
INNER JOIN supporter s ON s.Name = p.supported_by
SET p.supported_by = s.id
WHERE p.ccc_store_sp = {destination_store_id}//

UPDATE patient p
INNER JOIN regimen_service_type rst ON rst.name = p.service AND rst.ccc_store_sp = {destination_store_id}
SET p.service = rst.id
WHERE p.ccc_store_sp = {destination_store_id}//