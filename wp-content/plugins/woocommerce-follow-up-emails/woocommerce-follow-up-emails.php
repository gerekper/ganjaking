<?php
/**
 * Plugin Name: Follow-Up Emails
 * Plugin URI: https://woocommerce.com/products/follow-up-emails/
 * Description: Automate your email marketing, and create scheduled newletters to drive customer engagement for WordPress, WooCommerce, and Sensei.
 * Version: 4.9.18
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text domain: follow_up_emails
 * Tested up to: 5.8
 * WC tested up to: 6.1
 * WC requires at least: 3.0
 *
 * Woo: 18686:05ece68fe94558e65278fe54d9ec84d2
 * Copyright: © 2022 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/** Path and URL constants **/
define( 'FUE_VERSION', '4.9.18' ); // WRCS: DEFINED_VERSION.
define( 'FUE_KEY', 'aHR0cDovLzc1bmluZXRlZW4uY29tL2Z1ZS5waH' );
define( 'FUE_FILE', __FILE__ );
define( 'FUE_URL', plugins_url( '', __FILE__ ) );
define( 'FUE_DIR', plugin_dir_path( __FILE__ ) );
define( 'FUE_INC_DIR', FUE_DIR . 'includes' );
define( 'FUE_INC_URL', FUE_URL . '/includes' );
define( 'FUE_ADDONS_DIR', FUE_DIR . '/addons' );
define( 'FUE_ADDONS_URL', FUE_URL . '/addons' );
define( 'FUE_TEMPLATES_DIR', FUE_DIR . 'templates' );
define( 'FUE_TEMPLATES_URL', FUE_URL . '/templates' );

load_plugin_textdomain( 'follow_up_emails', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

global $fue, $wpdb;
require_once FUE_INC_DIR . '/class-follow-up-emails.php';
$fue = new Follow_Up_Emails( $wpdb );

add_action( 'admin_init', function() {
	require_once FUE_INC_DIR . '/class-fue-privacy.php';
} );

if ( ! function_exists( 'FUE' ) ) :
	/**
	 * Returns an instance of the Follow_Up_Emails class
	 * @since 5.0
	 * @return Follow_Up_Emails
	 */
	function FUE() {
		return Follow_Up_Emails::instance();
	}
endif;
