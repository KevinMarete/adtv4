UPDATE regimen_drug rd
INNER JOIN regimen r ON rd.regimen = r.regimen_code AND r.ccc_store_sp = {destination_store_id}
SET rd.regimen = r.id
WHERE rd.ccc_store_sp = {destination_store_id}//

UPDATE regimen_drug rd
INNER JOIN drugcode dc ON rd.drugcode = dc.drug AND dc.ccc_store_sp = {destination_store_id}
SET rd.drugcode = dc.id
WHERE rd.ccc_store_sp = {destination_store_id}//

UPDATE regimen_drug rd 
SET Merged_From = ''
WHERE rd.ccc_store_sp = {destination_store_id}//