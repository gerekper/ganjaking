<?php
/**
 * Plugin Name: WooCommerce Shipment Tracking
 * Plugin URI: https://woocommerce.com/products/shipment-tracking/
 * Description: Add tracking numbers to orders allowing customers to track their orders via a link. Supports many shipping providers, as well as custom ones if necessary via a regular link.
 * Version: 2.4.4
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-shipment-tracking
 * Domain Path: /languages
 * WC requires at least: 8.3
 * WC tested up to: 8.5
 * Tested up to: 6.4
 *
 * Copyright: © 2024 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Shipment_Tracking
 *
 * Woo: 18693:1968e199038a8a001c9f9966fd06bf88
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.6.20
 * @return void
 */
function woocommerce_shipment_tracking_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Shipment Tracking requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-shipment-tracking' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

define( 'WC_SHIPMENT_TRACKING_FILE', __FILE__ );
define( 'WC_SHIPMENT_TRACKING_DIR', __DIR__ );

/**
 * WC_Shipment_Tracking class
 */
if ( ! class_exists( 'WC_Shipment_Tracking' ) ) :
	define( 'WC_SHIPMENT_TRACKING_VERSION', '2.4.4' ); // WRCS: DEFINED_VERSION.

	require_once WC_SHIPMENT_TRACKING_DIR . '/includes/class-wc-shipment-tracking.php';
endif;

add_action( 'plugins_loaded', 'woocommerce_shipment_tracking_init' );

/**
 * Initializes the extension.
 *
 * @since 1.6.20
 * @return void
 */
function woocommerce_shipment_tracking_init() {
	load_plugin_textdomain( 'woocommerce-shipment-tracking', false, plugin_basename( dirname( WC_SHIPMENT_TRACKING_FILE ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipment_tracking_missing_wc_notice' );
		return;
	}

	$GLOBALS['WC_Shipment_Tracking'] = wc_shipment_tracking();
}

/**
 * Returns an instance of WC_Shipment_Tracking.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return WC_Shipment_Tracking
 */
function wc_shipment_tracking() {
	static $instance;

	if ( ! isset( $instance ) ) {
		$instance = new WC_Shipment_Tracking();
	}

	return $instance;
}

/**
 * Adds a tracking number to an order.
 *
 * @param int         $order_id        The order id of the order you want to
 *                                     attach this tracking number to.
 * @param string      $tracking_number The tracking number.
 * @param string      $provider        The tracking provider. If you use one
 *                                     from `WC_Shipment_Tracking_Actions::get_providers()`,
 *                                     the tracking url will be taken case of.
 * @param int         $date_shipped    The timestamp of the shipped date.
 *                                     This is optional, if not set it will
 *                                     use current time.
 * @param bool|string $custom_url      If you are not using a provder from
 *                                     `WC_Shipment_Tracking_Actions::get_providers()`,
 *                                     you can add a url for tracking here.
 *                                     This is optional.
 */
function wc_st_add_tracking_number( $order_id, $tracking_number, $provider, $date_shipped = null, $custom_url = false ) {
	if ( ! $date_shipped ) {
		$date_shipped = gmdate( 'U' );
	}

	$st            = WC_Shipment_Tracking_Actions::get_instance();
	$provider_list = $st->get_providers();
	$custom        = true;
	$provider_slug = sanitize_title( str_replace( ' ', '', wc_st_get_provider_alias( $provider ) ) );
	// Check if a given `$provider` is predefined or custom.
	foreach ( $provider_list as $country ) {
		foreach ( $country as $provider_code => $url ) {
			if ( sanitize_title( str_replace( ' ', '', $provider_code ) ) === $provider_slug ) {
				$provider = sanitize_title( $provider_code );
				$custom   = false;
				break;
			}
		}

		if ( ! $custom ) {
			break;
		}
	}

	if ( $custom ) {
		$args = array(
			'tracking_provider'        => '',
			'custom_tracking_provider' => $provider,
			'custom_tracking_link'     => $custom_url,
			'tracking_number'          => $tracking_number,
			'date_shipped'             => gmdate( 'Y-m-d', $date_shipped ),
		);
	} else {
		$args = array(
			'tracking_provider'        => $provider,
			'custom_tracking_provider' => '',
			'custom_tracking_link'     => '',
			'tracking_number'          => $tracking_number,
			'date_shipped'             => gmdate( 'Y-m-d', $date_shipped ),
		);
	}

	$st->add_tracking_item( $order_id, $args );
}

/**
 * Deletes tracking information based on tracking_number relating to an order.
 *
 * @param int    $order_id        Order ID.
 * @param string $tracking_number The tracking number to be deleted.
 * @param string $provider        You can filter the delete by specifying a
 *                                tracking provider. This is optional.
 */
function wc_st_delete_tracking_number( $order_id, $tracking_number, $provider = false ) {
	$st = WC_Shipment_Tracking_Actions::get_instance();

	$tracking_items = $st->get_tracking_items( $order_id );

	if ( count( $tracking_items ) > 0 ) {
		foreach ( $tracking_items as $item ) {
			if ( ! $provider ) {
				if ( $item['tracking_number'] === $tracking_number ) {
					$st->delete_tracking_item( $order_id, $item['tracking_id'] );
					return true;
				}
			} else {
				if ( $item['tracking_number'] === $tracking_number && ( sanitize_title( $provider ) === $item['tracking_provider'] || sanitize_title( $provider ) === $item['custom_tracking_provider'] ) ) {
					$st->delete_tracking_item( $order_id, $item['tracking_id'] );
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Declaring HPOS compatibility.
 */
function wc_st_declare_hpos_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php', true );
	}
}
add_action( 'before_woocommerce_init', 'wc_st_declare_hpos_compatibility' );

/**
 * Declare cart/checkout blocks compatibility.
 */
function wc_st_declare_cart_checkout_blocks_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'wc_st_declare_cart_checkout_blocks_compatibility' );

/**
 * Get the alias name of provider.
 *
 * @param string $provider Provider name.
 *
 * @return string.
 */
function wc_st_get_provider_alias( $provider ) {
	/**
	 * Filter to add an alias for shipment tracking provider.
	 *
	 * @param array List of provider alias.
	 *
	 * @since 2.4.0
	 */
	$provider_aliases = apply_filters(
		'wc_shipment_tracking_provider_alias',
		array(
			'United Kingdom' => array(
				'DPD Local' => 'dpd',
			),
		)
	);

	foreach ( $provider_aliases as $country => $providers ) {
		foreach ( $providers as $provider_code => $alias ) {
			if ( strtolower( $alias ) === strtolower( $provider ) ) {
				return $provider_code;
			}
		}
	}

	return $provider;
}
