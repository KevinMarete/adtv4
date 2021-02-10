<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Visit_purpose extends BaseModel {

    protected $table = 'visit_purpose';
    protected $fillable = array('Name', 'Active');

    public static function getAll($service = 'null') {
        $purposes=[];
        $purposes = Visit_purpose::where("Active", "1")->get()->toArray();
        if ($service == 'prep') {
            $prep_purposes = [];
            foreach ($purposes as $purpose) {
                if ($purpose['name'] == 'Start' || $purpose['name'] == 'Routine Refill' || $purpose['name'] == 'Restart') {
                    $prep_purposes[] = $purpose;
                }
            }
            $purposes = $prep_purposes;
        }
        return $purposes;
    }

    public function getActive() {
        $query = DB::table('visit_purpose')->where("active", "1")->get()->toArray();
        return $query;
    }

    public function getFiltered($enrollment_check, $start_art_check) {
        $filter = "";
        if ($enrollment_check == 1) {
            $filter .= " AND Name NOT LIKE '%enroll%'";
        }

        if ($start_art_check == 1) {
            $filter .= " AND Name NOT LIKE '%startart%'";
        }
        $query = DB::select(" SELECT * FROM visit_purpose WHERE active='1' $filter ");
        return $query;
    }

    public function getThemAll() {
        $query = DB::table('visit_purpose')->orderBy("name", "asc")->get();
        return $query;
    }

    public function getTotalNumber() {
        $query = DB::select(" SELECT count(*) as Total_Purposes FROM visit_purpose");
        return $query[0]->Total_Purposes;
    }

    public function getPagedPurposes($offset, $items) {
        $query = Doctrine_Query::create()->select("Name")->from("visit_purpose")->offset($offset)->limit($items);
        $purposes = $query->execute();
        return $purposes;
    }

    public static function getSource($id) {
        $query = DB::table('visit_purpose')->where("id", $id)->get();
        return $query[0];
    }

}

?>