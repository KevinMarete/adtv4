DROP PROCEDURE IF EXISTS update_lost_to_follow;
DELIMITER  $$
CREATE PROCEDURE update_lost_to_follow()
BEGIN
	DECLARE lost_to_follow_id INT(1);
	SET lost_to_follow_id = (SELECT id FROM patient_status WHERE Name LIKE '%lost to%' );

update patient set current_status = lost_to_follow_id, status_change_date = CURDATE()  WHERE `nextappointment` = ' ';

END $$
DELIMITER ;