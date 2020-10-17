<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class PatientPrepTest extends BaseModel {
    protected $table = 'patient_prep_test';
    protected $fillable = ['patient_id','prep_reason_id','is_tested','test_date','test_result'];
    protected $guarded = [];

}
