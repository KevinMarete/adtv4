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

class Regimen_management extends \App\Controllers\BaseController {

	protected $session;
    var $db;
    var $table;

    function __construct() {
		$this->session = session();
		$this->session->set("link_id", "index");
		$this->session->set("linkSub", "regimen_management");
		$this->session->set("linkTitle", "Regimen Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
    }

    public function index() {
        $access_level = session()->get('user_indicator');

        $source = 0;
        if ($access_level == "pharmacist") {
            $source = session()->get('facility');
        }

        $data = array();
        $data['content_view'] = "\Modules\ADT\Views\\\regimen_listing_v";
        $data['styles'] = array("jquery-ui.css");
        $data['scripts'] = array("jquery-ui.js");

        $regimens = Regimen::getAllHydrated($source, $access_level);

        $tmpl = array('table_open' => '<table id="regimen_setting" class="table table-bordered table-hover table-striped setting_table">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('id', 'Regimen', 'Line', 'Regimen Category', 'Type Of Service', 'Options');
        foreach ($regimens as $regimen) {
            $links = "";
            $drug = $regimen['id'];
            $type_of_service = $regimen['Regimen_Service_Type'];

            //if($type_of_service!="ART" && $access_level!="system_administrator"){
            if ($access_level != "facility_administrator") {
                $array_param = array('id' => $regimen['id'], 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal');
                if ($regimen['Enabled'] == 1) {
                    //$links .= anchor('' . $regimen['id'], 'Edit', array('class' => 'edit_user','id'=>$regimen['id']));
                    $links .= anchor('#edit_form', 'Edit', $array_param);
                }
            } elseif ($access_level == "facility_administrator") {
                //href="#entry_form" role="button" id="new_regimen" class="btn" data-toggle="modal"
                $array_param = array('id' => $regimen['id'], 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal');
                if ($regimen['Enabled'] == 1) {
                    $links .= anchor('#edit_form', 'Edit', $array_param);
                }
            }

            if ($regimen['Enabled'] == 1 && @$regimen['Merged_To']) {
                $links .= " | ";
                $links .= anchor(base_url() . '/public/regimen_management/disable/' . $regimen['id'], 'Disable', array('class' => 'disable_user actual'));
            }

            if ($regimen['Enabled'] == 1 && @$regimen['Merged_To'] == "" && $access_level == "facility_administrator") {
                $links .= " | ";
                $links .= anchor(base_url() . '/public/regimen_management/disable/' . $regimen['id'], 'Disable', array('class' => 'disable_user actual'));
                $links .= " | ";
                $links .= "<a href='#' class='merge_drug' id='$drug'>Merge</a>";
            }
            if ($regimen['Enabled'] == 0 && $access_level == "facility_administrator") {
                $links .= anchor(base_url() . '/public/regimen_management/enable/' . $regimen['id'], 'Enable', array('class' => 'enable_user actual'));
            }
            if ($regimen['Merged_To'] != '') {
                if ($access_level == "facility_administrator") {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/public/regimen_management/unmerge/' . $regimen['id'], 'Unmerge', array('class' => 'unmerge_drug'));
                }
                $checkbox = "<input type='checkbox' name='drugcodes' id='drugcodes' class='drugcodes' value='$drug' disabled/>";
            } else {
                $checkbox = "<input type='checkbox' name='drugcodes' id='drugcodes' class='drugcodes' value='$drug'/>";
            }
            $mapped = "";
            if ($regimen['map'] != 0) {
                $mapped = "<b>(mapped)</b>";
            }

            if ($regimen['Regimen_Code']) {
                $regimen_code = $regimen['Regimen_Code'] . " | " . $regimen['Regimen_Desc'];
            } else {
                $regimen_code = $regimen['Regimen_Desc'];
            }
            $this->table->addRow($regimen['id'], $checkbox . "" . $regimen_code . " " . $mapped, $regimen['Line'], $regimen['Regimen_Category'], $regimen['Regimen_Service_Type'], $links);
        }
        $data['access_level'] = $access_level;
        $data['regimens'] = $this->table->generate();

        $data['regimen_categories'] = Regimen_Category::getAll();
        $data['regimen_service_types'] = Regimen_Service_Type::getAll();
        //dd($data['regimen_service_types']);


        $sql = "SELECT s.id,s.code,s.name,sr.Name as category_name,s.category_id
		FROM sync_regimen s 
		LEFT JOIN sync_regimen_category sr ON sr.id = s.category_id
		WHERE s.Active = '1'
		AND sr.Active = '1'
		AND s.id NOT IN(SELECT r.map
		FROM regimen r
		WHERE r.map !='0')
		OR s.name LIKE '%other%'
		OR s.code LIKE '%x%'
		ORDER BY s.category_id,s.code asc";
        $query = $this->db->query($sql);
        $unmapped_regimens = $query->getResultArray();
        $sync_regimens = Sync_Regimen::getActive();
        $data['edit_mappings'] = $unmapped_regimens;
        $data['mappings'] = $sync_regimens;

        $this->base_params($data);
    }

    public function save() {
        $regimencode = $this->request->getPost('regimen_code');
        // if (!strripos($regimencode, "X")){
        // check for duplicate code before 
        $query = $this->db->query("SELECT * FROM regimen WHERE regimen_code = '$regimencode'");

        if (count($query->getResult()) > 0) {

            session()->set('msg_error', 'Regimen Code already exists!');
            $this->session->setFlashdata('filter_datatable', $query->getResult()[0]->regimen_code);
            return redirect(base_url() . '/public/settings_management');
        } else {


            $access_level = session()->get('user_indicator');
            $source = 0;
            if ($access_level == "pharmacist") {
                $source = session()->get('facility');
            }
            $regimen = new Regimen();
            $regimen->Regimen_Code = $this->request->getPost('regimen_code');
            $regimen->Regimen_Desc = $this->request->getPost('regimen_desc');
            $regimen->Category = $this->request->getPost('category');
            $regimen->Line = $this->request->getPost('line');
            $regimen->Type_Of_Service = $this->request->getPost('type_of_service');
            $regimen->Remarks = $this->request->getPost('remarks');
            $regimen->Enabled = "1";
            $regimen->Source = $source;
            $regimen->map = $this->request->getPost('regimen_mapping');

            $regimen->save();
            session()->set('message_counter', '1');
            session()->set('msg_success', $this->request->getPost('regimen_code') . ' was added.');
            $this->session->setFlashdata('filter_datatable', $this->request->getPost('regimen_code'));
            //Filter after saving
            return redirect()->to(base_url() . '/public/settings_management');
        }
        // }
    }

    public function edit() {
        $regimen_id = $this->request->getPost('id');
        $data['regimens'] = Regimen::getHydratedRegimen($regimen_id);
        echo json_encode($data);
    }

    public function update() {
        $regimen_id = $this->request->getPost('regimen_id');
        $regimen_Code = $this->request->getPost('regimen_code');
        $regimen_Desc = $this->request->getPost('regimen_desc');
        $category = $this->request->getPost('category');
        $line = $this->request->getPost('line');
        $type_Of_Service = $this->request->getPost('type_of_service');
        $remarks = str_replace("'", "\'", $this->request->getPost('remarks'));
        $map = $this->request->getPost('regimen_mapping');

        $query = $this->db->query("UPDATE regimen SET regimen_code='$regimen_Code',regimen_desc='$regimen_Desc',category='$category',line='$line',type_of_service='$type_Of_Service',remarks='$remarks',map='$map' WHERE id='$regimen_id'");
        session()->set('message_counter', '1');
        session()->set('msg_success', $this->request->getPost('regimen_code') . ' was Updated');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('regimen_code'));
        //Filter after updating
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function enable($regimen_id = '') {
        if ($this->request->getPost('multiple')) {
            //Handle the array with all drugcodes that are to be merged
            $regimens = $this->request->getPost('drug_codes');
            $regimens_to_disable = implode(",", $regimens);
            $the_query = "UPDATE regimen SET enabled='1' WHERE id IN($regimens_to_disable);";
            if ($this->db->query($the_query)) {
                session()->set('msg_success', 'The selected regimens were successfully enabled!');
            } else {
                session()->set('msg_error', 'One or more of the selected regimens were not enabled!');
            }
        } else {
            $query = $this->db->query("UPDATE regimen SET enabled='1'WHERE id='$regimen_id'");
            $results = Regimen::getRegimen($regimen_id);
            session()->set('message_counter', '1');
            session()->set('msg_success', $results->regimen_code . ' was enabled');
            $this->session->setFlashdata('filter_datatable', $results->regimen_code);
            //Filter

            return redirect()->to(base_url() . '/public/settings_management');
        }
    }

    public function disable($regimen_id = '') {
        if ($this->request->getPost('multiple')) {
            //Handle the array with all drugcodes that are to be merged
            $regimens = $this->request->getPost('drug_codes');
            $regimens_to_disable = implode(",", $regimens);
            $the_query = "UPDATE regimen SET enabled='0' WHERE id IN($regimens_to_disable);";
            if ($this->db->query($the_query)) {
				$this->session->set('msg_success','The selected regimens were successfully disabled!');
            } else {
				$this->session->set('msg_error', 'One or more of the selected regimens were not disabled!');
            }
        } else {
            $query = $this->db->query("UPDATE regimen SET enabled='0'WHERE id='$regimen_id'");
            $results = Regimen::getRegimen($regimen_id);
            //$this -> session -> set_userdata('message_counter', '2');
            session()->set('msg_error', $results->regimen_code . ' was disabled');
            $this->session->setFlashdata('filter_datatable', $results->regimen_code);
            //Filter
            return redirect()->to(base_url() . '/public/settings_management');
        }
    }

    public function merge($primary_drugcode_id) {
        //Handle the array with all regimens that are to be merged
        $drugcodes = $_POST['drug_codes'];
        $drugcodes = array_diff($drugcodes, array($primary_drugcode_id));
        $drugcodes_to_remove = implode(",", $drugcodes);

        //First Query that disables the regimen that are to be merged
        $the_query = "UPDATE regimen SET enabled='0',merged_to='$primary_drugcode_id' WHERE id IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Second Query that updates patient table start_regimen
        $the_query = "UPDATE patient SET start_regimen_merged_from=start_regimen,start_regimen='$primary_drugcode_id' WHERE start_regimen IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Third Query that updates patient table current_regimen
        $the_query = "UPDATE patient SET current_regimen_merged_from=current_regimen,current_regimen='$primary_drugcode_id' WHERE current_regimen IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Fourth Query that updates patient_visit table regimen
        $the_query = "UPDATE patient_visit SET regimen_merged_from=regimen,regimen='$primary_drugcode_id' WHERE regimen IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Fifth Query that updates patient_visit table last_regimen
        $the_query = "UPDATE patient_visit SET last_regimen_merged_from=last_regimen,last_regimen='$primary_drugcode_id' WHERE last_regimen IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Final Query that updates regimen_drug table
        $the_query = "UPDATE regimen_drug SET regimen_merged_from=regimen,regimen='$primary_drugcode_id' WHERE regimen IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        $results = Regimen::getRegimen($primary_drugcode_id);
        session()->set('message_counter', '1');
        session()->set('msg_success', $results->regimen_code . ' was Merged');
    }

    public function unmerge($drugcode) {
       // $this->load->database();
        //First Query that umerges the regimen
        $the_query = "UPDATE regimen SET merged_to='' WHERE id='$drugcode';";
        $this->db->query($the_query);
        //Second Query that updates patient table start_regimen
        $the_query = "UPDATE patient SET start_regimen='$drugcode',start_regimen_merged_from='' WHERE start_regimen_merged_from='$drugcode';";
        $this->db->query($the_query);
        //Third Query that updates patient table current_regimen
        $the_query = "UPDATE patient SET current_regimen='$drugcode',current_regimen_merged_from='' WHERE current_regimen_merged_from='$drugcode';";
        $this->db->query($the_query);
        //Fourth Query that updates patient_visit table
        $the_query = "UPDATE patient_visit SET regimen='$drugcode',regimen_merged_from='' WHERE regimen_merged_from='$drugcode';";
        $this->db->query($the_query);
        //Fifth Query that updates patient_visit table last regimen
        $the_query = "UPDATE patient_visit SET last_regimen='$drugcode',last_regimen_merged_from='' WHERE last_regimen_merged_from='$drugcode';";
        $this->db->query($the_query);
        //Final Query that updates regimen_drug table
        $the_query = "UPDATE regimen_drug SET regimen='$drugcode',regimen_merged_from='' WHERE regimen_merged_from='$drugcode';";
        $this->db->query($the_query);

        $results = Regimen::getRegimen($drugcode);
        session()->set('message_counter', '1');
        session()->set('msg_error', $results->regimen_code . ' was Unmerged');
         return redirect()->to(base_url() . '/public/settings_management');
    }

	public function getRegimenLine($service=false,$pmtct_oi=FALSE) {
        $service = $this->uri->getSegment(3);
        $pmtct_oi = $this->uri->getSegment(4) ?? false;
		if($pmtct_oi){
			$regimens = Regimen::whereHas('regimen_service_type', function($query){
                $query->where('name', 'like', '%pmtct%')->orWhere('name', 'like', '%oi%');
            })->where('enabled', '1')->orderBy('regimen_code')->get()->toArray();
		}else{
			$regimens = Regimen::where('enabled', '1')->where('type_of_service', $service)->orderBy('regimen_code')->get()->toArray();
		}
		echo json_encode($regimens);
	}

    public function getDrugs($regimen) {
        $sql = "select rd.drugcode as drug_id,d.drug as drug_name from drugcode d,regimen_drug rd left join regimen r ON r.id=rd.regimen where (rd.regimen='$regimen' or r.regimen_code='OI') and r.enabled='1' and d.enabled='1' and rd.drugcode=d.id and rd.active='1' group by rd.drugcode order by rd.drugcode desc";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
            echo json_encode($results);
        }
    }

    public function getAllDrugs($regimen = null) {

		$cond = ($regimen == null) ? "UNION SELECT id as drug_id,drug as drug_name FROM drugcode" : "  WHERE (rd.regimen='$regimen' or r.regimen_code LIKE '%oi%') ".
		"AND (d.drug !='NULL') GROUP BY d.id ORDER BY d.drug ASC" ;
		$sql = "SELECT rd.drugcode as drug_id, d.drug as drug_name ". 
		"FROM regimen_drug rd ".
		"LEFT JOIN regimen r ON r.id=rd.regimen ".
		"LEFT JOIN drugcode d ON d.id=rd.drugcode ".$cond;

		$results = DB::select($sql);
		if ($results) {
			echo json_encode($results);
		}
	}
	
	public function getNonMappedRegimens($param='0'){
		$data = array();
		$query = $this->db->query("SELECT s.id,s.code,s.name,sr.Name as category_name,s.category_id
			FROM sync_regimen s 
			LEFT JOIN sync_regimen_category sr ON sr.id = s.category_id
			WHERE s.Active = '1'
			AND sr.Active = '1'
			AND s.id NOT IN(SELECT r.map
			FROM regimen r
			WHERE r.map !='0')
			OR s.name LIKE '%other%'
			ORDER BY s.category_id,s.code asc");
        $data['sync_regimen'] = $query->getResultArray();
        if ($param == 1) {
            echo json_encode($data['sync_regimen']);
            die();
        }

        $data['non_mapped_regimen'] = Regimen::getNonMappedRegimens(); //Not mapped regimens

        echo json_encode($data);
    }

    public function updateBulkMapping() {
        $regimen_id = $this->request->getPost("regimen_id");
        $map_id = $this->request->getPost("map_id");

        $query = $this->db->query("UPDATE regimen SET map = '$map_id' WHERE id = '$regimen_id'");
        $aff = $this->db->affectedRows();
        echo $aff;
    }

    public function getFilteredRegiments() {
        $age = $this->request->getPost("age") + 0;
        $service = $this->request->getPost("service");
        $regimens = "";
        if ($service == 'prep' || $service == 'pep') {
            $regimens = Regimen::getServiceRegimens($service);
        } else {

            if ($age == '') {
                $regimens = Regimen::getRegimens();
            } else {
                if ($age < 15) {
                    //paediatric regimens
                    $regimens = Regimen::getChildRegimens();
                } else if ($age >= 15) {
                    //adult regimens
                    $regimens = Regimen::getAdultRegimens();
                }
            }
        }
        echo json_encode($regimens);
    }

    public function base_params($data) {
        $data['quick_link'] = "regimen";
        $data['title'] = "Regimens";
        $data['page_title'] = "Regimen Management";
        $data['link'] = "settings_management";
        echo view('\Modules\ADT\Views\\regimen_listing_v', $data);
    }

}

?>