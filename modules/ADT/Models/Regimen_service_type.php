<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
//use Illuminate\Database\Capsule\Manager as DB;

class Regimen_service_type extends BaseModel
{

  protected $table = 'regimen_service_type';

  public function getAll()
  {
    $query = Doctrine_Query::create()->select("*")->from("Regimen_Service_Type")->where("Active", "1");
    $regimens = $query->execute();
    return $regimens;
  }
  public static function getHydratedAll()
  {
    $db = \Config\Database::connect();
    $builder = $db->table('Regimen_Service_Type')->where('active', '1');
    $query = $builder->get();

    return $query->getResult('array');
  }

  public function getTotalNumber()
  {
    $query = Doctrine_Query::create()->select("count(*) as Total_Types")->from("Regimen_Service_Type");
    $total = $query->execute();
    return $total[0]['Total_Types'];
  }

  public function getPagedTypes($offset, $items)
  {
    $query = Doctrine_Query::create()->select("Name")->from("Regimen_Service_Type")->offset($offset)->limit($items);
    $types = $query->execute();
    return $types;
  }
  public function getItems()
  {
    $query = Doctrine_Query::create()->select("id,Name")->from("Regimen_Service_Type")->where("Active", "1")->orderBy("Name asc");
    $types = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
    return $types;
  }
}
