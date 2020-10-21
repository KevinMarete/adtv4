<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Sync_drug extends BaseModel {
    protected $table = 'sync_drug';
    protected $guarded = ['id'];

    public static function getActiveList() {
		$drug_name = "CONCAT_WS('] ',CONCAT_WS(' [',name,abbreviation),CONCAT_WS(' ',strength,formulation)) as Drug,unit as Unit_Name,packsize as Pack_Size,category_id as Category";
    $sync_drug = Sync_drug::select(DB::raw("id, ".$drug_name))->where('active', '1')
                ->where(function ($query) {
                  $query->where('category_id', '1')
                    ->orWhere('category_id', '2')
                    ->orWhere('category_id', '3')
                    ->orWhere('category_id', '4');
                  })
                    // ->where(DB::raw("(category_id='1' or category_id='2' or category_id='3' or category_id='4')"))
                    ->orderBy('category_id')
                    ->orderBy('name')
                    ->get();
		return $sync_drug;
	}

}
