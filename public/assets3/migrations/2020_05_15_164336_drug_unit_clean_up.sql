UPDATE drugcode SET unit=1 WHERE `drug` LIKE '%sus%'//
UPDATE drugcode SET unit=1 WHERE `drug` LIKE '%liq%'//
UPDATE drugcode SET unit=1 WHERE `drug` LIKE '%ml%'//
UPDATE drugcode SET unit=2 WHERE `drug` LIKE '%cap%'//
UPDATE drugcode SET unit=4 WHERE `drug` LIKE '%tab%'//
UPDATE drugcode SET unit=5 WHERE `drug` LIKE '%inj%'//
UPDATE drugcode SET strength=3 WHERE unit=1//
UPDATE drugcode SET strength=1 WHERE unit=2//
UPDATE drugcode SET strength=1 WHERE unit=4//
UPDATE drugcode SET strength=3 WHERE unit=5//
UPDATE drugcode SET dose='1OD' WHERE pack_size=30//
UPDATE drugcode SET duration=30 WHERE dose='1OD'//
UPDATE drugcode SET quantity=30 WHERE dose='1OD'//
UPDATE drugcode SET dose='1BD' WHERE pack_size=60//
UPDATE drugcode SET duration=30 WHERE dose='1BD'//
UPDATE drugcode SET quantity=60 WHERE dose='1BD'//
UPDATE drugcode SET dose='2BD' WHERE pack_size=120//
UPDATE drugcode SET duration=30 WHERE dose='2BD'//
UPDATE drugcode SET quantity=120 WHERE dose='2BD'//
UPDATE drugcode SET dose='1OD' WHERE pack_size=84//
UPDATE drugcode SET dose='1OD' WHERE pack_size=90//
UPDATE drugcode SET dose='1OD' WHERE pack_size=100//
UPDATE drugcode SET dose='1OD' WHERE pack_size=672//
UPDATE drugcode SET dose='1OD' WHERE pack_size=1000//