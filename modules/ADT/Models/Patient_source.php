<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Patient_Source extends BaseModel {

    protected $table = 'patient_source';
    protected $fillable = array('Name', 'Active');

    public function getAll() {
        $query = Doctrine_Query::create()->select("*")->from("patient_source")->where("active", "1");
        $sources = $query->execute();
        return $sources;
    }

    public function getSources() {
        $query = DB::table('patient_source')->where('active', '1')->get();
        return BaseModel::resultSet($query);
    }

    public function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_Sources FROM patient_source");
        return $query[0]->Total_Sources;
    }

    public function getPagedSources($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("Patient_Source")->offset($offset)->limit($items);
        $sources = $query->execute();
        return $sources;
    }

    public static function getThemAll() {
        $query = DB::table('patient_source')->get();
        return $query;
    }

    public static function getSource($id) {
        $query = DB::table('patient_source')->where('id', $id)->get();
        return $query[0];
    }

    public function getItems() {
        $query = DB::table('patient_source')->select('id', 'name')->where('active', '1')->orderby("name asc");
        return BaseModel::resultSet($query);
    }

}

?>