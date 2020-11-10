CREATE TABLE `il_processed_jobs` (
  `id` int(11) NOT NULL,
  `datetime` text DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `il_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4//

--
-- Indexes for dumped tables
--

--
-- Indexes for table `il_processed_jobs`
--
ALTER TABLE `il_processed_jobs`
  ADD PRIMARY KEY (`id`)//

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `il_processed_jobs`
--
ALTER TABLE `il_processed_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT//
