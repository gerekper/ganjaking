<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

add_action( 'divi_extensions_init', 'groovymenu_custom_divi_module' );

if ( ! function_exists( 'groovymenu_custom_divi_module' ) ) {
	function groovymenu_custom_divi_module() {
		require_once plugin_dir_path( __FILE__ ) . 'divi-grooni-groovymenu' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'DiviGrooniGroovyMenu_init.php';
	}
}
