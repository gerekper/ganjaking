<?php
namespace WeDevs\PM_Pro\Modules\Invoice\Db\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use WeDevs\PM_Pro\Core\Database\Abstract_Migration as Migration;

class Create_Invoice_Table extends Migration {
    public function schema() {
        Capsule::schema()->create( 'pm_invoice', function( $table ) {
            $table->bigIncrements( 'id' );

            $table->string( 'title' );
            $table->bigInteger( 'client_id' );
            $table->bigInteger( 'project_id' );
            $table->tinyInteger( 'status' )
                ->default(0)
                ->comment('0: Incomplete; 1: Complete; 3: Partial');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->float( 'discount' )->default(0);
            $table->tinyInteger( 'partial' )
                ->default(0)
                ->comment('1: Partial; 0: Not Partial;');
            $table->float( 'partial_amount' )->default(0);
            $table->text( 'terms' )->nullable();
            $table->text( 'client_note' )->nullable();
            $table->longText( 'items' );

            $table->unsignedInteger( 'created_by' )->nullable();
            $table->unsignedInteger( 'updated_by' )->nullable();

            $table->timestamps();
        });
    }
}
