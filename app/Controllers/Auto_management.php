<?php
class Auto_management extends MY_Controller {
	var $viral_load_url="";
	function __construct() {
		parent::__construct();
		ini_set("max_execution_time", "100000");
		ini_set("memory_limit", '2048M');
		ini_set("allow_url_fopen", '1');

		$this -> viralload_url = "https://viralload.nascop.org/";
	}

	public function index($manual = FALSE){
		$message ="";
		$retry_seconds = 3600 ; //1 hour (60*60)
		$today =  date('YmdHis');
		//get last update time of log file for auto_update
		$log=Migration_Log::getLog('auto_update');
		$last_update = $log['last_index'];
		$status = (int)$log['count'];

		$last_update = strtotime($last_update);
		$time_diff = time()-$last_update;
		$retry = ($time_diff > $retry_seconds) ? TRUE : FALSE ;

		/*
		 * Conditions:
		 *------------------------------------------
		 * TodayDate is not equal to LastUpdateDate
		 * Retry(Difference in seconds btwn the CurrentTime and LastUpdateTime should be greater than RetrySeconds) 
		 * Status as false in the database
		 * Manual is TRUE
		 * Testing string: 
		 	echo 'TodayDate:='.date('Y-m-d').',LastUpdateDate:='.date('Y-m-d', $last_update).',Retry:='.$retry.',Status:='.$status.',TimeDiff:='.$time_diff.',RetrySeconds:='.$retry_seconds;die();
		*/

		 	if ((date('Y-m-d') != date('Y-m-d', $last_update)) || ($retry && $status == 0) || $manual == TRUE) {
			// Update today's date before starting process
		 		$sql="UPDATE migration_log SET last_index='$today', count = 0 WHERE source='auto_update'";
		 		$this -> db -> query($sql);

			//function to update destination column to 1 in drug_stock_movement table for issued transactions that have name 'pharm'
		 		$message .= $this->updateIssuedTo();
			//function to update source_destination column in drug_stock_movement table where it is zero
		 		$message .= $this->updateSourceDestination();
			//function to update ccc_store_sp column in drug_stock_movement table for pharmacy transactions
		 		$message .= $this->updateCCC_Store();
			//function to set negative batches to zero
		 		$message .= $this->setBatchBalance();
			//function to update patients without current_regimen with last regimen dispensed
		 		$message .= $this->update_current_regimen(); 
			//function to update patient data such as active to lost_to_follow_up	
		 		$message .= $this->updatePatientData();
			//function to update data bugs by applying query fixes
		 		$message .= $this->updateFixes();
			//function to add new facilities list
		 		$message .= $this->updateFacilties();
			//function to update patient visit dose from id to name
		 		$message .= $this->update_dose_name();
			//function to do_procs
		 		$message .= $this->do_procs();			
			//function to run_migrations
		 		$message .= $this->run_migrations();			
			//function to auto_backup
		 		$message .= $this->auto_backup();			
			//function to get viral load data
		 		$message .= $this->updateViralLoad();

	        //finally update the log file for auto_update 
		 		if ($this -> session -> userdata("curl_error") == '') {
		 			$sql="UPDATE migration_log SET  count = 1 WHERE source='auto_update'";
		 			$this -> db -> query($sql);
		 			$this -> session -> set_userdata("curl_error", "");
		 		} 
		 	}
		 	if($manual==TRUE){
		 		$message="<div class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>&times;</button>".$message."</div>";
		 	}
		 	echo $message;
		 }

		 public function updateIssuedTo(){
		 	$sql="UPDATE drug_stock_movement
		 	SET destination='1'
		 	WHERE destination LIKE '%pharm%'";
		 	$this->db->query($sql);
		 	$count=$this->db->affected_rows();
		 	$message="(".$count.") issued to transactions updated!<br/>";
		 	$message="";
		 	if($count>0){
		 		$message="(".$count.") issued to transactions updated!<br/>";
		 	}
		 	return $message;
		 }

		 public function updateSourceDestination(){
		 	$values=array(
		 		'received from'=>'source',
		 		'returns from'=>'destination',
		 		'issued to'=>'destination',
		 		'returns to'=>'source'
		 		);
		 	$message="";
		 	foreach($values as $transaction=>$column){
		 		$sql="UPDATE drug_stock_movement dsm
		 		LEFT JOIN transaction_type t ON t.id=dsm.transaction_type
		 		SET dsm.source_destination=IF(dsm.$column=dsm.facility,'1',dsm.$column)
		 		WHERE t.name LIKE '%$transaction%'
		 		AND(dsm.source_destination IS NULL OR dsm.source_destination='' OR dsm.source_destination='0')";
		 		$this->db->query($sql);
		 		$count=$this->db->affected_rows();
		 		$message.=$count." ".$transaction." transactions missing source_destination(".$column.") have been updated!<br/>";
		 	}
		 	if($count<=0){
		 		$message="";
		 	}
		 	return $message;
		 }

		 public function updateCCC_Store(){
		 	$facility_code=$this->session->userdata("facility");
		 	$sql="UPDATE drug_stock_movement dsm
		 	SET ccc_store_sp='1'
		 	WHERE dsm.source !=dsm.destination
		 	AND ccc_store_sp='2' 
		 	AND (dsm.source='$facility_code' OR dsm.destination='$facility_code')";
		 	$this->db->query($sql);
		 	$count=$this->db->affected_rows();
		 	$message="(".$count.") transactions changed from main pharmacy to main store!<br/>";
		 	if($count<=0){
		 		$message="";
		 	}
		 	return $message;
		 }

		 public function setBatchBalance(){
		 	$facility_code=$this->session->userdata("facility");
		 	$sql="UPDATE drug_stock_balance dsb
		 	SET dsb.balance=0
		 	WHERE dsb.balance<0 
		 	AND dsb.facility_code='$facility_code'";
		 	$this->db->query($sql);
		 	$count=$this->db->affected_rows();
		 	$message="(".$count.") batches with negative balance have been updated!<br/>";
		 	if($count<=0){
		 		$message="";
		 	}
		 	return $message;
		 }

		 public function update_current_regimen() {
		 	$count=1;
		//Get all patients without current regimen and who are not active
		 	$sql_get_current_regimen = "SELECT p.id,p.patient_number_ccc, p.current_regimen ,ps.name
		 	FROM patient p 
		 	INNER JOIN patient_status ps ON ps.id = p.current_status
		 	WHERE current_regimen = '' 
		 	AND ps.name != 'active'";
		 	$query = $this -> db -> query($sql_get_current_regimen);
		 	$result_array = $query -> result_array();
		 	if($result_array){
		 		foreach ($result_array as $value) {
		 			$patient_id = $value['id'];
		 			$patient_ccc = $value['patient_number_ccc'];
				//Get last regimen
		 			$sql_last_regimen = "SELECT pv.last_regimen FROM patient_visit pv WHERE pv.patient_id= ? ORDER BY id DESC LIMIT 1";
		 			$query = $this -> db -> query($sql_last_regimen, $patient_ccc);
		 			$res = $query -> result_array();
		 			if (count($res) > 0) {
		 				$last_regimen_id = $res[0]['last_regimen'];
		 				$sql = "UPDATE patient p SET p.current_regimen = ?  WHERE p.id = ?";
		 				$query = $this -> db -> query($sql, array($last_regimen_id, $patient_id));
		 				$count++;
		 			}
		 		}   
		 	}     
		 	$message="(".$count.") patients without current_regimen have been updated with last dispensed regimen!<br/>";
		 	if($count<=0){
		 		$message="";
		 	}
		 	return $message;
		 }

		 public function updatePatientData() {
		$days_to_lost_followup=$this -> session -> userdata('lost_to_follow_up');//Default lost to follow up
		$days_to_pep_end = 30;
		$days_to_prep_inactive = 30; //They should not be late for their appointments
		$days_in_year = date("z", mktime(0, 0, 0, 12, 31, date('Y'))) + 1;
		$adult_age = 15;
		$active = 'active';
		$lost = 'lost';
		$pep = 'pep';
		$prep = 'prep';
		$pmtct = 'pmtct';
		$two_year_days = $days_in_year * 2;
		$adult_days = $days_in_year * $adult_age;
		$message = "";
		$state = array();
		//Get Patient Status id's
		$status_array = array($active, $lost, $pep, $pmtct);
		foreach ($status_array as $status) {
			$s = "SELECT id,name FROM patient_status ps WHERE ps.name LIKE '%$status%'";
			$q = $this -> db -> query($s);
			$rs = $q -> result_array();
			if($rs){
				$state[$status] = $rs[0]['id'];
			} else{
                $state[$status]='NAN'; //If non existant
            }	
        }
        if(!empty($state)){
        	/*Change Last Appointment to Next Appointment*/
        	$sql['Change Last Appointment to Next Appointment'] = "(SELECT patient_number_ccc,nextappointment,temp.appointment,temp.patient
        	FROM patient p
        	LEFT JOIN 
        	(SELECT MAX(pa.appointment)as appointment,pa.patient
        	FROM patient_appointment pa
        	GROUP BY pa.patient) as temp ON p.patient_number_ccc =temp.patient
        	WHERE p.nextappointment !=temp.patient
        	AND DATEDIFF(temp.appointment,p.nextappointment)>0
        	GROUP BY p.patient_number_ccc) as p1
        	SET p.nextappointment=p1.appointment";
        	/*Change Active to Lost_to_follow_up*/
        	if(isset($state[$lost])){
        		$sql['Change Active to Lost_to_follow_up'] = "(SELECT patient_number_ccc,nextappointment,DATEDIFF(CURDATE(),nextappointment) as days
        		FROM patient p
        		LEFT JOIN patient_status ps ON ps.id=p.current_status
        		LEFT JOIN regimen_service_type rst ON rst.id = p.service
        		WHERE ps.Name LIKE '%$active%'
        		AND (DATEDIFF(CURDATE(),nextappointment )) >= $days_to_lost_followup
        		AND p.status_change_date != CURDATE()
        		AND rst.name NOT LIKE '%$pep%'
        		AND rst.name NOT LIKE '%$prep%') as p1
        		SET p.current_status = '$state[$lost]', p.status_change_date = CURDATE()";
        	}

        	/*Change Lost_to_follow_up to Active */
        	if(isset($state[$active])){
        		$sql['Change Lost_to_follow_up to Active'] = "(SELECT patient_number_ccc,nextappointment,DATEDIFF(CURDATE(),nextappointment) as days
        		FROM patient p
        		LEFT JOIN patient_status ps ON ps.id=p.current_status
        		LEFT JOIN regimen_service_type rst ON rst.id = p.service
        		WHERE ps.Name LIKE '%$lost%'
        		AND (DATEDIFF(CURDATE(),nextappointment )) < $days_to_lost_followup
        		AND rst.name NOT LIKE '%$pep%'
        		AND rst.name NOT LIKE '%$prep%') as p1
        		SET p.current_status = '$state[$active]', p.status_change_date = CURDATE()";
        	}

        	/*Change Active to PEP End*/
        	if(isset($state[$pep])){
        		$sql['Change Active to PEP End'] = "(SELECT patient_number_ccc,rst.name as Service,ps.Name as Status,DATEDIFF(CURDATE(),date_enrolled) as days_enrolled
        		FROM patient p
        		LEFT JOIN regimen_service_type rst ON rst.id=p.service
        		LEFT JOIN patient_status ps ON ps.id=p.current_status
        		WHERE (DATEDIFF(CURDATE(),date_enrolled))>=$days_to_pep_end 
        		AND rst.name LIKE '%$pep%' 
        		AND ps.Name NOT LIKE '%$pep%') as p1
        		SET p.current_status = '$state[$pep]', p.status_change_date = CURDATE()";
        	}

        	/*Change PEP End to Active*/
        	if(isset($state[$active])){
        		$sql['Change PEP End to Active'] = "(SELECT patient_number_ccc,rst.name as Service,ps.Name as Status,DATEDIFF(CURDATE(),date_enrolled) as days_enrolled
        		FROM patient p
        		LEFT JOIN regimen_service_type rst ON rst.id=p.service
        		LEFT JOIN patient_status ps ON ps.id=p.current_status
        		WHERE (DATEDIFF(CURDATE(),date_enrolled))<$days_to_pep_end 
        		AND rst.name LIKE '%$pep%' 
        		AND ps.Name NOT LIKE '%$active%') as p1
        		SET p.current_status = '$state[$active]', p.status_change_date = CURDATE()";
        	}

        	/*Change Active to PMTCT End(children)*/
        	if(isset($state[$pmtct])){
        		$sql['Change Active to PMTCT End(children)'] = "(SELECT patient_number_ccc,rst.name AS Service,ps.Name AS Status,DATEDIFF(CURDATE(),dob) AS days
        		FROM patient p
        		LEFT JOIN regimen_service_type rst ON rst.id = p.service
        		LEFT JOIN patient_status ps ON ps.id = p.current_status
        		WHERE (DATEDIFF(CURDATE(),dob )) >=$two_year_days
        		AND (DATEDIFF(CURDATE(),dob)) <$adult_days
        		AND rst.name LIKE  '%$pmtct%'
        		AND ps.Name NOT LIKE  '%$pmtct%') as p1
        		SET p.current_status = '$state[$pmtct]', p.status_change_date = CURDATE()";
        	}

        	/*Change PMTCT End to Active(Adults)*/
        	if(isset($state[$active])){
        		$sql['Change PMTCT End to Active(Adults)'] = "(SELECT patient_number_ccc,rst.name AS Service,ps.Name AS Status,DATEDIFF(CURDATE(),dob) AS days
        		FROM patient p
        		LEFT JOIN regimen_service_type rst ON rst.id = p.service
        		LEFT JOIN patient_status ps ON ps.id = p.current_status 
        		WHERE (DATEDIFF(CURDATE(),dob)) >=$two_year_days 
        		AND (DATEDIFF(CURDATE(),dob)) >=$adult_days 
        		AND rst.name LIKE '%$pmtct%'
        		AND ps.Name LIKE '%$pmtct%') as p1
        		SET p.current_status = '$state[$active]', p.status_change_date = CURDATE()";
        	}

        	/*Change PREP Active to Lost To Follow Up*/
        	$sql['Change PREP Active to Lost to FollowUp'] = "(SELECT patient_number_ccc,nextappointment,DATEDIFF(CURDATE(),nextappointment) as days
        	FROM patient p
        	LEFT JOIN patient_status ps ON ps.id=p.current_status
        	LEFT JOIN regimen_service_type rst ON rst.id = p.service
        	WHERE ps.Name LIKE '%$active%'
        	AND rst.name LIKE '%$prep%'
        	AND (DATEDIFF(CURDATE(), nextappointment)) >= $days_to_prep_inactive) as p1
        	SET p.current_status = '$state[$lost]', p.status_change_date = CURDATE()";

        	/*Change PREP Lost To Follow Up to Active*/
        	$sql['Change PREP Lost to FollowUp to Active'] = "(SELECT patient_number_ccc,nextappointment,DATEDIFF(CURDATE(),nextappointment) as days
        	FROM patient p
        	LEFT JOIN patient_status ps ON ps.id=p.current_status
        	LEFT JOIN regimen_service_type rst ON rst.id = p.service
        	WHERE ps.Name LIKE '%$lost%'
        	AND rst.name LIKE '%$prep%'
        	AND (DATEDIFF(CURDATE(), nextappointment)) < $days_to_prep_inactive) as p1
        	SET p.current_status = '$state[$active]', p.status_change_date = CURDATE()";

        	foreach ($sql as $i => $q) {
        		$stmt1 = "UPDATE patient p,";
        		$stmt2 = " WHERE p.patient_number_ccc=p1.patient_number_ccc;";
        		$stmt1 .= $q;
        		$stmt1 .= $stmt2;

        		$q = $this -> db -> query($stmt1);
        		if ($this -> db -> affected_rows() > 0) {
        			$message .= $i . "(<b>" . $this -> db -> affected_rows() . "</b>) rows affected<br/>";
        		}
        	}
        }
        return $message;
    }

    public function updateFixes(){
		$days_to_lost_followup = $this -> session -> userdata('lost_to_follow_up');//Default lost to follow up
		//Rename the prophylaxis cotrimoxazole
		$fixes[]="UPDATE drug_prophylaxis
		SET name='cotrimoxazole'
		WHERE name='cotrimozazole'";
        //Remove start_regimen_date in OI only patients records
		$fixes[]="UPDATE patient p
		LEFT JOIN regimen_service_type rst ON p.service=rst.id
		SET p.start_regimen_date='' 
		WHERE rst.name LIKE '%oi%'
		AND p.start_regimen_date IS NOT NULL";
        //Update status_change_date for lost_to_follow_up patients @180
		$fixes[]="UPDATE patient p,(SELECT p.id,CASE WHEN p.nextappointment != '' THEN INTERVAL $days_to_lost_followup DAY + p.nextappointment ELSE CASE WHEN p.start_regimen_date != '' THEN INTERVAL $days_to_lost_followup DAY + p.start_regimen_date ELSE INTERVAL $days_to_lost_followup DAY + p.date_enrolled END END AS choosen_date FROM patient p LEFT JOIN patient_status ps ON ps.id = p.current_status WHERE ps.Name LIKE '%lost%' AND p.status_change_date = '') as test SET p.status_change_date=test.choosen_date WHERE p.id=test.id";
	    //Update patients without service lines ie Pep end status should have pep as a service line
		$fixes[]="UPDATE patient p
		LEFT JOIN patient_status ps ON ps.id=p.current_status,
		(SELECT id 
		FROM regimen_service_type
		WHERE name LIKE '%pep%') as rs
		SET p.service=rs.id
		WHERE ps.name LIKE '%pep end%'
		AND p.service=''";
		//Updating patients without service lines ie PMTCT status should have PMTCT as a service line
		$fixes[]= "UPDATE patient p
		LEFT JOIN patient_status ps ON ps.id=p.current_status,
		(SELECT id 
		FROM regimen_service_type
		WHERE name LIKE '%pmtct%') as rs
		SET p.service=rs.id
		WHERE ps.name LIKE '%pmtct end%'
		AND p.service=''";
		//Remove ??? in drug instructions
		$fixes[]="UPDATE drug_instructions 
		SET name=REPLACE(name, '?', '.')
		WHERE name LIKE '%?%'";
		$facility_code=$this->session->userdata("facility");
		//Auto Update Supported and supplied columns for satellite facilities
		$fixes[] = "UPDATE facilities f, 
		(SELECT facilitycode,supported_by,supplied_by
		FROM facilities 
		WHERE facilitycode='$facility_code') as temp
		SET f.supported_by=temp.supported_by,
		f.supplied_by=temp.supplied_by
		WHERE f.parent='$facility_code'
		AND f.parent !=f.facilitycode";
	    //Auto Update to trim other_drugs,adr and other_illnesses
		$fixes[]="UPDATE patient p
		SET p.other_drugs = TRIM(Replace(Replace(Replace(p.other_drugs,'\t',''),'\n',''),'\r','')),
		p.other_illnesses = TRIM(Replace(Replace(Replace(p.other_illnesses,'\t',''),'\n',''),'\r','')),
		p.adr = TRIM(Replace(Replace(Replace(p.adr,'\t',''),'\n',''),'\r',''))";
		//Update status_change_date if blank and start_regimen_date exist
		$fixes[] = "UPDATE patient p
					SET p.status_change_date = p.start_regimen_date
					WHERE p.status_change_date = ''";
		//Update status to "lost_to_followup" if there is no appointment_date and start_regimen_date is over 180 days
		$fixes[] = "UPDATE patient p
					LEFT JOIN patient_status ps ON ps.id = p.current_status,
					(SELECT id FROM patient_status WHERE name LIKE '%lost%' LIMIT 1) ps1
					SET p.current_status = ps1.id
					WHERE p.nextappointment = '' AND ps.Name LIKE '%active%' AND (p.start_regimen_date = '' OR  DATEDIFF(CURDATE(), p.start_regimen_date) >= 180)";
		//Execute fixes
		$total=0;
		foreach ($fixes as $fix) {
			//will exempt all database errors
			$db_debug = $this->db->db_debug;
			$this->db->db_debug = false;
			$this -> db -> query($fix);
			$this->db->db_debug = $db_debug;
			//count rows affected by fixes
			if ($this -> db -> affected_rows() > 0) {
				$total += $this -> db -> affected_rows();
			}
		}

		$message="(".$total.") rows affected by fixes applied!<br/>";
		if($total>0){
			$message="";
		}
		return $message;
	}

	public function updateFacilties(){
		$total=Facilities::getTotalNumber();
		$message="";
		if($total < 9800){
			$this -> load -> library('PHPExcel');
			$inputFileType = 'Excel5';
			$inputFileName = $_SERVER['DOCUMENT_ROOT'] . '/ADT/assets/templates/sites/facility_list.xls';
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader -> load($inputFileName);
			$highestColumm = $objPHPExcel -> setActiveSheetIndex(0) -> getHighestColumn();
			$highestRow = $objPHPExcel -> setActiveSheetIndex(0) -> getHighestRow();
			$arr = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
			$facilities=array();
			$facility_code=$this->session->userdata("facility");
			$lists=Facilities::getParentandSatellites($facility_code);
			for ($row = 2; $row < $highestRow; $row++) {
				$facility_id=$arr[$row]['A'];
				$facility_name=$arr[$row]['B'];
				$facility_type_name=str_replace(array("'"), "", $arr[$row]['G']);
				$facility_type_id=Facility_Types::getTypeID($facility_type_name);
				$district_name=str_replace(array("'"), "", $arr[$row]['E']);
				$district_id=District::getID($district_name);
				$county_name=str_replace(array("'"), "", $arr[$row]['D']);
				$county_id=Counties::getID($county_name);
				$email=$arr[$row]['T'];
				$phone=$arr[$row]['R'];
				$adult_age=15;
				$weekday_max='';
				$weekend_max='';
				$supported_by='';
				$service_art=0;
				if(strtolower($arr[$row]['AD'])=="y"){
					$service_art=1;
				}
				$service_pmtct=0;
				if(strtolower($arr[$row]['AR'])=="y"){
					$service_pmtct=1;
				}
				$service_pep=0;
				$supplied_by='';
				$parent='';
				$map=0;
		        //if is this facility or satellite of this facility
				if(in_array($facility_id,$lists)){
					$details=Facilities::getCurrentFacility($facility_id);
					if($details){
						$parent=$details[0]['parent'];
						$supported_by=$details[0]['supported_by'];
						$supplied_by=$details[0]['supplied_by'];
						$service_pep=$details[0]['service_pep'];
						$weekday_max=$details[0]['weekday_max'];
						$weekend_max=$details[0]['weekend_max'];
						$map=$details[0]['map'];
					}
				}
				//append to facilities data array
				$facilities[$row]=array(
					'facilitycode'=>$facility_id,
					'name'=>$facility_name,
					'facilitytype'=>$facility_type_id,
					'district'=>$district_id,
					'county'=>$county_id,
					'email'=>$email,
					'phone'=>$phone,
					'adult_age'=>$adult_age,
					'weekday_max'=>$weekday_max,
					'weekend_max'=>$weekend_max,
					'supported_by'=>$supported_by,
					'service_art'=>$service_art,
					'service_pmtct'=>$service_pmtct,
					'service_pep'=>$service_pep,
					'supplied_by'=>$supplied_by,
					'parent'=>$parent,
					'map'=>$map);
			}
			$sql="TRUNCATE facilities";
			$this->db->query($sql);
			$this->db->insert_batch('facilities',$facilities);
			$counter=count($facilities);
			$message=$counter . " facilities have been added!<br/>";
		}
		return $message;
	}

	public function updateViralLoad(){
		$facility_code = $this -> session -> userdata("facility");
		$url = $this -> viralload_url . "vlapi.php?mfl=" . $facility_code;
		$patient_tests=array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
		$json_data = curl_exec($ch); 
		if (empty($json_data)) {
			$message = "cURL Error: " . curl_error($ch)."<br/>";
		} else {
			$data = json_decode($json_data, TRUE); 
			$lab_data=$data['posts'];
			$message="Viral Load Download Failed!<br/>";
			if(!empty($lab_data)){
				foreach($lab_data as $lab){
					foreach($lab as $tests){
						$id = intval($tests['ID']);
						$ccc_no = trim($tests['Patient']);
						$result = $tests['Result'];
						$date_tested = $tests['DateTested'];
						$justification = $tests['Justification'];
						$datecollected = $tests['datecollected'];
						//An array to store patient viral Load data
						$sql = "CALL proc_check_viralload(?, ?, ?, ?, ?,?)";
						$parameters = array($id, $ccc_no, $date_tested,$datecollected, $result, $justification);
						$this->db->query($sql, $parameters);
					}
				}
				$message="Viral Load Download Success!<br/>";
			}
		}

		return $message;
	}

	public function update_dose_name(){
   		//function to update dose on patient visit from id to dose name 
		$message = '';
		$sql="SELECT id, Name FROM dose";
		$query = $this -> db -> query($sql);
		$doses = $query -> result_array();
		foreach ($doses as $dose) {
			$dose_id= $dose['id'];
			$dose_name=$dose['Name'];
			$sql1="UPDATE patient_visit set dose='$dose_name' where dose='$dose_id' ";
			$query1 = $this -> db -> query($sql1);
			$message = 'Updated Dose Records in Visits ('.$this-> db -> affected_rows().')';
		}

		return $message;
	}

	public function run_migrations(){
		$count = 0;
		$delimeter = "//";
		$queries_dir  = 'assets/migrations';
		$accepted_files = array('sql');
		$duplicate_error_codes = array(1022, 1060, 1061, 1062, 1064);
		if (is_dir($queries_dir)) {
			$files = scandir($queries_dir);
			
			//Get all executed migrations
			$migrations = array();
			$get_migrations_sql = "SELECT migration from migrations";
			$results = $this->db->query($get_migrations_sql)->result_array();
			if(!empty($results)){
				foreach ($results as $result) {
					$migrations[] = $result['migration'];
				}
			}

			//Get migration files
			foreach ($files as $file_name) {
				$error_status = 0;
				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
				if ($file_name != '.' && $file_name != '..' && in_array($ext, $accepted_files) && !in_array($file_name, $migrations)) {
					//Get query statements
					$query_file = $queries_dir . '/' . $file_name;
					$query_stmt = file_get_contents($query_file);
					//Execute query statements
					$statements = explode($delimeter, $query_stmt);
					foreach($statements as $statement){
						$statement = trim($statement);
						if ($statement){
							if (!$this->db->simple_query($statement))
							{	
								$error_code = $this->db->_error_number();
								$error_status = 1;
								$error = $file_name.'==>'.$this->db->_error_message().'<br/>';
							}
						}
					}
					$count++;
					//If no error or is duplicate error_code then insert migration into migrations table
					if($error_status == 0 || in_array($error_code, $duplicate_error_codes)){
						$data = array(
							'migration' => $file_name
							);
						$this->db->insert('migrations', $data);
					}
				}
			}
		}
		return "(".$count.") rows affected!<br/>";
	}

	public function auto_backup(){
		$returnable = "AutoBackup: ";
		$autobackup=$this->session->userdata("autobackup");

		if($autobackup ==1) {
			// check if auto backup is set
			$backup_result = file_get_contents(base_url().'tools/backup/run_backup');
			if (strpos($backup_result, 'Error') !== false){
				$returnable .='Backup:Failed, Upload:Failed';

			}else{

				$upload_result = file_get_contents(base_url().'tools/backup/upload_backup/'.str_replace(" ", "", explode('-', $backup_result)[1]));
				$returnable .='Backup:Success, ';
				$returnable .=$upload_result.'<br />';
			}
			return $returnable;
		}
		else {
			return $returnable.'disabled<br/>';
		}
	}

	public function updateSms() {
		$alert="";
		$facility_name=$this -> session -> userdata('facility_name');
		$facility_phone=$this->session->userdata("facility_phone");
		$facility_sms_consent=$this->session->userdata("facility_sms_consent");
		if($facility_sms_consent==TRUE){
			/* Find out if today is on a weekend */
			$weekDay = date('w');
			if ($weekDay == 6) {
				$tommorrow = date('Y-m-d', strtotime('+2 day'));
			} else {
				$tommorrow = date('Y-m-d', strtotime('+1 day'));
			}
			$nextweek=date('Y-m-d', strtotime('+1 week'));
			$phone_minlength = '8';
			$phone = "";
			$phone_list = "";
			$messages_list="";
			$first_part = "";
			$kenyacode = "254";
			$arrDelimiters = array("/", ",", "+");
			/*Get All Patient Who Consented Yes That have an appointment Tommorow */
			$sql = "SELECT p.phone,p.patient_number_ccc,p.nextappointment,temp.patient,temp.appointment,temp.machine_code as status,temp.id
			FROM patient p
			LEFT JOIN 
			(SELECT pa.id,pa.patient, pa.appointment, pa.machine_code
			FROM patient_appointment pa
			WHERE pa.appointment IN ('$tommorrow','$nextweek')
			GROUP BY pa.patient) as temp ON temp.patient=p.patient_number_ccc
			WHERE p.sms_consent =  '1'
			AND p.nextappointment =temp.appointment
			AND char_length(p.phone)>$phone_minlength
			AND temp.machine_code !='s'
			GROUP BY p.patient_number_ccc";
			$query = $this -> db -> query($sql);
			$results = $query -> result_array();
			$phone_data=array();
			if ($results) {
				foreach ($results as $result) {
					$phone = $result['phone'];
					$appointment = $result['appointment'];
					$newphone = substr($phone, -$phone_minlength);
					$first_part = str_replace($newphone, "", $phone);
					$message = "You have an Appointment on " . date('l dS-M-Y', strtotime($appointment)) . " at $facility_name Contact Phone: $facility_phone";
					if (strlen($first_part) < 7) {
						if ($first_part === '07') {
							$phone = "+" . $kenyacode . substr($phone, 1);
							$phone_list .= $phone;
							$messages_list .= "+" .$message;
						} else if ($first_part == '7') {
							$phone = "0" . $phone;
							$phone = "+" . $kenyacode . substr($phone, 1);
							$phone_list .= $phone;
							$messages_list .= "+" .$message;
						} else if ($first_part == '+' . $kenyacode . '07') {
							$phone = str_replace($kenyacode . '07', $kenyacode . '7', $phone);
							$phone_list .= $phone;
							$messages_list .= "+" .$message;
						}
					} else {
						/*If Phone Does not meet requirements*/
						$phone = str_replace($arrDelimiters, "-|-", $phone);
						$phones = explode("-|-", $phone);
						foreach ($phones as $phone) {
							$newphone = substr($phone, -$phone_minlength);
							$first_part = str_replace($newphone, "", $phone);
							if (strlen($first_part) < 7) {
								if ($first_part === '07') {
									$phone = "+" . $kenyacode . substr($phone, 1);
									$phone_list .= $phone;
									$messages_list .= "+" .$message;
									break;
								} else if ($first_part == '7') {
									$phone = "0" . $phone;
									$phone = "+" . $kenyacode . substr($phone, 1);
									$phone_list .= $phone;
									$messages_list .= "+" .$message;
									break;
								} else if ($first_part == '+' . $kenyacode . '07') {
									$phone = str_replace($kenyacode . '07', $kenyacode . '7', $phone);
									$phone_list .= $phone;
									$messages_list .= "+" .$message;
									break;
								}
							}
						}
					}
					$stmt = "update patient_appointment set machine_code='s' where id='" . $result['id'] . "'";
					$q = $this -> db -> query($stmt);
				}
				$phone_list = substr($phone_list, 1);
				$messages_list = substr($messages_list, 1);
				$phone_list = explode("+", $phone_list);
				$messages_list = explode("+", $messages_list);

				foreach ($phone_list as $counter=>$contact) {
					$message = urlencode($messages_list[$counter]);
					//file("http://41.57.109.242:13000/cgi-bin/sendsms?username=clinton&password=ch41sms&to=$contact&text=$message");
				}
				$alert = "Patients notified (<b>" . sizeof($phone_list) . "</b>)";
			}
		}
		return $alert;
	}

	public function get_viral_load($patient_no){
		//Validate patient_no when use of / to separate mflcode and ccc_no
		$mflcode = $this->uri->segment(3);
		$ccc_no = $this->uri->segment(4);
		if($ccc_no){
			$patient_no = $mflcode.'/'.$ccc_no;
		}
		$this->db->select('*');
		$this->db->where('patient_ccc_number', $patient_no);
		$this->db->from('patient_viral_load');
		$this->db->order_by('test_date','desc');
		$query = $this->db->get();
		$result = $query->result_array();
		echo json_encode($result);
	}

	public function do_procs($file_path = null){
		$file_path = (isset($file_path)) ? $file_path : '' ;
		$file_path  = './assets/migrations/procs/';
		$this->setup_migration_table();


		$procs = array();
		$get_migrations_sql = "SELECT migration from migrations";
		$results = $this->db->query($get_migrations_sql)->result_array();
		if(!empty($results)){
			foreach ($results as $result) {
				$procs[] = $result['migration'];
			}
		}
		$proc_files = scandir($file_path);

		$CI = &get_instance();
		$CI -> load -> database();
		$hostname = explode(':', $CI->db->hostname)[0];
		$port     = (isset($CI->db->port)) ? $CI->db->port : 3306 ;
		$username = $CI->db->username;
		$password = $CI->db->password;
		$database = $CI->db->database;

		$mysql_home = realpath($_SERVER['MYSQL_HOME']) . "\mysql";

		for ($key=2; $key <count($proc_files) ; $key++) { 
			if (!(in_array($proc_files[$key], $procs))){
				$mysql_bin = str_replace("\\", "\\\\", $mysql_home);
				$mysql_con = $mysql_bin . ' -u ' . $username . ' -p' . $password .  ' -P' . $port . ' -h ' . $hostname . ' ' . $database . ' < ' . $file_path.''.$proc_files[$key];

				if(!exec($mysql_con)){
					$data = array(
						'migration' => $proc_files[$key]
						);
					$this->db->insert('migrations', $data);

				}
			}}

		}

		public function setup_migration_table(){
			//Create migrations table
			$migration_tbl_sql = "CREATE TABLE IF NOT EXISTS `migrations` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`migration` varchar(100) NOT NULL,
			`run_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$this->db->query($migration_tbl_sql);
		}

	}
	?>