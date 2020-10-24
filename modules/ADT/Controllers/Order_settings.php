<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Controllers\BaseController;
use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Patient_Source;
use Illuminate\Database\Capsule\Manager as DB;

class Order_settings extends BaseController {

    var $db;
    var $table;
    var $session;

	function __construct() {
        $this->session = session();
		$this->session->set("link_id", "/public/listing/sync_drug");
		$this->session->set("linkSub", "/public/order_settings/listing/sync_drug");
        $this->session->set("linkTitle", "Settings Management");
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
        ini_set("max_execution_time", "1000000");
    }

    public function listing($table = "") {
        //Setup parameters
		$access_level = $this->session->get('user_indicator');
        $seperator = ' | ';
		$exclude_columns = ['Active'];
		$params = [
			'sync_drug' => [
				'columns' => ['ID', 'Name', 'Abbreviation', 'Strength', 'Packsize', 'Formulation', 'Options'],
				'query' => 'SELECT id, name, abbreviation, strength, packsize, formulation, Active FROM sync_drug'
            ],
			'sync_regimen' => [
				'columns' => ['ID','Code', 'Name', 'Options'],
				'query' => 'SELECT id, code, name, Active FROM sync_regimen'
            ],
			'sync_regimen_category' => [
				'columns' => ['ID', 'Name', 'Active'],
				'query' => 'SELECT id, name, Active FROM sync_regimen_category'
            ],
			'sync_facility' => [
				'columns' => ['ID', 'Name', 'Code', 'Category', 'Keph Level', 'Active'],
				'query' => 'SELECT id, name, code, category, keph_level, Active FROM sync_facility'
            ]
        ];

		//Initialize table library
		$tmpl = ['table_open' => '<table class="setting_table table table-bordered table-striped">'];
		$this->table->setTemplate($tmpl);
		$this->table->setHeading($params[$table]['columns']);

		//Load table data
        $query = $this->db->query($params[$table]['query']);
        $results = $query->getResultArray();

        //Append data to table
        foreach ($results as $result) {
			$row = [];
            foreach ($result as $index => $value) {
                if ($index == 'Active') {
                    $edit_link = anchor('#' . $table . '_form', 'Edit', array('id' => $result['id'], 'table' => $table, 'role' => 'button', 'class' => 'edit_setting', 'data-toggle' => 'modal'));
                    $disable_link = anchor(base_url() . '/public/order_settings/disable/' . $table . '/' . $result['id'], 'Disable', array('class' => 'disable_user'));
                    $enable_link = anchor(base_url() . '/public/order_settings/enable/' . $table . '/' . $result['id'], 'Enable', array('class' => 'enable_user'));
                    ;
                    $links = $edit_link;
                    if ($access_level == "facility_administrator") {
                        if ($value == 1) {
                            $links = $edit_link . $seperator . $disable_link;
                        } else {
                            $links = $edit_link . $seperator . $enable_link;
                        }
                    }
                    //Add options links
                    $row[] = $links;
                }
                //Add specific values
                if (!in_array($index, $exclude_columns)) {
                    $row[] = $value;
                }
            }
            $this->table->addRow($row);
        }

        $data['sources'] = $this->table->generate();
        $data['title'] = strtoupper($table);
        $data['banner_text'] = strtoupper($table);
        $data['table'] = $table;
        $data['link'] = $table;
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        $this->base_params($data);
    }

    public function enable($table = '', $id) {
        $name_column = 'name';
        if ($table == 'sync_regimen_category')
            $name_column = 'Name';
        //Update status
        $builder = $this->db->table($table);
        $builder->where('id', $id);
        $builder->update(array('Active' => 1));

        //Get details
        $builder2 = $this->db->table($table);
        $builder2->where('id', $id);
        $result = $builder2->get()->getRowArray();
        //$result = $this->db->get_where($table, array('id' => $id))->row_array();

        $this->session->set('msg_success', $result[$name_column] . ' was enabled!');
        $this->session->setFlashdata('filter_datatable', $result[$name_column]);
        $this->session->set("link_id", "listing/" . $table);
        $this->session->set("linkSub", "order_settings/listing/" . $table);
        //Filter datatable
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function disable($table = '', $id) {
        $name_column = 'name';
        if ($table == 'sync_regimen_category')
            $name_column = 'Name';
        //Update status
        $builder = $this->db->table($table);
        $builder->where('id', $id);
        $builder->update(array('Active' => 0));

        //Get details
        $builder2 = $this->db->table($table);
        $builder2->where('id', $id);
        $result = $builder2->get()->getRowArray();

        // $result = $this->db->get_where($table, array('id' => $id))->row_array();

        $this->session->set('msg_error', $result[$name_column] . ' was disabled!');
        $this->session->setFlashdata('filter_datatable', $result[$name_column]);
        $this->session->set("link_id", "listing/" . $table);
        $this->session->set("linkSub", "order_settings/listing/" . $table);
        //Filter datatable
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function save($table = '') {
        $builder = $this->db->table($table);
        $builder->insert($this->request->getPost());

        if ($this->db->affectedRows() > 0) {
            $this->session->set('msg_success', $this->request->getPost('name') . ' was successfully Added!');
        } else {
            $this->session->set('msg_error', $this->request->getPost('name') . ' was not Added!');
        }
        $this->session->set('message_counter', '1');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('name'));
        $this->session->set("link_id", "listing/" . $table);
        $this->session->set("linkSub", "order_settings/listing/" . $table);

        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function update($table = '', $id = '') {
        $builder = $this->db->table($table);
        $builder->where('id', $id);
        $builder->update($this->request->getPost());

        if ($this->db->affectedRows() > 0) {
            $this->session->set('msg_success', $this->request->getPost('name') . ' was successfully Updated!');
        } else {
            $this->session->set('msg_error', $this->request->getPost('name') . ' was not Updated!');
        }
        $this->session->set('message_counter', '1');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('name'));
        $this->session->set("link_id", "listing/" . $table);
        $this->session->set("linkSub", "order_settings/listing/" . $table);

        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function fetch($table = '') {
        //Set parameters
        $params = array(
            'sync_regimen_category' => array(
                'name_column' => 'Name AS name',
                'active_column' => 'Active'),
            'sync_regimen' => array(
                'name_column' => 'CONCAT_WS(" | ",code,name) AS name',
                'active_column' => 'Active'),
            'sync_facility' => array(
                'name_column' => 'name',
                'active_column' => 'Active'),
            'counties' => array(
                'name_column' => 'county AS name',
                'active_column' => 'active'),
            'district' => array(
                'name_column' => 'name',
                'active_column' => 'active')
        );
        //Fetch resources
        $column = str_replace('`', '', $params[$table]['name_column']);

        $query = DB::table($table)->select('id', DB::raw($column))->where($params[$table]['active_column'], 1)->orderBy('name', 'asc')->get()->toArray();


        //$this->db->select(array('id', $params[$table]['name_column']));
        //$data = $this->db->order_by('name', 'ASC')->get_where($table, array($params[$table]['active_column'] => 1))->getResultArray();
        echo json_encode($query);
    }

    public function get_details($table = '', $id = '') {
        $builder = $this->db->table($table);
        $result = $builder->where('id', $id)->get()->getRowArray();
        //$data = $this->db->($table, array('id' => $id))->row_array();
        echo json_encode($result);
    }

    public function base_params($data) {
        $data['quick_link'] = "settings";
        echo view("\Modules\ADT\Views\\mysetting_v", $data);
    }

}
