<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Suppliers extends BaseModel {

    protected $table = 'suppliers';
    protected $fillable = array('name');

    public function getAll() {
        $query = DB::table('suppliers')->get();
        return BaseModel::resultSet($query);
    }

}
