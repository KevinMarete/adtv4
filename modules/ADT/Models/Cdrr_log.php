<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class Cdrr_log extends BaseModel {
    protected $table = 'cdrr_log';
    protected $guarded = ['id'];

    public function cdrr(){
        return $this->belongsTo(Cdrr::class, 'cdrr_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
