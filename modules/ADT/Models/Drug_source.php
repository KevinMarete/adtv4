<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Drug_source extends BaseModel {

    protected $table = 'drug_source';
    protected $fillable = array('name', 'active');

    public static function getAll() {
        $res = DB::table("drug_source")->where("Active", "1")->get();
        return json_decode(json_encode($res), true);
    }

    public static function getAllHydrated() {
        $query = DB::table("drug_source")->where("active", '1')->orderBy("id", "ASC")->get();
        return BaseModel::resultSet($query);
    }

    public function getAllHydrate() {
        $query = Doctrine_Query::create()->select("*")->from("drug_source")->where("Active=1")->orderBy("id ASC");
        //return $query->getSqlQuery();
        $destinations = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $destinations;
    }

    public function getTotalNumber() {
        $query = Doctrine_Query::create()->select("count(*) as Total_Sources")->from("drug_source")->where("Active='1'");
        $total = $query->execute();
        return $total[0]['Total_Sources'];
    }

    public function getPagedSources($offset, $items) {
        $query = Doctrine_Query::create()->select("Name,Active")->from("drug_source")->where("Active='1'")->offset($offset)->limit($items);
        $ois = $query->execute();
        return $ois;
    }

    public static function getSource($id) {
        $query = DB::table("drug_source")->where("id", $id)->get();
        return $query[0];
    }

    public static function getThemAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table("drug_source")->get();
        } else {
            $query = DB::table("drug_source")->where("Active", "1")->get();
        }
        return $query;
    }

}

?>