CREATE TABLE drug_prescription_details (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  drug_prescriptionid bigint(20) NOT NULL,
  drug_name varchar(200) NOT NULL,
  coding_system varchar(200) NOT NULL,
  strength varchar(20) NOT NULL,
  dosage varchar(50) NOT NULL,
  frequency varchar(100) NOT NULL,
  duration varchar(100) NOT NULL,
  quantity_prescribed int(11) NOT NULL,
  prescription_notes varchar(300) NOT NULL,
  PRIMARY KEY (id)
)//
