CREATE TABLE IF NOT EXISTS `sync_regimen_category` (
  `id` int(2) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Active` varchar(2) NOT NULL DEFAULT '1',
  `ccc_store_sp` int(11) NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=latin1//