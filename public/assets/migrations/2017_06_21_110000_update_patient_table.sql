UPDATE patient p 
LEFT JOIN patient_status ps ON ps.id = p.current_status
SET p.drug_prophylaxis = (SELECT id FROM drug_prophylaxis WHERE name LIKE '%cotrimoxazole%')
WHERE ps.Name LIKE '%active%'
AND p.drug_prophylaxis = ''//

UPDATE patient 
SET dob = DATE_FORMAT(dob, '%Y-%m-%d'), 
date_enrolled = DATE_FORMAT(date_enrolled, '%Y-%m-%d'), 
start_regimen_date = DATE_FORMAT(start_regimen_date, '%Y-%m-%d'),
status_change_date = DATE_FORMAT(status_change_date, '%Y-%m-%d'),
nextappointment = DATE_FORMAT(nextappointment, '%Y-%m-%d')//