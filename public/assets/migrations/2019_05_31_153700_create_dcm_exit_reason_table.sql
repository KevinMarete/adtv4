DROP TABLE IF EXISTS dcm_exit_reason//
CREATE TABLE IF NOT EXISTS dcm_exit_reason (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(150) NOT NULL,
  active int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
)//

INSERT INTO dcm_exit_reason (id, name, active) VALUES
(1,'New co-infection',1),
(2,'Pregnancy',1),
(3,'Poor adherence',1),
(4,'Change in regimen',1),
(5,'Change in BMI below 18.5',1),
(6,'High viral load',1),
(7,'Others',1)//