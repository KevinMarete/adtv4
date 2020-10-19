ALTER TABLE `sync_regimen_category` ADD KEY `ccc_store_sp` (`ccc_store_sp`)//
ALTER TABLE `sync_regimen_category` MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22//
ALTER TABLE `sync_regimen_category` CHANGE  `Active`  `Active` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '1'//