<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Drug_destination extends BaseModel {

    protected $table = 'drug_destination';
    protected $fillable = array('name', 'active');

    public static function getAll() {
        return DB::table("Drug_Source")->where("active", "1")->get();
    }

    public function getAllHydrate() {
        $query = Doctrine_Query::create()->select("*")->from("Drug_Destination")->where("Active=1")->orderBy("id ASC");
        //return $query->getSqlQuery();
        $destinations = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $destinations;
    }

    public function getTotalNumber() {
        $query = Doctrine_Query::create()->select("count(*) as Total_Destinations")->from("Drug_Destination")->where("Active", "1");
        $total = $query->execute();
        return $total[0]['Total_Destinations'];
    }

    public function getPagedSources($offset, $items) {
        $query = Doctrine_Query::create()->select("Name,Active")->from("Drug_Destination")->where("Active", "1")->offset($offset)->limit($items);
        $ois = $query->execute();
        return $ois;
    }

    public static function getSource($id) {
        $query = Doctrine_Query::create()->select("*")->from("Drug_Destination")->where("id = '$id'");
        $ois = $query->execute();
        return $ois[0];
    }

    public static function getThemAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {

            $query = DB::table("Drug_Destination")->get();
        } else {
            $query = DB::table("Drug_Destination")->where("Active", "1")->get();
        }
        return $query;
    }

}

?>