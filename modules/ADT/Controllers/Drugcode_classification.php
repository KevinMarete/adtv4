<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\Drug_Classification;
use Illuminate\Database\Capsule\Manager as DB;

class Drugcode_classification extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "drugcode_classification");
        session()->set("linkTitle", "Drug Code Classification");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $data = array();
        $classifications = Drug_Classification::getAllHydrated($access_level);
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('Id', 'Name', 'Options');
        foreach ($classifications as $classification) {
            $links = "";
            $array_param = array('id' => $classification['id'], 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal', 'name' => $classification['name']);
            if ($classification['active'] == 1) {
                $links .= anchor('#edit_form', 'Edit', $array_param);
            }
            //Check if user is an admin
            if ($access_level == "facility_administrator") {

                if ($classification['active'] == 1) {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/drugcode_classification/disable/' . $classification['id'], 'Disable', array('class' => 'disable_user'));
                } else {
                    $links .= anchor(base_url() . '/drugcode_classification/enable/' . $classification['id'], 'Enable', array('class' => 'enable_user'));
                }
            }

            $this->table->addRow($classification['id'], ucwords($classification['name']), $links);
        }
        $data['classifications'] = $this->table->generate();
        $this->base_params($data);
    }

    public function save() {

        //call validation function
        //$valid = $this->_submit_validate();
        $valid = true;
        if ($valid == false) {
            $data['settings_view'] = "classification_v";
            $this->base_params($data);
        } else {
            $drugname = $this->request->getPost("classification_name");
            $generic_name = new Drug_classification();
            $generic_name->Name = $drugname;
            $generic_name->Active = "1";
            $generic_name->save();
            $this->session->set('msg_success', $this->request->getPost('classification_name') . ' was Added');
            $this->session->setFlashdata('filter_datatable', $this->request->getPost('classification_name'));
            //Filter datatable
            return redirect()->to(base_url() . '/settings_management');
        }
    }

    public function update() {
        $classification_id = $this->request->getPost('classification_id');
        $classification_name = $this->request->getPost("edit_classification_name");
        $query = $this->db->query("UPDATE drug_classification SET name='$classification_name' WHERE id='$classification_id'");
        $this->session->set('msg_success', $this->request->getPost('edit_classification_name') . ' was Updated');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('edit_classification_name'));
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function enable($classification_id) {
        $query = $this->db->query("UPDATE drug_classification SET Active='1'WHERE id='$classification_id'");
        $results = Drug_Classification::getClassification($classification_id);
        $this->session->set('msg_success', $results->name . ' was enabled');
        $this->session->setFlashdata('filter_datatable', $results->name);
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function disable($classification_id) {
        $query = $this->db->query("UPDATE drug_classification SET Active='0'WHERE id='$classification_id'");
        $results = Drug_Classification::getClassification($classification_id);
        $this->session->set('msg_error', $results->name . ' was disabled');
        $this->session->setFlashdata('filter_datatable', $results->name);
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    private function _submit_validate() {
        // validation rules
        $this->form_validation->set_rules('classification_name', 'Classification Name', 'trim|required|min_length[2]|max_length[100]');

        return $this->form_validation->run();
    }

    public function base_params($data) {
        $data['quick_link'] = "indications";
        echo view('\Modules\ADT\Views\\classification_v', $data);
    }

}

?>