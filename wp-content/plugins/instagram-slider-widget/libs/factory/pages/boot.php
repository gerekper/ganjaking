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
if( !defined('ABSPATH') ) {
	exit;
}

// module provides function only for the admin area
if( !is_admin() ) {
	return;
}

if( defined('FACTORY_PAGES_438_LOADED') ) {
	return;
}

define('FACTORY_PAGES_438_LOADED', true);

define('FACTORY_PAGES_438_VERSION', '4.3.8');

define('FACTORY_PAGES_438_DIR', dirname(__FILE__));
define('FACTORY_PAGES_438_URL', plugins_url(null, __FILE__));

if( !defined('FACTORY_FLAT_ADMIN') ) {
	define('FACTORY_FLAT_ADMIN', true);
}

load_plugin_textdomain('wbcr_factory_pages_438', false, dirname(plugin_basename(__FILE__)) . '/langs');

require(FACTORY_PAGES_438_DIR . '/pages.php');
require(FACTORY_PAGES_438_DIR . '/includes/page.class.php');
require(FACTORY_PAGES_438_DIR . '/includes/admin-page.class.php');
require(FACTORY_PAGES_438_DIR . '/templates/impressive-page.class.php');

