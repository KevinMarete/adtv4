<?php
namespace Modules\ADT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class User_right extends Model {


    public static function getRights($access_level) {
        $query = DB::select("SELECT m.*, u.id , u.access_level, u.menu , u.access_type, u.active FROM user_right u LEFT JOIN menu m ON m.id = u.menu WHERE (u.access_level = '$access_level' AND u.active = '1')");
        return $query;
    }

}
