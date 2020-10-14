<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Patient extends BaseModel {
    protected $table = 'patient';
    protected $appends = ['name','full_name','phone_number'];
    protected $guarded = ['id'];

    public function district(){
        return $this->belongsTo(District::class, 'pob', 'id');
    }

    public function gender(){
        return $this->belongsTo(Gender::class, 'gender', 'id');
    }

    public function patient_source(){
        return $this->belongsTo(PatientSource::class, 'source', 'id');
    }

    public function supporter(){
        return $this->belongsTo(Supporter::class, 'pob', 'id');
    }

    public function service(){
        return $this->belongsTo(RegimenServiceType::class, 'service', 'id');
    }

    public function regimen(){
        return $this->belongsTo(Regimen::class, 'start_regimen', 'id');
    }

    public function current_regimen(){
        return $this->belongsTo(Regimen::class, 'current_regimen', 'id');
    }

    public function current_status(){
        return $this->belongsTo(PatientStatus::class, 'current_status', 'id');
    }

    public function facilities(){
        return $this->belongsTo(Facilities::class, 'transfer_from', 'facilitycode');
    }

    public function pep_reason(){
        return $this->belongsTo(PepReason::class, 'pep_reason', 'id');
    }

    public function who_stage(){
        return $this->belongsTo(WhoStage::class, 'who_stage', 'id');
    }

    public function dependant(){
        return $this->belongsTo(Dependant::class, 'patient_number_ccc', 'child');
    }

    public function spouse(){
        return $this->belongsTo(Spouse::class, 'primary_spouse', 'id');
    }

    public function getNameAttribute(){
        return $this->first_name.' '.$this->last_name.' '.$this->other_name;
    }

    public function getFullNameAttribute(){
        return $this->first_name.' '.$this->other_name.' '.$this->last_name.' '.$this->other_name;
    }

    public function getPhoneNumberAttribute(){
        $number = '';
        if(empty($this->phone)) $number = $this->alternate;
        else $number = $this->phone;
        return $number;
    }

}
