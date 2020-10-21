<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class SyncRegimenCategory extends BaseModel {
    protected $table = 'sync_regimen_category';
    protected $guarded = ['id'];
    protected $appends = ['regimens'];

    // public function regimens(){
    //     return $this->hasMany(Regimen::class, 'id', 'category');
    // }

    public function getRegimensAttribute(){
        return Regimen::where('category', $this->id)->get();
    }

}
