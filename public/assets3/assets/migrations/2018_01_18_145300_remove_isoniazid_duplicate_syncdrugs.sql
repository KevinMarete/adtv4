delete from sync_drug where name like '%iso%' and strength = '300mg' and packsize = 672//
INSERT INTO sync_drug (id, name, abbreviation, strength, packsize, formulation, unit, note, weight, category_id, regimen_id, Active) VALUES
(242,	'Isoniazid (H)',	'',	'300mg',	672,	'Tabs',	'',	'',	999,	1,	0,	'1')//
