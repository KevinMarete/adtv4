<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class DrugPrescriptionDetails extends BaseModel {
    protected $table = 'drug_prescription_details';
    protected $guarded = ['id'];

    function drug_prescription() {
        return $this->hasOne(DrugPrescription::class, 'id', 'drug_prescriptionid');
    }

}
