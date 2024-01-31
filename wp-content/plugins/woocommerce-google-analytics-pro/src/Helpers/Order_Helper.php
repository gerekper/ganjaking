<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12\SV_WC_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_11_12\SV_WC_Order_Compatibility;
use WC_Order_Item;
use WC_Order_Item_Product;
use WC_Order_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Order helper class.
 *
 * Provides functionality to store and retrieve GA identities for orders and other helpful tidbits for ensuring
 * order tracking works as expected.
 *
 * @since 2.0.0
 */
class Order_Helper {


	/**
	 * Order helper constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// save GA identity to each order
		add_action( 'woocommerce_checkout_update_order_meta', [ self::class, 'store_ga_identity' ] );

		// save GA session params to each order
		add_action( 'woocommerce_checkout_update_order_meta', [ self::class, 'store_ga_session_params' ] );

		// mark the order as placed, which prevents us from tracking completed orders that were placed before GA Pro was enabled
		add_action( 'woocommerce_checkout_update_order_meta', [ self::class, 'add_order_placed_meta' ] );
	}


	/**
	 * Stores the GA Identity (CID) on an order.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order ID
	 * @param string|null $cid optional client identity to use, otherwise it will be generated
	 * @return string|null the set client identity on success or null on failure
	 */
	public static function store_ga_identity( int $order_id, ?string $cid = null ): ?string {

		if ( null === $cid ) {

			// get CID - ensuring that order will always have some kind of client id, so that
			// the transactions are properly tracked and reported in GA
			$cid = Identity_Helper::get_cid( true );
		}

		// store CID in order meta if it is not empty
		if ( ! empty( $cid ) ) {

			$cid = trim( $cid );

			SV_WC_Order_Compatibility::update_order_meta( $order_id, '_wc_google_analytics_pro_identity', $cid );
		}

		return ! is_string( $cid ) || '' === $cid ? null : $cid;
	}


	/**
	 * Stores the GA session parameters (ID, number) on an order.
	 *
	 * This helps us associate purchase events (which may happen asynchronous from placing the order)
	 * with the correct session.
	 *
	 * @since 2.0.9
	 *
	 * @param int $order_id the order ID
	 */
	public static function store_ga_session_params( int $order_id, ?array $params = [] ): void {

		$params = empty( $params ) ? Identity_Helper::get_session_params() : $params;

		// store session params in order meta if not empty
		if ( ! empty( $params ) ) {

			SV_WC_Order_Compatibility::update_order_meta( $order_id, '_wc_google_analytics_pro_session_params', $params );
		}
	}


	/**
	 * Gets the GA Identity associated with an order.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order ID
	 * @return string
	 */
	public static function get_order_ga_identity( int $order_id ): string {

		return SV_WC_Order_Compatibility::get_order_meta( $order_id, '_wc_google_analytics_pro_identity' );
	}


	/**
	 * Gets the GA session parameters associated with an order.
	 *
	 * @since 2.0.9
	 *
	 * @param int $order_id the order ID
	 * @return array {session_id?: int|string, session_number?: int|string}
	 */
	public static function get_order_ga_session_params( int $order_id ): array {

		$params = SV_WC_Order_Compatibility::get_order_meta( $order_id, '_wc_google_analytics_pro_session_params' );

		return ! empty( $params ) ? (array) $params : []; // avoid returning an empty string if the params are not set
	}


	/**
	 * Adds a meta to mark new orders as placed.
	 *
	 * The meta `_wc_google_analytics_pro_placed` helps prevent tracking completed orders that were placed before GA Pro was enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order object
	 */
	public static function add_order_placed_meta( int $order_id ): void {

		SV_WC_Order_Compatibility::update_order_meta( $order_id, '_wc_google_analytics_pro_placed', 'yes' );
	}


	/**
	 * Checks if the order is already tracked.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order object
	 * @return bool
	 */
	public static function is_order_tracked( int $order_id ) : bool {

		return wc_string_to_bool( SV_WC_Order_Compatibility::get_order_meta( $order_id, '_wc_google_analytics_pro_tracked' ) );
	}


	/**
	 * Checks if the order is already tracked in Universal Analytics.
	 *
	 * @UA
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order object
	 * @return bool
	 */
	public static function is_order_tracked_in_ua( int $order_id ) : bool {

		return wc_string_to_bool( SV_WC_Order_Compatibility::get_order_meta( $order_id, '_wc_google_analytics_pro_tracked_in_ua' ) );
	}


	/**
	 * Sets the order as tracked.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order object
	 */
	public static function set_order_tracked( int $order_id ) : void {

		SV_WC_Order_Compatibility::update_order_meta( $order_id, '_wc_google_analytics_pro_tracked', 'yes' );
	}


	/**
	 * Sets the order as tracked in Universal Analytics.
	 *
	 * @UA
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order object
	 */
	public static function set_order_tracked_in_ua( int $order_id ) : void {

		SV_WC_Order_Compatibility::update_order_meta( $order_id, '_wc_google_analytics_pro_tracked_in_ua', 'yes' );
	}


	/**
	 * Checks if the order was placed while GA Pro was enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order object
	 */
	public static function was_order_placed_while_ga_enabled(int $order_id ) : bool {

		return wc_string_to_bool( SV_WC_Order_Compatibility::get_order_meta( $order_id, '_wc_google_analytics_pro_placed' ) );
	}


	/**
	 * Gets the identities associated with a given order in the format useful for submission to Google Analytics.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order
	 * @return array
	 */
	public static function get_order_identities( \WC_Order $order ): array {

		$cid = self::get_order_ga_identity( $order->get_id() );

		return array(
			'cid' => $cid ?: Identity_Helper::get_cid(),
			'uid' => $order->get_customer_id( 'edit' ),
			'uip' => $order->get_customer_ip_address(),
			'ua'  => $order->get_customer_user_agent(),
		);
	}


	/**
	 * Gets the order item variant (comma separated list of order item variation attributes).
	 *
	 * @since 2.0.6
	 *
	 * @param WC_Order_Item $item the order item
	 * @return string
	 */
	public static function get_order_item_variant( WC_Order_Item $item ) : string
	{
		if ( $refunded_item_id = $item->get_meta('_refunded_item_id' ) ) {
			$item = WC_Order_Factory::get_order_item( $refunded_item_id );
		}

		if ( ! $item instanceof WC_Order_Item_Product ) {
			return '';
		}

		// return a comma separated list of non-empty order item variation attributes
		return implode( ', ', array_filter( array_map(
			static fn ( $item ) => $item->value,
			// only include variation attributes
			array_filter(
				$item->get_meta_data(),
				static fn( $meta_data ) => SV_WC_Helper::str_starts_with( $meta_data->key, 'pa_' )
			)
		) ) );
	}


}
