<?php

/**
 * Plugin Name: WooChimp
 * Plugin URI: http://www.rightpress.net/woochimp
 * Description: MailChimp WooCommerce Integration
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 *
 * Text Domain: woochimp
 * Domain Path: /languages
 *
 * Version: 2.2.7
 *
 * Requires at least: 4.0
 * Tested up to: 5.4
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.3
 *
 * @package WooChimp
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('WOOCHIMP_PLUGIN_KEY', 'woochimp');
define('WOOCHIMP_PLUGIN_PUBLIC_PREFIX', 'woochimp_');
define('WOOCHIMP_PLUGIN_PRIVATE_PREFIX', 'woochimp_');
define('WOOCHIMP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WOOCHIMP_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('WOOCHIMP_SUPPORT_PHP', '5.3');
define('WOOCHIMP_SUPPORT_WP', '4.0');
define('WOOCHIMP_SUPPORT_WC', '3.0');
define('WOOCHIMP_VERSION', '2.2.7');

// Load main plugin class
require_once 'woochimp.class.php';

// Initialize automatic updates
require_once(plugin_dir_path(__FILE__) . 'rightpress-updates/rightpress-updates.class.php');
RightPress_Updates_6044286::init(__FILE__, WOOCHIMP_VERSION);
