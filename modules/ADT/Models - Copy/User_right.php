<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class User_right extends BaseModel {


    public static function getRights($access_level) {
        $query = DB::select("SELECT m.*, u.id , u.access_level, u.menu , u.access_type, u.active FROM user_right u LEFT JOIN menu m ON m.id = u.menu WHERE (u.access_level = '$access_level' AND u.active = '1')");
        return $query;
    }

}
