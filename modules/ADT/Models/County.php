<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class County extends BaseModel {
    protected $table = 'counties';
    protected $guarded = ['id'];

    public static function getTypeID($name) {
        $county = County::where('county', 'like', '%'.$name.'%')->first();
		return $county->id ?? 0;
	}

}
