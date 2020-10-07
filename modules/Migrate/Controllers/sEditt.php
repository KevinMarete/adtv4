<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Editt extends MX_Controller {

	var $source_db = "";
	var $target_db = "";
	var $cfg = array();
	var $migration_db = array();

	function __construct() {
		parent::__construct();
		ini_set("max_execution_time", "100000");
		ini_set("memory_limit", '2048M');
		//Load defaults
		$this->cfg = $this->get_config('migrator'); 
		$this->migration_db = array('source' => '', 'target' => '');
	}

	public function index()
	{	
		$data = $this->config->config;
		$data['active_menu'] = 3;
		$data['title'] = 'Migration | Toolkit';

		$data['js']= ['public/js/jquery.min.js','public/lib/smartwizard/js/jquery.smartWizard.js','public/lib/select2/js/select2.full.min.js','public/lib/datatables/js/jquery.dataTables.min.js','public/lib/datatables/js/dataTables.select.min.js','public/lib/progressbar/js/progressbar.min.js','public/lib/migrator/js/migrator.js'];
		$data['css']= ['public/lib/smartwizard/css/smart_wizard.css','public/lib/select2/css/select2.min.css','public/lib/datatables/css/jquery.dataTables.min.css','public/lib/migrator/css/migrator.css'];
		$data['content_view'] = "migrate/migrator_view";
		$this -> template($data);

	}
	/*	
	*	Get config
	*	@return array
	*/
	public function get_config($config_file)
	{	
		$conf = $this->load->config($config_file, TRUE);
		// sess_destroy();
		if(!$this->session->userdata('source_database')){
			$this->session->set_userdata($conf);
		}
		$this->cfg = $this->session->userdata;
		return $this->cfg;
	}
	/*	
	*	Get database connection
	*	@return object
	*/
	public function get_db_connection($category = 'source', $allow_multi_db = TRUE)
	{	
		$status = FALSE;
		if(!$this->input->post()){
			//Get config parameters
			$driver = $this->session->userdata($category.'_driver');
			$username = $this->session->userdata($category.'_username');
			$password = $this->session->userdata($category.'_password');
			$hostname = $this->session->userdata($category.'_hostname');
			$port = $this->session->userdata($category.'_port');
			$database = $this->session->userdata($category.'_database');
		}else{
			//Get posted parameters
			$driver = $this->input->post('driver');
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$hostname = $this->input->post('hostname');
			$port = $this->input->post('port');
			$database = $this->input->post('database');
		}

		//Load database using dsn
		$dsn = $driver.'://'.$username.':'.$password.'@'.$hostname.':'.$port.'/'.$database;

		//Check DB Connection Object
		$db_obj = $this->load->database($dsn, $allow_multi_db);
		$connected = $db_obj->initialize();
		$con = mysqli_connect($hostname.':'.$port,$username,$password,$database);
		if (!$con){echo json_encode(array('status' => false)); die;}
		if ($connected){
			//Initialize DB Object
			$this->migration_db[$category] = $db_obj;
			if($this->input->post()){
				//Assign posted values to config
				$this->session->set_userdata($category.'_driver', $driver);
				$this->session->set_userdata($category.'_username', $username);
				$this->session->set_userdata($category.'_password', $password);
				$this->session->set_userdata($category.'_hostname', $hostname);
				$this->session->set_userdata($category.'_port', $port);
				$this->session->set_userdata($category.'_database', $database);
				//Set status
				$status = TRUE;
			}
		}

		//Return
		if(!$this->input->post()){
			return $this->migration_db[$category];
		}else{
			echo json_encode(array('status' => $status));
		}
		
	}
	/*	
	*	Get facilities
	*	@return json
	*/
	public function get_facilities()
	{


		$search = strip_tags(trim($this->input->get('q')));
		$sql = "SELECT facilitycode as id,name FROM facilities WHERE name LIKE ?";
		$query = $this->get_db_connection('target')->query($sql, array('%'.$search.'%'));
		$list = $query -> result_array();
		if(count($list) > 0){
			foreach ($list as $key => $value) {
				$data[] = array('id' => $value['id'], 'text' => $value['name']);			 	
			} 
		} 
		else {
			$data[] = array('id' => '0', 'text' => 'No Facilities Found');
		}
		echo json_encode($data);
	}
	/*	
	*	Get stores
	*	@return json
	*/
	public function get_stores()
	{
		$search = strip_tags(trim($this->input->get('q')));
		$sql = "SELECT id,name FROM ccc_store_service_point WHERE name LIKE ? and active = ?";
		$query = $this->get_db_connection('target')->query($sql, array('%'.$search.'%', 1));
		$list = $query -> result_array();
		if(count($list) > 0){
			foreach ($list as $key => $value) {
				$data[] = array('id' => $value['id'], 'text' => $value['name']);			 	
			} 
		} 
		else {
			$data[] = array('id' => '0', 'text' => 'No stores Found');
		}
		echo json_encode($data);
	}
	/*	
	*	Initialize tables
	*
	* 	By adding migration flag column
	*	@return none
	*/
	public function initialize_tables()
	{

		$conf = $this->config->config;
		  //Get source database object
		$source_db = $this->get_db_connection('source');

  //Set column configuration
		$new_column_name = $conf['migration_flag_column'];
		$new_column_type = $conf['migration_flag_type'];
		$new_column_default_value = $conf['migration_flag_default'];

  //Add column to source database tables
		foreach ($conf['tables'] as $destination_tbl => $source_tbl) {
   //Check if column exists in table then add if it does not
			$sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE  table_schema = ? AND table_name = ?  AND column_name = ?";
			$is_column = $source_db->query($sql, array($source_tbl, $source_db->database, $new_column_name))->result_array();
			if(empty($is_column)){
				$sql = "ALTER TABLE $source_tbl ADD $new_column_name $new_column_type DEFAULT $new_column_default_value";
				$source_db->query($sql);
			}
		}

	}
	/*	
	*	Get tables
	*	@return json
	*/
	public function get_tables()
	{	
		//Initialize tables for migration
		$this->initialize_tables();
		$data = array('data' => array());

		$conf = $this->config->config;
		$tables = $conf['tables'];

		$migration_flag_column = $conf['migration_flag_column'];
		$migration_flag_default = $conf['migration_flag_default'];
		foreach ($tables as $destination_tbl => $source_tbl) {
			$records = $this->get_db_connection('source')->get_where($source_tbl, array($migration_flag_column => $migration_flag_default))->num_rows();

			if($records > 0){
				$records_progress_bar = '<div id="'.$source_tbl.'_bar" total="'.$records.'"></div>';
				$data['data'][] = array($destination_tbl, $source_tbl, $records_progress_bar);
			}
		}	
		echo json_encode($data);	
	}
	/*	
	*	Run SQL file
	*	@return object
	*/
	public function run_sql_file($sqlfile, $db_obj, $db_params = array()){
		$result_set = FALSE;
		$conf = $this->config->config;

		$delimeter = $conf['query_delimiter'];
		$accepted_files = $conf['query_filetype'];

		$ext = pathinfo($sqlfile, PATHINFO_EXTENSION);
		if (in_array($ext, $accepted_files)) {
			if(file_exists($sqlfile)){
				$query_stmt = file_get_contents($sqlfile);
				//Execute query statements
				$statements = explode($delimeter, $query_stmt);
				foreach($statements as $statement){
					$statement = trim($statement);
					if ($statement){
						//Replace params on query
						foreach ($db_params as $key => $value) {
							$statement = str_replace($key, $value, $statement);
						}
						$result_set = $db_obj->query($statement);
					}
				}
			}
		}
		return $result_set;
	}
	/*	
	*	Start Migration
	*	@return json
	*/
	public function start_migration($source_tbl, $destination_tbl, $facility_code, $store_id, $total, $offset = 0)
	{	
		$conf = $this->config->config;

		//Get source data
		$source_params = array(
			'{destination_facility_code}' => $facility_code,
			'{destination_store_id}' => $store_id,
			'{migration_flag_column}' => $conf['migration_flag_column'],
			'{migration_flag_default}' => $conf['migration_flag_default'],
			'{migration_limit}' => $conf['migration_limit'],
			'{migration_offset}' => $conf['migration_offset']
			);
		$source_result = $this->run_sql_file($conf[$destination_tbl.'_query'], $this->get_db_connection('source'), $source_params);
		$source_data = $source_result->result_array();
		if($source_data){
			//Save data to destination
			$this->get_db_connection('target')->insert_batch($destination_tbl, $source_data);

			//Update selected data using table matching indices
			$matching_indices = $conf[$destination_tbl.'_indices'];
			$update_data = array();
			foreach($source_data as $index => $data){	
				foreach ($matching_indices as $key => $value) {
					$update_data[$index][$value] = $data[$key];
				}
				$update_data[$index][$conf['migration_flag_column']] = TRUE;
			}
			$this->get_db_connection('source')->where('name','My Name 2');
			//echo $source_tbl;
			//echo '<pre>';print_r($update_data);echo '</pre>';die();
			//print_r(array_values($matching_indices)[0]);die();
			@$this->get_db_connection('source')->update_batch($source_tbl, $update_data, array_values($matching_indices)[0]);

			//Set offset
			if(($total-$offset) < $conf['migration_limit']){
				$offset = $total;
			}else{
				$offset = $offset + sizeof($source_data);
			}
			//Update destination tables that require updating(during last batch)
			if($offset == $total){
				$update_params = array(
					'{destination_facility_code}' => $facility_code,
					'{destination_store_id}' => $store_id
					);
				$this->run_sql_file(@$conf[$destination_tbl.'_update'], $this->get_db_connection('target'), $update_params);
			}	
		}else{
			$offset = $total;
		}
		echo json_encode(array('offset' => $offset));
	}

	public function template($data) {
		$data['show_menu'] = 0;
		$data['show_sidemenu'] = 0;
		$this -> load -> module('template');
		$this -> template -> index($data);
	}

	protected function setsourceDB($db){
		$this->source_db = $db;
	}
	protected function getsourceDB($db){
		return $this->source_db;
	}


	protected function settargetDB($db){
		$this->target_db = $db;
	}
	protected function gettargetDB($db){
		return $this->target_db;
	}
}