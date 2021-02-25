<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class CdrrItem extends BaseModel {
    protected $table = 'cdrr_item';
    protected $guarded = ['id'];

    public function dhis_element(){
        return $this->belongsTo(DhisElements::class, 'drug_id', 'target_id');
    }

    public function cdrr(){
        return $this->belongsTo(Cdrr::class, 'cdrr_id', 'id');
    }

    public function sync_drug(){
        return $this->belongsTo(Sync_drug::class, 'drug_id  ', 'id');
    }

}
