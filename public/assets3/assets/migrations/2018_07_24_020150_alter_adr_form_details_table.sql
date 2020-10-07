ALTER TABLE adr_form_details ADD brand INT(11) NULL AFTER adr_id//
ALTER TABLE adr_form_details ADD dose_id INT(11) NULL AFTER brand//
ALTER TABLE adr_form_details ADD route INT(11) NULL AFTER dose_id//
ALTER TABLE adr_form_details ADD frequency INT(11) NULL AFTER route//
