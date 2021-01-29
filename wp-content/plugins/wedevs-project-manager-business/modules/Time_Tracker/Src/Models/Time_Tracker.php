<?php
namespace WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\User\Models\User;
use WeDevs\PM\Common\Traits\Model_Events;
use WeDevs\PM\Task\Models\Task;

class Time_Tracker extends Eloquent {
    use Model_Events;

    protected $table = 'pm_time_tracker';

    protected $fillable = [
        'user_id',
        'project_id',
        'list_id',
        'task_id',
        'start',
        'stop',
        'total',
        'run_status',
        'created_by',
        'updated_by',
    ];

    public function task() {
    	return $this->hasOne( 'WeDevs\PM\Task\Models\Task', 'id', 'task_id' );
    }

    public function user() {
        return $this->hasOne( 'WeDevs\PM\User\Models\User', 'ID', 'user_id' );
    }
}
