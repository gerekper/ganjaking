<?php
/**
 * Plugin Name: EventON
 * Plugin URI: http://www.myeventon.com/
 * Description: A beautifully crafted minimal calendar experience
 * Version: 4.4.2
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 6.0
 * Tested up to: 6.2.2
 * 
 * Text Domain: eventon
 * Domain Path: /lang/languages/
 * 
 * @package EventON
 * @category Core
 * @author AJDE
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$eventon_license = get_option( '_evo_products' );
foreach ( $eventon_license as $key => $value ) {
    $eventon_license[ $key ]['status'] = 'active';
    $eventon_license[ $key ]['key'] = '********-****-****-****-************';
    $eventon_license[ $key ]['remote_validity'] = 'valid';
}
update_option( '_evo_products', $eventon_license );

if ( ! defined( 'EVO_PLUGIN_FILE' ) ) {
	define( 'EVO_PLUGIN_FILE', __FILE__ );
}

// Include main EventON Class
if ( ! class_exists( 'EventON', false ) ) {
	include_once dirname( EVO_PLUGIN_FILE ) . '/includes/class-eventon.php';
}


// Returns the main instance of EVO
function EVO(){	
	return EventON::instance();
}

// Global for backwards compatibility
$GLOBALS['eventon'] = EVO();	

// From the sweet spot of the universe!
?>