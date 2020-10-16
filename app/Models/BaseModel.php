<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    public $timestamps = false;

    public static function resultSet($query) {
        return json_decode(json_encode($query), true);
    }

}
