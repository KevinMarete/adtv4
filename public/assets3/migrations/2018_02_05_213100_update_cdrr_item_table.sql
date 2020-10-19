UPDATE cdrr_item ci
INNER JOIN cdrr c ON c.id = ci.cdrr_id
INNER JOIN sync_drug sd ON sd.id = ci.drug_id
SET ci.balance = ROUND(ci.balance/ sd.packsize), ci.received = ROUND(ci.received / sd.packsize), ci.dispensed_units = ROUND(ci.dispensed_units/ sd.packsize), ci.losses = ROUND(ci.losses/ sd.packsize), ci.adjustments = ROUND(ci.adjustments/ sd.packsize), ci.adjustments_neg = ROUND(ci.adjustments_neg/ sd.packsize), ci.count = ROUND(ci.count/ sd.packsize), ci.expiry_quant = ROUND(ci.expiry_quant/ sd.packsize), ci.resupply = ROUND(ci.resupply / sd.packsize) 
WHERE c.code IN ('F-CDRR_packs', 'F-CDRR_units')//