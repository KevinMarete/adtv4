<?php

namespace Modules\ADT\Controllers;

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Updater;
use App\Libraries\Zip;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\User_right;
use \Modules\ADT\Models\Patient_appointment;
use \Modules\ADT\Models\CCC_store_service_point;
use Illuminate\Database\Capsule\Manager as DB;

class Facilitydashboard_Management extends \App\Controllers\BaseController {

    var $drug_array = [];
    var $drug_count = 0;
    var $counter = 0;
    var $db;
    var $table;

    function __construct() {

        // $this->load->library('PHPExcel');
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
    }

    public function getExpiringDrugs($period = 30, $stock_type = 1) {
        
        $expiryArray = array();
        $stockArray = array();
        $resultArraySize = 0;
        $count = 0;
        $facility_code = session()->get('facility');
        //$drugs_sql = "SELECT s.id AS id,s.drug AS Drug_Id,d.drug AS Drug_Name,d.pack_size AS pack_size, u.name AS Unit, s.batch_number AS Batch,s.expiry_date AS Date_Expired,DATEDIFF(s.expiry_date,CURDATE()) AS Days_Since_Expiry FROM drugcode d LEFT JOIN drug_unit u ON d.unit = u.id LEFT JOIN drug_stock_movement s ON d.id = s.drug LEFT JOIN transaction_type t ON t.id=s.transaction_type WHERE t.effect=1 AND DATEDIFF(s.expiry_date,CURDATE()) <='$period' AND DATEDIFF(s.expiry_date,CURDATE())>=0 AND d.enabled=1 AND s.facility ='" . $facility_code . "' GROUP BY Batch ORDER BY Days_Since_Expiry asc";
        $drugs_sql = "SELECT d.drug as drug_name,d.pack_size,u.name as drug_unit,dsb.batch_number as batch,dsb.balance as stocks_display,dsb.expiry_date,DATEDIFF(dsb.expiry_date,CURDATE()) as expired_days_display FROM drugcode d LEFT JOIN drug_unit u ON d.unit=u.id LEFT JOIN drug_stock_balance dsb ON d.id=dsb.drug_id WHERE DATEDIFF(dsb.expiry_date,CURDATE()) <='$period' AND DATEDIFF(dsb.expiry_date,CURDATE())>=0 AND d.enabled=1 AND dsb.facility_code ='" . $facility_code . "' AND dsb.stock_type='" . $stock_type . "' AND dsb.balance>0 ORDER BY expired_days_display asc";
        $drugs = $this->db->query($drugs_sql);
        $results = $drugs->getResultArray();
        $d = 0;
        $drugs_array = $results;

        $nameArray = array();
        $dataArray = array();
        foreach ($drugs_array as $drug) {
            $nameArray[] = $drug['drug_name'] . '(' . $drug['batch'] . ')';
            $expiryArray[] = (int) $drug['expired_days_display'];
            $stockArray[] = (int) $drug['stocks_display'];
            $resultArraySize++;
        }
        $resultArray = array(array('name' => 'Expiry', 'data' => $expiryArray), array('name' => 'Stock', 'data' => $stockArray));

        $resultArray = json_encode($resultArray);
        $categories = $nameArray;
        $categories = json_encode($categories);
        //Load Data Variables
        $data['resultArraySize'] = $resultArraySize;
        $data['container'] = 'chart_expiry';
        $data['chartType'] = 'bar';
        $data['title'] = 'Chart';
        $data['chartTitle'] = 'Expiring Drugs';
        $data['categories'] = $categories;
        $data['yAxix'] = 'Drugs';
        $data['resultArray'] = $resultArray;
        echo view('\Modules\ADT\Views\\chart_v', $data);
    }

    public function getPatientEnrolled($startdate = "", $enddate = "") {
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate = date('Y-m-d', strtotime($enddate));
        $first_date = $startdate;
        $last_date = $enddate;
        $maleAdult = array();
        $femaleAdult = array();
        $maleChild = array();
        $femaleChild = array();
        $facility_code = session()->get('facility');
        $timestamp = time();
        $edate = date('Y-m-d', $timestamp);
        $dates = array();
        $x = 7;
        $y = 0;
        $resultArraySize = 0;
        $days_in_year = date("z", mktime(0, 0, 0, 12, 31, date('Y'))) + 1;
        $adult_age = 15;
        $patients_array = array();

        //If no parameters are passed, get enrolled patients for the past 7 days
        if ($startdate == "" || $enddate == "") {
            for ($i = 0; $i < $x; $i++) {
                // if (date("D", $timestamp) != "Sun") {
                $sdate = date('Y-m-d', $timestamp);
                //Store the days in an array
                $dates[$y] = $sdate;
                $y++;
                // }
                //If sunday is included, add one more day
                // else {$x = 8;}
                $timestamp += 24 * 3600;
            }
            $start_date = $sdate;
            $end_date = $edate;
        } else {
            $startdate = strtotime($startdate);
            for ($i = 0; $i < $x; $i++) {
                // if (date("D", $startdate) != "Sun") {
                $sdate = date('Y-m-d', $startdate);
                //Store the days in an array

                $dates[$y] = $sdate;
                $y++;
                // }
                //If sunday is included, add one more day
                // else {$x = 8;}
                $startdate += 24 * 3600;
            }
            $start_date = $startdate;
            $end_date = $enddate;
        }

        /* Loop through all dates in range and get summary of patients enrollment i those days */
        foreach ($dates as $date) {

            $stmt = "SELECT p.date_enrolled, g.name AS gender, ROUND(DATEDIFF(CURDATE(),p.dob)/$days_in_year) AS age,COUNT(*) AS total
					FROM patient p
					LEFT JOIN gender g ON p.gender = g.id
					WHERE p.date_enrolled ='$date'
					GROUP BY g.name, ROUND(DATEDIFF(CURDATE(),p.dob)/$days_in_year)>$adult_age";
            $q = $this->db->query($stmt);
            $rs = $q->getResultArray();

            /* Loop through selected days result set */
            $total_male_adult = 0;
            $total_female_adult = 0;
            $total_male_child = 0;
            $total_female_child = 0;

            if ($rs) {
                foreach ($rs as $r) {
                    /* Check if Adult Male */
                    if (strtolower($r['gender']) == "male" && $r['age'] >= $adult_age) {
                        $total_male_adult = $r['total'];
                    }
                    /* Check if Adult Female */
                    if (strtolower($r['gender']) == "female" && $r['age'] >= $adult_age) {
                        $total_female_adult = $r['total'];
                    }
                    /* Check if Child Male */
                    if (strtolower($r['gender']) == "male" && $r['age'] < $adult_age) {
                        $total_male_child = $r['total'];
                    }
                    /* Check if Child Female */
                    if (strtolower($r['gender']) == "female" && $r['age'] < $adult_age) {
                        $total_female_child = $r['total'];
                    }
                }
            }
            /* Place Values into an Array */
            $patients_array[$date] = array("Adult Male" => $total_male_adult, "Adult Female" => $total_female_adult, "Child Male" => $total_male_child, "Child Female" => $total_female_child);
        }

        $resultArraySize = 6;
        $categories = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        foreach ($patients_array as $key => $value) {
            $maleAdult[] = (int) $value['Adult Male'];
            $femaleAdult[] = (int) $value['Adult Female'];
            $maleChild[] = (int) $value['Child Male'];
            $femaleChild[] = (int) $value['Child Female'];
        }
        $resultArray = array(array('name' => 'Male Adult', 'data' => $maleAdult), array('name' => 'Female Adult', 'data' => $femaleAdult), array('name' => 'Male Child', 'data' => $maleChild), array('name' => 'Female Child', 'data' => $femaleChild));
        $resultArray = json_encode($resultArray);
        $categories = json_encode($categories);

        $data['resultArraySize'] = $resultArraySize;
        $data['container'] = "chart_enrollment";
        $data['chartType'] = 'bar';
        $data['chartTitle'] = 'Patients Enrollment';
        $data['yAxix'] = 'Patients';
        $data['categories'] = $categories;
        $data['resultArray'] = $resultArray;
        echo view('\Modules\ADT\Views\\chart_stacked_v', $data);
    }

    public function getExpectedPatients($startdate = "", $enddate = "") {
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate = date('Y-m-d', strtotime($enddate));
        $first_date = $startdate;
        $last_date = $enddate;
        $facility_code = session()->get('facility');
        $timestamp = time();
        $edate = date('Y-m-d', $timestamp);
        $dates = array();
        $x = 7;
        $y = 0;
        $missed = array();
        $visited = array();

        //If no parameters are passed, get enrolled patients for the past 7 days
        if ($startdate == "" || $enddate == "") {
            // for ($i = 0; $i < $x; $i++) {
            if (1) {
                $sdate = date('Y-m-d', $timestamp);
                //Store the days in an array
                $dates[$y] = $sdate;
                $y++;
                // }
                //If sunday is included, add one more day
                // else {$x = 8;}
                $timestamp += 24 * 3600;
            }
            $start_date = $sdate;
            $end_date = $edate;
        } else {
            $startdate = strtotime($startdate);
            for ($i = 0; $i < $x; $i++) {
                // if (1) {
                $sdate = date('Y-m-d', $startdate);
                //Store the days in an array

                $dates[$y] = $sdate;
                $y++;
                // }
                //If sunday is included, add one more day
                // else {$x = 8;}
                $startdate += 24 * 3600;
            }
            $start_date = $startdate;
            $end_date = $enddate;
        }
        //Get Data for total_expected and total_visited in selected period
        $start_date = $first_date;
        $end_date = $last_date;
        $sql = "SELECT temp1.appointment,
		               temp1.total_expected,
		               temp2.total_visited 
		        FROM (SELECT pa.appointment,
		        	         count(distinct pa.patient) as total_expected 
		        	  FROM patient_appointment pa 
		        	  WHERE pa.appointment 
		        	  BETWEEN '$start_date' 
		        	  AND '$end_date' 
		        	  AND pa.facility='$facility_code' 
		        	  GROUP BY pa.appointment) as temp1 
                LEFT JOIN (SELECT dispensing_date, 
                	              COUNT( DISTINCT patient_id ) AS total_visited 
                	       FROM patient_visit 
                	       WHERE dispensing_date 
                	       BETWEEN  '$start_date' 
                	       AND  '$end_date' 
                	       AND facility='$facility_code' 
                	       GROUP BY dispensing_date) as temp2 ON temp1.appointment=temp2.dispensing_date
                	       
               UNION
               
               SELECT temp2.dispensing_date as appointment,
		               temp1.total_expected,
		               temp2.total_visited 
		        FROM (SELECT pa.appointment ,
		        	         count(distinct pa.patient) as total_expected 
		        	  FROM patient_appointment pa 
		        	  WHERE pa.appointment 
		        	  BETWEEN '$start_date' 
		        	  AND '$end_date' 
		        	  AND pa.facility='$facility_code' 
		        	  GROUP BY pa.appointment) as temp1 
                RIGHT JOIN (SELECT dispensing_date, 
                	              COUNT( DISTINCT patient_id ) AS total_visited 
                	       FROM patient_visit 
                	       WHERE dispensing_date 
                	       BETWEEN  '$start_date' 
                	       AND  '$end_date' 
                	       AND facility='$facility_code' 
                	       GROUP BY dispensing_date) as temp2 ON temp1.appointment=temp2.dispensing_date
               
               ";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();

        $outer_array = array();
        foreach ($results as $result) {
            $outer_array[$result['appointment']]['expected'] = $result['total_expected'];
            $outer_array[$result['appointment']]['visited'] = $result['total_visited'];
        }
        $keys = array_keys($outer_array);
        //Loop through dates and check if they are in the result array
        foreach ($dates as $date) {
            $index = array_search($date, $keys);
            //echo $index."--<br>";
            if ($index === false) {
                //echo $date." -- ".$index."<br>";
                $visited[] = 0;
                $missed[] = 0;
            } else {
                $visited[] = @(int) $outer_array[$keys[$index]]['visited'];
                $missed[] = @(int) $outer_array[$keys[$index]]['expected'];
            }
        }
        $categories = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $resultArray = array(array('name' => 'Visited', 'data' => $visited), array('name' => 'Expected', 'data' => $missed));
        $resultArray = json_encode($resultArray);
        $categories = json_encode($categories);
        $data['resultArraySize'] = 6;
        $data['container'] = "chart_appointments";
        $data['chartType'] = 'bar';
        $data['chartTitle'] = 'Patients Expected';
        $data['yAxix'] = 'Patients';
        $data['categories'] = $categories;
        $data['resultArray'] = $resultArray;
        echo view('\Modules\ADT\Views\\chart_v', $data);
    }

    public function getStockSafetyQty($stock_type = "2") {
        $facility_code = session()->get("facility");
        //Main Store
        if ($stock_type == '1') {
            $stock_param = "AND source !=destination";
        }
        //Pharmacy
        else if ($stock_type == '2') {
            $stock_param = "AND source =destination";
        }
        $sql = "SELECT d.drug as drug_name,du.Name as drug_unit,temp1.qty as stock_level,temp2.minimum_consumption FROM (SELECT drug_id, SUM( balance ) AS qty FROM drug_stock_balance WHERE expiry_date > CURDATE() AND stock_type =  '$stock_type' AND balance >=0 GROUP BY drug_id) as temp1 LEFT JOIN (SELECT drug, SUM( quantity_out ) AS total_consumption, SUM( quantity_out ) * 0.5 AS minimum_consumption FROM drug_stock_movement WHERE DATEDIFF( CURDATE() , transaction_date ) <=90 AND facility='$facility_code' $stock_param GROUP BY drug) as temp2 ON temp1.drug_id=temp2.drug LEFT JOIN drugcode d ON d.id=temp1.drug_id LEFT JOIN drug_unit du ON du.id=d.unit WHERE temp1.qty<temp2.minimum_consumption";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $tmpl = array('table_open' => '<table id="stock_level" class="table table-striped table-condensed">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('No', 'Drug', 'Unit', 'Qty (Units)', 'Threshold Qty (Units)', 'Order Priority');
        $x = 1;
        foreach ($results as $drugs) {
            if ($drugs['minimum_consumption'] == 0 and $drugs['stock_level'] == 0) {
                $priority = 100;
            } else {
                $priority = ($drugs['stock_level'] / $drugs['minimum_consumption']) * 100;
            }
            //Check for priority
            if ($priority >= 50) {
                $priority_level = "<span class='low_priority'><b>LOW</b></span>";
            } else {
                $priority_level = "<span class='high_priority'><b>HIGH</b></span>";
            }

            $this->table->addRow($x, $drugs['drug_name'], $drugs['drug_unit'], number_format($drugs['stock_level']), number_format($drugs['minimum_consumption']), $priority_level);
            $x++;
        }
        $drug_display = $this->table->generate();
        echo $drug_display;
    }

    public function getPatientMasterList() {
        ini_set("memory_limit", '2048M');
        helper('file');
        helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "patient_master_list.csv";
        $query = 
        "SELECT ccc_number,first_name,other_name,last_name,date_of_birth,age,maturity,pob,gender,pregnant,current_weight,current_height,current_bsa,current_bmi,phone_number,physical_address,alternate_address,other_illnesses,other_drugs,drug_allergies,tb,smoke,alcohol,date_enrolled,patient_source,supported_by,service,start_regimen,start_regimen_date,current_status,sms_consent,family_planning,tbphase,startphase,endphase,partner_status,status_change_date,disclosure,support_group,current_regimen,nextappointment,days_to_nextappointment,clinicalappointment,start_height,start_weight,start_bsa,start_bmi,transfer_from,prophylaxis,isoniazid_start_date,isoniazid_end_date,rifap_isoniazid_start_date,rifap_isoniazid_end_date,pep_reason,differentiated_care_status, ".
        "CASE WHEN t.is_tested = 1 THEN 'YES' ".
        "ELSE 'NO' END AS is_tested ".
        ",test_date	as prep_test_date, ".
        "CASE WHEN t.test_result = 1 THEN 'Positive' ".
        "ELSE 'Negative' END AS  prep_test_result, ".
        "name as prep_reason_name ".
        "FROM vw_patient_list v1 ".
		"LEFT JOIN (".
			"SELECT ppt.*,pr.name ".
			"FROM patient_prep_test ppt ".
			"INNER JOIN prep_reason pr ON pr.id = ppt.prep_reason_id ".
			"INNER JOIN (".
					"SELECT patient_id, MAX(test_date) test_date ".
					"FROM patient_prep_test ".
					"GROUP BY patient_id".
					") t ON t.patient_id = ppt.patient_id AND t.test_date = ppt.test_date ".
			"GROUP BY ppt.patient_id ".
			") t ON t.patient_id = v1.patient_id ".
		"GROUP BY v1.patient_id";

        $db = \Config\Database::connect();
        $results = $db->query($query);
        $util = (new \CodeIgniter\Database\Database())->loadUtils($db);
        $data = $util->getCSVFromResult($results, $delimiter, $newline);
        ob_clean(); //Removes spaces
        return $this->response->download($filename, $data);
    }

}
