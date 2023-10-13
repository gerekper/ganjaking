<?php
/**
 * Plugin Name: YITH Core Framework
 * Plugin URI: https://babia.to
 * Description: <code><strong>YITH Core Framework</strong></code>
 * Version: 4.4.2
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-framework
 * Domain Path: /languages/
 * WC requires at least: 7.9
 * WC tested up to: 8.1
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Framework
 * @version 4.4.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

require_once 'plugin-fw/yit-plugin-registration-hook.php';
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );
include_once 'plugin-upgrade/functions-yith-licence.php';
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );
require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';


yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
