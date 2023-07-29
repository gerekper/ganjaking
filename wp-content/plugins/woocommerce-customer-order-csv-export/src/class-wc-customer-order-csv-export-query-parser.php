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

defined( 'ABSPATH' ) or exit;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Utilities\OrderUtil;
use SkyVerge\WooCommerce\CSV_Export\Taxonomies_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Query Parser
 *
 * Parses an export query to exportable object IDs
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_Query_Parser {


	/**
	 * Get object IDs to export based on the input query
	 *
	 * @since 4.0.0
	 * @param array $query
	 * @param string $export_type Export type, `orders`, `customers` or `coupons`
	 * @param string $output_type Output type, `csv` or `xml`
	 * @return array
	 */
	public static function parse_export_query( $query, $export_type, $output_type = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		$ids = [];

		switch ( $export_type ) {

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:

				$ids = self::parse_orders_export_query( $query, $export_type, $output_type );
			break;

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:

				$ids = self::parse_customers_export_query( $query, $output_type );
			break;

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS:

				$ids = self::parse_coupons_export_query( $query, $output_type );
				break;
		}

		/**
		 * Filters the parsed query results for the given output type.
		 *
		 * Allows actors to adjust the parsed query results or provide results for custom export types.
		 *
		 * @since 5.0.0
		 *
		 * @param array $ids array of resulting object IDs
		 * @param array $query export query
		 * @param string $export_type
		 */
		$ids = apply_filters( "wc_customer_order_export_parsed_{$output_type}_query_results", $ids, $query, $export_type );

		/**
		 * Filters the parsed query results.
		 *
		 * Allows actors to adjust the parsed query results or provide results for custom export types.
		 *
		 * @since 5.0.0
		 *
		 * @param array $ids array of resulting object IDs
		 * @param array $query export query
		 * @param string $export_type
		 */
		return apply_filters( 'wc_customer_order_export_parsed_query_results', $ids, $query, $export_type, $output_type );
	}


	/**
	 * Parse export query for orders
	 *
	 * This method will also be used for getting orders IDs when exporting
	 * guest customers.
	 *
	 * @since 4.0.0
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
	 * @param string $output_type optional, defaults to `csv`
	 * @return array
	 */
	public static function parse_orders_export_query( $query, $export_type = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) : array {

		global $wpdb;

		if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) {
			$default_order_statuses = array_keys( (array) wc_get_order_statuses() );
		} else {
			$default_order_statuses = 'any';
		}

		$hpos_enabled = Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled();

		$query_args = [
			'fields'         => 'ids',
			'post_type'      => 'shop_order',
			'post_status'    => ! empty( $query['statuses'] ) && WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ? (array) $query['statuses'] : $default_order_statuses,
			'posts_per_page' => ! empty( $query['limit'] ) ? max( 1, (int) $query['limit'] ) : -1,
			'offset'         => empty( $query['offset'] ) ? 0 : absint( $query['offset'] ),
			'date_query'  => [
				[
					'before'    => empty( $query['end_date'] )   ? date( 'Y-m-d 23:59', current_time( 'timestamp' ) ) : $query['end_date'] . ' 23:59:59.99',
					'after'     => empty( $query['start_date'] ) ? date( 'Y-m-d 00:00', 0 ) : $query['start_date'],
					'inclusive' => true,
				],
			],
		];

		// allow offset to be used with "no" posts limit
		if ( $query_args['offset'] > 0 && -1 === $query_args['posts_per_page'] ) {
			$query_args['posts_per_page'] = 999999999999; // a really large number {@link http://dev.mysql.com/doc/refman/5.7/en/select.html#idm140195560794688}
		}

		// only include orders with guest customers
		if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export_type ) {

			$query_args['meta_query'] = [
				[
					'key'   => '_customer_user',
					'value' => 0
				],
			];

			if ( ! empty( $query['exclude_billing_emails'] ) ) {

				$query_args['meta_query'][] = [
					'key'     => '_billing_email',
					'value'   => $query['exclude_billing_emails'],
					'compare' => 'NOT IN',
				];
			}
		}

		if ( ! empty( $query['not_exported'] ) ) {

			$term_slugs_to_exclude = [
				// exclude globally exported orders/customers
				Taxonomies_Handler::GLOBAL_TERM,
			];

			if ( ! empty( $query['automation_id'] ) ) {

				// excluded orders/customers exported for this automation
				$term_slugs_to_exclude[] = Taxonomies_Handler::TERM_PREFIX . $query['automation_id'];
			}

			if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export_type ) {
				$taxonomy = Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER;
			} else {
				$taxonomy = Taxonomies_Handler::TAXONOMY_NAME_ORDERS;
			}

			if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type && Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ) {

				// WC_Orders_Query does not support tax_query, so we need to include/exclude exported orders manually
				$query_args[ 'post__not_in' ] = ( new WP_Query([
					'post_type'      => Framework\SV_WC_Order_Compatibility::get_order_post_types(),
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'tax_query'      => [
						'relation'   => 'OR', // required to exclude orders in _any_ of the given terms
						[
							'taxonomy' => $taxonomy,
							'terms'    => $term_slugs_to_exclude,
							'field'    => 'slug',
						],
					],
				]) )->get_posts();

			} else {

				$exclude_exported = [
					'relation' => 'OR',
					[
						// exclude orders/customers exported globally or for this automation
						'taxonomy' => $taxonomy,
						'terms'    => $term_slugs_to_exclude,
						'field'    => 'slug',
						'operator' => 'NOT IN',
					],
					[
						// include orders/customers never exported
						'taxonomy' => $taxonomy,
						'operator' => 'NOT EXISTS',
					]
				];

				$query_args['tax_query'] = $exclude_exported;
			}

		}

		if ( 'orders' === $export_type && isset( $_POST['export_query']['refunds'] ) && 'only_refunds' === $_POST['export_query']['refunds'] ) {

			// we don't need the refund's ID, just order IDs
			if ($hpos_enabled) {

				// wc_get_orders() does not support the `id=>parent` return value, so we have to use a custom query
				$orders_table     = OrdersTableDataStore::get_orders_table_name();
				$refund_order_ids = $wpdb->get_col(
					"
						SELECT DISTINCT parent_order_id
						FROM {$orders_table}
						WHERE type = 'shop_order_refund'
					"
				);
			} else {
				$refund_order_ids = array_unique( get_posts( [
					'fields'      => 'id=>parent',
					'nopaging'    => true,
					'post_type'   => 'shop_order_refund',
					'post_status' => 'any',
				] ) );
			}

			// [0] below will produce no results as no matching refunds were found
			$query_args['post__in'] = ! empty( $refund_order_ids ) ? $refund_order_ids : [0];
		}

		/**
		 * Filters the \WP_Query args used for selecting orders to export based on a query.
		 *
		 * These query args affect both orders and customers, as guest customers are exported
		 * from orders, not from the users table.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $export_type such as orders, customers, or coupons
		 */
		$query_args = apply_filters( "wc_customer_order_export_{$output_type}_query_args", $query_args, $export_type );

		/**
		 * Filters the \WP_Query args used for selecting orders to export based on a query.
		 *
		 * These query args affect both orders and customers, as guest customers are exported
		 * from orders, not from the users table.
		 *
		 * In 4.0.0 removed $this param, renamed from `wc_customer_order_csv_export_admin_query_args`
		 * to `wc_customer_order_csv_export_query_args`, moved here from WC_Customer_Order_Export_Admin class
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $export_type such as orders, customers, or coupons
		 * @param string $output_type such as csv or xml
		 */
		$query_args = apply_filters( 'wc_customer_order_export_query_args', $query_args, $export_type, $output_type );

		/**
		 * Fires before running the WP_Query for orders export to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $export_type such as orders, customers, or coupons
		 */
		do_action( "wc_customer_order_export_before_{$output_type}_orders_query", $query_args, $export_type );

		/**
		 * Fires before running the WP_Query for orders export
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $export_type such as orders, customers, or coupons
		 * @param string $output_type such as csv or xml
		 */
		do_action( 'wc_customer_order_export_before_orders_query', $query_args, $export_type, $output_type );

		// get order IDs
		if ( $hpos_enabled ) {
			// note that we can't use wc_get_orders without HPOS support, because before WC 7.0.0, it did not support
			// advanced queries like meta_query, date_query etc: https://github.com/woocommerce/woocommerce/wiki/HPOS:-new-order-querying-APIs
			$order_ids = wc_get_orders( self::map_wc_orders_query_args( $query_args ) );
		} else {
			$order_query = new WP_Query( $query_args );
			$order_ids   = $order_query->posts;
		}

		/**
		 * Fires after running the WP_Query for orders export to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $export_type such as orders, customers, or coupons
		 */
		do_action( "wc_customer_order_export_after_{$output_type}_orders_query", $query_args, $export_type );

		/**
		 * Fires after running the WP_Query for orders export to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $export_type such as orders, customers, or coupons
		 * @param string $output_type such as csv or xml
		 */
		do_action( 'wc_customer_order_export_after_orders_query', $query_args, $export_type, $output_type );

		// filter order IDs based on additional filtering criteria (products and product categories)
		if ( ! empty( $order_ids ) && ! empty( $query['products'] ) ) {

			$order_ids = self::filter_orders_containing_products( $order_ids, $query['products'] );

		}

		if ( ! empty( $order_ids ) && ! empty( $query['product_categories'] ) ) {

			$order_ids = self::filter_orders_containing_product_categories( $order_ids, $query['product_categories'] );
		}

		// handle subscription & renewal order filtering
		if ( wc_customer_order_csv_export()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			$subscriptions = $renewals = [];

			if ( isset( $query['subscription_orders'] ) ) {
				$subscriptions = self::filter_orders_containing_subscriptions( $order_ids, 'subscriptions' );
			}

			if ( isset( $query['subscription_renewals'] ) ) {
				$renewals = self::filter_orders_containing_subscriptions( $order_ids, 'renewals' );
			}

			if ( isset( $query['subscription_orders'] ) || isset( $query['subscription_renewals'] ) ) {
				$order_ids = array_unique( array_merge( $subscriptions, $renewals ) );
			}
		}

		return $order_ids;
	}


	/**
	 * Filters provided order IDs containing subscriptions or subscription renewals.
	 *
	 * @since 4.4.5
	 *
	 * @see \wcs_order_contains_subscription()
	 * @see \wcs_order_contains_renewal()
	 *
	 * @param int[] $order_ids array of order IDs
	 * @param string $which whether to filter 'subscriptions' or 'renewals'
	 * @return int[]
	 */
	private static function filter_orders_containing_subscriptions( $order_ids, $which ) {

		$order_ids    = is_array( $order_ids ) ? array_filter( array_map( 'absint', $order_ids ) ) : [];
		$filtered_ids = [];

		if ( ! empty( $order_ids ) ) {

			if ( 'subscriptions' === $which ) {

				// unfortunately, there does not appear to be a way to query and return only subscription order IDs when
				// using wcs_get_subscriptions(), so we fall back to get_posts for now
				$subscription_orders = get_posts( [
					'nopaging'        => true,
					'post_status'     => 'any',
					'fields'          => 'id=>parent',
					'post_type'       => 'shop_subscription',
					'post_parent__in' => $order_ids,
				] );

				if ( ! empty( $subscription_orders ) ) {

					foreach ( $subscription_orders as $subscription_order ) {

						$order_id = current( (array) $subscription_order );

						if ( is_numeric( $order_id ) && in_array( $order_id, $order_ids, false ) ) {
							$filtered_ids[] = (int) $order_id;
						}
					}
				}

			} elseif ( 'renewals' === $which ) {

				$filtered_ids = wc_get_orders( [
					'limit'    => -1,
					'status'   => 'any',
					'return'   => 'ids',
					'type'     => 'shop_order',
					'meta_key' => '_subscription_renewal',
					'post__in' => $order_ids, // caveat: `post__in` works in all supported WC versions, while `id` works only in WC7+
				] );
			}
		}

		return array_filter( $filtered_ids );
	}


	/**
	 * Parse export query for customers
	 *
	 * @since 4.0.0
	 * @param array $query {
	 *                 The export query args. All params are optional.
	 *
	 *                 @type bool $not_exported Whether to include only non-exported orders
	 *                 @type string $start_date minimum order date
	 *                 @type string $end_date maximum order date
	 * }
	 * @param string $output_type the output type, either `csv` or `xml`
	 * @return array of mixed items. int $id for registered customers, array with keys of `email` and `order_id` for guest customers
	 */
	public static function parse_customers_export_query( $query, $output_type = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {
		global $wpdb;

        $term_slugs_to_exclude = [];

		$query_args = [
			// will exclude shop employees for stores using WP 4.4+
			'role__not_in' => [ 'administrator', 'shop_manager' ],
			'date_query'   => [
				[
					'before'    => empty( $query['end_date'] )   ? date( 'Y-m-d 23:59', current_time( 'timestamp' ) ) : $query['end_date'] . ' 23:59:59.99',
					'after'     => empty( $query['start_date'] ) ? date( 'Y-m-d 00:00', 0 ) : $query['start_date'],
					'inclusive' => true,
				],
			],
		];

		if ( ! empty( $query['not_exported'] ) ) {

			// exclude customers with exported terms (WP_User_Query does not support tax_query yet)
			// @see https://core.trac.wordpress.org/ticket/31383

			$term_slugs_to_exclude = [
				// exclude globally exported customers
				Taxonomies_Handler::GLOBAL_TERM,
			];
			if ( ! empty( $query['automation_id'] ) ) {
				// excluded customers exported for this automation
				$term_slugs_to_exclude[] = Taxonomies_Handler::TERM_PREFIX . $query['automation_id'];
			}

			$terms_to_exclude = [];
			foreach ( $term_slugs_to_exclude as $term_slug ) {
				$term               = get_term_by( 'slug', $term_slug, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER, 'ARRAY_A' );
				$terms_to_exclude[] = $term['term_id'];
			}
			$customers_to_exclude = get_objects_in_term( $terms_to_exclude, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER );

			$query_args['exclude'] = $customers_to_exclude;
		}

		/**
		 * Filters the \WP_User_Query args used for selecting customers to export based on a query.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_User_Query arguments
		 */
		$query_args = apply_filters( "wc_customer_order_export_{$output_type}_user_query_args", $query_args );

		/**
		 * Filters the \WP_User_Query args used for selecting customers to export based on a query.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_User_Query arguments
		 * @param string $output_type such as csv or xml
		 */
		$query_args = apply_filters( 'wc_customer_order_export_user_query_args', $query_args, $output_type );

		/**
		 * Fires before running the \WP_User_Query for customers export to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_User_Query arguments
		 */
		do_action( "wc_customer_order_export_before_{$output_type}_users_query", $query_args );

		/**
		 * Fires before running the \WP_User_Query for customers export.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_User_Query arguments
		 * @param string $output_type such as csv or xml
		 */
		do_action( 'wc_customer_order_export_before_users_query', $query_args, $output_type );

		$users = get_users( $query_args );

		/**
		 * Fires after running the \WP_User_Query for customers export to the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_User_Query arguments
		 */
		do_action( "wc_customer_order_export_after_{$output_type}_users_query", $query_args );

		/**
		 * Fires after running the \WP_User_Query for customers export.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_User_Query arguments
		 * @param string $output_type such as csv or xml
		 */
		do_action( 'wc_customer_order_export_after_users_query', $query_args, $output_type );

		$customers = [];

		// Exclude registered customers from guest customer query
		$query['exclude_billing_emails'] = [];

		foreach ( $users as $user ) {

			$customers[] = $user->ID;

			if ( isset( $user->user_email ) ) {
				$query['exclude_billing_emails'][] = $user->user_email;
			}
		}

		// to export guest customers, we need to fetch them from orders...
		// please, Lord, make https://trello.com/c/4Ll0X3pL/44-separate-customers-from-user-accounts
		// come true soon!
		$order_ids = self::parse_orders_export_query( $query, WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS, $output_type );

		foreach ( $order_ids as $order_id ) {

			$billing_email = Framework\SV_WC_Order_Compatibility::get_order_meta( $order_id, '_billing_email' );

			// skip orders without a billing email
			if ( ! $billing_email ) {
				continue;
			}

			if ( ! empty( $query['not_exported'] ) ) {

				// escape the term values
				$term_slugs_to_exclude = array_map( 'esc_sql', (array) $term_slugs_to_exclude );
				// comma separated string with string placeholders (%s)
				$terms_placeholder_string = implode( ', ', array_fill( 0, count( $term_slugs_to_exclude ), '%s' ) );

				// check if a registered customer with this billing address has already been exported
				$is_exported = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT u.ID
						FROM $wpdb->users u
						LEFT JOIN $wpdb->term_relationships tr ON u.ID = tr.object_id
						LEFT JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
						LEFT JOIN $wpdb->terms t ON tt.term_id = t.term_id
						WHERE u.user_email = %s
						AND tt.taxonomy = %s
						AND t.slug IN ( $terms_placeholder_string )",
						array_merge(
							[
								$billing_email,
								Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER,
							],
							$term_slugs_to_exclude
						)
					)
				);

				// skip customers already exported as registered customers
				if ( $is_exported ) {
					continue;
				}

				// find orders with the same billing address, for which the customer has already been exported

				$term_slugs = [
					// globally exported guest customers
					Taxonomies_Handler::GLOBAL_TERM,
				];

				if ( ! empty( $query['automation_id'] ) ) {

					// guest customers exported for this automation
					$term_slugs[] = Taxonomies_Handler::TERM_PREFIX . $query['automation_id'];
				}

				$args = [
					'fields'         => 'ids',
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'meta_query'     => [
						[
							'key'   => '_billing_email',
							'value' => $billing_email,
						],
					],
					'tax_query' => [
						[
							'taxonomy' => Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER,
							'terms'    => $term_slugs,
							'field'    => 'slug',
							'operator' => 'IN',
						]
					]
				];

				if (Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled()) {
					$exported_orders = wc_get_orders( static::map_wc_orders_query_args( $args ) );
				} else {
					$exported_orders = ( new WP_Query( $args ) )->posts;
				}

				// skip customers that have already been exported from another order
				if ( count( $exported_orders ) > 0 ) {
					continue;
				}

			}

			// uniquely identify a guest customer based on their billing email and related order id
			$customers[] = [ $billing_email, $order_id ];
		}

		return $customers;
	}


	/**
	 * Filter provided order IDs based on whether they contain provided products
	 *
	 * @since 4.0.0
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
	 * @since 4.0.0
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
	 * Parse export query for coupons.
	 *
	 * @since 4.6.0
	 *
	 * @param array $query {
	 *                 The export query args. All params are optional.
	 *
	 *                 @type array $product_categories
	 *                             Array of product categories coupons must
	 *                             support to be exported
	 * }
	 * @param string $output_type such as csv or xml
	 * @return array of coupon IDs
	 */
	public static function parse_coupons_export_query( $query, $output_type = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		$query_args = [
			'fields'         => 'ids',
			'post_type'      => 'shop_coupon',
			'posts_per_page' => -1,
		];

		/**
		 * Filters the \WP_Query args used for selecting coupons to export based on a query.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 */
		$query_args = apply_filters( "wc_customer_order_export_{$output_type}_coupon_query_args", $query_args );

		/**
		 * Filters the \WP_Query args used for selecting coupons to export based on a query.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $output_type such as csv or xml
		 */
		$query_args = apply_filters( 'wc_customer_order_export_coupon_query_args', $query_args, $output_type );

		/**
		 * Fires before running the \WP_Query for coupons export.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $output_type such as csv or xml
		 */
		do_action( 'wc_customer_order_export_before_coupons_query', $query_args, $output_type );

		// get coupon IDs
		$coupon_query = new WP_Query( $query_args );
		$coupon_ids   = $coupon_query->posts;

		/**
		 * Fires after running the \WP_Query for coupons export.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_args \WP_Query arguments
		 * @param string $output_type such as csv or xml
		 */
		do_action( 'wc_customer_order_export_after_coupons_query', $query_args, $output_type );

		// filter coupon IDs based on additional filtering criteria (products and product categories)
		if ( ! empty( $coupon_ids ) && ! empty( $query['coupon_products'] ) ) {

			$coupon_products = ( is_array( $query['coupon_products'] ) ) ? $query['coupon_products'] : [ $query['coupon_products'] ];

			$coupon_ids = self::filter_coupons_for_products( $coupon_ids, $coupon_products );

		}

		if ( ! empty( $coupon_ids ) && ! empty( $query['coupon_product_categories'] ) ) {

			$coupon_product_categories = ( is_array( $query['coupon_product_categories'] ) ) ? $query['coupon_product_categories'] : [ $query['coupon_product_categories'] ];

			$coupon_ids = self::filter_coupons_for_product_categories( $coupon_ids, $coupon_product_categories );

		}

		return $coupon_ids;
	}


	/**
	 * Filter provided coupon IDs based on whether they apply to provided products.
	 *
	 * @since 4.6.0
	 *
	 * @param array $coupon_ids Array of coupon IDs
	 * @param array $product_ids Array of product IDs
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
		$filtered_coupon_ids = [];

		foreach ( $coupons as $coupon ) {

			$coupon_product_ids = explode( ',', $coupon->meta_value );

			if ( ! empty( $coupon_product_ids ) && ! empty( array_intersect( $coupon_product_ids, $product_ids ) ) ) {

				$filtered_coupon_ids[] = $coupon->post_id;

			}

		}

		return $filtered_coupon_ids;
	}


	/**
	 * Filter provided coupon IDs based on whether they apply to provided categories.
	 *
	 * @since 4.6.0
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
		$filtered_coupon_ids = [];

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
	 * Accepts either a comma-separated string of IDs or an array of IDs
	 *
	 * @since 4.0.0
	 * @param array|string $ids IDs
	 * @return string comma-separated list of IDs
	 */
	private static function get_sanitized_id_list( $ids ) {
		return implode( ',', array_map( 'absint', is_string( $ids ) ? explode( ',', $ids ) : $ids ) );
	}


	/**
	 * Maps a subset of WP_Query args to WC_Orders_Query args.
	 *
	 * The main purpose of this method is to ensure wc_get_orders will respect the `fields` argument. We also map other
	 * query args that we use ourselves, but bot all of them, because OrdersTableQuery will handle the rest.
	 *
	 * @since 5.5.0
	 *
	 * @param array $query_args
	 * @return array
	 */
	protected static function map_wc_orders_query_args( array $query_args = [] ): array {

		$mapping = [
			'posts_per_page' => 'limit',
			'post__in'       => 'ids',
			'post_type'      => 'type',
			'post_status'    => 'status',
			'fields'         => 'return', // crucial - if this is not mapped, wc_get_orders will return full order objects
		];

		foreach ($mapping as $query_key => $table_field) {
			if (isset( $query_args[$query_key] ) && '' !== $query_args[$query_key]) {
				$query_args[$table_field] = $query_args[$query_key];
				unset( $query_args[$query_key] );
			}
		}

		return $query_args;
	}


}
