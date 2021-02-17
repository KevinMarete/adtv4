<?php

namespace Modules\Setup\Controllers;

use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;



use DB;
//use Illuminate\Support\Facades\DB;







class Setup extends \CodeIgniter\Controller {

    public function index($message = '') {
        $data['active_menu'] = 7;
        $data['content_view'] = "\Modules\Setup\Views\\setup_view";
        $data['title'] = "Dashboard | System Setup";
        $db = \Config\Database::connect();
        $sql = "SELECT * from users";
        $result = $db->query($sql);
        $data['usercount'] = count($result->getResultArray());
        $this->template($data);
    }

    public function getFacilities() {
        $db = \Config\Database::connect();
        $q = $_GET['q'];
        //get all facilities
        $sql = "SELECT facilitycode as facility_code,name as facility_name 
                FROM facilities
                WHERE name IS NOT NULL 
                AND name !=''
                AND name LIKE '%$q%'
                ORDER BY name ASC";
        $query = $db->query($sql);
        $results = $query->getResultArray();

        if ($results) {
            foreach ($results as $result) {
                $answer[] = array("id" => $result['facility_code'], "text" => $result['facility_name']);
            }
        } else {
            $answer[] = array("id" => "0", "text" => "No Results Found..");
        }
        echo json_encode($answer);
    }

    public function initialize() {
        // @todo find old code from users table
        //Get mflcode
        $mflcode = $_POST['facility'];
        //Get database config
        $db = \Config\Database::connect();
        $session = session();

        $sql = "SELECT Facility_Code from users limit 1";
        $result = $db->query($sql);
        $old_facility_code = $result->getResultArray()[0]['Facility_Code'];
  
           // $presence= DB::table('users')->where('Username','user') ->first();
           //corrected for facility initializing

           $user_sql="SELECT Username from users where Username='user'";
           $presence=$db->query($user_sql)->getResult();
           
           //look if the facilty exist and excute the queries

       if( $old_facility_code!=$mflcode)
       {
            
        if ($presence > 0) {
            //Update all users to mflcode
            $db->query("REPLACE INTO users (id, Name, Username, Password, Access_Level, Facility_Code, Created_By, Time_Created, Phone_Number, Email_Address, Active, Signature, map, ccc_store_sp) VALUES(2,	'Demo User',	'user',	'1a7a4c2f236042c4f8cfd79ec9ff2094','3','$mflcode','1',	'2021-17-02',' 070000001','webadt@chai.com','1','1',0,2)");

			$db->query("UPDATE users SET Facility_Code = '$mflcode'  WHERE Facility_Code = '$old_facility_code'");
			$db->query("UPDATE drug_stock_movement SET facility = '$mflcode'  WHERE facility = '$old_facility_code'");
			$db->query("UPDATE drug_stock_movement SET source = '$mflcode'  WHERE source = '$old_facility_code'");
			$db->query("UPDATE drug_stock_movement SET destination = '$mflcode'  WHERE destination = '$old_facility_code'");
			$db->query("UPDATE patient SET facility_code = '$mflcode'  WHERE facility_code = '$old_facility_code'");
			$db->query("UPDATE patient_visit SET facility = '$mflcode'  WHERE facility = '$old_facility_code'");
			$db->query("UPDATE patient_appointment SET facility = '$mflcode'  WHERE facility = '$old_facility_code'");
			$db->query("UPDATE clinic_appointment SET facility = '$mflcode'  WHERE facility = '$old_facility_code'");
			$db->query("UPDATE drug_cons_balance set facility = '$mflcode' where facility = '$old_facility_code'");
			$db->query("UPDATE drug_stock_balance set facility_code = '$mflcode' where facility_code = '$old_facility_code'");

           
            //Redirect with message
            $message = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Success!</strong> Facility initialized to MFLCODE: ' . $mflcode . ' <br /> User Login user:user</div>';
           $session->setFlashdata('init_msg', $message);
           return redirect()->to(base_url('/setup'));
        } else {
           $message = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Facility already Initialized</strong></div>';
           $session->setFlashdata('init_msg', $message);
           return redirect()->to(base_url('/setup'));
        }
    }

//if the facility already exist alert

    else{
        $message = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Facility already Initialized</strong></div>';
        $session->setFlashdata('init_msg', $message);
        return redirect()->to(base_url('/setup'));
      
    }
    }

    public function template($data) {
        $data['show_menu'] = 0;
        $data['show_sidemenu'] = 0;
        $template = new Template();
        $template->index($data);
    }

}
