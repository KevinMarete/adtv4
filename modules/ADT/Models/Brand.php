<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use \Modules\ADT\Models\Drugcode;
use Illuminate\Database\Capsule\Manager as DB;

class Brand extends BaseModel {

    protected $table = 'brand';
    protected $fillable = array('Drug_Id', 'Brand');

    function Drugcode() {
        return $this->hasOne(Drugcode::class, 'drug_id');
       
    }

    public static function getAll() {
        $query = DB::table('brand')->orderBy('Drug_Id', 'desc')->get();
        return $query;
    }

    public static function getBrandName($id) {
        $query = DB::table('brand')->where('id', $id)->get();
        return $query[0];
    }

}
