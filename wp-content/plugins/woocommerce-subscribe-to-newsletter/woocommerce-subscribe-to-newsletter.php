<?php
/**
 * Plugin Name: WooCommerce Subscribe to Newsletter
 * Plugin URI: https://woocommerce.com/products/newsletter-subscription/
 * Description: Allow users to subscribe to your newsletter during checkout, when registering on your site, or via a sidebar widget.
 * Version: 3.5.1
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Requires PHP: 5.4
 * Requires at least: 4.4
 * Tested up to: 6.1
 * Text Domain: woocommerce-subscribe-to-newsletter
 * Domain Path: /languages/
 *
 * WC requires at least: 3.0
 * WC tested up to: 7.2
 * Woo: 18605:9b4ddf6c5bcc84c116ede70d840805fe
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Newsletter_Subscription
 * @since   2.3.5
 */

defined( 'ABSPATH' ) || exit;

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once dirname( __FILE__ ) . '/woo-includes/woo-functions.php';
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '9b4ddf6c5bcc84c116ede70d840805fe', '18605' );

/**
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_Newsletter_Subscription_Requirements', false ) ) {
	require_once dirname( __FILE__ ) . '/includes/class-wc-newsletter-subscription-requirements.php';
}

if ( ! WC_Newsletter_Subscription_Requirements::are_satisfied() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_NEWSLETTER_SUBSCRIPTION_FILE' ) ) {
	define( 'WC_NEWSLETTER_SUBSCRIPTION_FILE', __FILE__ );
}

// Include the main plugin class.
if ( ! class_exists( 'WC_Subscribe_To_Newsletter' ) ) {
	include_once dirname( WC_NEWSLETTER_SUBSCRIPTION_FILE ) . '/includes/class-wc-subscribe-to-newsletter.php';
}

// Global for backwards compatibility.
$GLOBALS['WC_Subscribe_To_Newsletter'] = WC_Subscribe_To_Newsletter::instance();
