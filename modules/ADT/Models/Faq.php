<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Faq extends BaseModel {

    protected $table = 'faq';
    protected $fillable = ['modules', 'questions', 'answers', 'active'];

    public static function getAll() {
        $query = DB::table("faq")->get()->toArray();
        return BaseModel::resultSet($query);
    }

    public function getAllActive() {
        $query = Doctrine_Query::create()->select("*")->from("faq")->where("active='1'");
        $faqs = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $faqs;
    }

    public function getAllHydrated() {
        $query = Doctrine_Query::create()->select("modules,questions, answers")->from("faq")->where("active='1'")->groupBy("modules");
        $faqs = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $faqs;
    }

}
