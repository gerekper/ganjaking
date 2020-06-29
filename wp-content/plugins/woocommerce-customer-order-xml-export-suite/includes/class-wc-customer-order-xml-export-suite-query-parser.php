<?php
/**
 * WooCommerce Customer/Order XML Export Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Query Parser
 *
 * Parses an export query to exportable object IDs
 *
 * @since 2.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Query_Parser {

	/**
	 * Get object IDs to export based on the input query
	 *
	 * @since 2.0.0
	 * @param array $query
	 * @param string $export_type Export type, 'orders', 'customers', or 'coupons'
	 * @return array
	 */
	public static function parse_export_query( $query, $export_type ) {

		$ids = array();

		switch ( $export_type ) {

			case 'orders':

				$ids = self::parse_orders_export_query( $query );
			break;

			case 'customers':

				$ids = self::parse_customers_export_query( $query );
			break;

			case 'coupons':

				$ids = self::parse_coupons_export_query( $query );
			break;
		}

		/**
		 * Allow actors to adjust the parsed query results, or provide results for custom export types
		 *
		 * @since 2.0.0
		 * @param array $ids array of resulting object IDs
		 * @param array $query export query
		 * @param string $export_type
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_parsed_query_results', $ids, $query, $export_type );
	}


	/**
	 * Parse export query for orders
	 *
	 * This method will also be used for getting orders IDs when exporting
	 * guest customers.
	 *
	 * @since 2.0.0
	 * @param array $query {
	 *                 The export query args. All params are optional.
	 *
	 *                 @type bool $not_exported Whether to include only non-exported orders
	 *                 @type array $statuses Array of order statuses to export
	 *                 @type string|array $products Array or comma-delimited string of
	 *                                              product IDs orders should have
	 *                 @type array $product_categories Array of product category IDs the
	 *                                                 orders should have
	 *                 @type string $start_date minimum order date
	 *                 @type string $end_date maximum order date
	 *                 @type int $limit maximum number of orders to export
	 *                 @type int $offset offset for the order query
	 * }
	 * @param string $export_type optional, defaults to `orders`
	 * @return array
	 */
	public static function parse_orders_export_query( $query, $export_type = 'orders' ) {

		$query_args = array(
			'fields'         => 'ids',
			'post_type'      => 'shop_order',
			'post_status'    => ( ! empty( $query['statuses'] ) && 'orders' === $export_type ) ? (array) $query['statuses'] : 'any',
			'posts_per_page' => ! empty( $query['limit'] ) ? max( 1, (int) $query['limit'] ) : -1,
			'offset'         => empty( $query['offset'] ) ? 0 : absint( $query['offset'] ),
			'date_query'  => array(
				array(
					'before'    => empty( $query['end_date'] )   ? date( 'Y-m-d 23:59', current_time( 'timestamp' ) ) : $query['end_date'] . ' 23:59:59.99',
					'after'     => empty( $query['start_date'] ) ? date( 'Y-m-d 00:00', 0 ) : $query['start_date'],
					'inclusive' => true,
				),
			),
		);

		// allow offset to be used with "no" posts limit
		if ( $query_args['offset'] > 0 && -1 === $query_args['posts_per_page'] ) {
			$query_args['posts_per_page'] = 999999999999; // a really large number {@link http://dev.mysql.com/doc/refman/5.7/en/select.html#idm140195560794688}
		}

		// only include orders with guest customers
		if ( 'customers' === $export_type ) {

			$query_args['meta_query'] = array(
				array(
					'key'   => '_customer_user',
					'value' => 0
				),
			);

			if ( ! empty( $query['exclude_billing_emails'] ) ) {

				$query_args['meta_query'][] = array(
					'key'     => '_billing_email',
					'value'   => $query['exclude_billing_emails'],
					'compare' => 'NOT IN',
				);
			}
		}

		if ( ! empty( $query['not_exported'] ) ) {

			if ( ! isset( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = array();
			}

			$exclude_exported          = array();
			$exclude_exported['key']   = 'customers' === $export_type ? '_wc_customer_order_xml_export_suite_customer_is_exported' : '_wc_customer_order_xml_export_suite_is_exported';
			$exclude_exported['value'] = 0;

			$query_args['meta_query'][] = $exclude_exported;
		}

		/**
		 * Allow actors to change the WP_Query args used for selecting orders to export based on a query.
		 *
		 * These query args affect both orders and customers, as guest customers are exported
		 * from orders, not from the users table.
		 *
		 * In 2.0.0 removed $this param, renamed from `wc_customer_order_xml_export_suite_admin_query_args`
		 * to `wc_customer_order_xml_export_suite_query_args`, moved here from WC_Customer_Order_Export_Admin class
		 *
		 * @since 1.2.7
		 * @param array $query_args - WP_Query arguments
		 * @param string $export_type - either `customers` or `orders`
		 */
		$query_args = apply_filters( 'wc_customer_order_xml_export_suite_query_args', $query_args, $export_type );

		/**
		 * Fires before running the WP_Query for orders export
		 *
		 * @since 2.0.0
		 * @param array $query_args
		 * @param string $export_type - either `customers` or `orders`
		 */
		do_action( 'wc_customer_order_xml_export_suite_before_orders_query', $query_args, $export_type );

		// get order IDs
		$order_query = new WP_Query( $query_args );
		$order_ids   = $order_query->posts;

		/**
		 * Fires after running the WP_Query for orders export
		 *
		 * @since 2.0.0
		 * @param array $query_args
		 * @param string $export_type - either `customers` or `orders`
		 */
		do_action( 'wc_customer_order_xml_export_suite_after_orders_query', $query_args, $export_type );


		// filter order IDs based on additional filtering criteria (products and product categories)
		if ( ! empty( $order_ids ) && ! empty( $query['products'] ) ) {

			$order_ids = self::filter_orders_containing_products( $order_ids, $query['products'] );

		}

		if ( ! empty( $order_ids ) && ! empty( $query['product_categories'] ) ) {

			$order_ids = self::filter_orders_containing_product_categories( $order_ids, $query['product_categories'] );
		}


		// handle subscription & renewal order filtering
		if ( wc_customer_order_xml_export_suite()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			$subscriptions = $renewals = array();

			if ( isset( $query['subscription_orders'] ) ) {

				if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {
					$subscriptions = array_filter( $order_ids, 'wcs_order_contains_subscription' );
				} else {
					$subscriptions = array_filter( $order_ids, array( 'WC_Subscriptions_Order', 'order_contains_subscription' ) );
				}
			}

			if ( isset( $query['subscription_renewals'] ) ) {

				if ( SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte_2_0() ) {
					$renewals = array_filter( $order_ids, 'wcs_order_contains_renewal' );
				} else {
					$renewals = array_filter( $order_ids, array( 'WC_Subscriptions_Renewal_Order', 'is_renewal' ) );
				}
			}

			if ( ! empty( $subscriptions ) || ! empty( $renewals ) ) {
				$order_ids = array_merge( $subscriptions, $renewals );
			}
		}

		return $order_ids;
	}


	/**
	 * Parse export query for customers
	 *
	 * @since 2.0.0
	 * @param array $query {
	 *                 The export query args. All params are optional.
	 *
	 *                 @type bool $not_exported Whether to include only non-exported orders
	 *                 @type string $start_date minimum order date
	 *                 @type string $end_date maximum order date
	 * }
	 * @return array of mixed items. int $id for registered customers, array with keys of `email` and `order_id` for guest customers
	 */
	public static function parse_customers_export_query( $query ) {
		global $wpdb;

		$query_args = array(
			// will exclude shop employees for stores using WP 4.4+
			'role__not_in' => array( 'administrator', 'shop_manager' ),
			'date_query'   => array(
				array(
					'before'    => empty( $query['end_date'] )   ? date( 'Y-m-d 23:59', current_time( 'timestamp' ) ) : $query['end_date'] . ' 23:59:59.99',
					'after'     => empty( $query['start_date'] ) ? date( 'Y-m-d 00:00', 0 ) : $query['start_date'],
					'inclusive' => true,
				),
			),
		);

		if ( ! empty( $query['not_exported'] ) ) {

			$query_args['meta_key']   = '_wc_customer_order_xml_export_suite_is_exported';
			$query_args['meta_value'] = 0;
		}

		/**
		 * Allow actors to change the WP_User_Query args used for selecting customers to export based on a query.
		 *
		 * @since 2.0.0
		 * @param array $query_args - WP_User_Query arguments
		 */
		$query_args = apply_filters( 'wc_customer_order_xml_export_suite_user_query_args', $query_args );

		/**
		 * Fires before running the WP_User_Query for customers export
		 *
		 * @since 2.0.0
		 * @param array $query_args
		 */
		do_action( 'wc_customer_order_xml_export_suite_before_users_query', $query_args );

		$users = get_users( $query_args );

		/**
		 * Fires after running the WP_User_Query for customers export
		 *
		 * @since 2.0.0
		 * @param array $query_args
		 */
		do_action( 'wc_customer_order_xml_export_suite_after_users_query', $query_args );

		$customers = array();

		// Exclude registered customers from guest customer query
		$query['exclude_billing_emails'] = array();

		foreach ( $users as $user ) {

			$customers[] = $user->ID;

			if ( isset( $user->user_email ) ) {
				$query['exclude_billing_emails'][] = $user->user_email;
			}
		}


		// to export guest customers, we need to fetch them from orders...
		// please, Lord, make https://trello.com/c/4Ll0X3pL/44-separate-customers-from-user-accounts
		// come true soon!
		$order_ids = self::parse_orders_export_query( $query, 'customers' );

		foreach ( $order_ids as $order_id ) {

			$billing_email = get_post_meta( $order_id, '_billing_email', true );

			// skip orders without a billing email
			if ( ! $billing_email ) {
				continue;
			}

			if ( ! empty( $query['not_exported'] ) ) {

				// check if a registered customer with this billing address has already been exported
				$is_exported = $wpdb->get_var( $wpdb->prepare( "
					SELECT u.ID
					FROM $wpdb->users u
					LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id
					WHERE u.user_email = %s
					AND um.meta_key = '_wc_customer_order_xml_export_suite_is_exported'
					AND CAST( um.meta_value AS CHAR ) = '1'
				", $billing_email ) );

				// skip customers already exported as registered customers
				if ( $is_exported ) {
					continue;
				}

				// find orders with the same billing address, for which the customer has already been exported
				$exported_orders = new WP_Query( array(
					'fields'         => 'ids',
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'meta_query'     => array(
						array(
							'key'   => '_billing_email',
							'value' => $billing_email,
						),
						array(
							'key'   => '_wc_customer_order_xml_export_suite_customer_is_exported',
							'value' => 1,
						),
					),
				) );

				// skip customers that have already been exported from another order
				if ( count( $exported_orders->posts ) > 0 ) {
					continue;
				}

			}

			// uniquely identify a guest customer based on their billing email and related order id
			$customers[] = array( $billing_email, $order_id );
		}

		return $customers;
	}


	/**
	 * Parses export query for coupons.
	 *
	 * @since 2.5.0
	 *
	 * @param array $query {
	 *                 The export query args. All params are optional.
	 *
	 *                 @type bool $not_exported Whether to include only non-exported orders
	 * }
	 * @return array of coupon IDs
	 */
	public static function parse_coupons_export_query( $query ) {

		$query_args = array(
			'fields'         => 'ids',
			'post_type'      => 'shop_coupon',
			'posts_per_page' => -1,
		);

		/**
		 * Filters the query args used for getting coupons during a coupon export.
		 *
		 * @since 2.5.0
		 *
		 * @param array $query_args query args
		 */
		$query_args = apply_filters( 'wc_customer_order_xml_export_coupon_query_args', $query_args );

		// get coupon IDs
		$coupon_query = new WP_Query( $query_args );
		$coupon_ids   = $coupon_query->posts;

		// filter coupon IDs based on additional filtering criteria (products and product categories)
		if ( ! empty( $coupon_ids ) && ! empty( $query['coupon_products'] ) ) {

			$coupon_products = ( is_array( $query['coupon_products'] ) ) ? $query['coupon_products'] : array( $query['coupon_products'] );

			$coupon_ids = self::filter_coupons_for_products( $coupon_ids, $coupon_products );
		}

		if ( ! empty( $coupon_ids ) && ! empty( $query['coupon_product_categories'] ) ) {

			$coupon_product_categories = ( is_array( $query['coupon_product_categories'] ) ) ? $query['coupon_product_categories'] : array( $query['coupon_product_categories'] );

			$coupon_ids  = self::filter_coupons_for_product_categories( $coupon_ids, $coupon_product_categories );
		}

		return $coupon_ids;
	}


	/**
	 * Filter provided order IDs based on whether they contain provided products
	 *
	 * @since 2.0.0
	 * @param string|array $order_ids A comma-separated list or array of order IDs
	 * @param string|array $product_ids A comma-separated list or array of product IDs
	 * @return array
	 */
	public static function filter_orders_containing_products( $order_ids, $product_ids ) {

		global $wpdb;

		$order_id_list   = self::get_sanitized_id_list( $order_ids );
		$product_id_list = self::get_sanitized_id_list( $product_ids );

		return $wpdb->get_col( "SELECT DISTINCT order_id
			FROM {$wpdb->prefix}woocommerce_order_items items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta im ON items.order_item_id = im.order_item_id
			WHERE items.order_id IN ( {$order_id_list} )
			AND items.order_item_type = 'line_item'
			AND im.meta_key IN ( '_product_id', '_variation_id' )
			AND im.meta_value IN ( {$product_id_list} )
		" );
	}


	/**
	 * Filter provided order IDs based on whether they contain
	 * products in the provided categories
	 *
	 * @since 2.0.0
	 * @param string|array $order_ids A comma-separated list or array of order IDs
	 * @param string|array $product_categories A comma-separated list or array of product category IDs
	 * @return array
	 */
	public static function filter_orders_containing_product_categories( $order_ids, $product_categories ) {

		global $wpdb;

		$order_id_list    = self::get_sanitized_id_list( $order_ids );
		$product_cat_list = self::get_sanitized_id_list( $product_categories );

		return $wpdb->get_col( "SELECT DISTINCT order_id
			FROM {$wpdb->prefix}woocommerce_order_items items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta im ON items.order_item_id = im.order_item_id
			LEFT JOIN {$wpdb->term_relationships} tr ON im.meta_value = tr.object_id
			LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			WHERE items.order_id IN ( {$order_id_list} )
			AND items.order_item_type = 'line_item'
			AND im.meta_key = '_product_id'
			AND tt.taxonomy = 'product_cat'
			AND tt.term_id IN ( {$product_cat_list} )
		" );
	}

	/**
	 * Filters coupon IDs based on whether they apply to provided products.
	 *
	 * @since 2.5.0
	 *
	 * @param array $coupon_ids Array of coupon IDs
	 * @param array $product_ids Array of product IDs
	 *
	 * @return array
	 */
	public static function filter_coupons_for_products( $coupon_ids, $product_ids ) {
		global $wpdb;

		$coupon_id_list = self::get_sanitized_id_list( $coupon_ids );

		// applicable products are stored as post metadata
		$coupons = $wpdb->get_results( "SELECT meta.post_id, meta.meta_value
			FROM {$wpdb->prefix}postmeta meta
			WHERE meta.post_id IN ( {$coupon_id_list} )
			AND meta.meta_key = 'product_ids'
		" );

		// filter out coupons that don't apply to at least one provided product
		$filtered_coupon_ids = array();

		foreach ( $coupons as $coupon ) {

			$coupon_product_ids = explode( ',', $coupon->meta_value );

			if ( ! empty( $coupon_product_ids ) && ! empty( array_intersect( $coupon_product_ids, $product_ids ) ) ) {

				$filtered_coupon_ids[] = $coupon->post_id;

			}

		}

		return $filtered_coupon_ids;
	}

	/**
	 * Filters provided coupon IDs for to provided categories.
	 *
	 * @since 2.5.0
	 *
	 * @param array $coupon_ids Array of coupon IDs
	 * @param array $product_category_ids Array of product category IDs
	 * @return array
	 */
	public static function filter_coupons_for_product_categories( $coupon_ids, $product_category_ids ) {
		global $wpdb;

		$coupon_id_list = self::get_sanitized_id_list( $coupon_ids );

		// applicable product categories are stored as post metadata
		$coupons = $wpdb->get_results( "SELECT meta.post_id, meta.meta_value
			FROM {$wpdb->prefix}postmeta meta
			WHERE meta.post_id IN ( {$coupon_id_list} )
			AND meta.meta_key = 'product_categories'
		" );

		// filter out coupons that don't apply to at least one provided product category
		$filtered_coupon_ids = array();

		foreach ( $coupons as $coupon ) {

			$coupon_category_ids = unserialize( $coupon->meta_value );

			if ( ! empty( $coupon_category_ids ) && ! empty( array_intersect( $coupon_category_ids, $product_category_ids ) ) ) {

				$filtered_coupon_ids[] = $coupon->post_id;

			}

		}

		return $filtered_coupon_ids;
	}


	/**
	 * Sanitize a list of IDs
	 *
	 * Passes each ID through `absint()` to ensure integer ID values.
	 * Accepts wither a comma-separated string of IDs or an array of IDs
	 *
	 * @since 2.0.0
	 * @param array|string $ids IDs
	 * @return string comma-separated list of IDs
	 */
	private static function get_sanitized_id_list( $ids ) {
		return implode( ',', array_map( 'absint', is_string( $ids ) ? explode( ',', $ids ) : $ids ) );
	}

}
