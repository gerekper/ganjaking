<?php

namespace WeDevs\PM_Pro\Integrations\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;

class Integrations extends Eloquent {

    protected $table = 'pm_integrations';

    protected $fillable = [
        'primary_key',
        'project_id',
        'foreign_key',
        'action_type',
        'type',
        'source',
        'username'
    ];
}