<?php
/**
 * Factory Pages
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @since         1.0.1
 * @package       core
 * @copyright (c) 2018, Webcraftic Ltd
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// module provides function only for the admin area
if ( ! is_admin() ) {
	return;
}

if ( defined( 'FACTORY_PAGES_423_LOADED' ) ) {
	return;
}

define( 'FACTORY_PAGES_423_LOADED', true );

define( 'FACTORY_PAGES_423_VERSION', '4.2.3' );

define( 'FACTORY_PAGES_423_DIR', dirname( __FILE__ ) );
define( 'FACTORY_PAGES_423_URL', plugins_url( null, __FILE__ ) );

if ( ! defined( 'FACTORY_FLAT_ADMIN' ) ) {
	define( 'FACTORY_FLAT_ADMIN', true );
}

load_plugin_textdomain( 'wbcr_factory_pages_423', false, dirname( plugin_basename( __FILE__ ) ) . '/langs' );

require( FACTORY_PAGES_423_DIR . '/pages.php' );
require( FACTORY_PAGES_423_DIR . '/includes/page.class.php' );
require( FACTORY_PAGES_423_DIR . '/includes/admin-page.class.php' );
require( FACTORY_PAGES_423_DIR . '/templates/impressive-page.class.php' );

