<?php

namespace Modules\ADT\Controllers;

use Illuminate\Database\Capsule\Manager as DB;
use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Updater;
use App\Libraries\Zip;
use DateTime;
use Modules\ADT\Models\Access_level;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\User_right;
use \Modules\ADT\Models\Patient_appointment;
use \Modules\ADT\Models\CCC_store_service_point;
use Modules\ADT\Models\Counties;
use Modules\ADT\Models\District;
use Modules\ADT\Models\Facilities;
use Modules\ADT\Models\Faq;
use Modules\ADT\Models\Menu;

class Admin_management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        date_default_timezone_set('Africa/Nairobi');
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function addCounty() {
        $results = Counties::all();
        $dyn_table = "<table border='1' width='100%' id='county_listing' cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>County Name</th><th> Options</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                if ($result['active'] == '1') {
                    $option = "<a href='#edit_counties' data-toggle='modal' role='button' class='edit' table='counties' county='" . $result['county'] . "' county_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/counties/" . $result['id'] . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_counties' data-toggle='modal' role='button' class='edit' table='counties' county='" . $result['county'] . "' county_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/counties/" . $result['id'] . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result['county'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'County';
        $data['table'] = 'counties';
        $data['actual_page'] = 'View Counties';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addSatellite() {
        $results = Facilities::where('parent', $this->session->get("facility"))->where('facilitycode', '!=', $this->session->get("facility"))->orderBy("name")->get()->toArray();
        $dyn_table = "<table border='1' width='100%' id='satellite_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Facility Code</th><th>Facility Name</th><th>Options</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                $option = "<a href='" . base_url() . "/admin_management/remove/" . $result['facilitycode'] . "'' class='red'>Remove</a>";
                $dyn_table .= "<tr><td>" . $result['facilitycode'] . "</td><td>" . $result['name'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Satellite';
        $data['table'] = 'satellites';
        $data['actual_page'] = 'View Satellites';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addFacility() {
        $results = Facilities::orderBy("name")->get()->toArray();
        $dyn_table = "<table border='1' width='100%' id='facility_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Facility Code</th><th>Facility Name</th><th>Options</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                if ($result['flag'] == '1') {
                    $option = "<a href='#edit_facilities' data-toggle='modal' role='button' class='edit' table='facilities' facility_name='" . $result['name'] . "' facility_code='" . $result['facilitycode'] . "' facility_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/facilities/" . $result['id'] . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_facilities' data-toggle='modal' role='button' class='edit' table='facilities' facility_name ='" . $result['name'] . "' facility_code='" . $result['facilitycode'] . "' facility_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/facilities/" . $result['id'] . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result['facilitycode'] . "</td><td>" . $result['name'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Facility';
        $data['table'] = 'facilities';
        $data['actual_page'] = 'View Facilities';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addDistrict() {
        $results = District::all();
        $dyn_table = "<table border='1' width='100%' id='district_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>District Name</th><th> Options</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                if ($result['active'] == "1") {
                    $option = "<a href='#edit_district' data-toggle='modal' role='button' class='edit' table='district' district='" . $result['name'] . "' district_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/district/" . $result['id'] . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_district' data-toggle='modal' role='button' class='edit' table='district' district='" . $result['name'] . "' district_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/district/" . $result['id'] . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result['name'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'District';
        $data['table'] = 'district';
        $data['actual_page'] = 'View Districts';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addMenu() {
        $results = Menu::all();
        $dyn_table = "<table border='1' width='100%' id='menu_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Menu Name</th><th>Menu URL</th><th>Menu Description</th><th> Options</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                if ($result['active'] == "1") {
                    $option = "<a href='#edit_menu' data-toggle='modal' role='button' class='edit' table='menu' menu_name='" . $result['menu_text'] . "' menu_url='" . $result['menu_url'] . "' menu_desc='" . $result['description'] . "' menu_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/menu/" . $result['id'] . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_menu' data-toggle='modal' role='button' class='edit' table='menu' menu_name='" . $result['menu_text'] . "' menu_url='" . $result['menu_url'] . "' menu_desc='" . $result['description'] . "' menu_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/menu/" . $result['id'] . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result['menu_text'] . "</td><td>" . $result['menu_url'] . "</td><td>" . $result['description'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Menu';
        $data['table'] = 'menu';
        $data['column'] = 'active';
        $data['actual_page'] = 'View Menus';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addFAQ() {
        $results = Faq::all();
        $dyn_table = "<table border='1' width='100%' id='faq_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Module</th><th>Question</th><th>Answer</th><th>Options</th></tr></thead><tbody>";
        $option = "";
        if ($results) {
            foreach ($results as $result) {
                if ($result['active'] == "1") {
                    $option = "<a href='#edit_faq' data-toggle='modal' role='button' class='edit' table='faq' faq_module='" . $result['modules'] . "' faq-question='" . $result['questions'] . "' faq_answer='" . $result['answers'] . "' faq_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/faq/" . $result['id'] . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_faq' data-toggle='modal' role='button' class='edit' table='faq' faq_module='" . $result['modules'] . "' faq-question='" . $result['questions'] . "' faq_answer='" . $result['answers'] . "' faq_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/faq/" . $result['id'] . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result['modules'] . "</td><td>" . $result['questions'] . "</td><td>" . $result['answers'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'FAQ';
        $data['table'] = 'faq';
        $data['actual_page'] = 'View FAQ';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addAccessLevel() {
        $results = Access_level::get()->toArray();
        $dyn_table = "<table border='1' width='100%' id='access_level_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Name</th><th>Indicator</th><th>Description</th><th> Options</th></tr></thead><tbody>";
        $option = "";
        if ($results) {
            foreach ($results as $result) {
                if ($result['active'] == "1") {
                    $option = "<a href='#edit_access_level' data-toggle='modal' role='button' class='edit' table='access_level' access_level_name='" . $result['level_name'] . "' access_level_description='" . $result['description'] . "' access_level_indicator='" . $result['indicator'] . "' access_level_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/access_level/" . $result['id'] . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_access_level' data-toggle='modal' role='button' class='edit' table='access_level' access_level_name='" . $result['level_name'] . "' access_level_description='" . $result['description'] . "' access_level_indicator='" . $result['indicator'] . "' access_level_id='" . $result['id'] . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/access_level/" . $result['id'] . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result['level_name'] . "</td><td>" . $result['indicator'] . "</td><td>" . $result['description'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Access Level';
        $data['table'] = 'access_level';
        $data['actual_page'] = 'View Access Level';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function addUsers() {
        $sql = "SELECT u.id, u.Name,u.Username, a.Level_Name as Access, u.Email_Address, u.Phone_Number, u2.Name as Creator,u.Active as Active FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id WHERE  a.level_name != 'Pharmacist'";
        $results = DB::select($sql);
        $dyn_table = "<table border='1' width='100%' id='user_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Full Name</th><th>UserName</th><th>Access Level</th><th>Email Address</th><th>Phone Number</th><th>Account Creator</th><th> Options</th></tr></thead><tbody>";
        $option = "";
        if ($results) {
            foreach ($results as $result) {
                if ($result->id != $this->session->get("user_id")) {
                    if ($result->Active == "1") {
                        $option = "<a href='" . base_url() . "/admin_management/disable/users/" . $result->id . "' class='red'>Disable</a>";
                    } else {
                        $option = "<a href='" . base_url() . "/admin_management/enable/users/" . $result->id . "' class='green'>Enable</a>";
                    }
                }
                $dyn_table .= "<tr><td>" . $result->Name . "</td><td>" . $result->Username . "</td><td>" . $result->Access . "</td><td>" . $result->Email_Address . "</td><td>" . $result->Phone_Number . "</td><td>" . $result->Creator . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Users';
        $data['table'] = 'users';
        $data['column'] = 'active';
        $data['actual_page'] = 'View Users';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function inactive() {
        $facility_code = $this->session->get("facility");
        $user = new User();
        $results = $user->getInactive($facility_code);
        $dyn_table = "<table border='1' width='100%' id='inactive_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Full Name</th><th>UserName</th><th>Access Level</th><th>Email Address</th><th>Phone Number</th><th>Account Creator</th><th> Options</th></tr></thead><tbody>";
        $option = "";
        if ($results) {
            foreach ($results as $result) {
                if ($result['id'] != $this->session->get("user_id")) {
                    if ($result['uactive'] == "1") {
                        $option = "<a href='" . base_url() . "/admin_management/disable/users/" . $result['id'] . "' class='red'>Disable</a>";
                    } else {
                        $option = "<a href='" . base_url() . "/admin_management/enable/users/" . $result['id'] . "' class='green'>Enable</a>";
                    }
                }
                $dyn_table .= "<tr><td>" . $result['name'] . "</td><td>" . $result['username'] . "</td><td>" . $result['level_name'] . "</td><td>" . $result['email_address'] . "</td><td>" . $result['phone_number'] . "</td><td>" . $result['u2name'] . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Users';
        $data['table'] = '';
        $data['actual_page'] = 'Deactivated Users';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function online() {
        $today = date('Y-m-d');
        $sql = "SELECT DISTINCT(user_id) as user_id,start_time as time_log FROM access_log  WHERE access_type = 'Login' AND start_time LIKE '%".$today."%'";
        $results = DB::select($sql);
        if ($results) {
            $user = new User();
            foreach ($results as $result) {
                $user_id = $result->user_id;
                $activity = $this->dateDiff($result->time_log, date('Y-m-d H:i:s'));
                $specific = DB::select("SELECT u.Name,u.Username, a.Level_Name as Access, u.Email_Address, u.Phone_Number, b.Name as Creator,u.Active as Active ".
                                        "from users as u LEFT JOIN access_level as a ON u.access_level = a.id LEFT JOIN users b ON u.created_by = b.id WHERE u.id = '".$user_id."'");
                $dyn_table = "<table border='1' width='100%' id='online_listing'  cellpadding='5' class='dataTables'>";
                $dyn_table .= "<thead><tr><th>Full Name</th><th>UserName</th><th>Access Level</th><th>Email Address</th><th>Activity Duration</th></tr></thead><tbody>";
                $option = "";
                if ($specific) {
                    foreach ($specific as $res) {
                        $dyn_table .= "<tr><td>" . $res->Name . "</td><td>" . $res->Username . "</td><td>" . $res->Access . "</td><td>" . $res->Email_Address . "</td><td>" . $activity . "</td></tr>";
                    }
                }
            }
        } else {
            $dyn_table = "<table border='1' width='100%' id='online_listing'  cellpadding='5' class='dataTables'>";
            $dyn_table .= "<thead><tr><th>Full Name</th><th>UserName</th><th>Access Level</th><th>Email Address</th><th>Activity Duration</th></tr></thead><tbody>";
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Users';
        $data['column'] = 'active';
        $data['table'] = '';
        $data['actual_page'] = 'Online Users';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function assignRights() {
        $sql = "select ur.id,al.level_name,m.menu_text,ur.active,ur.access_level as access_id,ur.menu as menu_id from user_right ur,menu m, access_level al where m.id=ur.menu and al.id=ur.access_level";
        $results = DB::select($sql);
        $dyn_table = "<table border='1' width='100%' id='assign_rights_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>Access Level</th><th>Menu</th><th> Options</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                if ($result->active == "1") {
                    $option = "<a href='#edit_user_right' data-toggle='modal' role='button' class='edit' table='user_right' access_id='" . $result->access_id . "' edit_menu_id='" . $result->menu_id . "' right_id='" . $result->id . "'>Edit</a> | <a href='" . base_url() . "/admin_management/disable/user_right/" . $result->id . "' class='red'>Disable</a>";
                } else {
                    $option = "<a href='#edit_user_right' data-toggle='modal' role='button' class='edit' table='user_right' access_id='" . $result->access_id . "' edit_menu_id='" . $result->menu_id . "' right_id='" . $result->id . "'>Edit</a> | <a href='" . base_url() . "/admin_management/enable/user_right/" . $result->id . "' class='green'>Enable</a>";
                }
                $dyn_table .= "<tr><td>" . $result->level_name . "</td><td>" . $result->menu_text . "</td><td>" . $option . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'User Rights';
        $data['table'] = 'user_right';
        $data['column'] = 'active';
        $data['actual_page'] = 'User Rights';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function getAccessLogs() {
        $sql = "select * from access_log al left join users u on u.id=al.user_id";
        $results = DB::select($sql);
        $dyn_table = "<table border='1' width='100%' id='access_log_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>User</th><th>Start Time</th><th>End Time</th><th>Session Duration</th><th>Status</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                $time_log = date('Y-m-d H:i:s', strtotime($result->start_time));
                if ($result->end_time) {
                    $now = date('Y-m-d H:i:s', strtotime($result->end_time));
                    $next_date = date('d-M-Y h:i:s a', strtotime($result->end_time));
                } else {
                    $now = date('Y-m-d H:i:s');
                    $next_date = "-";
                }
                $dd = date_diff(new \DateTime($time_log), new \DateTime($now));

                if ($dd->h > 0) {
                    $activity = $dd->h . " Hour(s)" . $dd->i . " Minutes and " . $dd->s . " Seconds";
                } else {
                    $activity = $dd->i . " Minutes and " . $dd->s . " Seconds";
                }
                $dyn_table .= "<tr><td>" . $result->Name . "</td><td>" . date('d-M-Y h:i:s a', strtotime($result->start_time)) . "</td><td>" . $next_date . "</td><td>" . $activity . "</td><td>" . $result->access_type . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Access Logs';
        $data['column'] = 'active';
        $data['table'] = '';
        $data['actual_page'] = 'Access Logs';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function getDeniedLogs() {
        $sql = "select * from denied_log al left join users u on u.id=al.user_id";
        $results = DB::select($sql);
        $dyn_table = "<table border='1' width='100%' id='denied_listing'  cellpadding='5' class='dataTables'>";
        $dyn_table .= "<thead><tr><th>User</th><th>Timestamp</th></tr></thead><tbody>";
        if ($results) {
            foreach ($results as $result) {
                $dyn_table .= "<tr><td>" . $result['Name'] . "</td><td>" . date('d-M-Y h:i:s a', strtotime($result['timestamp'])) . "</td></tr>";
            }
        }
        $dyn_table .= "</tbody></table>";
        $data['label'] = 'Denied Logs';
        $data['column'] = 'active';
        $data['table'] = '';
        $data['actual_page'] = 'Denied Logs';
        $data['dyn_table'] = $dyn_table;
        $this->base_params($data);
    }

    public function save($table = "") {
        if ($table == "counties") {
            $county_name = $this->post("name");
            $new_county = new Counties();
            $new_county->county = $county_name;
            $new_county->save();
            $this->session->set('msg_success', 'County: ' . $county_name . ' was Added');
            $this->session->set('default_link', 'addCounty');
        } else if ($table == "satellites") {
            $satellite_codes = explode(',', $this->post("satellite_holder"));
            if (!empty($satellite_codes)) {
                $message = '';
                $central_code = $this->session->get("facility");
                foreach ($satellite_codes as $satellite_code) {
                    $sql = "update facilities set parent = '$central_code' where facilitycode = '$satellite_code'";
                    DB::statement($sql);
                    $message .= 'Facility No: ' . $satellite_code . ' was Added as a Satellite<br/>';
                }
                $this->session->set('msg_success', $message);
            }
            $this->session->set('default_link', 'addSatellite');
        } else if ($table == "facilities") {
            $facility_code = $this->post("facility_code");
            $facility_name = $this->post("facility_name");
            Facilities::create(['facilitycode' => $facility_code, 'name' => $facility_name]);
            $this->session->set('msg_success', 'Facility: ' . $facility_name . ' was Added');
            $this->session->set('default_link', 'addFacility');
        } else if ($table == "district") {
            $disrict_name = $this->post("name");
            $new_district = new District();
            $new_district->name = $disrict_name;
            $new_district->save();
            $this->session->set('msg_success', 'District: ' . $disrict_name . ' was Added');
            $this->session->set('default_link', 'addDistrict');
        } else if ($table == "menu") {
            $menu_name = $this->post("menu_name");
            $menu_url = $this->post("menu_url");
            $menu_desc = $this->post("menu_description");
            $new_menu = new Menu();
            $new_menu->menu_text = $menu_name;
            $new_menu->menu_url = $menu_url;
            $new_menu->description = $menu_desc;
            $new_menu->save();
            $this->session->set('msg_success', 'Menu: ' . $menu_name . ' was Added');
            $this->session->set('default_link', 'addMenu');
        } else if ($table == "faq") {
            $faq_module = $this->post("faq_module");
            $faq_question = $this->post("faq_question");
            $faq_answer = $this->post("faq_answer");
            $new_faq = new Faq();
            $new_faq->modules = $faq_module;
            $new_faq->questions = $faq_question;
            $new_faq->answers = $faq_answer;
            $new_faq->save();
            $this->session->set('msg_success', 'FAQ was Added');
            $this->session->set('default_link', 'addFAQ');
        } else if ($table == "access_level") {
            $level_name = $this->post("level_name");
            $indicator = $this->post("indicator");
            $description = $this->post("description");
            $new_access_level = new Access_Level();
            $new_access_level->level_name = $level_name;
            $new_access_level->indicator = $indicator;
            $new_access_level->description = $description;
            $new_access_level->save();
            $this->session->set('msg_success', 'Access Level was Added');
            $this->session->set('default_link', 'addAccessLevel');
        } else if ($table == "users") {
            $access_level_id = $this->post('access_level', TRUE);
            //default password
            $default_password = '123456';

            $encrypt = new \Encrypt();
            $user_data = [
                'Name' => $this->post('fullname', TRUE),
                'Username' => $this->post('username', TRUE),
                'Password' => md5($encrypt->get_key() . $default_password),
                'Access_Level' => $access_level_id,
                'Facility_Code' => $this->post('facility', TRUE),
                'Created_By' => $this->session->get('user_id'),
                'Time_Created' => date('Y-m-d,h:i:s A'),
                'Phone_Number' => $this->post('phone', TRUE),
                'Email_Address' => $this->post('email', TRUE),
                'Active' => 1,
                'Signature' => 1
            ];

            if ($this->session->get('access_level') != $access_level_id) {
                User::create($user_data);
                $this->session->set('msg_success', 'User: ' . $this->post('fullname', TRUE) . ' was Added');
                $this->session->set('default_link', 'addUsers');
            } else {
                $this->session->set('msg_error', 'You do not have rights to add a user at this level');
                $this->session->set('default_link', 'addUsers');
            }
        } else if ($table == "user_right") {
            $access_level = $this->post("access_level");
            $menu = $this->post("menus");
            if ($menu) {
                $new_right = new User_right();
                $new_right->access_level = $access_level;
                $new_right->menu = $menu;
                $new_right->access_type = "4";
                $new_right->save();
                $this->session->set('msg_success', 'User Right was Added');
                $this->session->set('default_link', 'assignRights');
            }
        }
        return redirect()->to(base_url()."/home");
    }

    public function sendActivationCode($username, $contact, $password, $code = "", $type = "phone") {

        //If activation code is to be sent through email
        if ($type == "email") {
            $email = $contact;
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
            $this->email->message("Dear $username,<p> You account has been created and your password is <b>$password</b></p>Please click the following link to activate your account.
			<form action='" . base_url() . "user_management/activation' method='post'>
			<input type='submit' value='Activate account' id='btn_activate_account'>
			<input type='hidden' name='activation_code' id='activation_code' value='" . $code . "'>
			</form>
			<br>
			Regards, <br>
			Web ADT Team.
			");

            //success message else show the error
            if ($this->email->send()) {
                echo 'Your email was successfully sent to ' . $email . '<br/>';
                //unlink($file);
                $this->email->clear(TRUE);
            } else {
                //show_error($this -> email -> print_debugger());
            }
            //ob_end_flush();
        }
    }

    public function inactive_users() {
        $facility_code = $this->session->get("facility");
        $total = User::where('Facility_Code', $facility_code)->where('Active', '0')->where('Access_Level', '!=', '2')->count();
        echo @$total;
    }

    public function online_users() {
        $facility_code = $this->session->get("facility");
        $today = date('Y-m-d');
        $sql = "update access_log set access_type ='Logout' WHERE datediff('$today',`start_time`)>0";
        DB::statement($sql);
        $sql = "SELECT COUNT(DISTINCT(user_id)) AS total FROM access_log WHERE access_type = 'Login' AND `start_time` LIKE '%$today%'";
        $results = DB::select($sql);
        $total = 0;
        $temp = "";
        if ($results) {
            foreach ($results as $result) {
                $total = $result->total;
            }
        }
        $temp = $total;
        echo $temp;
    }

    public function disable($table = "", $id = "") {
        if ($table == "users") {
            $sql = "update $table set Active='0' where id='$id'";
        } else if ($table == "facilities") {
            $sql = "update $table set flag='0' where id='$id'";
        } else {
            $sql = "update $table set active='0' where id='$id'";
        }
        DB::statement($sql);
        $this->session->set('msg_error', $table . ' Record No:' . $id . ' was disabled');
        $this->setDefaultLink($table);
        return redirect()->to(base_url()."/home");
    }

    public function enable($table = "", $id = "") {
        if ($table == "users") {
            $sql = "update $table set Active='1' where id='$id'";
        } else if ($table == "facilities") {
            $sql = "update $table set flag='1' where id='$id'";
        } else {
            $sql = "update $table set active='1' where id='$id'";
        }
        DB::statement($sql);
        $this->session->set('msg_success', $table . ' Record No:' . $id . ' was enabled');
        $this->setDefaultLink($table);
        return redirect()->to(base_url()."/home");
    }

    public function remove($facilitycode = "") {
        $sql = "update facilities set parent='' where facilitycode='$facilitycode'";
        DB::statement($sql);
        $this->session->set('msg_error', ' Facility No:' . $facilitycode . ' was removed as a Satellite');
        $this->session->set('default_link', 'addSatellite');
        return redirect()->to(base_url()."/home");
    }

    public function update($table = "") {
        if ($table == "counties") {
            $county_id = $this->post("county_id");
            $county_name = $this->post("county_name");
            DB::table($table)->where('id', $county_id)->update(['county' => $county_name]);
            $this->session->set('msg_success', 'County: ' . $county_name . ' was Updated');
            $this->session->set('default_link', 'addCounty');
        } else if ($table == "facilities") {
            $facility_id = $this->post("facility_id");
            $facility_code = $this->post("facility_code");
            $facility_name = $this->post("facility_name");
            DB::table($table)->where('id', $facility_id)->update(['facilitycode' => $facility_code, 'name' => $facility_name]);
            $this->session->set('msg_success', 'Facility: ' . $facility_name . ' was Updated');
            $this->session->set('default_link', 'addFacility');
        } else if ($table == "district") {
            $district_id = $this->post("district_id");
            $district_name = $this->post("district_name");
            DB::table($table)->where('id', $district_id)->update(['name' => $district_name]);
            $this->session->set('msg_success', 'District: ' . $district_name . ' was Updated');
            $this->session->set('default_link', 'addDistrict');
        } else if ($table == "menu") {
            $menu_id = $this->post("menu_id");
            $menu_name = $this->post("menu_name");
            $menu_url = $this->post("menu_url");
            $menu_description = $this->post("menu_description");
            DB::table($table)->where('id', $menu_id)->update(['menu_text' => $menu_name, 'menu_url' => $menu_url, 'description' => $menu_description]);
            $this->session->set('msg_success', 'Menu: ' . $menu_name . ' was Updated');
            $this->session->set('default_link', 'addMenu');
        } elseif ($table == "faq") {
            $faq_id = $this->post("faq_id");
            $faq_module = $this->post("faq_module");
            $faq_question = $this->post("faq_question");
            $faq_answer = $this->post("faq_answer");
            DB::table($table)->where('id', $faq_id)->update(['modules' => $faq_module, 'questions' => $faq_question, 'answers' => $faq_answer]);
            $this->session->set('msg_success', 'FAQ was Updated');
            $this->session->set('default_link', 'addFAQ');
        } elseif ($table == "access_level") {
            $level_id = $this->post("level_id");
            $level_name = $this->post("level_name");
            $indicator = $this->post("indicator");
            $description = $this->post("description");
            DB::table($table)->where('id', $level_id)->update(['level_name' => $level_name, 'indicator' => $indicator, 'description' => $description]);
            $this->session->set('msg_success', 'Access Level was Updated');
            $this->session->set('default_link', 'addAccessLevel');
        } else if ($table == "user_right") {
            $right_id = $this->post("right_id");
            $access_id = $this->post("access_level");
            $menu_id = $this->post("menus");
            DB::table($table)->where('id', $right_id)->update(['access_level' => $access_id, 'menu' => $menu_id]);
            $this->session->set('msg_success', 'User Right was Updated');
            $this->session->set('default_link', 'assignRights');
        }
        return redirect()->to(base_url()."/home");
    }

    public function setDefaultLink($table = "") {
        if ($table == "counties") {
            $this->session->set('default_link', 'addCounty');
        } else if ($table == "users") {
            $this->session->set('default_link', 'addUsers');
        } else if ($table == "district") {
            $this->session->set('default_link', 'addDistrict');
        } else if ($table == "menu") {
            $this->session->set('default_link', 'addMenu');
        } else if ($table == "faq") {
            $this->session->set('default_link', 'addFAQ');
        } else if ($table == "user_right") {
            $this->session->set('default_link', 'assignRights');
        } else if ($table == "access_level") {
            $this->session->set('default_link', 'addAccessLevel');
        } else if ($table == "facilities") {
            $this->session->set('default_link', 'addFacility');
        }
    }

    function chartBuilder() {
        
    }

    public function getSystemUsageBackup($period = '') {
        $dataArray = [];
        $total_series = [];
        $results = Access_level::orderBy('id')->get()->toArray();
        $count = 1;
        foreach ($results as $result) {
            $access_level = $result['id'];
            $level = $result['level_name'];
            $sql = "SELECT acl.id AS access, acl.level_name, COUNT(*) AS total FROM access_log al,access_level acl  WHERE DATEDIFF(CURDATE(),al.start_time) <=  '$period' AND acl.id = al.access_level AND al.access_level='$access_level'";
            $results = DB::select($sql);
            if ($results) {
                foreach ($results as $result) {
                    $total = $result->total;
                    $dataArray[] = (int) $total;
                }
            }
            $series = ['name' => "Usage", 'data' => $dataArray];
        }
        $total_series[] = $series;

        $access_levels = Access_level::where('active', 1)->get();
        foreach ($access_levels as $access_level) {
            $columns[] = $access_level->level_name;
        }
        $resultArray = json_encode($total_series);
        $categories = json_encode($columns);
        $resultArraySize = 0;
        $data['resultArraySize'] = $resultArraySize;
        $data['container'] = 'chart_expiry';
        $data['chartType'] = 'bar';
        $data['title'] = 'Chart';
        $data['chartTitle'] = 'System Usage Summary';
        $data['categories'] = $categories;
        $data['yAxix'] = 'No. Of Times';
        $data['resultArray'] = $resultArray;
        print_r($data);
        // $this->load->view('chart_v', $data);
    }

    public function getSystemUsage($period = '', $access_level = '') {
        $query = "SELECT acl.level_name category, COUNT(acl.id) number ".
                    "FROM access_level acl ".
                    "LEFT JOIN access_log aclog ON aclog.access_level = acl.id ".
                    "WHERE DATEDIFF(CURDATE(),aclog.start_time) <=  '".$period."' ".
                    "GROUP BY acl.level_name ".
                    "ORDER BY number DESC;";


        $chartTitle = 'System Usage Summary';
        $yaxis = 'No. of Times';
        $this->getChartBuilder($query, $chartTitle, $yaxis);
    }

    public function drillAccessLevel() {
        $access_level = $_POST['level'];
        $period = $_POST['period'];

        if ($access_level === 'System Administrator') {
            $level_id = '1';
        } else if ($access_level === 'Pharmacist') {
            $level_id = '2';
        } else if ($access_level === 'Facility Administrator') {
            $level_id = '3';
        } else {
            $level_id = '1';
        }
        $query = "SELECT u.Username category, COUNT(aclog.id) number ".
                    "FROM users u ".
                    "LEFT JOIN access_log aclog ON aclog.user_id = u.id ".
                    "WHERE aclog.access_level='".$level_id."' AND DATEDIFF(CURDATE(),aclog.start_time) <=  '".$period."' ".
                    "GROUP BY u.Name ".
                    "ORDER BY number DESC;";


        $chartTitle = $access_level . ' System Usage Summary';
        $yaxis = 'No. of Times';
        $this->getChartBuilder($query, $chartTitle, $yaxis);
    }

    function getdataByUser() {

        $user = $_POST['user'];
        $period = $_POST['period'];

        $query = "SELECT activity category, count(activity) number  ".
                    "FROM vw_workload ".
                    "WHERE operator='".$user."' ". 
                    "AND DATEDIFF(CURDATE(),transaction_date) <=  '".$period."' ".
                    "GROUP BY activity";
        $chartTitle = "System Usage Summary";
        $yaxis = 'No. of Times';
        $this->getChartBuilder($query, $chartTitle, $yaxis);
    }

    function inventory($user, $period) {
        $query = DB::select("SELECT dsm.id,dru.drug, dsm.batch_number,dsm.transaction_date,dsm.source,dsm.destination FROM drug_stock_movement dsm LEFT JOIN drugcode dru ON dsm.drug = dru.id WHERE DATEDIFF(CURDATE(),dsm.transaction_date) <= '$period' ORDER BY dsm.id DESC ");
    }

    function dispensement1($user, $period) {
        $query = DB::select("SELECT * ".
                    "FROM `patient_visit` ".
                    "WHERE `user`='$user' AND DATEDIFF(CURDATE(),dispensing_date) <= '".$period."'");
    }

    function getChartBuilder($query, $chartTitle, $yaxis) {

        $categories = [];
        $datas = [];

        $toplevel = $this->query($query);
        foreach ($toplevel as $c):
            $categories[] = $c->category;
            $datas[] = $c->number;
        endforeach;

        $series = ['name' => "Usage", 'data' => array_map('intval', $datas)];
        $series_array[] = $series;
        $resultArraySize = 0;
        $data['resultArraySize'] = $resultArraySize;
        $data['container'] = 'chart_expiry';
        $data['chartType'] = 'bar';
        $data['title'] = 'Chart';
        $data['chartTitle'] = $chartTitle;
        $data['categories'] = json_encode($categories);
        $data['yAxix'] = $yaxis;
        $data['resultArray'] = json_encode($series_array);

        //print_r($data);
        echo view('\Modules\ADT\Views\chart_v', $data);
    }

    function query($query) {
        return DB::select($query);
    }

    public function getWeeklySumary($startdate = '', $enddate = '') {
        $dataArray = [];
        $total_series = [];
        $timestamp = time();
        $edate = date('Y-m-d', $timestamp);
        $series = [];
        $dates = [];
        $columns = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $x = 6;
        $y = 0;
        if ($startdate == "" || $enddate == "") {
            for ($i = 0; $i < $x; $i++) {
                if (date("D", $timestamp) != "Sun") {
                    $sdate = date('Y-m-d', $timestamp);
                    //Store the days in an array
                    $dates[$y] = $sdate;
                    $y++;
                }
                //If sunday is included, add one more day
                else {
                    $x = 8;
                }
                $timestamp += 24 * 3600;
            }
            $start_date = $sdate;
            $end_date = $edate;
        } else {
            $startdate = strtotime($startdate);
            for ($i = 0; $i < $x; $i++) {
                if (date("D", $startdate) != "Sun") {
                    $sdate = date('Y-m-d', $startdate);
                    //Store the days in an array

                    $dates[$y] = $sdate;
                    $y++;
                }
                //If sunday is included, add one more day
                else {
                    $x = 8;
                }
                $startdate += 24 * 3600;
            }
            $start_date = $startdate;
            $end_date = $enddate;
        }
        
        foreach ($dates as $date_period) {
            $sql = "SELECT count(*) as total FROM access_log WHERE DATE_FORMAT(start_time,'%Y-%m-%d')='$date_period' ORDER BY start_time ";
            $results = DB::select($sql);
            foreach ($results as $value) {
                $total = $value->total;
                $dataArray[] = (int) $total;
            }
            $series = ['name' => "Summary", 'data' => $dataArray];
        }
        $total_series[] = $series;

        $resultArray = json_encode($total_series);
        $categories = json_encode($columns);
        $resultArraySize = 0;
        $data['resultArraySize'] = $resultArraySize;
        $data['container'] = 'chart_enrollment';
        $data['chartType'] = 'bar';
        $data['title'] = 'Chart';
        $data['chartTitle'] = 'Weekly System Access Summary';
        $data['categories'] = $categories;
        $data['yAxix'] = 'Access Total';
        $data['resultArray'] = $resultArray;
        echo view('\Modules\ADT\Views\chart_v_1', $data);
    }

    public function getWeeklySumaryPerUser() {
        $categories = [];
        $datas = [];
        $period = $this->post('start');
        $date = date_create($period);
        $periodf = date_format($date, "Y-m-d");
        $day = $this->post('day');

        $results = DB::select("SELECT u.Username category,count(aclog.user_id) number,aclog.user_id,aclog.access_level,aclog.start_time, DATE_FORMAT(aclog.start_time,'%d-%b-%Y') startdate, DAYNAME(aclog.start_time) day,TIMESTAMPDIFF(MINUTE, aclog.start_time, aclog.end_time) howlong FROM access_log aclog LEFT JOIN users u ON aclog.user_id = u.id WHERE aclog.start_time BETWEEN '$periodf' AND DATE_ADD('$periodf', INTERVAL 5 DAY) AND DAYNAME(aclog.start_time)='$day' GROUP BY aclog.user_id,day ORDER BY aclog.start_time ASC ");

        foreach ($results as $c):
            $categories[] = $c->category;
            $datas[] = $c->number;
        endforeach;

        $series = ['name' => "Usage", 'data' => array_map('intval', $datas)];
        $series_array[] = $series;

        $resultArraySize = 0;
        $data['resultArraySize'] = $resultArraySize;
        $data['container'] = 'chart_enrollment';
        $data['chartType'] = 'bar';
        $data['title'] = 'Chart';
        $data['chartTitle'] = 'System Access Summary for ' . $day;
        $data['categories'] = json_encode($categories);
        $data['yAxix'] = 'Access Total';
        $data['resultArray'] = json_encode($series_array);
        //print_r($data);
        echo view('\Modules\ADT\Views\chart_v_1', $data);
    }

    function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = ['year', 'month', 'day', 'hour', 'minute', 'second'];
        $diffs = [];

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = [];
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }

    public function base_params($data) {
        $data['content_view'] = "\Modules\ADT\Views\admin\add_param_a";
        $data['title'] = "webADT | System Admin";
        $data['banner_text'] = "System Admin";
        echo view('\Modules\ADT\Views\admin\admin_template', $data);
    }

}
