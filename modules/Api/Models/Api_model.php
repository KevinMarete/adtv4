<?php

namespace Modules\Api\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;
use Modules\ADT\Models\Il_error;
use Modules\ADT\Models\Patient;
use Modules\ADT\Models\PatientStatus;

class Api_model extends BaseModel {

    var $db;

    function __construct() {
        $this->db = \Config\Database::connect();
    }

    function saveAPIConfig($conf) {

        DB::statement("update api_config set value = 'off' where type='toggle'");

        foreach ($conf as $key => $val) {
            DB::statement("UPDATE api_config SET value='$val' WHERE config='$key'");
        }

        return true;
    }

    function savePatientMatching($patient) {
        DB::table('api_patient_matching')->insert($patient);
        return true;
    }

    function savePatient($patient, $external_id) {
        $insert_id = DB::table('patient')->insertGetId($patient);
        return $insert_id;
    }

    function updatePatient($patient, $internal_patient_id) {
        $b = $this->db->table('patient');
        $b->where('id', $internal_patient_id);
        $b->update($patient);

        if ($this->db->affectedRows() > 0) {
            $resultable = true;
        } else {
            $resultable = false;
        }

        return true;
    }

    function updatePatientORU($patient, $internal_patient_id) {
        $update = Patient::find($internal_patient_id);
        if($patient['facility_code'] != $update->facility_code) {
            $this->save_error('Trying to update patient with wrong mflcode ('.$patient['facility_code'].')', $patient['sender']);
            return false;
        }
        if($patient['patient_number_ccc']) {
            $update->patient_number_ccc = $patient['patient_number_ccc'];
        }
        if($patient['pregnant']) {
            $update->pregnant = $patient['pregnant'];
        }
        if($patient['smoke']) {
            $update->smoke = $patient['smoke'];
        }
        if($patient['start_height']) {
            $update->start_height = $patient['start_height'];
        }
        if($patient['start_weight']) {
            $update->start_weight = $patient['start_weight'];
        }
        if($patient['current_status']) {
            $update->current_status = $this->getCurrentStatus($patient['current_status']);
        }
        $update->save();

        return true;
    }

    function getPatient($internal_id = null) {
        $cond = '';
        $query_str = "SELECT p.*,ps.name as patient_status,pso.name as patient_source ,g.name as patient_gender FROM patient p ".
		"left join patient_status ps on p.current_status = ps.id ".
		"left join patient_source pso on p.source = pso.id ".
		"left join gender g on g.id = p.gender ".
		"WHERE p.patient_number_ccc = '".$internal_id."' ";

        // do left join in the case of patient created on adt and not already on IL


        $query = DB::select($query_str);

        if (count($query) > 0) {
            $returnable = $query[0];
        } else {
            $returnable = false;
        }
        return $returnable;
    }

    function getPatientbyID($internal_id = null) {
     
        $cond = '';
        $query_str = "SELECT p.*,ps.name as patient_status,pso.name as patient_source ,g.name as patient_gender FROM patient p
		left join patient_status ps on p.current_status = ps.id 
		left join patient_source pso on p.source = pso.id
		left join gender g on g.id = p.gender

		WHERE p.patient_number_ccc   = '$internal_id' ";

        // do left join in the case of patient created on adt and not already on IL


        $query = $this->db->query($query_str);

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult()[0];
        } else {
            $returnable = 'Not Found';
        }
        return $returnable;
    }

    function getPatientExternalID($internal_id = null, $assigning_authority = null) {
        $cond = (isset($assigning_authority)) ? "and assigning_authority = '$assigning_authority' " : null;
        $query_str = "SELECT external_id as ID, identifier_type as IDENTIFIER_TYPE, assigning_authority as ASSIGNING_AUTHORITY FROM api_patient_matching WHERE internal_id   = '$internal_id' and external_id IS NOT NULL" . $cond;
        $query = $this->db->query($query_str);

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult()[0];
        } else {
            $returnable = false;
            $returnable = array('ID' => '', 'IDENTIFIER_TYPE' => '', 'ASSIGNING_AUTHORITY' => '');
        }
        return $returnable;
    }

    function getPatientInternalID($external_id = null, $assigning_authority = null) {
        $cond = (isset($assigning_authority)) ? "and assigning_authority = '$assigning_authority' " : null;
        $query_str = "SELECT id FROM api_patient_matching WHERE internal_id   = '$internal_id' and external_id IS NOT NULL" . $cond;
        $query = $this->db->query($query_str);

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult()[0];
        } else {
            $returnable = false;
            $returnable = array('ID' => '', 'IDENTIFIER_TYPE' => '', 'ASSIGNING_AUTHORITY' => '');
        }
        return $returnable;
    }

    function getPatientAppointment($appointment_id = null) {

        $sql = "SELECT DATE_FORMAT(MIN(pa.appointment), '%Y%m%d') appointment, pa.facility facility_code, p.*
				FROM patient_appointment pa 
				LEFT JOIN patient p ON p.patient_number_ccc = pa.patient
				WHERE pa.id = '$appointment_id'";
        $query = $this->db->query($sql);

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult()[0];
        } else {
            $returnable = [];
        }
        return $returnable;
    }

    function getDispensing($order_id = null) {

        $sql = "SELECT *, dpd.strength as drug_strength, pv.id visit_id, DATE_FORMAT(timecreated, '%Y%m%d%h%i%s') timecreated, pv.duration disp_duration, TRIM(d.drug) drugcode, pv.quantity disp_quantity, pv.dose disp_dose, dpd.prescription_number
				FROM patient_visit pv 
				INNER JOIN drug_prescription_details_visit dpdv ON dpdv.visit_id = pv.id
				INNER JOIN drug_prescription_details dpd ON dpd.id = dpdv.drug_prescription_details_id
				INNER JOIN drug_prescription dp ON dp.id = dpd.drug_prescriptionid AND pv.patient_id = dp.patient
				INNER JOIN patient p ON p.patient_number_ccc = pv.patient_id
				INNER JOIN drugcode d ON d.id = pv.drug_id
				WHERE dp.id = '$order_id' group by dpd.drug_name ";

        $query = $this->db->query($sql);

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult();
        } else {
            $returnable = [];
        }
        return $returnable;
    }

    function getUsers($merchantemail = null) {

        $query = DB::select("SELECT * FROM user");

        if (count($query) > 0) {
            $returnable = $query;
        } else {
            $returnable = [];
        }
        return $returnable;
    }

    function saveAppointment($appointment, $appointment_type) {
        $appointment_tbl = ($appointment_type == 'CLINICAL') ? 'clinic_appointment' : 'patient_appointment'; // appointment table
        $appointment_col = ($appointment_type == 'CLINICAL') ? 'clinicalappointment' : 'nextappointment'; // appointment column
        $patient = $appointment['patient'];
        $appointment = $appointment['appointment'];

        $query = $this->db->query("update patient set $appointment_col = '$appointment' where patient_number_ccc = '$patient'");
        $insert_id = DB::table($appointment_tbl)->insertGetId($appointment);
        return $insert_id;
    }

    function saveDrugPrescription($prescription, $prescription_details) {
        $pe_details = [];
        $insert_id = DB::table('drug_prescription')->insertGetId($prescription);
        foreach ($prescription_details as $details) {
            # code...
            $pe_details = [
                'drug_prescriptionid' => $insert_id,
                'drug_name' => $details->DRUG_NAME,
                'prescription_number' => $details->PRESCRIPTION_NUMBER,
                'coding_system' => $details->CODING_SYSTEM,
                'strength' => $details->STRENGTH,
                'dosage' => $details->DOSAGE,
                'frequency' => $details->FREQUENCY,
                'duration' => $details->DURATION,
                'quantity_prescribed' => $details->QUANTITY_PRESCRIBED,
                'prescription_notes' => $details->PRESCRIPTION_NOTES
            ];
            DB::table('drug_prescription_details')->insert($pe_details);
        }
        return $pe_details;
    }

    function getRegimen($regimenCode) {
        $query = $this->db->query("SELECT * FROM regimen WHERE lower(regimen_code) = lower('$regimenCode')");

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult()[0];
        } else {
            $returnable = false;
        }
        return $returnable;
    }

    function getActivePatientStatus() {
        $query = $this->db->query("SELECT * FROM patient_status WHERE lower(Name) = 'active'");

        if (count($query->getResult()) > 0) {
            $returnable = $query->getResult()[0];
        } else {
            $returnable = false;
        }
        return $returnable;
    }

    function getCurrentStatus($status) {
        $ps = PatientStatus::query();
        if($status == 'TRANSFER_OUT') {
            $ps = $ps->where('Name', 'Transfer out');
        }
        elseif($status == 'DIED') {
            $ps = $ps->where('Name', 'Deceased');
        }
        elseif($status == 'LOST_TO_FOLLOWUP') {
            $ps = $ps->where('Name', 'Lost to follow-up');
        }
        elseif($status == 'OTHER_REASONS' || $status == 'CANNOT_AFFORD_DRUGS') {
            $ps = $ps->where('Name', 'Other reasons');
        }
        else {
            $ps = $ps->where('Name', 'Unknown');
        }
        $ps = $ps->first();
        return $ps->id ?? null;
    }

    function save_error($error, $sender) {
        Il_error::create([
            'error' => $error,
            'status' => 'unresolved',
            'sending_system' => $sender,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    function getIlDiscontinuationReason($status) {
        $reason = 'UNKNOWN';
        if($status == 2) {
            $reason = 'DIED';
        }
        elseif($status == 5) {
            $reason = 'LOST_TO_FOLLOWUP';
        }
        elseif($status == 6) {
            $reason = 'TRANSFER_OUT';
        }
        elseif($status == 3 || $status == 4 || $status == 7 || $status == 8 || $status == 9 || $status == 10) {
            $reason = 'OTHER_REASONS';
        }
        return $reason;
    }

}
