<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class District extends BaseModel {
    protected $table = 'district';

	public static function find($id)
	{
		$db = \Config\Database::connect();
		$builder = $db->table('District')->where('id', $id);
		$query = $builder->get();

		return $query->getRow();
	}
}
