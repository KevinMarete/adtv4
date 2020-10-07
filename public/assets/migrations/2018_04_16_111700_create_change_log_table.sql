CREATE TABLE IF NOT EXISTS change_log (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  patient varchar(20) NOT NULL,
  facility int(32) NOT NULL,
  change_type varchar(20) NOT NULL,
  old_value varchar(50) NOT NULL,
  new_value varchar(50) NOT NULL,
  change_purpose varchar(50) NOT NULL,
  change_date timestamp NOT NULL DEFAULT now(),
  PRIMARY KEY (id),
  KEY patient (patient),
  KEY facility (facility)
)//