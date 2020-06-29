<?php

namespace WeDevs\PM_Pro\Label\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;


class Task_Label_Task extends Eloquent {

    use Model_Events;

    protected $table = 'pm_task_label_task';
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'label_id',
    ];
}
