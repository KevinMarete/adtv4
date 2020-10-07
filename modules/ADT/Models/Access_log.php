<?php

namespace Modules\ADT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Access_log extends Model {

    protected $table = 'access_log';
    protected $fillable = array('machine_code', 'user_id', 'access_level', 'start_time', 'end_time', 'facility_code', 'access_type','updated_at');

    public function getAll() {
        $query = DB::select("SELECT * FROM access_log");
        return $query;
    }

    public static function getLastUser($user_id) {
        $query = DB::select(" SELECT id FROM access_log WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
        return $query[0]->id;
    }

}
