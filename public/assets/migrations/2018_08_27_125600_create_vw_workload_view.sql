DROP VIEW vw_workload//
CREATE VIEW vw_workload AS
SELECT dsm.operator, 
CASE WHEN dsm.drug >= 0 THEN "drug_stock_movement" END AS source_table,
CASE WHEN dsm.drug >= 0 THEN "Inventory" END AS activity,dsm.transaction_date
FROM drug_stock_movement dsm
WHERE dsm.operator !=''
UNION ALL
SELECT pv.user operator, 
CASE WHEN pv.id >= 0 THEN "patient_visit" END AS source_table, 
CASE WHEN pv.id >= 0 THEN "Dispensment" END AS activity , pv.dispensing_date
FROM patient_visit pv
WHERE pv.user !='' //