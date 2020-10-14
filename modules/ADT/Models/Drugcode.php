<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Drugcode extends BaseModel {
    protected $table = 'drugcode';
    protected $guarded = ['id'];

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

}

?>
