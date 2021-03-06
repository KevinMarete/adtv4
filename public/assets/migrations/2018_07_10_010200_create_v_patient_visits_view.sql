CREATE OR REPLACE VIEW v_patient_visits AS
select pa_vi.id AS patient_visit_id,
pa_vi.patient_id AS patient_id,
pa.id AS id,
pa.medical_record_number AS medical_record_number,
pa.patient_number_ccc AS patient_number_ccc,
pa.first_name AS first_name,
pa.last_name AS last_name,
pa.other_name AS other_name,
pa.dob AS dob,
pa.pob AS pob,
pa.gender AS gender,
pa.pregnant AS pregnant,
pa.weight AS weight,
pa.height AS height,
pa.sa AS sa,
pa.phone AS phone,
pa.physical AS physical,
pa.alternate AS alternate,
pa.other_illnesses AS other_illnesses,
pa.other_drugs AS other_drugs,
pa.adr AS adr,
pa.tb AS tb,
pa.smoke AS smoke,
pa.alcohol AS alcohol,
pa.date_enrolled AS date_enrolled,
pa.source AS source,
pa.supported_by AS supported_by,
pa.timestamp AS timestamp,
pa.facility_code AS facility_code,
pa.service AS service,
pa.start_regimen AS start_regimen,
pa.start_regimen_date AS start_regimen_date,
pa.current_status AS current_status,
pa.migration_id AS migration_id,
pa.machine_code AS machine_code,
pa.sms_consent AS sms_consent,
pa.partner_status AS partner_status,
pa.fplan AS fplan,
pa.tbphase AS tbphase,
pa.startphase AS startphase,
pa.endphase AS endphase,
pa.disclosure AS disclosure,
pa.non_commun AS non_commun,
pa.status_change_date AS status_change_date,
pa.partner_type AS partner_type,
pa.support_group AS support_group,
pa.current_regimen AS current_regimen,
pa.Start_Regimen_Merged_From AS Start_Regimen_Merged_From,
pa.Current_Regimen_Merged_From AS Current_Regimen_Merged_From,
pa.nextappointment AS nextappointment,
pa.start_height AS start_height,
pa.start_weight AS start_weight,
pa.start_bsa AS start_bsa,
pa.transfer_from AS transfer_from,
pa.active AS active,
pa.drug_allergies AS drug_allergies,
pa.tb_test AS tb_test,
pa.pep_reason AS pep_reason,
pa.who_stage AS who_stage,
pa.drug_prophylaxis AS drug_prophylaxis,
g.name AS gender_desc,
pa_vi.visit_purpose AS visit_purpose_id,
vp.name AS visit_purpose_name,
pa_vi.current_height AS current_height,
pa_vi.current_weight AS current_weight,
pa_vi.regimen AS regimen_id,
rst.name AS regimen_service_type,
pa_vi.regimen_change_reason AS regimen_change_reason,
pa_vi.drug_id AS drug_id,
pa_vi.batch_number AS batch_number,
pa_vi.brand AS brand,
pa_vi.indication AS indication,
pa_vi.pill_count AS pill_count,
pa_vi.comment AS comment,
pa_vi.timestamp AS visit_timestamp,
pa_vi.user AS user,
pa_vi.facility AS facility,
pa_vi.dose AS dose,
dose.frequency AS frequency,
pa_vi.dispensing_date AS dispensing_date,
pa_vi.quantity AS quantity,
pa_vi.last_regimen AS last_regimen,
pa_vi.duration AS duration,
pa_vi.months_of_stock AS months_of_stock,
pa_vi.adherence AS adherence,
pa_vi.missed_pills AS missed_pills,
pa_vi.non_adherence_reason AS non_adherence_reason,
pa_vi.merged_from AS merged_from,
pa_vi.regimen_merged_from AS regimen_merged_from,
pa_vi.last_regimen_merged_from AS last_regimen_merged_from,
p_app.appointment AS appointment,
pa_vi.differentiated_care AS differentiated_care,
pa_vi.active AS pv_active 
from ((((((patient_visit pa_vi left join patient pa on((pa.patient_number_ccc = pa_vi.patient_id)))
 left join gender g on((g.id = pa.gender))) 
left join regimen_service_type rst on((pa.service = rst.id))) 
left join visit_purpose vp on((vp.id = pa_vi.visit_purpose))) 
left join patient_appointment p_app on(((p_app.patient = pa_vi.patient_id) and (p_app.appointment = pa_vi.dispensing_date)))) 
left join dose on((convert(pa_vi.dose using utf8) = dose.Name)))