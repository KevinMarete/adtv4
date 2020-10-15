DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS proc_check_viralload(

	IN in_test_number INT,
	IN in_ccc_number VARCHAR(30), 
	IN in_test_date DATE,
	IN in_result VARCHAR(30),
	IN in_justification TEXT
	
	)
BEGIN
	DECLARE patient_id INT DEFAULT 0;
	IF NOT EXISTS(SELECT * FROM patient_viral_load WHERE test_id = in_test_number)
	THEN
		INSERT INTO patient_viral_load(test_id, patient_ccc_number, test_date, result, justification)
		VALUES(in_test_number, in_ccc_number,in_test_date,in_result,in_justification);
	END IF;
END $$
DELIMITER ;//