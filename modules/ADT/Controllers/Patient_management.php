<?php

namespace Modules\ADT\Controllers;

use App\Controllers\BaseController;
use Modules\ADT\Models\Dependant;
use Modules\ADT\Models\Facilities;
use Modules\ADT\Models\Patient;
use Modules\ADT\Models\PatientVisit;
use Modules\ADT\Models\Spouse;
use Illuminate\Database\Capsule\Manager as DB;
use Modules\ADT\Models\CCC_store_service_point;
use Modules\ADT\Models\District;
use Modules\ADT\Models\Drugcode;
use Modules\ADT\Models\DrugProphylaxis;
use Modules\ADT\Models\FamilyPlanning;
use Modules\ADT\Models\Gender;
use Modules\ADT\Models\NonAdherenceReasons;
use Modules\ADT\Models\OtherIllnesses;
use Modules\ADT\Models\Patient_appointment;
use Modules\ADT\Models\PatientPrepTest;
use Modules\ADT\Models\PatientSource;
use Modules\ADT\Models\PatientStatus;
use Modules\ADT\Models\PatientViralLoad;
use Modules\ADT\Models\PepReason;
use Modules\ADT\Models\PrepReason;
use Modules\ADT\Models\Regimen;
use Modules\ADT\Models\RegimenChangePurpose;
use Modules\ADT\Models\RegimenServiceType;
use Modules\ADT\Models\ReportingFacility;
use Modules\ADT\Models\VisitPurpose;
use Modules\ADT\Models\WhoStage;
use Modules\Api\Controllers\Api;

ob_start();

class Patient_management extends BaseController {

    var $api;
    var $patient_module;
    var $dispense_module;
    var $appointment_module;
    protected $session;

    function __construct() {
        // $this->load->library('PHPExcel');
        ini_set("max_execution_time", "100000");
        ini_set('memory_limit', '512M');
        $this->session = session();
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

    public function get_api_values() {
        $this->init_api_values();

        echo "api: " . $this->api . "<br/>";
        echo "patient_module: " . $this->patient_module . "<br/>";
        echo "dispense_module: " . $this->dispense_module . "<br/>";
        echo "appointments_module: " . $this->appointment_module . "<br/>";
        echo "api_adt_url: " . $this->api_adt_url . "<br/>";
    }

    public function index() {
        $source = $this->session->get('facility');
        $facility_settings = Facilities::where('facilitycode', $source)->first();

        $data['medical_number'] = $facility_settings->medical_number;
        $data['pill_count'] = $facility_settings->pill_count;
        $data['content_view'] = "\Modules\ADT\Views\patients\listing_view";
        $this->base_params($data);
    }

    public function merge_list() {
        $data['quick_link'] = "merging";
        $data['title'] = "Patient Merging";
        $data['page_title'] = "Patient Merging Management";
        $data['link'] = "settings_management";
        $data['banner_text'] = "Patient Merging Listing";

        $this->session->set("link_id", "merge_list");
        $this->session->set("linkSub", "patient_management/merge_list");

        echo view("\Modules\ADT\Views\\patient_merging_v", $data);
    }

    public function get_Last_vl_result($patient_no) {
        //Validate patient_no when use of / to separate mflcode and ccc_no
        $mflcode = $this->uri->getSegment(3);
        $ccc_no = $this->uri->getSegment(4);
        if ($ccc_no) {
            $patient_no = $mflcode . '/' . $ccc_no;
        }

        $results = PatientViralLoad::where('patient_ccc_number', $patient_no)->orderBy('test_date', 'desc')->first();
        echo json_encode($results);
    }

    public function details() {
        $data['content_view'] = "\Modules\ADT\Views\patients\patient_details_v";
        $data['hide_side_menu'] = 1;
        $this->base_params($data);
    }

    public function addpatient_show() {
        $data = [];
        $data['facility_code'] = $this->session->get('facility');
        $facilities = Facilities::where('facilitycode', $data['facility_code'])->first();
        $data['cs'] = $facilities['ccc_separator'];
        $data['districts'] = District::orderBy('name')->get()->toArray();
        $data['genders'] = Gender::all()->toArray();
        $data['statuses'] = PatientStatus::where('active', '1')->get()->toArray();
        $data['sources'] = PatientSource::where('active', '1')->get()->toArray();
        $data['drug_prophylaxis'] = DrugProphylaxis::all()->toArray();
        $data['service_types'] = RegimenServiceType::where('active', '1')->get()->toArray();
        $data['facilities'] = Facilities::get()->toArray();
        $data['family_planning'] = FamilyPlanning::where('active', '1')->orderBy('name')->get()->toArray();
        $data['other_illnesses'] = OtherIllnesses::where('active', '1')->orderBy('name')->get()->toArray();
        $data['pep_reasons'] = PepReason::where('active', '1')->orderBy('name')->get();
        $data['prep_reasons'] = PrepReason::where('active', '1')->orderBy('name')->get();
        $data['drugs'] = Drugcode::where('enabled', '1')->orderBy('drug')->get()->toArray();
        $data['who_stages'] = WhoStage::all()->toArray();
        $data['hide_side_menu'] = '1';
        $data['content_view'] = "\Modules\ADT\Views\add_patient_v";
        $this->base_params($data);
    }

    public function checkpatient_no($patient_no) {
        //Variables
        $facility_code = $this->session->get('facility');
        $results = Patient::where('facility_code', $facility_code)->where('patient_number_ccc', $patient_no)->get();
        if (!empty($results)) {
            echo json_decode("1");
        } else {
            echo json_decode("0");
        }
    }

    public function merge_spouse($patient_no, $spouse_no) {
        $spousedata = ['primary_spouse' => $patient_no, 'secondary_spouse' => $spouse_no];
        Spouse::create($spousedata);
    }

    public function unmerge_spouse($patient_no) {
        Spouse::where('primary_spouse', $patient_no)->delete();
    }

    public function merge_parent($patient_no, $parent_no) {
        $childdata = ['child' => $patient_no, 'parent' => $parent_no];
        Dependant::create($childdata);
    }

    public function unmerge_parent($patient_no) {
        Dependant::where('child', $patient_no)->delete();
    }

    public function listing() {
        $access_level = $this->session->get('user_indicator');
        $facility_code = $this->session->get('facility');
        $link = "";

        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = ['patient_number_ccc', 'first_name', 'last_name', 'other_name', 'nextappointment', 'phone', 'regimen_desc', 'patient_status'];

        $iDisplayStart = $this->post('iDisplayStart', true);
        $iDisplayLength = $this->post('iDisplayLength', true);
        $iSortCol_0 = $this->post('iSortCol_0', true);
        $iSortingCols = $this->post('iSortingCols', true);
        $sSearch = $this->post('sSearch', true);
        $sEcho = $this->post('sEcho', true);

        $qb = Patient::query();

        // Paging
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $qb = $qb->take($iDisplayLength)->skip($iDisplayStart);
            // $this->db->limit($this->db->escape_str($iDisplayLength), $this->db->escape_str($iDisplayStart));
        }

        // Ordering
        if (isset($iSortCol_0)) {
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $iSortCol = $this->post('iSortCol_' . $i, true);
                $bSortable = $this->post('bSortable_' . intval($iSortCol), true);
                $sSortDir = $this->post('sSortDir_' . $i, true);

                if ($bSortable == 'true') {
                    $qb = $qb->orderBy($iSortCol, $sSortDir);
                    // $this->db->order_by($aColumns[intval($this->db->escape_str($iSortCol))], $this->db->escape_str($sSortDir));
                }
            }
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */

        $j = 0;
        for ($i = 0; $i < count($aColumns); $i++) {
            if ($i >= 1) {
                $j++;
            }
            $bSearchable = $this->post('bSearchable_' . $j, true);
            $sSearch_ = $this->post('sSearch_' . $j, true);
            // Individual column filtering
            if (isset($bSearchable) && $bSearchable == 'true' && !empty($sSearch_)) {
                if ($i >= 3 and $i < 5) {
                    $i = $i + 2;
                }
                $col = $aColumns[$i];
                if ($col == 'First_Name') {
                    // $value = $this->db->escape_like_str($sSearch_);
                    // $where = "(First_Name LIKE '%$value%' OR Last_Name LIKE '%$value%' OR Last_Name LIKE '%$value%')";
                    // $this->db->where($where);
                    $qb = $qb->where("(first_name LIKE '%$sSearch_%' OR last_name LIKE '%$sSearch_%' OR other_oame LIKE '%$sSearch_%')");
                } else {
                    $qb = $qb->where($col, 'like', '%' . $sSearch_ . '%');
                    // $this->db->like($col, $this->db->escape_like_str($sSearch_));
                }
            }
            if (isset($sSearch) && !empty($sSearch)) {
                $qb = $qb->orWhere($aColumns[$i], 'like', '%' . $sSearch_ . '%');
                // $this->db->or_like($aColumns[$i], $this->db->escape_like_str($sSearch));
            }
        }



        // Select Data
        // $qb = $qb->select('SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $aColumns)));
        // $this->db->select('SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $aColumns)), false);
        // $qb = $qb->select("p.id,p.Patient_Number_CCC,p.First_Name,p.Last_Name,p.Other_Name,p.NextAppointment,p.phone as Phone,r.Regimen_Desc,s.Name,p.Active patient p");
        // $this->db->from("patient p");
        $qb = $qb->where("facility_code", $facility_code);
        // $this->db->join("regimen r", "r.id=p.Current_Regimen", "left");
        // $this->db->join("patient_status s", "s.id=p.current_status", "left");

        $rResult = $qb->get();
        //echo $this->db->last_query();die();
        // Data set length after filtering
        // $this->db->select('FOUND_ROWS() AS found_rows');
        $iFilteredTotal = $qb->count();

        // Total data set length
        // $this->db->select("p.*");
        // $this->db->from("patient p");
        // $this->db->where("p.Facility_Code", $facility_code);
        // $this->db->join("regimen r", "r.id=p.Current_Regimen", "left");
        // $this->db->join("patient_status s", "s.id=p.current_status", "left");
        // $tot_patients = $this->db->get();
        $iTotal = Patient::where('facility_code', $facility_code)->count();

        // Output
        $msg = ['sEcho' => intval($sEcho), 'iTotalRecords' => $iTotal, 'iTotalDisplayRecords' => $iFilteredTotal, 'aaData' => []];

        foreach ($rResult as $aRow) {
            $row = [];
            $col = 0;
            $name = "";
            $id = "";
            foreach ($aColumns as $col) {
                if ($col == "first_name" or $col == "last_name" or $col == "other_name") {
                    // if ($col == "first_name") {
                    //     $name = $aRow[$col] . " ";
                    //     $name = strtoupper($name);
                    //     continue;
                    // } else {
                    //     if ($col == "Last_Name") {
                    //         $name .= $aRow[$col] . " ";
                    //         $name = strtoupper($name);
                    //         continue;
                    //     } else if ($col == "Other_Name") {
                    //         $name .= $aRow[$col];
                    //         $name = strtoupper($name);
                    //         $name = "<span style='white-space:nowrap;'>" . $name . "</span>";
                    //     }
                    // }
                    $name = "<span style='white-space:nowrap;'>" . strtoupper($name) . "</span>";
                } else if ($col == "date_enrolled") {
                    $name = date('d-M-Y', strtotime($aRow[$col]));
                } else if ($col == "nextappointment") {
                    if ($aRow[$col]) {
                        $name = date('d-M-Y', strtotime($aRow[$col]));
                    } else {
                        $name = "N/A";
                    }
                }
                //Check if phone No does not exist
                else if ($col == "phone") {
                    $name = str_replace(" ", "", $aRow['phone']);
                } else if ($col == "regimen_desc") {
                    $name = "<b style='white-space:nowrap;'>" . $aRow[$col] . "</b>";
                } else if ($col == "patient_status") {
                    $name = "<b>" . $aRow[$col] . "</b>";
                } else {
                    $name = $aRow[$col];
                    $name = strtoupper($name);
                }

                $row[] = $name;
            }
            $id = $aRow['id'];
            $link = "";
            if ($access_level == "facility_administrator") {
                if ($aRow['Active'] == 1) {
                    $link = ' | <a href="' . base_url() . '/public/patient/disable/' . $id . '" class="red actual"> Disable </a>';
                } else {
                    $link = ' | <a href="' . base_url() . '/public/patient/enable/' . $id . '" class="green actual"> Enable </a>';
                }
            }

            if ($aRow['Active'] == 1) {
                $row[] = ' <a href="' . base_url() . '/public/patient/viewDetails/' . $id . '"> Detail </a> | <a href="' . base_url() . 'patient/edit/' . $id . '"> Edit </a> ' . $link;
            } else {
                $link = str_replace("|", "", $link);
                $link .= ' | <a href="' . base_url() . '/public/patient/delete/' . $id . '" class="red actual"> Delete </a>';
                $row[] = $link;
            }

            $msg['aaData'][] = $row;
        }
        echo json_encode($msg, JSON_PRETTY_PRINT);
    }

    public function extract_illness($illness_list = "") {
        $illness_array = explode(",", $illness_list);
        $new_array = [];
        foreach ($illness_array as $index => $illness) {
            if ($illness == null) {
                unset($illness_array[$index]);
            } else {
                $illness = str_replace("\n", "", $illness);
                $new_array[] = trim($illness);
            }
        }
        return json_encode($new_array);
    }

    public function viewDetails($record_no) {
        $this->session->set('record_no', $record_no);
        $patient = "";
        $facility = "";
        $sql = "SELECT p.*, rst.Name as service_name, dp.child, s.secondary_spouse " .
                "FROM patient p " .
                "LEFT JOIN regimen_service_type rst ON rst.id=p.service " .
                "LEFT JOIN dependants dp ON p.patient_number_ccc=dp.parent " .
                "LEFT JOIN spouses s ON p.patient_number_ccc=s.primary_spouse " .
                "WHERE p.id='" . $record_no . "' " .
                "GROUP BY p.id";
        $results = (array) DB::select($sql);

        $depdendant_msg = "";
        if ($results) {
            $results[0]['other_illnesses'] = $this->extract_illness($results[0]['other_illnesses']);
            $data['results'] = $results;
            $patient = $results[0]['patient_number_ccc'];
            $facility = $this->session->get("facility");
            //Check dependedants/spouse status
            $child = $results[0]['child'];
            $spouse = $results[0]['secondary_spouse'];
            $patient_name = strtoupper($results[0]['first_name'] . ' ' . $results[0]['last_name']);
            if ($child != NULL) {

                $pat = $this->getDependentStatus($child);
                if ($pat != '') {
                    $depdendant_msg .= "Patient $patient_name\'s dependant " . $pat . " is lost to follow up ";
                }
            }
            if ($spouse != NULL) {
                $pat = $this->getDependentStatus($spouse);
                if ($pat != '') {
                    $depdendant_msg .= "Patient $patient_name\'s spouse " . $pat . " is lost to follow up ";
                }
            }
        }
        //Patient History
        $sql = "SELECT pv.dispensing_date, " .
                "v.name AS visit, " .
                "u.Name AS unit, " .
                "pv.dose, " .
                "pv.duration, " .
                "pv.indication, " .
                "pv.patient_visit_id AS record, " .
                "d.drug, " .
                "pv.quantity, " .
                "pv.current_weight, " .
                "pv.current_height, " .
                "r1.regimen_desc as last_regimen, " .
                "r.regimen_desc, " .
                "pv.batch_number, " .
                "pv.pill_count, " .
                "pv.adherence, " .
                "pv.user, " .
                "rcp.name as regimen_change_reason " .
                "FROM v_patient_visits pv " .
                "LEFT JOIN drugcode d ON pv.drug_id = d.id " .
                "LEFT JOIN drug_unit u ON d.unit = u.id " .
                "LEFT JOIN regimen r ON pv.regimen_id = r.id " .
                "LEFT JOIN regimen r1 ON pv.last_regimen = r1.id " .
                "LEFT JOIN visit_purpose v ON pv.visit_purpose_id = v.id " .
                "LEFT JOIN regimen_change_purpose rcp ON rcp.id=pv.regimen_change_reason " .
                "WHERE pv.patient_id = '" . $patient . "' " .
                "AND pv.facility =  '" . $facility . "' " .
                "AND pv.active='1' AND pv.pv_active='1' " .
                "GROUP BY d.drug,pv.dispensing_date " .
                "ORDER BY  pv.patient_visit_id DESC";
        $results = (array) DB::select($sql);
        if ($results) {
            $data['history_logs'] = $results;
        } else {
            $data['history_logs'] = "";
        }
        $data['dependant_msg'] = $depdendant_msg;
        $data['districts'] = District::orderBy('name')->get()->toArray();
        $data['genders'] = Gender::all()->toArray();
        $data['statuses'] = PatientStatus::where('active', '1')->get()->toArray();
        $data['sources'] = PatientSource::where('active', '1')->get()->toArray();
        $data['drug_prophylaxis'] = DrugProphylaxis::all()->toArray();
        $data['service_types'] = RegimenServiceType::where('active', '1')->get()->toArray();
        $data['facilities'] = Facilities::orderBy('name')->get();
        $data['family_planning'] = FamilyPlanning::where('active', '1')->orderBy('name')->get()->toArray();
        $data['other_illnesses'] = OtherIllnesses::where('active', '1')->orderBy('name')->get()->toArray();
        $data['pep_reasons'] = PepReason::where('active', '1')->orderBy('name')->get();
        $data['drugs'] = Drugcode::where('enabled', '1')->orderBy('drug')->get()->toArray();
        $data['regimens'] = Regimen::orderBy('regimen_code')->get();
        $data['who_stages'] = WhoStage::all()->toArray();
        $data['content_view'] = '\Modules\ADT\Views\patient_details_';
        //Hide side menus
        $data['hide_side_menu'] = '1';
        $this->base_params($data);
    }

    public function getFacililtyAge() {
        $facility_code = $this->session->get('facility');
        $adult_age = Facilities::where('facilitycode', $facility_code)->first();

        return $adult_age->adult_age;
    }

    public function edit($record_no = null) {
        $record_no = $this->uri->getSegment(3);
        $sql = "SELECT p.*, rst.Name as service_name, dp.parent, s.secondary_spouse, t.* " .
                "FROM patient p " .
                "LEFT JOIN regimen_service_type rst ON rst.id=p.service " .
                "LEFT JOIN dependants dp ON p.patient_number_ccc=dp.child " .
                "LEFT JOIN spouses s ON p.patient_number_ccc=s.primary_spouse " .
                "LEFT JOIN (" .
                "SELECT patient_id, prep_reason_id AS prep_reason, is_tested AS prep_test_answer, test_date AS prep_test_date, test_result AS prep_test_result " .
                "FROM patient_prep_test " .
                "WHERE patient_id = ? " .
                "ORDER BY test_date DESC " .
                "LIMIT 1" .
                ") t ON t.patient_id = p.id " .
                "WHERE p.id = ? " .
                "GROUP BY p.id";
        $results = DB::select($sql, [$record_no, $record_no]);


        if ($results) {
            $results[0]->other_illnesses = $this->extract_illness($results[0]->other_illnesses);
            $data['results'] = $results;
        }

        $data['facility_code'] = $this->session->get('facility');
        $data['facility_code'] = $this->session->get('facility');
        $facilities = Facilities::where('facilitycode', $data['facility_code'])->first();
        $data['cs'] = $facilities['ccc_separator'];


        $data['record_no'] = $record_no;
        $data['facility_adult_age'] = $this->getFacililtyAge();
        $data['districts'] = District::orderBy('name')->get()->toArray();
        $data['genders'] = Gender::all()->toArray();
        $data['statuses'] = PatientStatus::where('active', '1')->get()->toArray();
        $data['sources'] = PatientSource::where('active', '1')->get()->toArray();
        $data['drug_prophylaxis'] = DrugProphylaxis::all()->toArray();
        $data['service_types'] = RegimenServiceType::where('active', '1')->get()->toArray();
        $data['facilities'] = Facilities::get()->toArray();
        $data['family_planning'] = FamilyPlanning::where('active', '1')->orderBy('name')->get()->toArray();
        $data['other_illnesses'] = OtherIllnesses::where('active', '1')->orderBy('name')->get()->toArray();
        $data['pep_reasons'] = PepReason::where('active', '1')->orderBy('name')->get();
        $data['prep_reasons'] = PrepReason::where('active', '1')->orderBy('name')->get();
        $data['regimens'] = Regimen::orderBy('regimen_code')->get()->toArray();
        $data['drugs'] = Drugcode::where('enabled', '1')->orderBy('drug')->get()->toArray();
        $data['who_stages'] = WhoStage::all()->toArray();
        $data['content_view'] = '\Modules\ADT\Views\edit_patients_v';
        //Hide side menus
        $data['hide_side_menu'] = '1';
        $this->base_params($data);
    }

    function requiredFields($ccid = null) {
        $ccid = $this->uri->getSegment(3);
        $required = '';
        $status = 0;
        $result = Patient::where('id', $ccid)->first();
        $mandatory = [
            'patient_number_ccc', 'first_name', 'dob', 'gender', 'pregnant',
            'height', 'weight', 'date_enrolled', 'start_regimen', 'source', 'service'
        ];
        $label = [
            'Patient CCC No.', 'First Name', 'Date of Birth', 'Gender', 'Pregnancy Status',
            'Height', 'Weight', 'Enrollment Date', 'Date Regimen Started', 'Source of patient', 'Service'
        ];
        $i = 0;
        foreach ($mandatory as $r) {
            if (trim($result[$r]) == '' || trim($result[$r]) == 'NULL') {
                $required .= $label[$i] . ", ";
                $status = 1;
            }
            $i++;
        }

        echo json_encode(['status' => $status, 'fields' => rtrim($required, ',')]);
    }

    public function report() {

        $content_view = '\Modules\ADT\Views\patient_report_v';
        $data['transfered'] = $this->loadChoices('patient_source', 'name');
        $data['service'] = $this->loadChoices('regimen_service_type', 'name');
        $data['startreg'] = $this->loadChoices('regimen', 'regimen_desc');
        $data['currstat'] = $this->loadChoices('patient_status', 'Name');

        //$data['hide_side_menu'] = 1;  
        $data['content_view'] = $content_view;
        $this->base_params($data);
    }

    public function save() {
        // Check for duplicate ccc
        $ccc = trim($this->post('patient_number'));
        if (empty($ccc)) {
            $this->session->set('patient_error', 'Patient CCC cannot be empty');
            return redirect()->to(base_url('/public/patients/add'));
        }
        if (Patient::where('patient_number_ccc', $ccc)->exists()) {
            $this->session->set('patient_error', 'Patient CCC already exists');
            return redirect()->to(base_url('/public/patients/add'));
        }

        $this->init_api_values();
        $family_planning = "";
        $other_illness_listing = "";
        $other_allergies_listing = "";
        $patient = "";

        $family_planning = $this->post('family_planning_holder');
        if ($family_planning == null) {
            $family_planning = "";
        }
        $drug_prophylaxis = $this->post('drug_prophylaxis_holder');
        if ($drug_prophylaxis == null) {
            $drug_prophylaxis = "";
        }
        $other_illness_listing = $this->post('other_illnesses_holder');
        if ($other_illness_listing == null) {
            $other_illness_listing = "";
        }
        $other_chronic = $this->post('other_chronic');
        if ($other_chronic != "") {
            if ($other_illness_listing) {
                $other_illness_listing = $other_illness_listing . "," . $other_chronic;
            } else {
                $other_illness_listing = $other_chronic;
            }
        }
        //Other allergies
        $other_allergies_list = $this->post('other_allergies_listing', TRUE);
        //List of drug allergies.
        $drug_allergies = $this->post('drug_allergies_holder', TRUE);
        if ($drug_allergies == null) {
            $drug_allergies = "";
        }

        if ($drug_allergies != "") {
            if ($other_allergies_list) {
                $other_allergies_listing = $other_allergies_list . "," . $drug_allergies;
            } else {
                $other_allergies_listing = $drug_allergies;
            }
        } else {
            $other_allergies_listing = $other_allergies_list;
        }

        //Patient Information & Demographics
        $new_patient = new Patient();
        $new_patient->medical_record_number = $this->post('medical_record_number');
        $new_patient->patient_number_ccc = $this->post('patient_number');
        $new_patient->first_name = $this->post('first_name');
        $new_patient->last_name = $this->post('last_name');
        $new_patient->other_name = $this->post('other_name');
        $new_patient->dob = $this->post('dob');
        $new_patient->pob = $this->post('pob');
        $new_patient->gender = $this->post('gender');
        $new_patient->pregnant = empty($this->post('pregnant')) ? "0" : $this->post('pregnant');
        $new_patient->breastfeeding = empty($this->post('breastfeeding')) ? "0" : $this->post('breastfeeding');
        $new_patient->start_weight = $this->post('weight');
        $new_patient->start_height = $this->post('height');
        $new_patient->start_bsa = $this->post('surface_area');
        $new_patient->start_bmi = $this->post('start_bmi');
        $new_patient->weight = $this->post('weight');
        $new_patient->height = $this->post('height');
        $new_patient->sa = $this->post('surface_area');
        $new_patient->bmi = $this->post('start_bmi');
        $new_patient->phone = $this->post('phone');
        $new_patient->sms_consent = empty($this->post('sms_consent')) ? "0" : $this->post('sms_consent');
        ;
        $new_patient->physical = $this->post('physical');
        $new_patient->alternate = $this->post('alternate');
        $new_patient->differentiated_care = $this->post('differentiated_care');


        //Patient History
        $new_patient->partner_status = empty($this->post('partner_status')) ? "0" : $this->post('partner_status');
        ;
        $new_patient->disclosure = $this->post('disclosure');
        $new_patient->fplan = $family_planning;
        $new_patient->other_illnesses = $other_illness_listing;
        $new_patient->other_drugs = $this->post('other_drugs');
        $new_patient->adr = $other_allergies_listing;
        //other drug allergies
        $new_patient->support_group = $this->post('support_group_listing');
        $new_patient->smoke = empty($this->post('smoke')) ? "0" : $this->post('smoke');
        $new_patient->alcohol = empty($this->post('alcohol')) ? "0" : $this->post('alcohol');
        $new_patient->tb = empty($this->post('tb')) ? "0" : $this->post('tb');
        $new_patient->tb_category = $this->post('tbcategory');
        $new_patient->tbphase = $this->post('tbphase');
        $new_patient->startphase = $this->post('fromphase');
        $new_patient->endphase = $this->post('tophase');

        //Program Information
        $new_patient->date_enrolled = $this->post('enrolled');
        $new_patient->current_status = $this->post('current_status');
        $new_patient->status_change_date = $this->post('service_started');
        $new_patient->source = $this->post('source');
        $new_patient->transfer_from = $this->post('transfer_source');
        $new_patient->drug_prophylaxis = $this->post('drug_prophylaxis');
        $new_patient->facility_code = $this->session->get('facility');
        $new_patient->service = $this->post('service');
        $new_patient->start_regimen = $this->post('regimen');
        $new_patient->current_regimen = $this->post('regimen');
        $new_patient->start_regimen_date = $this->post('service_started');
        $new_patient->tb_test = $this->post('tested_tb');
        $new_patient->pep_reason = $this->post('pep_reason');
        $new_patient->who_stage = $this->post('who_stage');
        $new_patient->drug_prophylaxis = $drug_prophylaxis;
        $new_patient->isoniazid_start_date = $this->post('iso_start_date');
        $new_patient->isoniazid_end_date = $this->post('iso_end_date');
        $new_patient->rifap_isoniazid_start_date = $this->post('rifa_iso_start_date');
        $new_patient->rifap_isoniazid_end_date = $this->post('rifa_iso_end_date');


        $spouse_no = $this->post('match_spouse');
        $patient_no = $this->post('patient_number');
        $child_no = $this->post('match_parent');

        $new_patient->save();
        //Map patient to spouse
        if ($spouse_no != NULL) {
            $this->merge_spouse($patient_no, $spouse_no);
        }
        //Map child to parent/guardian
        if ($child_no != NULL) {
            $this->merge_parent($patient_no, $child_no);
        }

        $max = Patient::max('id');
        $auto_id = $max;

        //Add Prep Data
        $is_tested = $this->post('prep_test_answer');
        $prep_test_data = [
            'patient_id' => $auto_id,
            'prep_reason_id' => $this->post('prep_reason'),
            'is_tested' => $is_tested,
            'test_date' => $this->post('prep_test_date'),
            'test_result' => $this->post('prep_test_result'),
        ];
        //Only 'Save' for those tested
        if ($is_tested) {
            PatientPrepTest::create($prep_test_data);
        }

        $patient = $this->post('patient_number');
        $direction = $this->post('direction');

        if ($this->api && $this->patient_module) {
            // post to IL via API
            $api = new Api();
            $api->getPatient($new_patient->patient_number_ccc, 'ADD');
            // /> POST TO IL VIA API
        }

        if ($direction == 0) {
            $this->session->set('msg_save_transaction', 'success');
            $this->session->setFlashdata('dispense_updated', 'Patient: ' . $this->post('first_name', TRUE) . " " . $this->post('last_name', TRUE) . ' was Saved');
            return redirect()->to(base_url() . "/public/patients");
        } else if ($direction == 1) {
            return redirect()->to(base_url() . "/public/dispensement_management/dispense/$auto_id");
        }
    }

    public function getDependentStatus($patient_number_ccc) {
        $sql = "SELECT ps.name,p.patient_number_ccc,p.first_name,p.last_name,p.other_name FROM patient p " .
                "INNER JOIN patient_status ps ON ps.id = p.current_status " .
                "AND p.patient_number_ccc='" . $patient_number_ccc . "' " .
                "AND ps.name LIKE '%lost%'";
        $result = DB::select($sql);
        if (count($result) > 0) {
            $patient = '<b>' . strtoupper($result[0]['first_name'] . ' ' . $result[0]['last_name'] . ' ' . $result[0]['other_name']) . '</b> ( CCC Number:' . $result[0]['patient_number_ccc'] . ')';
            return $patient;
        } else {
            return '';
        }
    }

    public function update($record_id = null) {
        $record_id = $this->uri->getSegment(3);
        $this->init_api_values();
        $family_planning = "";
        $other_illness_listing = "";
        $other_allergies_listing = "";
        $prev_appointment = "";
        $facility = "";
        $patient = "";

        //Check if appointment exists
        $prev_appointment = $this->post('prev_appointment_date');
        $appointment = $this->post('next_appointment_date');
        $prev_clinicalappointment = $this->post('prev_clinical_appointment_date');
        $clinicalappointment = $this->post('next_clinical_appointment_date');



        $facility = $this->session->get('facility');
        $patient = $this->post('patient_number');

        if ($appointment) {
            $sql = "select * from patient_appointment where patient='$patient' and appointment='$prev_appointment' and facility='$facility'";
            $results = DB::select($sql);

            if ($results) {
                $record_no = $results[0]->id;
                //If exisiting appointment(Update new Record)
                $sql = "update patient_appointment set appointment='$appointment',patient='$patient',facility='$facility' where id='$record_no'";
            } else {
                //If no appointment(Insert new record)
                $sql = "insert patient_appointment(patient,appointment,facility)VALUES('$patient','$appointment','$facility')";
            }
            DB::select($sql);
        }

        if ($clinicalappointment) {
            $sql = "select * from clinic_appointment where patient='$patient' and appointment='$prev_clinicalappointment' and facility='$facility'";
            $results = DB::select($sql);

            if ($results) {
                $record_no = $results[0]->id;

                //If exisiting appointment(Update new Record)
                $sql = "update clinic_appointment set appointment='$clinicalappointment' where id='$record_no'";
            } else {
                //If no appointment(Insert new record)
                $sql = "insert clinic_appointment(patient,appointment,facility)VALUES('$patient','$appointment','$facility')";
            }
            DB::select($sql);
        }

        $family_planning = $this->post('family_planning_holder');
        if ($family_planning == null) {
            $family_planning = "";
        }
        $drug_prophylaxis = $this->post('drug_prophylaxis_holder');
        if ($drug_prophylaxis == null) {
            $drug_prophylaxis = "";
        }
        $other_illness_listing = $this->post('other_illnesses_holder');
        if ($other_illness_listing == null) {
            $other_illness_listing = "";
        }
        $other_chronic = $this->post('other_chronic');
        if ($other_chronic != "") {
            if ($other_illness_listing) {
                $other_illness_listing = $other_illness_listing . "," . $other_chronic;
            } else {
                $other_illness_listing = $other_chronic;
            }
        }
        //Other allergies
        $other_allergies_list = $this->post('other_allergies_listing');
        //List of drug allergies.
        $drug_allergies = $this->post('drug_allergies_holder');
        if ($drug_allergies == null) {
            $drug_allergies = "";
        }

        if ($drug_allergies != "") {
            if ($other_allergies_list) {
                $other_allergies_listing = $other_allergies_list . "," . $drug_allergies;
            } else {
                $other_allergies_listing = $drug_allergies;
            }
        } else {
            $other_allergies_listing = $other_allergies_list;
        }

        $other_drugs = $this->post('other_drugs');
        if (!$other_drugs) {
            $other_drugs = "";
        }


        $data = [
            'drug_prophylaxis' => $drug_prophylaxis,
            'isoniazid_start_date' => $this->post('iso_start_date'),
            'isoniazid_end_date' => $this->post('iso_end_date'),
            'tb_test' => $this->post('tested_tb'),
            'who_stage' => $this->post('who_stage'),
            'pep_reason' => $this->post('pep_reason'),
            'medical_record_number' => $this->post('medical_record_number'),
            'patient_number_ccc' => $this->post('patient_number'),
            'first_name' => $this->post('first_name'),
            'last_name' => $this->post('last_name'),
            'other_name' => $this->post('other_name'),
            'dob' => $this->post('dob'),
            'pob' => $this->post('pob'),
            'gender' => $this->post('gender'),
            'pregnant' => empty($this->post('pregnant')) ? "0" : $this->post('pregnant'),
            'breastfeeding' => empty($this->post('breastfeeding')) ? "0" : $this->post('breastfeeding'),
            'start_weight' => $this->post('start_weight'),
            'start_height' => $this->post('start_height'),
            'start_bsa' => $this->post('start_bsa'),
            'weight' => $this->post('current_weight'),
            'height' => $this->post('current_height'),
            'sa' => $this->post('bsa'),
            'bmi' => $this->post('bmi'),
            'phone' => $this->post('phone'),
            'sms_consent' => $this->post('sms_consent'),
            'physical' => $this->post('physical'),
            'alternate' => $this->post('alternate'),
            'partner_status' => $this->post('partner_status',),
            'disclosure' => $this->post('disclosure'),
            'fplan' => $family_planning,
            'differentiated_care' => $this->post('differentiated_care'),
            'clinicalappointment' => $this->post('next_clinical_appointment_date'),
            'breastfeeding' => $this->post('breastfeeding'),
            'other_illnesses' => $other_illness_listing,
            'other_drugs' => $other_drugs,
            'adr' => $other_allergies_listing,
            'smoke' => $this->post('smoke'),
            'alcohol' => $this->post('alcohol'),
            'tb' => $this->post('tb'),
            'tb_category' => $this->post('tbcategory'),
            'tbphase' => $this->post('tbphase'),
            'startphase' => $this->post('fromphase'),
            'endphase' => $this->post('tophase'),
            'date_enrolled' => $this->post('enrolled'),
            'current_status' => $this->post('current_status'),
            'status_change_date' => $this->post('status_started'),
            'source' => $this->post('source'),
            'transfer_from' => $this->post('transfer_source'),
            'supported_by' => $this->post('support'),
            'facility_code' => $this->session->get('facility'),
            'service' => $this->post('service'),
            'start_regimen' => $this->post('regimen'),
            'start_regimen_date' => $this->post('service_started'),
            'current_regimen' => $this->post('current_regimen'),
            'nextappointment' => $this->post('next_appointment_date')
        ];
        // echo "<pre>";
        $status_change_query = "insert into change_log (old_value,new_value,facility,patient,change_type)
        select current_status
        ,'" . $this->post('current_status') . "' 
        ,'" . $this->session->get('facility') . "'
        ,'" . $this->post('patient_number') . "',
        'status' from patient where patient_number_ccc  = '"
                . $this->post('patient_number') . "' and current_status != '"
                . $this->post('current_status') . "'";

        $service_change_query = "insert into change_log (old_value,new_value,facility,patient,change_type)
        select service
        ,'" . $this->post('service') . "' 
        ,'" . $this->session->get('facility') . "'
        ,'" . $this->post('patient_number') . "',
        'service' from patient where patient_number_ccc  = '"
                . $this->post('patient_number') . "' and service !='"
                . $this->post('service') . "'";

        DB::select($service_change_query);
        DB::select($status_change_query);

        Patient::where('id', $record_id)->update($data);


        $spouse_no = $this->post('match_spouse');
        $patient_no = $this->post('patient_number');
        $child_no = $this->post('match_parent');
        //Map patient to spouse but unmap all for this patient to remove duplicates
        if ($spouse_no != NULL) {
            $this->unmerge_spouse($patient_no);
            $this->merge_spouse($patient_no, $spouse_no);
        }
        //Map child to parent/guardian but unmap all for this patient to remove duplicates
        if ($child_no != NULL) {
            $this->unmerge_parent($patient_no);
            $this->merge_parent($patient_no, $child_no);
        }
        //Update/Insert Test Data
        $is_tested = $this->post('prep_test_answer');
        $test_data = [
            'patient_id' => $record_id,
            'prep_reason_id' => $this->post('prep_reason'),
            'is_tested' => $is_tested,
            'test_date' => $this->post('prep_test_date'),
            'test_result' => $this->post('prep_test_result')
        ];

        if ($is_tested) {
            $this->updateTestData($test_data);
        }

        //Set session for notications
        $this->session->set('msg_save_transaction', 'success');
        $this->session->set('user_updated', $this->post('first_name'));

        if ($this->api && $this->patient_module) {
            // post to IL via API
            $api = new Api();
            $api->getPatient($data['patient_number_ccc'], 'EDIT');
            // file_get_contents(base_url() . '/public/tools/api/getPatient/' . $record_id . '/EDIT');
            // /> POST TO IL VIA API
        }

        return redirect()->to(base_url() . "/public/patient/load_view/details/$record_id");
    }

    public function updateTestData($test_data = []) {
        $prev_test_data = PatientPrepTest::where($test_data)->get();
        if (empty($prev_test_data)) {
            PatientPrepTest::create($test_data);
        }
    }

    public function update_visit() {
        $original_patient_number = $this->post("original_patient_number");
        $patient_number = $this->post("patient_number");
        // //update patient visits
        // $this->db->where('patient_id', $original_patient_number);
        // $this->db->update('patient_visit', array("patient_id" => $patient_number));

        DB::select("call change_ccc('" . $original_patient_number . "','" . $patient_number . "')");
        //update spouses
        $this->unmerge_spouse($original_patient_number);
        //update dependants
        $this->unmerge_parent($original_patient_number);
    }

    public function base_params($data) {
        $data['title'] = "webADT | Patients";
        $data['banner_text'] = "Facility Patients";
        $data['link'] = "/public/patients";
        echo view('\Modules\ADT\Views\template', $data);
    }

    public function create_timestamps() {
        $visits = PatientVisit::all();
        foreach ($visits as $visit) {
            $current_date = $visit->dispensing_Date;
            $changed_date = strtotime($current_date);
            $visit->dispensing_date_timestamp = $changed_date;
            $visit->save();
        }
    }

    public function regimen_breakdown() {
        $selected_facility = $this->post('facility');
        if (isset($selected_facility)) {
            $facility = $this->post('facility');
        }
        $data = [];
        $data['current'] = "/public/patient_management";
        $data['title'] = "webADT | Patient Regimen Breakdown";
        $data['content_view'] = "\Modules\ADT\Views\patient_regimen_breakdown_v";
        $data['banner_text'] = "Patient Regimen Breakdown";
        $data['facilities'] = ReportingFacility::all();
        //Get the regimen data
        $data['optimal_regimens'] = Regimen::where(['optimality' => "1", 'source' => '0'])->orderBy('regimen_desc')->get();
        $data['sub_optimal_regimens'] = Regimen::where(['optimality' => "1", 'source' => '2'])->orderBy('regimen_desc')->get();
        $months = 12;
        $months_previous = 11;
        $regimen_data = [];
        for ($current_month = 1; $current_month <= $months; $current_month++) {
            $start_date = date("Y-m-01", strtotime("-$months_previous months"));
            $end_date = date("Y-m-t", strtotime("-$months_previous months"));
            //echo $start_date." to ".$end_date."</br>";
            if ($facility) {
                $get_month_statistics_sql = "SELECT regimen,count(patient_id) as patient_numbers,sum(months_of_stock) as months_of_stock FROM (select  distinct patient_id,months_of_stock,regimen,dispensing_date from `patient_visit` where facility = '" . $facility . "' and  dispensing_date between str_to_date('" . $start_date . "','%Y-%m-%d') and str_to_date('" . $end_date . "','%Y-%m-%d')) patient_visits group by regimen";
            } else {
                $get_month_statistics_sql = "SELECT regimen,count(patient_id) as patient_numbers,sum(months_of_stock) as months_of_stock FROM (select  distinct patient_id,months_of_stock,regimen,dispensing_date from `patient_visit` where dispensing_date between str_to_date('" . $start_date . "','%Y-%m-%d') and str_to_date('" . $end_date . "','%Y-%m-%d')) patient_visits group by regimen";
            }
            $month_statistics_query = (array) DB::select($get_month_statistics_sql);
            foreach ($month_statistics_query as $month_data) {
                $regimen_data[$month_data['regimen']][$start_date] = array("patient_numbers" => $month_data['patient_numbers'], "mos" => $month_data['months_of_stock']);
            }
            //echo $get_month_statistics_sql . "<br>";
            $months_previous--;
        }
        $data['regimen_data'] = $regimen_data;
        echo view("\Modules\ADT\Views\platform_template", $data);
    }

    public function create_appointment_timestamps() {
        /* $appointments = Patient_Appointment::getAll();
          foreach($appointments as $appointment){
          $app_date = $appointment->Appointment;
          $changed_date = strtotime($app_date);
          //echo $app_date." currently becomes ".$changed_date." which was initially ".date("m/d/Y",$changed_date)."<br>";
          $appointment->Appointment = $changed_date;
          $appointment->save();
          } */
    }

    public function export() {
        $facility_code = $this->session->get('facility');
        $sql = "SELECT medical_record_number,patient_number_ccc,first_name,last_name,other_name,dob,pob,IF(gender=1,'MALE','FEMALE')as gender,IF(pregnant=1,'YES','NO')as pregnant,weight as Current_Weight,height as Current_height,sa as Current_BSA,p.phone,physical as Physical_Address,alternate as Alternate_Address,other_illnesses,other_drugs,adr as Drug_Allergies,IF(tb=1,'YES','NO')as TB,IF(smoke=1,'YES','NO')as smoke,IF(alcohol=1,'YES','NO')as alcohol,date_enrolled,ps.name as Patient_source,s.Name as supported_by,timestamp,facility_code,rst.name as Service,r1.regimen_desc as Start_Regimen,start_regimen_date,pst.Name as Current_status,migration_id,machine_code,IF(sms_consent=1,'YES','NO') as SMS_Consent,fplan as Family_Planning,tbphase,startphase,endphase,IF(partner_status=1,'HIV Positive',IF(partner_status=2,'HIV Negative','')) as partner_status,status_change_date,IF(partner_type=1,'YES','NO') as Disclosure,support_group,r.regimen_desc as Current_Regimen,nextappointment,start_height,start_weight,start_bsa,IF(p.transfer_from !='',f.name,'N/A') as Transfer_From,DATEDIFF(nextappointment,CURDATE()) AS Days_to_NextAppointment,dp.name as prophylaxis " .
                "FROM patient p " .
                "left join regimen r on r.id=p.current_regimen " .
                "left join regimen r1 on r1.id=p.start_regimen " .
                "left join patient_source ps on ps.id=p.source " .
                "left join supporter s on s.id=p.supported_by " .
                "left join regimen_service_type rst on rst.id=p.service " .
                "left join patient_status pst on pst.id=p.current_status " .
                "left join facilities f on f.facilitycode=p.transfer_from " .
                "left join drug_prophylaxis dp on dp.id=p.drug_prophylaxis " .
                "WHERE facility_code='" . $facility_code . "' " .
                "ORDER BY p.patient_number_ccc ASC";
        $results = (array) DB::select($sql);

        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);
        $i = 1;

        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, "medical_record_number");
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "patient_number_ccc");
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "first_name");
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "last_name");
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "other_name");
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, "dob");
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "pob");
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "gender");
        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "pregnant");
        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "Current_Weight");
        $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "Current_height");
        $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "Current_BSA");
        $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "phone");
        $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, "Physical_Address");
        $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, "Alternate_Address");
        $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, "other_illnesses");
        $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "other_drugs");
        $objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "Drug_Allergies");
        $objPHPExcel->getActiveSheet()->SetCellValue('S' . $i, "TB");
        $objPHPExcel->getActiveSheet()->SetCellValue('T' . $i, "smoke");
        $objPHPExcel->getActiveSheet()->SetCellValue('U' . $i, "alcohol");
        $objPHPExcel->getActiveSheet()->SetCellValue('V' . $i, "date_enrolled");
        $objPHPExcel->getActiveSheet()->SetCellValue('W' . $i, "Patient_source");
        $objPHPExcel->getActiveSheet()->SetCellValue('X' . $i, "supported_by");
        $objPHPExcel->getActiveSheet()->SetCellValue('Y' . $i, "timestamp");
        $objPHPExcel->getActiveSheet()->SetCellValue('Z' . $i, "facility_code");
        $objPHPExcel->getActiveSheet()->SetCellValue('AA' . $i, "pob");
        $objPHPExcel->getActiveSheet()->SetCellValue('AB' . $i, "Service");
        $objPHPExcel->getActiveSheet()->SetCellValue('AC' . $i, "Start_Regimen");
        $objPHPExcel->getActiveSheet()->SetCellValue('AD' . $i, "start_regimen_date");
        $objPHPExcel->getActiveSheet()->SetCellValue('AE' . $i, "Current_status");
        $objPHPExcel->getActiveSheet()->SetCellValue('AF' . $i, "migration_id");
        $objPHPExcel->getActiveSheet()->SetCellValue('AG' . $i, "machine_code");
        $objPHPExcel->getActiveSheet()->SetCellValue('AH' . $i, "SMS_Consent");
        $objPHPExcel->getActiveSheet()->SetCellValue('AI' . $i, "Family_Planning");
        $objPHPExcel->getActiveSheet()->SetCellValue('AJ' . $i, "tbphase");
        $objPHPExcel->getActiveSheet()->SetCellValue('AK' . $i, "startphase");
        $objPHPExcel->getActiveSheet()->SetCellValue('AL' . $i, "endphase");
        $objPHPExcel->getActiveSheet()->SetCellValue('AM' . $i, "partner_status");
        $objPHPExcel->getActiveSheet()->SetCellValue('AN' . $i, "status_change_date");
        $objPHPExcel->getActiveSheet()->SetCellValue('AO' . $i, "Disclosure");
        $objPHPExcel->getActiveSheet()->SetCellValue('AP' . $i, "support_group");
        $objPHPExcel->getActiveSheet()->SetCellValue('AQ' . $i, "Current_Regimen");
        $objPHPExcel->getActiveSheet()->SetCellValue('AR' . $i, "nextappointment");
        $objPHPExcel->getActiveSheet()->SetCellValue('AS' . $i, "start_height");
        $objPHPExcel->getActiveSheet()->SetCellValue('AT' . $i, "start_weight");
        $objPHPExcel->getActiveSheet()->SetCellValue('AU' . $i, "start_bsa");
        $objPHPExcel->getActiveSheet()->SetCellValue('AV' . $i, "Transfer_From");
        $objPHPExcel->getActiveSheet()->SetCellValue('AW' . $i, "Days_To_NextAppointment");
        $objPHPExcel->getActiveSheet()->SetCellValue('AY' . $i, "Drug_Prophylaxis");

        foreach ($results as $result) {
            $i++;
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $result["medical_record_number"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, $result["patient_number_ccc"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, $result["first_name"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $result["last_name"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $result["other_name"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $result["dob"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, $result["pob"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $result["gender"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, $result["pregnant"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, $result["Current_Weight"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, $result["Current_height"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, $result["Current_BSA"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, $result["phone"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, $result["Physical_Address"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, $result["Alternate_Address"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, $result["other_illnesses"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, $result["other_drugs"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, $result["Drug_Allergies"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('S' . $i, $result["TB"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('T' . $i, $result["smoke"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('U' . $i, $result["alcohol"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('V' . $i, $result["date_enrolled"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('W' . $i, $result["Patient_source"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('X' . $i, $result["supported_by"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('Y' . $i, $result["timestamp"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('Z' . $i, $result["facility_code"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AA' . $i, $result["pob"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AB' . $i, $result["Service"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AC' . $i, $result["Start_Regimen"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AD' . $i, $result["start_regimen_date"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AE' . $i, $result["Current_status"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AF' . $i, $result["migration_id"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AG' . $i, $result["machine_code"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AH' . $i, $result["SMS_Consent"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AI' . $i, $result["Family_Planning"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AJ' . $i, $result["tbphase"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AK' . $i, $result["startphase"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AL' . $i, $result["endphase"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AM' . $i, $result["partner_status"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AN' . $i, $result["status_change_date"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AO' . $i, $result["Disclosure"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AP' . $i, $result["support_group"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AQ' . $i, $result["Current_Regimen"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AR' . $i, $result["nextappointment"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AS' . $i, $result["start_height"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AT' . $i, $result["start_weight"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AU' . $i, $result["start_bsa"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AV' . $i, $result["Transfer_From"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AW' . $i, $result["Days_to_NextAppointment"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AY' . $i, $result["prophylaxis"]);
        }

        if (ob_get_contents())
            ob_end_clean();
        $filename = "Patient Master List For " . $facility_code . ".csv";
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'CSV');

        $objWriter->save('php://output');

        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
    }

    public function enable($id) {
        $patient = Patient::find($id);
        $patient->active = '1';
        $patient->save();

        $get_user = Patient::find($id);
        $first_name = $get_user->first_name ?? '';

        //Set session for notications
        $this->session->set('msg_save_transaction', 'success');
        $this->session->set('user_enabled', $first_name . " was enabled!");
        return redirect()->to(base_url() . "/public/patients");
    }

    public function disable($id) {
        $patient = Patient::find($id);
        $patient->active = '0';
        $patient->save();

        $get_user = Patient::find($id);
        $first_name = $get_user->first_name ?? '';

        //Set session for notications
        $this->session->set('msg_save_transaction', 'success');
        $this->session->set('user_disabled', $first_name . " was disabled!");
        return redirect()->to(base_url() . "/public/patients");
    }

    public function delete($id) {
        $patient = Patient::where('id', $id)->where('active', 0)->delete();
        //Set session for notications
        $this->session->set('msg_save_transaction', 'success');
        $this->session->set('user_disabled', "User Deleted");
        return redirect()->to(base_url() . "/public/patients");
    }

    public function getAppointments($appointment = "") {
        $appointment = $this->uri->getSegment(3);
        $results = "";
        $sql = "select count(distinct(patient)) as total_appointments,weekend_max,weekday_max from patient_appointment pa,facilities f  where pa.appointment = '$appointment' and f.facilitycode=pa.facility";
        $results = DB::select($sql);
        echo json_encode($results);
    }

    public function getSixMonthsDispensing($patient_no = null) {
        //Validate patient_no when use of / to separate mflcode and ccc_no
        $mflcode = $this->uri->getSegment(3);
        $ccc_no = $this->uri->getSegment(4);
        if ($ccc_no) {
            $patient_no = $mflcode . '/' . $ccc_no;
        }

        $dyn_table = "";
        $facility = $this->session->get("facility");

        $sql = "SELECT DATE_FORMAT(pv.dispensing_date,'%d-%b-%Y') as dispensing_date, " .
                "UPPER(dc.Drug) as drug, " .
                "pv.quantity, " .
                "pv.pill_count, " .
                "pv.missed_pills, " .
                "round(((pv.quantity-(pv.pill_count-pv.months_of_stock))/pv.quantity)*100,2) as pill_adh, " .
                "round(((pv.quantity-pv.missed_pills)/pv.quantity)*100,2) as missed_adh, " .
                "pv.adherence " .
                "FROM patient_visit pv " .
                "LEFT JOIN patient p ON p.patient_number_ccc=pv.patient_id " .
                "LEFT JOIN drugcode dc ON dc.id=pv.drug_id " .
                "WHERE pv.patient_id = ? " .
                "AND pv.facility = ? " .
                "GROUP BY dispensing_date,pv.patient_id, pv.drug_id " .
                "ORDER BY pv.dispensing_date DESC";
        $results = DB::select($sql, [$patient_no, $facility]);

        if ($results) {
            foreach ($results as $result) {
                $dyn_table .= "<tbody><tr>";
                $dyn_table .= "<td>" . $result->dispensing_date . "</td>";
                $dyn_table .= "<td>" . $result->drug . "</td>";
                $dyn_table .= "<td>" . $result->quantity . "</td>";
                $dyn_table .= "<td>" . $result->pill_count . "</td>";
                $dyn_table .= "<td>" . $result->missed_pills . "</td>";
                $dyn_table .= "<td>" . $result->pill_adh . "%</td>";
                $dyn_table .= "<td>" . $result->missed_adh . "%</td>";

                $adherence = doubleval(str_replace(array("%", "<", ">", "="), "", $result->adherence));
                $average_adherence = (( doubleval($result->pill_adh) + doubleval($result->missed_adh) + $adherence) / 3);
                $dyn_table .= "<td>" . $adherence . "%</td>";
                $dyn_table .= "<td>" . number_format($average_adherence, 2) . "%</td>";
                $dyn_table .= "</tr></tbody>";
            }
        }
        echo $dyn_table;
    }

    public function old_getSixMonthsDispensing($patient_no) {
        $facility = $this->session->get("facility");
        $dyn_table = "";
        $sql = "SELECT pv.pill_count,"
                . "pv.missed_pills,"
                . "ds.frequency,"
                . "ds.value,"
                . "pv.months_of_stock,"
                . "pv.adherence,"
                . "pv.dispensing_date,"
                . "d.drug,"
                . "pv.quantity"
                . " from patient_visit pv"
                . " left join drugcode d on d.id=pv.drug_id "
                . "left join dose ds on ds.Name=pv.dose "
                . "where patient_id = '$patient_no' "
                . "and datediff(curdate(),dispensing_date)<=360 "
                . "and datediff(curdate(),dispensing_date)>=0 "
                . "and pv.facility='$facility'"
                . "and pv.active='1'"
                . "order by pv.dispensing_date desc";
        $results = DB::select($sql);
        if ($results) {
            foreach ($results as $result) {
                if ($result['pill_count'] == "") {
                    $result['pill_count'] = "-";
                }
                if ($result['missed_pills'] == "") {
                    $result['missed_pills'] = "-";
                }
                //Calculate Adherence for Missed Pills
                if ($result['frequency'] == 1) {
                    if ($result['missed_pills'] <= 0) {
                        $self_reporting = "100%";
                    } else if ($result['missed_pills'] < 2 && $result['missed_pills'] > 0) {
                        $self_reporting = "≥95%";
                    } else if ($result['missed_pills'] >= 2 && $result['missed_pills'] <= 4) {
                        $self_reporting = "84-94%";
                    } else if ($result['missed_pills'] >= 5) {
                        $self_reporting = "<85%";
                    }
                } else if ($result['frequency'] == 2) {
                    if ($result['missed_pills'] <= 0) {
                        $self_reporting = "100%";
                    } else if ($result['missed_pills'] <= 3 && $result['missed_pills'] > 0) {
                        $self_reporting = "≥95%";
                    } else if ($result['missed_pills'] >= 4 && $result['missed_pills'] <= 8) {
                        $self_reporting = "84-94%";
                    } else if ($result['missed_pills'] >= 9) {
                        $self_reporting = "<85%";
                    }
                } else {
                    $self_reporting = "-";
                }

                //Calculate Adherence for Pill Count(formula)
                $dosage_frequency = ($result['frequency'] * $result['value']);
                $actual_pill_count = $result['pill_count'];
                $expected_pill_count = $result['months_of_stock'];

                $numerator = ($expected_pill_count - $actual_pill_count);
                $denominator = ($dosage_frequency * 30);
                //$denominator=$expected_pill_count;
                if ($denominator > 0) {
                    $pill_count_reporting = ($numerator / $denominator) * 100;
                    $pill_count_reporting = number_format($pill_count_reporting, 2) . "%";
                } else {
                    $pill_count_reporting = "-";
                }

                if ($result['adherence'] == " ") {
                    $result['adherence'] = "-";
                }

                $dyn_table .= "<tbody><tr><td>" . date('d-M-Y', strtotime($result['dispensing_date'])) . "</td><td>" . $result['drug'] . "</td><td align='center'>" . $result['quantity'] . "</td><td align='center'>" . $result['pill_count'] . "</td><td align='center'>" . $result['missed_pills'] . "</td><td align='center'>" . $pill_count_reporting . "</td><td align='center'>" . $self_reporting . "</td><td align='center'>" . $result['adherence'] . "</td></tr></tbody>";
            }
        }
        echo $dyn_table;
    }

    public function getRegimenChange($patient_no) {
        //Validate patient_no when use of / to separate mflcode and ccc_no
        $mflcode = $this->uri->getSegment(3);
        $ccc_no = $this->uri->getSegment(4);
        if ($ccc_no) {
            $patient_no = $mflcode . '/' . $ccc_no;
        }

        $dyn_table = "";
        $facility = $this->session->get("facility");
        $sql = "select dispensing_date, r1.regimen_desc as current_regimen, r2.regimen_desc as previous_regimen, if(rc.name is null,pv.regimen_change_reason,rc.name) as reason "
                . "from patient_visit pv "
                . "left join regimen r1 on pv.regimen = r1.id"
                . " left join regimen r2 on pv.last_regimen = r2.id"
                . " left join regimen_change_purpose rc on pv.regimen_change_reason = rc.id "
                . "where pv.patient_id = ? "
                . "and pv.facility = ? "
                . "and pv.regimen != pv.last_regimen "
                . "group by dispensing_date,pv.regimen "
                . "order by pv.dispensing_date desc";
        $results = DB::select($sql, [$patient_no, $facility]);
        if ($results) {
            foreach ($results as $result) {
                if ($result->current_regimen == "") {
                    $result->current_regimen = "-";
                }
                if ($result->previous_regimen == "") {
                    $result->previous_regimen = "-";
                }
                if ($result->reason == "") {
                    $result->reason = "-";
                } elseif ($result->reason == "undefined") {
                    $result->reason = "-";
                } elseif ($result->reason == "null") {
                    $result->reason = "-";
                }
                //if ($result['current_regimen'] == "-") {
                $dyn_table .= "<tbody><tr><td>" . date('d-M-Y', strtotime($result->dispensing_date)) . "</td><td>" . $result->current_regimen . "</td><td align='center'>" . $result->previous_regimen . "</td><td align='center'>" . $result->reason . "</td></tr></tbody>";
                //}
            }
        }
        echo $dyn_table;
    }

    public function getAppointmentHistory($patient_no) {
        //Validate patient_no when use of / to separate mflcode and ccc_no
        $mflcode = $this->uri->getSegment(3);
        $ccc_no = $this->uri->getSegment(4);
        if ($ccc_no) {
            $patient_no = $mflcode . '/' . $ccc_no;
        }

        $dyn_table = "";
        $status = "";
        $facility = $this->session->get("facility");
        $sql = "SELECT pa.appointment,IF(pa.appointment=pv.dispensing_date,'Visited',DATEDIFF(pa.appointment,curdate()))as Days_To " .
                "FROM(SELECT patient,appointment FROM patient_appointment pa WHERE patient = ? AND facility = ?) as pa,(SELECT patient_id,dispensing_date FROM patient_visit WHERE patient_id = ? AND facility = ?) as pv GROUP BY pa.appointment ORDER BY pa.appointment desc";
        $results = DB::select($sql, [$patient_no, $facility, $patient_no, $facility]);
        if ($results) {
            foreach ($results as $result) {

                if ($result->Days_To > 0) {
                    $status = "<td align='center'>" . $result->Days_To . " Days To</td>";
                } else if ($result->Days_To < 0) {
                    $mysql = "select dispensing_date,DATEDIFF(dispensing_date,'" . @$result->appointment . "')as days from patient_visit where patient_id='$patient_no' and dispensing_date>'" . @$result->appointment . "' and facility='$facility' ORDER BY dispensing_date asc LIMIT 1";
                    $myresults = DB::select($mysql);
                    $result->dispensing_date = date('Y-m-d');
                    if ($myresults) {
                        $result->dispensing_date = $myresults[0]->dispensing_date;
                        $result->Days_To = $myresults[0]->days;
                    }
                    $result->Days_To = str_replace("-", "", $result->Days_To);
                    $status = "<td align='center'> Late By " . $result->Days_To . " Days (" . date('d-M-Y', strtotime($result->dispensing_date)) . ")</td>";
                } else {
                    $status = "<td align='center' class='green'>" . $result->Days_To . "</td>";
                }

                $dyn_table .= "<tbody><tr><td>" . date('d-M-Y', strtotime($result->appointment)) . "</td>$status</tr></tbody>";
            }
        }
        echo $dyn_table;
    }

    public function updateLastRegimen() {

        //Get list of patients who changed regimen
        $sql_patient = "SELECT DISTINCT(p.id) as patient_id FROM patient p " .
                "LEFT JOIN patient_visit pv ON pv.patient_id=p.id " .
                "WHERE pv.regimen_change_reason IS NOT NULL";
        $patients = (array) DB::select($sql_patient);
        foreach ($patients as $patient) {
            $patient_id = $patient["patient_id"];
            $sql = "SELECT * FROM patient_visit WHERE regimen_change_reason IS NOT NULL AND patient_id =" . $patient_id . " ORDER BY dispensing_date ASC";

            $result = (array) DB::select($sql);
            foreach ($result as $key => $value) {

                if ($key == 0) {//For the first in the list, get the previous regimen under which the patient was
                    $curr_disp = $result[$key]["dispensing_date"];
                    $s = "SELECT * from patient_visit WHERE dispensing_date <'" . $curr_disp . "' AND patient_id =" . $patient_id . " ORDER BY dispensing_date DESC LIMIT 1";

                    $res = (array) DB::select($s);

                    if (count($res) > 0) {
                        //echo (count($res))."<br>";
                        $regimen = $res[0]["regimen"];
                        $sql = "UPDATE patient_visit SET last_regimen =" . $regimen . " WHERE id =" . $result[$key]["id"];
                        DB::statement($sql);
                    }
                } else {
                    $x = $key - 1;
                    //Get last regimen
                    //Check if patients was not dispensed under same regimen
                    if ($result[$x]["regimen"] != $result[$key]["regimen"]) {
                        //Update current_patient visit last regimen column
                        $sql = "UPDATE patient_visit SET last_regimen =" . $result[$x]["regimen"] . " WHERE id =" . $result[$key]["id"];
                        $count = DB::select($sql);
                    }
                }
            }
        }
    }

    public function updatePregnancyStatus() {
        $patient_ccc = $this->post("patient_ccc");
        //Check if patient is on PMTCT and change them to ART
        $sql = "SELECT rst.name FROM patient p " .
                "LEFT JOIN regimen_service_type rst ON p.service = rst.id " .
                "WHERE p.patient_number_ccc ='" . $patient_ccc . "'";
        $result = DB::select($sql);
        $service = $result[0]->name;
        $extra = '';
        if (stripos($service, "pmtct") === 0) {
            $sql_get_art = "SELECT id FROM regimen_service_type WHERE name LIKE '%art%'";
            $result = DB::select($sql_get_art);
            $art_service_id = $result[0]->id;
            $extra = ", service = '$art_service_id' ";
        }
        $sql = "UPDATE patient SET pregnant = '0', breastfeeding = '0' $extra WHERE patient_number_ccc ='$patient_ccc'";
        $count = DB::select($sql);
    }

    public function update_tb_status() {
        $patient_ccc = $this->post("patient_ccc");
        $patient = Patient::where('patient_number_ccc', $patient_ccc)->first();
        $patient->tb = '0';
        $patient->save();
    }

    public function getWhoStage() {
        $patient_ccc = $this->post("patient_ccc");
        $result = Patient::where('patient_number_ccc', $patient_ccc)->first();
        $data['patient_who'] = trim($result->who_stage);

        $result = WhoStage::all()->toArray();
        $data['who_stage'] = $result;
        echo json_encode($data);
    }

    public function updateWhoStage() {
        $patient_ccc = $this->post("patient_ccc");
        $who_stage = $this->post("who_stage");
        $patient = Patient::where('patient_number_ccc', $patient_ccc)->first();
        $patient->who_stage = $who_stage;
        $patient->save();
    }

    function generateReport() {

        $query = "";
        $from = $this->post('from');
        $to = $this->post('to');
        //$dateEnrolled = $this->post('dateEnrolled');
        $gender = $this->post('gender');
        $patientsource = $this->post('patientSource');
        $maturity = $this->post('agegroup');
        $service = $this->post('service');
        $startRegimen = $this->post('startRegimen');
        $currRegimen = $this->post('currentRegimen');
        $currStatus = $this->post('currentStatus');
        $smokes = $this->post('smokes');
        $drink = $this->post('alcohol');
        $pregnant = $this->post('pregnant');
        $tb = $this->post('tb');
        $disclosure = $this->post('disclosure');
        $differentiated = $this->post('diffCare');

        if (!empty($from) && empty($to)) {
            $query .= " AND date_enrolled ='$from'";
        }
        if (!empty($to)) {
            $query .= " AND date_enrolled BETWEEN '$from' AND '$to' ";
        }

        $query .= (!empty($gender)) ? " AND gender ='$gender'" : '';
        $query .= (!empty($maturity)) ? " AND maturity ='$maturity'" : '';
        $query .= (!empty($service)) ? " AND service ='$service'" : '';
        $query .= (!empty($startRegimen)) ? " AND start_regimen ='$startRegimen'" : '';
        $query .= (!empty($currRegimen)) ? " AND current_regimen ='$currRegimen'" : '';
        $query .= (!empty($smokes)) ? " AND smoke ='$smokes'" : '';
        $query .= (!empty($drink)) ? " AND alcohol ='$drink'" : '';
        $query .= (!empty($pregnant)) ? " AND pregnant ='$pregnant'" : '';
        $query .= (!empty($tb)) ? " AND tb ='$tb'" : '';
        $query .= (!empty($differentiated)) ? " AND differentiated_care_status ='$differentiated'" : '';
        $query .= (!empty($currStatus)) ? " AND current_status ='$currStatus'" : '';
        $query .= (!empty($disclosure)) ? " AND disclosure ='$disclosure'" : '';
        $query .= (!empty($patientsource)) ? " AND patient_source ='$patientsource'" : '';


        $raw = preg_replace('/AND/', '', $query, 1); // remove first appearance of AND

        if (!empty($query)) {
            $results = $this->query(' WHERE ' . $raw);
            $this->getPatientMasterList($results);
        } else {
            $results = $this->query();
            $this->getPatientMasterList($results);
        }
    }

    function query($raw = '') {
        $query_str = "SELECT ccc_number,first_name,other_name,last_name,date_of_birth,age,maturity,pob,gender,pregnant,adherence,current_weight,current_height,current_bsa,current_bmi,phone_number,physical_address,alternate_address,other_illnesses,other_drugs,drug_allergies,tb,smoke,alcohol,date_enrolled,patient_source,supported_by,service,start_regimen,start_regimen_date,last_regimen,current_status,sms_consent,family_planning,tbphase,startphase,endphase,partner_status,status_change_date,disclosure,support_group,current_regimen,nextappointment,days_to_nextappointment,clinicalappointment,start_height,start_weight,start_bsa,start_bmi,transfer_from,prophylaxis,isoniazid_start_date,isoniazid_end_date,pep_reason,differentiated_care_status,viral_load_test_results,viral_load_test_date
        	from vw_patient_list $raw";
        return DB::select($query_str);
    }

    public function getPatientMasterList($results) {

        ini_set("memory_limit", '2048M');
        helper('file');
        helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "patient_master_list.csv";
        $db = \Config\Database::connect();
        $util = (new \CodeIgniter\Database\Database())->loadUtils($db);
        $data = $util->getCSVFromResult($results, $delimiter, $newline);
        ob_clean(); //Removes spaces
        return $this->response->download($filename, $data);
    }

    public function getPatientMergeList() {
        $iDisplayStart = $this->request->getGet('iDisplayStart');
        $iDisplayLength = $this->request->getGet('iDisplayLength');
        $iSortCol_0 = $this->request->getGet('iSortCol_0');
        $iSortingCols = $this->request->getGet('iSortingCols');
        $sSearch = $this->request->getGet('sSearch');
        $sEcho = $this->request->getGet('sEcho');
        $where = "";
        $facility_code = $this->session->get("facility");

        //columns
        $aColumns = array('id',
            'patient_number_ccc',
            'first_name',
            'other_name',
            'last_name',
            'active');

        $count = 0;


        //Filtering
        if (isset($sSearch) && !empty($sSearch)) {
            $column_count = 0;
            for ($i = 0; $i < count($aColumns); $i++) {
                $bSearchable = $this->request->getGet('bSearchable_' . $i);

                // Individual column filtering
                if (isset($bSearchable) && $bSearchable == 'true') {
                    if ($column_count == 0) {
                        $where .= "(";
                    } else {
                        $where .= " OR ";
                    }
                    $where .= $aColumns[$i] . " LIKE '%$sSearch'";
                    $column_count++;
                }
            }
        }

        //data
        $query = DB::table("patient")->select(DB::raw('SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $aColumns)), false))
                ->where("facility_code", $facility_code);
        // $this->db->from("patient p");
        //  $this->db->where("p.facility_code", $facility_code);
        //search sql clause
        if ($where != "") {
            $where .= ")";
            $query = $query->where(DB::raw($where));
        }

        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $query = $query->limit($iDisplayLength, $iDisplayStart);
        }

        // Paging
        // Ordering
        if (isset($iSortCol_0)) {
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $iSortCol = $this->request->getGet('iSortCol_' . $i, true);
                $bSortable = $this->request->getGet('bSortable_' . intval($iSortCol), true);
                $sSortDir = $this->request->getGet('sSortDir_' . $i, true);

                if ($bSortable == 'true') {
                    $query = $query->orderBy($aColumns [intval($iSortCol)], $sSortDir);
                }
            }
        }

        $rResult = $query->get();

// Data set length after filtering
//        $this->db->select('FOUND_ROWS() AS found_rows');
        $iFilteredTotal = $query->count();

// Total data set length
//        $this->db->select("p.*");
//        $this->db->from("patient p");
//        $this->db->where("p.facility_code", $facility_code);
        $total = Patient::where("facility_code", $facility_code)->get()->toArray();
        $iTotal = count($total);

        // Output
        $msg = array('sEcho' => intval($sEcho),
            'iTotalRecords' => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => array());

//loop through data to parse to josn array
        foreach ($rResult as $patient) {
            $row = array();
            //options
            $links = "<a href='#' class='btn btn-danger btn-mini unmerge_patient' id='" . $patient->id . "'>unmerge</a>";
            $checkbox = "<input type='checkbox' name='patients' class='patients' value='" . $patient->id . "' disabled/>";
            if ($patient->active == 1) {
                $links = "<a href='#' class='btn btn-success btn-mini merge_patient' id='" . $patient->id . "'>Merge</a>";
                $checkbox = "<input type='checkbox' name='patients' class='patients' value='" . $patient->id . "'/>";
            }
            $row[] = $checkbox . " " . $patient->patient_number_ccc;
            $patient_name = $patient->first_name . " " . $patient->other_name . " " . $patient->last_name;
            $row[] = str_replace("  ", " ", $patient_name);
            $row[] = $links;
            $msg['aaData'][] = $row;
        }
        echo json_encode($msg);
    }

    public function merge() {
        //Handle the array with all patients that are to be merged
        $target_patient_id = $this->post('target_ccc');
        $patients = $this->post('patients');
        $patients = array_diff($patients, [$target_patient_id]);

        //Get Target CCC_NO
        $results = Patient::find($target_patient_id);
        if ($results) {
            $target_patient_ccc = $results->patient_number_ccc;
        }
        //loop through merged patients
        foreach ($patients as $patient) {
            //Merging patients involves disabling the patients being merged.
            Patient::where('id', $patient)->update(['active' => '0']);
            //Get CCC_NO
            $results = Patient::find($patient);
            if ($results) {
                $ccc_no = $results->patient_number_ccc;
            }
            //Transfer appointments to target patient
            Patient_appointment::where('patient', $ccc_no)->update([
                'merge' => $ccc_no,
                'patient' => $target_patient_ccc
            ]);
            //Transfer visits to target patient
            PatientVisit::where('patient_id', $ccc_no)->update([
                'migration_id' => $ccc_no,
                'patient_id' => $target_patient_ccc
            ]);
            $patient_no[] = $ccc_no;
        }

        $patients_to_remove = implode(",", $patient_no);

        $this->session->set('message_counter', '1');
        $this->session->set('msg_success', '[' . $patients_to_remove . '] was Merged to [' . $target_patient_ccc . '] !');
        $this->session->set("link_id", "merge_list");
        $this->session->set("linkSub", "/public/patient_management/merge_list");
    }

    public function unmerge() {
        //Handle the array with all patients that are to be unmerged
        $target_patient_id = $this->post('target_ccc');

        //Merging patients involves disabling the patients being merged.
        DB::update("UPDATE patient SET active='1'WHERE id='$target_patient_id'");
        //Patient::where('id', $target_patient_id)->update(['active', '1']);
        //Get Target CCC_NO
        $results = Patient::find($target_patient_id);
        if ($results) {
            $target_patient_ccc = $results->patient_number_ccc;
        }
        //Transfer appointments to original patient
        Patient_appointment::where('merge', $target_patient_ccc)->update(
                ['merge' => '', 'patient' => $target_patient_ccc]
        );
        //Transfer visits and visits to original patient
        PatientVisit::where('migration_id', $target_patient_ccc)->update(
                ['migration_id' => '', 'patient_id' => $target_patient_ccc]
        );

        $this->session->set('message_counter', '1');
        $this->session->set('msg_success', '[' . $target_patient_ccc . '] was unmerged!');
        $this->session->set("link_id", "merge_list");
        $this->session->set("linkSub", "/public/patient_management/merge_list");
    }

    public function load_view($page_id = null, $id = null) {
        $page_id = $this->uri->getSegment(3);
        $id = $this->uri->getSegment(4);
        $this->init_api_values();
        $facilities = Facilities::orderBy('name')->get();
        $config['details'] = [
            'api' => $this->api,
            'patient_module' => $this->patient_module,
            'patient_id' => $id,
            'content_view' => '\Modules\ADT\Views\patients\details_v',
            'hide_side_menu' => '1',
            'patient_msg' => $this->get_patient_relations($id),
            'facilities' => $facilities
        ];
        $this->base_params($config[$page_id]);
    }

    public function get_patient_relations($patient_id = NULL) {

        $results = DB::select("select p.first_name,p.last_name,LOWER(ps.name) as status,dp.child,s.secondary_spouse " .
                        "from patient p " .
                        "left join patient_status ps on ps.id=p.current_status " .
                        "left join dependants dp on p.patient_number_ccc=dp.parent " .
                        "left join spouses s on p.patient_number_ccc=s.primary_spouse " .
                        "where p.id = '" . $patient_id . "'");

        $dependant_msg = "";
        if ($results) {
            $status = $results[0]->status;
            //Check dependedants/spouse status
            $child = $results[0]->child;
            $spouse = $results[0]->secondary_spouse;
            $patient_name = strtoupper($results[0]->first_name . ' ' . $results[0]->last_name);
            if ($child != NULL) {
                $pat = $this->getDependentStatus($child);
                if ($pat != '') {
                    $dependant_msg .= "Patient $patient_name\'s dependant " . $pat . " is lost to follow up ";
                }
            }
            if ($spouse != NULL) {
                $pat = $this->getDependentStatus($spouse);
                if ($pat != '') {
                    $dependant_msg .= "Patient $patient_name\'s spouse " . $pat . " is lost to follow up ";
                }
            }
        }

        return ['status' => $status ?? '', 'message' => $dependant_msg];
    }

    public function load_form($form_id = null) {
        $form_id = $this->uri->getSegment(3);
        $data = [];
        if ($form_id == "patient_details") {
            $data['pob'] = District::where('active', '1')->orderBy('name')->get();
            $data['gender'] = Gender::all();
            $data['current_status'] = PatientStatus::where('active', '1')->orderBy('name')->get();
            $data['source'] = PatientSource::where('active', '1')->orderBy('name')->get();
            $data['drug_prophylaxis'] = DrugProphylaxis::all();
            $data['service'] = RegimenServiceType::where('active', '1')->orderBy('name')->get();
            $data['fplan'] = FamilyPlanning::where('active', '1')->orderBy('name')->get();
            $data['other_illnesses'] = OtherIllnesses::where('active', '1')->orderBy('name')->get();
            $data['pep_reason'] = PepReason::where('active', '1')->orderBy('name')->get();
            $data['prep_reason'] = PrepReason::where('active', '1')->orderBy('name')->get();
            $data['who_stage'] = WhoStage::orderBy('name')->get();
            $regimens = Regimen::where('enabled', '1')->get();
            $data['start_regimen'] = $regimens;
            $data['current_regimen'] = $regimens;

            //Get facilities beacuse of UTF-8 encoding
            $facilities = Facilities::all();
            foreach ($facilities as $facility) {
                $facility_list[] = ['id' => $facility->facilitycode, 'Name' => $facility->name];
            }
            $data['transfer_from'] = $facility_list;

            //Get drug allergies
            $allergies = Drugcode::where('enabled', '1')->orderBy('drug')->get();

            $data['drug_allergies'][] = ['id' => '0', 'Name' => 'None'];
            foreach ($allergies as $allergy) {
                $data['drug_allergies'][] = ['id' => $allergy->id, 'Name' => $allergy->drug];
            }
        } else if ($form_id == "dispensing_frm") {
            $data['ccc_store_sp'] = CCC_store_service_point::where('active', '1')->get();
            $data['visit_purpose'] = VisitPurpose::where('active', '1')->get();
            $data['regimen_change_reason'] = RegimenChangePurpose::where('active', '1')->get();
            $data['non_adherence_reason'] = NonAdherenceReasons::where('active', '1')->get();
            $regimens = Regimen::where('enabled', '1')->get();
            $data['last_regimen'] = $regimens;
            $data['current_regimen'] = $regimens;
        }

        echo json_encode($data);
    }

    public function load_patient($id = null) {
        $id = $this->uri->getSegment(3);
        $details = Patient::find($id)->toArray();

        //Sanitize data
        foreach ($details as $index => $detail) {
            if ($index == "other_illnesses") {
                $illnesses = explode(",", $detail);
                $others = $this->get_other_chronic($illnesses);
                $data[$index] = $others[$index];
                $data["other_chronic"] = $others['other_chronic'];
            } else {
                $data[$index] = $detail;
            }
        }

        //Add Latest Test
        $data = array_merge($data, $this->get_latest_test($id));

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function get_latest_test($patient_id = null) {
        $patient_id = $this->uri->getSegment(3);
        $prep_test_data = [
            'prep_reason' => 0,
            'prep_test_answer' => 0,
            'prep_test_date' => '',
            'prep_test_result' => 0
        ];

        $sql = "SELECT prep_reason_id AS prep_reason, is_tested AS prep_test_answer, test_date AS prep_test_date, test_result AS prep_test_result " .
                "FROM patient_prep_test " .
                "WHERE patient_id = ? " .
                "ORDER BY test_date DESC " .
                "LIMIT 1";
        $result = DB::select($sql, [$patient_id]);
        if (!empty($result)) {
            $prep_test_data = $result;
        }
        return $prep_test_data;
    }

    public function get_visits($patient_id = null) {
        $patient_id = $this->uri->getSegment(3);
        $facility_code = $this->session->get("facility");

        $sql = "select  v_v.dispensing_date,
                        v_v.visit_purpose_name AS visit, 
                        v_v.dose, 
                        v_v.duration, 
                        v_v.patient_visit_id AS record_id, 
                        D.drug, 
                        v_v.quantity, 
                        v_v.current_weight, 
                        R.regimen_desc, 
                        v_v.batch_number, 
                        v_v.pill_count, 
                        v_v.adherence, 
                        v_v.user, 
                        IF(v_v.differentiated_care = '1','YES','NO') AS differentiated_care, 
                        rcp.name AS regimen_change_reason 
                from v_patient_visits as v_v
                INNER JOIN regimen as R ON R.id = v_v.regimen_id
                INNER JOIN drugcode as D ON D.id = v_v.drug_id 
                LEFT JOIN regimen_change_purpose as rcp on v_v.regimen_change_reason = rcp.id
                WHERE v_v.id = $patient_id
                AND v_v.pv_active = 1
                GROUP BY v_v.drug_id,v_v.dispensing_date
                ORDER BY v_v.dispensing_date DESC";

        $visits = DB::select($sql);
        $temp = [];

        foreach ($visits as $counter => $visit) {
            foreach ($visit as $key => $value) {
                if ($key == "record_id") {
                    $link = base_url() . '/public/dispensement_management/edit/' . $value;
                    $value = "<a href='" . $link . "' class='btn btn-small btn-warning'>Edit</a>";
                }

                $temp[$counter][] = $value;
            }
        }

        $data['aaData'] = $temp;

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function load_visits($patient_id = null) {
        $patient_id = $this->uri->getSegment(3);

        $iDisplayStart = $this->post('iDisplayStart');
        $iDisplayLength = $this->post('iDisplayLength');
        $iSortCol_0 = $this->post('iSortCol_0');
        $iSortingCols = $this->post('iSortingCols');
        $sSearch = $this->post('sSearch');
        $sEcho = $this->post('sEcho');
        $facility_code = $this->session->get("facility");

        //Selected columns
        $aColumns = [
            'pv.dispensing_date',
            'v.name AS visit',
            'pv.dose',
            'pv.duration',
            'pv.id AS record_id',
            'd.drug',
            'pv.quantity',
            'pv.current_weight',
            'r1.regimen_desc',
            'pv.batch_number',
            'pv.pill_count',
            'pv.adherence',
            'pv.user',
            'rcp.name AS regimen_change_reason'
        ];

        $builder = DB::table("patient_visit as pv");

        // Paging
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $builder->limit($iDisplayLength, $iDisplayStart);
        }

        // Ordering
        if (isset($iSortCol_0)) {
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $iSortCol = $this->post('iSortCol_' . $i, true);
                $bSortable = $this->post('bSortable_' . intval($iSortCol), true);
                $sSortDir = $this->post('sSortDir_' . $i, true);

                if ($bSortable == 'true') {
                    $builder->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                }
            }
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if (isset($sSearch) && !empty($sSearch)) {
            for ($i = 0; $i < count($aColumns); $i++) {
                $bSearchable = $this->post('bSearchable_' . $i, true);

                // Individual column filtering
                if (isset($bSearchable) && $bSearchable == 'true') {
                    //If 'AS' is found remove it
                    $col = $aColumns[$i];
                    $pos = strpos($col, "AS");

                    if ($pos !== FALSE) {
                        $col = trim($col = substr($col, 0, $pos));
                    }

                    if ($i != 0) {
                        $sWhere .= " OR " . $col . " LIKE '%" . $sSearch . "%'";
                    } else {
                        $sWhere .= "( " . $col . " LIKE '%" . $sSearch . "%'";
                    }
                }
            }
            $sWhere .= ")";
        }

        // Select Data
        $builder->select(DB::raw('SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $aColumns))));
        $builder->leftJoin("patient as p", "pv.patient_id", "=", "p.patient_number_ccc");
        $builder->leftJoin("drugcode as d", "pv.drug_id", "=", "d.id");
        $builder->leftJoin("regimen as r", "pv.regimen", "=", "r.id");
        $builder->leftJoin("regimen as r1", "pv.last_regimen", "=", "r1.id");
        $builder->leftJoin("visit_purpose as v", "pv.visit_purpose", "=", "v.id");
        $builder->leftJoin("regimen_change_purpose rcp", "rcp.id=pv.regimen_change_reason", "left");
        $builder->where("p.id", $patient_id);
        $builder->where("pv.facility", $facility_code);
        $builder->where("pv.active", 1);
        if ($sWhere) {
            $this->db->where(DB::raw($sWhere));
        }
        $builder->groupBy("d.drug, pv.dispensing_date");

        $rResult = $builder->get();

        // echo $this->db->last_query();
        // die();
        // Data set length after filtering
        $this->db->select('FOUND_ROWS() AS found_rows');
        $iFilteredTotal = $builder->count();

        // Total data set length'
        $builder2 = DB::table("patient_visit as pv");
        $builder2->leftJoin("patient as p", "pv.patient_id", "=", "p.patient_number_ccc");
        $builder2->where("p.id", $patient_id);
        $builder2->where("pv.facility", $facility_code);
        $builder2->where("pv.active", 1);
        if ($sWhere) {
            $builder2->whereRaw($sWhere);
        }
        $builder2->groupBy("d.drug, pv.dispensing_date");
        $iTotal = count($builder2->get());

        // Output
        $msg = [
            'sEcho' => intval($sEcho),
            'iTotalRecords' => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => []
        ];

        foreach ($rResult as $count => $aRow) {
            $data = [];
            foreach ($aRow as $col => $value) {
                if ($col == "record_id") {
                    $link = base_url() . '/public/dispensement_management/edit/' . $value;
                    $data[] = "<a href='" . $link . "' class='btn btn-small btn-warning'>Edit</a>";
                } else {
                    $data[] = $value;
                }
            }
            $msg['aaData'][] = $data;
        }

        echo json_encode($msg, JSON_PRETTY_PRINT);
    }

    public function get_other_chronic($illnesses) {
        $illness_list = ['other_illnesses' => '', 'other_chronic' => ''];
        $other_chronic = [];
        $chronic = [];
        if ($illnesses) {
            $indicators = OtherIllnesses::where('active', '1')->orderBy('name')->select('indicator')->get()->toArray();
            foreach ($illnesses as $illness) {
                if (in_array($illness, $indicators)) {
                    $chronic[] = $illness;
                } else {
                    $other_chronic[] = $illness;
                }
            }

            $illness_list['other_illnesses'] = implode(",", $chronic);
            $illness_list['other_chronic'] = implode(",", $other_chronic);
        }
        return $illness_list;
    }

    public function load_summary($patient_id = NULL) {
        //procedure
    }

    public function get_patients($status = null) {
        $filter = "";
        if ($status != NULL) {
            if ($status == 'inactive') {
                $filter .= "AND ps.Name NOT LIKE '%active%'";
            }
        } else {
            $filter .= "AND ps.Name LIKE '%active%'";
        }
        $facility_code = $this->session->get("facility");
        $access_level = $this->session->get('user_indicator');

        $facility_settings = Facilities::where('facilitycode', $facility_code)->first();

        $medical_number = $facility_settings->medical_number;

        $contact_sql = "IF(p.phone='',p.alternate,p.phone) as phone_number,";
        $medical_cond = 'p.patient_number_ccc as ccc_no,';

        if ($medical_number == '1') {
            $medical_cond = 'p.medical_record_number,p.patient_number_ccc as ccc_no,';
            $contact_sql = "";
        }
        $store = $this->session->get('ccc_store_id');

        $sql = "SELECT " . $medical_cond . " UPPER(CONCAT_WS(' ',CONCAT_WS(' ',p.first_name,p.other_name),p.last_name)) as patient_name, " .
                "DATE_FORMAT(p.nextappointment,'%b %D, %Y') as appointment, " . $contact_sql . " " .
                "CONCAT_WS(' | ',r.regimen_code,r.regimen_desc) as regimen, " .
                "ps.name as status, " .
                "p.active, " .
                "p.id, " .
                "p.current_status " .
                "FROM patient p " .
                "LEFT JOIN regimen r ON r.id=p.current_regimen " .
                "LEFT JOIN patient_status ps ON ps.id=p.current_status " .
                "WHERE p.facility_code = '" . $facility_code . "' " .
                "AND p.patient_number_ccc != '' " . $filter . " ";

        $patients = DB::select($sql);

        $temp = [];

        foreach ($patients as $counter => $patient) {
            foreach ($patient as $key => $value) {
                if ($key == "active") {
                    $id = $patient->id;
                    $link = "";
                    //Active Patient
                    if ($access_level == "facility_administrator") {
                        if ($value == 1) {
                            $link = '| <a href="' . base_url() . '/public/patient/disable/' . $id . '" class="red actual">Disable</a>';
                        } else {
                            $link = '| <a href="' . base_url() . '/public/patient/enable/' . $id . '" class="green actual">Enable</a>';
                        }
                    }
                    if ($value == 1) {
                        if (strtolower($patient->status) != 'active') {
                            $link = '<a href="' . base_url() . '/public/patient/load_view/details/' . $id . '">Detail</a> | <a href="' . base_url() . '/public/patient/edit/' . $id . '">Edit</a> ' . $link;
                        } else {
                            $link = '<a href="' . base_url() . '/public/dispensement_management/dispense/' . $id . '">Dispense</a> | <a href="' . base_url() . '/public/patient/load_view/details/' . $id . '">Detail</a> | <a href="' . base_url() . '/public/patient/edit/' . $id . '"> Edit </a> ' . $link;
                        }
                    } else {
                        $link = str_replace("|", "", $link);
                        $link .= '| <a href="' . base_url() . '/public/patient/delete/' . $id . '" class="red actual">Delete</a>';
                    }

                    $value = $link;
                    unset($patient->id);
                }
                $temp [$counter][] = $value;
            }
        }

        $data['aaData'] = $temp;

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function get_patient_details($patient_id = null, $type = null) {
        if (!isset($patient_id)) {
            $patient_id = $this->post('patient_id');
        }

        $sql = "select patient_number_ccc,CONCAT_WS(' ',first_name,last_name,other_name) as names," .
                "height,weight,FLOOR(DATEDIFF(CURDATE(),dob)/365) as Dob," .
                "clinicalappointment,pregnant,tb,isoniazid_start_date,isoniazid_end_date " .
                "from patient where id = ?";
        $query = DB::select($sql, [$patient_id]);
        if (isset($type)) {
            return $query[0] ?? null;
        } else {
            echo json_encode($query[0]);
        }
    }

    //to get the dose for a child patient
    public function get_peadiatric_dose() {
        $weight = $this->post('weight');
        $drug_id = $this->post('drug_id');
        $sql = "select do.id,Name,value,frequency from dossing_chart d " .
                "inner join dose do on do.id=d.dose_id " .
                "where min_weight <= '" . $weight . "' and max_weight >= '" . $weight . "' and drug_id='" . $drug_id . "' and is_active = '1'";

        $data = DB::select($sql);
        echo json_encode($data);
    }

    //get the viral _load_information
    public function get_viral_load_info($patient_id = null) {
        $patient_id = $this->uri->getSegment(3);
        $patient_details = $this->get_patient_details($patient_id, 'array');

        $patient_ccc = $patient_details->patient_number_ccc;

        $msg = 0;
        $max_days_from_enrolled = 180;
        $max_days_to_notification = 10;
        $max_days_to_LDL_test = 365;
        $max_days_for_greater_1000_test = 90;
        $sql = "SELECT p.patient_number_ccc,pv.result,pv.test_date,DATEDIFF(NOW(), test_date) AS test_date_diff, DATEDIFF(NOW(), start_regimen_date) AS start_regimen_date_diff FROM patient p left JOIN  patient_viral_load pv ON p.patient_number_ccc = pv.patient_ccc_number  and p.patient_number_ccc = '$patient_ccc'" .
                "where p.patient_number_ccc = '$patient_ccc' group by p.patient_number_ccc order by test_date desc";

        $datas = DB::select($sql);
        foreach ($datas as $data) {
            $viral_load_test_date = $data->test_date;
            //if patient has no viral_load_test date
            if (empty($viral_load_test_date)) {   //check the viral load test date 
                $start_regimen_date_diff = $data->start_regimen_date_diff;
                //if patient is enrolled in care and there is  ten or less days to viral load test date
                if ($start_regimen_date_diff < $max_days_from_enrolled && (($max_days_from_enrolled - $start_regimen_date_diff) <= $max_days_to_notification)) {
                    $msg = "This patient needs to do viral Load test before " . ($start_regimen_date_diff) . " days from today";
                }
                // no patient_viral load info and 180 days has passed.
                else if ($start_regimen_date_diff > $max_days_from_enrolled) {
                    $msg = "This patient requires a viral load test as there is no viral load Information and 6 months has passed from the date of start regimen";
                }
            }
            //if patient has viral load test date
            else {
                $result = $data->result;
                $test_date_diff = $data->test_date_diff;
                //if LDL
                if ($result == '< LDL copies/ml') {
                    if ($test_date_diff < $max_days_to_LDL_test && (($max_days_to_LDL_test - $test_date_diff) <= $max_days_to_notification )) {
                        $msg = "This patient needs to do viral Load test before " . ($max_days_to_LDL_test - $test_date_diff) . " days from today";
                    } else if ($test_date_diff > $max_days_to_LDL_test) {
                        $msg = "This patient needs to do viral Load test urgently as one year has elapsed from the last test";
                    }
                }
                //else viral is more than 1000
                else if ($result > 1000) {
                    if ($test_date_diff < $max_days_for_greater_1000_test && (($max_days_for_greater_1000_test - $test_date_diff) <= $max_days_to_notification)) {
                        $diff = $max_days_for_greater_1000_test - $test_date_diff;
                        $msg = "This patient needs to do viral Load test  " . ($diff) . " days from today";
                    } else if ($test_date_diff > $max_days_for_greater_1000_test) {
                        $msg = "This patient needs to do viral Load test as 90 days has passed";
                    }
                }
            }
        }
        echo json_encode($msg);
    }

}

ob_get_clean();
?>