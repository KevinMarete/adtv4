ALTER TABLE adr_form CHANGE id id INT(11) NOT NULL//
ALTER TABLE adr_form ADD report_title VARCHAR(50) NULL AFTER id//
ALTER TABLE adr_form ADD report_type VARCHAR(50) NULL AFTER report_title//
ALTER TABLE adr_form ADD county INT(11) DEFAULT NULL AFTER report_type//
ALTER TABLE adr_form ADD sub_county INT(11) DEFAULT NULL AFTER county//
ALTER TABLE adr_form ADD age_group VARCHAR(50) NULL AFTER sub_county//
ALTER TABLE adr_form ADD date_of_onset_of_reaction VARCHAR(50) NULL AFTER age_group//
ALTER TABLE adr_form ADD synch INT(11) DEFAULT 0 AFTER datecreated//
ALTER TABLE adr_form ADD ppid INT(11) DEFAULT NULL AFTER synch//
ALTER TABLE adr_form CHANGE other_comment other_comment TEXT DEFAULT NULL//
