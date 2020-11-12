<?php

namespace Modules\ADT\Controllers;

use App\Controllers\BaseController;
use Illuminate\Database\Capsule\Manager as DB;
use Modules\ADT\Models\CCC_store_service_point;
use Modules\ADT\Models\ClinicAppointment;
use Modules\ADT\Models\DCM_change_log;
use Modules\ADT\Models\DCM_exit_reason;
use Modules\ADT\Models\Dose;
use Modules\ADT\Models\Drug_Classification;
use Modules\ADT\Models\Drugcode;
use Modules\ADT\Models\DrugInstructions;
use Modules\ADT\Models\DrugPrescriptionDetails;
use Modules\ADT\Models\DrugPrescrition;
use Modules\ADT\Models\DrugStockBalance;
use Modules\ADT\Models\Facilities;
use Modules\ADT\Models\Non_adherence_reasons;
use Modules\ADT\Models\NonAdherenceReasons;
use Modules\ADT\Models\OpportunisticInfection;
use Modules\ADT\Models\Patient;
use Modules\ADT\Models\Patient_appointment;
use Modules\ADT\Models\PatientPrepTest;
use Modules\ADT\Models\PatientStatus;
use Modules\ADT\Models\PatientVisit;
use Modules\ADT\Models\PrepReason;
use Modules\ADT\Models\Regimen;
use Modules\ADT\Models\Regimen_change_purpose;
use Modules\ADT\Models\RegimenChangePurpose;
use Modules\ADT\Models\RegimenDrug;
use Modules\ADT\Models\Transaction_type;
use Modules\ADT\Models\Visit_purpose;
use Modules\ADT\Models\VisitPurpose;
use Modules\Api\Controllers\Api;
use Mpdf\Mpdf;

class Dispensement_management extends BaseController {

    var $api;
    var $patient_module;
    var $dispense_module;
    var $appointment_module;
    var $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index() {
    }

    public function init_api_values() {
        $sql = "SELECT * FROM api_config";
        $api_config = DB::select($sql);

        $conf = [];
        foreach ($api_config as $ob) {
            $conf[$ob->config] = $ob->value;
        }

        $this->api = ($conf['api_status'] == 'on') ? TRUE : FALSE;
        $this->patient_module = ($conf['api_patients_module'] == 'on') ? TRUE : FALSE;
        $this->dispense_module = ($conf['api_dispense_module'] == 'on') ? TRUE : FALSE;
        $this->appointment_module = ($conf['api_appointments_module'] == 'on') ? TRUE : FALSE;
        $this->api_adt_url = (strlen($conf['api_adt_url']) > 2) ? $conf['api_adt_url'] : FALSE;
    }

    public function get_patient_details() {
        $record_no = $this->post('record_no');
        $facility_code = $this->session->get('facility');
        $sql = "select ps.name as patient_source,p.patient_number_ccc,FLOOR(DATEDIFF(CURDATE(),p.dob)/365) as age from patient p ".
		"LEFT JOIN patient_source ps ON ps.id = p.source ".
		"where p.id='".$record_no."' and facility_code='".$facility_code."'";
        $results = DB::select($sql);
        echo json_encode($results);
    }

    public function get_patient_data($patient_id = NULL) {
        $data = [];
        /* Dispensing information */
        $sql = "SELECT  p.ccc_store_sp, p.patient_number_ccc AS patient_id, ".
		"UPPER(CONCAT_WS(' ', CONCAT_WS(' ', p.first_name, p.other_name), p.last_name)) AS patient_name, ".
		"UPPER(CONCAT_WS(' ', CONCAT_WS(' ', p.first_name, p.other_name), p.last_name)) AS patient_name_link, ".
		"CURDATE() AS dispensing_date, ".
		"p.height AS current_height, ".
		"p.weight AS current_weight, ".
		"p.nextappointment AS appointment_date ".
		"FROM patient p ".
		"WHERE p.id = ?";
        $data = DB::select($sql, [$patient_id]);
        if (!empty($data)) {
            /* Visit information */
            $sql = "SELECT  v.dispensing_date AS prev_visit_date, ".
			"v.last_regimen AS prev_regimen_id, ".
			"d.drug AS prev_drug_name, ".
			"v.quantity AS prev_drug_qty, ".
			"v.drug_id AS prev_drug_id, ".
			"v.dose AS prev_drug_dose, ".
			"v.duration AS prev_duration ".
			"FROM patient_visit v ".
			"LEFT JOIN drugcode d ON d.id = v.drug_id ".
			"WHERE v.patient_id = ? ".
            "AND v.dispensing_date IN (SELECT MAX(dispensing_date) FROM patient_visit WHERE patient_id = ?)";
            
            $visits = DB::select($sql, [$data['patient_id'], $data['patient_id']]);
            $data['prev_visit_data'] = "";
            if (!empty($visits)) {
                foreach ($visits as $visit) {
                    $data['prev_visit_date'] = $visit['prev_visit_date'];
                    $data['last_regimen'] = $visit['prev_regimen_id'];
                    $data['prev_visit_data'] .= "<tr><td>" . $visit['prev_drug_name'] . "</td><td>" . $visit['prev_drug_qty'] . "</td></tr>";
                }
            }
        }
        echo json_encode($data);
    }

    public function dispense($record_no = null) {
        $record_no = $this->uri->getSegment(3);
        $this->init_api_values();

        $facility_code = $this->session->get('facility');

        $dispensing_date = "";
        $data = [];
        $data['api'] = $this->api;
        $data['dispense_module'] = $this->dispense_module;
        $data['appointment_module'] = $this->appointment_module;
        $data['patient_module'] = $this->patient_module;

        $data['last_regimens'] = "";
        $data['visits'] = "";
        $data['appointments'] = "";
        $dispensing_date = date('Y-m-d');

        $sql = "SELECT * FROM facilities where facilitycode='$facility_code'";
        $query = $this->db->query($sql);
        $facility_settings = Facilities::where('facilitycode', $facility_code)->first();

        $data['pill_count'] = @$facility_settings['pill_count'];


        $sql = "select ps.name as patient_source,p.patient_number_ccc,FLOOR(DATEDIFF(CURDATE(),p.dob)/365) as age, LOWER(rst.name) as service_name , p.clinicalappointment from patient p 
		LEFT JOIN patient_source ps ON ps.id = p.source
		LEFT JOIN regimen_service_type rst ON rst.id = p.service
		where p.id='$record_no' and facility_code='$facility_code'
		";
        $results = DB::select($sql);

        if ($results) {
            $patient_no = $results[0]->patient_number_ccc;
            $age = @$results[0]->age;
            $data['age']  = @$results[0]->age+0;
            $service_name = $results[0]->service_name;
            $data['results'] = $results;
        }


        /*         * ********** */
        $data['differentiated_care'] = 0;
        
        $results1 = PatientVisit::where('patient_id', $patient_no)->where('active', '1')->orderBy('dispensing_date', 'desc')->first();
        $dated = '';
        $results = [];
        if ($results1) {
            $dated = $results1->dispensing_date;
            $data['differentiated_care'] = $results1->differentiated_care;
            $sql = "SELECT d.id as drug_id,d.drug,d.dose,d.duration, pv.quantity,pv.dispensing_date,pv.pill_count,r.id as regimen_id,r.regimen_desc,r.regimen_code,pv.months_of_stock as mos,ds.value,ds.frequency
			FROM patient_visit pv
			LEFT JOIN drugcode d ON d.id = pv.drug_id
			LEFT JOIN dose ds ON ds.Name=d.dose
			LEFT JOIN regimen r ON r.id = pv.regimen
			WHERE pv.patient_id =  '$patient_no'
			AND pv.active=1
			AND pv.dispensing_date = '$dated'
			ORDER BY dispensing_date DESC";
            $results = DB::select($sql);
        }


        // /*************/
        $data['prescription'] = [];
        $pid = (isset($_GET['pid'])) ? $_GET['pid'] : null;
        if ($pid && $this->api && $this->dispense_module) {
            $ps_sql = "SELECT dp.*,dpd.*,
			CASE WHEN dpdv.id IS NULL THEN 'not dispensed' ELSE 'dispensed' END as dispense_status
			FROM drug_prescription dp,drug_prescription_details dpd 
			left outer join drug_prescription_details_visit  dpdv on dpdv.drug_prescription_details_id  =dpd.id
			left outer join patient_visit on dpdv.visit_id  = patient_visit.id
			where
			dp.id = dpd.drug_prescriptionid and dp.id = $pid";
            $ps = DB::select($ps_sql);
            $data['prescription'] = $ps;
            // find if possible regimen from prescription
            foreach ($ps as $key => $p) {
                $drugname = $p->drug_name;
                $r_query = Regimen::where('regimen_code', 'like', '%'.$drugname.'%')->first();
                if ($r_query) {
                    $data['prescription_regimen_id'] = $r_query['id'];
                }
            }
        }
        // var_dump($data['prescription']);die;
        //// dispense prescription from EMR



        $data['non_adherence_reasons'] = NonAdherenceReasons::where('active', '1')->get()->toArray();
        $data['regimen_changes'] = RegimenChangePurpose::where('active', '1')->get()->toArray();
        $data['purposes'] = Visit_purpose::getAll($service_name);
        $data['dated'] = $dated;
        $data['patient_id'] = $record_no;
        $data['service_name'] = $service_name;;
        $data['patient_appointment'] = $results;
        $data['hide_side_menu'] = 1;
        $data['content_view'] = "\Modules\ADT\Views\patients\dispense_v";
        $this->base_params($data);
    }

    public function adr($record_no = null) {
        $dated = '';
        $id = $this->db->table('adr_form')->selectMax('id')->get()->getResult();
        $newid =(int) $id[0]->id + 1;
        if ($_POST) {
            $adr = [
                'id'=>$newid,
                'report_title' => $this->post('report_title'),
                'institution_name' => $this->post('institution'),
                'institution_code' => $this->post('institutioncode'),
                'county' => $this->post('county_id'),
                'sub_county' => $this->post('sub_county_id'),
                'address' => $this->post('address'),
                'contact' => $this->post('contact'),
                'patient_name' => $this->post('patientname'),
                'ip_no' => $this->post('ip_no'),
                'dob' => $this->post('dob'),
                'patient_address' => $this->post('patientaddress'),
                'ward_clinic' => $this->post('clinic'),
                'gender' => $this->post('gender'),
                'is_alergy' => $this->post('allergy'),
                'alergy_desc' => $this->post('allergydesc'),
                'is_pregnant' => $this->post('pregnancystatus'),
                'weight' => $this->post('patientweight'),
                'height' => $this->post('patientheight'),
                'diagnosis' => $this->post('diagnosis'),
                'reaction_description' => $this->post('reaction'),
                // 'severity' => (isset($this->post('severity'))) ? $this->post('severity') : false,
                'severity' => $this->post('severity'),
                'action_taken' => $this->post('action'),
                'outcome' => $this->post('outcome'),
                'reaction_casualty' => $this->post('casuality'),
                'other_comment' => $this->post('othercomment'),
                'reporting_officer' => $this->post('officername'),
                'reporting_officer' => $this->post('reportingdate'),
                'email_address' => $this->post('officeremail'),
                'office_phone' => $this->post('officerphone'),
                'designation' => $this->post('designation_id'),
                'signature' => $this->post('officersignature')
            ];
            

            $this->db->table('adr_form')->insert($adr);
            //$adr_id = $this->db->insert_id();
            if (count($_POST['drug_name']) > 0) {

                foreach ($_POST['drug_name'] as $key => $drug) {
                    $adr_details = [
                        'adr_id' => $newid,
                        'drug' => $_POST['drug_name'][$key],
                        'brand' => $_POST['brand_name'][$key],
                        'dose_id' => $_POST['dose_id'][$key],
                        'route' => $_POST['route_id'][$key],
                        'dose' => $_POST['dose'][$key],
                        'route_freq' => $_POST['frequency_id'][$key],
                        'date_started' => $_POST['dispensing_date'][$key],
                        'date_stopped' => $_POST['date_stopped'][$key],
                        'indication' => $_POST['indication'][$key],
                        'suspecteddrug' => (isset($_POST['suspecteddrug'][$key])) ? $_POST['suspecteddrug'][$key] : false,
                        'visitid' => $_POST['visitid'][$key]
                    ];
                    $this->db->table('adr_form_details')->insert($adr_details);
                }
                redirect()->to(base_url().'/inventory_management/adr/');
                
            } else {
                echo "No drugs selected";
                // no drugs selected
                // Form saved successfully
                die;
            }

            die;
        }


        $facility_code = $this->session->get('facility');

        $data = [];
        $dispensing_date = "";
        $data['last_regimens'] = "";
        $data['visits'] = "";
        $data['appointments'] = "";
        $data['uniqueid']=$newid;
        $data['user_full_name'] = $this->session->get('full_name');
        $data['user_email'] = $this->session->get('Email_Address');
        $data['user_phone'] = $this->session->get('Phone_Number');
        // last visit id by patient
        $sql = "select dispensing_date from vw_patient_list vpv,patient_visit pv WHERE pv.patient_id = vpv.ccc_number and vpv.patient_id = $record_no order by dispensing_date desc  limit 1";
        $query = $this->db->query($sql);
        if ($query->getResultArray()) {
            $dispense_date = $query->getResultArray()[0]['dispensing_date'];
        }

        // Facility Details
        $sql = "select * from facilities WHERE facilitycode = $facility_code";
        $query = $this->db->query($sql);
        if ($query->getResultArray()) {
            $data['facility_details'] = $query->getResultArray()[0];
        }

        $sql = "select * from vw_patient_list WHERE patient_id = $record_no";
        $query = $this->db->query($sql);
        if ($query->getResultArray()) {
            $data['patient_details'] = $query->getResultArray()[0];
        }

        //Patient History

        $sql = "select  v_v.dispensing_date, ".
		"v_v.visit_purpose_name AS visit, ".
		"v_v.dose, ".
		"v_v.duration, ".
		"v_v.patient_visit_id AS record_id, ".
		"D.drug, ".
		"v_v.quantity, ".
		"v_v.current_weight, ".
		"R.regimen_desc, ".
		"v_v.batch_number, ".
		"v_v.pill_count, ".
		"v_v.adherence, ".
		"v_v.indication, ".
		"v_v.frequency, ".
		"v_v.user,". 
               " do.value, ".
		"v_v.regimen_change_reason AS regimen_change_reason ".
		"from v_patient_visits as v_v ".
		"INNER JOIN regimen as R ON R.id = v_v.current_regimen ".
		"INNER JOIN drugcode as D ON D.id = v_v.drug_id ".
                "LEFT JOIN dose as do ON do.id = D.unit ".
		"WHERE v_v.id = ".$record_no.
		" AND v_v.pv_active = 1 ".
		"AND dispensing_date = '".$dispense_date."' ".
		"GROUP BY v_v.drug_id,v_v.dispensing_date ".
		"ORDER BY v_v.dispensing_date DESC";

        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
            $data['patient_visits'] = $results;
        } else {
            $data['patient_visits'] = "";
        }

        $dispensing_date = date('Y-m-d');

        $sql = "select ps.name as patient_source,p.patient_number_ccc,FLOOR(DATEDIFF(CURDATE(),p.dob)/365) as age, LOWER(rst.name) as service_name , p.clinicalappointment from patient p 
		LEFT JOIN patient_source ps ON ps.id = p.source
		LEFT JOIN regimen_service_type rst ON rst.id = p.service
		where p.id='$record_no' and facility_code='$facility_code'
		";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();

        if ($results) {
            $patient_no = $results[0]['patient_number_ccc'];
            $age = @$results[0]['age'];
            $service_name = $results[0]['service_name'];
            $data['results'] = $results;
        }


        $sql = "SELECT * FROM patient_visit pv ".
		"left join dose d on pv.dose = d.name ".
		"left join drugcode dc on pv.drug_id = dc.id ".
		"WHERE patient_id = '".$patient_no."' ".
		"ORDER BY dispensing_date DESC";

        $query = $this->db->query($sql);
        $results = $query->getResultArray();

        $username = ($this->session->get('username'));
        $sql = "select ccc_store_sp from users where Username = '$username'";
        $query = $this->db->query($sql);
        $store_results = $query->getResultArray();
        if ($store_results) {
            $data['ccc_store'] = $store_results[0]['ccc_store_sp'];
            // $data['ccc_store'] = $this -> session -> get('ccc_store')[0]['id'];
        }
        $data['diagnosis']= Drug_Classification::all();
        $data['non_adherence_reasons'] = Non_adherence_reasons::where('active', '1')->get()->toArray();
        $data['regimen_changes'] = Regimen_change_purpose::where('active', '1')->get()->toArray();
        $data['purposes'] = Visit_purpose::getAll();
        $data['dated'] = $dated;
        $data['patient_id'] = $record_no;
        $data['service_name'] = $service_name;
        $data['purposes'] = Visit_Purpose::getAll();
        $data['patient_appointment'] = $results;
        $data['hide_side_menu'] = 1;
        $data['content_view'] = "\Modules\ADT\Views\patients/dispense_adr_v";
        $this->base_params($data);
    }

    public function get_prep_reasons() {
        $data = [];
        $reasons = PrepReason::where('active', '1')->get();
        foreach ($reasons as $reason) {
            $data[] = ['text' => $reason->name, 'value' => $reason->id];
        }
        echo json_encode($data);
    }


    public function update_prep_test($patient_id, $prep_reason_id, $is_tested, $test_date, $test_result) {
        $message = '';
        $test_data = [
            'patient_id' => $patient_id,
            'prep_reason_id' => $prep_reason_id,
            'is_tested' => $is_tested,
            'test_date' => $test_date,
            'test_result' => $test_result
        ];
        $prev_test_data = PatientPrepTest::where($test_data)->first();
        if (empty($prev_test_data)) {
            PatientPrepTest::create($test_data);
            $message .= 'Test Result Updated Successfully!<br/>';
        } else {
            $message .= 'Test Result Already Exist!<br/>';
        }
        if ($test_result == TRUE) {
            $message .= 'Switch Patient from PREP to ART service!<br/>';
        }
        echo $message;
    }

    public function get_other_dispensing_details() {
        $data = [];
        $patient_ccc = $this->post("patient_ccc");
        $data['non_adherence_reasons'] = NonAdherenceReasons::where('active', '1')->get()->toArray();
        $data['regimen_changes'] = RegimenChangePurpose::where('active', '1')->get()->toArray();
        $data['dcm_exit_reasons'] = DCM_exit_reason::where('active', '1')->get()->toArray();
        $data['patient_appointment'] = Patient_appointment::where('patient', $patient_ccc)->orderBy('appointment', 'desc')->limit(2)
                                        ->get()->toArray();

        echo json_encode($data);
    }

    public function getPreviouslyDispensedDrugs() {
        $patient_ccc = $this->post("patient_ccc");
        $ccc_id = $this->post("ccc_store");
        $sql = "SELECT d.id as drug_id,d.drug,d.dose,pv.duration, pv.quantity,pv.dispensing_date,pv.pill_count,r.id as regimen_id,r.regimen_desc,r.regimen_code,pv.months_of_stock as mos,ds.value,ds.frequency ".
		"FROM patient_visit pv ".
		"LEFT JOIN drugcode d ON d.id = pv.drug_id ".
		"LEFT JOIN dose ds ON ds.Name=d.dose ".
		"LEFT JOIN regimen r ON r.id = pv.regimen ".
		"WHERE pv.patient_id =  '".$patient_ccc."' ".
		"AND pv.active = 1 ".
		"AND pv.ccc_store_sp = '".$ccc_id."' ".
		"AND pv.dispensing_date = (SELECT MAX(dispensing_date) dispensing_date FROM patient_visit pv WHERE pv.patient_id =  '".$patient_ccc."' AND pv.active=1) ".
		"GROUP BY pv.drug_id,pv.dispensing_date,pv.patient_id ".
		"ORDER BY dispensing_date DESC";
        // echo $sql;
        $results = DB::select($sql);
        echo json_encode($results);
    }

    //Get list of drugs for a specific regimen
    public function getDrugsRegimens() {
        $regimen_id = $this->post('selected_regimen');
        $and_stocktype = "";
        if ($this->post('stock_type')) {
            $stock_type = $this->post('stock_type');
            $and_stocktype = "AND dsb.stock_type = '$stock_type' ";
        }
        $sql = "SELECT DISTINCT(d.id),UPPER(d.drug) as drug,IF(none_arv = 1, FALSE, TRUE) as is_arv ".
		"FROM regimen_drug rd ".
		"LEFT JOIN regimen r ON r.id = rd.regimen ".
		"LEFT JOIN drugcode d ON d.id=rd.drugcode ".
		"WHERE d.enabled='1' ".
		"AND rd.regimen='$regimen_id' ".
        "and rd.active= 1 ".
        "UNION ".

        "SELECT DISTINCT(d.id),UPPER(d.drug) as drug,IF(none_arv = 1, FALSE, TRUE) as is_arv ".
		"FROM regimen_drug rd ".
		"LEFT JOIN regimen r ON r.id = rd.regimen ".
		"LEFT JOIN drugcode d ON d.id=rd.drugcode ".
		"WHERE d.enabled='1' ".
        "AND  r.regimen_code LIKE '%oi%' ".
 		"ORDER BY drug asc";

        $get_drugs_array = DB::select($sql);
        echo json_encode($get_drugs_array);

    }

    public function getBrands() {
        $drug_id = $this->post("selected_drug");
        $get_drugs_sql = "SELECT DISTINCT id,brand FROM brand WHERE drug_id='" . $drug_id . "' AND brand!=''";
        $get_drugs_array = DB::select($get_drugs_sql);
        echo json_encode($get_drugs_array);
    }

    public function getDoses() {
        $get_doses_array = Dose::all()->toArray();
        echo json_encode($get_doses_array);
    }

    public function getDrugDose($drug_id = null) {
        $drug_id = $this->uri->getSegment(3);
        $dose_array = [];
        $facility_code = $this->session->get('facility');
        $weight = $this->post("weight");
        $age = $this->post("age");
        $drug_id = $this->post("drug_id");

        $facility = Facilities::where('facilitycode', $facility_code)->first();
        $adult_age = $facility->adult_age;

        if ($age < $adult_age) {
            $weight_cond = (isset($weight)) ? "and min_weight <= ".$weight." and max_weight >= ".$weight : "";
            $sql = "select drug_id as id,Name as dose,frequency as freq,value from dossing_chart d  inner join dose do on do.id=d.dose_id ".
			"where drug_id=".$drug_id." ".
			$weight_cond.
			" and is_active = 1";
            $dose_array = DB::select($sql);
        }
        if (empty($dose_array) || $age > $adult_age) {
            $get_doses_sql = "SELECT d.id,d.dose,do.frequency as freq,value FROM drugcode d ,dose do where do.Name = d.dose  and d.id='$drug_id'";
            $dose_array = DB::select($get_doses_sql);
        }
        echo json_encode($dose_array);
    }

    public function getFacililtyAge() {
        $facility_code = $this->session->get('facility');
        $get_adult_age_array = Facilities::select('adult_age')->where('facilitycode', $facility_code)->first();
        //echo $facility_code;
        echo json_encode($get_adult_age_array);
    }

    //function to return drugs on the sync_drugs
    public function getMappedDrugCode() {
        $drug_id = $this->post("selected_drug");
        $get_drugcode_array = Drugcode::select('map')->where('id', $drug_id)->first()->toArray();
        echo json_encode($get_drugcode_array);
    }

    public function getIndications() {
        $drug_id = $this->post("drug_id");
        $get_indication_array = [];
        $results = RegimenDrug::whereHas('regimen', function ($query){
            $query->where('regimen_code', 'like', '%oi%');
        })->where('drugcode', $drug_id)->get();
        //if drug is an OI show indications
        if ($results) {
            $get_indication_array = OpportunisticInfection::where('active', '1')->get();
        }
        echo json_encode($get_indication_array);
    }

    public function edit($record_no = null) {
        $record_no = $this->uri->getSegment(3);
        $facility_code = $this->session->get('facility');
        $ccc_id = '2';
        $sql = "select pv.*,p.first_name,p.other_name,p.last_name,p.id as p_id "
                . "from patient_visit pv,"
                . "patient p "
                . "where pv.id='$record_no' "
                . "and pv.patient_id=p.patient_number_ccc "
                . "and facility='$facility_code'";
        $results = DB::select($sql);
        
        if ($results) {
            $data['results'] = $results;
            //Get expriry date the batch
            foreach ($results as $value) {
                $batch_number = $value->batch_number;
                $drug_id = $value->drug_id;
                $ccc_id = $value->ccc_store_sp;

                $expiry_array = DrugStockBalance::where('batch_number', $batch_number)
                                                ->where('drug_id', $drug_id)
                                                ->where('stock_type', $ccc_id)
                                                ->where('facility_code', $facility_code)
                                                ->get();
                $expiry_date = "";
                $data['expiries'] = $expiry_array;
                foreach ($expiry_array as $row) {
                    $expiry_date = $row->expiry_date;
                    $data['original_expiry_date'] = $expiry_date;
                }
            }
        } else {
            $data['results'] = "";
        }
        $data['purposes'] = VisitPurpose::where('Active', '1')->get()->toArray();
        $data['record'] = $record_no;
        $data['ccc_id'] = $ccc_id;
        $data['regimens'] = Regimen::orderBy('regimen_code')->get()->toArray();
        $data['non_adherence_reasons'] = NonAdherenceReasons::where('active', '1')->get()->toArray();
        $data['regimen_changes'] = RegimenChangePurpose::where('active', '1')->get()->toArray();
        $data['doses'] = Dose::where('active', '1')->orderBy('name')->get()->toArray();
        $data['indications'] = OpportunisticInfection::where('active', '1')->get()->toArray();
        $data['content_view'] = '\Modules\ADT\Views\edit_dispensing_v';
        $data['hide_side_menu'] = 1;
        $this->base_params($data);
    }

    public function save() {
        $appointment_id = 0;
        $period = date("M-Y");
        $ccc_id = $this->post("ccc_store_id");
        $this->session->set('ccc_store_id', $ccc_id);
        $record_no = $this->session->get('record_no');
        $patient_name = $this->post("patient_details");
        $next_appointment_date = $this->post("next_appointment_date");
        $differentiated_care = ($this->post("differentiated_care")) ? 1 : 0;
        $next_clinical_appointment_date = $this->post("next_clinical_appointment_date");
        $next_clinical_appointment = $this->post("next_clinical_appointment");
        $prescription = (int) $this->post("prescription");

        $last_appointment_date = $this->post("last_appointment_date");
        $last_appointment_date = date('Y-m-d', strtotime($last_appointment_date));
        $dispensing_date = $this->post("dispensing_date");
        $dispensing_date_timestamp = date('U', strtotime($dispensing_date));
        $facility = $this->session->get("facility");
        $patient = $this->post("patient");
        $height = $this->post("height");
        $current_regimen = $this->post("current_regimen");
        $drugs = $this->post("drug");
        $unit = $this->post("unit");
        $batch = $this->post("batch");
        $expiry = $this->post("expiry");
        $dose = $this->post("dose");
        $duration = $this->post("duration");
        $quantity = $this->post("qty_disp");
        $qty_available = $this->post("soh");
        // $brand = $this->post("brand");

        $soh = $this->post("soh");
        $indication = $this->post("indication");
        $mos = $this->post("next_pill_count");
        //Actual Pill Count
        $pill_count = $this->post("pill_count");
        $comment = $this->post("comment");
        $missed_pill = $this->post("missed_pills");
        $purpose = $this->post("purpose");
        $purpose_refill_text = $this->post('purpose_refill_text');
        $weight = $this->post("weight");
        $last_regimen = $this->post("last_regimen");
        $regimen_change_reason = $this->post("regimen_change_reason");
        $dcm_exit_reason = $this->post("dcm_exit_reason");

        $non_adherence_reasons = $this->post("non_adherence_reasons");
        $patient_source = strtolower($this->post("patient_source"));
        $timestamp = date('U');
        $period = date("Y-m-01");
        $user = $this->session->get("username");
        $adherence = $this->post("adherence");

        $stock_type_text = $this->post("stock_type_text");

        //update service type
        $res = Regimen::find($current_regimen);
        $service = $res->type_of_service;
        $service_results = Patient::where('patient_number_ccc', $patient)->first();
        $dcm_change_id = '';
        $dcm_status ='';
        
        $patient_dcm = DCM_change_log::where('patient', $patient)->orderBy('id', 'desc')->first();
        if($patient_dcm){
            $dcm_change_id  = $patient_dcm->id;
            $dcm_status  = $patient_dcm->status;
        }

        $patient_service = $service_results->service;

        if ($patient_service != $service) {
            Patient::where('service', $patient_service)->where('patient_number_ccc', $patient)->update(
                ['service' => $service]
            );
        }

        if (!$differentiated_care) {
            $next_clinical_appointment_date = $next_appointment_date;
            Patient::where('patient_number_ccc', $patient)->update(
                ['differentiated_care' => '0', 'adherence' => $adherence]
            );
            if($dcm_status == '1'){
                $sql = "UPDATE  dcm_change_log SET status=0 ,end_date = CURDATE(),exit_reason = '$dcm_exit_reason' WHERE id='$dcm_change_id';";
                DCM_change_log::where('id', $dcm_change_id)->update(
                    ['status' => 0, 'end_date' => date('Y-m-d'), 'exit_reason' => $dcm_exit_reason]
                );
            }
        }

        if ($differentiated_care == 1) {
            Patient::where('patient_number_ccc', $patient)->update(
                ['differentiated_care' => '1', 'adherence' => $adherence]
            );

            DCM_change_log::create([
                'status' => 1,
                'start_date' => date('Y-m-d'),
                'patient' => $patient
            ]);
        }

        //end update service type
        //Get transaction type
        $transaction_type = Transaction_type::where('name', 'like', '%dispense%')->where('effect', '0')->first();
        $transaction_type = $transaction_type->id;
        //Source destination
        $source = '';
        $destination = '';
        //Source and destination depending on the stock type
        if (stripos($stock_type_text, 'store')) {
            $source = $facility;
            $destination = '0';
        } elseif (stripos($stock_type_text, 'pharmacy')) {
            $source = $facility;
            $destination = $facility;
        }

        /*
         * Update Appointment Info
         */
        $sql = [];
        $add_query = "";
        //If purpose of refill is start ART, update start regimen and start regimen date
        if ($purpose_refill_text == "start art") {
            $add_query = " , start_regimen = '".$current_regimen."',start_regimen_date = '".$dispensing_date."' ";
        }

        $trans_id = '';
        $status_add = ' ';
        if (stripos($patient_source, 'transit') === 0) {//If patient is on transit, change his status
            $result = PatientStatus::where('name', 'like', '%transit%')->first();
            $add_query .= ", current_status = '".$result->id."' ";
        }

        /// save clinical appointment $ return clinical appointment id then tie it to appointment date
        // if ($next_clinical_appointment_date !== $next_clinical_appointment) {
        $result = ClinicAppointment::where('patient', $patient)->where('appointment', $next_clinical_appointment)->first();
        if(!empty($result) && $result->id > 0){ 
            ClinicAppointment::where('id', $result->id )->update([
                'appointment' => $next_clinical_appointment_date,
                'differentiated_care' => $differentiated_care
            ]);
        }
        else {
            ClinicAppointment::create([
                'patient' => $patient,
                'appointment' => $next_clinical_appointment_date,
                'facility' => $facility,
                'differentiated_care' => $differentiated_care
            ]);
        }

        $result = ClinicAppointment::where('patient', $patient)->where('appointment', $next_clinical_appointment_date)->first();
        $clinical_appointment_id = $result->id;

        // }
        // <!-- save clinical appointment



        if ($last_appointment_date) {
            if ($last_appointment_date > $dispensing_date) {
                //come early for appointment
                $sql[] = "delete from patient_appointment where patient='$patient' and appointment='$last_appointment_date';";
            }
        }
        $sql[] = "insert into patient_appointment (patient,appointment,facility,clinical_appointment) values ('$patient','$next_appointment_date','$facility','$clinical_appointment_id');";

        /*
         * Update patient Info
         */

        $sql[] = "update patient SET weight='$weight',height='$height',current_regimen='$current_regimen',nextappointment='$next_appointment_date',clinicalappointment = '$next_clinical_appointment_date' $add_query where patient_number_ccc ='$patient' and facility_code='$facility';";

        /*
         * Update Visit and Drug Info
         */

        for ($i = 0; $i < sizeof($drugs); $i++) {
            //Get running balance in drug stock movement
            $sql_run_balance = "SELECT machine_code as balance FROM drug_stock_movement WHERE drug ='".$drugs[$i]."' AND ccc_store_sp ='".$ccc_id."' AND expiry_date >=CURDATE() ORDER BY id DESC  LIMIT 1";
            $run_balance_array = DB::select($sql_run_balance);
            if (count($run_balance_array) > 0) {
                $prev_run_balance = $run_balance_array[0]->balance;
            } else {
                //If drug does not exist, initialise the balance to zero
                $prev_run_balance = 0;
            }
            $act_run_balance = $prev_run_balance - $quantity[$i];
            //Get running balance in drug stock movement end ---------

            $remaining_balance = $soh[$i] - $quantity[$i];
            if ($pill_count[$i] == '') {
                $pill_count[$i] = $mos[$i];
            }
            /* if ($mos != "") {//If transaction has actual pill count, actual pill count will pill count + amount dispensed
              $mos[$i] = $quantity[$i] + (int)$mos[$i];
              } */

            //Add visit

            $visit_id = DB::table('patient_visit')->insertGetId([
                'patient_id' => $patient,
                'visit_purpose' => $purpose,
                'current_height' => $height,
                'current_weight' => $weight,
                'regimen' => $current_regimen,
                'regimen_change_reason' => $regimen_change_reason,
                'last_regimen' => $last_regimen,
                'drug_id' => $drugs[$i],
                'batch_number' => $batch[$i],
                // 'brand' => $brand[$i],
                'indication' => $indication[$i],
                'pill_count' => $pill_count[$i],
                'comment' => $comment[$i],
                'timestamp' => $timestamp,
                'user' => $user,
                'facility' => $facility,
                'dose' => $dose[$i],
                'dispensing_date' => $dispensing_date,
                'dispensing_date_timestamp' => $dispensing_date_timestamp,
                'quantity' => $quantity[$i],
                'duration' => $duration[$i],
                'adherence' => $adherence,
                'missed_pills' => $missed_pill[$i],
                'non_adherence_reason' => $non_adherence_reasons,
                'months_of_stock' => $mos[$i],
                'ccc_store_sp' => $ccc_id,
                'differentiated_care' => $differentiated_care
            ]);

            $regimen_change_query = " insert into change_log (old_value,new_value,facility,patient,change_purpose,change_type)
            select '" . $last_regimen . "' 
            ,'" . $current_regimen . "'
            ,'" . $facility . "'
            ,'" . $patient . "'
            ,'" . $regimen_change_reason . "','regimen' where '" . $current_regimen . "' != '" . $last_regimen . "'";

            DB::statement($regimen_change_query);


            if ($prescription > 0) {
                //Check Regimen Drug Table to figure out which drug is ART/OI
                $chk_reg_drug_sql = "SELECT 1 FROM regimen_drug WHERE regimen = '$current_regimen' AND drugcode = '$drugs[$i]'";
                $chk_result = DB::select($chk_reg_drug_sql);
                if ($chk_result) {
                    //Is an ARV
                    $this->db->table('drug_prescription_details_visit')->insert(array('drug_prescription_details_id' => $this->getPrescription($prescription)['arv_prescription'], 'visit_id' => $visit_id));
                } else {
                    //Is an OI
                    $this->db->table('drug_prescription_details_visit')->insert(array('drug_prescription_details_id' => $this->getPrescription($prescription)['oi_prescription'], 'visit_id' => $visit_id));
                }
            }


            $sql[] = "insert into drug_stock_movement (drug, transaction_date, batch_number, transaction_type,source,destination,expiry_date,quantity, quantity_out,balance, facility,`timestamp`,machine_code,ccc_store_sp) VALUES ('$drugs[$i]','$dispensing_date','$batch[$i]','$transaction_type','$source','$destination','$expiry[$i]',0,'$quantity[$i]',$remaining_balance,'$facility','$dispensing_date_timestamp','$act_run_balance','$ccc_id');";
            $sql[] = "update drug_stock_balance SET balance=balance - '$quantity[$i]' WHERE drug_id='$drugs[$i]' AND batch_number='$batch[$i]' AND expiry_date='$expiry[$i]' AND stock_type='$ccc_id' AND facility_code='$facility';";
            $sql[] = "INSERT INTO drug_cons_balance(drug_id,stock_type,period,facility,amount,ccc_store_sp) VALUES('$drugs[$i]','$ccc_id','$period','$facility','$quantity[$i]','$ccc_id') ON DUPLICATE KEY UPDATE amount=amount+'$quantity[$i]';";
            $sql[] = "UPDATE patient p JOIN patient_visit pv on p.patient_number_ccc = pv.patient_id JOIN drugcode dc on  pv.drug_id = dc.id SET p.isoniazid_start_date  = pv.dispensing_date , p.isoniazid_end_date = pv.dispensing_date + INTERVAL 168 DAY, drug_prophylaxis = concat(drug_prophylaxis ,',',(select id from drug_prophylaxis where  name like '%iso%')) WHERE dc.drug LIKE '%iso%'  and p.isoniazid_start_date IS NULL AND pv.patient_id  = '$patient';";
            $sql[] ="UPDATE patient p  JOIN patient_visit pv on p.patient_number_ccc = pv.patient_id  JOIN drugcode dc on  pv.drug_id = dc.id SET drug_prophylaxis = concat(drug_prophylaxis ,',',(select id from drug_prophylaxis where  lower(name) like '%cotri%')) WHERE lower(dc.drug) LIKE '%cotri%'   AND pv.patient_id  = '$patient';";
            $sql[] ="UPDATE patient p  JOIN patient_visit pv on p.patient_number_ccc = pv.patient_id  JOIN drugcode dc on  pv.drug_id = dc.id SET drug_prophylaxis = concat(drug_prophylaxis ,',',(select id from drug_prophylaxis where  lower(name) like '%rifap%')) WHERE lower(dc.drug) LIKE '%Rifapentine/Isoniazid%'   AND pv.patient_id  = '$patient';";
        }

        // $queries = explode(";", $sql);
        // $count = count($queries);
        // $c = 0;
        foreach ($sql as $query) {
            //$c++;
            //if (strlen($query) > 0) {
            DB::statement($query);
            //}
        }


        if (isset($prescription)) {
            // fetch appointment_id
            $q = "SELECT id FROM patient_appointment WHERE patient = '$patient' AND appointment = '$next_appointment_date' LIMIT 1";
            $query = $this->db->query($q);
            $result = Patient_appointment::where('patient', $patient)->where('appointment', $next_appointment_date)->first();
            $appointment_id = $result->id;

            if ($this->api && $this->dispense_module) {
                // post to IL via API
                $api = new Api();
                $api->getDispensing($prescription);
                $api->getAppointment($appointment_id);
                // /> POST TO IL VIA API
            }

            //file_get_contents(base_url() . 'tools/api/getdispensing/' . $prescription);
            //file_get_contents(base_url() . 'tools/api/getappointment/' . $appointment_id);
        }

        $this->session->set('msg_save_transaction', 'success');
        $this->session->setFlashdata('dispense_updated', 'Dispensing to patient No. ' . $patient . ' successfully completed!');
        return redirect()->to(base_url()."/patients");
    }

    public function save_edit() {
        $timestamp = "";
        $patient = "";
        $facility = "";
        $user = "";
        $record_no = "";
        $soh = $this->post("soh");
        //Get transaction type
        $transaction_type = Transaction_type::where('name', 'like', '%dispense%')->where('effect', '0')->first();
        $transaction_type = $transaction_type->id;
        $transaction_type1 = Transaction_type::where('name', 'like', '%returns%')->where('effect', '1')->first();
        $transaction_type1 = $transaction_type1->id;
        $original_qty = @$_POST["qty_hidden"];
        $facility = $this->session->get("facility");
        $user = $this->session->get("full_name");
        $timestamp = date('Y-m-d H:i:s');
        $patient = @$_POST['patient'];
        $expiry_date = @$_POST['expiry'];
        $ccc_id = @$_POST["ccc_id"];
        $differentiated_care = ($this->post("differentiated_care") =='on') ? 1 : 0 ;

        //Define source and destination
        $source = $facility;
        $destination = $facility;

        //Get ccc_store_name 
        $ccc_store = CCC_store_service_point::where('id', $ccc_id)->where('active', '1')->first();
        $ccc_name = $ccc_store->name;

        if (stripos($ccc_name, 'store')) {
            $source = $facility;
            $destination = '';
        }

        //Get running balance in drug stock movement
        $sql_run_balance = "SELECT machine_code as balance FROM drug_stock_movement WHERE drug ='" . @$_POST['original_drug'] . "' AND ccc_store_sp ='$ccc_id' AND expiry_date >=CURDATE() ORDER BY id DESC  LIMIT 1";
        $run_balance_array = DB::select($sql_run_balance);
        if (count($run_balance_array) > 0) {
            $prev_run_balance = $run_balance_array[0]->balance;
        } else {
            //If drug does not exist, initialise the balance to zero
            $prev_run_balance = 0;
        }

        //Get running balance in drug stock movement end ---------
        //If record is to be deleted
        if (@$_POST['delete_trigger'] == 1) {
            $sql = "update patient_visit set active='0' WHERE id='" . @$_POST["dispensing_id"] . "';";
            DB::statement($sql);
            $bal = $soh + @$_POST["qty_disp"];

            $act_run_balance = $prev_run_balance + @$_POST["qty_disp"]; //Actual running balance		
            //If deleting previous transaction, check if batch has not expired, if not, insert in drug stock balance table
            $today = strtotime(date("Y-m-d"));
            $original_expiry = strtotime(@$_POST["original_expiry_date"]);
            if ($today <= $original_expiry) {
                //If balance for this batch is greater than zero, update stock, otherwise, insert in drug stock balance
                $sql_batch_balance = "SELECT balance FROM drug_stock_balance WHERE drug_id='" . @$_POST["original_drug"] . "' AND batch_number='" . @$_POST["batch"] . "' AND expiry_date='" . @$_POST["original_expiry_date"] . "' AND stock_type='$ccc_id' AND facility_code='$facility'";
                $res = DB::select($sql_batch_balance);
                $prev_batch_balance = "";
                if ($res) {
                    $prev_batch_balance = $res[0]->balance;
                }
                if ($prev_batch_balance > 0) {
                    //Update drug_stock_balance
                    $sql = "UPDATE drug_stock_balance SET balance=balance+" . @$_POST["qty_disp"] . " WHERE drug_id='" . @$_POST["original_drug"] . "' AND batch_number='" . @$_POST["batch"] . "' AND expiry_date='" . @$_POST["original_expiry_date"] . "' AND stock_type='$ccc_id' AND facility_code='$facility'";
                    DB::statement($sql);
                } else {

                    $sql = "INSERT INTO drug_stock_balance (balance,dug_id,batch_number,expiry_date,stock_type,facility_code) VALUES('" . @$_POST["qty_disp"] . "','" . @$_POST["original_drug"] . "','" . @$_POST["batch"] . "','" . @$_POST["original_expiry_date"] . "','$ccc_id','$facility')";
                    DB::statement($sql);
                }
            }


            //Insert in drug stock movement
            //Get balance after update
            $sql = "SELECT balance FROM drug_stock_balance WHERE drug_id='" . @$_POST["original_drug"] . "' AND batch_number='" . @$_POST["batch"] . "' AND expiry_date='" . @$_POST["original_expiry_date"] . "' AND stock_type='$ccc_id' AND facility_code='$facility'";
            $results = DB::select($sql);
            $actual_balance = $results[0]->balance;
            $sql = "INSERT INTO drug_stock_movement (drug, transaction_date, batch_number, transaction_type,source,destination,source_destination,expiry_date, quantity, balance, facility, machine_code,timestamp,ccc_store_sp) SELECT '" . @$_POST["original_drug"] . "','" . @$_POST["original_dispensing_date"] . "', '" . @$_POST["batch"] . "','$transaction_type1','$source','$destination','Dispensed To Patients','$expiry_date','" . @$_POST["qty_disp"] . "','" . @$actual_balance . "','$facility','$act_run_balance','$timestamp','$ccc_id' from drug_stock_movement WHERE batch_number= '" . @$_POST["batch"] . "' AND drug='" . @$_POST["original_drug"] . "' LIMIT 1;";
            DB::statement($sql);

            //Update drug consumption
            $period = date('Y-m-01');
            $sql = "UPDATE drug_cons_balance SET amount=amount-" . $original_qty . " WHERE drug_id='" . @$_POST["original_drug"] . "' AND stock_type='$ccc_id' AND period='$period' AND facility='$facility'";
            DB::statement($sql);

            $this->session->set('dispense_deleted', 'success');
        } else {//If record is edited
            $period = date('Y-m-01');
            $sql = "UPDATE patient_visit SET dispensing_date = '" . @$_POST["dispensing_date"] . "', visit_purpose = '" . @$_POST["purpose"] . "', current_weight='" . @$_POST["weight"] . "', current_height='" . @$_POST["height"] . "', regimen='" . @$_POST["current_regimen"] . "', drug_id='" . @$_POST["drug"] . "', batch_number='" . @$_POST["batch"] . "', dose='" . @$_POST["dose"] . "', duration='" . @$_POST["duration"] . "', quantity='" . @$_POST["qty_disp"] . "', brand='" . @$_POST["brand"] . "', indication='" . @$_POST["indication"] . "', pill_count='" . @$_POST["pill_count"] . "', missed_pills='" . @$_POST["missed_pills"] . "', comment='" . @$_POST["comment"] . "',non_adherence_reason='" . @$_POST["non_adherence_reasons"] . "',adherence='" . @$_POST["adherence"] . "',differentiated_care='" . @$differentiated_care . "' WHERE id='" . @$_POST["dispensing_id"] . "';";
            DB::statement($sql);
            if (@$_POST["batch"] != @$_POST["batch_hidden"] || @$_POST["qty_disp"] != @$_POST["qty_hidden"]) {
                //Update drug_stock_balance
                //Balance=balance+(previous_qty_disp-actual_qty_dispense)
                $bal = $soh;
                //New qty dispensed=old qty - actual qty dispensed
                $new_qty_dispensed = $_POST["qty_hidden"] - $_POST["qty_disp"];
                $act_run_balance = $prev_run_balance - $_POST["qty_disp"];
                //If new quantity dispensed is less than qty previously dispensed
                //echo $new_qty_dispensed;die();
                if ($new_qty_dispensed > 0) {
                    $bal = $soh + $new_qty_dispensed;
                    $sql = "UPDATE drug_stock_balance SET balance=balance+" . @$new_qty_dispensed . " WHERE drug_id='" . @$_POST["original_drug"] . "' AND batch_number='" . @$_POST["batch"] . "' AND expiry_date='" . @$_POST["original_expiry_date"] . "' AND stock_type='$ccc_id' AND facility_code='$facility'";
                    DB::statement($sql);

                    //Update drug consumption
                    $sql = "UPDATE drug_cons_balance SET amount=amount-" . $new_qty_dispensed . " WHERE drug_id='" . @$_POST["original_drug"] . "' AND stock_type='$ccc_id' AND period='$period' AND facility='$facility'";
                    DB::statement($sql);
                } else if ($new_qty_dispensed < 0) {
                    $bal = $soh - $new_qty_dispensed;
                    $new_qty_dispensed = abs($new_qty_dispensed);
                    $sql = "UPDATE drug_stock_balance SET balance=balance-" . @$new_qty_dispensed . " WHERE drug_id='" . @$_POST["original_drug"] . "' AND batch_number='" . @$_POST["batch"] . "' AND expiry_date='" . @$_POST["original_expiry_date"] . "' AND stock_type='$ccc_id' AND facility_code='$facility'";
                    DB::statement($sql);

                    //Update drug consumption
                    $sql = "UPDATE drug_cons_balance SET amount=amount+" . $new_qty_dispensed . " WHERE drug_id='" . @$_POST["original_drug"] . "' AND stock_type='$ccc_id' AND period='$period' AND facility='$facility'";
                    DB::statement($sql);
                }
                //Balance after returns
                $bal1 = $soh + $original_qty;
                $act_run_balance1 = $prev_run_balance + $original_qty; //Actual running balance
                $act_run_balance = $act_run_balance + $original_qty;
                //Returns transaction
                $sql = "INSERT INTO drug_stock_movement (drug, transaction_date, batch_number, transaction_type,source,destination,source_destination,expiry_date, quantity,balance, facility, machine_code,timestamp,ccc_store_sp) SELECT '" . @$_POST["original_drug"] . "','" . @$_POST["original_dispensing_date"] . "', '" . @$_POST["batch_hidden"] . "','$transaction_type1','$source','$destination','Dispensed To Patients',expiry_date,'" . @$_POST["qty_hidden"] . "','$bal1','$facility','$act_run_balance1','$timestamp','$ccc_id' from drug_stock_movement WHERE batch_number= '" . @$_POST["batch_hidden"] . "' AND drug='" . @$_POST["original_drug"] . "' LIMIT 1;";
                DB::statement($sql);
                //Dispense transaction
                $sql = "INSERT INTO drug_stock_movement (drug, transaction_date, batch_number, transaction_type,source,destination,expiry_date, quantity_out,balance, facility, machine_code,timestamp,ccc_store_sp) SELECT '" . @$_POST["drug"] . "','" . @$_POST["original_dispensing_date"] . "', '" . @$_POST["batch"] . "','$transaction_type','$source','$destination',expiry_date,'" . @$_POST["qty_disp"] . "','$bal','$facility','$act_run_balance','$timestamp','$ccc_id' from drug_stock_movement WHERE batch_number= '" . @$_POST["batch"] . "' AND drug='" . @$_POST["drug"] . "' LIMIT 1;";
                DB::statement($sql);
            }
            $this->session->set('dispense_updated', 'success');
        }
        $sql = "select * from patient where patient_number_ccc='$patient' and facility_code='$facility'";
        $results = DB::select($sql);
        $record_no = $results[0]->id;
        $this->session->set('msg_save_transaction', 'success');
        return redirect()->to(base_url("/patient_management/load_view/details/$record_no"));
    }

    public function drugAllergies() {
        $drug = $this->post("selected_drug");
        $patient_no = $this->post("patient_no");
        $allergies = Patient::where('patient_number_ccc', $patient_no)->first();
        @$drug_list = explode(",", @$allergies->adr);
        $is_allergic = 0;
        foreach ($drug_list as $value) {
            if ($value != '') {
                $value = str_ireplace("-", "", $value);
                if ($drug == $value) {
                    $is_allergic = 1;
                }
            }
        }
        echo $is_allergic;
    }

    public function print_test() {
        $check_if_print = @$this->post("print_check");
        $no_to_print = $this->post("print_count");
        $drug_name = $this->post("print_drug_name");
        $qty = $this->post("print_qty");
        $drug_unit = $this->post("print_drug_unit");
        $dose_value = $this->post("print_dose_value");
        $dose_frequency = $this->post("print_dose_frequency");
        $dose_hours = $this->post("print_dose_hours");
        $drug_instructions = $this->post("print_drug_info");
        $patient_name = $this->post("print_patient_name");
        $pharmacy_name = $this->post("print_pharmacy");
        $dispensing_date = $this->post("print_date");
        $facility_name = $this->post("print_facility_name");
        $facility_phone = $this->post("print_facility_phone");
        $str = "";

        //MPDF Config
        $mode = 'utf-8';
        $format = array(88.9, 38.1);
        $default_font_size = '9';
        $default_font = 'Segoe UI';
        $margin_left = '2';
        $margin_right = '2';
        $margin_top = '4';
        $margin_bottom = '2';
        $margin_header = '';
        $margin_footer = '';
        $orientation = 'P';

        $this->mpdf = new Mpdf([$mode, $format, $default_font_size, $default_font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation]);

        if ($check_if_print) {
            //loop through checkboxes check if they are selected to print
            foreach ($check_if_print as $counter => $check_print) {
                //selected to print
                if ($check_print) {
                    //count no. to print
                    $count = 1;
                    while ($count <= $no_to_print[$counter]) {
                        $this->mpdf->addPage();
                        $str = '<table border="1"  style="border-collapse:collapse;font-size:9px;">';
                        $str .= '<tr>';
                        $str .= '<td colspan="2">Drugname: <b>' . strtoupper($drug_name[$counter]) . '</b></td>';
                        $str .= '<td>Qty: <b>' . $qty[$counter] . '</b></td>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<td colspan="3">';
                        $str .= '<b>' . $dose_value[$counter] . ' ' . $drug_unit[$counter] . '</b> to be taken <b>' . $dose_frequency[$counter] . '</b> a day after every <b>' . $dose_hours[$counter] . '</b> hours</td>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<td colspan="3">Before/After Meals: ';
                        $str .= '<b>' . $drug_instructions[$counter] . '</b></td>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<td>Patient Name: <b>' . $patient_name . '</b> </td><td> Pharmacy :<b>' . $pharmacy_name[$counter] . '</b> </td> <td>Date:<b>' . $dispensing_date . '</b></td>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<td colspan="3" style="text-align:center;">Keep all medicines in a cold dry place out of reach of children.</td></tr>';
                        $str .= '<tr><td colspan="2">Facility Name: <b>' . $this->session->get("facility_name") . '</b></td><td> Facility Phone: <b>' . $this->session->get("facility_phone") . '</b>';
                        $str .= '</td>';
                        $str .= '</tr>';
                        $str .= '</table>';
                        //write to page
                        $this->mpdf->WriteHTML($str);
                        $count++;
                    } //end while
                }//end if
            } //end foreach
            $file_name = 'assets/download/' . $patient_name . '(Labels).pdf';
            $this->mpdf->Output($file_name, 'F');
            echo base_url() .'/'. $file_name;
            // return $this->response->download($file_name, null);
        } else {
            echo 0;
        }
    }

    public function getInstructions($drug_id = null) {
        $drug_id = $this->uri->getSegment(3);
        $instructions = "";
        $results = Drugcode::find($drug_id);
        if ($results) {
            //get values
            $values = $results->instructions;
            //get instruction names
            if ($values != "") {
                $values = explode(",", $values);
                foreach ($values as $value) {
                    $results = DrugInstructions::find($value);
                    if ($results) {
                        foreach ($results as $result) {
                            $instructions .= $result->name . "\n";
                        }
                    }
                }
            }
        }
        echo ($instructions);
    }

    public function getPrescriptions($patient_ccc = null) {
        $prescription = [];
        $sql = "SELECT * FROM drug_prescription WHERE patient = '$patient_ccc' ORDER BY id DESC LIMIT 1";
        $results = DB::select($sql);
        if ($results) {
            $sql = "SELECT * FROM drug_prescription_details WHERE drug_prescriptionid =" . $results[0]->id;
            $res = DB::select($sql);
            if ($res) {
                $prescription = (array) $results[0];
                $prescription['prescription_details'] = $res;
            }
        }
        header('Content-Type: application/json');
        echo json_encode($prescription);
        die;
    }

    public function getPrescription($pid) {
        $data = [];
        // $ps_sql = "SELECT dpd.id,drug_prescriptionid,drug_name from drug_prescription dp,drug_prescription_details dpd where ".
		// "dp.id = dpd.drug_prescriptionid and dp.id = ".$pid;
        $ps = DrugPrescriptionDetails::with('drug_prescription')->where('drug_prescriptionid', $pid)->get();
        // $ps = (array) DB::select($ps_sql);
        $data = $ps;
     
        // find if possible regimen from prescription
        foreach ($ps as $key => $p) {
            $drugname = $p->drug_name;
            $rs = Regimen::where('regimen_code', 'like', '%'.$drugname.'%')->first();
            if ($rs) {
                $data[$key]->prescription_regimen_id = $rs->id;
                $arv_prescription = $p->id;
                $data['arv_prescription'] = $arv_prescription;
                //Get oi_prescription(s)
                // $sql = "SELECT dpd.id from drug_prescription dp,drug_prescription_details dpd where ".
                // "dp.id = dpd.drug_prescriptionid and dp.id = ".$pid." and dpd.id != '".$arv_prescription."'";
                $query = DrugPrescriptionDetails::whereHas('drug_prescription', function($query) use ($pid) {
                    $query->where('id', $pid);
                })->where('id', '!=', $arv_prescription)->first();
                // $query = DB::select($sql);
                $data['oi_prescription'] = $query->id ?? null;
            }
        }
        return $data;
    }

    public function base_params($data) {
        $data['title'] = "webADT | Drug Dispensing";
        $data['banner_text'] = "Facility Dispensing";
        $data['link'] = "/dispensements";
        echo view('\Modules\ADT\Views\template', $data);
    }

    public function save_session() {
        $session_name = $this->post("session_name");
        $session_value = $this->post("session_value");
        $this->session->set($session_name, $session_value);

        echo $this->session->get($session_name);
    }

}
