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
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Appointment;

/**
 * AJAX class.
 *
 * This class handles AJAX calls for Local Pickup Plus either from admin or frontend.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Ajax {


	/**
	 * Add AJAX hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {


		// ====================
		//   GENERAL ACTIONS
		// ====================

		// make sure Local Pickup Plus is loaded during cart/checkout operations
		add_action( 'wp_ajax_woocommerce_checkout',        array( $this, 'load_shipping_method' ), 5 );
		add_action( 'wp_ajax_nopriv_woocommerce_checkout', array( $this, 'load_shipping_method' ), 5 );


		// ====================
		//    ADMIN ACTIONS
		// ====================

		// Admin: add new time range picker HTML.
		add_action( 'wp_ajax_wc_local_pickup_plus_get_time_range_picker_html', [ $this, 'get_time_range_picker_html' ] );
		// Admin: get pickup location IDs from a JSON search.
		add_action( 'wp_ajax_wc_local_pickup_plus_json_search_pickup_location_ids', [ $this, 'json_search_pickup_location_ids' ] );
		// Admin: update order shipping item pickup data.
		add_action( 'wp_ajax_wc_local_pickup_plus_update_order_shipping_item_pickup_data', [ $this, 'update_order_shipping_item_pickup_data' ] );


		// ====================
		//   FRONTEND ACTIONS
		// ====================

		// set the handling for items in a package when the shipping method is changed
		add_action( 'wp_ajax_wc_local_pickup_plus_set_package_items_handling',        [ $this, 'set_package_items_handling' ] );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_set_package_items_handling', [ $this, 'set_package_items_handling' ] );
		// set a cart item for shipping or pickup
		add_action( 'wp_ajax_wc_local_pickup_plus_set_cart_item_handling',        array( $this, 'set_cart_item_handling' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_set_cart_item_handling', array( $this, 'set_cart_item_handling' ) );
		// set a package pickup data
		add_action( 'wp_ajax_wc_local_pickup_plus_set_package_handling',        array( $this, 'set_package_handling' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_set_package_handling', array( $this, 'set_package_handling' ) );
		// pickup locations lookup
		add_action( 'wp_ajax_wc_local_pickup_plus_pickup_locations_lookup',        array( $this, 'pickup_locations_lookup' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_pickup_locations_lookup', array( $this, 'pickup_locations_lookup' ) );
		// get location name
		add_action( 'wp_ajax_wc_local_pickup_plus_get_pickup_location_name',        array( $this, 'get_pickup_location_name' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_get_pickup_location_name', array( $this, 'get_pickup_location_name' ) );
		// get location area
		add_action( 'wp_ajax_wc_local_pickup_plus_get_pickup_location_area',        array( $this, 'get_pickup_location_area' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_get_pickup_location_area', array( $this, 'get_pickup_location_area' ) );
		// get location pickup appointment data
		add_action( 'wp_ajax_wc_local_pickup_plus_get_pickup_location_appointment_data',        array( $this, 'get_pickup_location_appointment_data' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_get_pickup_location_appointment_data', array( $this, 'get_pickup_location_appointment_data' ) );
		// get opening hours for a given location
		add_action( 'wp_ajax_wc_local_pickup_plus_get_pickup_location_opening_hours_list',        array( $this, 'get_pickup_location_opening_hours_list' ) );
		add_action( 'wp_ajax_nopriv_wc_local_pickup_plus_get_pickup_location_opening_hours_list', array( $this, 'get_pickup_location_opening_hours_list' ) );
	}


	/**
	 * Loads the Local Pickup Plus shipping method class.
	 *
	 * Ensures the method is loaded from the 'woocommerce_update_shipping_method' AJAX action early.
	 * Otherwise it would not be loaded in time to update the shipping package.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function load_shipping_method() {

		wc_local_pickup_plus()->load_shipping_method();
	}


	/**
	 * Get time range picker HTML.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function get_time_range_picker_html() {

		check_ajax_referer( 'get-time-range-picker-html', 'security' );

		if ( isset( $_POST['name'] ) ) {

			$business_hours = new \WC_Local_Pickup_Plus_Business_Hours();

			$input_field = $business_hours->get_time_range_picker_input_html( array(
				'name'           => sanitize_text_field( $_POST['name'] ),
				'selected_start' => ! empty( $_POST['selected_start'] ) ? max( 0, (int) $_POST['selected_start'] ) : 9 * HOUR_IN_SECONDS,
				'selected_end'   => ! empty( $_POST['selected_end'] )   ? max( 0, (int) $_POST['selected_end'] )   : 17 * HOUR_IN_SECONDS,
			) );

			wp_send_json_success( $input_field );
		}

		wp_send_json_error( 'Missing field name' );
	}


	/**
	 * Update order shipping item pickup data.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function update_order_shipping_item_pickup_data() {

		check_ajax_referer( 'update-order-pickup-data', 'security' );

		if ( isset( $_POST['item_id'] ) && is_numeric( $_POST['item_id'] ) ) {

			$orders_handler     = wc_local_pickup_plus()->get_orders_instance();
			$order_item_handler = $orders_handler ? $orders_handler->get_order_items_instance() : null;

			if ( $order_item_handler ) {

				$item_id              = (int) Framework\SV_WC_Helper::get_posted_value( 'item_id', 0 );
				$pickup_location_id   = Framework\SV_WC_Helper::get_posted_value( 'pickup_location', null );
				$set_appointment      = Framework\SV_WC_Helper::get_posted_value( 'set_pickup_appointment', null );
				$pickup_date          = Framework\SV_WC_Helper::get_posted_value( 'pickup_date' );
				$appointment_offset   = absint( Framework\SV_WC_Helper::get_posted_value( 'pickup_appointment_offset', 0 ) );
				$pickup_items         = Framework\SV_WC_Helper::get_posted_value( 'pickup_items', [] );
				$force_selection      = (bool) Framework\SV_WC_Helper::get_posted_value( 'force', false );
				$pickup_location      = is_numeric( $pickup_location_id ) ? wc_local_pickup_plus_get_pickup_location( (int) $pickup_location_id ) : null;
				$appointment_duration = wc_local_pickup_plus_shipping_method()->get_default_appointment_duration();
				$appointments_handler = wc_local_pickup_plus()->get_appointments_instance();
				$appointment          = $appointments_handler->get_shipping_item_appointment( $item_id );

				// delete the appointment if admin chose to remove it, regardless of a pickup location
				if ( 'no' === $set_appointment && 'required' !== wc_local_pickup_plus_appointments_mode() ) {
					try {
						if ( $appointment ) {
							$appointment->delete();
						} else {
							wc_delete_order_item_meta( $item_id, '_pickup_appointment_start' );
							wc_delete_order_item_meta( $item_id, '_pickup_appointment_end' );
						}
					} catch ( \Exception $e ) { }
				}

				// update corresponding order item meta if the pickup location exists
				if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {

					// maybe set an appointment
					if ( 'no' !== $set_appointment && ! empty( $pickup_date ) && 'disabled' !== wc_local_pickup_plus_appointments_mode() ) {

						try {

							$order = wc_get_order( wc_get_order_id_by_order_item_id( $item_id ) );

							// anytime appointment
							if ( $pickup_location->get_appointments()->is_anytime_appointments_enabled() ) {

								// make sure $start_date - $end_date is exactly 86400 by modifying $end_date's timestamp directly
								$start_date = new \DateTime( $pickup_date, $pickup_location->get_address()->get_timezone() );
								$end_date   = ( clone $start_date )->setTimestamp( $start_date->getTimestamp() + DAY_IN_SECONDS );

							// appointment with time
							} else {

								$start_date = new \DateTime( date( 'Y-m-d H:i:s', strtotime( $pickup_date ) + $appointment_offset ), $pickup_location->get_address()->get_timezone() );
								$end_date   = ( clone $start_date )->add( new \DateInterval( sprintf( 'PT%dS', $appointment_duration ) ) );
							}

						} catch ( \Exception $e ) {

							$order      = null;
							$start_date = null;
							$end_date   = null;
						}

						// at this point $order can be either false or null, or a date could be invalid
						if ( ! $order instanceof \WC_Order || null === $start_date || null === $end_date ) {
							wp_send_json_error( sprintf( 'Could not set pickup data for order item #%s', ( is_string( $item_id ) || is_numeric( $item_id ) ) ? $item_id : '' ) );
						}

						if ( $force_selection || $appointments_handler->is_appointment_time_available( $order->get_date_created(), $pickup_location, $appointment_duration, $start_date, $end_date ) ) {

							try {

								// an appointment object will exist only for existing
								if ( $appointment ) {

									// in theory setting an invalid start date could throw a notice, but this is unlikely here
									$appointment->set_start( $start_date );
									$appointment->set_end( $end_date );
									$appointment->set_pickup_location_id( $pickup_location->get_id() );
									$appointment->set_pickup_location_name( $pickup_location->get_name() );
									$appointment->set_pickup_location_phone( $pickup_location->get_phone() );
									$appointment->set_pickup_location_address( $pickup_location->get_address() );

									$appointment->save();

								// for newly created items we might have to set meta directly
								} elseif ( $item_id > 0 ) {

									wc_update_order_item_meta( $item_id, '_pickup_appointment_start', $start_date ? $start_date->getTimestamp() : null );
									wc_update_order_item_meta( $item_id, '_pickup_appointment_end', $end_date ? $end_date->getTimestamp() : null );
									wc_update_order_item_meta( $item_id, '_pickup_location_id', $pickup_location->get_id() );
									wc_update_order_item_meta( $item_id, '_pickup_location_address', $pickup_location->get_address()->get_array() );
									wc_update_order_item_meta( $item_id, '_pickup_location_name', $pickup_location->get_name() );
									wc_update_order_item_meta( $item_id, '_pickup_location_phone', $pickup_location->get_phone() );
								}

							} catch ( \Exception $e ) {

								wp_send_json_error( sprintf( 'Could not set pickup data for order item #%1$s: %2$s', ( is_string( $item_id ) || is_numeric( $item_id ) ) ? $item_id : '', $e->getMessage() ) );
							}

						} else {

							wp_send_json_error( [
								'error'   => 'appointment-time-not-available',
								'message' => esc_html__( "Heads up! The appointment entered in this order is outside your configured availability.\n\nWould you like to save the selected date and time?", 'woocommerce-shipping-local-pickup-plus' ),
							] );
						}
					}

					// update the rest of the pickup data that may have changed
					$order_item_handler->set_order_item_pickup_location( $item_id, $pickup_location );
					$order_item_handler->set_order_item_pickup_items( $item_id, (array) $pickup_items );
				}

				// our JS script expects success to reload the page and display updated data
				wp_send_json_success();
			}
		}

		wp_send_json_error( sprintf( 'Could not set pickup data for order item %s', isset( $_POST['item_id'] ) && ( is_string( $_POST['item_id'] ) || is_numeric( $_POST['item_id'] ) ) ? $_POST['item_id'] : '' ) );
	}


	/**
	 * Get pickup location IDs for a JSON search output.
	 *
	 * Used in admin in enhanced dropdown inputs to link products to locations.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function json_search_pickup_location_ids() {

		check_ajax_referer( 'search-pickup-locations', 'security' );

		$search_term = (string) wc_clean( Framework\SV_WC_Helper::get_requested_value( 'term' ) );

		if ( '' === trim( $search_term ) ) {
			die;
		}

		$plugin       = wc_local_pickup_plus();
		$locations    = array();
		$default_args = array(
			'post_type'   => 'wc_pickup_location',
			'post_status' => 'publish',
			'fields'      => 'ids',
		);

		// search term is an alphanumeric string: could be title, address piece, but also postcode...
		if ( ! is_numeric( $search_term ) ) {

			// get locations by generic search term (like title)
			$locations = $this->add_location_to_results( $locations, get_posts( wp_parse_args( array(
				's' => $search_term,
			), $default_args ) ) );

			// if geocoding is enabled, assume a non-numeric keyword could be a geographical entity
			if ( $plugin->geocoding_enabled() && ( $geocoding_handler = $plugin->get_geocoding_api_instance() ) ) {

				$coordinates = $geocoding_handler->get_coordinates( $search_term );

				if ( $coordinates ) {
					$locations = $this->add_location_to_results( $locations, wc_local_pickup_plus_get_pickup_locations_nearby( $coordinates ) );
				}
			}

			// try getting locations assuming search term is for address parts
			if ( $pickup_location_handler = wc_local_pickup_plus()->get_pickup_locations_instance() ) {
				$locations = $this->add_location_to_results( $locations, $pickup_location_handler->get_pickup_locations_by_address_part( 'any', $search_term ) );
			}

		// search term is a number: could be ID, phone, postcode...
		} else {

			// try first by location ID
			$locations = $this->add_location_to_results( $locations, get_posts( wp_parse_args( array(
				'post__in' => array( 0, $search_term ),
			), $default_args ) ) );

			// try by phone number
			$locations = $this->add_location_to_results( $locations, get_posts( wp_parse_args( array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '_pickup_location_phone',
						'value'   => $search_term,
						'compare' => 'LIKE',
					),
				),
			), $default_args ) ) );

			// try by numerical postcode
			if ( $pickup_location_handler = wc_local_pickup_plus()->get_pickup_locations_instance() ) {
				$locations = $this->add_location_to_results( $locations, $pickup_location_handler->get_pickup_locations_by_address_part( 'postcode', $search_term ) );
			}
		}

		$found_locations = array();

		// prepare results for enhanced dropdown
		foreach ( $locations as $found_location_id => $found_location ) {
			$found_locations[ $found_location_id ] = $found_location->get_formatted_name( 'admin' );
		}

		wp_send_json( $found_locations );
	}


	/**
	 * Adds pickup locations to an array of results (helper method).
	 *
	 * @since 2.3.15
	 *
	 * @param array $results associative array of IDs and pickup location objects
	 * @param int[]|\WC_Local_Pickup_Plus_Pickup_Location[] $pickup_locations array of pickup location IDs or objects
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[] associative array of pickup location IDs and objects
	 */
	private function add_location_to_results( array $results, $pickup_locations ) {

		if ( ! empty( $pickup_locations ) && is_array( $pickup_locations ) ) {

			foreach ( $pickup_locations as $pickup_location ) {

				// validate pickup location ID and object
				if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {
					$pickup_location_id = $pickup_location->get_id();
				} elseif ( is_numeric( $pickup_location ) ) {
					$pickup_location_id = $pickup_location;
					$pickup_location    = wc_local_pickup_plus_get_pickup_location( $pickup_location_id );
				} else {
					continue;
				}

				// add to results if doesn't exist already
				if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location && ! array_key_exists( (int) $pickup_location_id, $results ) ) {
					$results[ (int) $pickup_location_id ] = $pickup_location;
				}
			}
		}

		return $results;
	}


	/**
	 * Get a pickup location name.
	 *
	 * Used in frontend to get a pickup location name by its ID.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function get_pickup_location_name() {

		check_ajax_referer( 'get-pickup-location-name', 'security' );

		if ( ! empty( $_POST['id'] ) && ( $pickup_location = wc_local_pickup_plus_get_pickup_location( (int) $_REQUEST['id'] ) ) ) {
			wp_send_json_success( $pickup_location->get_formatted_name() );
		}

		wp_send_json_error( sprintf( 'Could not determine Pickup Location from requested ID %s', isset( $_POST['id'] ) && ( is_string( $_POST['id'] ) || is_numeric( $_POST['id'] ) ) ? $_POST['id'] : '' ) );
	}


	/**
	 * Sets the handling for items in a package when the shipping method is changed.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 */
	public function set_package_items_handling() {

		check_ajax_referer( 'set-package-items-handling', 'security' );

		$handling   = Framework\SV_WC_Helper::get_posted_value( 'handling' );
		$package_id = Framework\SV_WC_Helper::get_posted_value( 'package_id' );

		if ( ( is_numeric( $package_id ) || ( is_string( $package_id ) && '' !== $package_id ) ) && in_array( $handling, [ 'pickup', 'ship' ], true ) ) {

			$package                = wc_local_pickup_plus()->get_packages_instance()->get_shipping_package( $package_id );
			$package_cart_item_keys = ! empty( $package ) ? array_keys( $package['contents'] ) : [];

			if ( ! empty( $package_cart_item_keys ) ) {

				foreach ( $package_cart_item_keys as $cart_item_key ) {

					$session_data = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_item_key );

					// set cart item handling for items in this package
					$session_data['handling'] = $handling;

					wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, $session_data );
				}
			}
		}

		wp_send_json_success();
	}


	/**
	 * Set a cart item for shipping or local pickup, along with pickup data
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function set_cart_item_handling() {

		check_ajax_referer( 'set-cart-item-handling', 'security' );

		if (      isset( $_POST['cart_item_key'], $_POST['pickup_data'], $_POST['pickup_data']['handling'] )
		     &&   in_array( $_POST['pickup_data']['handling'], array( 'ship', 'pickup' ), true )
		     && ! WC()->cart->is_empty() ) {

			$cart_item_key = $_POST['cart_item_key'];
			$handling_type = $_POST['pickup_data']['handling'];
			$session_data  = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_item_key );

			if ( is_string( $cart_item_key ) && '' !== $cart_item_key ) {

				// designate item for pickup
				if ( 'pickup' === $handling_type ) {

					$session_data['handling'] = 'pickup';

					if ( isset( $_POST['pickup_data']['lookup_area'] ) ) {
						$session_data['lookup_area'] = sanitize_text_field( $_POST['pickup_data']['lookup_area'] );
					}

					if ( ! empty( $_POST['pickup_data']['pickup_location_id'] ) ) {

						$pickup_location = wc_local_pickup_plus_get_pickup_location( $_POST['pickup_data']['pickup_location_id'] );

						if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {
							$session_data['pickup_location_id'] = $pickup_location->get_id();
						}
					}

					wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, $session_data );

				// remove any pickup information previously set
				} elseif ( 'ship' === $handling_type ) {

					wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, array(
						'handling'           => 'ship',
						'lookup_area'        => '',
						'pickup_location_id' => 0,
					) );
				}

				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}


	/**
	 * Set a package pickup data, when meant for pickup.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function set_package_handling() {

		check_ajax_referer( 'set-package-handling', 'security' );

		$package_id         = Framework\SV_WC_Helper::get_posted_value( 'package_id' );
		$pickup_date        = Framework\SV_WC_Helper::get_posted_value( 'pickup_date' );
		$pickup_location_id = Framework\SV_WC_Helper::get_posted_value( 'pickup_location_id' );
		$pickup_lookup_area = Framework\SV_WC_Helper::get_posted_value( 'lookup_area' );
		$appointment_offset = Framework\SV_WC_Helper::get_posted_value( 'appointment_offset' );

		if ( is_numeric( $package_id ) || ( is_string( $package_id ) && '' !== $package_id ) ) {

			$previous_pickup_date = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $package_id, 'pickup_date' );

			wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $package_id, [
				'pickup_date'        => $pickup_date,
				'pickup_location_id' => (int) $pickup_location_id,
				'lookup_area'        => sanitize_text_field( $pickup_lookup_area ),
				// reset appointment offset when pickup date changes so the first available time becomes selected by default
				'appointment_offset' => $previous_pickup_date === $pickup_date ? $appointment_offset : '',
			] );

			$package                = wc_local_pickup_plus()->get_packages_instance()->get_shipping_package( $package_id );
			$package_cart_item_keys = ! empty( $package ) ? array_keys( $package['contents'] ) : [];

			if ( wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled() ) {
				// if per-item selection is disabled, set all items to this package's location ID
				$cart_item_keys = array_keys( wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data() );
			} else {
				// otherwise, set package pickup data to all items in the same package
				$cart_item_keys = $package_cart_item_keys;
			}

			if ( ! empty( $cart_item_keys ) ) {

				foreach ( $cart_item_keys as $cart_item_key ) {

					$session_data = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_item_key );

					// set cart item handling for items in this package
					if ( wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled()
					     && wc_local_pickup_plus_shipping_method()->is_item_handling_mode( 'automatic' )
					     && in_array( $cart_item_key, $package_cart_item_keys ) ) {
						$session_data['handling'] = 'pickup';
					}

					if ( $pickup_lookup_area ) {
						$session_data['lookup_area'] = $pickup_lookup_area;
					}

					$pickup_location = wc_local_pickup_plus_get_pickup_location( $pickup_location_id );

					if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {
						$session_data['pickup_location_id'] = $pickup_location->get_id();
					}

					$session_data['pickup_date']        = $pickup_date;
					$session_data['appointment_offset'] = $appointment_offset;

					wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, $session_data );
				}
			}

			wp_send_json_success();
		}

		wp_send_json_error();
	}


	/**
	 * Perform a pickup locations lookup and return results in JSON format.
	 *
	 * Used in frontend to search nearby locations.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function pickup_locations_lookup() {

		check_ajax_referer( 'pickup-locations-lookup', 'security' );

		$data = array();

		if ( ! empty( $_REQUEST['term'] ) ) {

			// gather request variables
			$search_term  = sanitize_text_field( $_REQUEST['term'] );
			$product_id   = isset( $_REQUEST['product_id'] )  ? (int) $_REQUEST['product_id']                       : null;
			$current_area = ! empty( $_REQUEST['area'] )      ? wc_format_country_state_string( $_REQUEST['area'] ) : null;
			$country      = isset( $current_area['country'] ) ? $current_area['country']                            : '';
			$state        = isset( $current_area['state'] )   ? $current_area['state']                              : '';

			// prepare query args for \WP_Query
			$page        = isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] ) ? (int) $_REQUEST['page'] : -1;
			$query_args  = array(
				'post_status'    => 'publish',
				'posts_per_page' => $page > 0 ? $page * 10 : -1,
				'offset'         => $page > 1 ? $page * 10 : 0,
			);

			// obtain coordinates if using geocoding
			if ( wc_local_pickup_plus()->geocoding_enabled() ) {

				// TODO: the following should really be moved to \WC_Local_Pickup_Plus_Pickup_Location_Field::get_lookup_area()
				// where it would also bubble up to the UI as a visual reference. Additionally, we should also properly geolocate
				// the lookup area to the visitor's country. However, that requires more time investment, so this is a quick fix
				// for stores that sell only to a single country. {IT 2017-11-21}

				// if shipping to a single country, limit lookup area to that country
				if ( $country === 'anywhere' || empty( $country ) ) {

					$ship_to_countries = WC()->countries->get_shipping_countries();

					if ( 1 === count( $ship_to_countries ) ) {
						$country = key( $ship_to_countries );
					}

				}

				if ( $country === 'anywhere' || empty( $country ) ) {

					$geocode = $search_term;

				} else {

					$address = array(
						'address_1' => $search_term,
						'country'   => $country,
					);

					if ( ! empty( $state ) ) {
						$address['state'] = $state;
					}

					$address = new \WC_Local_Pickup_Plus_Address( $address );
					$geocode = $address->get_array();
				}

				$coordinates = wc_local_pickup_plus()->get_geocoding_api_instance()->get_coordinates( $geocode );
			}

			// search by distance when there are found coordinates
			if ( ! empty( $coordinates ) ) {

				$origin = $coordinates;

			// search by address (either as fallback if no coordinates found or geocoding is disabled)
			} else {

				// without geocoding we have more limited search possibilities, utilizing only the geodata table with address columns:
				$origin = new \WC_Local_Pickup_Plus_Address( array(
					'country'   => 'anywhere' === $country || empty( $country ) ? '' : $country,
					'state'     => $state,
					// we can't know in advance which entity the user is searching for:
					'name'      => $search_term, // -> they might be typing the place name directly (narrowest)...
					'postcode'  => $search_term, // -> or searching by postcode (narrower)...
					'address_1' => $search_term, // -> or searching by address (narrower)...
					'city'      => $search_term, // -> or searching by city/town (broader)
				) );
			}

			$found_locations = wc_local_pickup_plus_get_pickup_locations_nearby( $origin, $query_args );

			if ( ! empty ( $found_locations ) ) {

				foreach ( $found_locations as $pickup_location ) {

					if ( $product_id > 0 && ! wc_local_pickup_plus_product_can_be_picked_up( $product_id, $pickup_location ) ) {
						continue;
					}

					// Format results as expected by select2 script.
					// The fields 'id' and 'text' are the default ones, everything else can be used by a template formatter.
					$data[] = array(
						'id'      => $pickup_location->get_id(),
						'text'    => $pickup_location->get_name(),
						'name'    => $pickup_location->get_name(),
						'address' => wp_strip_all_tags( $pickup_location->get_address()->get_formatted_html( true ) ),
						'phone'   => $pickup_location->get_phone(),
					);
				}

				wp_send_json_success( $data );
			}
		}

		wp_send_json_error();
	}


	/**
	 * Get a location area (country, state or formatted label) from a location ID.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function get_pickup_location_area() {

		check_ajax_referer( 'get-pickup-location-area', 'security' );

		if (    isset( $_POST['location'] )
		     && ( $location_id = is_numeric( $_POST['location'] ) ? (int) $_POST['location'] : null ) ) {

			$location  = wc_local_pickup_plus_get_pickup_location( $location_id );
			$formatted = isset( $_POST['formatted'] ) && $_POST['formatted'];

			if ( $location && 'publish' === $location->get_post()->post_status ) {

				$country      = $location->get_address()->get_country();
				$state        = $location->get_address()->get_state();
				$states       = WC()->countries->get_states( $country );
				$state_name   = isset( $states[ $state ] ) ? $states[ $state ] : '';
				$countries    = WC()->countries->get_countries();
				$country_name = isset( $countries[ $country ] ) ? $countries[ $country ] : '';

				if ( $formatted ) {
					// send just a label which is the state or country name
					if ( ! empty( $country_name ) ) {
						wp_send_json_success( empty( $state_name ) ? $country_name : $state_name );
					}
				} else {
					// send complete area data
					wp_send_json_success( array(
						'country' => array(
							'code' => $country,
							'name' => $country_name,
						),
						'state'   => array(
							'code' => $state,
							'name' => $state_name,
						),
					) );
				}
			}
		}

		die;
	}


	/**
	 * Sends all the necessary pickup location data to schedule an appointment.
	 *
	 * The data is sent to jQuery DatePicker to build the front-end pickup appointment calendar.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function get_pickup_location_appointment_data() {

		check_ajax_referer( 'get-pickup-location-appointment-data', 'security' );

		if ( isset( $_POST['location'] ) && ( $location_id = is_numeric( $_POST['location'] ) ? (int) $_POST['location'] : null ) ) {

			$location = wc_local_pickup_plus_get_pickup_location( $location_id );

			if ( $location && 'publish' === $location->get_post()->post_status ) {

				wp_send_json_success( $this->prepare_pickup_location_appointment_data( $location ) );
			}
		}

		die;
	}


	/**
	 * Prepares all the necessary pickup location data to schedule an appointment.
	 *
	 * The data is sent to jQuery DatePicker to build the front-end pickup appointment calendar.
	 *
	 * @since 2.9.4
	 *
	 * @param WC_Local_Pickup_Plus_Pickup_Location $location selected pickup location
	 * @return array
	 */
	private function prepare_pickup_location_appointment_data( WC_Local_Pickup_Plus_Pickup_Location $location ) {

		try {

			// local time now is from when we start building our calendar with available dates
			$start_time = new \DateTime( 'now', $location->get_address()->get_timezone() );

		} catch ( \Exception $e ) {

			wc_local_pickup_plus()->log( sprintf( 'Error instantiating DateTime: %1$s', $e->getMessage() ) );
			wp_send_json_error();
			die;
		}

		// the optional lead time is used to offset the first available date by some days
		$first_pickup_time = $location->get_appointments()->get_first_available_pickup_time( $start_time );
		$first_pickup_date = ( clone $first_pickup_time )->setTime( 0, 0, 0 );

		// the deadline defines the farthest selectable day in the calendar
		if ( $location->has_pickup_deadline() ) {
			$pickup_days = max( 1, $location->get_pickup_deadline()->in_days() );
		} else {
			// if no deadline is specified, we'll build a year-long calendar
			$pickup_days = 365;
		}

		// the iteration date will be relative to the start time (adjusted by lead time offset) as long as there's deadline left
		$iteration_date = clone $first_pickup_date;

		// variables used in the while loop below to compile available and unavailable days in calendar
		$available_days    = 0;
		$unavailable_days  = 0;
		$unavailable_dates = [];

		// the iteration date is progressively bumped ahead until there is a sufficient amount of days available for pickup (or a reasonable limit is met at one year length);
		// simultaneously, the unavailable dates are collected: these will be passed to JS to black out specific dates (public holidays, days without opening hours)
		do {

			if ( $iteration_date->format( 'Y-m-d' ) === $first_pickup_date->format( 'Y-m-d' ) ) {

				$minimum_hours = $location->get_appointments()->get_schedule_minimum_hours( $iteration_date );

				// if anytime appointments are enabled, set calendar day to the first pickup time so that
				// the day can be considered as a day that has available times in calendar_day_has_available_times()
				$calendar_day = wc_local_pickup_plus_shipping_method()->is_anytime_appointments_enabled() ? ( clone $first_pickup_time ) : ( clone $iteration_date );

			} else {

				$minimum_hours = null;
				$calendar_day  = clone $iteration_date;
			}

			if (
					  $this->calendar_day_has_available_times( $location, $calendar_day, $start_time )
				 && ! $location->get_public_holidays()->is_public_holiday( $iteration_date )
				 &&   $location->get_business_hours()->has_schedule( $iteration_date->format( 'w' ) )
				 && ! empty( $location->get_business_hours()->get_schedule( $iteration_date->format( 'w' ), false, $minimum_hours ) )
			) {

				$available_days ++;

			} else {

				$unavailable_dates[] = $iteration_date->format( 'Y-m-d' );
				$unavailable_days ++;
			}

			$total_days = $unavailable_days + $available_days;
			$iteration_date->add( new \DateInterval( 'P1D' ) );

		} while ( $total_days < $pickup_days );

		// we cut these additional dates because:
		// - the final iteration date, because it's bumped one day ahead than it should at the end of the previous while loop
		$unavailable_dates[] = $iteration_date->format( 'Y-m-d' );
		// - the day after the final iteration date to rule out any rare glitch that could make an unavailable date selectable
		$unavailable_dates[] = ( clone $iteration_date )->add( new \DateInterval( 'P1D' ) )->format( 'Y-m-d' );
		// - the day before the start date to rule out any rare glitch that could make a day in the past available
		$unavailable_dates[] = ( clone $first_pickup_date )->sub( new \DateInterval( 'P1D' ) )->format( 'Y-m-d' );

		usort( $unavailable_dates, [ $this, 'sort_calendar_dates' ] );

		return [
			// the address is merely used to append some information to the calendar HTML
			'address'             => $location->has_description() ? wp_kses_post( $location->get_address()->get_formatted_html( true ) . "\n" . '<br />' . "\n" . $location->get_description() ) : $location->get_address()->get_formatted_html( true ),
			// first selectable day
			'calendar_start'      => $first_pickup_date->getTimestamp(),
			// when the calendar can't go any further
			'calendar_end'        => $iteration_date->getTimestamp(),
			// dates marked unavailable cannot be selected
			'unavailable_dates'   => array_unique( $unavailable_dates ),
			// default date when opening the calendar for the first time
			'default_date'        => $first_pickup_date->getTimestamp(),
			// if only one day is available and appointments are required, select it automatically
			'auto_select_default' => 1 === $available_days && 'required' === wc_local_pickup_plus_shipping_method()->pickup_appointments_mode(),
		];
	}


	/**
	 * Determines whether a given day has available appointment times.
	 *
	 * If anytime appointments are enabled, a day has available appointment times if not enough appointments have been scheduled for that day.
	 *
	 * @since 2.8.0
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location object
	 * @param \DateTime $calendar_day the start of the day we want to check
	 * @param \DateTime $chosen_time used to calculate the first available pickup time
	 * @return bool
	 */
	private function calendar_day_has_available_times( $pickup_location, $calendar_day, $chosen_time ) {

		if ( wc_local_pickup_plus_shipping_method()->is_anytime_appointments_enabled() ) {

			$end_date            = clone $calendar_day;
			$has_available_times = wc_local_pickup_plus()->get_appointments_instance()->is_appointment_time_available(
				$chosen_time,
				$pickup_location,
				null, // appointment duration is not used when anytime appointments are enabled
				$calendar_day,
				$end_date->setTime( 23, 59, 59 )
			);

		} else {

			$has_available_times = ! empty( $pickup_location->get_appointments()->get_available_times( $calendar_day ) );
		}

		return $has_available_times;
	}


	/**
	 * Sorts calendar dates (`usort` callback helper method).
	 *
	 * @see \WC_Local_Pickup_Plus_Ajax::get_pickup_location_appointment_data()
	 *
	 * @since 2.3.5
	 *
	 * @param string $date_a first date to compare
	 * @param string $date_b second date to compare
	 * @return int
	 */
	private function sort_calendar_dates( $date_a, $date_b ) {

		return (int) strtotime( $date_a ) - (int) strtotime( $date_b );
	}


	/**
	 * Get a list of opening hours for any given day of the week.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function get_pickup_location_opening_hours_list() {

		check_ajax_referer( 'get-pickup-location-opening-hours-list', 'security' );

		if ( isset( $_POST['location'], $_POST['date'] ) ) {

			$location_id = is_numeric( $_POST['location'] ) ? (int) $_POST['location'] : null;

			if ( $location_id ) {

				$date       = $_POST['date'];
				$location   = wc_local_pickup_plus_get_pickup_location( $location_id );
				$package_id = (int) Framework\SV_WC_Helper::get_posted_value( 'package_id' );

				if ( $location && $date ) {
					$list = $this->prepare_pickup_location_opening_hours_list( $location, $date, $package_id );
				} else {
					$list = '';
				}

				if ( ! empty( $list ) ) {
					wp_send_json_success( $list );
				} else {
					wp_send_json_error();
				}
			}
		}

		die;
	}


	/**
	 * Prepares a list of opening hours for any given day of the week.
	 *
	 * @since 2.9.5
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $location pickup location
	 * @param string $date selected appointment date
	 * @param int $package_id the package ID associated with appointment
	 * @return string
	 */
	private function prepare_pickup_location_opening_hours_list( WC_Local_Pickup_Plus_Pickup_Location $location, string $date, int $package_id ) {

		try {
			$chosen_datetime = new \DateTime( $date, $location->get_address()->get_timezone() );
		} catch ( \Exception $e ) {
			return '';
		}

		$list          = '';
		$day           = date( 'w', strtotime( $date ) ); // get day of week from date (0-6, starting from sunday)
		$minimum_hours = $location->get_appointments()->get_schedule_minimum_hours( $chosen_datetime );

		if ( $opening_hours = $location->get_business_hours()->get_schedule( $day, false, $minimum_hours ) ) {

			ob_start(); ?>

			<?php if ( ! empty( $opening_hours ) ) : ?>

				<small class="pickup-location-field-label"><?php
					/* translators: Placeholder: %s - day of the week name */
					printf( __( 'Opening hours for pickup on %s:', 'woocommerce-shipping-local-pickup-plus' ),
						'<strong>' . date_i18n( 'l', strtotime( $date ) ) . '</strong>'
					); ?></small>
				<ul>
					<?php foreach ( $opening_hours as $time_string ) : ?>
						<li><small><?php echo esc_html( $time_string ); ?></small></li>
					<?php endforeach; ?>
				</ul>
				<input
					type="hidden"
					name="_shipping_method_pickup_appointment_offset[<?php echo esc_attr( $package_id ); ?>]"
					value="<?php echo esc_attr( (int) $minimum_hours ); ?>"
				/>

			<?php endif; ?>

			<?php $list .= ob_get_clean();
		}

		return $list;
	}


}
