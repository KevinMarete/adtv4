<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Drug_classification extends BaseModel {

    protected $table = 'drug_classification';
    protected $fillable = array('name', 'active');

    public function getAll() {
        $query = Doctrine_Query::create()->select("Name")->from("drug_classification");
        $drugcodes = $query->execute();
        return $drugcodes;
    }

    public static function getAllHydrated($access_level = "", $get_active = "1") {
        if (($access_level = "" || $access_level == "facility_administrator") && $get_active == "1") {
            $query = DB::table('drug_classification')->get();
        } else {
            $query = DB::table('drug_classification')->where('active', '1')->get();
        }
        return BaseModel::resultSet($query);
    }

    public static function getClassification($id) {
        $query = Doctrine_Query::create()->select("*")->from("drug_classification")->where("id = '$id'");
        $drugcodes = $query->execute();
        return $drugcodes[0];
    }

    public function getAllActive() {
        $query = Doctrine_Query::create()->select("Id,Name")->from("drug_classification")->where("Active=1");
        $drugcodes = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $drugcodes;
    }

}
