<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\User;
use \Modules\ADT\Models\Regimen_category;
use \Modules\ADT\Models\Regimen_Service_Type;
use \Modules\ADT\Models\Drugcode;
use \Modules\ADT\Models\Sync_Regimen;
use Illuminate\Database\Capsule\Manager as DB;

class Regimen_Drug extends BaseModel {

    protected $table = 'regimen_drug';
    protected $fillable = array('Regimen', 'Drugcode', 'Source', 'Active', 'Merged_From', 'Regimen_Merged_From');
    protected $with=['Drugcode'];

    public function Drugcode() {
        return $this->hasOne(Drugcode::class, 'id','drugcode');
    }

    public function getAll($source = 0, $access_level = "") {
        if ($access_level = "" || $access_level == "system_administrator") {
            $displayed_enabled = "";
        } else {
            $displayed_enabled = "AND Enabled='1'";
        }

        $query = Doctrine_Query::create()->select("*")->from("regimen_drug")->where('Source = "' . $source . '" or Source ="0"' . $displayed_enabled);
        $regimen_drugs = $query->execute();
        return $regimen_drugs;
    }

    public function getTotalNumber($source = 0) {
        $query = Doctrine_Query::create()->select("count(*) as Total_Regimen_Drugs")->from("Regimen_Drug")->where('Source = "' . $source . '" or Source ="0"');
        $total = $query->execute();
        return $total[0]['Total_Regimen_Drugs'];
    }

    public function getPagedRegimenDrugs($offset, $items, $source = 0) {
        $query = Doctrine_Query::create()->select("Regimen,Drugcode,Active")->from("Regimen_Drug")->where('Source = "' . $source . '" or Source ="0"')->offset($offset)->limit($items);
        $regimen_drugs = $query->execute();
        return $regimen_drugs;
    }

}

?>