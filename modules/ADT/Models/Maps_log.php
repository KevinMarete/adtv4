<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class Maps_log extends BaseModel {
    protected $table = 'maps_log';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function maps(){
        return $this->belongsTo(Maps::class, 'maps_id');
    }
}
