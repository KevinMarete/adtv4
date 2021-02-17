<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class District extends BaseModel {

    protected $table = 'district';

    public static function getAll() {
        $query = DB::table("district")->get()->toArray();
        return BaseModel::resultSet($query);
    }

    public static function getID($name) {
		$district = District::where('name', 'like', '%'.$name.'%')->first();
		return $district->id ?? 0;
	}

}
