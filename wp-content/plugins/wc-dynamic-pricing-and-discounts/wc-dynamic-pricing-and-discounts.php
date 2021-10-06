<?php

/**
 * Plugin Name: WooCommerce Dynamic Pricing & Discounts
 * Plugin URI: http://www.rightpress.net/woocommerce-dynamic-pricing-and-discounts
 * Description: All-purpose product pricing, cart discount and checkout fee tool for WooCommerce
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 *
 * Text Domain: rp_wcdpd
 * Domain Path: /languages
 *
 * Version: 2.4
 *
 * Requires at least: 4.0
 * Tested up to: 5.7
 *
 * WC requires at least: 3.0
 * WC tested up to: 5.4
 *
 * @package WooCommerce Dynamic Pricing & Discounts
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('RP_WCDPD_PLUGIN_KEY', 'wc-dynamic-pricing-and-discounts');
define('RP_WCDPD_PLUGIN_PUBLIC_PREFIX', 'rp_wcdpd_');
define('RP_WCDPD_PLUGIN_PRIVATE_PREFIX', 'rp_wcdpd_');
define('RP_WCDPD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RP_WCDPD_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('RP_WCDPD_ADMIN_CAPABILITY', 'manage_rp_wcdpd');
define('RP_WCDPD_SUPPORT_PHP', '5.3');
define('RP_WCDPD_SUPPORT_WP', '4.0');
define('RP_WCDPD_SUPPORT_WC', '3.0');
define('RP_WCDPD_VERSION', '2.4');

// Load main plugin class
require_once 'rp_wcdpd.class.php';

// Initialize automatic updates
require_once(RP_WCDPD_PLUGIN_PATH . 'rightpress-updates/rightpress-updates.class.php');
RightPress_Updates_7119279::init(__FILE__, RP_WCDPD_VERSION);
