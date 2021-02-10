<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;

class Access_level extends BaseModel {

    protected $table = 'access_level';
    protected $fillable = array('level_name', 'description', 'indicator');

    function users() {
        $this->hasMany(User::class, 'access_level', 'id');
    }

    public static function getAll($user_type = "1") {
        $query = DB::select("SELECT al.Id as Id,al.Level_Name as Access FROM access_level al WHERE $user_type");
        return json_decode(json_encode($query), true);
    }

    public static function getAllHydrated() {
        $query = Access_level::select(DB::raw("Id,Level_Name as Access"))->get();
        return $query;
    }

}
