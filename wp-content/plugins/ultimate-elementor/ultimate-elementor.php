<?php
/**
 * Plugin Name: Ultimate Addons for Elementor
 * Plugin URI: https://ultimateelementor.com/
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Version: 1.36.28
 * Elementor tested up to: 3.18.0
 * Elementor Pro tested up to: 3.18.0
 * Description: Ultimate Addons is a premium extension for Elementor that adds 40+ widgets and works on top of any Elementor Package (Free, Pro). You can use it with any WordPress theme.
 * Text Domain: uael
 *
 * @package UAEL
 */

$brainstrom = get_option( 'brainstrom_products', [] );
$brainstrom['plugins']['uael']['status'] = 'registered';
$brainstrom['plugins']['uael']['purchase_key'] = 'registered';
update_option( 'brainstrom_products', $brainstrom ); 
 
define( 'UAEL_FILE', __FILE__ );

require_once 'classes/class-uael-loader.php';

/**
 * Load Brainstorm product updater
 */

$bsf_core_version_file = realpath( dirname( __FILE__ ) . '/admin/bsf-core/version.yml' );

if ( is_file( $bsf_core_version_file ) ) {
	global $bsf_core_version, $bsf_core_path;
	$bsf_core_dir = realpath( dirname( __FILE__ ) . '/admin/bsf-core/' );
	$version      = file_get_contents( realpath( plugin_dir_path( __FILE__ ) . '/admin/bsf-core/version.yml' ) );
	if ( version_compare( $version, $bsf_core_version ? $bsf_core_version : '0.0.0', '>' ) ) {
		$bsf_core_version = $version;
		$bsf_core_path    = $bsf_core_dir;
	}
}

if ( ! function_exists( 'bsf_core_load' ) ) {

	/**
	 * Load Brainstorm product updater
	 */
	function bsf_core_load() {

		global $bsf_core_version, $bsf_core_path;

		if ( is_file( realpath( $bsf_core_path . '/index.php' ) ) ) {
			include_once realpath( $bsf_core_path . '/index.php' );
		}
	}
}

add_action( 'init', 'bsf_core_load', 999 );
