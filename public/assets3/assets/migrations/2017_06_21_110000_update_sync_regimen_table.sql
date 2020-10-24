UPDATE `sync_regimen` SET `Active` = '1' WHERE `Active` = ''//

UPDATE `sync_regimen` SET `Active` = '0' WHERE `code` IN('AF3A','AF3B','AT1A','AT1B','AT1C','AT2A','CF3A','CF3B','CT1A','CT1B','CT1C','CT2A','PM1','PM2','PC1','PC2','PC4','PC5','PA1B','PA3B','OI4A','OI4C','OI3A','OI3C','CM3N','CM3R','OC3N','OC3R')//

UPDATE `sync_regimen` SET `category_id` = '7' WHERE `category_id` = '9'//