<?php
namespace WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;
use Carbon\Carbon;

class Task_Custom_Field extends Eloquent {

    use Model_Events;

    protected $table = 'pm_task_custom_fields';
    public $timestamps = false;

    protected $fillable = [
        'field_id',
        'project_id',
        'list_id',
        'task_id',
        'value',
        'color'
    ];
}
