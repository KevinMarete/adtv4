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
use Modules\ADT\Models\Drugcode;
use Modules\ADT\Models\Opportunistic_infection;
use Modules\ADT\Models\PatientSource;
use Modules\ADT\Models\Regimen;
use Modules\ADT\Models\Regimen_drug;
use Modules\ADT\Models\RegimenChangePurpose;
use Modules\ADT\Models\RegimenServiceType;
use Modules\ADT\Models\Supporter;
use Modules\ADT\Models\Visit_purpose;
use Updater as GlobalUpdater;

class Home_controller extends \App\Controllers\BaseController {

    var $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index() {
        $this->platform_home();
    }

    public function platform_home() {
        $session = session();
        //Check if the user is already logged in and if so, take him to their home page. Else, display the platform home page.
        $user_id = $session->get('user_id');
        if (strlen($user_id) > 0) {
            return redirect()->to(base_url() . '/home');
            //redirect("home_controller/home");
        }
        $data = array();
        $data['current'] = "home_controller";
        $data['title'] = "webADT | System Dashboard";
        $data['banner_text'] = "System Dashboard";
        $data['content_view'] = "";
        echo view("App\Views\\template_platform", $data);
    }

    public function home() {
        if (!session()->get('user_id')) {
            return redirect()->to(base_url() . '/login');
        }
        $updater = new \Updater();
        $session = session();
        $rights = User_right::getRights($session->get('access_level'));

        $menu_data = array();
        $menus = array();
        $counter = 0;
        foreach ($rights as $right) {
            $menu_data['menus'][$right->menu] = $right->access_type;
            $menus['menu_items'][$counter]['url'] = $right->menu_url;
            $menus['menu_items'][$counter]['text'] = $right->menu_text;
            $menus['menu_items'][$counter]['offline'] = $right->offline;
            $counter++;
        }
        $session->set($menu_data);
        $session->set($menus);

        //Check if the user is a pharmacist. If so, update his/her local envirinment with current values
        if ($session->get('user_indicator') == "pharmacist") {
            $facility_code = $session->get('facility');
            //Retrieve the Totals of the records in the master database that have clones in the clients!
            $today = date('m/d/Y');
            $timestamp = strtotime($today);
            $data['scheduled_patients'] = Patient_Appointment::getAllScheduled($timestamp);
        }


        //Get CCC Stores if they exist
        $ccc_stores = CCC_store_service_point::getAllActive();
        $session->set('ccc_store', $ccc_stores);

        $data['download_status'] = $updater->check_ADTRelease_downloaded();
        $session->set(['download_status' => $data['download_status']]);
        $data['update_available'] = json_decode($updater->check_ADTrelease());
        if ($data['update_available'] && (int)str_replace('.', '', $data['update_available']->release) > (int)str_replace('.', '', config('Adt_config')->adt_version)) {
            $session->set(['update_available' => true]);
        }
        else {
            $session->remove('update_available');
        }
        $data['title'] = "webADT | System Home";
        $data['content_view'] = "\Modules\ADT\Views\\home_v";
        $data['banner_text'] = "Home";

        $data['link'] = "home";
        $data['user'] = $session->get('full_name');
        echo view("\Modules\ADT\Views\\template", $data);
    }

    public function dispensement($user, $period) {
        $results = $this->db->query("SELECT * 
                    FROM `patient_visit` 
                    WHERE `user`='$user' AND DATEDIFF(CURDATE(),dispensing_date) <= '$period' ORDER BY id DESC")->getResultArray();
        $dyn_table = "<table border='1' width='100%' id='menu_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Patient ID</th><th>Batch Number</th><th>Date Dispensed</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                $dyn_table .= "<tr><td>" . $result['patient_id'] . "</td><td>" . $result['batch_number'] . "</td><td>" . $result['dispensing_date'] . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";

        $data['title'] = "webADT | Drug Dispensement";
        $data['content_view'] = "user_dispensement_log";
        $data['banner_text'] = "Home";
        $data['link'] = "home";
        $data['thetitle'] = ucfirst($user) . "'s  Patient Drug Dispensment activity over last $period days";
        $data['over'] = $period;
        $data['user_'] = ucfirst($user);
        $data['content'] = $dyn_table;
        $data['user'] = $this->session->get('full_name');
        echo view("\Modules\ADT\Views\\template", $data);
    }

    public function inventory($user, $period) {
        $results = $this->db->query("SELECT dsm.id,dru.drug, dsm.batch_number,dsm.transaction_date,dsm.source,dsm.destination FROM drug_stock_movement dsm LEFT JOIN drugcode dru ON dsm.drug = dru.id WHERE DATEDIFF(CURDATE(),dsm.transaction_date) <= '$period' AND operator='$user' ORDER BY dsm.id DESC")->getResultArray();
        $dyn_table = "<table border='1' width='100%' id='menu_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Drug</th><th>Batch Number</th><th>Transaction Date</th><th>From</th><th>To</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                $dyn_table .= "<tr><td>" . $result['drug'] . "</td><td>" . $result['batch_number'] . "</td><td>" . $result['transaction_date'] . "</td><td>" . $result['source'] . "</td><td>" . $result['destination'] . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";

        $data['title'] = "webADT | Drug Movement";
        $data['content_view'] = "user_dispensement_log";
        $data['banner_text'] = "Home";
        $data['link'] = "home";
        $data['over'] = $period;
        $data['thetitle'] = ucfirst($user) . "'s  Drug Stock Movement activity over last $period days";
        $data['user_'] = ucfirst($user);
        $data['content'] = $dyn_table;
        $data['user'] = $this->session->get('full_name');
        echo view("\Modules\ADT\Views\\template", $data);
    }

    public function synchronize_patients() {
        $data['regimens'] = Regimen::where('source', 0)->orderBy('regimen_desc')->get();
        $data['supporters'] = Supporter::all();
        $data['service_types'] = RegimenServiceType::where('active', '1')->get();
        $data['sources'] = PatientSource::where('active', '1')->get();
        $data['drugs'] = Drugcode::getAll();
        $data['regimen_change_purpose'] = RegimenChangePurpose::where('active', '1')->get();
        $data['visit_purpose'] = Visit_purpose::getAll();
        $data['opportunistic_infections'] = Opportunistic_infection::where('active', '1')->get();
        $data['regimen_drugs'] = Regimen_drug::where('source', '0')->where('active', '1')->get();
    }

    public function getNotified() {
        //Notify for patients
        // set current date
        $notice = array();
        $date = date('y-m-d');
        // parse about any English textual datetime description into a Unix timestamp
        $ts = strtotime($date);
        // find the year (ISO-8601 year number) and the current week
        $year = date('o', $ts);
        $week = date('W', $ts);
        $facility_code = $this->session->get('facility');
        // print week for the current date
        for ($i = 1; $i <= 6; $i++) {
            // timestamp from ISO week date format
            $ts = strtotime($year . 'W' . $week . $i);
            $string_date = date("l", $ts);
            $number_date = date("Y-m-d ", $ts);

            $appointment_query = $this->db->query("SELECT COUNT(distinct(patient)) as Total from patient_appointment where appointment='$number_date' and facility='$facility_code'");
            $visit_query = $this->db->query("SELECT COUNT(distinct(patient_id)) as Total from patient_visit where dispensing_date='$number_date' and visit_purpose='2'and facility='$facility_code'");
            $appointments_on_date = $appointment_query->getResultArray();
            $visits_on_date = $visit_query->getResultArray();
            $notice['Days'][$i - 1] = $string_date;
            $notice['Appointments'][$i - 1] = $appointments_on_date[0]['Total'];
            $notice['Visits'][$i - 1] = $visits_on_date[0]['Total'];
            $notice['Percentage'][$i - 1] = round((@$visits_on_date[0]['Total'] / @$appointments_on_date[0]['Total']) * 100, 2) . "%";
        }
        echo json_encode($notice);
    }

    public function get_faq() {
        error_reporting(1);
        $sql = $this->db->query("SELECT modules,questions,answers FROM faq WHERE active='1' GROUP BY modules");

        //if ($sql->countAllResult() > 0) {
            foreach ($sql->getResult()as $rows) {
                $header = $rows->questions;
            }
            // print_r ($header); die
        //}

        $data['title'] = "webADT | System Home";
        $data['content_view'] = "faq_v";
        $data['banner_text'] = "Frequently Asked Questions";
        $data['hide_side_menu'] = 1;
        $data['user'] = session()->get('full_name');
        echo view("\Modules\ADT\Views\\template", $data);
    }

    public function testlib() {
        $updater = new GlobalUpdater();
        $connection = ($this->updater->check_connection());

        $rs = $updater->check_ADTrelease();
        $rs = (json_decode($rs));

        $adt_downloaded_status = ($updater->check_ADTRelease_downloaded());
        var_dump($adt_downloaded_status);


        // echo  '
        // <script type="text/javascript" scr="https://code.jquery.com/jquery-3.4.1.min.js" ></script>
        // $update_ADT = ($this->updater->update_ADT());
    }

    public function updater($process = '') {
        $updater = new GlobalUpdater();
        if ($process == 'download') {
            $download = ($updater->download_ADTRelease());
            echo 'ADT Release Download Succesful <br />';
        }

        if ($process == 'update') {
            $download = ($updater->update_ADT());
        }
    }

}
