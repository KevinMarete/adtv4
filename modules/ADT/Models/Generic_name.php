<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Generic_name extends BaseModel {

    protected $table = 'generic_name';
    protected $fillable = array('name', 'active');

    public function getAll() {
        return DB::table("generic_name")->where("active", "1")->get();
    }

    public static function getAllHydrated($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table("generic_name")->get();
        } else {
            $query = DB::table("generic_name")->where("Active", "1")->get();
        }
        return BaseModel::resultSet($query);
    }

    public static function getGeneric($id) {
        return DB::table("generic_name")->where("id", $id)->get()[0];
    }

    public static function getGenericByName($name) {
        return DB::table("generic_name")->where("name", $name)->get()[0];
    }

    public function getAllActive() {
        return BaseModel::resultSet(DB::table("generic_name")->where("active", "1")->get());
    }

}
