<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\Drugcode;
use \Modules\ADT\Models\Drug_source; 
use \Modules\ADT\Models\Drug_unit;
use \Modules\ADT\Models\Drug_classification; 
use \Modules\ADT\Models\Sync_drug;
use \Modules\ADT\Models\Drug_instructions; 
use \Modules\ADT\Models\Generic_name; 
use \Modules\ADT\Models\Supporter; 
use \Modules\ADT\Models\Dose; 
use Illuminate\Database\Capsule\Manager as DB;  

class Drugcode_management extends \App\Controllers\BaseController { 

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "drugcode_management");
        session()->set("linkTitle", "Drug Code Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session(); 
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        //dd($access_level);
        $source = 0;
        if ($access_level == "pharmacist") {
            $source = session()->get('facility');
        }
        $data = array();
        $drugcodes = Drugcode::getAll($source, $access_level);
        //dd($drugcodes);
        $tmpl = array('table_open' => '<table id="drugcode_setting" class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('id', 'Drug', 'Unit', 'Dose', 'Supplier', 'Options');

        // dd($drugcodes);


        foreach ($drugcodes as $drugcode) {
            $array_param = array('id' => $drugcode['id'], 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal');

            $links = "";
            if ($drugcode['Enabled'] == 1) {
                $links .= anchor('#edit_drugcode', 'Edit', $array_param);
            }

            $drug = $drugcode['id'];
            if ($drugcode['Enabled'] == 1 && $access_level == "facility_administrator") {

                $links .= " | ";
                $links .= anchor(base_url() . '/public/drugcode_management/disable/' . $drugcode['id'], 'Disable', array('class' => 'disable_user'));
                $links .= " | ";
                $links .= "<a href='#' class='merge_drug' id='$drug'>Merge</a>";
            } elseif ($access_level == "facility_administrator") {
                $links .= anchor(base_url() . '/public/drugcode_management/enable/' . $drugcode['id'], 'Enable', array('class' => 'enable_user'));
            }
            if ($drugcode['Merged_to'] != '') {
                if ($access_level == "facility_administrator") {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/public/drugcode_management/unmerge/' . $drugcode['id'], 'Unmerge', array('class' => 'unmerge_drug'));
                }
                $checkbox = "<input type='checkbox' name='drugcodes' id='drugcodes' class='drugcodes' value='$drug' disabled/>";
            } else {
                $checkbox = "<input type='checkbox' name='drugcodes' id='drugcodes' class='drugcodes' value='$drug'/>";
            }
            $mapped = "";
            if ($drugcode['map'] != 0) {
                $mapped = "<b>(mapped)</b>";
            }

            $this->table->addRow($drugcode['id'], $checkbox . "&nbsp;" . strtoupper($drugcode['Drug']) . " " . $mapped, "<b>" . $drugcode['Pack_Size'] . "</b>", "<b>" . $drugcode['Dose'] . "</b>", "<b>" . $drugcode['supplier'] . "</b>", $links);
        }

        $data['drugcodes'] = $this->table->generate();
        $data['suppliers'] = Drug_source::getAllHydrated();
        $data['classifications'] = Drug_classification::getAllHydrated($access_level, "0");
        $query = $this->db->query("SELECT s.id,CONCAT_WS('] ',CONCAT_WS(' [',s.name,s.abbreviation),CONCAT_WS(' | ',s.strength,s.formulation)) as name,s.packsize
                                       FROM sync_drug s 
                                       WHERE s.id NOT IN(SELECT dc.map
                                                         FROM drugcode dc
                                                         WHERE dc.map !='0')
                                       AND (s.category_id='1' or s.category_id='2' or s.category_id='3' or s.category_id='4')
                                       AND s.active='1'
                                       ORDER BY name asc");

        $data['edit_mappings'] = $query->getResultArray();
        $data['mappings'] = Sync_Drug::getOrderedActive();
        $data['instructions'] = Drug_instructions::getAllInstructions();
        $this->base_params($data);
    }

    public function add() {
        $data = array();
        $data['drug_units'] = Drug_Unit::getThemAll();
        $data['generic_names'] = Generic_Name::getAllActive();
        $data['supporters'] = Supporter::getAllActive();
        $data['doses'] = Dose::getAllActive();
        echo json_encode($data);
    }

    public function save() {

        //$valid = $this->_submit_validate();
        $access_level = $this->session->set('user_indicator');
        $source = 0;
        if ($access_level == "pharmacist") {
            $source = $this->session->set('facility');
        }
        $non_arv = 0;
        $tb_drug = 0;
        $drug_in_use = 0;
        $supplied = 0;
        if ($this->request->getPost('none_arv') == "on") {
            $non_arv = 1;
        }
        if ($this->request->getPost('tb_drug') == "on") {
            $tb_drug = 1;
        }
        if ($this->request->getPost('drug_in_use') == "on") {
            $drug_in_use = 1;
        }

        //get drug instructions
        $instructions = $this->request->getPost('instructions_holder', TRUE);
        if ($instructions == null) {
            $instructions = "";
        }

        $drugcode = new Drugcode();
        $drugcode->Drug = $this->request->getPost('drugname');
        $drugcode->Unit = $this->request->getPost('drugunit');
        $drugcode->Pack_Size = $this->request->getPost('packsize');
        $drugcode->Safety_Quantity = $this->request->getPost('safety_quantity');
        $drugcode->Generic_Name = $this->request->getPost('genericname');
        $drugcode->Supported_By = $this->request->getPost('supplied_by');
        $drugcode->classification = $this->request->getPost('classification');
        $drugcode->none_arv = $non_arv;
        $drugcode->Tb_Drug = $tb_drug;
        $drugcode->Drug_In_Use = $drug_in_use;
        $drugcode->Comment = $this->request->getPost('comments');
        $drugcode->Dose = $this->request->getPost('dose_frequency');
        $drugcode->Duration = $this->request->getPost('duration');
        $drugcode->Quantity = $this->request->getPost('quantity');
        $drugcode->Strength = $this->request->getPost('dose_strength');
        $drugcode->map = $this->request->getPost('drug_mapping');
        $drugcode->Source = $source;
        $drugcode->instructions = $instructions;

        $drugcode->save();
        //$this -> session -> set('message_counter', '1');
        $this->session->set('msg_success', $this->request->getPost('drugname') . ' was successfully Added!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('drugname'));
        //Filter after saving
        return redirect()->to(base_url() . '/public/settings_management');
    }

    //}

    public function edit() {
        $drugcode_id = $this->request->getPost('drugcode_id');
        $data['generic_names'] = Generic_Name::getAllActive();
        $data['drug_units'] = Drug_Unit::getThemAll();
        $data['doses'] = Dose::getAllActive();
        $data['supporters'] = Supporter::getAllActive();
        $data['doses'] = Dose::getAllActive();
        $data['drugcodes'] = Drugcode::getDrugCodeHydrated($drugcode_id);
        echo json_encode($data);
    }

    public function update() {
        $non_arv = "0";
        $tb_drug = "0";
        $drug_in_use = "0";
        $supplied = 0;
        if ($this->request->getPost('none_arv') == "on") {
            $non_arv = "1";
        }
        if ($this->request->getPost('tb_drug') == "on") {

            $tb_drug = "1";
        }
        if ($this->request->getPost('drug_in_use') == "on") {
            $drug_in_use = "1";
        }

        $source_id = $this->request->getPost('drugcode_id');
        //get drug instructions
        $instructions = $this->request->getPost('instructions_holder', TRUE);
        if ($instructions == null) {
            $instructions = "";
        }

        $data = array('Drug' => $this->request->getPost('drugname'), 'Unit' => $this->request->getPost('drugunit'), 'Pack_Size' => $this->request->getPost('packsize'), 'Safety_Quantity' => $this->request->getPost('safety_quantity'), 'Generic_Name' => $this->request->getPost('genericname'), 'Supported_By' => $this->request->getPost('supplied_by'), 'classification' => $this->request->getPost('classification'), 'none_arv' => $non_arv, 'tb_drug' => $tb_drug, 'Drug_In_Use' => $drug_in_use, 'Comment' => $this->request->getPost('comments'), 'Dose' => $this->request->getPost('dose_frequency'), 'Duration' => $this->request->getPost('duration'), 'Quantity' => $this->request->getPost('quantity'), 'Strength' => $this->request->getPost('dose_strength'), 'map' => $this->request->getPost('drug_mapping'), 'instructions' => $instructions);

        $builder = $this->db->table('drugcode');
        $builder->where('id', $source_id);
        $builder->update($data);
        //$this -> session -> set('message_counter', '1');
        $this->session->set('msg_success', $this->request->getPost('drugname') . ' was Updated');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('drugname'));
        //Filter after saving
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function enable($drugcode_id = '') {

        if ($this->request->getPost('multiple')) {
            //Handle the array with all drugcodes that are to be merged
            $drugcodes = $this->request->getPost('drug_codes');
            $drugcodes_to_disable = implode(",", $drugcodes);
            $the_query = "UPDATE drugcode SET enabled='1' WHERE id IN($drugcodes_to_disable);";
            if ($this->db->query($the_query)) {
                $this->session->set('msg_success', 'The selected drugs were successfully enabled!');
            } else {
                $this->session->set('msg_error', 'One or more of the selected drugs were not enabled!');
            }
        } else {
            $query = $this->db->query("UPDATE drugcode SET Enabled='1'WHERE id='$drugcode_id'");
            $results = Drugcode::getDrugCode($drugcode_id);
            //$this -> session -> set('message_counter', '1');
            $this->session->set('msg_success', $results->drug . ' was enabled!');
            $this->session->setFlashdata('filter_datatable', $results->drug);
            //Filter
            return redirect()->to(base_url() . '/public/settings_management');
        }
    }

    public function disable($drugcode_id = '') {
        if ($this->request->getPost('multiple')) {
            //Handle the array with all drugcodes that are to be merged
            $drugcodes = $this->request->getPost('drug_codes');
            $drugcodes_to_disable = implode(",", $drugcodes);
            $the_query = "UPDATE drugcode SET enabled='0' WHERE id IN($drugcodes_to_disable);";
            if ($this->db->query($the_query)) {
                $this->session->set('msg_success', 'The selected drugs were successfully disabled!');
            } else {
                $this->session->set('msg_error', 'One or more of the selected drugs were not disabled!');
            }
        } else {
            $query = $this->db->query("UPDATE drugcode SET Enabled='0'WHERE id='$drugcode_id'");
            $results = Drugcode::getDrugCode($drugcode_id);
            $this->session->set('message_counter', '2');
            $this->session->set('msg_success', $results->drug . ' was disabled!');
            $this->session->setFlashdata('filter_datatable', $results->drug);
            //Filter
            return redirect()->to(base_url() . '/public/settings_management');
        }
    }

    public function merge($primary_drugcode_id) {
        //Handle the array with all drugcodes that are to be merged
        $drugcodes = $this->request->getPost('drug_codes');
        $drugcodes = array_diff($drugcodes, array($primary_drugcode_id));
        $drugcodes_to_remove = implode(",", $drugcodes);

        //First Query that disables the drug_codes that are to be merged
        $the_query = "UPDATE drugcode SET enabled='0',merged_to='$primary_drugcode_id' WHERE id IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Second Query that updates drug_stock_movement table to merge all drug id's in transactions that have the drugcodes that are to be merged with the primary_drugcode_id
        $the_query = "UPDATE drug_stock_movement SET merged_from=drug,drug='$primary_drugcode_id' WHERE drug IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Third Query that updates patient_visit table for all transactions involving the drugcode to be merged with the primary_drugcode_id
        $the_query = "UPDATE patient_visit SET merged_from=drug_id,drug_id='$primary_drugcode_id' WHERE drug_id IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        //Final Query that updates regimen_drug table for all regimens involving the drugcode to be merged with the primary_drugcode_id
        $the_query = "UPDATE regimen_drug SET merged_from=drugcode,drugcode='$primary_drugcode_id' WHERE drugcode IN($drugcodes_to_remove);";
        $this->db->query($the_query);
        $results = Drugcode::getDrugCode($primary_drugcode_id);
        $this->session->set('message_counter', '1');
        $this->session->set('msg_success', $results->drug . ' was Merged!');
    }

    public function unmerge($drugcode) {
        //$this->load->database();
        //First Query that umerges the drug_code
        $the_query = "UPDATE drugcode SET merged_to='' WHERE id='$drugcode';";
        $this->db->query($the_query);
        //Second Query that updates drug_stock_movement table to unmerge all drug id's that match the merged_from column
        $the_query = "UPDATE drug_stock_movement SET drug='$drugcode',merged_from='' WHERE merged_from='$drugcode';";
        $this->db->query($the_query);
        //Third Query that updates patient_visit table to unmerge all drug id's that match the merged_from column
        $the_query = "UPDATE patient_visit SET drug_id='$drugcode',merged_from='' WHERE merged_from='$drugcode';";
        $this->db->query($the_query);
        //Final Query that updates regimen_drug table to unmerge all drug id's that match the merged_from column
        $the_query = "UPDATE regimen_drug SET drugcode='$drugcode',merged_from='' WHERE merged_from='$drugcode';";
        $this->db->query($the_query);

        $results = Drugcode::getDrugCode($drugcode);
        $this->session->set('message_counter', '1');
        $this->session->set('msg_error', $results->drug . ' was unmerged!');
        return redirect()->to(base_url() . '/public/settings_management');
    }

    private function _submit_validate() {
        // validation rules
        $this->form_validation->set_rules('drugname', 'Drug Name', 'trim|required|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('packsize', 'Pack Size', 'trim|required|min_length[2]|max_length[10]');

        return $this->form_validation->run();
    }

    public function getNonMappedDrugs($param = '0') {
        $data = array();
        $query = $this->db->query("SELECT s.id,CONCAT_WS('] ',CONCAT_WS(' [',s.name,s.abbreviation),CONCAT_WS(' | ',s.strength,s.formulation)) as name,s.packsize
                                       FROM sync_drug s 
                                       WHERE s.id NOT IN(SELECT dc.map
                                                         FROM drugcode dc
                                                         WHERE dc.map !='0')
                                       AND (s.category_id='1' or s.category_id='2' or s.category_id='3' or s.category_id='4')
                                       AND s.active = '1'
                                       ORDER BY name asc");
        $data['sync_drugs'] = $query->getResultArray();
        if ($param == 1) {
            echo json_encode($data['sync_drugs']);
            die();
        }

        $data['non_mapped_drugs'] = Drugcode::getNonMappedDrugs(); //Not mapped regimens
        echo json_encode($data);
    }

    public function updateBulkMapping() {
        $drug_id = $this->request->getPost("drug_id");
        $map_id = $this->request->getPost("map_id");

        $query = $this->db->query("UPDATE drugcode SET map = '$map_id' WHERE id = '$drug_id'");
        $aff = $this->db->affectedRows();
        echo $aff;
    }

    public function base_params($data) {
        $data['styles'] = array("jquery-ui.css");
        $data['scripts'] = array("jquery-ui.js");
        $data['quick_link'] = "drugcode";
        $data['title'] = "Drug Code";
        $data['banner_text'] = "Drug Code Management";
        $data['link'] = "settings_management";
        echo view('\Modules\ADT\Views\\drugcode_listing_v', $data);
    }

}

?>