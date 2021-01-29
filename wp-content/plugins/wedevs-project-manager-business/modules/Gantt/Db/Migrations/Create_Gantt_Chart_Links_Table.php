<?php
namespace WeDevs\PM_Pro\Modules\Gantt\Db\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use WeDevs\PM_Pro\Core\Database\Abstract_Migration as Migration;

class Create_Gantt_Chart_Links_Table extends Migration {
    public function schema() {
        Capsule::schema()->create( 'pm_gantt_chart_links', function( $table ) {
            $table->increments( 'id' );
            $table->unsignedInteger( 'source' );
            $table->unsignedInteger( 'target' );
            $table->unsignedInteger( 'type' );
            $table->unsignedInteger( 'created_by' )->nullable();
            $table->unsignedInteger( 'updated_by' )->nullable();
            $table->timestamps();
        } );
    }
}
