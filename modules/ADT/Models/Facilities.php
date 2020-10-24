<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Facilities extends BaseModel {

    protected $table = 'facilities';
    protected $guarded = ['id'];
        
    public function facility_county(){
        return $this->belongsTo(County::class, 'county', 'id');
    }
        
    public function parent_district(){
        return $this->belongsTo(District::class, 'district', 'id');
    }
        
    public function type(){
        return $this->belongsTo(FacilityType::class, 'facilitytype', 'id');
    }
        
    public function supplier(){
        return $this->belongsTo(FacilityType::class, 'supplied_by', 'id');
    }
        
    public function support(){
        return $this->belongsTo(Supporter::class, 'supported_by', 'id');
    }
        
    public function sync_facility(){
        return $this->belongsTo(Sync_facility::class, 'map', 'id');
    }

    public function getDistrictFacilities($district)
    {
        $query = Doctrine_Query::create()->select("facilitycode,name")->from("Facilities")->where("District = '" . $district . "'");
        $facilities = $query->execute();
        return $facilities;
    }

    public static function search($search)
    {
        $query = Doctrine_Query::create()->select("facilitycode,name")->from("Facilities")->where("name like '%" . $search . "%'");
        $facilities = $query->execute();
        return $facilities;
    }

    public static function getFacilityName($facility_code)
    {
        $query = Doctrine_Query::create()->select("name")->from("Facilities")->where("facilitycode = '$facility_code'");
        $facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $facility[0]['name'];
    }

    public static function getTotalNumber($district = 0)
    {
        if ($district == 0) {
            $query = DB::select("SELECT COUNT(*) as Total_Facilities FROM Facilities");
        } else if ($district > 0) {
            $query = DB::select("SELECT COUNT(*) as Total_Facilities FROM Facilities WHERE district = '$district'");
        }
        return $query[0]->Total_Facilities;
    }

    public static function getTotalNumberInfo($facility_code)
    {
        $query = Doctrine_Query::create()->select("COUNT(*) as Total_Facilities")->from("Facilities")->where("facilitycode = '$facility_code'");
        $count = $query->execute();
        return $count[0]->Total_Facilities;
    }

    public function getPagedFacilities($offset, $items, $district = 0)
    {
        if ($district == 0) {
            $query = Doctrine_Query::create()->select("*")->from("Facilities")->orderBy("name")->offset($offset)->limit($items);
        } else if ($district > 0) {
            $query = Doctrine_Query::create()->select("*")->from("Facilities")->where("district = '$district'")->orderBy("name")->offset($offset)->limit($items);
        }

        $facilities = $query->execute();
        return $facilities;
    }

    public static function getFacility($id)
    {
        $query = Doctrine_Query::create()->select("*")->from("Facilities")->where("id = '$id'");
        $facility = $query->execute();
        return $facility[0];
    }

    public static function getCurrentFacility($id)
    {
        $facility = DB::table("facilities")->where("facilitycode", $id)->get();
        return $facility;
    }

    public static function getAll() {
        $query = DB::table('Facilities')->get();
        return BaseModel::resultSet($query);
    }

    public static function getFacilities() {
        $query = DB::table('Facilities')->select('facilitycode', 'name')->orderBy("name")->get();
        return BaseModel::resultSet($query);
    }

    public static function getSatellites($parent) {
        $query = DB::select("SELECT id,facilitycode,name FROM Facilities  where parent = '$parent' AND facilitycode !='$parent' order By  name asc");
        return BaseModel::resultSet($query);
    }

    public static function getCodeFacility($id)
    {
        /*
        $query = Doctrine_Query::create()->select("*")->from("Facilities")->where("facilitycode = '$id'");
        $facility = $query->execute();
        return $facility[0];
        */

        $db = \Config\Database::connect();
        $builder = $db->table('Facilities')->where('facilitycode', $id);
        $query = $builder->get();

        return $query->getRow();
    }

    public static function getMapFacility($id)
    {
        $query = Doctrine_Query::create()->select("*")->from("Facilities")->where("map = '$id'");
        $facility = $query->execute();
        return $facility[0];
    }

    public static function getSupplier($id)
    {
        return DB::select("SELECT f.*,s.name supplier_name FROM facilities f LEFT JOIN suppliers s ON f.supplied_by = s.id WHERE f.facilitycode='$id'")[0];
    }

    public static function getParent($id)
    {
        $query = Doctrine_Query::create()->select("*")->from("Facilities")->where("facilitycode = '$id'");
        $facility = $query->execute();
        return $facility[0];
    }

    public function getMainSupplier($facility_code)
    {
        $query = Doctrine_Query::create()->select("*")->from("Facilities f")->leftJoin('f.supplier s')->where("facilitycode = '$facility_code'");
        $facility = $query->execute();
        return $facility[0];
    }

    public static function getType($facility_code)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT count(*) as count FROM sync_facility s1
					right join sync_facility s2 on s1.id = s2.parent_id
					WHERE s1.code ='$facility_code'");
        return (int) $query->getResultArray()[0]['count'];
    }

    public function getId($facility_code)
    {
        $query = Doctrine_Query::create()->select("id")->from("Facilities")->where("facilitycode='$facility_code'");
        $facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $facility[0];
    }

    public function getSatellite($parent)
    {
        $query = Doctrine_Query::create()->select("*")->from("Facilities")->where("facilitycode!='$parent' and parent = '$parent'")->orderBy("name asc");
        $facility = $query->execute();
        return $facility;
    }

    public static function getCentralCode($id)
    {
        $query = DB::table('facilities')->select('*')->where('facilitycode', $id)->get();
        return $query[0]->parent;
    }

    public static function getCentralName($id)
    {
        return DB::table('facilities')->select('id', 'facilitycode', 'name')->where('facilitycode', $id)->get();
    }

    public static function getParentandSatellites($parent)
    {
        $query = Doctrine_Query::create()->select("DISTINCT(facilitycode) as code")->from("Facilities")->where("parent = '$parent' OR facilitycode ='$parent' ")->orderBy("name asc");
        $facilities = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        $lists = array();
        if ($facilities) {
            foreach ($facilities as $facility) {
                $lists[] = $facility['code'];
            }
        }
        return $lists;
    }

    public function getItems()
    {
        $query = Doctrine_Query::create()->select("facilitycode AS id,name AS Name")->from("Facilities")->orderBy("name asc");
        $facility = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $facility;
    }
}
