<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Opportunistic_infection;
use Illuminate\Database\Capsule\Manager as DB;

class Indication_management extends \App\Controllers\BaseController {

    var $db;
    var $table;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "indication_management");
        session()->set("linkTitle", "Drug Indication Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $infections = Opportunistic_Infection::getThemAll($access_level);
        //dd($infections);
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('Id', 'Name', 'Options');

        foreach ($infections as $infection) {
            $links = "";

            if ($infection->active == 1) {
                $array_param = array(
                    'id' => $infection->id,
                    'role' => 'button',
                    'class' => 'edit_user',
                    'data-toggle' => 'modal',
                    'name' => $infection->name,
                    'title' => $infection->indication
                );
                //$links = anchor('indication_management/edit/' .$infection->id, 'Edit',array('class' => 'edit_user','id'=>$infection->id,'name'=>$infection->Name));
                $links .= anchor('#edit_form', 'Edit', $array_param);
            }
            if ($access_level == "facility_administrator") {

                if ($infection->active == 1) {
                    $links .= " | ";
                    $links .= anchor('indication_management/disable/' . $infection->id, 'Disable', array('class' => 'disable_user'));
                } else {
                    $links .= anchor('indication_management/enable/' . $infection->id, 'Enable', array('class' => 'enable_user'));
                }
            }
            $infection_temp = "";
            if ($infection->name) {
                $infection_temp = " | " . $infection->name;
            }
            $this->table->addRow($infection->id, $infection->indication . $infection_temp, $links);
        }

        $data['indications'] = $this->table->generate();
        $data['title'] = "Drug Indications";
        $data['banner_text'] = "Drug Indications";
        $data['link'] = "indications";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function save() {
        $creator_id = $this->session->userdata('user_id');
        $source = $this->session->userdata('facility');

        $indication = new Opportunistic_Infection();
        $indication->Name = $this->input->post('indication_name');
        $indication->Indication = $this->input->post('indication_code');
        $indication->Active = "1";
        $indication->save();

        //$this -> session -> set_userdata('message_counter','1');
        $this->session->set_userdata('msg_success', $this->input->post('indication_code') . ' was Added');
        $this->session->set_flashdata('filter_datatable', $this->input->post('indication_code')); //Filter datatable
        redirect('settings_management');
    }

    public function edit($indication_id) {
        $data['title'] = "Edit Drug Indications";
        $data['settings_view'] = "editindications_v";
        $data['banner_text'] = "Edit Drug Indications";
        $data['link'] = "indications";
        $data['indications'] = Opportunistic_Infection::getIndication($indication_id);
        $this->base_params($data);
    }

    public function update() {
        $indication_id = $this->input->post('indication_id');
        $indication_name = $this->input->post('indication_name');
        $indication_code = $this->input->post('indication_code');


        $this->load->database();
        $query = $this->db->query("UPDATE opportunistic_infection SET Name='$indication_name',Indication='$indication_code' WHERE id='$indication_id'");
        //$this -> session -> set_userdata('message_counter','1');
        $this->session->set_userdata('msg_success', $this->input->post('indication_code') . ' was Updated');
        $this->session->set_flashdata('filter_datatable', $this->input->post('indication_code')); //Filter datatable
        redirect('settings_management');
    }

    public function enable($indication_id) {
        $this->load->database();
        $query = $this->db->query("UPDATE opportunistic_infection SET Active='1'WHERE id='$indication_id'");
        $results = Opportunistic_Infection::getIndication($indication_id);
        //$this -> session -> set_userdata('message_counter','1');
        $this->session->set_userdata('msg_success', $results->Indication . ' was enabled');
        $this->session->set_flashdata('filter_datatable', $results->Indication); //Filter datatable
        redirect('settings_management');
    }

    public function disable($indication_id) {
        $this->load->database();
        $query = $this->db->query("UPDATE opportunistic_infection SET Active='0'WHERE id='$indication_id'");
        $results = Opportunistic_Infection::getIndication($indication_id);
        //$this -> session -> set_userdata('message_counter','2');
        $this->session->set_userdata('msg_error', $results->Indication . ' was disabled');
        $this->session->set_flashdata('filter_datatable', $results->Indication); //Filter datatable
        redirect('settings_management');
    }

    public function base_params($data) {
        $data['quick_link'] = "indications";
        echo view('\Modules\ADT\Views\\indications_v', $data);
    }

}
