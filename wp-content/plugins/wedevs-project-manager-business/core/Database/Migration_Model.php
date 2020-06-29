<?php

namespace WeDevs\PM_Pro\Core\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Migration_Model extends Eloquent {
    protected $table;

    public function __construct( array $attributes = [] ) {
        $this->table = pm_pro_migrations_table_prefix() . '_migrations';

        parent::__construct( $attributes );
    }

    protected $fillable = [
        'id',
        'migration'
    ];
}