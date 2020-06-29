<?php

if ( ! function_exists( 'yith_wcmas_get_user_custom_addresses' ) ) {
	/**
	 * Returns all the custom addresses by the given user ID
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param int|bool $user_id The user ID
	 *
	 * @return array The custom addresses array
	 */
	function yith_wcmas_get_user_custom_addresses( $user_id = false ) {
		if ( ! $user_id ) {
			return maybe_unserialize( WC()->session->get( 'ywcmas_guest_user_addresses' ) );
		}
		return maybe_unserialize( get_user_meta( $user_id, 'yith_wcmas_shipping_addresses', true ) );
	}
}

if ( ! function_exists( 'yith_wcmas_get_user_default_and_custom_addresses' ) ) {
	/**
	 * Returns all custom and default addresses (billing and shipping addresses) merged in one array
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param int|bool $user_id The user ID
	 *
	 * @return array $addresses All user's addresses array
	 */
	function yith_wcmas_get_user_default_and_custom_addresses( $user_id = false ) {

		if ( ! $user_id ) {
			return maybe_unserialize( ! empty( WC()->session->get( 'ywcmas_guest_user_addresses' ) ) ? WC()->session->get( 'ywcmas_guest_user_addresses' ) : '' );
		}

		$addresses = array();
		$types = array(
			YITH_WCMAS_BILLING_ADDRESS_ID => 'billing_',
			YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID => 'shipping_'
		);
		foreach ( $types as $key => $type ) {

			if( ( $country = get_user_meta( $user_id, $type . 'country', true ) ) == false ) {
				continue;
			}

			$fields = array_keys( WC()->countries->get_address_fields( $country, $type ) );
			foreach( (array) $fields as $field ) {
				$addresses[ $key ][ $field ] =  get_user_meta( $user_id, $field, true );
			}
		}

		if ( yith_wcmas_get_user_custom_addresses( $user_id ) ) {
			$addresses = array_merge( $addresses, yith_wcmas_get_user_custom_addresses( $user_id ) );
		}
		return $addresses;
	}
}

if ( ! function_exists( 'yith_wcmas_get_user_address_by_id' ) ) {
	/**
	 * Returns the address by the address ID given
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param $address_id string The address ID to find
	 * @param int|bool $user_id The user ID
	 *
	 * @return array The address array
	 */
	function yith_wcmas_get_user_address_by_id( $address_id, $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$all_addresses = yith_wcmas_get_user_default_and_custom_addresses( $user_id );
		if ( ! $all_addresses ) {
			return false;
		}
		if ( ! isset( $all_addresses[ $address_id ] ) ) {
			return false;
		}
		return $all_addresses[ $address_id ];
	}
}

if ( ! function_exists( 'yith_wcmas_print_addresses_select_options' ) ) {
	/**
	 * Prints the <option> of an addresses <select>
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param bool $selected The value which should be marked as selected
	 * @param bool $customer_id The customer ID
	 */
	function yith_wcmas_print_addresses_select_options( $selected = false, $customer_id = false, $maybe_local_pickup = false ) {
		if ( 0 === $customer_id && function_exists( 'get_current_screen' ) && get_current_screen() && 'shop_order' == get_current_screen()->id ) {
			// If this is called from admin and the user of the order is a guest user, no $addresses are set.
			$addresses = false;
		} else {
			// Otherwise, get the user addresses (even if the user is a guest user).
			$addresses = yith_wcmas_get_user_default_and_custom_addresses( $customer_id );
			if ( is_array( $addresses ) ) {
				ksort( $addresses );
			}
		}
		if ( $addresses ) {
			foreach ( $addresses as $address_id => $address ) {
				$address_name = $address_id;
				$prefix = 'shipping_';
				if ( YITH_WCMAS_BILLING_ADDRESS_ID == $address_id ) {
					$address_name = esc_html__( 'Billing Address', 'yith-multiple-shipping-addresses-for-woocommerce' );
					$prefix = 'billing_';
				} elseif ( YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID == $address_id ) {
					$address_name = esc_html__( 'Default Shipping Address', 'yith-multiple-shipping-addresses-for-woocommerce' );
				}

				$customer_name = $address[$prefix . 'first_name'] . ' ' . $address[$prefix . 'last_name'];

				$full_address = $address[$prefix . 'address_1'];
				if ( ! empty( $address[$prefix . 'address_2'] ) )
					$full_address .= ' ' . $address[$prefix . 'address_2'];

				$country_name = WC()->countries->get_countries()[$address[$prefix . 'country']];
				$state_name = '';
				if ( ! empty( $address[$prefix . 'state'] ) ) {
					$state_name = strlen( $address[$prefix . 'state'] ) == 2 ? WC()->countries->get_states( $address[$prefix . 'country'] )[$address[$prefix . 'state']] : $address[$prefix . 'state'];
				}

				$location = $address[$prefix . 'city'];
				if ( $state_name )
					$location .= ' - ' . $state_name;
				$location .= ', ' . $address[$prefix . 'postcode'] . ' ' . $country_name;

				$address_info = apply_filters( 'ywcmas_input_select_address', $customer_name . ', ' . $full_address . ', ' . $location, $address );

				$selected_attr = $selected == $address_id ? 'selected' : '';
				echo '<option ' . $selected_attr . ' value="' . $address_id . '">' . $address_name . ' [' . $address_info . ']</option>';

			}
			if ( $maybe_local_pickup ) {
				$show_local_pickup = false;
				$zones = WC_Shipping_Zones::get_zones();
				if ( $zones ) {
					foreach ( $zones as $zone_id => $zone_data ) {
						$zone = WC_Shipping_Zones::get_zone( $zone_id );
						$shipping_methods = $zone->get_shipping_methods( true );
						foreach ( $shipping_methods as $shipping_method ) {
							if ( $shipping_method instanceof WC_Shipping_Local_Pickup ) {
								$show_local_pickup = true;
								break;
							}
						}
					}
				}

				if ( $show_local_pickup ) {
					$selected_attr = $selected == 'local_pickup' ? 'selected' : '';
					echo '<option ' . $selected_attr . ' value="local_pickup">' . esc_html__( 'Local pickup', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</option>';
				}
			}
		} else {
			echo '<option selected value="_no-address">' . esc_html__( 'No addresses available', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</option>';
		}
	}
}


if ( ! function_exists( 'yith_wcmas_print_shipping_statuses_select_options' ) ) {
	/**
	 * @param bool $selected
	 */
	function yith_wcmas_print_shipping_statuses_select_options( $selected = false, $is_local_pickup = false ) {
		$statuses = yith_wcmas_shipping_item_statuses();
		if ( $statuses ) {
			foreach ( $statuses as $status_id => $status_name ) {
				if ( ! $is_local_pickup && 'wcmas-pickup' == $status_id ) {
					continue;
				}
				$selected_attr = $selected == $status_id ? 'selected' : '';
				echo '<option ' . $selected_attr . ' value="' . $status_id . '">' . $status_name . '</option>';
			}
		}
	}
}


if ( ! function_exists( 'yith_wcmas_get_multi_shipping_array' ) ) {
	/**
	 * From multi shipping data array, generate a new array containing the info needed for creating the cart packages
	 *
	 * Structure:
	 * array(
	 *  [<shipping_id>] => array(
	 *                      [<item_id>] => array(
	 *                                      ['qty'] => (int)
	 *                                     )
	 *                     )
	 * )
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 * @return $multi_shipping array The Multi Shipping array
	 */
	function yith_wcmas_get_multi_shipping_array() {
		$multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		$multi_shipping = array();
		if ( $multi_shipping_data ) {
			foreach ( $multi_shipping_data as $item_id => $item ) {
				foreach ( $item as $shipping_selector_id => $shipping_selector ) {
					if ( ! isset( $multi_shipping[$shipping_selector['shipping']] ) ) {
						$multi_shipping[$shipping_selector['shipping']] = array();
					}
					if ( ! isset( $multi_shipping[$shipping_selector['shipping']][$item_id] ) ) {
						$multi_shipping[$shipping_selector['shipping']][$item_id] = array( 'qty' => 0 );
					}
					if ( isset( $multi_shipping[$shipping_selector['shipping']][$item_id]['qty'] ) ) {
						$multi_shipping[$shipping_selector['shipping']][$item_id]['qty'] += (int) $shipping_selector['qty'];
					}
				}

			}
		}
		return $multi_shipping;
	}
}

if ( ! function_exists( 'yith_wcmas_shipping_item_statuses' ) ) {
	/**
	 * Return the array of predefined statuses for shipping items
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @return $statuses array The array of shipping item statuses
	 */
	function yith_wcmas_shipping_item_statuses() {
		$statuses = array(
			'wcmas-processing' => _x( 'Processing', 'Shipping item status', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			'wcmas-shipped'    => _x( 'Shipped', 'Shipping item status', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			'wcmas-cancelled'  => _x( 'Cancelled', 'Shipping item status', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			'wcmas-returned'   => _x( 'Returned', 'Shipping item status', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			'wcmas-pickup'     => _x( 'Local pickup ready', 'Shipping item status', 'yith-multiple-shipping-addresses-for-woocommerce' ),
		);
		return apply_filters( 'yith_wcmas_shipping_item_statuses', $statuses );
	}
}

if ( ! function_exists( 'yith_wcmas_shipping_address_from_destination_array' ) ) {
	/**
	 * Prints the shipping address from detination array. It can print in a single line or with breaking lines
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param $destination array Destination array
	 * @param $single_line bool Whether to use single line or not. True for single line. Single line by default.
	 *
	 * @return $address_info string The address
	 */
	function yith_wcmas_shipping_address_from_destination_array( $destination, $single_line = true ) {
		$br = '<br>';
		if ( $single_line ) {
			$br = '';
		}

		$customer_name = !empty( $destination['first_name'] ) ? $destination['first_name'] : '';
		if ( ! empty( $destination['last_name'] ) )
			$customer_name .= ' ' . $destination['last_name'];
		if ( ! empty( $destination['company'] ) ) {
			if ( $customer_name )
				$customer_name .= ' - ';
			$customer_name .= $destination['company'];
		}
		if ( $customer_name )
			$customer_name .= ', ' . $br;

		$full_address = !empty( $destination['address'] ) ? $destination['address'] : '';
		if ( ! empty( $destination['address_2'] ) )
			$full_address .= ' ' . $destination['address_2'];
		if ( $full_address )
			$full_address .= ', ' . $br;

		$country_name = '';
		if ( ! empty( $destination['country'] ) ) {
			$country_name = WC()->countries->get_countries()[$destination['country']];
		}
		$state_name = '';
		if ( ! empty( $destination['state'] ) ) {
			$state_name = strlen( $destination['state'] ) == 2 || strlen( $destination['state'] ) == 1 ? WC()->countries->get_states( $destination['country'] )[$destination['state']] : $destination['state'];
		}

		$location = !empty( $destination['city'] ) ? $destination['city'] : '';
		if ( $state_name )
			$location .= ' - ' . $state_name;
		$location .= ', ' . $destination['postcode'] . ' ' . $country_name;

		$address_info = apply_filters( 'ywcmas_print_address_from_destionation_array', $customer_name . $full_address . $location, $destination );

		return $address_info;
	}
}
if ( ! function_exists( 'yith_wcmas_array_insert' ) ) {
	/**
	 * Insert an $element in the middle of an array, pointing the $position where the $element should be placed in.
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param array $array
	 * @param int|string $position
	 * @param mixed $element
	 */
	function yith_wcmas_array_insert( &$array, $position, $element ) {
		if ( is_int( $position ) ) {
			array_splice( $array, $position, 0, $element );
		} else {
			$pos   = array_search( $position, array_keys( $array ) );
			$array = array_merge(
				array_slice( $array, 0, $pos ),
				$element,
				array_slice( $array, $pos )
			);
		}
	}
}

if ( ! function_exists( 'yith_wcmas_item_is_excluded' ) ) {
	/**
	 * Checks if an item is excluded by product or category
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 *
	 * @param $item_id string The cart item id
	 *
	 * @return bool
	 */
	function yith_wcmas_item_is_excluded( $item_id ) {
		$meta_exclude = '_ycmas_exclude_for_multi_shipping';
		$product = WC()->cart->get_cart_item( $item_id )['data'];
		$product_is_excluded = yit_get_prop( $product, $meta_exclude );

		$product_parent = null;
		$parent_is_excluded = null;
		if ( 'variation' == $product->get_type() ) {
			$product_parent = wc_get_product( $product->get_parent_id() );
			$parent_is_excluded = yit_get_prop( $product_parent, $meta_exclude );
		}
		if ( $product_is_excluded || $parent_is_excluded ) {
			return true;
		}

		$category_ids = $product->get_category_ids();
		$parent_category_ids = ! empty( $product_parent ) ? $product_parent->get_category_ids() : '';
		if ( $category_ids ) {
			foreach ( $category_ids as $category_id ) {
				if ( get_term_meta( $category_id, $meta_exclude, true ) ) {
					return true;
				}
			}
		}
		if ( $parent_category_ids ) {
			foreach ( $parent_category_ids as $category_id ) {
				if ( get_term_meta( $category_id, $meta_exclude, true ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcmas_order_has_multi_shipping' ) ) {
	/**
	 * Checks if an order has multiple shipping items
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.4
	 *
	 * @param $order WC_Order The order object
	 *
	 * @return bool
	 */
	function yith_wcmas_order_has_multi_shipping( $order ) {
		$multi_shipping = false;
		$shipping_items = $order->get_items( 'shipping' );
		if ( $shipping_items ) {
			foreach ( $shipping_items as $shipping_item_id => $shipping_item ) {
				if ( $shipping_item->get_meta( 'ywcmas_shipping_destination' ) ) {
					$multi_shipping = true;
					break;
				}
			}
		}
		return $multi_shipping;
	}
}

