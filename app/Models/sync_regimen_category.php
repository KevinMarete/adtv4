<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\Sync_regimen_category;
use Illuminate\Database\Capsule\Manager as DB;

class Sync_regimen_category extends BaseModel {

    protected $table = 'sync_regimen_category';
    protected $fillable = array('Name', 'Active');

    public static function Sync_regimen() {
        $this->hasOne('Sync_Regimen_Category', array('id', 'category_id'));
    }

    public static function getAll() {
        $query = DB::table('sync_regimen_category')->select("sync_regimen_category")->where("Active", "1")->orderBy("Name asc")->get();
        return $query;
    }

    public static function getAllHydrate() {
        $query = DB::table('sync_regimen_category')->select("sync_regimen_category")->where("Active", "1")->orderBy("Name asc")->get();
        return BaseModel::resultSet($query);
    }

}

