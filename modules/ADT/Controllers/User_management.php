<?php

namespace Modules\ADT\Controllers;

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\User_facility;
use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Access_log;
use \Modules\ADT\Models\Access_level;
use \Modules\ADT\Models\Sync_facility;
use \Modules\ADT\Models\Password_log;
use \Modules\ADT\Models\CCC_store_service_point;
use Illuminate\Database\Capsule\Manager as DB;

class User_management extends \App\Controllers\BaseController {

    var $endpoint;
    var $table_generator;
    var $session;
    var $db;
    var $table;
    var $encrypt;

    function __construct() {

        session()->set("link_id", "index");
        session()->set("linkSub", "user_management");
        session()->set("linkTitle", "Users Management");
        //$this -> load -> helper('geoiploc');
        $this->endpoint = "https://hiskenya.org/api/";
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
        $this->encrypt = new \Encrypt();
    }

    function sendToLgin() {
        return redirect()->to(base_url('/login'));
    }

    public function get_sites($user_id = '') {
        $row = User_facility::where('user_id',$user_id)->first();
        if ($row) {
            echo $row->facility;
        }
    }

    public function index() {
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
                    $links .= anchor(base_url() . '/user_management/disable/' . $user['id'], 'Disable', array('class' => 'disable_user'));
                }
            } else {
                $links .= anchor(base_url() . '/user_management/enable/' . $user['id'], 'Enable', array('class' => 'enable_user'));
            }
            if ($user['Access'] == "Pharmacist") {
                $level_access = "User";
            } else {
                $level_access = $user['Access'];
            }
            $table->addRow($user['id'], $user['Name'], $user['Email_Address'], $user['Phone_Number'], $level_access, $user['Creator'], $links);
        }

        $data['users'] = $table->generate();
        ;
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

    function login() {

        $session = session();
        helper(['cookie', 'url']);
        // $this->check_db_port();
        $users = User::getAll();

        $data = array();
        $data['stores'] = CCC_store_service_point::where('active', '1')->get();

        if(count($users) <= 1) return redirect()->to(base_url('/setup'));
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
            return redirect()->to(base_url('/home'));
            //  redirect()->to($actual_page);
            //echo 1;
        }
    }

    public static function loginUser($username, $password) {

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

    public function authenticate() {
        helper(['form', 'url']);
        $db = \Config\Database::connect();
        $data = [];
        $session = session();

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
            $load_access = DB::table('access_level')->where('id', $logged_in['user']->Access_Level ?? 0)->get();
            if (!isset($logged_in)) {
                $this->session->set('m_error_msg', 'Invalid Credentials. Please try again');
                return redirect()->back()->withInput();
            }
            // dd($load_access);
            //This code checks if the credentials are valid
            if ($logged_in == false) {
                $data['invalid'] = true;
                $data['title'] = "System Login";
                $this->session->set('m_error_msg', 'Invalid Credentials. Please try again');
                return redirect()->back()->withInput();
            }      //Check if credentials are valid for username not password
            else if (isset($logged_in["attempt"]) && $logged_in["attempt"] == "attempt" && $load_access[0]->indicator != "system_administrator") {

                //check to see whether the user is active
                if ($logged_in["user"]->Active == 0) {
                    $data['inactive'] = true;
                    $data['title'] = "System Login";
                    $data['login_attempt'] = "<p class='error'>The Account has been deactivated. Seek help from the Facility Administrator</p>";
                    $this->session->set('m_error_msg', 'The Account has been deactivated. Seek help from the Facility Administrator');
                    return redirect()->back()->withInput();
                } else {
                    $data['invalid'] = false;
                    $data['title'] = "System Login";
                    $data['login_attempt'] = "enter the correct password!</p>";
                    $this->session->set('m_error_msg', 'Invalid Credentials. Please try again');
                    return redirect()->back()->withInput();
                }

            } else if (isset($logged_in["attempt"]) && $logged_in["attempt"] == "attempt" && $load_access[0]->indicator == "system_administrator") {
                $data['title'] = "System Login";
                $data['invalid'] = true;
                $this->session->set('m_error_msg', 'Invalid Credentials. Please try again');
                return redirect()->back()->withInput();
            } else {
                //If the credentials are valid, continue
                $today_time = strtotime(date("Y-m-d"));
                $create_time = strtotime($logged_in->Time_Created);
                //check to see whether the user is active
                if ($logged_in->Active == "0" && $logged_in->Access->Indicator != "system_administrator") {
                    $data['inactive'] = true;
                    $data['title'] = "System Login";
                    $this->session->set('m_error_msg', 'The Account is not active. Seek help from the Administrator');
                    return redirect()->back()->withInput();
                }
                /*
                  else if (($today_time - $create_time) > (90 * 24 * 3600) && $logged_in -> Access -> Indicator != "system_administrator") {
                  $user_id = Users::getUserID($username);
                  $this -> session -> set('user_id', $user_id);
                  $data['title'] = "System Login";
                  $data['expired'] = true;
                  $data['login_attempt'] = "Your Password Has Expired.<br/>Please Click <a href='change_password'>Here</a> to Change your Current Password";
                  $this -> load -> view("login_v", $data);
                  }
                 */ else if ($logged_in->Active == "1" && $logged_in->Signature != 1 && $load_access[0]->indicator != "system_administrator") {

                    $user_id = User::getUserID($username);
                    $session->set('user_id', $user_id);
                    $facility_details = Facilities::getCurrentFacility($logged_in->Facility_Code);
                    $data['unactivated'] = true;
                    $data['title'] = "System Login";
                    $this->session->set('m_error_msg', 'Your Account Has Not Been Activated.<br/>Please Check your Email to Activate Account');
                    return redirect()->back()->withInput();
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
                    $session_data = [
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
                    ];

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
                    return redirect()->back()->withInput();
                }
            }
        } else { //Not validated
            $data = [];
            $data['title'] = "System Login";
            $data['validation'] = $this->validator;
            $data['stores'] = CCC_store_service_point::where('active', '1')->get();
            return redirect()->back()->withInput();
        }
    }

    public function getFacilityDetails($facility_code) {
        $db = \Config\Database::connect();
        $sql = "SELECT f.county county_id,f.district district_id,f.*, c.county county_, d.name subcounty_ 
                FROM facilities f 
                LEFT JOIN  counties c  
                ON c.id = f.county left JOIN district d ON d.id = f.district
                WHERE f.facilitycode=?";
        return $db->query($sql, array($facility_code))->getResultArray();
    }

    public function logout($param = "1") {
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
        return redirect()->to(base_url() . '/login');
    }

    public function resetPassword() {
        $data['title'] = "Reset Password";
        echo view('\Modules\ADT\Views\\resend_password_v', $data);
    }

    public function resendPassword() {

        $type = $this->request->getPost("type");
        $characters = strtoupper("abcdefghijklmnopqrstuvwxyz");
        $characters = $characters . 'abcdefghijklmnopqrstuvwxyz0123456789';
        $random_string_length = 8;
        $string = '';
        for ($i = 0; $i < $random_string_length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        $password = $string;
        $key = $this->encrypt->get_key();
        $encrypted_password = md5($key . $password);
        $timestamp = date("Y-m-d");

        //Change the password
        if ($type == 'email') {
            $email = $this->request->getPost("contact_email");
            $user_id_sql = $this->db->query("SELECT id FROM users WHERE Email_Address='$email' LIMIT 1");
            $arr = $user_id_sql->getResultArray();
            $count = count($arr);
            $user_id = "";
            if ($count == 0) {
                $message = '<p class="message error">The email you entered was not found ! </p>';
                $this->resetPassword($message);
            } else {
                foreach ($arr as $us_id) {
                    $user_id = $us_id['id'];
                }
                $query = $this->db->query("update users set Password='$encrypted_password',Time_Created='$timestamp' where Email_Address='$email'");
                $new_password_log = new Password_Log();
                $new_password_log->user_id = $user_id;
                $new_password_log->password = $encrypted_password;
                $new_password_log->save();
                $this->sendPassword($email, $password, 'email');
            }
        } else if ($type == 'phone') {
            $phone = $this->request->getPost("contact_phone");
            $user_id_sql = $this->db->query("SELECT id FROM users WHERE Phone_Number='$phone' LIMIT 1");
            $arr = $user_id_sql->getResultArray();
            $count = count($arr);
            $user_id = "";
            if ($count == 0) {
                $data['error'] = '<p class="alert-error">The phone number your entered was not found ! </p>';
                $this->resetPassword($data);
            } else {
                foreach ($arr as $us_id) {
                    $user_id = $us_id['id'];
                }
                $query = $this->db->query("update users set Password='$encrypted_password',Time_Created='$timestamp' where Phone_Number='$phone'");
                $new_password_log = new Password_Log();
                $new_password_log->user_id = $user_id;
                $new_password_log->password = $encrypted_password;
                $new_password_log->save();
                $this->sendPassword($phone, $password, "phone");
            }
        }
    }

    public function sendPassword($contact, $code = "", $type = "phone") {

        //If activation code is to be sent through email
        if ($type == "email") {

            $email = trim($contact);
            //setting the connection variables
            $config['mailtype'] = "html";
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.googlemail.com';
            $config['smtp_port'] = 465;
            $config['smtp_user'] = stripslashes('webadt.chai@gmail.com');
            $config['smtp_pass'] = stripslashes('WebAdt_052013');
            ini_set("SMTP", "ssl://smtp.gmail.com");
            ini_set("smtp_port", "465");
            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");
            $this->email->from('webadt.chai@gmail.com', "WEB_ADT CHAI");
            $this->email->to("$email");
            $this->email->subject("Account Activation");
            $this->email->message("Dear $contact, This is your new password:<b> $code </b><br>
				<br>
				Regards,<br>
				Web ADT Team
				");

            //success message else show the error
            if ($this->email->send()) {
                $data['message'] = 'Email address was sent to <b>' . $email . '</b> <br/>Your Password was Reset';
                //unlink($file);
                $this->email->clear(TRUE);
            } else {
                //$data['error'] = $this -> email -> print_debugger();
                //show_error($this -> email -> print_debugger());
            }
            //ob_end_flush();
            $data['reset'] = true;
            delete_cookie("actual_page");
            $data['title'] = "webADT | System Login";
            echo view("login_v", $data);
        }
    }

    public function profile($data = "") {
        $data['title'] = 'webADT | User Profile';
        $data['banner_text'] = 'My Profile';
        $data['content_view'] = 'user_profile_v';
        $this->base_params($data);
    }

    public function profile_update() {
        $data['title'] = 'webADT | User Profile';
        $data['banner_text'] = 'My Profile';
        $user_id = $this->session->get('user_id');
        $full_name = $this->request->getPost('u_fullname');
        $user_name = $this->request->getPost('u_username');
        $email = $this->request->getPost('u_email');
        $phone = $this->request->getPost('u_phone');
        $store = $this->request->getPost('user_store');

        $c_user = 0;
        $e_user = 0;

        //Check if username does not already exist
        //If username was changed by the user, check if it exists in the db
        if (session()->set('username') != $user_name) {
            $username_exist_sql = $this->db->query("SELECT * FROM users WHERE username='$user_name'");
            $c_user = count($username_exist_sql->getResultArray());
        }
        //If email was changed by the user, check if it exists in the db
        if ($this->session->set('Email_Address') != $email) {
            $email_exist_sql = $this->db->query("SELECT * FROM users WHERE Email_Address='$email'");
            $e_user = count($email_exist_sql->getResultArray());
        }

        if ($c_user > 0 and $e_user > 0) {
            $data['error'] = "<span class='message error'>The username and email entered are already in use!</span>";
        } else if ($c_user > 0) {
            $data['error'] = "<span class='message error'>The username entered is already in use !</span>";
        } else if ($e_user > 0) {
            $data['error'] = "<span class='message error'>The email entered is already in use !</span>";
        }

        //Neither email nor username is in use
        else if ($e_user == 0 and $c_user == 0) {
            //Update user details
            $update_user_sql = $this->db->query("UPDAT users SET Name='$full_name',username='$user_name',Email_Address='$email',Phone_Number='$phone',ccc_store_sp='$store' WHERE id='$user_id'");
            if ($update_user_sql == 1) {
                $message_success = "<span class='message info'>Your details were successfully updated!<span>";
            }
            //Update session details!
            $session_data = array('username' => $user_name, 'full_name' => $full_name, 'Email_Address' => $email, 'Phone_Number' => $phone, 'ccc_store_id' => $store);
            $this->session->set($session_data);
            $this->session->set("message_user_update_success", $message_success);
        }

        //Add/update user ordering sites
        $this->save_user_facilities(session()->set('user_id'), $this->request->getPost('profile_user_facilities_holder', TRUE));


        $previous_url = $this->request->getCookie('actual_page', true);
        //redirect($previous_url);
        return redirect()->to(base_url('/' . $previous_url));
    }

    public function base_params($data) {
        echo view("\Modules\ADT\Views\\template", $data);
    }

    public function save() {
        //default password
        $default_password = '123456';

        $user_data = [
            'Name' => $this->post('fullname', TRUE),
            'Username' => $this->post('username', TRUE),
            'Password' => md5($this->encrypt->get_key() . $default_password),
            'Access_Level' => $this->post('access_level', TRUE),
            'Facility_Code' => $this->post('facility', TRUE),
            'Created_By' => $this->session->get('user_id'),
            'Time_Created' => date('Y-m-d,h:i:s A'),
            'Phone_Number' => $this->post('phone', TRUE),
            'Email_Address' => $this->post('email', TRUE),
            'Active' => 1,
            'Signature' => 1
        ];

        $this->db->table("users")->insert($user_data);

        //Save user facilities
        $this->save_user_facilities($this->db->insertID(), $this->post('user_facilities_holder', TRUE));

        $this->session->set('msg_success', $this->post('fullname') . ' \' s details were successfully saved! The default password is <strong>' . $default_password . '</strong>');
        return redirect()->to(base_url('/settings_management'));
    }

    public function edit() {
        $access_level = $this->session->get('user_indicator');
        $user_type = "1";
        $facilities = "";
        //If user is a super admin, allow him to add only facilty admin and nascop pharmacist
        if ($access_level == "system_administrator") {
            $user_type = "indicator='nascop_pharmacist' or indicator='facility_administrator'";
            $facilities = Facilities::orderBy('name')->get()->toArray();
        }
        //If user is a facility admin, allow him to add only facilty users
        else if ($access_level == "facility_administrator") {
            $facility_code = $this->session->get('facility');
            $user_type = "indicator='pharmacist'";
            $facilities = Facilities::where('facilitycode', $facility_code)->get()->toArray();
        }

        $user_id = @$_GET['u_id'];
        $data['users'] = User::where('id', $user_id)->get()->toArray();
        $data['user_type'] = Access_level::getAll($user_type);
        echo json_encode($data);
    }

    public function update() {
        $user_id = $this->post('user_id');
        $name = $this->post('fullname');
        $username = $this->post('username');
        $access_Level = $this->post('access_level');
        $phone_number = $this->post('phone');
        $email_address = $this->post('email');
        $facility = $this->post('facility');

        $query = $this->db->query("UPDATE users SET Name='$name',Username='$username',Access_Level='$access_Level',Phone_Number='$phone_number',Email_Address='$email_address',Facility_Code='$facility' WHERE id='$user_id'");
        $this->session->set('msg_success', $this->post('username') . ' \' s details were successfully Updated!');
        $this->session->setFlashdata('filter_datatable', $this->post('username'));
        //Filter datatable
        return redirect()->to(base_url('/settings_management'));
    }

    public function enable($user_id) {
        $user = User::find($user_id);
        $user->Active = '1';
        $user->save();
        $this->session->set('msg_success', $user->Name . ' was enabled!');
        $this->session->setFlashdata('filter_datatable', $user->Name);
        //Filter datatable
        return redirect()->to(base_url('/settings_management'));
    }

    public function disable($user_id) {
        $user = User::find($user_id);
        $user->Active = '0';
        $user->save();
        $this->session->set('msg_error', $user->Name . ' was disabled!');
        $this->session->setFlashdata('filter_datatable', $user->Name);
        //Filter datatable
        return redirect()->to(base_url('/settings_management'));
    }

    public function save_user_facilities($user_id = '', $user_facilites = '') {
        $save_data = array('user_id' => $user_id, 'facility' => json_encode(explode(',', $user_facilites)));
        $table = 'user_facilities';
        if ($user_facilites) {
            $user = User::find($user_id)->first();
            //$user = $this->db->where($table, array('user_id' => $user_id))->get();
            if ($user) {
                $build = $this->db->table($table);
                $build->where('id', $user->id);
                $build->update($save_data);
            } else {
                $build = $this->db->table($table);
                $build->insert($save_data);
            }
        }
        return $save_data;
    }

    public function save_new_password($type = 2) {
        $old_password = $this->request->getPost("old_password");
        $new_password = $this->request->getPost("new_password");
        $valid_old_password = $this->correct_current_password($old_password);

        $key = $this->encrypt->get_key();
        $encrypted_password = md5($key . $new_password);
        $user_id = session()->get('user_id');
        $timestamp = date("Y-m-d");

        //check if password matches last three passwords for this user
        $sql = "SELECT * 
		FROM (SELECT password 
		FROM password_log 
		WHERE user_id='$user_id' 
		ORDER BY id DESC 
		LIMIT 3) as pl 
		WHERE pl.password='$encrypted_password'";
        $checkpassword_query = $this->db->query($sql);
        $check_results = $checkpassword_query->getResultArray();

        //Check if old password is correct
        if ($valid_old_password == FALSE) {
            if ($type == 2) {
                $response = array('msg_password_change' => 'password_no_exist');
            } else {
                $this->session->set("matching_password", "This is not your current password");
            }
        } else if ($check_results) {
            if ($type == 2) {
                $response = array('msg_password_change' => 'password_exist');
            } else {
                $this->session->set("matching_password", "The current password Matches a Previous Password");
            }
        } else {
            //update new password
            $sql = "UPDATE users 
			SET Password='$encrypted_password',Time_Created='$timestamp' 
			WHERE id='$user_id'";
            $query = $this->db->query($sql);

            //add new password in log
            $new_password_log = new Password_Log();
            $new_password_log->user_id = $user_id;
            $new_password_log->password = $encrypted_password;
            $new_password_log->save();

            if ($type == 2) {
                $response = array('msg_password_change' => 'password_changed');
            } else {
                $this->session->set("changed_password", "Your Password Has Been Changed");
            }
        }

        //delete_cookie("actual_page");
        if ($type == 2) {
            echo json_encode($response);
        } else {
            $this->session->remove("user_id");
            return redirect()->to(base_url('/login'));
        }
    }

    public function correct_current_password($pass) {
        $key = $this->encrypt->get_key();
        $pass = $key . $pass;
        $user = User::getUserDetail(session()->get('user_id'));


        $current_password = md5($pass);


        if ($user[0]['Password'] != $current_password) {
            $this->session->setFlashdata('correct_current_password', 'The current password you provided is not correct.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function template($data) {
        $data['show_menu'] = 0;
        $data['show_sidemenu'] = 0;
        $template = new Template();
        $template->index($data);
    }

    public function get_stores() {
        $store_results = CCC_store_service_point::getAllActive();
        echo json_encode($store_results);
    }

}
