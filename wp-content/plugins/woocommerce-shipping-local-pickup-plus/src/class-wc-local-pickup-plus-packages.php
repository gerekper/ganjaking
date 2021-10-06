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

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Handles shipping packages.
 *
 * @since 2.3.1
 */
class WC_Local_Pickup_Plus_Packages {


	/**
	 * Sets up the packages handler.
	 *
	 * @since 2.3.1
	 */
	public function __construct() {

		// filter shipping packages based on item handling data from session
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'handle_packages' ), 1 );

		// filter again the shipping packages to toggle Local Pickup Plus available from available rates
		add_filter( 'woocommerce_shipping_packages',      array( $this, 'filter_package_rates' ), 1 );

	}


	/**
	 * Create a package for shipping or pickup.
	 *
	 * @since 2.0.0
	 *
	 * @param array $items items to put into the package
	 * @return array
	 */
	private function create_package( $items ) {

		return [
			'contents'        => $items,
			'contents_cost'   => array_sum( wp_list_pluck( $items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->get_applied_coupons(),
			'user'            => [
				'ID' => get_current_user_id(),
			],
			'destination'     => $this->get_package_destination_address(
				[
					'country'   => WC()->customer->get_billing_country(),
					'state'     => WC()->customer->get_billing_state(),
					'postcode'  => WC()->customer->get_billing_postcode(),
					'city'      => WC()->customer->get_billing_city(),
					'address'   => WC()->customer->get_billing_address(),
					'address_2' => WC()->customer->get_billing_address_2(),
				],
				[
					'country'   => WC()->customer->get_shipping_country(),
					'state'     => WC()->customer->get_shipping_state(),
					'postcode'  => WC()->customer->get_shipping_postcode(),
					'city'      => WC()->customer->get_shipping_city(),
					'address'   => WC()->customer->get_shipping_address(),
					'address_2' => WC()->customer->get_shipping_address_2(),
				]
			),
			'cart_subtotal'   => WC()->cart->get_displayed_subtotal(),
		];
	}


	/**
	 * Gets the package destination address.
	 *
	 * Helper method to check package destination address based on locale settings and address information provided by the customer.
	 *
	 * @since 2.3.17
	 *
	 * @param array $billing_address the customer's billing address
	 * @param array $shipping_address the customer's shipping address
	 * @return array the package destination address
	 */
	private function get_package_destination_address( $billing_address, $shipping_address ) {

		// grab the locales so we can check if the state and/or postcode are required for this particular shipping country
		$locale = WC()->countries->get_country_locale();

		// assume state and postcode are provided
		$state_provided = $postcode_provided = true;

		// check if a specific rule is set for this country making the state not required; o/w check if state is provided
		if ( ! isset( $locale[ WC()->customer->get_shipping_country() ]['state']['required'] ) || $locale[ WC()->customer->get_shipping_country() ]['state']['required'] ) {
			$state_provided = '' !== $shipping_address['state'];
		}

		// check if a specific rule is set for this country making the postcode not required; o/w check if postcode is provided
		if ( isset( $locale[ WC()->customer->get_shipping_country() ]['postcode']['required'] ) && ! $locale[ WC()->customer->get_shipping_country() ]['postcode']['required'] ) {
			$postcode_provided = '' !== $shipping_address['postcode'];
		}

		$set_shipping_address = array_diff_assoc( $billing_address, $shipping_address );

		return ! empty( $set_shipping_address ) && $state_provided && $postcode_provided ? $shipping_address : $billing_address;
	}


	/**
	 * Gets the pickup location ID for a given package.
	 *
	 * @since 2.3.15
	 *
	 * @param array $package package data
	 * @return int pickup location ID
	 */
	public function get_package_pickup_location_id( $package = array() ) {

		$pickup_location_id = 0;

		if ( isset( $package['pickup_location_id'] ) && is_numeric( $package['pickup_location_id'] ) ) {
			$pickup_location_id = 0 === $package['pickup_location_id'] ? $this->get_package_only_pickup_location_id( $package ) : $package['pickup_location_id'];
		}

		return $pickup_location_id;
	}


	/**
	 * Gets the pickup location for a given package.
	 *
	 * @since 2.3.15
	 *
	 * @param array $package package data
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	public function get_package_pickup_location( $package = array() ) {

		return wc_local_pickup_plus_get_pickup_location( $this->get_package_pickup_location_id( $package ) );
	}


	/**
	 * Gets the pickup location id if there is only one pickup location available.
	 *
	 * @since 2.3.15
	 *
	 * @param array $package package data
	 * @return int pickup location ID or 0 when not found
	 */
	public function get_package_only_pickup_location_id( $package = array() ) {

		$pickup_location_id = 0;

		if (      is_array( $package )
			 && ! empty( $package )
			 &&   isset( $package['contents'] )
			 &&   is_array( $package['contents'] ) ) {

			$location_ids = array();

			foreach ( $package['contents'] as $item ) {

				$package_product = isset( $item['data'] ) ? $item['data'] : null;

				if ( $package_product instanceof \WC_Product ) {

					$available_locations = wc_local_pickup_plus()->get_products_instance()->get_product_pickup_locations( $package_product, array( 'fields' => 'ids' ) );
					$location_ids[]      = ! empty( $available_locations ) && 1 === count( $available_locations ) ? current( $available_locations ) : 0;
				}
			}

			$location_ids       = array_unique( $location_ids );
			$pickup_location_id = 1 === count( $location_ids ) ? current( $location_ids ) : 0;
		}

		return $pickup_location_id;
	}


	/**
	 * Returns a pickup location ID when only a single pickup location can be used for a cart item.
	 *
	 * @since 2.3.4
	 *
	 * @param array $cart_item the cart item
	 * @return int a pickup location ID or 0 if no pickup location is determined or there is more than one possible pickup location for the item
	 */
	private function get_cart_item_pickup_location_id( $cart_item ) {

		$product             = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
		$available_locations = $product ? wc_local_pickup_plus()->get_products_instance()->get_product_pickup_locations( $product, array( 'fields' => 'ids' ) ) : null;

		return 1 === count( $available_locations ) ? current( $available_locations ) : 0;
	}


	/**
	 * Returns a pickup location ID when only a single pickup location can be used for a package.
	 *
	 * This covers cases where only a single common, available pickup common location exists for the items in cart or when
	 * using per-order location mode and a location has been set for any other items in cart already.
	 *
	 * @since 2.2.0
	 *
	 * @param array $contents cart item contents
	 * @param array $pickup_data cart item pickup data
	 * @return int a pickup location ID or 0 if no pickup location is determined or there is more than one possible pickup location for the products
	 */
	private function get_cart_item_common_pickup_location_id( array $contents, $pickup_data = array() ) {

		$location_ids = array();

		// Determine if there's only a single pickup location possible for all the cart items combined:
		// some items may be available in all locations, while others may be available in a single location.
		// In the latter case look up the common location.
		foreach ( $contents as $item ) {

			$package_product = isset( $item['data'] ) ? $item['data'] : null;

			if ( $package_product instanceof \WC_Product ) {

				$available_locations = wc_local_pickup_plus()->get_products_instance()->get_product_pickup_locations( $package_product, array( 'fields' => 'ids' ) );
				$location_ids[]      = 1 === count( $available_locations ) ? (int) current( $available_locations ) : 0;
			}
		}

		$location_ids    = array_unique( $location_ids );
		$pickup_location = 1 === count( $location_ids ) ? wc_local_pickup_plus_get_pickup_location( current( $location_ids ) ) : null;

		// determine if the cart item should "inherit" a location from other cart items in per-order location mode
		if ( ! $pickup_location && ! empty( $pickup_data ) && wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled() ) {

			foreach ( $pickup_data as $item_pickup_data ) {

				if ( ! empty( $item_pickup_data['pickup_location_id'] ) ) {
					$pickup_location = wc_local_pickup_plus_get_pickup_location( $item_pickup_data['pickup_location_id'] );
					break;
				}
			}
		}

		return $pickup_location ? $pickup_location->get_id() : 0;
	}


	/**
	 * Determines whether a cart item should be picked up while processing raw cart data.
	 *
	 * @see WC_Local_Pickup_Plus_Packages::handle_packages()
	 *
	 * @since 2.3.0
	 *
	 * @param array $cart_item the item data from cart
	 * @param string $cart_item_key the cart item key that could match a stored session array key
	 * @param array $pickup_data (optional) pickup data from session
	 * @param array $shipping_rates (optional) available shipping rates for the cart item
	 *
	 * @return bool
	 */
	private function cart_item_should_be_picked_up( $cart_item, $cart_item_key, $pickup_data = array(), $shipping_rates = array() ) {

		$pickup        = false;
		$has_item_data = isset( $cart_item['data'] );

		// customer session data indicates that this item should be picked up
		if (    array_key_exists( $cart_item_key, $pickup_data )
		     && isset( $pickup_data[ $cart_item_key ]['handling'] )
		     && 'pickup' === $pickup_data[ $cart_item_key ]['handling'] ) {

			$pickup = true;

			// sanity check for products marked for shipping only
			if ( $has_item_data && ! wc_local_pickup_plus_product_can_be_picked_up( $cart_item['data'] ) ) {
				$pickup = false;
			}

			// sanity check for automatic mode forcing all items to be shipped
			// (there is an item that cannot be picked up and there isn't any item that must be picked up)
			if ( $has_item_data
			     && wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled()
			     && wc_local_pickup_plus_shipping_method()->is_item_handling_mode( 'automatic' )
			     && $this->is_shipping_required()
			     && ! $this->are_shipping_and_pickup_required() ) {
				$pickup = false;
			}

		// sanity check if the cart item must be picked up by product setting
		} elseif ( $has_item_data ) {

			$can_be_picked_up  = wc_local_pickup_plus_product_can_be_picked_up( $cart_item['data'] );
			$must_be_picked_up = wc_local_pickup_plus_product_must_be_picked_up( $cart_item['data'] );
			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			// without a previous preference set in session, the item should be picked up if:
			// - pickup for the item is possible (item can be picked up) AND:
			// - it MUST be picked up OR
			// - there are no other shipping options available (shipping rates) OR
			// - automatic mode is not enabled and the default package handling is pickup OR
			// - automatic mode is enabled, the default package handling is pickup and the cart does not contain a product that cannot be picked up OR
			// - automatic mode is enabled, the default package handling is ship and the cart contains a product that must be picked up (unless the cart already requires a shipping package)
			$pickup = $can_be_picked_up
			          && ( $must_be_picked_up
			               || empty( $shipping_rates )
			               || ( $local_pickup_plus->is_per_order_selection_enabled()
			                    && ! $local_pickup_plus->is_item_handling_mode( 'automatic' )
			                    && $local_pickup_plus->is_default_handling( 'pickup' )
			                    && array_key_exists( $cart_item_key, $pickup_data )
			                    && isset( $pickup_data[ $cart_item_key ]['handling'] )
			                    && 'ship' !== $pickup_data[ $cart_item_key ]['handling'] )
			               || ( $local_pickup_plus->is_per_order_selection_enabled()
			                    && $local_pickup_plus->is_item_handling_mode( 'automatic' )
			                    && $local_pickup_plus->is_default_handling( 'pickup' )
			                    && ! $this->is_shipping_required() )
			               || ( $local_pickup_plus->is_per_order_selection_enabled()
			                    && $local_pickup_plus->is_item_handling_mode( 'automatic' )
			                    && $local_pickup_plus->is_default_handling( 'ship' )
			                    && $this->is_pickup_required()
			                    && ! $this->are_shipping_and_pickup_required() ) );
		}

		return $pickup;
	}


	/**
	 * Filter packages to separate packages for pickup from ordinary packages.
	 *
	 * @since 2.0.0
	 *
	 * @param array $packages the packages array
	 * @return array
	 */
	public function handle_packages( $packages ) {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();
		$pickup_data       = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( null );

		if ( ! empty( $packages ) && $local_pickup_plus->is_available() ) {

			$shipping_rates        = $this->get_rates_for_package( $packages[0] );
			$package_pickup_data   = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( 0 );
			$new_packages          = array();
			$cart_items            = WC()->cart->cart_contents;
			$index                 = 0;
			$pickup_items          = array();
			$ship_items            = array();
			$is_per_order_mode     = wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled();
			$default_location_id   = is_user_logged_in() ? get_user_meta( get_current_user_id(), '_default_pickup_location', true ) : null;

			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				// skip virtual items completely as they don't need any handling
				if ( $cart_item['data'] instanceof \WC_Product && ! $cart_item['data']->needs_shipping() ) {
					continue;
				}

				if ( $this->cart_item_should_be_picked_up( $cart_item, $cart_item_key, $pickup_data, $shipping_rates ) ) {

					$cart_item_pickup_location_id = ! empty( $pickup_data[ $cart_item_key ]['pickup_location_id'] ) ? (int) $pickup_data[ $cart_item_key ]['pickup_location_id'] : 0;

					// special handling for cases when there is only a single pickup location possible for this item
					if ( ! $is_per_order_mode && 0 === $cart_item_pickup_location_id ) {
						$cart_item_pickup_location_id = $this->get_cart_item_pickup_location_id( $cart_item );
					}

					// special handling for cases where there is only a single pickup location possible among all or some of the items in cart
					if ( ! $is_per_order_mode && 0 === $cart_item_pickup_location_id ) {
						$contents                     = isset( $cart_item['contents'] ) ? $cart_item['contents'] : array();
						$cart_item_pickup_location_id = $this->get_cart_item_common_pickup_location_id( $contents, $pickup_data );
					}

					// inherit pickup location from the package when per-order location mode is enabled
					if ( $is_per_order_mode && 0 === $cart_item_pickup_location_id && ! empty( $package_pickup_data ) ) {
						$cart_item_pickup_location_id = ! empty( $package_pickup_data['pickup_location_id'] ) ? $package_pickup_data['pickup_location_id'] : 0;
					}

					// if user has a default/preferred location set, use it
					if ( ! $cart_item_pickup_location_id && $default_location_id > 0 ) {
						$cart_item_pickup_location_id = $default_location_id;
					}

					$pickup_items[ $cart_item_key ]                       = $cart_item;
					$pickup_items[ $cart_item_key ]['pickup_location_id'] = $cart_item_pickup_location_id;
					$pickup_items[ $cart_item_key ]['pickup_date']        = ! empty( $pickup_data[ $cart_item_key ]['pickup_date'] ) ? $pickup_data[ $cart_item_key ]['pickup_date'] : '';
					$pickup_items[ $cart_item_key ]['appointment_offset'] = ! empty( $pickup_data[ $cart_item_key ]['appointment_offset'] ) ? $pickup_data[ $cart_item_key ]['appointment_offset'] : '';

				} else {

					$ship_items[ $cart_item_key ] = $cart_item;
				}
			}

			/**
			 * Filters the cart items separated by handling before they are processed into packages.
			 *
			 * @since 2.2.0
			 *
			 * @param array $items associative array of cart items separated by handling (for pickup or shipping)
			 */
			$items = (array) apply_filters( 'wc_local_pickup_plus_cart_shipping_packages', array(
				'pickup_items' => $pickup_items,
				'ship_items'   => $ship_items,
			) );

			// create pickup packages and put pickup items with the same pickup location in the same package too
			if ( ! empty( $items['pickup_items'] ) ) {

				$same_pickup_locations = array();

				foreach ( $items['pickup_items'] as $item_key => $pickup_item ) {

					$pickup_location_id = isset( $pickup_item['pickup_location_id'] ) ? (int) $pickup_item['pickup_location_id'] : 0;

					// special handling for cases when there is only a single pickup location possible for this item
					if ( isset( $cart_item, $cart_item_pickup_location_id ) && ! $is_per_order_mode && 0 === $cart_item_pickup_location_id ) {
						$cart_item_pickup_location_id = $this->get_cart_item_pickup_location_id( $cart_item );
					}

					// special handling for cases where there is only a single pickup location possible (this has to run again to account for filtered array)
					if ( ! $is_per_order_mode && 0 === $pickup_location_id ) {

						$contents           = is_array( $pickup_items ) ? $pickup_items : array();
						$pickup_location_id = $this->get_cart_item_common_pickup_location_id( $contents, $pickup_data );
					}

					$same_pickup_locations[ (string) $pickup_location_id ][ $item_key ] = $pickup_item;
				}

				foreach ( $same_pickup_locations as $pickup_location_id => $pickup_items ) {

					// default empty
					$pickup_date        = '';
					$appointment_offset = '';

					if ( isset( $packages[ $index ]['pickup_location_id'] ) && (int) $packages[ $index ]['pickup_location_id'] !== (int) $pickup_location_id ) {

						// if the pickup location changed, the pickup date should be reset
						$pickup_date        = '';
						$appointment_offset = '';

					} elseif ( ! empty( $pickup_items['pickup_date'] ) ) {

						// try using an available date and appointment offset from the current package
						$pickup_date        = $pickup_items['pickup_date'] ? $pickup_items['pickup_date'] : '';
						$appointment_offset = ! empty( $pickup_items['appointment_offset'] ) ? $pickup_items['appointment_offset'] : '';

					} elseif ( ! empty( $packages[ $index ]['pickup_date'] ) ) {

						// try using an available date and appointment offset existing in the packages array
						$pickup_date        = $packages[ $index ]['pickup_date'] ? $packages[ $index ]['pickup_date'] : '';
						$appointment_offset = ! empty( $pickup_items['appointment_offset'] ) ? $pickup_items['appointment_offset'] : '';

					} elseif ( $package_data = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $index ) ) {

						// try grabbing the date and appointment offset from an existing package with the same location ID
						if ( isset( $package_data['pickup_location_id'] ) && (int) $package_data['pickup_location_id'] === (int) $pickup_location_id ) {
							$pickup_date        = ! empty( $package_data['pickup_date'] ) ? $package_data['pickup_date'] : '';
							$appointment_offset = ! empty( $package_data['appointment_offset'] ) ? $package_data['appointment_offset'] : '';
						}
					}

					wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $index, [
						'pickup_location_id' => (int) $pickup_location_id,
						'pickup_date'        => $pickup_date,
						'appointment_offset' => $appointment_offset,
					] );

					$new_packages[ $index ]                       = $this->create_package( $pickup_items );
					$new_packages[ $index ]['pickup_location_id'] = (int) $pickup_location_id;
					$new_packages[ $index ]['pickup_date']        = $pickup_date;
					$new_packages[ $index ]['appointment_offset'] = $appointment_offset;

					// if each cart item can have its own shipping method
					if ( $local_pickup_plus->is_per_item_selection_enabled()
					     || ( $local_pickup_plus->is_per_order_selection_enabled()
					          && $local_pickup_plus->is_item_handling_mode( 'customer' ) ) ) {
						$new_packages[ $index ]['ship_via'] = [ $local_pickup_plus->get_method_id() ];
					}

					$index++;
				}
			}

			// create a single package for items meant to be shipped otherwise
			if ( ! empty( $items['ship_items'] ) ) {

				// the index value here right one unit above the last pickup package, so the shipping package will be always the last package
				$new_packages[ $index ] = $this->create_package( $items['ship_items'] );

				// also wipe pickup data from session for this package
				wc_local_pickup_plus()->get_session_instance()->delete_package_pickup_data( $index );
			}

			$packages = $new_packages;
		}

		return $packages;
	}


	/**
	 * Determines whether a package not set for pickup by customer should be picked up.
	 *
	 * This is for internal use to rule out an edge case where the handling toggle cannot be printed because the package has no set rates.
	 *
	 * @see WC_Local_Pickup_Plus_Packages::filter_package_rates()
	 *
	 * @since 2.3.0
	 *
	 * @param array $package package data
	 *
	 * @return bool
	 */
	private function package_should_be_picked_up( $package ) {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();
		$pickup            = false;

		$only_lpp_available = isset( $package['rates'] )
		                      && 1 === count( $package['rates'] )
		                      && $local_pickup_plus->get_method_id() === key( $package['rates'] );

		// Assume a package can be picked up by default when:
		// - no other shipping rates are available for that package (cannot be shipped otherwise)
		// - per order selection is being used
		// - either the default handling is pickup OR automatic grouping is disabled
		if (    $local_pickup_plus
		     && $only_lpp_available
		     && $local_pickup_plus->is_per_order_selection_enabled()
		     && $local_pickup_plus->is_default_handling( 'pickup' ) ) {

			// there are no other shipping methods so we should offer pickup (or split the package later if there are ship-only items)
			$pickup = ! $this->package_can_be_shipped( $package );
		}

		return $pickup;
	}


	/**
	 * Filter package rates to remove Local Pickup Plus option for packages that cannot be picked up
	 * and remove shipping options for packages that must be picked up.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $packages shipping packages array
	 * @return array
	 */
	public function filter_package_rates( $packages ) {

		$local_pickup_plus      = wc_local_pickup_plus_shipping_method();
		$local_pickup_plus_id   = $local_pickup_plus->get_method_id();
		$append_ship_only_items = array();
		$pickup_packages        = array();

		if ( ! empty( $packages ) && $local_pickup_plus->is_available() ) {

			foreach ( $packages as $index => $package ) {

				if ( ! isset( $package['ship_via'] ) && isset( $package['rates'][ $local_pickup_plus_id ] ) ) {

					if ( ! $this->package_can_be_shipped( $package )
					     || ( isset( $package['contents'] )
					          && is_array( $package['contents'] )
					          && $this->package_should_be_picked_up( $package ) ) ) {

						// so we don't unset Local Pickup Rates, however, we need to check if there are any items that cannot be picked up
						foreach ( $package['contents'] as $item_key => $item ) {

							if ( isset( $item['data'] ) && ! wc_local_pickup_plus_product_can_be_picked_up( $item['data'] ) ) {

								// in this case we store the items forced to be shipped in a package that will be appended
								$append_ship_only_items[ $item_key ] = $item;

								unset( $package['contents'][ $item_key ] );
							}
						}

						// ensure the package is set for pickup
						$packages[ $index ]['ship_via'] = $package['ship_via'] = array( $local_pickup_plus_id );
						// remove shipping options
						$packages[ $index ]['rates'] = wc_local_pickup_plus_shipping_method()->get_rates_for_package( $package );

					} elseif ( ! $this->package_can_be_picked_up( $package ) && $this->package_can_be_shipped( $package ) ) {

						// remove pickup option
						unset( $packages[ $index ]['rates'][ $local_pickup_plus_id ] );
						// ensure the package is set for shipping
						if ( ! empty( $packages[ $index ]['rates'] ) ) {
							$packages[ $index ]['ship_via'] = $package['ship_via'] = [ key( $package['rates'] ) ];
						} else {
							unset( $packages[ $index ]['ship_via'] );
						}

					// if package items are not set for pickup and each cart item can have its own shipping method
					} elseif ( ! $this->package_should_be_picked_up( $package )
					           && ( $local_pickup_plus->is_per_item_selection_enabled()
					                || ( $local_pickup_plus->is_per_order_selection_enabled() && $local_pickup_plus->is_item_handling_mode( 'customer' ) ) ) ) {

						// remove pickup option
						unset( $packages[ $index ]['rates'][ $local_pickup_plus_id ] );
						// ensure the package is set for shipping
						if ( ! empty( $packages[ $index ]['rates'] ) ) {
							$packages[ $index ]['ship_via'] = $package['ship_via'] = [ key( $package['rates'] ) ];
						} else {
							unset( $packages[ $index ]['ship_via'] );
						}
					}
				}

				if ( ! empty( $append_ship_only_items ) && empty( $package['contents'] ) ) {

					unset( $packages[ $index ] );

				} elseif ( isset( $package['ship_via'] ) && in_array( $local_pickup_plus_id, $package['ship_via'], true ) ) {

					$pickup_packages[ $index ] = $package;
				}
			}

			// ensure that pickup packages are merged when location per order and automatic grouping are enabled
			if (    count( $pickup_packages ) > 1
			     && $local_pickup_plus->is_per_order_selection_enabled()
			     && $local_pickup_plus->is_item_handling_mode( 'automatic' ) ) {

				$grouped_pickup_package = null;

				// merge pickup packages
				foreach ( $pickup_packages as $index => $package ) {

					if ( ! $grouped_pickup_package ) {
						$grouped_pickup_package = $package;
					} else {
						$grouped_pickup_package['contents'] = array_merge( $grouped_pickup_package['contents'], $package['contents'] );
					}

					// remove from original packages
					unset( $packages[ $index ] );
				}

				// refresh rates
				$rates = $local_pickup_plus->get_rates_for_package( $grouped_pickup_package );
				$rates = array_merge( $this->get_rates_for_package( $grouped_pickup_package ), $rates );

				$grouped_pickup_package['rates'] = $rates;

				$packages[] = $grouped_pickup_package;

				// ensure that there are no "holes" in the package array
				$packages = array_values( $packages );
			}

			if ( ! empty( $append_ship_only_items ) ) {

				$package = $this->create_package( $append_ship_only_items );

				$package['rates'] = $this->get_rates_for_package( $package );

				$packages[ count( $packages ) ] = $package;
			}
		}

		return $packages;
	}


	/**
	 * Returns shipping rates for a package.
	 *
	 * @since 2.3.0
	 *
	 * @param array $package shipping package
	 * @return array shipping rates
	 */
	public function get_rates_for_package( $package ) {

		$available_rates = array();

		if ( ! empty( $package['contents'] ) && ( $shipping_zone = wc_get_shipping_zone( $package ) ) ) {

			/* @type \WC_Shipping_Method[] $shipping_methods */
			$shipping_methods = $shipping_zone->get_shipping_methods( true );

			if ( is_array( $shipping_methods ) ) {

				$available_rates = array( array() );

				foreach ( $shipping_methods as $shipping_method ) {

					/* @type object $package this is an array really, the PHPDoc in WooCommerce is wrong */
					$rates = $shipping_method->get_rates_for_package( $package );

					if ( ! empty( $rates ) ) {
						$available_rates[] = $rates;
					}
				}

				$available_rates = call_user_func_array( 'array_merge', $available_rates );
			}
		}

		return $available_rates;
	}


	/**
	 * Checks whether the package can be shipped or not.
	 *
	 * If there are no shipping methods/rates available, the package cannot be shipped.
	 * Unless per order selection is used and ship is the default handling, then shipping may depend on shipping zones limitations.
	 *
	 * @since 2.3.1
	 *
	 * @param array $package shipping package
	 * @return bool
	 */
	public function package_can_be_shipped( $package ) {

		unset( $package['ship_via'] );

		$can_be_shipped = count( $this->get_rates_for_package( $package ) ) > 0;

		if ( ! $can_be_shipped ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if (    $local_pickup_plus->is_per_order_selection_enabled()
			     && $local_pickup_plus->is_default_handling( 'ship' ) ) {

				$can_be_shipped = true;
			}
		}

		if ( $can_be_shipped && isset( $package['contents'] ) && is_array( $package['contents'] ) ) {

			foreach ( $package['contents'] as $item ) {

				if ( isset( $item['data'] ) && $item['data'] instanceof \WC_Product && wc_local_pickup_plus_product_must_be_picked_up( $item['data'] ) ) {

					$can_be_shipped = false;
					break;
				}
			}
		}

		return $can_be_shipped;
	}


	/**
	 * Checks whether the package can be picked up or not.
	 *
	 * If there is an item that cannot be picked up, the package cannot be picked up.
	 *
	 * @since 2.7.0
	 *
	 * @param array $package shipping package
	 * @return bool
	 */
	public function package_can_be_picked_up( $package ) {

		$can_be_picked_up = true;

		if ( isset( $package['contents'] ) && is_array( $package['contents'] ) ) {

			foreach ( $package['contents'] as $item ) {

				if ( isset( $item['data'] ) && $item['data'] instanceof \WC_Product && ! wc_local_pickup_plus_product_can_be_picked_up( $item['data'] ) ) {

					$can_be_picked_up = false;
					break;
				}
			}
		}

		return $can_be_picked_up;
	}


	/**
	 * Checks whether the package contains must be picked up products only.
	 *
	 * If there is an item that can be shipped, it means the full package must not be picked up.
	 *
	 * @see package_can_be_picked_up
	 *
	 * @since 2.9.3
	 *
	 * @param array $package shipping package
	 * @return bool true if the full package must be picked up
	 */
	public function package_contains_must_pick_up_products_only($package ) {

		if ( isset( $package['contents'] ) && is_array( $package['contents'] ) ) {

			foreach ( $package['contents'] as $item ) {

				// a single item that must not be picked up is enough to determine that the full package must not be picked up
				if ( isset( $item['data'] ) && $item['data'] instanceof \WC_Product && ! wc_local_pickup_plus_product_must_be_picked_up( $item['data'] ) ) {

					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Returns the package for the given cart item.
	 *
	 * Each cart item has a unique key, which can only belong to a single package at a time.
	 * However, cart items do not know which package they are part of.
	 * Given the cart item key, this method looks up the package the cart item is part of.
	 *
	 * @since 2.3.1
	 *
	 * @param string $cart_item_id cart item key
	 * @return array|null shipping package or null if none found
	 */
	public function get_cart_item_package( $cart_item_id ) {

		$the_package = null;
		$packages    = WC()->shipping()->get_packages();

		if ( ! empty( $packages ) ) {
			foreach ( $packages as $package_id => $package ) {

				foreach ( $package['contents'] as $cart_item_key => $item ) {
					if ( $cart_item_id === $cart_item_key ) {

						$the_package = $package;
						break 2;
					}
				}
			}
		}

		return $the_package;
	}


	/**
	 * Gets a shipping package.
	 *
	 * @since 2.3.13
	 *
	 * @param int $package_id the package id (starts at 0, defaults to 0)
	 * @return array|null shipping package data as array or null if not found
	 */
	public function get_shipping_package( $package_id = 0 ) {

		$packages = WC()->shipping()->get_packages();

		if ( empty( $packages ) ) {
			$packages = WC()->cart->get_shipping_packages();
		}

		return ! empty( $packages[ $package_id ] ) ? $packages[ $package_id ] : null;
	}


	/**
	 * Returns the count of packages meant for pickup.
	 *
	 * When no arguments are passed, this helper method will count packages as they are stored in the session.
	 * However, we have a WooCommerce session and a Local Pickup Plus session running alongside with it for necessary internal purposes.
	 * By default, this method will give priority at the WooCommerce session, and if not set or empty, will look at the Local Pickup Plus session for counting.
	 * Given the many ways Local Pickup Plus can work at checkout and exception cases, this may lead to ambiguity or disparity between the possible counts.
	 * For this reason, if you need more certainty, you can pass an array of packages as an argument and the helper method will evaluate those instead.
	 *
	 * @since 2.3.10
	 *
	 * @param array $packages optional packages (defaults to packages in session: WC, if set first, or fallback to LPP session)
	 * @return int
	 */
	public function get_packages_for_pickup_count( $packages = array() ) {

		$pickup_packages = 0;

		if ( $packages = $this->get_packages_for_counting( $packages ) ) {

			foreach( $packages as $package ) {

				// normally a valid pickup location ID on the package indicates that it is meant for pickup
				if ( ! empty( $package['pickup_location_id'] ) && $package['pickup_location_id'] > 0 ) {
					$pickup_packages++;
				// a 'ship via' flag could also unequivocally tell the package is meant for pickup
				} elseif ( ! empty( $package['ship_via'] ) && is_array( $package['ship_via'] ) && 1 === count( $package['ship_via'] ) && in_array( wc_local_pickup_plus_shipping_method_id(), $package['ship_via'], false ) ) {
					$pickup_packages++;
				}
			}
		}

		return $pickup_packages;
	}


	/**
	 * Returns the count of packages meant for shipping.
	 *
	 * Note: may include in the count packages without a chosen shipping method.
	 *
	 * When no arguments are passed, this helper method will count packages as they are stored in the session.
	 * However, we have a WooCommerce session and a Local Pickup Plus session running alongside with it for necessary internal purposes.
	 * By default, this method will give priority at the WooCommerce session, and if not set or empty, will look at the Local Pickup Plus session for counting.
	 * Given the many ways Local Pickup Plus can work at checkout and exception cases, this may lead to ambiguity or disparity between the possible counts.
	 * For this reason, if you need more certainty, you can pass an array of packages as an argument and the helper method will evaluate those instead.
	 *
	 * @since 2.3.10
	 *
	 * @param array $packages optional packages (defaults to packages in session: WC, if set first, or fallback to LPP session)
	 * @return int
	 */
	public function get_packages_for_shipping_count( $packages = array() ) {

		$shipping_packages = 0;

		if ( $packages = $this->get_packages_for_counting( $packages ) ) {
			$pickup_packages   = $this->get_packages_for_pickup_count( $packages );
			$shipping_packages = max( 0, count( $packages ) - $pickup_packages );
		}

		return $shipping_packages;
	}


	/**
	 * Parses packages for counting.
	 *
	 * @since 2.3.15
	 *
	 * @param array $packages associative array, optional (defaults to packages in session)
	 * @return array
	 */
	private function get_packages_for_counting( $packages = array() ) {

		if ( empty( $packages ) || ! is_array( $packages ) ) {

			$packages = wc()->shipping()->get_packages();

			if ( empty( $packages ) ) {
				$packages = wc()->session->get( 'wc_local_pickup_plus_packages' );
			}

			if ( empty( $packages ) ) {
				$packages = wc()->session->get( 'wc_local_pickup_plus_cart_items' );
			}
		}

		return $packages;
	}


	/**
	 * Checks if the product combination in the cart
	 * requires at least one shipping package.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function is_shipping_required() {

		$shipping_required = false;
		$cart_contents     = WC()->cart->cart_contents;

		foreach ( $cart_contents as $cart_item ) {

			if ( isset( $cart_item['data'] ) && $cart_item['data'] instanceof \WC_Product ) {

				if ( wc_local_pickup_plus_product_must_be_shipped( $cart_item['data'] ) ) {

					$shipping_required = true;
					break;
				}
			}
		}

		return $shipping_required;
	}


	/**
	 * Checks if the product combination in the cart
	 * requires at least one pickup package.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function is_pickup_required() {

		$pickup_required = false;
		$cart_contents   = WC()->cart->cart_contents;

		foreach ( $cart_contents as $cart_item ) {

			if ( isset( $cart_item['data'] ) && $cart_item['data'] instanceof \WC_Product ) {

				if ( wc_local_pickup_plus_product_must_be_picked_up( $cart_item['data'] ) ) {

					$pickup_required = true;
					break;
				}
			}
		}

		return $pickup_required;
	}


	/**
	 * Checks if the product combination in the cart
	 * requires at least one shipping and one pickup packages.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function are_shipping_and_pickup_required() {

		return $this->is_shipping_required() && $this->is_pickup_required();
	}


	/**
	 * Gets the chosen shipping method for a given package.
	 *
	 * @since 2.7.5
	 * @deprecated 2.9.3
	 *
	 * TODO remove this method by April 2022 or by version 3.0.0 (whichever comes first) {FN 2020-27-11}
	 *
	 * @param int|string $key package ID
	 * @param array $package package data
	 * @return string shipping method ID
	 */
	public function get_chosen_shipping_method_for_package( $key, $package ) {

		wc_deprecated_function( __METHOD__, '2.9.3', 'wc_get_chosen_shipping_method_for_package()' );

		return wc_get_chosen_shipping_method_for_package( $key, $package );
	}


}
