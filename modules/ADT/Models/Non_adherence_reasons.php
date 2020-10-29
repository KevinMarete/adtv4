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
        return BaseModel::resultSet(DB::table('non_adherence_reasons')->where("Active", "1")->get());
    }

    public static function getThemAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table('non_adherence_reasons')->get();
        } else {
            $query = DB::table('non_adherence_reasons')->where("Active", "1")->get();
        }
        return $query;
    }

    public static function getSource($id) {
        $query = DB::table('non_adherence_reasons')->where("id", $id)->get();
        return $query[0];
    }

    public function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_Purposes FROM non_adherence_reasons");
        return $query[0]->Total_Purposes;
    }

    public function getPagedPurposes($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("non_adherence_reasons")->offset($offset)->limit($items);
        $purpose = $query->execute();
        return $purpose;
    }

}

?>