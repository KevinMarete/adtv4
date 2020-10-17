<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class District extends BaseModel
{

	public function getTotalNumber()
	{
		$query = Doctrine_Query::create()->select("count(*) as Total_Districts")->from("District");
		$total = $query->execute();
		return $total[0]['Total_Districts'];
	}

	public function getPagedDistricts($offset, $items)
	{
		$query = Doctrine_Query::create()->select("*")->from("District")->offset($offset)->limit($items);
		$districts = $query->execute();
		return $districts;
	}
	public function getAll()
	{
		$query = Doctrine_Query::create()->select("*")->from("District");
		$districts = $query->execute();
		return $districts;
	}

	public function getPOB()
	{
		$query = Doctrine_Query::create()->select("*")->from("District")->orderby("Name asc");
		$districts = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $districts;
	}
	public function getActive()
	{
		$query = Doctrine_Query::create()->select("*")->from("District")->where("active='1'");
		$districts = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $districts;
	}

	public function getItems()
	{
		$query = Doctrine_Query::create()->select("id,Name")->from("District")->where("active='1'")->orderby("Name asc");
		$districts = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $districts;
	}

	public function getID($name)
	{
		$query = Doctrine_Query::create()->select("id")->from("District")->where("Name LIKE '%$name%'");
		$districts = $query->execute();
		$district_id = 0;
		if ($districts) {
			$district_id = $districts[0]['id'];
		}
		return $district_id;
	}

	public static function find($id)
	{
		$db = \Config\Database::connect();
		$builder = $db->table('District')->where('id', $id);
		$query = $builder->get();

		return $query->getRow();
	}
}//end class
