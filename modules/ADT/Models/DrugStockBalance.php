<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class DrugStockBalance extends BaseModel {
    protected $table = 'drug_stock_balance';
    protected $guarded = ['id'];

    public function ccc_store_service_point(){
        return $this->belongsTo(CCC_store_service_point::class, 'ccc_store_sp', 'id');
    }

    public function drug(){
        return $this->belongsTo(Drugcode::class, 'drug_id', 'id');
    }

}
