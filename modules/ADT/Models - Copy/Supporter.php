<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Supporter extends BaseModel {

    protected $table = 'supporter';
    protected $fillable = array('name', 'active');

    public static function getAll() {
        $query = DB::table('supporter')->get();
        return $query;
    }

    public function getAllActive() {
        $query = DB::table('supporter')->where('active', '1')->get();
        return BaseModel::resultSet($query);
    }

    public function getThemAll() {
        $query = DB::table('supporter')->get();
        return BaseModel::resultSet($query);
    }

    public function getTotalNumber() {

        $query = DB::select("SELECT count(*) as Total_Supporters FROM supporter");
        return $query[0]->Total_Supporters;
    }

    public function getPagedSupporters($offset, $items) {
        $query = Doctrine_Query::create()->select("Name,Active")->from("supporter")->where("Active='1'")->offset($offset)->limit($items);
        $supporters = $query->execute();
        return $supporters;
    }

    public function getSource($id) {
        $query = DB::table('supporter')->where('id', $id)->get();
        return $query[0];
    }

}
