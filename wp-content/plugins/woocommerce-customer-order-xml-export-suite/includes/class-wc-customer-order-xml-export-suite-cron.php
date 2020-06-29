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
 * Customer/Order XML Export Suite Cron Class
 *
 * Adds custom schedule and schedules the export event, as well as cleaning up
 * of expired exports.
 *
 * @since 1.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Cron {


	/**
	 * Setup hooks and filters specific to WP-cron functions
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add custom schedule, e.g. every 10 minutes
		add_filter( 'cron_schedules', array( $this, 'add_auto_export_schedules' ) );

		// Schedule auto-update events if they don't exist, run in both frontend and
		// backend so events are still scheduled when an admin reactivates the plugin
		add_action( 'init', array( $this, 'add_scheduled_export' ) );

		// schedule cleanup of expired exports
		add_action( 'init', array( $this, 'schedule_export_cleanup' ) );

		// cleanup expired exports
		add_action( 'wc_customer_order_xml_export_suite_scheduled_export_cleanup', array( $this, 'cleanup_exports' ) );

		// Trigger export + upload of non-exported orders, wp-cron fires this action
		// on the given recurring schedule
		add_action( 'wc_customer_order_xml_export_suite_auto_export_orders',    array( $this, 'auto_export_orders' ) );
		add_action( 'wc_customer_order_xml_export_suite_auto_export_customers', array( $this, 'auto_export_customers' ) );

		// trigger order export when an order is processed or status updates
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'auto_export_order' ) );
		add_action( 'woocommerce_order_status_changed',     array( $this, 'auto_export_order' ) );
	}


	/**
	 * Check if auto-exports are enabled
	 *
	 * @since 2.0.0
	 * @param string $export_type
	 * @return bool
	 */
	public function exports_enabled( $export_type ) {
		return (bool) wc_customer_order_xml_export_suite()->get_methods_instance()->get_auto_export_method( $export_type );
	}


	/**
	 * Check if scheduled auto-exports are enabled
	 *
	 * @since 2.0.0
	 * @param string $export_type
	 * @return bool
	 */
	public function scheduled_exports_enabled( $export_type ) {

		$exports_enabled = $this->exports_enabled( $export_type );

		if ( $exports_enabled && 'orders' === $export_type ) {
			$exports_enabled = ( 'schedule' === get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_trigger' ) );
		}

		return $exports_enabled;
	}


	/**
	 * If automatic schedule exports are enabled, add the custom interval
	 * (e.g. every 15 minutes) set on the admin settings page
	 *
	 * In 2.0.0 renamed from `add_auto_export_schedule` to `add_auto_export_schedules`
	 *
	 * @since 1.0.0
	 * @param array $schedules WP-Cron schedules array
	 * @return array $schedules now including our custom schedule
	 */
	public function add_auto_export_schedules( $schedules ) {

		foreach ( array( 'orders', 'customers' ) as $export_type ) {

			if ( $this->scheduled_exports_enabled( $export_type ) ) {

				$export_interval = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_interval' );

				if ( $export_interval ) {

					$schedules[ 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_interval' ] = array(
						'interval' => (int) $export_interval * 60,
						'display'  => sprintf( _n(  'Every minute', 'Every %d minutes', (int) $export_interval, 'woocommerce-customer-order-xml-export-suite' ), (int) $export_interval )
					);
				}
			}
		}

		return $schedules;
	}


	/**
	 * If automatic scheduled exports are enabled, add the event if not already scheduled
	 *
	 * This performs a `do_action( 'wc_customer_order_xml_export_suite_auto_export_orders' )`
	 * on our custom schedule
	 *
	 * @since 1.0.0
	 */
	public function add_scheduled_export() {

		foreach ( array( 'orders', 'customers' ) as $export_type ) {

			if ( $this->scheduled_exports_enabled( $export_type ) ) {

				// Schedule export
				if ( ! wp_next_scheduled( 'wc_customer_order_xml_export_suite_auto_export_' . $export_type ) ) {

					$start_time = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_start_time' );
					$curr_time  = current_time( 'timestamp' );

					if ( $start_time ) {

						if ( $curr_time > strtotime( 'today ' . $start_time, $curr_time ) ) {

							$start_timestamp = strtotime( 'tomorrow ' . $start_time, $curr_time ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

						} else {

							$start_timestamp = strtotime( 'today ' . $start_time, $curr_time ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
						}

					} else {

						$export_interval = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_interval' );
						$start_timestamp = strtotime( "now +{$export_interval} minutes" );
					}

					wp_schedule_event( $start_timestamp, 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_interval', 'wc_customer_order_xml_export_suite_auto_export_' . $export_type );
				}
			}
		}
	}


	/**
	 * Exports any non-exported orders and performs the chosen action
	 * (upload, HTTP POST, email)
	 *
	 * @since 1.1.0
	 */
	public function auto_export_orders() {

		$export_method = get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_method' );

		if ( ! $export_method ) {
			return;
		}

		/**
		 * Allow actors to adjust whether only new orders should be included in auto-exports or not
		 *
		 * @since 2.0.0
		 * @param bool $new_only defaults to true
		 */
		$export_new_orders_only = apply_filters( 'wc_customer_order_xml_export_suite_auto_export_new_orders_only', true );

		require_once( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-query-parser.php' );

		$order_ids = WC_Customer_Order_XML_Export_Suite_Query_Parser::parse_orders_export_query( array(
			'statuses'           => get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_statuses' ),
			'products'           => get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_products' ),
			'product_categories' => get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_product_categories' ),
			'not_exported'       => $export_new_orders_only,
		) );

		if ( ! empty( $order_ids ) ) {

			$args = array(
				'type'       => 'orders',
				'method'     => $export_method,
				'invocation' => 'auto',
			);

			if ( $this->is_duplicate_export( $order_ids, $args ) ) {
				return;
			}

			/**
			 * Filters the order IDs that are auto-exported.
			 *
			 * @since 2.4.0
			 *
			 * @param int[] $order_ids the order ids being auto-exported
			 */
			$order_ids = apply_filters( 'wc_customer_order_xml_export_suite_auto_export_ids', $order_ids );

			try {

				wc_customer_order_xml_export_suite()->get_export_handler_instance()->start_export( $order_ids, $args );

			} catch ( SV_WC_Plugin_Exception $e ) {

				// log errors
				/* translators: Placeholders: %s - error message */
				wc_customer_order_xml_export_suite()->log( sprintf( esc_html__( 'Scheduled orders export failed: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ) );
			}
		}

	}


	/**
	 * Exports any non-exported orders and performs the chosen action
	 * (upload, HTTP POST, email)
	 *
	 * @since 2.0.0
	 */
	public function auto_export_customers() {

		$export_method = get_option( 'wc_customer_order_xml_export_suite_customers_auto_export_method' );

		if ( ! $export_method ) {
			return;
		}

		/**
		 * Allow actors to adjust whether only new customers should be included in auto-exports or not
		 *
		 * @since 2.0.0
		 * @param bool $new_only defaults to true
		 */
		$export_new_customers_only = apply_filters( 'wc_customer_order_xml_export_suite_auto_export_new_customers_only', true );

		require_once( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-query-parser.php' );

		$customers = WC_Customer_Order_XML_Export_Suite_Query_Parser::parse_customers_export_query( array(
			'not_exported' => $export_new_customers_only,
		) );

		if ( ! empty( $customers ) ) {

			$args = array(
				'type'       => 'customers',
				'method'     => $export_method,
				'invocation' => 'auto',
			);

			if ( $this->is_duplicate_export( $customers, $args ) ) {
				return;
			}

			/**
			 * Filters the customers that are going to be auto-exported.
			 *
			 * @since 2.4.0
			 *
			 * @param array $customers the customers being auto-exported
			 *     @type int|array int customer id for registered customers, array with keys `email` and `order_id` for guests
			 */
			$customers = apply_filters( 'wc_customer_order_xml_export_suite_auto_export_customers', $customers );

			try {

				wc_customer_order_xml_export_suite()->get_export_handler_instance()->start_export( $customers, $args );

			} catch ( SV_WC_Plugin_Exception $e ) {

				// log errors
				/* translators: Placeholders: %s - error message */
				wc_customer_order_xml_export_suite()->log( sprintf( esc_html__( 'Scheduled customers export failed: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ) );

				// Notify the admin that exports are failing
				$failure_notices = get_option( 'wc_customer_order_xml_export_suite_failure_notices', array() );

				$failure_notices['export'] = array( 'multiple_failures' => true );

				update_option( 'wc_customer_order_xml_export_suite_failure_notices', $failure_notices );

			}
		}
	}


	/**
	 * Determines if a potential new export job is a duplicate of one already in
	 * the queue.
	 *
	 * This serves as a way to combat against cron events being fired multiple
	 * times, as can happen in the wonderful world of WordPress. Here we:
	 *
	 *     1. Generate a fingerprint for the new job based on the args and current time.
	 *     2. Delay processing for a small amount of random time
	 *     3. Check if there are any existing jobs with a matching fingerprint
	 *     4. Whichever concurrent request finished first will block the other from processing
	 *
	 * @since 2.3.0
	 *
	 * @param array $object_ids order or customer IDs
	 * @param array $export_args
	 * @return bool
	 */
	protected function is_duplicate_export( $object_ids, $export_args ) {

		$timestamp    = time();
		$fingerprint  = $this->generate_auto_export_fingerprint( $object_ids, $export_args, $timestamp );
		$is_duplicate = false;

		// add a random artificial delay
		usleep( rand( 250000, 500000 ) );

		if ( $existing_exports = wc_customer_order_xml_export_suite()->get_export_handler_instance()->get_exports( array( 'status' => array( 'queued', 'processing' ), ) ) ) {

			foreach ( $existing_exports as $export ) {

				$export_args = array(
					'type'       => $export->type,
					'method'     => $export->method,
					'invocation' => $export->invocation,
				);

				if ( hash_equals( $fingerprint, $this->generate_auto_export_fingerprint( $export->object_ids, $export_args, $timestamp ) ) ) {
					$is_duplicate = true;
				}
			}
		}

		return $is_duplicate;
	}


	/**
	 * Generates a fingerprint hash for new export jobs to help detect duplicates.
	 *
	 * @since 2.3.0
	 *
	 * @param array $object_ids export job object IDs
	 * @param array $export_args export job args
	 * @param int $timestamp when this fingerprint was generated
	 * @return string
	 */
	protected function generate_auto_export_fingerprint( $object_ids, $export_args, $timestamp ) {

		return md5( json_encode( $object_ids ) . json_encode( $export_args ) . $timestamp );
	}


	/**
	 * Exports a single order when immediate auto-exports are enabled
	 *
	 * @since 2.0.0
	 * @param int $order_id Order ID to export
	 */
	public function auto_export_order( $order_id ) {

		if ( ! $this->exports_enabled( 'orders' ) || 'immediate' !== get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_trigger' ) ) {
			return;
		}

		// filter order based on status and other filtering options
		$order = wc_get_order( $order_id );

		// no order found, order not paid, or order already exported
		if ( ! $order || ! $order->is_paid() || 1 === (int) get_post_meta( $order_id, '_wc_customer_order_xml_export_suite_is_exported', true ) ) {
			return;
		}

		$product_ids        = get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_products' );
		$product_categories = get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_product_categories' );

		$export_handler = wc_customer_order_xml_export_suite()->get_export_handler_instance();

		require_once( wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/class-wc-customer-order-xml-export-suite-query-parser.php' );

		// bail out if order does not contain required products
		if ( ! empty( $product_ids ) ) {

			$order_ids = WC_Customer_Order_XML_Export_Suite_Query_Parser::filter_orders_containing_products( array( $order_id ), $product_ids );

			if ( empty( $order_ids ) ) {
				return;
			}
		}

		// bail out if order does not contain products in required categories
		if ( ! empty( $product_categories ) ) {

			$order_ids = WC_Customer_Order_XML_Export_Suite_Query_Parser::filter_orders_containing_product_categories( array( $order_id ), $product_categories );

			if ( empty( $order_ids ) ) {
				return;
			}
		}

		$args = array(
			'type'       => 'orders',
			'method'     => get_option( 'wc_customer_order_xml_export_suite_orders_auto_export_method' ),
			'invocation' => 'auto',
		);

		if ( $this->is_duplicate_export( array( $order_id ), $args ) ) {
			return;
		}

		try {

			// whoa, we got here! kick it off!
			$export_handler->start_export( $order_id, $args );

		} catch ( SV_WC_Plugin_Exception $e ) {

			// log errors
			/* translators: Placeholders: %s - error message */
			wc_customer_order_xml_export_suite()->log( sprintf( esc_html__( 'Automatic order export failed: %s', 'woocommerce-customer-order-xml-export-suite' ), $e->getMessage() ) );
		}
	}


	/**
	 * Clear scheduled events upon deactivation
	 *
	 * @since 1.2.0
	 */
	public function clear_scheduled_export() {

		wp_clear_scheduled_hook( 'wc_customer_order_xml_export_suite_auto_export_orders' );
		wp_clear_scheduled_hook( 'wc_customer_order_xml_export_suite_auto_export_customers' );
	}




	/**
	 * Schedule once-daily cleanup of old export jobs
	 *
	 * @since 2.0.0
	 */
	public function schedule_export_cleanup() {

		if ( ! wp_next_scheduled( 'wc_customer_order_xml_export_suite_scheduled_export_cleanup' ) ) {

			wp_schedule_event( strtotime( 'tomorrow +15 minutes' ), 'daily', 'wc_customer_order_xml_export_suite_scheduled_export_cleanup' );
		}
	}


	/**
	 * Clean up (remove) exports older than the maximum age (14 days by default)
	 *
	 * @since 2.0.0
	 */
	public function cleanup_exports() {

		wc_customer_order_xml_export_suite()->get_export_handler_instance()->remove_expired_exports();
	}


}
