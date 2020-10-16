<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Generic_name extends BaseModel {

    protected $table = 'generic_name';
    protected $fillable = array('name', 'active');

    public static function getAll() {
        $query = DB::table('generic_name')->get();
        return $query;
    }

    public static function getAllHydrated($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table('generic_name')->get();
        } else {
            $query = DB::table('generic_name')->where('active', '1')->get();
        }
        return BaseModel::resultSet($query);
    }

    public static function getGeneric($id) {
        $query = DB::table('generic_name')->where('id', $id)->get();
        return $query[0];
    }

    public static function getGenericByName($name) {
        $query = DB::table('generic_name')->where('name', $name)->get();
        return $query[0];
    }

    public function getAllActive() {
        $query = DB::table('generic_name')->where('active', '1')->get();
        $drugcodes = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return BaseModel::resultSet($query);
    }

}
