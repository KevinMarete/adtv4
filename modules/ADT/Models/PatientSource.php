<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class PatientSource extends BaseModel {

    protected $table = 'patient_source';

    public function getSources() {
        return BaseModel::resultSet(DB::table('patient_source')->where("Active", "1")->get());
    }

}
