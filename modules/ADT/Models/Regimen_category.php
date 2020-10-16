<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\Regimen;
use Illuminate\Database\Capsule\Manager as DB;

class Regimen_Category extends BaseModel {

    protected $table = 'regimen_category';
    protected $fillable = array('Name', 'Active');

    public function regimen() {
        $this->hasMany(Regimen::class, 'id', 'category');
    }

    public static function getAll() {
        $query = DB::table('Regimen_Category')->where("Active", "1")->orderBy("Name", "asc")->get();
        return $query;
    }

    public static function getAllHydrate() {
        $query = DB::table('Regimen_Category')->where("Active", "1")->get();
        return BaseModel::resultSet($query);
    }

}

