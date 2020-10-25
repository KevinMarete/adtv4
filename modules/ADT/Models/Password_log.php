<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;

class Password_log extends BaseModel {

    protected $table = 'password_log';
    protected $guarded = ['id'];

    public static function getAll() {
        $query = DB::table('password_log')->get();
        return $query;
    }

}
