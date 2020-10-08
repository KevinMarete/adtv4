<?php

class Migration_log extends Doctrine_Record {

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
        $query = DB::select("SELECT * FROM migration_log WHERE source='$source'");
        $result = array_map(function ($query) {
            return (array) $query;
        }, $result);
        return $result[0];
    }

}

?>