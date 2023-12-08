<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function vc_page_settings_custom_js_load() {
	wp_enqueue_script( 'ace-editor', vc_asset_url( 'lib/bower/ace-builds/src-min-noconflict/ace.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
}

add_action( 'vc-settings-render-tab-vc-custom_js', 'vc_page_settings_custom_js_load' );
