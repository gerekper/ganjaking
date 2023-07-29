<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order Export taxonomies handler.
 *
 * @since 5.0.0
 */
class Taxonomies_Handler {


	/** @var string custom taxonomy used for exported orders */
	const TAXONOMY_NAME_ORDERS = 'wc_export_is_order_exported';

	/** @var string custom taxonomy used for exported users (customers export) */
	const TAXONOMY_NAME_USER_CUSTOMER = 'wc_export_is_user_exported';

	/** @var string custom taxonomy used for exported orders' guest customers (customers export) */
	const TAXONOMY_NAME_GUEST_CUSTOMER = 'wc_export_is_guest_exported';

	/** @var string term used for globally exported orders/customers */
	const GLOBAL_TERM = 'global';

	/** @var string prefix used for non-global exported orders/customers */
	const TERM_PREFIX = 'automation_';


	/**
	 * Initializes the taxonomy handler.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {

		add_action( 'woocommerce_register_taxonomy', [ $this, 'register_is_exported_taxonomies' ] );
	}


	/**
	 * Registers taxonomies to mark objects as exported,
	 * globally (for an export type) or for a given automated export.
	 *
	 * @internal
	 *
	 * @since 5.0.0
	 */
	public function register_is_exported_taxonomies() {

		$args = [
			'hierarchical'       => false,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'show_in_nav_menus'  => false,
		];

		/**
		 * Filters the taxonomy arguments.
		 *
		 * @since 5.0.0
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'wc_customer_order_export_taxonomy_args', $args );

		register_taxonomy( self::TAXONOMY_NAME_ORDERS, 'shop_order', $args );
		register_taxonomy( self::TAXONOMY_NAME_GUEST_CUSTOMER, 'shop_order', $args );
		register_taxonomy( self::TAXONOMY_NAME_USER_CUSTOMER, 'user', $args );

		// add global terms
		wp_insert_term( self::GLOBAL_TERM, self::TAXONOMY_NAME_ORDERS );
		wp_insert_term( self::GLOBAL_TERM, self::TAXONOMY_NAME_GUEST_CUSTOMER );
		wp_insert_term( self::GLOBAL_TERM, self::TAXONOMY_NAME_USER_CUSTOMER );
	}


	/**
	 * Gets an array of exported automation IDs for the given taxonomy and object ID.
	 *
	 * @since 5.0.0
	 *
	 * @param int $object_id the object ID to check, either an order ID or a user ID
	 * @param string $taxonomy
	 * @return string[]
	 */
	public static function get_exported_automation_ids( $object_id, $taxonomy ) {

		$automation_ids = [];
		$object_terms   = wp_get_object_terms( $object_id, $taxonomy );

		if ( is_array( $object_terms ) ) {
			foreach ( $object_terms as $object_term ) {

				if ( isset( $object_term->name ) && 0 === strpos( $object_term->name, self::TERM_PREFIX ) ) {

					$automation_ids[] = substr( $object_term->name, strlen( self::TERM_PREFIX ) );
				}
			}
		}

		return $automation_ids;
	}


	/**
	 * Determines if the given object ID is exported globally in the given taxonomy.
	 *
	 * @since 5.0.0
	 *
	 * @param int $object_id object ID - either an order ID or customer ID
	 * @param string $taxonomy taxonomy string, e.g. self::TAXONOMY_NAME_ORDERS
	 * @return bool
	 */
	public static function is_exported_globally( $object_id, $taxonomy ) {

		$is_exported_globally = is_object_in_term( $object_id, $taxonomy, self::GLOBAL_TERM );

		return is_wp_error( $is_exported_globally ) ? false : $is_exported_globally;
	}


	/**
	 * Gets an array of automation IDs that have been exported for a given order ID.
	 *
	 * @since 5.0.0
	 *
	 * @param int $order_id
	 * @return string[]
	 */
	public static function get_exported_automation_ids_for_order( $order_id ) {

		return self::get_exported_automation_ids( $order_id, self::TAXONOMY_NAME_ORDERS );
	}


	/**
	 * Gets an array of automation IDs that have been exported for a guest customer of the given order ID.
	 *
	 * @since 5.0.0
	 *
	 * @param int $order_id
	 * @return string[]
	 */
	public static function get_exported_automation_ids_for_guest_customer( $order_id ) {

		return self::get_exported_automation_ids( $order_id, self::TAXONOMY_NAME_GUEST_CUSTOMER );
	}


	/**
	 * Gets an array of automation IDs that have been exported for a given customer ID.
	 *
	 * @since 5.0.0
	 *
	 * @param int $customer_id the WP_User ID
	 * @return string[]
	 */
	public static function get_exported_automation_ids_for_customer( $customer_id ) {

		return self::get_exported_automation_ids( $customer_id, self::TAXONOMY_NAME_USER_CUSTOMER );
	}


	/**
	 * Checks if the order has been exported globally.
	 *
	 * @since 5.0.0
	 *
	 * @param int $order_id the order ID
	 * @return bool
	 */
	public static function is_order_exported_globally( $order_id ) {

		return self::is_exported_globally( $order_id, self::TAXONOMY_NAME_ORDERS );
	}


	/**
	 * Checks if the guest customer for a given order ID has been exported globally.
	 *
	 * @since 5.0.0
	 *
	 * @param int $order_id the order ID
	 * @return bool
	 */
	public static function is_guest_customer_exported_globally( $order_id ) {

		return self::is_exported_globally( $order_id, self::TAXONOMY_NAME_GUEST_CUSTOMER );
	}


	/**
	 * Checks if the customer has been exported globally.
	 *
	 * @since 5.0.0
	 *
	 * @param int $customer_id the customer (WP_User) ID
	 * @return bool
	 */
	public static function is_customer_exported_globally( $customer_id ) {

		return self::is_exported_globally( $customer_id, self::TAXONOMY_NAME_USER_CUSTOMER );
	}


}
