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

    public static function getAllActive() {
        $query = DB::table('ccc_store_service_point')->where('active', '1')->get();
        return $query;
    }

    public static function getActive() {
        $query = DB::table('ccc_store_service_point')->where('active', '1')->get();
        return $query;
    }

    public static function getAllBut($ccc_id) {
        $query = Doctrine_Query::create()->select("*")->from("ccc_store_service_point")->where("Active = 1 AND id!=$ccc_id")->orderBy("id ASC");
        $stores = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $stores;
    }

    public static function getCCC($id) {
        $query = DB::table('ccc_store_service_point')->where('id', $id)->where('active', '1')->get();
        return json_decode(json_encode($query), true)[0];
    }

    public static function getStoreGroups() {
        $categories = [];
        $categories['Store'] = CCC_store_service_point::where('name', 'like', '%store%')->where('active', '1')->get()->toArray();
        $categories['Pharmacy'] = CCC_store_service_point::where('name', 'like', '%pharm%')->where('active', '1')->get()->toArray();
        return $categories;
    }

}
