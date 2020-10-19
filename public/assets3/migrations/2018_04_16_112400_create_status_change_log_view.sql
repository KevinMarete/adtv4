 CREATE VIEW status_change_log AS
    SELECT 
        cl.*, ps.Name AS old_status, nps.Name AS new_status
    FROM
        change_log cl
            LEFT JOIN
        patient p ON p.patient_number_ccc = cl.patient
            LEFT JOIN
        patient_status ps ON cl.old_value = ps.id
            LEFT JOIN
        patient_status nps ON cl.new_value = nps.id
    WHERE
        change_type = 'status'//