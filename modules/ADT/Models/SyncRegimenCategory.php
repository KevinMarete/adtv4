<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class SyncRegimenCategory extends BaseModel {
    protected $table = 'sync_regimen_category';
    protected $guarded = ['id'];
    protected $appends = ['regimens'];

    // public function regimens(){
    //     return $this->hasMany(SyncRegimen::class, 'id', 'category_id');
    // }

    public function getRegimensAttribute(){
        return SyncRegimen::where('category_id', $this->id)->get();
    }

}
