delete from sync_drug WHERE name = 'Nevirapine' AND strength = '10mg/ml' AND packsize = 100 //
delete from sync_drug WHERE name = 'Nevirapine' AND strength = '10mg/ml' AND packsize = 240 //
delete from sync_drug WHERE name = 'Nevirapine' AND strength = '10mg/ml' AND packsize = 60 //
delete from sync_drug WHERE name = 'Lopinavir/Ritonavir' AND strength = '100/25mg' AND packsize = 60 //

insert INTO sync_drug ( name, abbreviation, strength, packsize, formulation, unit, note, weight, category_id, regimen_id, Active) VALUES
('Nevirapine','NVP','10mg/ml',100,'Suspension',NULL,NULL,999,2,0,'1'),
('Nevirapine','NVP','10mg/ml',240,'Suspension',NULL,NULL,999,2,0,'1'),
('Nevirapine','NVP','10mg/ml',60,'Suspension',NULL,NULL,999,2,0,'1'),
('Lopinavir/Ritonavir ','LPV/r','100/25mg',60,'Tablet',NULL,NULL,999,2,0,'1')//