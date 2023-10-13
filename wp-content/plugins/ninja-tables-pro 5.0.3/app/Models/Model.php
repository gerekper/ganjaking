<?php

namespace NinjaTablesPro\App\Models;

use NinjaTables\Framework\Database\Orm\Model as BaseModel;

class Model extends BaseModel
{
    protected $guarded = ['id', 'ID'];
}
