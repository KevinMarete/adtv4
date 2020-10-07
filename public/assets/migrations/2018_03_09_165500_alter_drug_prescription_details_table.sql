ALTER TABLE drug_prescription_details
CHANGE strength strength varchar(20) NULL AFTER coding_system,
CHANGE dosage dosage varchar(50) NULL AFTER strength,
CHANGE frequency frequency varchar(100) NULL AFTER dosage,
CHANGE duration duration varchar(100) NULL AFTER frequency,
CHANGE quantity_prescribed quantity_prescribed int(11) NULL AFTER duration//