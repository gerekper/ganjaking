<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Appointment;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Handler of pickup location data for WooCommerce orders.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Orders {


	/** @var \WC_Local_Pickup_Plus_Order_Items order items handler instance */
	private $order_items;


	/**
	 * Orders pickup location data handler constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// load the order items handler
		$this->order_items = wc_local_pickup_plus()->load_class( '/includes/class-wc-local-pickup-plus-order-items.php', 'WC_Local_Pickup_Plus_Order_Items' );

		// filter shipping method labels to clean Local Pickup Plus label display
		add_filter( 'woocommerce_order_shipping_method', array( $this, 'filter_shipping_method_labels' ), 10, 1 );

		// hide order shipping address when Local Pickup Plus is the shipping method
		add_filter( 'woocommerce_order_hide_shipping_address', array( $this, 'hide_order_shipping_address' ) );

		// use the Pickup Location as the taxable address
		add_filter( 'woocommerce_customer_taxable_address', array( $this, 'set_customer_taxable_address' ) );

		// add order pickup data to order items table in My Account > View Order and Emails
		add_action( 'woocommerce_order_details_after_order_table_items', [ $this, 'add_order_pickup_data' ], 5, 1 );
		add_action( 'woocommerce_email_after_order_table',               [ $this, 'add_order_pickup_data' ], 5, 3 );
		// also add the data to webhook API responses for orders
		add_filter( 'woocommerce_api_order_response',      array( $this, 'add_api_order_response_pickup_data' ), 10, 4 );

		// send notifications to designated recipients for new orders, failed orders and cancelled orders featuring pickup locations
		// TODO: perhaps if in the future WooCommerce allows to add and filter CC/BCC recipients that would be more appropriate to add pickup location addresses {FN 2017-05-25}
		add_filter( 'woocommerce_email_recipient_new_order',       array( $this, 'notify_pickup_locations_recipients' ), 10, 2 );
		add_filter( 'woocommerce_email_recipient_cancelled_order', array( $this, 'notify_pickup_locations_recipients' ), 10, 2 );
		add_filter( 'woocommerce_email_recipient_failed_order',    array( $this, 'notify_pickup_locations_recipients' ), 10, 2 );
	}


	/**
	 * Get the order items handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Order_Items
	 */
	public function get_order_items_instance() {
		return $this->order_items;
	}


	/**
	 * Gets any order pickup location IDs from the given order.
	 *
	 * Note: this does not return additional pickup data like pickup date or pickup items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WP_Post|\WC_Order|\WC_Order_Refund $order the order as object, post object or ID
	 * @return null[]|int[] associative array of pickup location IDs for each shipping order item or null if location not found
	 */
	public function get_order_pickup_location_ids( $order ) {

		$order        = $order instanceof \WP_Post || is_numeric( $order ) ? wc_get_order( $order ) : $order;
		$location_ids = [];

		if ( ( $order instanceof \WC_Order || $order instanceof \WC_Order_Refund ) && ! $order instanceof \WC_Subscription ) {

			$order_shipping_items = $order->get_shipping_methods();

			foreach ( $order_shipping_items as $shipping_item_id => $shipping_item ) {

				// note: we still include invalid / not found IDs
				if ( wc_local_pickup_plus_shipping_method_id() === $shipping_item['method_id'] ) {
					$pickup_location_id                = $this->get_order_items_instance()->get_order_item_pickup_location_id( $shipping_item_id );
					$location_ids[ $shipping_item_id ] = is_numeric( $pickup_location_id ) ? (int) $pickup_location_id : null;
				}
			}
		}

		return $location_ids;
	}


	/**
	 * Gets any order pickup locations from the given order.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WP_Post|\WC_Order $order the order object, post or ID
	 * @return null[]|\WC_Local_Pickup_Plus_Pickup_Location[] associative array of pickup location objects for each shipping order item or null if location not found
	 */
	public function get_order_pickup_locations( $order ) {

		$pickup_locations = array();

		if ( $pickup_location_ids = $this->get_order_pickup_location_ids( $order ) ) {

			foreach ( $pickup_location_ids as $shipping_item_id => $pickup_location_id ) {
				// note: we will still list invalid / not found locations
				$pickup_locations[ $shipping_item_id ] = is_numeric( $pickup_location_id ) ? wc_local_pickup_plus_get_pickup_location( $pickup_location_id ) : null;
			}
		}

		return $pickup_locations;
	}


	/**
	 * Check whether an order has associated pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order|\WP_Post|\WC_Order_Refund $order an order ID or object, post object or refund
	 * @return bool
	 */
	public function order_has_pickup_locations( $order ) {

		$pickup_locations = $this->get_order_pickup_location_ids( $order );

		return ! empty( $pickup_locations );
	}


	/**
	 * Filter the shipping method labels.
	 *
	 * Local Pickup Plus method may have set a shipping discount in the method label, which in turn can also inject some HTML.
	 * This filter clears the HTML and restores the shipping method normal label, and also ensures there are no duplicate mentions of the shipping method, in case there are multiple packages for local pickup bound for different locations.
	 *
	 * @see \WC_Shipping_Local_Pickup_Plus::calculate_shipping()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $labels_string shipping method labels as a comma separated string
	 * @return string
	 */
	public function filter_shipping_method_labels( $labels_string ) {

		$array_labels    = explode( ', ', wp_strip_all_tags( $labels_string ) );
		$new_labels      = array();

		if ( ! empty( $array_labels ) && is_array( $array_labels ) )  {

			$is_rtl               = is_rtl();
			$shipping_method_name = wc_local_pickup_plus_shipping_method()->get_method_title();

			foreach ( $array_labels as $label ) {

				if (    ( ! $is_rtl && Framework\SV_WC_Helper::str_starts_with( $label, $shipping_method_name ) )
				     || (   $is_rtl && Framework\SV_WC_Helper::str_ends_with( $label, $shipping_method_name ) ) ) {
					$new_labels[] = $shipping_method_name;
				} else {
					$new_labels[] = $label;
				}
			}

			$labels_string = implode( ', ', array_unique( $new_labels ) );
		}

		return $labels_string;
	}


	/**
	 * Gets the order IDs for a given set of order item IDs.
	 *
	 * @since 2.7.0
	 *
	 * @param int[] $order_items_ids array of order item IDs
	 * @return int[]
	 */
	private function get_order_ids_by_order_item_ids( $order_item_ids ) {
		global $wpdb;

		$order_ids = [];

		$order_item_ids = '(' . implode( ',', $order_item_ids ) . ')';
		$order_items    = $wpdb->prefix . 'woocommerce_order_items';
		$order_results  = $wpdb->get_results( "
			SELECT order_id
			FROM {$order_items}
			WHERE order_item_id IN {$order_item_ids}
		", ARRAY_N );

		if ( ! empty ( $order_results ) ) {

			foreach ( $order_results as $orders ) {
				foreach ( $orders as $order_id ) {
					if ( is_numeric( $order_id ) ) {
						$order_ids[] = absint( $order_id );
					}
				}
			}
		}

		return $order_ids;
	}


	/**
	 * Gets order IDs that are associated to a given pickup location.
	 *
	 * TODO this method will have to be updated in future in consideration of WC 3.0+ changes {FN 2017-04-27}
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Local_Pickup_Plus_Pickup_Location $pickup_location_id pickup location ID or object
	 * @return int[]
	 */
	public function get_pickup_location_order_ids( $pickup_location_id ) {
		global $wpdb;

		if ( is_numeric( $pickup_location_id ) ) {
			$pickup_location_id = (int) $pickup_location_id;
		} elseif ( $pickup_location_id instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {
			$pickup_location_id = $pickup_location_id->get_id();
		}

		$order_ids = [];

		if ( is_int( $pickup_location_id ) && $pickup_location_id > 0 ) {

			$order_itemmeta     = $wpdb->prefix . 'woocommerce_order_itemmeta';
			$pickup_location_id = (int) $_GET['_pickup_location'];
			$item_results       = $wpdb->get_results( "
				SELECT order_item_id
				FROM {$order_itemmeta}
				WHERE meta_key = '_pickup_location_id'
				AND meta_value = {$pickup_location_id}
			", ARRAY_N );

			if ( ! empty( $item_results ) ) {

				$order_item_ids = [];

				foreach ( $item_results as $items ) {
					foreach ( $items as $item_id ) {
						if ( is_numeric( $item_id ) ) {
							$order_item_ids[] = absint( $item_id );
						}
					}
				}

				$order_ids = $this->get_order_ids_by_order_item_ids( $order_item_ids );
			}
		}

		return $order_ids;
	}


	/**
	 * Gets order IDs that have at least one shipping item with pickup appointment time within the given range.
	 *
	 * @since 2.7.0
	 *
	 * @param int $start start timestamp
	 * @param int $end end timestamp
	 * @return int[]
	 */
	public function get_orders_by_appointment_range( $start = 0, $end = 0 ) {
		global $wpdb;

		// sanitize for db query
		$start = (int) $start;
		$end   = (int) $end;

		// filter order item meta for new meta keys (`_pickup_appointment_start` and `_pickup_appointment_end`) or legacy meta key `_pickup_date`
		$order_items    = $wpdb->prefix . 'woocommerce_order_items';
		$order_itemmeta = $wpdb->prefix . 'woocommerce_order_itemmeta';

		$order_ids = $wpdb->get_col( "
			SELECT DISTINCT oi.order_id
			FROM {$order_items} oi
			LEFT JOIN {$order_itemmeta} appointment_start ON (oi.order_item_id = appointment_start.order_item_id AND appointment_start.meta_key = '_pickup_appointment_start')
			LEFT JOIN {$order_itemmeta} appointment_end ON (oi.order_item_id = appointment_end.order_item_id AND appointment_end.meta_key = '_pickup_appointment_end')
			LEFT JOIN {$order_itemmeta} appointment_date ON (oi.order_item_id = appointment_date.order_item_id AND appointment_date.meta_key = '_pickup_date')
			WHERE (
				(
					appointment_start.meta_value BETWEEN {$start} AND {$end}
					AND ( appointment_end.meta_value BETWEEN {$start} AND {$end} OR appointment_end.meta_value IS NULL )
				)
				OR UNIX_TIMESTAMP( STR_TO_DATE( appointment_date.meta_value, '%Y-%m-%d' ) ) BETWEEN {$start} AND {$end}
			)
		" );

		if ( false === $order_ids ) {
			return [];
		}

		return array_map( 'absint', $order_ids );
	}


	/**
	 * Get pickup data for order.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WP_Post|\WC_Order $order order ID, object or post object
	 * @param bool $raw whether output an array intended for machine reading or human reading (default)
	 * @return array associative array of key values for each pickup package in order
	 */
	public function get_order_pickup_data( $order, $raw = false ) {

		$order            = $order instanceof \WP_Post || is_numeric( $order ) ? wc_get_order( $order ) : $order;
		$pickup_data      = array();
		$pickup_locations = $this->get_order_pickup_location_ids( $order );

		if ( ! empty( $pickup_locations ) ) {

			$shipping_items        = array_keys( $order->get_shipping_methods() );
			$pickup_shipping_items = array_keys( $pickup_locations );

			// loop all shipping items
			foreach ( $shipping_items as $shipping_item_id ) {

				// check if the shipping item is among those meant for pickup
				if ( in_array( $shipping_item_id, $pickup_shipping_items, false ) ) {

					// try to get appointment
					try {
						$pickup_appointment = new Appointment( $shipping_item_id );
					} catch ( Exception $exception ) {
						$pickup_appointment = false;
					}

					$order_items_handler = $this->get_order_items_instance();
					$pickup_location_id  = $order_items_handler->get_order_item_pickup_location_id( $shipping_item_id );
					$pickup_items        = $order_items_handler->get_order_item_pickup_items( $shipping_item_id );

					if ( true === $raw ) {

						$pickup_data[ $shipping_item_id ] = [
							'pickup_location_id' => (int) $pickup_location_id,
							'pickup_items'       => is_array( $pickup_items ) ? $pickup_items : [],
							/** {@see Appointment::get_start()} should always return a {@see \DateTime} object for existing appointments */
							'pickup_date'        => $pickup_appointment ? $pickup_appointment->get_start()->format( 'Y-m-d' ) : '',
							// TODO extend raw data to parse pickup appointment time {FN 2019-11-27}
						];

					} else {

						$pickup_location         = $order_items_handler->get_order_item_pickup_location( $shipping_item_id );
						$pickup_location_name    = $order_items_handler->get_order_item_pickup_location_name( $shipping_item_id );
						$pickup_location_address = $order_items_handler->get_order_item_pickup_location_address( $shipping_item_id, 'plain' );
						$pickup_location_phone   = $order_items_handler->get_order_item_pickup_location_phone( $shipping_item_id, true );
						$pickup_location_notes   = $pickup_location ? $pickup_location->get_description() : '';

						$pickup_data[ $shipping_item_id ][ __( 'Pickup Location', 'woocommerce-shipping-local-pickup-plus' ) ] = $pickup_location_name;
						$pickup_data[ $shipping_item_id ][ __( 'Address', 'woocommerce-shipping-local-pickup-plus' ) ] = $pickup_location_address;

						if ( ! empty( $pickup_location_phone ) ) {
							$pickup_data[ $shipping_item_id ][ __( 'Phone', 'woocommerce-shipping-local-pickup-plus' ) ] = $pickup_location_phone;
						}

						if ( ! empty( $pickup_location_notes ) ) {
							$pickup_data[ $shipping_item_id ][ __( 'Notes', 'woocommerce-shipping-local-pickup-plus' ) ] = $pickup_location_notes;
						}

						// check for pickup appointment/date historical data
						if ( ! empty( $pickup_appointment ) && ! empty( $pickup_appointment_start = $pickup_appointment->get_start() ) ) {

							$pickup_data[ $shipping_item_id ][ __( 'Pickup Date', 'woocommerce-shipping-local-pickup-plus' ) ] = date_i18n( wc_date_format(), $pickup_appointment_start->getTimestamp() + $pickup_appointment_start->getOffset() );

							if ( $pickup_appointment->has_pickup_time() ) {

								// get time from appointment
								$pickup_data[ $shipping_item_id ][ __( 'Pickup Time', 'woocommerce-shipping-local-pickup-plus' ) ] = $pickup_appointment_start->format( wc_time_format() );

							// we are dealing with an anytime appointment with or without lead time, so let's show the schedule if pickup location is available
							} elseif ( ! empty( $pickup_location ) ) {

								// anytime appointments have a start date that is either equal to the start of the day or the minimum pickup time for that date
								$pickup_minimum_time = $pickup_appointment_start->getTimestamp() - ( clone $pickup_appointment_start )->setTime( 0, 0, 0 )->getTimestamp();

								$pickup_data[ $shipping_item_id ][ __( 'Pickup Time', 'woocommerce-shipping-local-pickup-plus' ) ] = $pickup_location->get_business_hours()->get_schedule( $pickup_appointment_start->format( 'w' ), true, $pickup_minimum_time );
							}
						}

						if ( ! empty( $pickup_items ) && ( $order_items = $order->get_items() ) ) {

							$items_to_pickup = array();

							foreach ( $order_items as $order_item_id => $order_item_data ) {

								if ( in_array( $order_item_id, $pickup_items, false ) ) {

									$name = isset( $order_item_data['name'] ) ? $order_item_data['name'] : null;
									$qty  = isset( $order_item_data['qty'] ) ? $order_item_data['qty'] : null;

									if ( $name && $qty ) {
										$items_to_pickup[] = is_rtl() ? '&times; ' . $qty . ' ' . $name : $name . ' &times; ' . $qty;
									}
								}
							}

							if ( ! empty( $items_to_pickup ) ) {
								$pickup_data[ $shipping_item_id ][ __( 'Items to Pickup', 'woocommerce-shipping-local-pickup-plus' ) ] = implode( ', ', $items_to_pickup );
							}
						}
					}
				}
			}
		}

		/**
		 * Filter an order pickup data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $pickup_data array of pickup data. Empty if there is no associated data
		 * @param \WC_Order $order a WooCommerce Order object
		 * @param bool $raw whether we are returning an array meant for human display (false) or raw data (true)
		 */
		return apply_filters( 'wc_local_pickup_plus_get_order_pickup_data', $pickup_data, $order, $raw );
	}


	/**
	 * Add order pickup data to customer order views and emails.
	 *
	 * This method is used as hook callback for both `woocommerce_order_items_table` and `woocommerce_email_after_order_table` actions.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object to output pickup data for
	 * @param bool $sent_to_admin when callback is for an email, whether this is sent to an admin
	 * @param bool $plan_text when callback is for an email, whether this is sent as plan text
	 */
	public function add_order_pickup_data( $order, $sent_to_admin = false, $plan_text = false ) {

		$pickup_data = $this->get_order_pickup_data( $order );

		if ( ! empty( $pickup_data ) ) {

			$current_action = current_action();

			if ( 'woocommerce_order_items_table' === $current_action || 'woocommerce_order_details_after_order_table_items' === $current_action ) {

				wc_get_template( 'orders/order-pickup-details.php', array(
					'order'           => $order,
					'pickup_data'     => $pickup_data,
					'shipping_method' => wc_local_pickup_plus_shipping_method(),
				), '', wc_local_pickup_plus()->get_plugin_path() . '/templates/' );

			} elseif ( 'woocommerce_email_after_order_table' === $current_action ) {

				$template = true === $plan_text ? 'emails/plain/order-pickup-details.php' : 'emails/order-pickup-details.php';

				wc_get_template( $template, array(
					'order'           => $order,
					'pickup_data'     => $pickup_data,
					'shipping_method' => wc_local_pickup_plus_shipping_method(),
					'sent_to_admin'   => $sent_to_admin,
				), '', wc_local_pickup_plus()->get_plugin_path() . '/templates/' );
			}
		}
	}


	/**
	 * Add pickup data to WC API responses for orders.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $order_response order data to send in response
	 * @param \WC_Order $order the order object the response is for
	 * @return array
	 */
	public function add_api_order_response_pickup_data( $order_response, $order ) {

		$pickup_data = $this->get_order_pickup_data( $order->get_id(), true );

		if ( ! empty( $pickup_data ) ) {
			$order_response['pickup_data'][] = $pickup_data;
		}

		return $order_response;
	}


	/**
	 * Notify recipients of new orders, failed orders and cancelled orders featuring pickup locations.
	 *
	 * TODO: perhaps if in the future WooCommerce allows to add and filter CC/BCC recipients that would be more appropriate to add pickup location addresses {FN 2017-05-25}
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $recipients the email recipients, as a comma-separated string
	 * @param \WC_Order $order the order as the email object
	 * @return string
	 */
	public function notify_pickup_locations_recipients( $recipients, $order = null ) {

		if ( $order instanceof \WC_Order && $this->order_has_pickup_locations( $order ) ) {

			$pickup_locations = $this->get_order_pickup_locations( $order );
			$new_recipients   = explode( ',', $recipients );

			foreach ( $pickup_locations as $pickup_location ) {
				// we need this check as orders may contain lost/deleted pickup locations that won't produce a pickup location instance
				if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {
					$new_recipients = array_merge( $new_recipients, array_filter( (array) $pickup_location->get_email_recipients( 'array' ) ) );
				}
			}

			$recipients = implode( ',', $new_recipients );
		}

		return $recipients;
	}


	/**
	 * Don't require the shipping address if Local Pickup Plus is the only shipping method.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $hidden_address_shipping_methods array of shipping methods that don't require a shipping address
	 * @return string[]
	 */
	public function hide_order_shipping_address( $hidden_address_shipping_methods ) {

		$hidden_address_shipping_methods[] = wc_local_pickup_plus_shipping_method_id();

		return $hidden_address_shipping_methods;
	}


	/**
	 * Determines whether to use the pickup location address as the taxable address, or not.
	 *
	 * Helper method: @see \WC_Local_Pickup_Plus_Orders::set_customer_taxable_address()
	 *
	 * @since 2.3.15
	 *
	 * @return bool
	 */
	private function use_pickup_location_taxable_address() {

		$use_location_tax  = false;
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if (    $local_pickup_plus
		     && $local_pickup_plus->apply_pickup_location_tax()
		     && $local_pickup_plus->is_available() ) {

			if ( is_admin() ) {

				// in admin context, it's up to the order to have recorded pickup location data or not, so we don't check anything here, only if the setting applies
				$use_location_tax = true;

			} else {

				$packages = WC()->shipping()->get_packages();

				if ( ! empty ( $packages ) ) {

					foreach ( $packages as $key => $package ) {

						$shipping_method = wc_get_chosen_shipping_method_for_package( $key, $package );

						if ( $shipping_method === $local_pickup_plus->get_method_id() ) {
							$use_location_tax = true;
							break;
						}
					}

				} else {

					// legacy handling kept here for sanity, however packages should exist at this point
					$shipping_methods = function_exists( 'wc_get_chosen_shipping_method_ids' ) ? wc_get_chosen_shipping_method_ids() : [];
					$methods_count    = is_array( $shipping_methods ) ? count( $shipping_methods ) : 0;
					// ensures there are no packages meant for shipping
					$has_local_pickup = 0 === $methods_count || in_array( wc_local_pickup_plus_shipping_method_id(), $shipping_methods, false );
					// this check is for when we have a single pickup location in the system which comes pre-selected at checkout and may have not been recorded in the shipping methods IDs in session yet
					$has_pickup_packages = $local_pickup_plus->is_default_handling( 'pickup' )
					    && wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count() === 1  // we have in total only 1 pickup location published
					    && wc_local_pickup_plus()->get_packages_instance()->get_packages_for_pickup_count()      >= 0   // there are perhaps one or more packages for pickup
					    && wc_local_pickup_plus()->get_packages_instance()->get_packages_for_shipping_count()    === 0; // there are no packages for shipping

					$use_location_tax = $has_local_pickup || $has_pickup_packages;
				}
			}
		}

		return $use_location_tax;
	}


	/**
	 * Attempts to return a pickup location address for tax purposes from an array of data.
	 *
	 * @see \WC_Local_Pickup_Plus_Orders::set_customer_taxable_address()
	 *
	 * @since 2.1.1
	 *
	 * @param array $package_data
	 * @return null|\WC_Local_Pickup_Plus_Address
	 */
	private function get_pickup_location_taxable_address( $package_data ) {

		$found_address = null;

		if ( is_array( $package_data ) ) {

			foreach ( $package_data as $package ) {

				// loop packages as we might not have yet a chosen pickup location
				if ( is_array( $package ) && ! empty( $package['pickup_location_id'] ) && $pickup_location = wc_local_pickup_plus_get_pickup_location( $package['pickup_location_id'] ) ) {

					// sanity check to skip WooCommerce packages that may still exhibit an pickup location ID key but no longer meant for pickup
					if ( ! ( isset( $package['handling'] ) || isset( $package['lookup_area'] ) || ( isset( $package['ship_via'] ) && wc_local_pickup_plus_shipping_method_id() === $package['ship_via'] ) ) ) {
						continue;
					}

					$address = $pickup_location->get_address();

					// TODO We can only use one taxable address at one time, but in niche cases there could be multiple locations in different tax areas, so these are currently unresolved {FN 2017-07-07}
					if ( $address instanceof \WC_Local_Pickup_Plus_Address ) {

						$found_address = $address;
						break;
					}
				}
			}
		}

		return $found_address;
	}


	/**
	 * Filters the customer taxable address.
	 *
	 * If applying taxes for a chosen pickup location, will use the location address for tax calculation purposes instead of the customer defined.
	 *
	 * @see \WC_Shipping_Local_Pickup_Plus::apply_pickup_location_tax()
	 * @see \WC_Local_Pickup_Plus_Orders::use_pickup_location_taxable_address()
	 * @see \WC_Local_Pickup_Plus_Orders::get_pickup_location_taxable_address()
	 *
	 * TODO If multiple pickup locations are chosen for different packages, there is currently no way to calculate taxes with multiple addresses, hence only the address from the first available location will be chosen. {FN 2017-07-07}
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $taxable_address associative array, defaults to customer address
	 * @return array
	 */
	public function set_customer_taxable_address( $taxable_address ) {

		// bail if we don't or can't use the pickup location's taxable address
		if ( ! $this->use_pickup_location_taxable_address() ) {
			return $taxable_address;
		}

		// in admin, ensure the address is set for the given order in context, if applicable
		if ( is_admin() && ( $order = wc_get_order( get_the_ID() ) ) ) {
			return $this->set_customer_taxable_address_for_order( $taxable_address, $order );
		}

		$found_address = false;

		// 1. try to get an address from the package data - which may not be set yet
		if ( $packages = WC()->shipping()->get_packages() ) {

			foreach ( $packages as $package ) {

				if ( $found_address = $this->get_pickup_location_taxable_address( $package ) ) {
					break;
				}
			}
		}

		if ( WC()->session ) {

			// 2. if the packages array is not built yet, try to look in Local Pickup Plus packages session (which normally would match what will go in WooCommerce)
			if ( ! $found_address ) {
				$found_address = $this->get_pickup_location_taxable_address( WC()->session->get( 'wc_local_pickup_plus_packages' ) );
			}

			// 3. if the packages in Local Pickup Plus aren't finalized, try looking in the Local Pickup Plus cart session (an earlier session)
			if ( ! $found_address ) {
				$found_address = $this->get_pickup_location_taxable_address( WC()->session->get( 'wc_local_pickup_plus_cart_items' ) );
			}
		}

		// 4. if we can't still determine an address, perhaps check if the cart contains an item that should be picked up, and use the address of the default location before customer makes a choice
		if ( ! $found_address && WC()->cart ) {

			$preferred_location = wc_local_pickup_plus_get_user_default_pickup_location();
			$preferred_address  = $preferred_location ? $preferred_location->get_address() : null;

			// don't bother if the customer does not have a preferred address
			if ( $preferred_address ) {

				foreach ( WC()->cart->cart_contents as $cart_item ) {

					$product = isset( $cart_item['data'] ) && $cart_item['data'] instanceof \WC_Product ? $cart_item['data'] : null;

					// check if the product _must_ be picked up or _can_ be picked up _and_ the default handling is to pickup
					if ( $product && ( wc_local_pickup_plus_product_must_be_picked_up( $product ) || ( wc_local_pickup_plus_shipping_method()->is_default_handling( 'pickup' ) && wc_local_pickup_plus_product_can_be_picked_up( $product ) ) ) ) {

						$found_address = $preferred_address;
						break;
					}
				}
			}
		}

		// 5. by this point either we have a proper address or not (applies tax determined by WooCommerce otherwise)
		if ( $found_address instanceof \WC_Local_Pickup_Plus_Address ) {
			$taxable_address = array(
				$found_address->get_country(),
				$found_address->get_state(),
				$found_address->get_postcode(),
				$found_address->get_city(),
			);
		}

		return $taxable_address;
	}


	/**
	 * Sets the taxable address for an order that has already been made.
	 *
	 * @since 2.3.8
	 *
	 * @param array $taxable_address
	 * @param \WC_Order $order
	 * @return array
	 */
	public function set_customer_taxable_address_for_order( $taxable_address, $order ) {

		$found_address = $this->get_pickup_location_taxable_address( $this->get_order_pickup_data( $order, true ) );

		if ( $found_address instanceof \WC_Local_Pickup_Plus_Address ) {
			$taxable_address = array(
				$found_address->get_country(),
				$found_address->get_state(),
				$found_address->get_postcode(),
				$found_address->get_city(),
			);
		}

		return $taxable_address;
	}


}
