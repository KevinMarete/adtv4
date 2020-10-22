<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Generic_name;
use Illuminate\Database\Capsule\Manager as DB;

class Genericname_management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "genericname_management");
        session()->set("linkTitle", "Generic Name Management");
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
        $generics = Generic_Name::getAllHydrated($access_level);
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('Id', 'Name', 'Options');
        foreach ($generics as $generic) {
            $links = "";
            $array_param = array(
                'id' => $generic['id'],
                'role' => 'button',
                'class' => 'edit_user',
                'data-toggle' => 'modal',
                'name' => $generic['name']
            );
            if ($generic['active'] == 1) {
                //$links = anchor('genericname_management/edit/' . $generic['id'], 'Edit', array('class' => 'edit_user','id'=>$generic['id'],'name'=>$generic['Name']));
                $links .= anchor('#edit_form', 'Edit', $array_param);
            }
            //Check if user is an admin
            if ($access_level == "facility_administrator") {

                if ($generic['active'] == 1) {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/public/genericname_management/disable/' . $generic['id'], 'Disable', array('class' => 'disable_user'));
                } else {
                    $links .= anchor(base_url() . '/public/genericname_management/enable/' . $generic['id'], 'Enable', array('class' => 'enable_user'));
                }
            }

            $this->table->addRow($generic['id'], $generic['name'], $links);
        }
        $data['generic_names'] = $this->table->generate();
        $this->base_params($data);
    }

    public function save() {

        //call validation function
        //$valid = $this->_submit_validate();
        $valid = true;
        if ($valid == false) {
            $data['settings_view'] = "generic_listing_v";
            $this->base_params($data);
        } else {
            $drugname = $this->request->getPost("generic_name");
            $source = new Generic_name();
            $source->Name = $drugname;
            $source->Active = "1";
            $source->save();
            //$this->db->replace('generic_name', array('name' => $drugname));
            $this->session->set('msg_success', $this->request->getPost('generic_name') . ' was Added');
            $this->session->setFlashdata('filter_datatable', $this->request->getPost('generic_name')); //Filter datatable
            return redirect()->to(base_url() . '/public/settings_management');
        }
    }

    public function edit($generic_id) {
        $data['title'] = "Edit Generic Name";
        $data['settings_view'] = "editgeneric_v";
        $data['banner_text'] = "Edit Generic Name";
        $data['link'] = "generic";
        $data['generics'] = Generic_Name::getGeneric($generic_id);
        $this->base_params($data);
    }

    public function update() {
        $generic_id = $this->request->getPost('generic_id');
        $generic_name = $this->request->getPost('edit_generic_name');


        $query = $this->db->query("UPDATE generic_name SET Name='$generic_name' WHERE id='$generic_id'");
        $this->session->set('msg_success', $this->request->getPost('edit_generic_name') . ' was Updated');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('edit_generic_name')); //Filter datatable
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function enable($generic_id) {
        $query = $this->db->query("UPDATE generic_name SET Active='1'WHERE id='$generic_id'");
        $results = Generic_Name::getGeneric($generic_id);
        $this->session->set('msg_success', $results->name . ' was enabled');
        $this->session->setFlashdata('filter_datatable', $results->name); //Filter datatable
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function disable($generic_id) {

        $query = $this->db->query("UPDATE generic_name SET Active='0'WHERE id='$generic_id'");
        $results = Generic_Name::getGeneric($generic_id);
        $this->session->set('msg_error', $results->name . ' was disabled');
        $this->session->setFlashdata('filter_datatable', $results->name); //Filter datatable
        return redirect()->to(base_url() . '/public/settings_management');
    }

    private function _submit_validate() {
        // validation rules
        $this->form_validation->set_rules('generic_name', 'Generic Name', 'trim|required|min_length[2]|max_length[100]');

        return $this->form_validation->run();
    }

    public function base_params($data) {
        $data['styles'] = array("jquery-ui.css");
        $data['scripts'] = array("jquery-ui.js");
        $data['quick_link'] = "generic";
        $data['title'] = "Generic Names";
        $data['banner_text'] = "Generic Management";
        $data['link'] = "settings_management";

        echo view('\Modules\ADT\Views\\generic_listing_v', $data);
    }

}
