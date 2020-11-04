<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class FacilityType extends BaseModel {
    protected $table = 'facility_types';
    protected $guarded = ['id'];


    public static function getTypeID($name) {
        $type = FacilityType::where('Name', 'like', '%'.$name.'%')->first();
		return $type->id ?? 0;
	}

}
