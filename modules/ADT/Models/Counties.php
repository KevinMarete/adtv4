<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Counties extends BaseModel {

    protected $table = 'counties';
    protected $fillable = ['county', 'active'];

    public function getTotalNumber() {
        $query = Doctrine_Query::create()->select("count(*) as Total_Districts")->from("Counties");
        $total = $query->execute();
        return $total[0]['Total_Counties'];
    }

    public function getPagedDistricts($offset, $items) {
        $query = Doctrine_Query::create()->select("*")->from("Counties")->offset($offset)->limit($items);
        $districts = $query->execute();
        return $districts;
    }

    public static function getAll() {
        $query = DB::table("Counties")->get()->toArray();
        return BaseModel::resultSet($query);
    }

    public function getActive() {
        $query = Doctrine_Query::create()->select("*")->from("Counties")->where("active='1'");
        $counties = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $counties;
    }

    public function getID($name) {
        $query = Doctrine_Query::create()->select("id")->from("Counties")->where("county LIKE '%$name%'");
        $counties = $query->execute();
        $county_id = 0;
        if ($counties) {
            $county_id = $counties[0]['id'];
        }
        return $county_id;
    }

}

//end class
?>