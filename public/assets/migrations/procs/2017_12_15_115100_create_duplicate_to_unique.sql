/*Remove Duplicates and make columns unique*/
DROP PROCEDURE IF EXISTS proc_duplicate_to_unique;
DELIMITER  $$
CREATE PROCEDURE proc_duplicate_to_unique(
	IN db_name CHAR(64),
    IN tmp_tbl CHAR(64), 
    IN source_tbl CHAR(64),
    IN source_col CHAR(64),
	IN target_tbl CHAR(64),
	IN target_col CHAR(64)
    )
BEGIN
	/*Get foreign key name*/
	DECLARE const_name, indx_name CHAR(64) DEFAULT NULL;
	SELECT CONSTRAINT_NAME INTO const_name FROM information_schema.KEY_COLUMN_USAGE WHERE constraint_schema = db_name AND table_name = target_tbl AND referenced_table_name = source_tbl;
	SELECT INDEX_NAME INTO indx_name FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = db_name AND TABLE_NAME = source_tbl AND COLUMN_NAME = source_col GROUP BY COLUMN_NAME;
	/*Remove referential integrity checks*/
	SET @@foreign_key_checks = 0;

	/*Drop foreign key*/
	IF (const_name IS NOT NULL) THEN
		SET @drop_fk_key = CONCAT('ALTER TABLE ', target_tbl, ' DROP FOREIGN KEY ', const_name);
		PREPARE stmt FROM @drop_fk_key;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	END IF;

	/*Create tmp_tbl for storing data*/
	SET @create_tmp_tbl = CONCAT('CREATE TEMPORARY TABLE ', tmp_tbl, ' (SELECT * FROM ', source_tbl , ')');
	PREPARE stmt FROM @create_tmp_tbl;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

	/*Alter target_col to varchar*/
	SET @alter_target_col = CONCAT('ALTER TABLE ', target_tbl, ' MODIFY COLUMN ', target_col , ' VARCHAR(150)');
	PREPARE stmt FROM @alter_target_col;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

	/*Update by changing target_col from id to name/description*/
	SET @update_target_col = CONCAT('UPDATE ', target_tbl,' t INNER JOIN ', tmp_tbl , ' tmp ON tmp.id = t.', target_col, ' SET t.', target_col,' = tmp.', source_col);
	PREPARE stmt FROM @update_target_col;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

	/*Truncate source table*/
	SET @truncate_source_tbl = CONCAT('TRUNCATE TABLE ', source_tbl);
	PREPARE stmt FROM @truncate_source_tbl;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

	/*Insert source_tbl with unique values from tmp_tbl*/
	SET @insert_unique_data = CONCAT('INSERT INTO ', source_tbl, ' SELECT * FROM ', tmp_tbl , ' GROUP BY ', source_col);
	PREPARE stmt FROM @insert_unique_data;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    /*Reset auto_increment on id col*/
	SET @num := 0;
	SET @update_auto_id = CONCAT('UPDATE ', source_tbl, ' SET id = @num := (@num+1)');
	PREPARE stmt FROM @update_auto_id;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
	SET @alter_auto_id = CONCAT('ALTER TABLE ', source_tbl, ' AUTO_INCREMENT = 1');
	PREPARE stmt FROM @alter_auto_id;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;    	

	/*Drop source_col to indices*/
	IF (indx_name IS NOT NULL) THEN
		SET @drop_source_col_indx = CONCAT('ALTER TABLE ', source_tbl, ' DROP INDEX ', indx_name);
		PREPARE stmt FROM @drop_source_col_indx;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	END IF;

	/*Alter source_col to UNIQUE*/
	IF (indx_name IS NULL) THEN
		SET indx_name = source_col;
	END IF;
	SET @alter_source_col = CONCAT('ALTER TABLE ', source_tbl, ' ADD UNIQUE ', source_col, ' (', indx_name, ')');
	PREPARE stmt FROM @alter_source_col;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;  

	/*Update target_tbl_data*/
	SET @update_target_data = CONCAT('UPDATE ', target_tbl, ' t INNER JOIN ', source_tbl ,' s ON LOWER(s.', source_col, ') = LOWER(t.', target_col, ') SET t.', target_col,' = s.id');
	PREPARE stmt FROM @update_target_data;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

	/*Alter back target_col to int*/
	SET @alter_target_back = CONCAT('ALTER TABLE ', target_tbl, ' MODIFY COLUMN ', target_col , ' INT(11) NULL');
	PREPARE stmt FROM @alter_target_back;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    /*Add Referential integrity checks*/
	SET @@foreign_key_checks = 1;

END $$
DELIMITER ;