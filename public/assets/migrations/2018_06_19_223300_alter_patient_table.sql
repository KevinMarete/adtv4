ALTER TABLE patient
CHANGE clinicalappointment clinicalappointment varchar(20) NULL,
CHANGE differentiated_care differentiated_care tinyint(1) DEFAULT 0//