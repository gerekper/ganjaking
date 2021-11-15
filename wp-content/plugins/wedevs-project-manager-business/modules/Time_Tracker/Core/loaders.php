<?php
use WeDevs\PM\Core\Database\Migrater;

function pm_pro_time_tracker_load_schema() {
    $contents = [];
    $files = glob( __DIR__ . "/../Db/Migrations/*.php" );

    $files = apply_filters( 'pm_pro_time_tracker_schema_migrations', $files );

    if ( $files === false ) {
        throw new RuntimeException( "Failed to glob for migration files" );
    }

    foreach ( $files as $file ) {
        $contents[basename( $file, '.php' )] = file_get_contents( $file );
    }

    unset( $file );
    unset( $files );

    return $contents;
}

function pm_pro_time_tracker_migrate_db() {
    $migrater = new Migrater();

    $migrater->create_migrations_table();
    $migrater->build_schema();
}

function pm_pro_time_tracker_seed_db() {
    (new RoleTableSeeder())->run();
}
