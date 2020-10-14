<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class VisitPurpose extends BaseModel {
    protected $table = 'visit_purpose';
    protected $guarded = ['id'];

    public function getAll($service = null){
        $purposes = VisitPurpose::where('active', '1')->get();
        $prep_purposes = [];
        if(strtolower($service) == 'prep'){
            foreach($purposes as $purpose){
				if($purpose->name == 'Start' || $purpose->name == 'Routine Refill' || $purpose->name == 'Restart'){
					$prep_purposes[] = $purpose;
				}
            }
            $purposes = $prep_purposes;
        }
        return $purposes;
    }

}
