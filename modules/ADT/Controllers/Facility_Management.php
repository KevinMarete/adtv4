<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Supporter;
use Illuminate\Database\Capsule\Manager as DB;

class Facility_Management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "facility_management");
        session()->set("linkTitle", "Facility Details Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
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
        $access_level = $this->session->get('user_indicator');
        $source = $this->request->getPost('id');
        $data['facilities'] = Facilities::getCurrentFacility($source);
        echo json_encode($data);
    }

    public function getFacilityList() {
        $response = [];
        $facilities = Facilities::orderBy('name')->get()->toArray();
        foreach ($facilities as $index => $facility) {
            foreach ($facility as $key => $value) {
                $response[$index][$key] = utf8_encode($value);
            }
        }
        echo json_encode($response);
    }

    public function getCurrent() {
        $source = $this->session->get('facility');
        $facilities = Facilities::getCurrentFacility($source);
        echo json_encode($facilities);
    }

    public function update() {
        $art_service = 0;
        $pmtct_service = 0;
        $pep_service = 0;
        $prep_service = 0;
        $service_hep = 0;

        if ($this->request->getPost('art_service') == "on") {
            $art_service = 1;
        }
        if ($this->request->getPost('pmtct_service') == "on") {
            $pmtct_service = 1;
        }
        if ($this->request->getPost('pep_service') == "on") {
            $pep_service = 1;
        }

        if ($this->request->getPost('prep_service') == "on") {
            $prep_service = 1;
        }

        if ($this->request->getPost('hep_service') == "on") {
            $service_hep = 1;
        }


        $facility_id = $this->request->getPost('facility_id');
        if ($facility_id) {
            $data = array(
                'facilitycode' => $this->request->getPost('facility_cod'),
                'name' => $this->request->getPost('facility_name'),
                'ccc_separator' => $this->request->getPost('ccc_separator'),
                'adult_age' => $this->request->getPost('adult_age'),
                'facilitytype' => $this->request->getPost('facility_type'),
                'district' => $this->request->getPost('district'),
                'county' => $this->request->getPost('county'),
                'weekday_max' => $this->request->getPost('weekday_max'),
                'weekend_max' => $this->request->getPost('weekend_max'),
                'lost_to_follow_up' => $this->request->getPost('lost_to_follow_up'),
                'supported_by' => $this->request->getPost('supported_by'),
                'phone' => $this->request->getPost('phone_number'),
                'service_art' => $art_service,
                'service_pmtct' => $pmtct_service,
                'service_pep' => $pep_service,
                'service_prep' => 1,
                'service_hep' => $service_hep,
                'supplied_by' => $this->request->getPost('supplied_by'),
                'parent' => $this->request->getPost('central_site'),
                'map' => $this->request->getPost("sms_map", TRUE),
                'pill_count' => $this->request->getPost('pill_count'),
                'medical_number' => $this->request->getPost('medical_number'),
                'facility_dhis' => $this->request->getPost('facility_dhis'),
                'autobackup' => $this->request->getPost('autobackup')
            );

            $builder = $this->db->table('facilities');
            $builder->where('id', $facility_id);
            $builder->update($data);
            $this->session->set("facility_sms_consent", $this->request->getPost("sms_map", TRUE));
            $this->session->set("lost_to_follow_up", $this->request->getPost('lost_to_follow_up'));
            $this->session->set("autobackup", $this->request->getPost("autobackup", TRUE));
            $this->session->set("facility_dhis", $this->request->getPost("facility_dhis", TRUE));
            $this->session->set('msg_success', $this->request->getPost('facility_name') . ' \'s details were successfully Updated!');
        } else {
            $this->session->set('msg_error', 'Facility details could not be updated!');
        }
        return redirect()->to(base_url() . '/settings_management');
    }

    public function base_params($data) {
        $source = session()->get('facility');
        $access_level = session()->get('user_indicator');
        $data['quick_link'] = "facility";
        if ($access_level == "system_administrator") {
            $data['facilities_list'] = Facilities::orderBy('name')->get()->toArray();
            echo view("\Modules\ADT\Views\\facility_v", $data);
        } else {
            $data['facilities'] = Facilities::where('facilitycode', $source)->get()->toArray();
            //d( $data['facilities']);
            //$data['facilities'] = Facilities::getCurrentFacility($source);
            echo view("\Modules\ADT\Views\\facility_user_v", $data);
        }
    }

}
