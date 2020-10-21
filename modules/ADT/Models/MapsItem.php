<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class MapsItem extends BaseModel {
    protected $table = 'maps_item';
    protected $guarded = ['id'];

    public function dhis_element(){
        return $this->belongsTo(SyncRegimen::class, 'regimen_id', 'target_id');
    }

}
