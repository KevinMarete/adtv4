<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;

class PatientSource extends BaseModel {

    protected $table = 'patient_source';

    public function getSources() {
        return BaseModel::resultSet(DB::table('Patient_Source')->where("Active", "1")->get());
    }

}
