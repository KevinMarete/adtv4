<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\Regimen_change_purpose;
use Illuminate\Database\Capsule\Manager as DB;

class Regimenchange_management extends \App\Controllers\BaseController {

    var $db;
    var $table;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "regimenchange_management");
        session()->set("linkTitle", "Regimen Change Reason Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = session()->get('user_indicator');
        $sources = Regimen_change_purpose::getThemAll($access_level);
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('Id', 'Name', 'Options');

        foreach ($sources as $source) {
            $links = "";
            if ($source->active == 1) {
                $array_param = array(
                    'id' => $source->id,
                    'role' => 'button',
                    'class' => 'edit_user',
                    'data-toggle' => 'modal',
                    'name' => $source->name
                );
                //$links = anchor('regimenchange_management/edit/' .$source->id, 'Edit',array('id'=>$source->id,'class' => 'edit_user','name'=>$source->Name));
                $links .= anchor('#edit_form', 'Edit', $array_param);
            }
            if ($access_level == "facility_administrator") {

                if ($source->active == 1) {
                    $links .= " | ";
                    $links .= anchor(base_url() . '/regimenchange_management/disable/' . $source->id, 'Disable', array('class' => 'disable_user'));
                } else {

                    $links .= anchor(base_url() . '/regimenchange_management/enable/' . $source->id, 'Enable', array('class' => 'enable_user'));
                }
            }
            $this->table->addRow($source->id, $source->name, $links);
        }

        $data['sources'] = $this->table->generate();
        ;
        $data['title'] = "Regimen change Reasons";
        $data['banner_text'] = "Regimen change Reasons";
        $data['link'] = "Regimen_change_reasons";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function save() {
        $creator_id = session()->get('user_id');
        $source = session()->get('facility');

        $source = new Regimen_change_purpose();
        $source->Name = $_POST['regimenchange_name'];
        $source->Active = "1";
        $source->save();

        //$this -> session -> set_userdata('message_counter','1');
        session()->set('msg_success', $_POST['regimenchange_name'] . ' was Added');
        session()->setFlashdata('filter_datatable', $_POST['regimenchange_name']); //Filter after saving
        return redirect()->to(base_url() . '/settings_management');
    }

    public function edit($source_id) {
        $data['title'] = "Edit Regimen Change reasons";
        $data['settings_view'] = "editclient_v";
        $data['banner_text'] = "Edit Regimen Change reasons";
        $data['link'] = "regimen_change_reasons";
        $data['sources'] = Regimen_change_purpose::getSource($source_id);
        $this->base_params($data);
    }

    public function update() {
        $regimenchange_id = $this->request->getPost('regimenchange_id');
        $regimenchange_name = $this->request->getPost('regimenchange_name');

        $query = $this->db->query("UPDATE Regimen_Change_Purpose SET Name='$regimenchange_name' WHERE id='$regimenchange_id'");
        //$this -> session -> set_userdata('message_counter','1');
        session()->set('msg_success', $this->request->getPost('regimenchange_name') . ' was Updated');
        session()->set('filter_datatable', $this->request->getPost('regimenchange_name')); //Filter after saving
        return redirect()->to(base_url() . '/settings_management');
    }

    public function enable($regimenchange_id) {
        $query = $this->db->query("UPDATE Regimen_Change_Purpose SET Active='1'WHERE id='$regimenchange_id'");
        $results = Regimen_change_purpose::getSource($regimenchange_id);
        //$this -> session -> set_userdata('message_counter','1');
        session()->set('msg_success', $results->name . ' was enabled');
        session()->set('filter_datatable', $results->name); //Filter
        return redirect()->to(base_url() . '/settings_management');
    }

    public function disable($regimenchange_id) {
        $query = $this->db->query("UPDATE Regimen_Change_Purpose SET Active='0'WHERE id='$regimenchange_id'");
        $results = Regimen_change_purpose::getSource($regimenchange_id);
        session()->set('message_counter', '2');
        session()->set('msg_error', $results->name . ' was disabled');
        session()->set('filter_datatable', $results->name); //Filter
        return redirect()->to(base_url() . '/settings_management');
    }

    public function base_params($data) {
        $data['quick_link'] = "regimen_change_reason";
        echo view("\Modules\ADT\Views\\regimenchange_listing_v", $data);
    }

}
