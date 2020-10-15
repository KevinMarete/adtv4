CREATE TABLE drug_prescription (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  patient varchar(30) NOT NULL,
  order_number bigint(20) NOT NULL,
  order_status varchar(30) NOT NULL,
  order_physician varchar(100) NOT NULL,
  notes varchar(200) NOT NULL,
  timecreated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) //
