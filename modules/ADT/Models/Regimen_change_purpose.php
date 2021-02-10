<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Regimen_change_purpose extends BaseModel {

    protected $table = 'regimen_change_purpose';
    protected $fillable = array('name', 'active');

    public function getAll() {
        $query = Doctrine_Query::create()->select("*")->from("Regimen_Change_Purpose")->where("Active", "1");
        $purposes = $query->execute();
        return $purposes;
    }

    public static function getAllHydrated() {
        return BaseModel::resultSet(DB::table('Regimen_Change_Purpose')->where("active", "1")->get());
    }

    public function getTotalNumber() {
        $query = Doctrine_Query::create()->select("count(*) as Total_Purposes")->from("Regimen_Change_Purpose");
        $total = $query->execute();
        return $total[0]['Total_Purposes'];
    }

    public function getPagedPurposes($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("regimen_change_purpose")->offset($offset)->limit($items);
        $purpose = $query->execute();
        return $purpose;
    }

    public static function getThemAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table('regimen_change_purpose')->get();
        } else {
            $query = DB::table('regimen_change_purpose')->where('active', '1')->get();
        }
        return $query;
    }

    public static function getSource($id) {
        $query = DB::table('regimen_change_purpose')->where('id', $id)->get();
        return $query[0];
    }

}

?>