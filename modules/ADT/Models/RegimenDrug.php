<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class RegimenDrug extends BaseModel {
    protected $table = 'regimen_drug';
    protected $guarded = ['id'];

    public function regimen(){
        return $this->belongsTo(Regimen::class, 'regimen', 'id');
    }

}
