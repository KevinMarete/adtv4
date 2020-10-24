<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Non_adherence_reasons extends BaseModel {

    protected $table = 'non_adherence_reasons';
    protected $fillable = array('Name', 'Active');

    public static function getAll() {
        $query = DB::table('Non_Adherence_Reasons')->where("Active", "1")->get();
        return $query;
    }

    public static function getAllHydrated() {
        return BaseModel::resultSet(DB::table('Non_Adherence_Reasons')->where("Active", "1")->get());
    }

    public static function getThemAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table('Non_Adherence_Reasons')->get();
        } else {
            $query = DB::table('Non_Adherence_Reasons')->where("Active", "1")->get();
        }
        return $query;
    }

    public static function getSource($id) {
        $query = DB::table('Non_Adherence_Reasons')->where("id", $id)->get();
        return $query[0];
    }

    public function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_Purposes FROM Non_Adherence_Reasons");
        return $query[0]->Total_Purposes;
    }

    public function getPagedPurposes($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("Non_Adherence_Reasons")->offset($offset)->limit($items);
        $purpose = $query->execute();
        return $purpose;
    }

}

?>