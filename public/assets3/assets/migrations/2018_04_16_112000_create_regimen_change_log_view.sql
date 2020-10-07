 CREATE VIEW regimen_change_log AS
    SELECT 
        cl.*,
        r.regimen_code AS old_regimen,
        rg.regimen_code AS new_regimen,
        rc.name AS regimen_change_purpose
    FROM
        change_log cl
            LEFT JOIN
        patient p ON p.patient_number_ccc = cl.patient
            LEFT JOIN
        regimen r ON cl.old_value = r.id
            LEFT JOIN
        regimen rg ON cl.new_value = rg.id
            LEFT JOIN
        regimen_change_purpose rc ON cl.change_purpose = rc.id
    WHERE
        change_type = 'regimen'//