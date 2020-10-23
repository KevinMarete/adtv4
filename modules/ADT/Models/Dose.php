<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use \Modules\ADT\Models\Drugcode;
use Illuminate\Database\Capsule\Manager as DB;

class Dose extends BaseModel {

    protected $table = 'dose';
    protected $guarded = ['id'];

    public static function getAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table('dose')->get();
        } else {
            $query = DB::table('dose')->where('active', '1')->get();
        }
        return $query;
    }

    public function getAllActive() {
        $query = DB::table('Dose')->where('active', '1')->orderBy('name', 'asc')->get();
        return BaseModel::resultSet($query);
    }

    public function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_Doses FROM Dose");
        return $query[0]->Total_Doses;
    }

    public function getPagedDoses($offset, $items) {
        $query = Doctrine_Query::create()->select("*")->from("Dose")->offset($offset)->limit($items);
        $doses = $query->execute();
        return $doses;
    }

    public static function getDose($id) {
        $query = DB::table('Dose')->where('id', $id)->get();
        return $query[0];
    }

    public static function getDoseHydrated($id) {
        $query = DB::table('Dose')->where('id', $id)->get();
        return BaseModel::resultSet($query);
    }

    public function getDoseLabel($name) {
        $query = DB::table('Dose')->where('name', $name)->get();
        return $query[0];
    }

}
