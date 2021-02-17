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

class Settings extends \App\Controllers\BaseController {

    var $db;
    var $table;

    function __construct() {
        session()->set("link_id", "listing/regimen_service_type");
        session()->set("linkSub", "settings/listing/regimen_service_type");
        session()->set("linkTitle", "Regimen Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
    }

    public function enable($table = "", $id) {

        $builder = $this->db->table($table);
        //If table is CCC_Store, disable CCC Store in drug_source and drug_destination
        if ($table == "ccc_store_service_point") {
            $builder->where('id', $id);
            $builder->update(array("active" => 1));
            $sql = "SELECT * FROM ccc_store_service_point WHERE id='$id' LIMIT 1";

            $ccc_stores = CCC_store_service_point::getAllActive();
            session()->set('ccc_store', $ccc_stores);
        } else {
            $builder->where('id', $id);
            $builder->update(array("active" => 1));
            $sql = "SELECT * FROM $table WHERE id='$id' LIMIT 1";
        }


        $query = $this->db->query($sql);
        $result = $query->getRow();

        if (in_array($table, array('patient_status', 'regimen_category', 'drug_unit'))) {
            $name = $result->Name;
        } else {
            $name = $result->name;
        }

        session()->set('msg_success', $name . ' was enabled!');
        session()->setFlashdata('filter_datatable', $name);
        session()->set("link_id", "listing/" . $table);
        session()->set("linkSub", "settings/listing/" . $table);
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function disable($table = "", $id) {

        $builder = $this->db->table($table);
        //If table is CCC_Store, disable CCC Store in drug_source and drug_destination
        if ($table == "ccc_store_service_point") {
            $builder->where('id', $id);
            $builder->update(array("active" => 0));

            $sql = "SELECT * FROM ccc_store_service_point WHERE id='$id' LIMIT 1";
            //Get CCC Stores if they exist
            $ccc_stores = CCC_store_service_point::getAllActive();
            session()->set('ccc_store', $ccc_stores);
        } else {
            $builder->where('id', $id);
            $builder->update(array("active" => 0));
            $sql = "SELECT * FROM $table WHERE id='$id' LIMIT 1";
        }


        $query = $this->db->query($sql);
        $result = $query->getRow();

        if (in_array($table, array('patient_status', 'regimen_category', 'drug_unit'))) {
            $name = $result->Name;
        } else {
            $name = $result->name;
        }

        session()->set('msg_error', $name . ' was disabled!');
        $this->session->setFlashdata('filter_datatable', $name);
        session()->set("link_id", "listing/" . $table);
        session()->set("linkSub", "settings/listing/" . $table);
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function listing($table) {
        //$uri = $this->request->uri;
        // $table = $uri->getSegment(3);

        $similar_tables = array('patient_status', 'regimen_category', 'drug_unit');
        $columns = array("#", "Name", "Options");
        if ($table == "transaction_type") {
            $columns = array("#", "Name", "Description", "Effect", "Options");
        } else if ($table == "patient") {
            $columns = array("#", "CCC NO", "Patient Name", "Options");
        }
        $access_level = session()->get('user_indicator');
        $tmpl = array('table_open' => '<table class="setting_table table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading($columns);
        $sql = "SELECT * FROM $table";
        //If table is CCC_Store, get ccc store from either drug_source or drug_destination
        if ($table == "ccc_store_service_point") {
            $sql = "SELECT * FROM ccc_store_service_point";
        } else if ($table == "patient") {
            $sql = "SELECT * FROM patient";
        }
        $query = $this->db->query($sql);
        $sources = $query->getResult();

        foreach ($sources as $source) {

            if ($table == "ccc_store_service_point") {
                $name = $source->name;
                $name = str_replace("ccc_store_", "", $name);
            } else if ($table == "patient") {
                $name = $source->first_name;
                $name .= ' ' . $source->other_name;
                $name .= ' ' . $source->last_name;
                //$name =str_replace(" ","",$name);
                $name = strtoupper($name);
            } else if (in_array($table, $similar_tables)) {
                $name = $source->Name;
            } else {
                $name = $source->name;
            }

            if ($table == "transaction_type") {
                $array_param = array('id' => $source->id, 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal', 'name' => $name, 'desc' => $source->desc, 'effect' => $source->effect);
            } else {
                $array_param = array('id' => $source->id, 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal', 'name' => $name);
            }

            $links = "";
            if ($table == "patient") {
                $links = "<a href='#' class='btn btn-danger btn-mini unmerge_patient' id='" . $source->id . "'>unmerge</a>";
                $checkbox = "<input type='checkbox' name='patients' class='patients' value='" . $source->id . "' disabled/>";
                if ($source->active == 1) {
                    $links = "<a href='#' class='btn btn-success btn-mini merge_patient' id='" . $source->id . "'>Merge</a>";
                    $checkbox = "<input type='checkbox' name='patients' class='patients' value='" . $source->id . "'/>";
                }
                $this->table->addRow("", $checkbox . "&nbsp;" . $source->patient_number_ccc, $name, $links);
            } else {
                if (in_array($table, $similar_tables)) {
                    $active = $source->Active;
                } else {
                    $active = $source->active;
                }
                if ($active == 1) {
                    $links .= anchor('#edit_form', 'Edit', $array_param);
                }
                if ($access_level == "facility_administrator") {

                    if ($active == 1) {
                        $links .= " | ";
                        $links .= anchor(base_url() . '/settings/disable/' . $table . '/' . $source->id, 'Disable', array('class' => 'disable_user'));
                    } else {
                        $links .= anchor(base_url() . '/settings/enable/' . $table . '/' . $source->id, 'Enable', array('class' => 'enable_user'));
                    }
                }
                if ($table == "transaction_type") {
                    $this->table->addRow($source->id, $source->name, $source->desc, $source->effect, $links);
                } else {
                    $this->table->addRow($source->id, $name, $links);
                }
            }
        }
        session()->set("link_id", "listing/" . $table);
        session()->set("linkSub", "settings/listing/" . $table);

        $data['sources'] = $this->table->generate();
        $data['title'] = strtoupper($table);
        $data['banner_text'] = strtoupper($table);
        $data['table'] = $table;
        $data['link'] = $table;
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function save($table = "") {
        $builder = $this->db->table($table);

        $name = $this->request->getPost("source_name");
        //If adding new ccc_store, add CCC Stores in both drug_source and destination, then add ccc_store prefix
        if ($table == "transaction_type") {
            $desc = $this->request->getPost("desc");
            $effect = $this->request->getPost("effect");
            $data_array = array(
                "name" => $name,
                "effect" => $effect,
                "`desc`" => $desc
            );
            $builder->insert($data_array);
        } else {
            $builder->insert(array("name" => $name, "active" => 1));
            // $this->db->insert($table, );
        }

        $ccc_stores = CCC_store_service_point::getAllActive();
        session()->set('ccc_store', $ccc_stores);

        session()->set('message_counter', '1');
        session()->set('msg_success', $this->request->getPost('source_name') . ' was successfully Added!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('source_name'));
        session()->set("link_id", "listing/" . $table);
        session()->set("linkSub", "settings/listing/" . $table);
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function update($table = "") {
        $id = $this->request->getPost("source_id");
        $name = $this->request->getPost("source_name");
        if ($table == "transaction_type") {
            $desc = $this->request->getPost("desc");
            $effect = $this->request->getPost("effect");
            $data_array = array(
                "name" => $name,
                "effect" => $effect,
                "`desc`" => $desc
            );
        } else {
            $data_array = array("name" => $name);
        }
        $builder = $this->db->table($table);
        $builder->where('id', $id);
        $builder->update($data_array);
        $ccc_stores = CCC_store_service_point::getAllActive();
        session()->set('ccc_store', $ccc_stores);

        session()->set('msg_success', $this->request->getPost('source_name') . ' was Updated!');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('source_name'));
        session()->set("link_id", "listing/" . $table);
        session()->set("linkSub", "settings/listing/" . $table);
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    public function base_params($data) {
        $data['quick_link'] = "settings";
        echo view("\Modules\ADT\Views\\mysetting_v", $data);
    }

}
