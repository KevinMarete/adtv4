<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editt extends MX_Controller {
	/**
	 * Migrator main controller.
	 *
	 * @author Kevin Marete
	 */
	var $cfg = array();
	var $migration_db = array();
	public function __construct()
	{
		parent::__construct();
		ini_set("max_execution_time", "100000");
		ini_set("memory_limit", '2048M');
		//Load defaults
		$this->cfg = $this->get_config('migrator'); 
		$this->migration_db = array('source' => '', 'target' => '');
	}

	public function index()
	{	
		$data = $this->cfg;
		$data['active_menu'] = 3;
		$data['title'] = 'Tools | Editt Migration';
		$this->load->view('migrator_view', $data);
	}
	/*	
	*	Get config
	*	@return array
	*/
	public function get_config($config_file)
	{	
		$this->load->config($config_file, TRUE);
		if(!$this->session->userdata('source_database')){
			$this->session->set_userdata($this->config->item($config_file));
		}
		return $this->session->userdata();
	}
	/*	
	*	Get database connection
	*	@return object
	*/
	public function get_db_connection($category = 'source', $allow_multi_db = TRUE)
	{	
		ini_set('memory_limit', '-1'); 
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
		$db_obj = @$this->load->database($dsn, $allow_multi_db);
		
		$connected = @$db_obj->initialize();
		if ($connected){
			$status = TRUE;
			//Assign posted values to config
			$this->session->set_userdata($category.'_driver', $driver);
			$this->session->set_userdata($category.'_username', $username);
			$this->session->set_userdata($category.'_password', $password);
			$this->session->set_userdata($category.'_hostname', $hostname);
			$this->session->set_userdata($category.'_port', $port);
			$this->session->set_userdata($category.'_database', $database);
		}else{
		// echo "<pre>";	var_dump($db_obj);die;
			return FALSE;
		}

		//Return
		if(!$this->input->post()){
			return $db_obj;
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
		//Pass to source database object
		$db_obj = $this->get_db_connection('source');
		if($db_obj){
			$this->myforge = $this->load->dbforge($db_obj, TRUE);
			//Set column to be added
			$fields[$this->cfg['migration_flag_column']] = array(
				'type' => $this->cfg['migration_flag_type'],
				'default' => $this->cfg['migration_flag_default']
	        );
			//Add column to source database tables
			foreach ($this->cfg['tables'] as $destination_tbl => $source_tbl) {
				$this->myforge->add_column($source_tbl, $fields);
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
		$tables = $this->cfg['tables'];
		$migration_flag_column = $this->cfg['migration_flag_column'];
		$migration_flag_default = $this->cfg['migration_flag_default'];
		foreach ($tables as $destination_tbl => $source_tbl) {
			$db_obj = $this->get_db_connection('source');
			if($db_obj){
				$records = $db_obj->get_where($source_tbl, array($migration_flag_column => $migration_flag_default))->num_rows();
				if($records > 0){
					$records_progress_bar = '<div id="'.$source_tbl.'_bar" total="'.$records.'"></div>';
					$data['data'][] = array($destination_tbl, $source_tbl, $records_progress_bar);
				}
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
		$delimeter = $this->cfg['query_delimiter'];
		$accepted_files = $this->cfg['query_filetype'];

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
		//Get source data
		$source_params = array(
			'{destination_facility_code}' => $facility_code,
			'{destination_store_id}' => $store_id,
			'{migration_flag_column}' => $this->cfg['migration_flag_column'],
			'{migration_flag_default}' => $this->cfg['migration_flag_default'],
			'{migration_limit}' => $this->cfg['migration_limit'],
			'{migration_offset}' => $this->cfg['migration_offset']
		);
		$source_result = $this->run_sql_file($this->cfg[$destination_tbl.'_query'], $this->get_db_connection('source'), $source_params);
		$source_data = $source_result->result_array();
		if($source_data){
			//Save in batches
			$this->get_db_connection('target')->insert_batch($destination_tbl, $source_data);
			//Update selected data using table matching indices
			$matching_indices = $this->cfg[$destination_tbl.'_indices'];
			$update_data = array();
			foreach($source_data as $index => $data){
				foreach ($matching_indices as $key => $value) {
					$update_data[$index][$value] = $data[$key];
				}
				$update_data[$index][$this->cfg['migration_flag_column']] = TRUE;
			}
			$this->get_db_connection('source')->update_batch($source_tbl, $update_data, array_values($matching_indices)[0]);
			//Set offset
			if(($total-$offset) < $this->cfg['migration_limit']){
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
				$this->run_sql_file(@$this->cfg[$destination_tbl.'_update'], $this->get_db_connection('target'), $update_params);
			}	
		}else{
			$offset = $total;
		}
		echo json_encode(array('offset' => $offset));
	}
}