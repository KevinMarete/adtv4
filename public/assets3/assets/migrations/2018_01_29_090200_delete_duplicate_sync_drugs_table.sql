
DELETE FROM sync_regimen  WHERE code LIKE '%CF4A%'//
INSERT INTO sync_regimen (name, code, old_code, description, category_id, Active) VALUES
('TDF + 3TC + NVP',	'CF4A',	'',	'Tenofovir + Lamivudine + Nevirapine',	7,	'1')//

DELETE FROM sync_regimen  WHERE code LIKE '%CF4D%'//
INSERT INTO sync_regimen (name, code, old_code, description, category_id, Active) VALUES
('TDF + 3TC + ATV/r',	'CF4D',	'',	'Tenofovir + Lamivudine + Atazanavir/Ritonavir',	7,	'1')//


DELETE FROM sync_regimen  WHERE code LIKE '%CF4B%'//
INSERT INTO sync_regimen (name, code, old_code, description, category_id, Active) VALUES
('TDF + 3TC + EFV',	'CF4B',	'',	'',	7,	'1')//

DELETE FROM sync_regimen  WHERE code LIKE '%CF4C%'//
INSERT INTO sync_regimen (name, code, old_code, description, category_id, Active) VALUES
('TDF + 3TC + LPV/r',	'CF4C',	'',	'Tenofovir + Lamivudine + Lopinavir/Ritonavir',	7,	'1')//