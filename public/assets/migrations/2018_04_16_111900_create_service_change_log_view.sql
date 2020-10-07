CREATE VIEW service_change_log AS
    SELECT 
        cl.*, st.name AS old_service, stn.name AS new_service
    FROM
        change_log cl
            LEFT JOIN
        patient p ON p.patient_number_ccc = cl.patient
            LEFT JOIN
        regimen_service_type st ON cl.old_value = st.id
            LEFT JOIN
        regimen_service_type stn ON cl.new_value = stn.id
    WHERE
        change_type = 'service'//