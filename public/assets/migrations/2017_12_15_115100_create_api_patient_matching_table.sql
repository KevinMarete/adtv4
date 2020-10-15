CREATE TABLE IF NOT EXISTS api_patient_matching (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  internal_id varchar(200) NOT NULL,
  external_id varchar(200) NOT NULL,
  identifier_type char(200) NOT NULL,
  assigning_authority varchar(200) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY external_id (external_id,assigning_authority)
) ENGINE=InnoDB DEFAULT CHARSET=latin1//