<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use \Modules\ADT\Models\Generic_name;
use \Modules\ADT\Models\Drug_unit;
use \Modules\ADT\Models\Supporter;
use \Modules\ADT\Models\Brand;
use \Modules\ADT\Models\Dose;
use \Modules\ADT\Models\Sync_drug;
use \Modules\ADT\Models\Suppliers;
use Illuminate\Database\Capsule\Manager as DB;

class Drugcode extends BaseModel {
    protected $table = 'drugcode';
    protected $guarded = ['id'];

    protected $with = ['Generic_Name', 'Drug_Unit', 'Supporter', 'Suppliers', 'Brand', 'Dose', 'Sync_Drug'];

    function Generic_Name() {
        return $this->hasOne(Generic_name::class, 'id', 'generic_name');
    }

    function Drug_Unit() {
        return $this->hasOne(Drug_unit::class, 'id', 'unit');
    }

    function Supporter() {
        return $this->hasOne(Supporter::class, 'id', 'supported_by');
    }

    function Suppliers() {
        return $this->hasOne(Suppliers::class, 'id', 'supported_by');
    }

    function Brand() {
        return $this->hasMany(Brand::class, 'drug_id');
    }

    function Dose() {
        return $this->hasOne(Dose::class, 'id', 'dose');
    }

    function Sync_Drug() {
        return $this->hasOne(Sync_drug::class, 'id', 'map');
    }

    public static function getAll($source = 0, $access_level = "") {
        if ($access_level == "" || $access_level == "facility_administrator") {
            $displayed_enabled = "Source='0' or Source !='0'";
        } else {
            $displayed_enabled = "(Source='$source' or Source='0') AND Enabled='1'";
        }

        //$query = Doctrine_Query::create()->select("SELECT d.id,d.Drug,du.Name as drug_unit,d.Pack_Size,d.Dose,s.Name as supplier,d.Safety_Quantity,d.Quantity,d.Duration,d.Enabled,d.Merged_To,d.map")->from("Drugcode d")->leftJoin('d.Drug_Unit du, d.Suppliers s')->where($displayed_enabled)->orderBy("id asc");
        $query = DB::select("SELECT d.id, d.drug AS Drug, d.pack_size AS Pack_Size, d.dose AS Dose, d.safety_quantity AS Safety_Quantity, d.quantity AS Quantity, d.duration AS Duration, d.enabled AS Enabled, d.merged_to AS Merged_to, d.map , d2.name, s.name AS supplier FROM drugcode d LEFT JOIN drug_unit d2 ON d.unit = d2.id LEFT JOIN suppliers s ON d.supported_by = s.id WHERE $displayed_enabled ORDER BY d.id asc");
        return BaseModel::resultSet($query);
    }

    public static function getAllEnabled($source = 0, $access_level = "") {
        $query = DB::select("SELECT id,Drug,Pack_Size,Safety_Quantity,Quantity,Duration,Enabled,Merged_To from Drugcode where enabled='1' order By Drug asc");
        return BaseModel::resultSet($query);
    }

    public static function getARVs() {
        $query = Doctrine_Query::create()->select("Drug,Pack_Size,Safety_Quantity,Quantity,Duration")->from("Drugcode")->where("None_Arv != '1'")->orderBy("id asc");
        $drugsandcodes = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $drugsandcodes;
    }

    public static function getAllObjects($source = 0) {
        $query = Doctrine_Query::create()->select("UPPER(d.Drug) As Drug,d.Pack_Size,d.Safety_Quantity,d.Quantity,d.Duration")->from("Drugcode d")->where("d.Supported_By='$source' and Enabled='1'")->orderBy("id asc");
        $drugsandcodes = $query->execute(array());
        return $drugsandcodes;
    }

    public static function getBrands() {
        $query = DB::table('Drugcode')->where('enabled', '1')->get();
        return $query;

    }
    public static function getEnabledDrugs() {
        return BaseModel::resultSet(DB::table('Drugcode')->where('enabled', '1')->get());
    }

    public static function getNonMappedDrugs() {
        $query = DB::select("SELECT d.*,du.Name as drug_unit from drugcode d left Join drug_unit du ON d.unit = du.id WHERE d.Enabled = '1' AND d.map='' OR d.map='0' order By drug asc ");
        return BaseModel::resultSet($query);
    }

    public static function getTotalNumber($source = 0) {
        $query = Doctrine_Query::create()->select("count(*) as Total_Drugs")->from("Drugcode")->where('Source = "' . $source . '" or Source ="0"');
        $total = $query->execute();
        return $total[0]['Total_Drugs'];
    }

    public static function getPagedDrugs($offset, $items, $source = 0) {
        $query = Doctrine_Query::create()->select("Drug,Unit,Pack_Size,Safety_Quantity,Generic_Name,Supported_By,Dose,Duration,Quantity,Source,Enabled,Supplied")->from("Drugcode")->where('Source = "' . $source . '" or Source ="0"')->offset($offset)->limit($items);
        $drugs = $query->execute();
        return $drugs;
    }

    public static function getDrugCode($id) {
        $query = DB::table('drugcode')->where('id', $id)->get();
        return $query[0];
    }

    public static function getDrug($drug_id, $ccc_id) {
        $db = \Config\Database::connect();
        $sql = "SELECT dc.*,du.Name as drugunit,dc.map
             FROM drugcode dc
             LEFT JOIN drug_unit du ON du.id=dc.unit
             WHERE dc.id='$drug_id'";
        $query = $db->query($sql);
        $drugs = $query->getResultArray();
        if ($drugs) {
            return $drugs[0];
        }
    }

    public static function getDrugBatches($drug_id, $ccc_id, $facility_code, $today) {
        $db = \Config\Database::connect();
        $sql = "SELECT d.id,d.drug as drugname,du.Name AS unit,d.pack_size,dsb.batch_number,dsb.expiry_date,dsb.stock_type,dsb.balance 
				FROM drug_stock_balance dsb 
				LEFT JOIN drugcode d ON d.id=dsb.drug_id 
				LEFT JOIN drug_unit du ON du.id = d.unit 
				WHERE dsb.drug_id='$drug_id'  
				AND dsb.expiry_date > CURDATE() 
				AND dsb.balance > 0   
				AND dsb.facility_code='$facility_code' 
				AND dsb.stock_type='$ccc_id' 
				ORDER BY dsb.expiry_date asc";
        $query = $db->query($sql);
        $batches = $query->getResultArray();
        return $batches;
    }

    public static function getDrugCodeHydrated($id) {
        $query = DB::table('drugcode')->where('id', $id)->get();
        return $query;
    }

    public static function deleteBrand($id) {
        $query = DB::delete("DELETE FROM brand WHERE id='$id'");
        return $query;
    }

    public static function getDrugID($drugname) {
        $query = Doctrine_Query::create()->select("id")->from("Drugcode")->where("Drug like '%$drugname%'");
        $drugs = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $drugs[0]['id'];
    }

    public static function getItems() {
        $query = Doctrine_Query::create()->select("id,Drug AS Name")->from("Drugcode")->where("Enabled='1'")->orderby("Drug asc");
        $drugs = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $drugs;
    }

}

?>
