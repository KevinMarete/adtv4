<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\Dose;
use Illuminate\Database\Capsule\Manager as DB;

class Dose_management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "dose_management");
        session()->set("linkTitle", "Drug Dose Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $doses = Dose::getAll($access_level);
        //dd($doses);
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('id', 'Name', 'Value', 'Frequency', 'Options');

        foreach ($doses as $dose) {
            $links = "";
            if ($dose->Active == 1) {
                $array_param = array(
                    'id' => $dose->id,
                    'role' => 'button',
                    'class' => 'edit_user',
                    'data-toggle' => 'modal'
                );
                //$links = anchor('','Edit',array('class' => 'edit_user','id'=>$dose->id,'name'=>$dose->Name));
                $links .= anchor('#edit_dose', 'Edit', $array_param);
            }
            if ($access_level == "facility_administrator") {

                if ($dose->Active == 1) {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/dose_management/disable/' . $dose->id, 'Disable', array('class' => 'disable_user'));
                } else {
                    $links .= anchor(base_url() . '/dose_management/enable/' . $dose->id, 'Enable', array('class' => 'enable_user'));
                }
            }


            $this->table->addRow($dose->id, $dose->Name, $dose->value, $dose->frequency, $links);
        }

        $data['doses'] = $this->table->generate();
        $data['title'] = "Drug Doses";
        $data['banner_text'] = "Drug Doses";
        $data['link'] = "dose";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function save() {
        $dose = new Dose();
        $dose->Name = $this->request->getPost('dose_name');
        $dose->Value = $this->request->getPost('dose_value');
        $dose->Frequency = $this->request->getPost('dose_frequency');
        $dose->Active = "1";
        $dose->save();

        $this->session->set('message_counter', '1');
        $this->session->set('msg_success', $this->request->getPost('dose_name') . ' was succesfully Added!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('dose_name')); //Filter after saving
        return redirect()->to(base_url() . '/settings_management');
    }

    public function edit() {
        $dose_id = $this->request->getPost("id");
        $data['doses'] = Dose::getDoseHydrated($dose_id);
        echo json_encode($data);
    }

    public function update() {
        $dose_id = $this->request->getPost('dose_id');
        $dose_name = $this->request->getPost('dose_name');
        $dose_value = $this->request->getPost('dose_value');
        $dose_frequency = $this->request->getPost('dose_frequency');

       // $this->load->database();
        $query = $this->db->query("UPDATE dose SET Name='$dose_name',value='$dose_value',frequency='$dose_frequency' WHERE id='$dose_id'");
        $this->session->set('msg_success', $this->request->getPost('dose_name') . ' was Updated!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('dose_name')); //Filter after saving
        return redirect()->to(base_url() . '/settings_management');
    }

    public function enable($dose_id) {
        //$this->load->database();
        $query = $this->db->query("UPDATE dose SET Active='1' WHERE id='$dose_id'");
        $results = Dose::getDose($dose_id);
        //$this -> session -> set('message_counter','1');
        $this->session->set('msg_success', $results->Name . ' was enabled!');
        $this->session->setFlashdata('filter_datatable', $results->Name); //Filter
        return redirect()->to(base_url() . '/settings_management');
    }

    public function disable($dose_id) {
        //$this->load->database();
        $query = $this->db->query("UPDATE dose SET Active='0' WHERE id='$dose_id'");
        $results = Dose::getDose($dose_id);
        //$this -> session -> set('message_counter','2');
        $this->session->set('msg_error', $results->Name . ' was disabled!');
        $this->session->setFlashdata('filter_datatable', $results->Name); //Filter
        return redirect()->to(base_url() . '/settings_management');
    }

    public function base_params($data) {
        $data['quick_link'] = "dose";
        echo view("\Modules\ADT\Views\\dose_v", $data);
    }

}
