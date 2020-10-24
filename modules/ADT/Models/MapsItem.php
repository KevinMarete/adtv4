<?php
namespace Modules\ADT\Models;
use Illuminate\Database\Capsule\Manager as DB;

use App\Models\BaseModel;

class MapsItem extends BaseModel {
    protected $table = 'maps_item';
    protected $guarded = ['id'];

    public function dhis_element(){
        return $this->belongsTo(DhisElements::class, 'regimen_id', 'target_id');
    }

    public static function getDhisItem($item,$code = '') {
		$sql = "SELECT * FROM maps_item mi ".
		"LEFT JOIN dhis_elements de on mi.regimen_id = de.target_id ".
		"WHERE  maps_id = ".$item.
		" AND dhis_report ='MoH 729b' AND target_report !='unknown'";
		
		$items = DB::select($sql);
		return $items;
		
	}

}
