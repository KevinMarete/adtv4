<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Sync_drug extends BaseModel {

    protected $table = 'sync_drug';
    protected $guarded = ['id'];

    public function getAll() {
        $query = DB::table('sync_drug')->get();
        return $query;
    }

    public function getActive() {
        $drug_name = "CONCAT_WS('] ',CONCAT_WS(' [',name,abbreviation),CONCAT_WS(' ',strength,formulation)) as name";
        $query = DB::select(" SELECT id,packsize,$drug_name FROM sync_drug WHERE active = '1' and (category_id='1' or category_id='2' or category_id='3' or category_id='4') order by category_id, name");
        return BaseModel::resultSet($query);
    }

    public function getMapActive() {
        $drug_name = "CONCAT_WS('] ',CONCAT_WS(' [',name,abbreviation),CONCAT_WS(' ',strength,formulation)) as name";
        $query = DB::select(" SELECT id,packsize,$drug_name FROM sync_drug WHERE active = '1' and (category_id='1' or category_id='2' or category_id='3' or category_id='4') order by category_id, name");
        return BaseModel::resultSet($query);
    }

    public function getActiveList() {
        $drug_name = "CONCAT_WS('] ',CONCAT_WS(' [',name,abbreviation),CONCAT_WS(' ',strength,formulation)) as Drug,unit as Unit_Name,packsize as Pack_Size,category_id as Category";
        $query = DB::select(" SELECT id,$drug_name FROM sync_drug where active = '1' and (category_id='1' or category_id='2' or category_id='3' or category_id='4') order by category_id, name");
        return BaseModel::resultSet($query);
    }

    public function getPackSize($id) {
        $query = DB::table('sync_drug')->select("packsize")->where('id', $id)->get();
        return BaseModel::resultSet($query)[0];
    }

    public static function getOrderedActive() {
        $drug_name = "CONCAT_WS('] ',CONCAT_WS(' [',name,abbreviation),CONCAT_WS(' ',strength,formulation)) as name";
        $query = DB::select(" SELECT id,packsize,$drug_name from sync_drug where active = '1' and (category_id='1' or category_id='2' or category_id='3' or category_id='4') order By category_id, name");
        return BaseModel::resultSet($query);
    }

}
