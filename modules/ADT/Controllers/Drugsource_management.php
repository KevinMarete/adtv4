<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Drug_source;
use Illuminate\Database\Capsule\Manager as DB;

class Drugsource_management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "drugsource_management");
        session()->set("linkTitle", "Drug Source Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $sources = Drug_source::getThemAll($access_level);
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('Id', 'Name', 'Options');

        foreach ($sources as $source) {
            $array_param = [
                'id' => $source->id,
                'role' => 'button',
                'class' => 'edit_user',
                'data-toggle' => 'modal',
                'name' => $source->name
            ];
            $links = "";
            if ($source->active == 1) {
                //$links = anchor('drugsource_management/edit/' .$source->id, 'Edit',array('class' => 'edit_user','id'=>$source->id,'name'=>$source->Name));
                $links .= anchor('#edit_form', 'Edit', $array_param);
            }
            if ($access_level == "facility_administrator") {

                if ($source->active == 1) {
                    $links .= " | ";
                    $links .= anchor(base_url().'/drugsource_management/disable/' . $source->id, 'Disable', array('class' => 'disable_user'));
                } else {
                    $links .= anchor(base_url().'/drugsource_management/enable/' . $source->id, 'Enable', array('class' => 'enable_user'));
                }
            }
            $this->table->addRow($source->id, $source->name, $links);
        }

        $data['sources'] = $this->table->generate();
        $data['title'] = "Drug Sources";
        $data['banner_text'] = "Drug Sources";
        $data['link'] = "drugsources";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function save() {
        $creator_id = $this->session->get('user_id');
        $source = $this->session->get('facility');

        $source = new Drug_source();
        $source->Name = $this->request->getPost('source_name');
        $source->Active = "1";
        $source->save();

        $this->session->set('message_counter', '1');
        $this->session->set('msg_success', $this->request->getPost('source_name') . ' was successfully Added!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('source_name')); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function edit($source_id) {
        $data['title'] = "Edit Drug Sources";
        $data['settings_view'] = "editdrugsources_v";
        $data['banner_text'] = "Edit Drug Sources";
        $data['link'] = "drugsources";
        $data['sources'] = Drug_source::getSource($source_id);
        $this->base_params($data);
    }

    public function update() {
        $source_id = $this->request->getPost('source_id');
        $source_name = $this->request->getPost('source_name');


      
        $query = $this->db->query("UPDATE drug_source SET Name='$source_name' WHERE id='$source_id'");
        //$this -> session -> set('message_counter','1');
        $this->session->set('msg_success', $this->request->getPost('source_name') . ' was Updated!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('source_name')); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function enable($source_id) {
      
        $query = $this->db->query("UPDATE drug_source SET Active='1' WHERE id='$source_id'");
        $results = Drug_source::getSource($source_id);
        //$this -> session -> set('message_counter','1');
        $this->session->set('msg_success', $results->name . ' was enabled!');
        $this->session->setFlashdata('filter_datatable', $results->name); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function disable($source_id) {
      
        $query = $this->db->query("UPDATE drug_source SET Active='0' WHERE id='$source_id'");
        $results = Drug_source::getSource($source_id);
        //$this -> session -> set('message_counter','2');
        $this->session->set('msg_error', $results->name . ' was disabled!');
        $this->session->setFlashdata('filter_datatable', $results->name); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function base_params($data) {
        $data['quick_link'] = "drug_sources";
        echo view("\Modules\ADT\Views\\drugsources_v", $data);
    }

}
