<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Drug_instructions extends BaseModel {

    protected $table = 'drug_instructions';
    protected $fillable = array('name', 'active');

    public static function getAllInstructions() {
        $query = DB::table('drug_instructions')->where('active', '1')->get();
        return BaseModel::resultSet($query);
    }

}
