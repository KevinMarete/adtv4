<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Patient extends BaseModel {

    protected $table = 'patient';
    protected $appends = ['name', 'full_name', 'phone_number'];
    protected $guarded = ['id'];

    public function district() {
        return $this->belongsTo(District::class, 'pob', 'id');
    }

    public function gender() {
        return $this->belongsTo(Gender::class, 'gender', 'id');
    }

    public function patient_source() {
        return $this->belongsTo(PatientSource::class, 'source', 'id');
    }

    public function supporter() {
        return $this->belongsTo(Supporter::class, 'pob', 'id');
    }

    public function service() {
        return $this->belongsTo(RegimenServiceType::class, 'service', 'id');
    }

    public function regimen() {
        return $this->belongsTo(Regimen::class, 'start_regimen', 'id');
    }

    public function current_regimen() {
        return $this->belongsTo(Regimen::class, 'current_regimen', 'id');
    }

    public function current_status() {
        return $this->belongsTo(PatientStatus::class, 'current_status', 'id');
    }

    public function facilities() {
        return $this->belongsTo(Facilities::class, 'transfer_from', 'facilitycode');
    }

    public function pep_reason() {
        return $this->belongsTo(PepReason::class, 'pep_reason', 'id');
    }

    public function who_stage() {
        return $this->belongsTo(WhoStage::class, 'who_stage', 'id');
    }

    public function dependant() {
        return $this->belongsTo(Dependant::class, 'patient_number_ccc', 'child');
    }

    public function spouse() {
        return $this->belongsTo(Spouse::class, 'primary_spouse', 'id');
    }

    public function getNameAttribute() {
        return $this->first_name . ' ' . $this->last_name . ' ' . $this->other_name;
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->other_name . ' ' . $this->last_name . ' ' . $this->other_name;
    }

    public function getPhoneNumberAttribute() {
        $number = '';
        if (empty($this->phone))
            $number = $this->alternate;
        else
            $number = $this->phone;
        return $number;
    }

    public static function getEnrollment($period_start, $period_end, $indicator) {
        $adult_age = 15;
        if ($indicator == "adult_male") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'male' AND round(datediff('$period_start',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "adult_female") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_start',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "child_male") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'male' AND round(datediff('$period_start',p.dob)/360)<$adult_age and p.Active='1'";
        } else if ($indicator == "child_female") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_start',p.dob)/360)<$adult_age and p.Active='1'";
        }

        $db = \Config\Database::connect();
        $sql = "
			SELECT ps.Name as source_name,COUNT(*) as total
			FROM patient p
			INNER JOIN patient_source ps ON ps.id = p.source
			INNER JOIN gender g ON g.id = p.gender
			INNER JOIN regimen_service_type rst ON rst.id = p.service
			WHERE p.Date_Enrolled BETWEEN '$period_start' 
			AND '$period_end' 
			$condition
			GROUP BY p.Source
		";
        $query = $db->query($sql);
        return $query->getResultArray();
    }

    public static function getStages($period_start, $period_end, $indicator) {
        $adult_age = 15;
        if ($indicator == "adult_male") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'male' AND round(datediff('$period_start',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "adult_female") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_start',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "child_male") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'male' AND round(datediff('$period_start',p.dob)/360)<$adult_age and p.Active='1'";
        } else if ($indicator == "child_female") {
            $condition = "AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_start',p.dob)/360)<$adult_age and p.Active='1'";
        }

        $db = \Config\Database::connect();
        $sql = "
			SELECT ws.name as stage_name,COUNT(*) as total
			FROM patient p
			INNER JOIN who_stage ws ON ws.id = p.who_stage
			INNER JOIN gender g ON g.id = p.gender
			INNER JOIN regimen_service_type rst ON rst.id = p.service
			WHERE p.Date_Enrolled BETWEEN '$period_start' 
			AND '$period_end' 
			$condition
			GROUP BY p.who_stage
		";
        $query = $db->query($sql);
        return $query->getResultArray();
    }

    public static function getPregnant($period_end, $indicator) {
        $adult_age = 15;
        if ($indicator == "F163") {
            $condition = "AND ps.Name LIKE '%active%' AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_end',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "D163") {
            $condition = "AND ps.Name LIKE '%active%' AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_end',p.dob)/360)<$adult_age and p.Active='1'";
        }

        $db = \Config\Database::connect();
        $sql = "
		SELECT '$indicator' as status_name, COUNT(*) as total
		FROM patient p
		INNER JOIN patient_status ps ON ps.id = p.current_status
		INNER JOIN regimen_service_type rst ON rst.id = p.service
		INNER JOIN gender g ON g.id = p.gender
		WHERE p.Date_Enrolled <='$period_end' AND p.Pregnant='1' 
		$condition
		GROUP BY p.Pregnant
		";

        $query = $db->query($sql);
        return $query->getResultArray();
    }

    public static function getAllArv($period_end, $indicator) {
        $adult_age = 15;
        if ($indicator == "G164") {
            $condition = "AND ps.Name LIKE '%active%' AND rst.Name LIKE '%art%' AND g.name LIKE 'male' AND round(datediff('$period_end',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "F164") {
            $condition = "AND ps.Name LIKE '%active%' AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_end',p.dob)/360)>=$adult_age and p.Active='1'";
        } else if ($indicator == "E164") {
            $condition = "AND ps.Name LIKE '%active%' AND rst.Name LIKE '%art%' AND g.name LIKE 'male' AND round(datediff('$period_end',p.dob)/360)<$adult_age and p.Active='1'";
        } else if ($indicator == "D164") {
            $condition = "AND ps.Name LIKE '%active%' AND rst.Name LIKE '%art%' AND g.name LIKE 'female' AND round(datediff('$period_end',p.dob)/360)<$adult_age and p.Active='1'";
        }

        $db = \Config\Database::connect();
        $sql = "
		SELECT '$indicator' as status_name, COUNT(*) as total
		FROM patient p
		INNER JOIN patient_status ps ON ps.id = p.current_status
		INNER JOIN regimen_service_type rst ON rst.id = p.service
		INNER JOIN gender g ON g.id = p.gender
		WHERE p.Date_Enrolled <='$period_end' AND p.Pregnant !='1' 
		$condition
		";

        $query = $db->query($sql);
        return $query->getResultArray();
    }

    public function get_patient($id = NULL, $columns = NULL) {
        $query = Doctrine_Query::create()->select($columns)->from("Patient p")->where("p.id = ?", array($id));
        $patients = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $patients[0];
    }

    public function get_patient_details($id) {
        $query = Doctrine_Query::create()->select("p.Patient_Number_CCC,CONCAT_WS(' ',first_name,last_name,other_name) as names,Height,Weight,FLOOR(DATEDIFF(CURDATE(),p.dob)/365) as Dob,p.clinicalappointment,Pregnant,Tb,isoniazid_start_date,isoniazid_end_date")->from("Patient p")->where("p.id = $id");
        $patients = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return @$patients[0];
    }

    // Started on ART
    public function start_on_ART() {
        $sql = ("SELECT DATE_FORMAT(p.start_regimen_date,'%M-%Y') as period ,
			                 COUNT( p.patient_number_ccc) AS totalart 
	                         FROM patient p 
	                         LEFT JOIN regimen_service_type rst ON rst.id=p.service 
	                         LEFT JOIN regimen r ON r.id=p.start_regimen 
	                         LEFT JOIN patient_source ps ON ps.id = p.source
	                         WHERE rst.name LIKE '%art%' 
	                        AND ps.name NOT LIKE '%transfer%'
	                         AND p.start_regimen !=''
	                         AND p.start_regimen_date >= '2011-01-01'
	                        GROUP BY YEAR(p.start_regimen_date),MONTH(p.start_regimen_date)
	                        ORDER BY p.start_regimen_date DESC
	                        
	                       ");
        $query = $this->db->query($sql);
        $patients = $query->result_array();
        foreach ($patients as $patient) {
            $data[$patient['period']][] = array('art_patients' => (int) $patient['totalart']);
        }

        return $data;
    }

    // Started on firstline regimen
    public function start_on_firstline() {
        $sql = ("SELECT DATE_FORMAT(p.start_regimen_date,'%M-%Y') as period ,
				         COUNT( p.patient_number_ccc) AS First 
	                     FROM patient p 
	                     LEFT JOIN regimen_service_type rst ON rst.id=p.service 
	                     LEFT JOIN regimen r ON r.id = p.start_regimen 
	                     LEFT JOIN patient_source ps ON ps.id = p.source 
	                     WHERE r.line=1 
	                     AND rst.name LIKE '%art%' 
	                     AND ps.name NOT LIKE '%transfer%'
	                     AND p.start_regimen !=''
	                     AND p.start_regimen_date >= '2011-01-01'
	                     GROUP BY YEAR(p.start_regimen_date),MONTH(p.start_regimen_date)
	                     ORDER BY p.start_regimen_date DESC");

        $query = $this->db->query($sql);
        $patients = $query->result_array();


        foreach ($patients as $patient) {
            $data[$patient['period']][] = array('firstline_patients' => (int) $patient['First']);
        }

        return $data;
    }

    //Still in Firstline
    public function still_in_firstline() {
        $sql = ("SELECT DATE_FORMAT(p.start_regimen_date,'%M-%Y') as period ,COUNT( * ) AS patients_still_firstline
                        FROM patient p
                        LEFT JOIN regimen_service_type rst ON rst.id=p.service
                        LEFT JOIN regimen r ON r.id=p.start_regimen
                        LEFT JOIN regimen r1 ON r1.id = p.current_regimen
                        LEFT JOIN patient_source ps ON ps.id = p.source
                        LEFT JOIN patient_status pt ON pt.id = p.current_status
                        WHERE rst.name LIKE '%art%'
                        AND ps.name NOT LIKE '%transfer%'
                        AND r.line=1
                        AND r1.line ='1'
                        AND p.start_regimen_date !=''
                        AND pt.Name LIKE '%active%'
                        GROUP BY YEAR(p.start_regimen_date),MONTH(p.start_regimen_date)
                        ORDER BY p.start_regimen_date DESC");


        $query = $this->db->query($sql);

        $patients = $query->result_array();

        foreach ($patients as $patient) {
            $data[$patient['period']][] = array('Still_in_Firstline' => (int) $patient['patients_still_firstline']);
        }

        return $data;
    }

    // Started ART 12 months ago
    public function started_art_12months() {
        $to_date = date('Y-m-d', strtotime($start_date . " -1 year"));
        $future_date = date('Y-m-d', strtotime($end_date . " -1 year"));

        $sql = "SELECT COUNT( * ) AS Total_Patients "
                . " FROM patient p "
                . " LEFT JOIN regimen_service_type rst ON rst.id=p.service "
                . " LEFT JOIN regimen r ON r.id=p.start_regimen "
                . " LEFT JOIN patient_source ps ON ps.id = p.source"
                . " WHERE p.start_regimen_date"
                . " BETWEEN '" . $to_date . "'"
                . " AND '" . $future_date . "'"
                . " AND rst.name LIKE  '%art%' "
                . " AND ps.name NOT LIKE '%transfer%'"
                . " AND p.start_regimen !=''";
        $patient_from_period_sql = $this->db->query($sql);
        $total_from_period_array = $patient_from_period_sql->result_array();
        $total_from_period = 0;
        foreach ($total_from_period_array as $value) {
            $total_from_period = $value['Total_Patients'];
        }
    }

    public function get_lost_to_followup() {
        //Get total number of patients lost to follow up 
        $sql = ("SELECT COUNT( p.patient_number_ccc ) AS total_patients_lost_to_follow, rst.name as service_type,
		 DATE_FORMAT(p.status_change_date,'%M-%Y') as period 
                        FROM patient p 
                        LEFT JOIN regimen_service_type rst ON rst.id=p.service 
                        LEFT JOIN regimen r ON r.id=p.start_regimen 
                        LEFT JOIN patient_source ps ON ps.id = p.source 
                        LEFT JOIN patient_status pt ON pt.id = p.current_status
                        WHERE rst.name LIKE '%art%' 
                        AND ps.name NOT LIKE '%transfer%' 
                        AND pt.Name LIKE '%lost%'
                        AND p.status_change_date >= '2011-01-01'
                        AND p.status_change_date!=''
                        GROUP BY YEAR(p.status_change_date),MONTH(p.status_change_date)
                        ORDER BY p.status_change_date DESC");
        $query = $this->db->query($sql);
        $patients = $query->result_array();

        foreach ($patients as $patient) {
            $data[$patient['period']][] = array('lost_to_followup' => (int) $patient['total_patients_lost_to_follow']);
        }

        return $data;
    }

    public function adherence_reports() {
        $ontime = 0;
        $missed = 0;
        $defaulter = 0;
        $lost_to_followup = 0;
        $overview_total = 0;

        $adherence = array(
            'ontime' => 0,
            'missed' => 0,
            'defaulter' => 0,
            'lost_to_followup' => 0
        );
        $sql = ("SELECT 
                    pa.appointment as appointment,
                    pa.patient,
                    IF(UPPER(rst.Name) ='ART','art','non_art') as service,
        		    IF(UPPER(g.name) ='MALE','male','female') as gender,
        		    IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365)<15,'<15', IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365) >= 15 AND FLOOR(DATEDIFF(CURDATE(),p.dob)/365) <= 24,'15_24','>24')) as age
                FROM patient_appointment pa
                LEFT JOIN patient p ON p.patient_number_ccc = pa.patient
                LEFT JOIN regimen_service_type rst ON rst.id = p.service
                LEFT JOIN gender g ON g.id = p.gender 
                WHERE pa.appointment >'2011-01-01'
                GROUP BY pa.patient,pa.appointment
                ORDER BY pa.appointment");
        $query = $this->db->query($sql);
        $patients = $query->result_array();

        #return $patients;

        if ($patients) {
            foreach ($patients as $patient) {
                $appointment = $patient['appointment'];
                $patient = $patient['patient'];
                $sql = ("SELECT 
        		            DATEDIFF('$appointment',pv.dispensing_date) as no_of_days
	                    FROM v_patient_visits pv
	                    WHERE pv.patient_id='$patient'
	                    AND pv.dispensing_date >= '$appointment'
	                    GROUP BY pv.patient_id,pv.dispensing_date
	                    ORDER BY pv.dispensing_date ASC
	                    LIMIT 1");
                $query = $this->db->query($sql);
                $results = $query->result_array();
            }
        }
        return $appointment;
    }

}
