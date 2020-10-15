<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Sync_facility extends BaseModel {

    protected $table = 'sync_facility';
    protected $fillable = array('name', 'code', 'category', 'hcsm_id', 'keph_level', 'location', 'sponsors', 'affiliate_organization_id', 'services', 'manager_id', 'district_id', 'address_id', 'parent_id', 'ordering', 'affiliation', 'service_point', 'county_id');

    public function getAll() {
        $query = Doctrine_Query::create()->select("*")->from("sync_facility");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility;
    }

    public static function getId($facility_code, $parent_sites = 0) {
        if ($parent_sites == 0) {
            $conditions = "code='$facility_code' and category like '%satellite%' and ordering = '0' and service_point = '1'";
        } else if ($parent_sites == 1) {
            $conditions = "code='$facility_code' and category like '%standalone%' and ordering = '1' and service_point = '1'";
        } else if ($parent_sites > 1) {
            $conditions = "code='$facility_code' and (category like '%central%' or category like '%standalone%') and ordering = '1' and (service_point = '0' or service_point = '1')";
        }
        $query = DB::select("SELECT id FROM sync_facility WHERE $conditions ORDER BY id");
        return @$query[0]->id;
    }

    public function getCode($facility_id, $parent_sites = 0) {
        if ($parent_sites == 0) {
            $conditions = "id='$facility_id' and category like '%satellite%' and ordering = '0' and service_point = '1'";
        } else if ($parent_sites == 1) {
            $conditions = "id='$facility_id' and category like '%standalone%' and ordering = '1' and service_point = '1'";
        } else {
            $conditions = "id='$facility_id' and category like '%central%' and ordering = '1' and service_point = '0'";
        }
        $query = Doctrine_Query::create()->select("code")->from("sync_facility")->where("$conditions");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return @$sync_facility[0];
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

    public function get_active() {
        $query = Doctrine_Query::create()->select("*")->from("sync_facility")->where("Active='1'");
        $sync_facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $sync_facility;
    }

}
?>

