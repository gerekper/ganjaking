<?php
namespace WeDevs\PM_Pro\Modules\Time_Tracker\Db\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use WeDevs\PM_Pro\Core\Database\Abstract_Migration as Migration;

class Create_Time_Tracker_Table extends Migration {
    public function schema() {
        Capsule::schema()->create( 'pm_time_tracker', function( $table ) {
            $table->bigIncrements( 'id' );

            $table->bigInteger( 'user_id' );
            $table->bigInteger( 'project_id' );
            $table->bigInteger( 'list_id' );
            $table->bigInteger( 'task_id' );
            $table->bigInteger( 'start' );
            $table->bigInteger( 'stop' );
            $table->bigInteger( 'total' );
            $table->tinyInteger( 'run_status' )
                ->comment('1: Running; 0: Stop;');

            $table->unsignedInteger( 'created_by' )->nullable();
            $table->unsignedInteger( 'updated_by' )->nullable();

            $table->timestamps();
        });
    }
}
