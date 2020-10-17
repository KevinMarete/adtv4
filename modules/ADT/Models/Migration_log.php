<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Migration_log extends BaseModel {

    protected $table = 'migration_log';
    protected $fillable = array('source', 'last_index', 'count');

    public static function getAll() {
        $query = DB::table("migration_log")->get();
        return $query;
    }

    public static function getTargets() {
        $query = DB::select("select * from migration_log where source !='auto_update' and source !='patient_appointment'");
        return $query;
    }

    public static function getLog($source) {
       return BaseModel::resultSet(DB::select("SELECT * FROM migration_log WHERE source='$source'"))[0];
     
    }

}

