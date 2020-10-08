<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Transaction_type extends BaseModel {

    protected $table = 'transaction_type';
    protected $fillable = array('name', 'desc', 'effect', 'active');

    public static function getAll() {
        $query = DB::table("transaction_type")->where("active", '1')->get();
        return $query;
    }

    public static function getAllNonAdjustments() {
        $query = DB::select("SELECT * FROM transaction_type WHERE `desc` NOT LIKE '%adjust%' AND active 1");
        return $query;
    }

    public static function getTransactionType($filter, $effect) {
        $query = DB::select("SELET * FROM transaction_type WHERE name LIKE '%$filter%' AND effect='$effect'");
        return $query[0];
    }

    public static function getAllTypes() {
        $query = DB::select("SELET id,name,effect FROM transaction_type WHEREName LIKE '%received%' OR Name LIKE '%adjustment%' OR Name LIKE '%return%' OR Name LIKE '%dispense%' OR Name LIKE '%issue%' OR Name LIKE '%loss%' OR Name LIKE '%ajustment%' OR Name LIKE '%physical%count%' OR Name LIKE '%starting%stock%'");
        return $query;
    }

    public static function getEffect($id) {
        $query = DB::table("transaction_type")->where("id", $id)->get();
        return $query[0];
    }

}

?>
	