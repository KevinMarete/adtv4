<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\Sync_regimen_category;
use Illuminate\Database\Capsule\Manager as DB;

class Sync_regimen extends BaseModel {

    protected $table = 'sync_regimen';
    protected $fillable = array('name', 'code', 'old_code', 'description', 'category_id', 'Active');

    public static function Sync_Regimen_Category() {
        $this->hasOne('Sync_Regimen_Category', array('category_id', 'id'));
    }

    public static function getAll() {
        return DB::table('sync_regimen')->get();
    }

    public static function getActive() {
        $db = \Config\Database::connect();
        /* $query = Doctrine_Query::create() -> select("sr.id,sr.code,sr.name,sr.category_id, sr.Sync_Regimen_Category.Name as category_name") -> from("sync_regimen sr") -> where("sr.Active = '1'")-> orderBy("category_id, code asc");
          $sync_regimen = $query -> execute(array(), Doctrine::HYDRATE_ARRAY); */

        $sql = "SELECT sr.id,sr.code,sr.name,sr.category_id, src.Name as category_name
				FROM sync_regimen sr
				LEFT JOIN sync_regimen_category src ON src.id = sr.category_id
				WHERE sr.Active = '1'
				AND src.Active = '1'
				ORDER BY category_id,code asc";
        $sync_regimen = $db->query($sql)->getResultArray();
        return $sync_regimen;
    }

    public static function getId($regimen_code) {
        $query = DB::select("SELECT id FROM sync_regimen WHERE code like '%$regimen_code%'");
        return @$query[0]['id'];
    }

}

?>