CREATE TABLE IF NOT EXISTS sadr_followups (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  county_id int(11) DEFAULT NULL,
  sadr_id int(11) DEFAULT NULL,
  designation_id int(11) DEFAULT NULL,
  description_of_reaction text,
  outcome varchar(100) DEFAULT NULL,
  reporter_email varchar(100) DEFAULT NULL,
  reporter_phone varchar(100) DEFAULT NULL,
  submitted tinyint(2) DEFAULT '0',
  emails tinyint(2) DEFAULT '0',
  active tinyint(1) DEFAULT '1',
  device tinyint(2) DEFAULT '0',
  notified tinyint(2) DEFAULT '0',
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
)//

