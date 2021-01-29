<?php
namespace WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;
use Carbon\Carbon;

class Custom_Field extends Eloquent {

    use Model_Events;

    protected $table = 'pm_custom_fields';
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'type',
        'optional_value',
        'order'
    ];

    public static function latest_order( $project_id ) {
        return  self::where( 'project_id', $project_id )
            ->max( 'order' );
    }

    public function value() {
        return $this->hasMany( 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models\Task_Custom_Field', 'field_id' );
    }
}
