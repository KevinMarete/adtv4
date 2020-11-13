<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class DrugPrescription extends BaseModel {
    protected $table = 'drug_prescription';
    protected $guarded = ['id'];

    function drug_prescription_details() {
        return $this->hasMany(DrugPrescriptionDetails::class, 'drug_prescriptionid');
    }

}
