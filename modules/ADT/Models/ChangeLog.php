<?php
namespace Modules\ADT\Models;

use App\Models\BaseModel;

class ChangeLog extends BaseModel {
    protected $table = 'change_log';
    protected $guarded = ['id'];

}
