<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class Regimen extends BaseModel {
    protected $table = 'regimen';
    protected $appends = ['name'];

    public function getNameAttribute(){
        return $this->regimen_code.' | '.$this->regimen_desc;
    }

    public function regimen_category(){
        return $this->belongsTo(RegimenCategory::class, 'category', 'id');
    }

    public function regimen_service_type(){
        return $this->belongsTo(RegimenServiceType::class, 'type_of_service', 'id');
    }

    public function drugs(){
        return $this->hasMany(RegimenDrug::class, 'id', 'regimen');
    }

    public function sync_regimen(){
        return $this->belongsTo(SyncRegimen::class, 'map', 'id');
    }

}
