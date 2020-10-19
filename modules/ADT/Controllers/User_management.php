<?php

namespace Modules\ADT\Controllers;

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Access_log;
use \Modules\ADT\Models\Access_level;
use \Modules\ADT\Models\Sync_facility;
use \Modules\ADT\Models\CCC_store_service_point;
use Illuminate\Database\Capsule\Manager as DB;

class User_management extends \App\Controllers\BaseController
{

    var $endpoint;
    var $table_generator;

    function __construct()
    {

        session()->set("link_id", "index");
        session()->set("linkSub", "user_management");
        session()->set("linkTitle", "Users Management");
        //$this -> load -> helper('geoiploc');
        $this->endpoint = "https://hiskenya.org/api/";
    }

    public function index()
    {
        $table = new \CodeIgniter\View\Table();
        $access_level = session()->get('user_indicator');
        $user_type = "1";
        $facilities = "";
        //If user is a super admin, allow him to add only facilty admin and nascop pharmacist
        if ($access_level == "system_administrator") {
            $user_type = "indicator not in ('system_administrator')";
            $facilities = json_decode(json_encode(Facilities::getAll()), TRUE);
            $users = json_decode(json_encode(User::getAll()), TRUE);
        }
        //If user is a facility admin, allow him to add only facilty users
        else {
            $facility_code = session()->get('facility');
            $user_type = "indicator not in ('system_administrator', 'facility_administrator') and indicator != '" . $access_level . "'";
            $facilities = json_decode(json_encode(Facilities::getCurrentFacility($facility_code)), TRUE);
            $q = "u.Facility_Code='" . $facility_code . "' and u.access_level > '1'";
            $users = json_decode(json_encode(User::getUsersFacility($q)), TRUE);
        }
        $user_types = Access_Level::getAll($user_type);

        //dd($users);

        $tmpl = array('table_open' => '<table class=" table table-bordered table-striped setting_table ">');
        $table->setTemplate($tmpl);
        $table->setHeading('id', 'Name', 'Email Address', 'Phone Number', 'Access Level', 'Registered By', 'Options');
        foreach ($users as $user) {
            $links = "";
            $array_param = array('id' => $user['id'], 'role' => 'button', 'class' => 'edit_user', 'data-toggle' => 'modal');
            if ($user['Active'] == 1) {
                if ($access_level == "system_administrator" || ($access_level == "facility_administrator" and $user['Indicator'] != "facility_administrator")) {
                    $links .= anchor('user_management/disable/' . $user['id'], 'Disable', array('class' => 'disable_user'));
                }
            } else {
                $links .= anchor('user_management/enable/' . $user['id'], 'Enable', array('class' => 'enable_user'));
            }
            if ($user['Access'] == "Pharmacist") {
                $level_access = "User";
            } else {
                $level_access = $user['Access'];
            }
            $table->addRow($user['id'], $user['Name'], $user['Email_Address'], $user['Phone_Number'], $level_access, $user['Creator'], $links);
        }

        $data['users'] = $table->generate();;
        $data['user_types'] = $user_types;
        $data['facilities'] = $facilities;
        $data['order_sites'] = Sync_Facility::get_active();
        $data['title'] = "System Users";
        $data['banner_text'] = "System Users";
        $data['link'] = "users";
        $actions = array(0 => array('Edit', 'edit'), 1 => array('Disable', 'disable'));
        $data['actions'] = $actions;
        echo view("\Modules\ADT\Views\users_v", $data);
    }

    function login()
    {

        $session = session();
        helper(['cookie', 'url']);
        // $this->check_db_port();
        $users = User::getAll();

        $data = array();
        $data['stores'] = CCC_store_service_point::where('active', '1')->get();

        // (count($users) == 1) ? redirect()->to('tools/setup') : '';
        //if seesion variable user_id is not present
        // test database connection
        if (!$session->get("user_id")) {
            $session->get('message', 0);
            $data['title'] = "webADT | System Login";
            echo view("\Modules\ADT\Views\\login_v", $data);
        } else {
            /*
             * if user_id is present
             * check actual page cookie
             * redirect to actual page which is the last page accessed
             */
            // $actual_page = get_cookie("actual_page");
            echo 'I am here';
            return redirect()->to(base_url('public/home'));
            //  redirect()->to($actual_page);
            //echo 1;
        }
    }

    public static function loginUser($username, $password)
    {

        $query = DB::select("SELECT * FROM users where username = '" . $username . "'");

        if ($query) {
            $user2 = User::find($query[0]->id);


            //    echo  $query[0]->Password .'=='. md5($password);
            //    die;

            if ($query[0]->Password == md5($password)) {
                return $user2;
            } else {
                $test["attempt"] = "attempt";
                $test["user"] = $user2;
                return $test;
            }
        } else {
            return false;
        }
    }

    public function authenticate()
    {
        helper(['form', 'url']);
        $db = \Config\Database::connect();
        $data = array();
        $session = session();
        //$validated = $this->_submit_validate();


        $input = $this->validate([
            'username' => 'trim|required|min_length[2]|max_length[30]',
            'password' => 'trim|required|min_length[2]|max_length[30]',
        ]);
        if ($input) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            //$remember = $_POST["remember"];
            $ccc_store = $_POST["ccc_store"];
            $encrypt = new \Encrypt();
            $key = $encrypt->get_key();
            $encrypted_password = $key . $password;
            $logged_in = $this->loginUser($username, $encrypted_password);
            //  dd($logged_in);
            $load_access = DB::table('access_level')->where('id', $logged_in->Access_Level)->get();
            // dd($load_access);
            //This code checks if the credentials are valid
            if ($logged_in == false) {
                $data['invalid'] = true;
                $data['title'] = "System Login";
                echo view("\Modules\ADT\Views\\login_v", $data);
            }      //Check if credentials are valid for username not password
            else if (isset($logged_in["attempt"]) && $logged_in["attempt"] == "attempt" && $load_access[0]->indicator != "system_administrator") {

                //check to see whether the user is active
                if ($logged_in["user"]->Active == 0) {
                    $data['inactive'] = true;
                    $data['title'] = "System Login";
                    $data['login_attempt'] = "<p class='error'>The Account has been deactivated. Seek help from the Facility Administrator</p>";
                    echo view("\Modules\ADT\Views\\login_v", $data);
                } else {
                    $data['invalid'] = false;
                    $data['title'] = "System Login";
                    $data['login_attempt'] = "enter the correct password!</p>";
                    echo view("\Modules\ADT\Views\\login_v", $data);
                    /*
                     *
                      //Check if there is a login attempt
                      if (!$this -> session -> userdata($username . '_login_attempt')) {

                      $login_attempt = 1;
                      $this -> session -> set_userdata($username . '_login_attempt', $login_attempt);
                      $fail = $this -> session -> userdata($username . '_login_attempt');
                      $data['login_attempt'] = "(Attempt: " . $fail . " )";
                      } else {

                      //Check if login Attempt is below 4
                      if ($this -> session -> userdata($username . '_login_attempt') && $this -> session -> userdata($username . '_login_attempt') <= 4) {
                      $login_attempt = $this -> session -> userdata($username . '_login_attempt');
                      $login_attempt++;
                      $this -> session -> set_userdata($username . '_login_attempt', $login_attempt);
                      $fail = $this -> session -> userdata($username . '_login_attempt');
                      $data['login_attempt'] = "(Attempt: " . $fail . " )";
                      }

                      if ($this -> session -> userdata($username . '_login_attempt') > 4) {
                      $fail = $this -> session -> userdata($username . '_login_attempt');
                      $data['login_attempt'] = "<p class='error'>The Account has been deactivated. Seek help from the Facility Administrator</p>";
                      $this -> session -> set_userdata($username . '_login_attempt', 0);
                      $this -> load -> database();
                      $query = $this -> db -> query("UPDATE users SET Active='0' WHERE(username='$username' or email_address='$username' or phone_number='$username')");
                      //Log Denied User in denied_log
                      $new_denied_log = new Denied_Log();
                      $new_denied_log -> ip_address = $_SERVER['REMOTE_ADDR'];
                      $new_denied_log -> location = $this -> getIPLocation();
                      $new_denied_log -> user_id = Users::getUserID($username);
                      $new_denied_log -> save();

                      }
                      }
                     *
                     */
                }
            } else if (isset($logged_in["attempt"]) && $logged_in["attempt"] == "attempt" && $load_access[0]->indicator == "system_administrator") {
                $data['title'] = "System Login";
                $data['invalid'] = true;
                echo view("\Modules\ADT\Views\\login_v", $data);
            } else {
                //If the credentials are valid, continue
                $today_time = strtotime(date("Y-m-d"));
                $create_time = strtotime($logged_in->Time_Created);
                //check to see whether the user is active
                if ($logged_in->Active == "0" && $logged_in->Access->Indicator != "system_administrator") {
                    $data['inactive'] = true;
                    $data['title'] = "System Login";
                    echo view("\Modules\ADT\Views\\login_v", $data);
                }
                /*
                  else if (($today_time - $create_time) > (90 * 24 * 3600) && $logged_in -> Access -> Indicator != "system_administrator") {
                  $user_id = Users::getUserID($username);
                  $this -> session -> set_userdata('user_id', $user_id);
                  $data['title'] = "System Login";
                  $data['expired'] = true;
                  $data['login_attempt'] = "Your Password Has Expired.<br/>Please Click <a href='change_password'>Here</a> to Change your Current Password";
                  $this -> load -> view("login_v", $data);
                  }
                 */ else if ($logged_in->Active == "1" && $logged_in->Signature != 1 && $load_access[0]->indicator != "system_administrator") {

                    $user_id = Users::getUserID($username);
                    $session->set('user_id', $user_id);
                    $facility_details = Facilities::getCurrentFacility($logged_in->Facility_Code);
                    $data['unactivated'] = true;
                    $data['title'] = "System Login";
                    echo view("\Modules\ADT\Views\\login_v", $data);
                }
                //looks good. Continue!
                else {
                    //$facility_details = Facilities::getCurrentFacility($logged_in -> Facility_Code);
                    $code = $logged_in->Facility_Code;
                    $facility_details = $this->getFacilityDetails($logged_in->Facility_Code);
                    //print_r($facility_details);
                    // die;

                    $phone = $logged_in->Phone_Number;
                    $check = substr($phone, 0);
                    $phone = str_replace('+254', '', $phone);
                    $load_access = DB::table('access_level')->where('id', $logged_in->Access_Level)->get();
                    $session_data = array(
                        'user_id' => $logged_in->id,
                        'user_indicator' => $load_access[0]->indicator,
                        'facility_name' => $facility_details[0]['name'],
                        'adult_age' => $facility_details[0]['adult_age'],
                        'access_level' => $logged_in->Access_Level,
                        'username' => $logged_in->Username,
                        'full_name' => $logged_in->Name,
                        'Email_Address' => $logged_in->Email_Address,
                        'Phone_Number' => $phone,
                        'facility' => $logged_in->Facility_Code,
                        'facility_subcounty' => $facility_details[0]['subcounty_'],
                        'facility_county' => $facility_details[0]['county_'],
                        'subcounty_id' => $facility_details[0]['district_id'],
                        'county_id' => $facility_details[0]['county_id'],
                        'ccc_store' => ($ccc_store > 0) ? $ccc_store : $logged_in->ccc_store_sp,
                        'ccc_store_id' => ($ccc_store > 0) ? $ccc_store : $logged_in->ccc_store_sp,
                        'facility_id' => $facility_details[0]['id'],
                        'county' => $facility_details[0]['county'],
                        'facility_phone' => $facility_details[0]['phone'],
                        'facility_sms_consent' => $facility_details[0]['map'],
                        'lost_to_follow_up' => ((@$facility_details[0]['lost_to_follow_up']) !== null) ? @$facility_details[0]['lost_to_follow_up'] : 90,
                        'pill_count' => ((@$facility_details[0]['pill_count']) !== null) ? @$facility_details[0]['pill_count'] : 0,
                        'medical_number' => ((@$facility_details[0]['medical_number']) !== null) ? 1 : 0,
                        'facility_dhis' => ((@$facility_details[0]['facility_dhis']) !== "0") ? 1 : 0,
                        'autobackup' => ((@$facility_details[0]['autobackup']) !== null) ? @$facility_details[0]['autobackup'] : 0
                    );

                    $session->set($session_data);

                    $user = $session->get('user_id');
                    $sql = "update access_log set access_type='Logout' where user_id='$user'";
                    $db->query($sql);
                    $new_access_log = new Access_Log();
                    $new_access_log->machine_code = implode(",", $session_data);
                    $new_access_log->user_id = $session->get('user_id');
                    $new_access_log->access_level = $session->get('access_level');
                    $new_access_log->start_time = date("Y-m-d H:i:s");
                    $new_access_log->facility_code = $session->get('facility');
                    $new_access_log->access_type = "Login";
                    $new_access_log->save();
                    //Set session to redirect the page to the previous page before logged out
                    $session->set("prev_page", "1");
                    return redirect()->to(base_url('public/home'));
                }
            }
        } else { //Not validated
            $data = array();
            $data['title'] = "System Login";
            $data['validation'] = $this->validator;
            $data['stores'] = CCC_store_service_point::where('active', '1')->get();
            echo view("\Modules\ADT\Views\\login_v", $data);
        }
    }

    public function getFacilityDetails($facility_code)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT f.county county_id,f.district district_id,f.*, c.county county_, d.name subcounty_ 
                FROM facilities f 
                LEFT JOIN  counties c  
                ON c.id = f.county left JOIN district d ON d.id = f.district
                WHERE f.facilitycode=?";
        return $db->query($sql, array($facility_code))->getResultArray();
    }

    public function logout($param = "1")
    {
        helper('cookie');
        $db = \Config\Database::connect();
        $session = session();
        $machine_code = $session->get("machine_code_id");
        $last_id = Access_Log::getLastUser($session->get('user_id'));
        $date = date("Y-m-d H:i:s");
        DB::update("UPDATE access_log SET access_type = 'Logout', end_time = '$date' WHERE id='$last_id'");
        $session->destroy();

        if ($param == "2") {
            delete_cookie("actual_page");
        }
        return redirect()->to(base_url() . '/public/login');
    }

    public function template($data)
    {
        $data['show_menu'] = 0;
        $data['show_sidemenu'] = 0;
        $template = new Template();
        $template->index($data);
    }

    public function get_stores()
    {
        $store_results = CCC_store_service_point::getAllActive();
        echo json_encode($store_results);
    }
}
