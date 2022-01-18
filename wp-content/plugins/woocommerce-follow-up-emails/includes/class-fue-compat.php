<?php
/**
 * WooCommerce Plugin Compatibility
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
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_FUE_Compatibility' ) ) :

	/**
	 * WooCommerce Compatibility Utility Class
	 *
	 * The unfortunate purpose of this class is to provide a single point of
	 * compatibility functions for dealing with supporting multiple versions
	 * of WooCommerce.
	 *
	 * The recommended procedure is to rename this file/class, replacing "my plugin"
	 * with the particular plugin name, so as to avoid clashes between plugins.
	 * Over time we expect to remove methods from this class, using the current
	 * ones directly, as support for older versions of WooCommerce is dropped.
	 *
	 * Current Compatibility: 2.1.x - 2.2
	 *
	 * @version 2.0
	 */
	class WC_FUE_Compatibility {


		/**
		 * Get the WC Order instance for a given order ID or order post
		 *
		 * Introduced in WC 2.2 as part of the Order Factory so the 2.1 version is
		 * not an exact replacement.
		 *
		 * If no param is passed, it will use the global post. Otherwise pass an
		 * the order post ID or post object.
		 *
		 * @since 2.0.0
		 * @param bool|int|string|\WP_Post $the_order
		 * @return bool|\WC_Order
		 */
		public static function wc_get_order( $the_order = false ) {
			return wc_get_order( $the_order );
		}

		/**
		 * Get the user ID for an order
		 *
		 * @since 2.0.0
		 * @param \WC_Order $order
		 * @return int
		 */
		public static function get_order_user_id( $order ) {

			if ( is_numeric( $order ) ) {
				$order = self::wc_get_order( $order );
			}

			if ( ! $order ) {
				return 0;
			}

			return $order->get_user_id();
		}


		/**
		 * Get the user for an order
		 *
		 * @since 2.0.0
		 * @param \WC_Order $order
		 * @return bool|WP_User
		 */
		public static function get_order_user( $order ) {

			if ( is_numeric( $order ) ) {
				$order = self::wc_get_order( $order );
			}

			return $order->get_user();
		}

		/**
		 * The the Order's status
		 * @param WC_Order $order
		 * @return string
		 */
		public static function get_order_status( $order ) {

			if ( is_numeric( $order ) ) {
				$order = self::wc_get_order( $order );
			}

			return $order->get_status();
		}

		/**
		 * Get the WC Product instance for a given product ID or post
		 *
		 * get_product() is soft-deprecated in WC 2.2
		 *
		 * @since 2.0.0
		 * @param bool|int|string|\WP_Post $the_product
		 * @param array $args
		 * @return WC_Product
		 */
		public static function wc_get_product( $the_product = false, $args = array() ) {
			return wc_get_product( $the_product, $args );
		}

		/**
		 * Get order property with compatibility check on order getter introduced
		 * in WC 3.0.
		 *
		 * @since 4.4.19
		 *
		 * @param WC_Order $order Order object.
		 * @param string   $prop  Property name.
		 *
		 * @return mixed Property value
		 */
		public static function get_order_prop( $order, $prop ) {
			$modifier = function ( $a ) {
				return $a;
			};

			switch ( $prop ) {
				case 'order_total':
					$getter = array( $order, 'get_total' );
					break;
				case 'post':
					$getter = array( $order, 'get_id' );
					$modifier = function ( $a ) {
						return get_post( $a );
					};
					break;
				case 'completed_date':
					$getter = array( $order, 'get_date_completed' );
					$modifier = function ( $wc_date_time  ) {
						return is_a( $wc_date_time, 'WC_DateTime' ) ? date( 'Y-m-d H:i:s', $wc_date_time->getTimestamp() ) : '';
					};
					break;
				case 'order_date':
					$getter = array( $order, 'get_date_created' );
					$modifier = function ( $wc_date_time  ) {
						return is_a( $wc_date_time, 'WC_DateTime' ) ? date( 'Y-m-d H:i:s', $wc_date_time->getTimestamp() ) : '';
					};
					break;
				case 'customer_user':
					$getter = array( $order, 'get_customer_id' );
					break;
				default:
					$getter = array( $order, 'get_' . $prop );
					break;
			}

			return is_callable( $getter ) ? $modifier( call_user_func( $getter ) ) : $order->{ $prop };
		}
	}

endif; // Class exists check
