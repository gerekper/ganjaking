<?php
/**
 * Plugin Name: EventON
 * Plugin URI: http://www.myeventon.com/
 * Secret Key: 83a5bb0e2ad5164690bc7a42ae592cf5
 * Description: A beautifully crafted minimal calendar experience
 * Version: 4.1.3
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 5.5
 * Tested up to: 6.0.2
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

$nm_eventon_options = get_option( '_evo_products' );
foreach ( $nm_eventon_options as $key => $value ) {
	$nm_eventon_options[ $key ]['status']          = 'active';
	$nm_eventon_options[ $key ]['key']             = '1415b451be1a13c283ba771ea52d38bb';
	$nm_eventon_options[ $key ]['remote_validity'] = 'valid';
}
update_option( '_evo_products', $nm_eventon_options );
// main eventon class
if ( ! class_exists( 'EventON' ) ) {
	
if ( ! defined( 'EVO_PLUGIN_FILE' ) ) {
	define( 'EVO_PLUGIN_FILE', __FILE__ );
}
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