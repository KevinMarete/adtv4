<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;


class CCC_store_service_point extends BaseModel {

    protected $table = 'ccc_store_service_point';

    public function getAll() {
        $query = Doctrine_Query::create()->select("*")->from("ccc_store_service_point");
        $stores = $query->execute();
        return $stores;
    }

    public  static function getAllActive() {
        $query = DB::table('ccc_store_service_point')->where('active', '1')->get();
        return $query;
    }

    public function getActive() {
        $query = Doctrine_Query::create()->select("*")->from("ccc_store_service_point")->where("Active", "1");
        $stores = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $stores;
    }

    public static function getAllBut($ccc_id) {
        $query = Doctrine_Query::create()->select("*")->from("ccc_store_service_point")->where("Active = 1 AND id!=$ccc_id")->orderBy("id ASC");
        $stores = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $stores;
    }

    public static function getCCC($id) {
        $query = Doctrine_Query::create()->select("*")->from("ccc_store_service_point")->where("id = '$id' and Active='1' ");
        $ois = $query->execute();
        return $ois[0];
    }

    public function getStoreGroups() {
        $query = Doctrine_Query::create()->select("id,Name as name")->from("ccc_store_service_point")->where("Name LIKE '%store%' and Active='1'");
        $category['Store'] = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        $query = Doctrine_Query::create()->select("id,Name as name")->from("ccc_store_service_point")->where("Name LIKE '%pharm%' and Active='1'");
        $category['Pharmacy'] = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $category;
    }

}
