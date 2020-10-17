<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Supporter;
use Illuminate\Database\Capsule\Manager as DB;

class Facility_Management extends \App\Controllers\BaseController {

    var $db;
    var $table;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "facility_management");
        session()->set("linkTitle", "Facility Details Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        ini_set("max_execution_time", "1000000");
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $data['access_level'] = $access_level;
        $data['sites'] = Facilities::getFacilities();
       // dd( $data['sites']);
        $data['supporter'] = Supporter::getAll();
        //get satellites
        //$data['satellites'] = Facilities::getSatellites($this -> session -> userdata("facility"));
        $district_query = $this->db->query("select * from district");
        $data['districts'] = $district_query->getResultArray();
        $county_query = $this->db->query("select * from counties");
        $data['counties'] = $county_query->getResultArray();
        $facility_type_query = $this->db->query("select * from facility_types");
        $data['facility_types'] = $facility_type_query->getResultArray();
        $data['title'] = "Facility Information";
        $data['banner_text'] = "Facility Information";
        $data['link'] = "facility";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function view() {
        $access_level = $this->session->userdata('user_indicator');
        $source = $this->input->post('id');
        $data['facilities'] = Facilities::getCurrentFacility($source);
        echo json_encode($data);
    }

    public function getFacilityList() {
        $response = array();
        $facilities = Facilities::getAll();
        foreach ($facilities as $index => $facility) {
            foreach ($facility as $key => $value) {
                $response[$index][$key] = utf8_encode($value);
            }
        }
        echo json_encode($response);
    }

    public function getCurrent() {
        $source = $this->session->userdata('facility');
        $facilities = Facilities::getCurrentFacility($source);
        echo json_encode($facilities);
    }

    public function update() {
        $art_service = 0;
        $pmtct_service = 0;
        $pep_service = 0;
        $prep_service = 0;
        $service_hep = 0;

        if ($this->input->post('art_service') == "on") {
            $art_service = 1;
        }
        if ($this->input->post('pmtct_service') == "on") {
            $pmtct_service = 1;
        }
        if ($this->input->post('pep_service') == "on") {
            $pep_service = 1;
        }

        if ($this->input->post('prep_service') == "on") {
            $prep_service = 1;
        }

        if ($this->input->post('hep_service') == "on") {
            $service_hep = 1;
        }


        $facility_id = $this->input->post('facility_id');
        if ($facility_id) {
            $data = array(
                'facilitycode' => $this->input->post('facility_cod'),
                'name' => $this->input->post('facility_name'),
                'ccc_separator' => $this->input->post('ccc_separator'),
                'adult_age' => $this->input->post('adult_age'),
                'facilitytype' => $this->input->post('facility_type'),
                'district' => $this->input->post('district'),
                'county' => $this->input->post('county'),
                'weekday_max' => $this->input->post('weekday_max'),
                'weekend_max' => $this->input->post('weekend_max'),
                'lost_to_follow_up' => $this->input->post('lost_to_follow_up'),
                'supported_by' => $this->input->post('supported_by'),
                'phone' => $this->input->post('phone_number'),
                'service_art' => $art_service,
                'service_pmtct' => $pmtct_service,
                'service_pep' => $pep_service,
                'service_prep' => 1,
                'service_hep' => $service_hep,
                'supplied_by' => $this->input->post('supplied_by'),
                'parent' => $this->input->post('central_site'),
                'map' => $this->input->post("sms_map", TRUE),
                'pill_count' => $this->input->post('pill_count'),
                'medical_number' => $this->input->post('medical_number'),
                'facility_dhis' => $this->input->post('facility_dhis'),
                'autobackup' => $this->input->post('autobackup')
            );

            $this->db->where('id', $facility_id);
            $this->db->update('facilities', $data);
            $this->session->set_userdata("facility_sms_consent", $this->input->post("sms_map", TRUE));
            $this->session->set_userdata("lost_to_follow_up", $this->input->post('lost_to_follow_up'));
            $this->session->set_userdata("autobackup", $this->input->post("autobackup", TRUE));
            $this->session->set_userdata("facility_dhis", $this->input->post("facility_dhis", TRUE));
            $this->session->set_userdata('msg_success', $this->input->post('facility_name') . ' \'s details were successfully Updated!');
        } else {
            $this->session->set_userdata('msg_error', 'Facility details could not be updated!');
        }
        redirect('settings_management');
    }

    public function base_params($data) {
        $source = session()->get('facility');
        $access_level = session()->get('user_indicator');
        $data['quick_link'] = "facility";
        if ($access_level == "system_administrator") {
            $data['facilities_list'] = Facilities::getAll($source);
            echo view("\Modules\ADT\Views\\facility_v", $data);
        } else {
            $sql = "SELECT * FROM Facilities where facilitycode='$source'";
            $query = $this->db->query($sql);
            $data['facilities'] = $query->getResultArray();
            //$data['facilities'] = Facilities::getCurrentFacility($source);
            echo view("\Modules\ADT\Views\\facility_user_v", $data);
        }
    }

}
