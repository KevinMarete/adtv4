<?php
namespace Modules\ADT\Controllers;

class System_management extends \App\Controllers\BaseController {
    var $carabiner;
    var $db;

    function __construct() {
   
        //$this->load->library('PHPExcel');
        //$this->load->helper('url');
        //$this->load->library('github_updater');
        //$this->load->library('Unzip');
        //$this->load->library('Curl');
        //date_default_timezone_set('Africa/Nairobi');
        $this->db = \Config\Database::connect();
    }

    public function index() {
        echo '1';
        return redirect()->to('login');
    }

    public function search_system($search_type, $stock_type = '2') {
        $search = $_GET['q'];
        $answer = [];
        //Patient Search
        if ($search_type == 'patient') {
            $sql = "SELECT p.id,p.patient_number_ccc,p.First_Name,p.other_name,p.Last_Name,p.phone ".
				"FROM patient p ".
				"WHERE p.patient_number_ccc LIKE '%".$search."%' OR p.First_Name LIKE '%".$search."%' OR other_name LIKE '%".$search."%' OR Last_Name LIKE '%".$search."%' OR phone LIKE '%".$search."%' ORDER BY first_name ASC";
            $query = $this->db->query($sql);
            $results = $query->getResultArray();

            if ($results) {
                foreach ($results as $result) {
                    $p_ccc = $result['patient_number_ccc'];
                    $_fname = $result['First_Name'];
                    $p_mname = $result['middle_name'];
                    $p_lname = $result['Last_Name'];
                    $p_phone = $result['phone'];
                    $res = 'CCC No: ' . $p_ccc . ' | ' . $_fname . ' ' . $p_mname . ' ' . $p_lname . ' (' . $p_phone . ')';
                    $answer[] = ["id" => $result['id'], "text" => $res];
                }
            } else {
                $answer[] = ["id" => "0", "text" => "No Results Found.."];
            }
        } else if ($search_type == 'drugcode') {
            $sql = "SELECT d.id,d.drug,du.Name as drug_unit, d.pack_size,g.name as generic_name ".
				"FROM drugcode d ".
				"LEFT JOIN drug_unit du ON du.id = d.unit ".
				"LEFT JOIN generic_name g ON g.id = d.generic_name ".
				"WHERE d.drug LIKE '%".$search."%' OR  du.Name LIKE '%".$search."%' OR g.name LIKE '%".$search."%' ";
            $query = $this->db->query($sql);
            $results = $query->getResultArray();

            if ($results) {
                foreach ($results as $result) {

                    $res = $result['drug'] . ' (' . $result['generic_name'] . ')  - ' . $result['drug_unit'];
                    $answer[] = ["id" => $result['id'], "text" => $res];
                }
            } else {
                $answer[] = ["id" => "0", "text" => "No Results Found.."];
            }
        }


        echo json_encode($answer);
    }

    public function checkConnection() {//Check Internet Connection
        $curl = new \Curl();
        $url = "http://google.com/";
        $curl->get($url);
        if ($curl->error) {
            echo json_encode('0');
        } else {
            echo json_encode('1');
        }
    }

}
