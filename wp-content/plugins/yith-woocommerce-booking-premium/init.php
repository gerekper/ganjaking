<?php
/**
 * Plugin Name: YITH Booking and Appointment for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-booking/
 * Description: <code><strong>YITH Booking and Appointment for WooCommerce</strong></code> allows you to create and manage Booking Products. You can create monthly/daily/hourly/per-minute booking products with Services and People by setting costs and availability. You can also synchronize your booking products with external services such as Booking.com or Airbnb. Moreover, it includes Google Calendar integration, Google Maps, Search Forms, YITH Booking theme, and many other features! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 5.6.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-booking-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 * @version 5.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );


if ( ! defined( 'YITH_WCBK_PREMIUM' ) ) {
	define( 'YITH_WCBK_PREMIUM', true );
}

if ( ! defined( 'YITH_WCBK_INIT' ) ) {
	define( 'YITH_WCBK_INIT', plugin_basename( __FILE__ ) );
}

if ( defined( 'YITH_WCBK_VERSION' ) ) {
	return;
}

if ( ! defined( 'YITH_WCBK_VERSION' ) ) {
	define( 'YITH_WCBK_VERSION', '5.6.0' );
}

if ( ! defined( 'YITH_WCBK_FILE' ) ) {
	define( 'YITH_WCBK_FILE', __FILE__ );
}

require_once __DIR__ . '/init-global.php';
