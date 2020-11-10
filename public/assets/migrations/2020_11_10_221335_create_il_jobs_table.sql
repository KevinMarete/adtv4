CREATE TABLE `il_jobs` (
  `id` int(11) NOT NULL,
  `datetime` text DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `attempts` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4//


ALTER TABLE `il_jobs`
  ADD PRIMARY KEY (`id`)//


ALTER TABLE `il_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT//

