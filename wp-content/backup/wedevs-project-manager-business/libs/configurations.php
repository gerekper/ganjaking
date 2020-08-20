<?php

use WeDevs\PM_Pro\Core\Config\Config;

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