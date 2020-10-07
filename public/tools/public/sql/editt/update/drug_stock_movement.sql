UPDATE drug_stock_movement dsm
LEFT JOIN drugcode d ON dsm.drug = d.drug AND d.ccc_store_sp = {destination_store_id}
LEFT JOIN transaction_type tt ON dsm.transaction_type = tt.name AND tt.ccc_store_sp = {destination_store_id}
LEFT JOIN users u ON dsm.operator = u.username AND u.ccc_store_sp = {destination_store_id}
SET dsm.drug = d.id, dsm.transaction_type = tt.id, dsm.operator = u.id
WHERE dsm.ccc_store_sp = {destination_store_id}//