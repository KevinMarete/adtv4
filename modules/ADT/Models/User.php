<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class User extends BaseModel {

//protected $table = 'users';

    protected function _encrypt_password($value) {
        $this->_set('Password', md5($value));
    }
    
    public function access(){
        return $this->belongsTo(AccessLevel::class, 'Access_Level', 'id');
    }

    //added by dave
    public function getAccessLevels() {
        $levelquery = DB::select("id,access,level FROM access_level");
        return $levelquery;
    }

    public  static function getRights($access_level) {
        $query = DB::select("SELECT u.id , u.access_level, u.menu , u.access_typee, u.active FROM user_right u WHERE (u.access_level = '$access_level' AND u.active = '1')'");
        return $query;
    }

    //facilities...
    public function getFacilityData() {
        $facilityquery = DB::select("facilitycode,name FROM facilities");
        return $facilityquery;
    }

    //get all users
    public static function getAll() {
        $query = DB::select("SELECT u.id AS id, u.name , u.username , u.email_address , u.phone_number , a.level_name, u2.name AS u2name, u.active AS uactive FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id");
        return $query;
    }

    public function getSpecific($user_id) {
        $query = DB::select("SELECT u.id, u.name, u.username, u.email_address, u.phone_number, a.level_name, u2.name AS u2name, u.active AS uactive FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id WHERE u.id = '$user_id'");
        return $query;
    }

    public function getThem() {
        $query = DB::select("SELECT u.id, u.name, u.username, u.email_address, u.phone_number, a.level_name, u2.name AS u2name, u.active AS uactive FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id WHERE  a.level_name != 'Pharmacist'");
        echo $query;
    }

    public function getInactive($facility_code) {
        $query = DB::select("SELECT u.id, u.name, u.username, u.email_address, u.phone_number, a.level_name, u2.name AS u2name, u.active AS uactive FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id WHERE  a.level_name !='Pharmacist' and facility_fode=" . $facility_code . " and u.active='0'");
        return $query;
    }

    public function getOnline($facility_code) {
        $query = DB::select("SELECT u.id, u.name, u.username, u.email_address, u.phone_number, a.level_name, u2.name AS u2name, u.active AS uactive FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id WHERE  a.level_name !='Pharmacist' and facility_fode=" . $facility_code . " and u.active='0'");
        return $query;
    }

    public static function getUser($id) {
        $query = Users::find($id)->first();
        return $query;
    }

    public static function getUserAdmin($id) {
        $query = Users::find($id)->first();
        return $query;
    }

    public static function getUserDetail($id) {
        $query = Users::find($id) - first();
        return $query;
    }

    public static function getUserID($username) {
        $query = DB::select("SELECT u.id , u.name , u.username , u.password, u.access_level, u.facility_code, u.created_by , u.time_created , u.phone_number , u.ccc_store_sp , u.email_address , u.active AS uactive, u.signature, u.map  FROM users u WHERE (u.username = '$username' OR u.email_address = '$username' OR u.phone_number = '$username')");
        return $query[0]['id'];
    }

    public function getUsersFacility($q = '1') {
        $query = DB::select("SELECT u.id , u.name e, u.username, u.email_address , u.phone_number, a.level_name, a.indicator, u2.name AS u2naME, u.active AS uactive FROM users u LEFT JOIN access_level a ON u.access_level = a.id LEFT JOIN users u2 ON u.created_by = u2.id WHERE $q");
        return $query;
    }

    public function getNotificationUsers() {
        $query = DB::select("SELECT Distinct(u.email_address) AS email_address FROM users u LEFT JOIN access_level a ON u.access_level = a.id WHERE (a.level_name LIKE '%facility%' OR a.level_name LIKE '%pharmacist%')");
        return $query;
    }

    public function get_email_account($email_address) {
        $query = Users::where("email_address", $email_address)->get();
        return $query;
    }

}
