<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Opportunistic_infection extends BaseModel {

    protected $table = 'opportunistic_infection';
    protected $fillable = array('Name', 'Indication', 'Active');

    public function getAll() {
        $query = DB::table('Opportunistic_Infection')->where('active', '1')->get();
        return $query;
    }

    public function getAllHydrated() {
        $query = DB::table('Opportunistic_Infection')->where('active', '1')->get();
        return BaseModel::resultSet($query);
    }

    public function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_OIs FROM opportunistic_infection");
        return $query[0]->Total_OIs;
    }

    public function getPagedOIs($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("opportunistic_infection")->offset($offset)->limit($items);
        $ois = $query->execute();
        return $ois;
    }

    public static function getIndication($id) {
        $query = DB::table('opportunistic_infection')->where('id', $id)->get();
        return $query[0];
    }

    public static function getThemAll($access_level = "") {
        if ($access_level = "" || $access_level == "facility_administrator") {
            $query = DB::table('opportunistic_infection')->get();
        } else {
            $query = DB::table('opportunistic_infection')->where('active', '1')->get();
        }
        return $query;
    }

}
