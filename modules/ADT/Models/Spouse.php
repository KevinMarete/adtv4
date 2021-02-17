<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class Spouse extends BaseModel {
    protected $table = 'spouses';
    protected $fillable = ['primary_spouse','secondary_spouse'];

}
