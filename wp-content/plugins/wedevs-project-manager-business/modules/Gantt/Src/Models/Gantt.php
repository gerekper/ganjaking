<?php
namespace WeDevs\PM_Pro\Modules\Gantt\Src\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;
use Carbon\Carbon;


class Gantt extends Eloquent {

    use Model_Events;

    protected $table = 'pm_gantt_chart_links';

    protected $fillable = [
        'source',
        'target',
        'type',
    ];
}
