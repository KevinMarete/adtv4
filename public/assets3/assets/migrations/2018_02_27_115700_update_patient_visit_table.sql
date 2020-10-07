UPDATE patient_visit v
        INNER JOIN
    (SELECT 
        id, dispensing_date
    FROM
        (SELECT 
        MAX(pv.id) AS id, MAX(dispensing_date) AS dispensing_date
    FROM
        patient p, patient_visit pv
    WHERE
        p.differentiated_care = '1'
            AND p.patient_number_ccc = pv.patient_id
    GROUP BY patient_number_ccc
    ORDER BY dispensing_date DESC) AS m2) m USING (id) 
SET 
    differentiated_care = 1//

UPDATE patient_visit v
        INNER JOIN
    (SELECT 
        *
    FROM
        (SELECT 
        patient_id, dispensing_date
    FROM
        patient_visit
    WHERE
        differentiated_care = 1) AS m2) m USING (patient_id , dispensing_date) 
SET 
    differentiated_care = 1//