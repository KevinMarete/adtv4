<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Drug_unit extends BaseModel {

    protected $table = 'drug_unit';
    protected $fillable = array('name');

    public function getAll() {
        $query = DB::table('drug_unit')->get();
        return $query;
    }

    public function getThemAll() {
        $query = DB::table('drug_unit')->get();
        return BaseModel::resultSet($query);
    }

    public function getAllActive() {
        $query = Doctrine_Query::create()->select("*")->from("drug_unit");
        $drugunits = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $drugunits;
    }

    public function getUnit($unit_id) {
        $query = DB::table('drug_unit')->where('id', $unit_id)->get();
        return BaseModel::resultSet($query);
    }

    public function getTotalNumber() {
        $query = DB::select("SELECT count(*) as Total_Units FROM Drug_Unit");
        return $query[0]->Total_Units;
    }

    public function getPagedDrugUnits($offset, $items) {
        $query = Doctrine_Query::create()->select("*")->from("Drug_Unit")->offset($offset)->limit($items);
        $drug_units = $query->execute();
        return $drug_units;
    }

}
