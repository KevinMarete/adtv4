<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Sync_facility extends BaseModel {

    protected $table = 'sync_facility';
    protected $fillable = ['name', 'code', 'category', 'hcsm_id', 'keph_level', 'location', 'sponsors', 'affiliate_organization_id', 'services', 'manager_id', 'district_id', 'address_id', 'parent_id', 'ordering', 'affiliation', 'service_point', 'county_id'];    

    public function getAll() {
        $query = Doctrine_Query::create()->select("*")->from("sync_facility");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility;
    }

    public static function getId($facility_code, $parent_sites = 0) {
        $query = Sync_facility::select('id')->where('code', $facility_code);
        if ($parent_sites == 0) {
            $query = $query->where('category', 'like', '%satellite%');
        } else if ($parent_sites == 1) {
            $query = $query->where('category', 'like', '%standalone%');
        } else if ($parent_sites > 1) {
            $query = $query->where(function($query){
                $query->where('category', 'like', '%central%');
                $query->orWhere('category', 'like', '%standalone%');
            });            
        }
        $query = $query->first();
        return @$query->id;
    }

    public static function getCode($facility_id, $parent_sites = 0) {
        $sync_facility = Sync_facility::select('code')->where('id', $facility_id);
        if ($parent_sites == 0) {
            $sync_facility = $sync_facility->where('category', 'like', '%satellite%');
        } else if ($parent_sites == 1) {
            $sync_facility = $sync_facility->where('category', 'like', '%standalone%');
        } else {
            $sync_facility = $sync_facility->where('category', 'like', '%central%');
        }
        $sync_facility = $sync_facility->first();
        return @$sync_facility;
    }

    public function getSatellites($central_site) {//Include CUrrent facility
        $query = Doctrine_Query::create()->select("id")->from("sync_facility")->where("parent_id='$central_site'");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility;
    }

    public function getOtherSatellites($central_site, $facility_code) { //Only get satellites
        $query = Doctrine_Query::create()->select("id")->from("sync_facility")->where("parent_id='$central_site' and code !='$facility_code'");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility;
    }

    public function getSatellitesDetails($central_site) {
        $query = Doctrine_Query::create()->select("*")->from("sync_facility")->where("parent_id='$central_site'");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility;
    }

    public function get_facility_category($code = NULL) {
        $query = Doctrine_Query::create()->select("category")->from("sync_facility")->where("code='$code'");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility[0]['category'];
    }

    public static  function get_active() {
        $query = DB::table("sync_facility")->where("active", '1')->get();
        return json_decode(json_encode($query),TRUE);
    }

}
?>

