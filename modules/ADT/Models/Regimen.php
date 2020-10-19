<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use App\Models\User;
use \Modules\ADT\Models\Regimen_category;
use \Modules\ADT\Models\Regimen_Service_Type;
use \Modules\ADT\Models\Regimen_Drug;
use \Modules\ADT\Models\Sync_Regimen;
use Illuminate\Database\Capsule\Manager as DB;

class Regimen extends BaseModel {

    protected $table = 'regimen';
    protected $fillable = array('Regimen_Code', 'Regimen_Desc', 'Category', 'Type_Of_Service', 'Remarks', 'Enabled', 'Source', 'Optimality', 'Merged_To', 'map');
    protected $with = ['Regimen_Service_Type', 'Regimen_Category', 'Regimen_Drug', 'Sync_Regimen'];

    /* public static function setUp() {
      $this->setTableName('regimen');
      $this->hasOne('Regimen_Category as Regimen_Category', array('local' => 'Category', 'foreign' => 'id'));
      $this->hasOne('Regimen_Service_Type as Regimen_Service_Type', array('local' => 'Type_Of_Service', 'foreign' => 'id'));
      $this->hasMany('Regimen_Drug as Drugs', array('local' => 'id', 'foreign' => 'Regimen'));
      $this->hasOne('Sync_Regimen as S_Regimen', array('local' => 'map', 'foreign' => 'id'));
      } */

    function Regimen_Category() {
        return $this->hasOne(Regimen_Category::class, 'id', 'category');
    }

    function Regimen_Service_Type() {
        return $this->hasOne(Regimen_Service_Type::class, 'id', 'type_of_service');
    }

    function Regimen_Drug() {
        return $this->hasMany(Regimen_Drug::class, 'regimen');
    }

    function Sync_Regimen() {
        return $this->hasOne(Sync_Regimen::class, 'id', 'map');
    }

    public static function getAll($source = 0) {
        $query = DB::select("SELECT * from Regimen where Source =  $source  or Source ='0' order By Regimen_Desc asc");
        return $query;
    }

    public static function getAllEnabled($source = 0) {
        $query = DB::select("SELECT * from Regimen where enabled ='1' order By Regimen_Code asc");
        return $query;
    }

    public static function getAllObjects($source = 0) {
        $query = DB::select("SELECT * from Regimen where Source =  $source  or Source ='0' order By Regimen_Code asc");
        return $query;
    }

    public static function getAllHydrated($source = 0, $access_level = "") {
        if ($access_level == "" || $access_level == "facility_administrator") {
            $displayed_enabled = "r.Source='0' or r.Source !='0'";
        } else {
            $displayed_enabled = "r.Source='$source' or r.Source='0' AND r.Enabled='1'";
        }
        $query = DB::select("SELECT r.id, r.Regimen_Code, r.Regimen_Desc, r.Line, rc.Name as Regimen_Category, rst.Name as Regimen_Service_Type, r.Enabled, r.Merged_To, r.map FROM Regimen r LEFT JOIN Regimen_Category rc ON r.category = rc.id LEFT JOIN Regimen_Service_Type rst ON rst.id = r.type_of_service WHERE $displayed_enabled ORDER BY r.id desc ");
        return BaseModel::resultSet($query);
    }

    public static function getTotalNumber($source = 0) {
        $query = Doctrine_Query::create()->select("count(*) as Total_Regimens")->from("Regimen")->where('Source = "' . $source . '" or Source ="0"');
        $total = $query->execute();
        return $total[0]['Total_Regimens'];
    }

    public static function getPagedRegimens($offset, $items, $source = 0) {
        $query = Doctrine_Query::create()->select("Regimen_Code,Regimen_Desc,Category,Line,Type_Of_Service,Remarks,Enabled")->from("Regimen")->where('Source = "' . $source . '" or Source ="0"')->offset($offset)->limit($items);
        $regimens = $query->execute();
        return $regimens;
    }

    public static function getOptimalityRegimens($optimality) {
        $query = Doctrine_Query::create()->select("*")->from("Regimen")->where('Optimality = "' . $optimality . '" and Source ="0"')->orderBy("Regimen_Desc asc");
        $regimens = $query->execute();
        return $regimens;
    }

    public static function getRegimen($id) {
        $query = Doctrine_Query::create()->select("*")->from("Regimen")->where("id = '$id'");
        $regimens = $query->execute();
        return $regimens[0];
    }

    public static function getHydratedRegimen($id) {
        $query = Doctrine_Query::create()->select("*")->from("Regimen")->where("id = '$id'");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public static function getRegimens() {
        $query = Doctrine_Query::create()->select("*")->from("Regimen")->orderBy("Regimen_Code asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public static function getNonMappedRegimens() {
        $query = Doctrine_Query::create()->select("*")->from("Regimen")->where("Enabled = '1' AND (map='' OR map='0')")->orderBy("Regimen_Code asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public static function getLineRegimens($service) {
        $query = Doctrine_Query::create()->select("*")->from("Regimen")->where("Enabled = '1' and Type_Of_Service='$service'")->orderBy("Regimen_Code asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public static function get_pmtct_oi_regimens() {
        $query = Doctrine_Query::create()->select("*")->from("Regimen r")->where("r.Enabled = '1' AND (r.Regimen_Service_Type.Name LIKE '%pmtct%' OR r.Regimen_Service_Type.Name LIKE '%oi%')")->orderBy("Regimen_Code asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public function getServiceRegimens($service) {
        $sql = ("SELECT regimen.id, category as Category,enabled as Enabled,line as Line,merged_to as Merged_To,optimality as Optimality,regimen_code as Regimen_Code,regimen_desc as Regimen_Desc,remarks as Remarks,source as Source,type_of_service as Type_Of_Service FROM regimen
			inner join regimen_service_type on type_of_service = regimen_service_type.id 
			and regimen_service_type.name like'%$service%' ORDER BY type_of_service");
        $query = $this->db->query($sql);
        $regimens = $query->result_array();
        return $regimens;
    }

    public static function getChildRegimens() {
        $query = Doctrine_Query::create()->select("*")->from("Regimen r")->leftJoin('r.Regimen_Category rc')->where("(rc.Name LIKE '%paed%' OR rc.Name LIKE '%ped%' OR rc.Name LIKE '%child%'  OR rc.Name LIKE '%oi%' OR rc.Name LIKE '%hepatitis%')  AND r.Enabled = '1'")->orderBy("Regimen_Code asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public static function getAdultRegimens() {
        $query = Doctrine_Query::create()->select("*")->from("Regimen r")->leftJoin('r.Regimen_Category rc')->where("(rc.Name LIKE '%adult%' OR rc.Name LIKE '%mother%' OR rc.Name LIKE '%oi%' OR rc.Name LIKE '%hepatitis%' OR rc.Name LIKE '%prep%') AND r.Enabled = '1'")->orderBy("Regimen_Code asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public static function getItems() {
        $query = Doctrine_Query::create()->select("id,CONCAT_WS(' | ',Regimen_Code,Regimen_Desc) AS Name")->from("Regimen")->where("Enabled='1'")->orderby("Name asc");
        $regimens = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $regimens;
    }

    public function get_patients_regimen_switched() {
        $sql = ("SELECT CONCAT_WS(  ' | ', r2.regimen_code, r2.regimen_desc ) AS from_regimen, CONCAT_WS(  ' | ', r1.regimen_code, r1.regimen_desc ) AS to_regimen, p.patient_number_ccc AS art_no, CONCAT_WS(  ' ', CONCAT_WS(  ' ', p.first_name, p.other_name ) , p.last_name ) AS full_name, pv.dispensing_date, rst.name AS service_type,IF(rcp.name is not null,rcp.name,pv.regimen_change_reason) as regimen_change_reason 
				FROM patient p 
				LEFT JOIN regimen_service_type rst ON rst.id = p.service 
				LEFT JOIN patient_status ps ON ps.id = p.current_status 
				LEFT JOIN (
							SELECT * FROM patient_visit 
							WHERE dispensing_date BETWEEN  '$start_date' AND  '$end_date' AND last_regimen != regimen AND last_regimen IS NOT NULL
							ORDER BY id DESC
							) AS pv ON pv.patient_id = p.patient_number_ccc 
				LEFT JOIN regimen r1 ON r1.id = pv.regimen 
				LEFT JOIN regimen r2 ON r2.id = pv.last_regimen 
				LEFT JOIN regimen_change_purpose rcp ON rcp.id=pv.regimen_change_reason 
				WHERE ps.Name LIKE  '%active%' 
				AND r2.regimen_code IS NOT NULL 
				AND r1.regimen_code IS NOT NULL 
				AND pv.dispensing_date IS NOT NULL 
				AND r2.regimen_code NOT LIKE '%oi%' 
				GROUP BY pv.patient_id, pv.dispensing_date");
        $query = $this->db->query($sql);
        $regimens = $query->result_array();
        return $regimens;
    }

}

?>