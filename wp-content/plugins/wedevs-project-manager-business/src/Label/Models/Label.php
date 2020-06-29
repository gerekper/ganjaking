<?php

namespace WeDevs\PM_Pro\Label\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;


class Label extends Eloquent {

    use Model_Events;

    protected $table = 'pm_task_label';

    protected $fillable = [
        'title',
        'description',
        'color',
        'status',
        'project_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
