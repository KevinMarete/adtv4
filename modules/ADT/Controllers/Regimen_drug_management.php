<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Access_log;
use \Modules\ADT\Models\Transaction_type;
use \Modules\ADT\Models\Drug_source;
use \Modules\ADT\Models\Drugcode;
use \Modules\ADT\Models\Drug_destination;
use \Modules\ADT\Models\Regimen;
use \Modules\ADT\Models\Regimen_Category;
use \Modules\ADT\Models\Regimen_service_type;
use \Modules\ADT\Models\Sync_regimen;
use \Modules\ADT\Models\CCC_store_service_point;
use \Modules\ADT\Models\Drug_Stock_Movement;
use Illuminate\Database\Capsule\Manager as DB;

class Regimen_drug_management extends \App\Controllers\BaseController {

    var $db;
    var $table;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "regimen_drug_management");
        session()->set("linkTitle", "Regimen Drug Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $source = 0;
        if ($access_level == "pharmacist") {
            $source = session()->get('facility');
        }
        $data = array();
        $data['styles'] = array("jquery-ui.css");
        $data['scripts'] = array("jquery-ui.js");
        //SELECT * from Regimen where Source =  $source  or Source ='0' order By Regimen_Desc asc
        $data['regimens'] = Regimen::where('source', $source)->orWhere('source', '0')->limit(5)->get();  
        foreach(  $data['regimens'] as $i=> $rd){
            //dd($rd->Regimen_Drug[$i]->Drugcode->drug);
        };
    
        $data['regimens_enabled'] = Regimen::getAllEnabled($source);
        $data['regimen_categories'] = Regimen_Category::getAll();
        $data['regimen_service_types'] = Regimen_Service_Type::getAll();
        $data['drug_codes'] = Drugcode::getAll($source);
        $data['drug_codes_enabled'] = Drugcode::getAllEnabled($source);
        $this->base_params($data);
    }

    public function save() {
        if ($this->input->post()) {
            $access_level = session()->get('user_indicator');
            $source = 0;
            $drug_message = array();

            if ($access_level == "pharmacist") {
                $source = session()->get('facility');
            }
            //get drugs selected
            $drugs = $this->input->post('drugs_holder', TRUE);
            if ($drugs != null) {
                $drugs = explode(",", $drugs);
                foreach ($drugs as $drug) {
                    //get drug name
                    $results = Drugcode::getDrugCode($drug);
                    //check if drug and regimen composite key is duplicate
                    $duplicate = $this->check_duplicate($this->input->post('regimen'), $drug);
                    if ($duplicate == false) {
                        $regimen_drug = new Regimen_Drug();
                        $regimen_drug->Regimen = $this->input->post('regimen');
                        $regimen_drug->Drugcode = $drug;
                        $regimen_drug->Source = $source;
                        $regimen_drug->save();
                        $message = " was successfully Added!";
                    } else {
                        $message = " exists could not be added!";
                    }
                    $drug_message[] = $results->Drug . $message;
                }
                $drug_message = implode(",", $drug_message);
                $this->session->set_userdata('msg_success', $drug_message);
            } else {
                $drug_message = "Failed!No drugs were be selected.";
                $this->session->set_userdata('msg_success', $drug_message);
            }
        }
        redirect('settings_management');
    }

    public function enable($regimen_drug_id) {
        $this->load->database();
        $query = $this->db->query("UPDATE regimen_drug SET active='1'WHERE drugcode='$regimen_drug_id'");
        $results = Drugcode::getDrugCode($regimen_drug_id);
        //$this -> session -> set_userdata('message_counter', '1');
        echo $results->Drug . ' was enabled!';
    }

    public function disable($regimen_drug_id) {
        $this->load->database();
        $query = $this->db->query("UPDATE regimen_drug SET active='0'WHERE drugcode='$regimen_drug_id'");
        $results = Drugcode::getDrugCode($regimen_drug_id);
        //$this -> session -> set_userdata('message_counter', '2');
        echo $results->Drug . ' was disabled!';
    }

    public function check_duplicate($regimen_id, $drug_id) {
        $sql = "SELECT * FROM regimen_drug WHERE regimen='$regimen_id' AND drugcode='$drug_id' AND active='1'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        $duplicate = true;
        if (!$results) {
            $duplicate = false;
        }
        return $duplicate;
    }

    public function base_params($data) {
        $data['quick_link'] = "regimen_drug";
        $data['title'] = "Regimen_Drug Management";
        $data['banner_text'] = "Regimen Drug Management";
        $data['link'] = "settings_management";
        echo view('\Modules\ADT\Views\\regimen_drug_listing_v', $data);
    }

}

ob_get_clean();
?>