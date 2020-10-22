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
    protected $session = null;

	function __construct() {
        $this->session = session();
		$this->session->set("link_id", "/public/listing/sync_drug");
		$this->session->set("linkSub", "/public/order_settings/listing/sync_drug");
        $this->session->set("linkTitle", "Settings Management");
        $this->table = new \CodeIgniter\View\Table();
        $this->db = \Config\Database::connect();
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
                    $disable_link = anchor('order_settings/disable/' . $table . '/' . $result['id'], 'Disable', array('class' => 'disable_user'));
                    $enable_link = anchor('order_settings/enable/' . $table . '/' . $result['id'], 'Enable', array('class' => 'enable_user'));
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
        $this->db->where('id', $id);
        $this->db->update($table, array('Active' => 1));

        //Get details
        $result = $this->db->get_where($table, array('id' => $id))->row_array();

        $this->session->set_userdata('msg_success', $result[$name_column] . ' was enabled!');
        $this->session->set_flashdata('filter_datatable', $result[$name_column]);
        $this->session->set_userdata("link_id", "listing/" . $table);
        $this->session->set_userdata("linkSub", "order_settings/listing/" . $table);
        //Filter datatable
        redirect('settings_management');
    }

    public function disable($table = '', $id) {
        $name_column = 'name';
        if ($table == 'sync_regimen_category')
            $name_column = 'Name';
        //Update status
        $this->db->where('id', $id);
        $this->db->update($table, array('Active' => 0));

        //Get details
        $result = $this->db->get_where($table, array('id' => $id))->row_array();

        $this->session->set_userdata('msg_error', $result[$name_column] . ' was disabled!');
        $this->session->set_flashdata('filter_datatable', $result[$name_column]);
        $this->session->set_userdata("link_id", "listing/" . $table);
        $this->session->set_userdata("linkSub", "order_settings/listing/" . $table);
        //Filter datatable
        redirect('settings_management');
    }

    public function save($table = '') {
        $this->db->insert($table, $this->input->post());

        if ($this->db->affected_rows() > 0) {
            $this->session->set_userdata('msg_success', $this->input->post('name') . ' was successfully Added!');
        } else {
            $this->session->set_userdata('msg_error', $this->input->post('name') . ' was not Added!');
        }
        $this->session->set_userdata('message_counter', '1');
        $this->session->set_flashdata('filter_datatable', $this->input->post('name'));
        $this->session->set_userdata("link_id", "listing/" . $table);
        $this->session->set_userdata("linkSub", "order_settings/listing/" . $table);

        redirect('settings_management');
    }

    public function update($table = '', $id = '') {
        $this->db->where('id', $id);
        $this->db->update($table, $this->input->post());

        if ($this->db->affected_rows() > 0) {
            $this->session->set_userdata('msg_success', $this->input->post('name') . ' was successfully Updated!');
        } else {
            $this->session->set_userdata('msg_error', $this->input->post('name') . ' was not Updated!');
        }
        $this->session->set_userdata('message_counter', '1');
        $this->session->set_flashdata('filter_datatable', $this->input->post('name'));
        $this->session->set_userdata("link_id", "listing/" . $table);
        $this->session->set_userdata("linkSub", "order_settings/listing/" . $table);

        redirect('settings_management');
    }

	public function fetch($table = ''){
        $table = $this->uri->getSegment(3);
		//Set parameters
		$params = [
			'sync_regimen_category' => [
				'name_column' => 'Name AS name',
				'active_column' => 'Active'],
			'sync_regimen' => [
				'name_column' => 'CONCAT_WS(" | ",code,name) AS name',
				'active_column' => 'Active'],
			'sync_facility' => [
				'name_column' => 'name',
				'active_column' => 'Active'],
			'counties' => [
				'name_column' => 'county AS name',
				'active_column' => 'active'],
			'district' => [
				'name_column' => 'name',
				'active_column' => 'active']
        ];
		//Fetch resources
        // $this->db->select(array('id', $params[$table]['name_column']));
        $data = DB::table($table)->select('id', $params[$table]['name_column'])
                ->where([$params[$table]['active_column'] => 1])->get()->toArray();
		// $data = $this->db->order_by('name', 'ASC')->get_where($table, array($params[$table]['active_column'] => 1))->result_array();
		echo json_encode($data);
	}

	public function get_details($table = '', $id = ''){
		$data = $this->db->get_where($table, array('id' => $id))->row_array();
		echo json_encode($data);
	}

    public function base_params($data) {
        $data['quick_link'] = "settings";
        echo view("\Modules\ADT\Views\\mysetting_v", $data);
    }

}
