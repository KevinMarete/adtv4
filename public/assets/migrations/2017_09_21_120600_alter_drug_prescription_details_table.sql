CREATE TABLE `api_drug_matching` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `generic_name` varchar(200) NOT NULL,
  `abbreviation` varchar(10) NOT NULL,
  `strength` varchar(50) NOT NULL,
  `formulation` int(30) NOT NULL,
  `drug_id` int NOT NULL
)//