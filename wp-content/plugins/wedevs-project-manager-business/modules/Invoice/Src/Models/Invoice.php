<?php
namespace WeDevs\PM_Pro\Modules\Invoice\Src\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\User\Models\User;
use WeDevs\PM\Common\Traits\Model_Events;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Project\Models\Project;

class Invoice extends Eloquent {
    use Model_Events;

    protected $table      = 'pm_invoice';
    protected $primaryKey = 'id';
    public $timestamps    = true;

    protected $fillable = [
        'client_id',
        'title',
        'start_at',
        'due_date',
        'discount',
        'partial',
        'partial_amount',
        'terms',
        'client_note',
        'items',
        'project_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function metas() {
        return $this->hasMany( 'WeDevs\PM\Common\Models\Meta', 'entity_id' )
            ->where( 'entity_type', 'invoice' );
    }

    public function project() {
        return $this->hasOne( 'WeDevs\PM\Project\Models\Project', 'id', 'project_id' );
    }
}
