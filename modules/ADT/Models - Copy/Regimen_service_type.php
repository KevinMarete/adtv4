<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;

class Regimen_service_type extends BaseModel {

    protected $table = 'regimen_service_type';
    protected $fillable = array('Name', 'Active');

    public static function getAll() {
        return DB::table('Regimen_Service_Type')->where("Active", "1")->get();
    }

    public static function getHydratedAll() {
        $query = DB::table('Regimen_Service_Type')->where("Active", "1")->get();
        return BaseModel::resultSet($query);
    }

    public static function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_Types FROM Regimen_Service_Type");
        return BaseModel::resultSet($query)[0]['Total_Types'];
    }

    public function getPagedTypes($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("Regimen_Service_Type")->offset($offset)->limit($items);
        $types = $query->execute();
        return $types;
    }

    public function getItems() {
        $query = Doctrine_Query::create()->select("id,Name")->from("Regimen_Service_Type")->where("Active", "1")->orderBy("Name asc");
        $types = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $types;
    }

}

?>