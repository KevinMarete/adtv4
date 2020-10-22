<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Menu extends BaseModel {

    protected $table = 'menu';
    protected $fillable = ['Menu_Text', 'Menu_Url', 'Description', 'Offline', 'active'];

    public static function getAllHydrated() {
        $query = Doctrine_Query::create()->select("Menu_Text, Menu_Url, Description")->from("Menu");
        $menus = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $menus;
    }

    public static function getAll() {
        $query = DB::table("menu")->get()->toArray();
        return BaseModel::resultSet($query);
    }

    public function getAllActive() {
        $query = Doctrine_Query::create()->select("*")->from("menu")->where("active='1'");
        $menus = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $menus;
    }

}
