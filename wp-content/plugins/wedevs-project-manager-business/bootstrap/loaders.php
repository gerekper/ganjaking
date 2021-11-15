<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Core\Router\WP_Router;
use WeDevs\PM_Pro\Core\Database\Migrater;
use WeDevs\PM_Pro\Core\WP\Frontend;

function pm_pro_load_configurations() {
    $files = glob( __DIR__ . "/../config/*.php" );

    if ( $files === false ) {
        throw new RuntimeException( "Failed to glob for config files" );
    }

    foreach ( $files as $file ) {
        $config[basename( $file, '.php' )] = include $file;
    }

    unset( $file );
    unset( $files );

    return $config;
}

function pm_pro_load_texts() {
    $files = glob( __DIR__ . "/../texts/*.php" );

    if ( $files === false ) {
        throw new RuntimeException( "Failed to glob for lang files" );
    }

    foreach ( $files as $file ) {
        $lang[basename( $file, '.php' )] = include $file;
    }

    unset( $file );
    unset( $files );

    return $lang;
}

function pm_pro_load_libs() {
    $files = glob( __DIR__ . "/../libs/*.php" );

    if ( $files === false ) {
        throw new RuntimeException( "Failed to glob for lib files" );
    }

    foreach ($files as $file) {
        require_once $file;
    }

    unset( $file );
    unset( $files );
}

/**
 * All php files in the routes directory will be loaded automatically.
 * These files will be considered as route files only, nothing else.
 * So make files in that directoy carefully.
 */
function pm_pro_load_routes() {
    $files = glob( __DIR__ . "/../routes/*.php" );

    $files = apply_filters( 'pm_pro_load_router_files', $files );

    if ( $files === false ) {
        throw new RuntimeException( "Failed to glob for route files" );
    }

    foreach ( $files as $file ) {
        require_once $file;
    }

    unset( $file );
    unset( $files );

    //pm_pro_load_module_routers();
}

// function pm_pro_load_orm() {
//     $capsule = new Capsule;

//     $status = $capsule->addConnection( pm_pro_config('db') );

//     // Setup eloquent model events
//     $capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher());

//     // Make this Capsule instance available globally via static methods... (optional)
//     $capsule->setAsGlobal();

//     // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
//     $capsule->bootEloquent();
// }

function pm_pro_load_schema() {
    $classes = array(
        'Create_PM_Pro_Migrations_Table',
    );

    $classes = apply_filters( 'pm_pro_schema_migrations', $classes );
    
    return $classes;
}

function pm_pro_migrate_db() {
    $migrater = new Migrater();

    $migrater->create_migrations_table();
    $migrater->build_schema();
}
function pm_pro_pseed_db() {
    (new ProSeeder())->run();
}

function pm_pro_register_routes() {
    $routes = Router::get_routes();

    WP_Router::register($routes);
}

function pm_pro_view() {
    new Frontend();
}
