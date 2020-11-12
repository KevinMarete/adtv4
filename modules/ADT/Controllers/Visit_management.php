<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Brand;
use \Modules\ADT\Models\PatientSource;
use \Modules\ADT\Models\VisitPurpose;
use Illuminate\Database\Capsule\Manager as DB;

class Visit_management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "visit_management");
        session()->set("linkTitle", "Visit Purpose Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = $this->session->get('user_indicator');
        $sources = VisitPurpose::getThemAll();
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('Id', 'Name', 'Options');

        foreach ($sources as $source) {
            $array_param = array(
                'id' => $source->id,
                'role' => 'button',
                'class' => 'edit_user',
                'data-toggle' => 'modal',
                'name' => $source->name
            );
            $links = "";
            if ($source->Active == 1) {
                $links .= anchor('#edit_form', 'Edit', $array_param);
            }
            if ($access_level == "facility_administrator") {

                if ($source->Active == 1) {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/visit_management/disable/' . $source->id, 'Disable', array('class' => 'disable_user'));
                } else {
                    $links .= anchor(base_url() . '/visit_management/enable/' . $source->id, 'Enable', array('class' => 'enable_user'));
                }
            }
            $this->table->addRow($source->id, $source->name, $links);
        }

        $data['sources'] = $this->table->generate();
        $data['title'] = "Visit Purposes";
        $data['banner_text'] = "Visit Purposes";
        $data['link'] = "visit";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function save() {
        $creator_id = $this->session->get('user_id');
        $source = $this->session->get('facility');

        $source = new VisitPurpose();
        $source->Name = $this->request->getPost('source_name');
        $source->Active = "1";
        $source->save();
        $this->session->set('msg_success', $this->request->getPost('source_name') . ' was Added');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('source_name')); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function edit($source_id) {
        $data['title'] = "Edit Client Sources";
        $data['settings_view'] = "\Modules\ADT\View\\editclient_v";
        $data['banner_text'] = "Edit Client Sources";
        $data['link'] = "indications";
        $data['sources'] = PatientSource::find($source_id);
        $this->base_params($data);
    }

    public function update() {
        $source_id = $this->request->getPost('source_id');
        $source_name = $this->request->getPost('source_name');
        $query = $this->db->query("UPDATE visit_purpose SET Name='$source_name' WHERE id='$source_id'");
        //$this -> session -> set('message_counter','1');
        $this->session->set('msg_success', $this->request->getPost('source_name') . ' was Updated');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('source_name')); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function enable($source_id) {
     
        $query = $this->db->query("UPDATE visit_purpose SET Active='1'WHERE id='$source_id'");
        $results = VisitPurpose::getSource($source_id);
        $this->session->set('message_counter', '1');
        $this->session->set('msg_success', $results->name . ' was enabled');
        $this->session->setFlashdata('filter_datatable', $results->name); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function disable($source_id) {
     
        $query = $this->db->query("UPDATE visit_purpose SET Active='0'WHERE id='$source_id'");
        $results = VisitPurpose::getSource($source_id);
        //$this -> session -> set('message_counter','2');
        $this->session->set('msg_error', $results->name . ' was disabled');
        $this->session->setFlashdata('filter_datatable', $results->name); //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function base_params($data) {
        $data['quick_link'] = "client_sources";
        echo view("\Modules\ADT\Views\\visit_v", $data);
    }

}
