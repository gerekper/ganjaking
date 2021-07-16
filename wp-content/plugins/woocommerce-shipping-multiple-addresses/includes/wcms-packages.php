<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_MS_Packages {
	private $wcms;

	public function __construct( WC_Ship_Multiple $wcms ) {
		$this->wcms = $wcms;

		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'shipping_packages' ) );
	}

	/**
	* Filter the shipping packages to break it up into multiple packages
	*
	* @param array $packages
	* @return array
	*/
	public function shipping_packages( $packages ) {

		$wcms_packages = array();
		$settings      = $this->wcms->settings;
		$methods       = ( wcms_session_isset( 'shipping_methods' ) ) ? wcms_session_get( 'shipping_methods' ) : array();

		// If multiple shipping is already setup then split packages
		$sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
		if ( ! empty( $sess_cart_addresses ) ) {

			// Group items into ship-to addresses
			$addresses = wcms_session_get( 'cart_item_addresses' );

			$products_array = array();
			$address_fields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );

			// Loop through all cart items
			if ( wcms_count_real_cart_items() > 0 ) {
				foreach ( wcms_get_real_cart_items() as $cart_item_key => $values ) {
					$qty = $values['quantity'];

					// Split each cart item by quantity
					for ( $i = 1; $i <= $qty; $i++ ) {
						if ( isset( $addresses[ 'shipping_first_name_' . $cart_item_key . '_' . $values['product_id'] . '_' . $i ] ) ) {
							$address = array();

							foreach ( $address_fields as $field_name => $field ) {
								$address_key   = str_replace( 'shipping_', '', $field_name );
								$addresses_key = $field_name . '_' . $cart_item_key . '_' . $values['product_id'] . '_' . $i;
								$address[ $address_key ] = ( isset( $addresses[ $addresses_key ] ) ) ? $addresses[ $addresses_key ] : '';
							}
						} else {
							$address = array();

							foreach ( $address_fields as $field_name => $field ) {
								$address_key = str_replace( 'shipping_', '', $field_name );
								$address[ $address_key ] = '';
							}
						}

						$current_address = wcms_get_formatted_address( $address );
						$key             = md5( $current_address );

						// Update values for individual cart items
						$_value          = $values;
						$price           = $_value['line_total'] / $qty;
						$tax             = $_value['line_tax'] / $qty;
						$sub             = $_value['line_subtotal'] / $qty;
						$sub_tax         = $_value['line_subtotal_tax'] / $qty;

						$_value['cart_key']          = $cart_item_key;
						$_value['quantity']          = 1;
						$_value['line_total']        = $price;
						$_value['line_tax']          = $tax;
						$_value['line_subtotal']     = $sub;
						$_value['line_subtotal_tax'] = $sub_tax;
						$meta                        = md5( WC_MS_Compatibility::get_item_data( $_value ) );

						$method = $this->wcms->get_product_shipping_method( $values['product_id'] );
						if ( ! $method ) {
							$method = '';
						}

						$key .= $method;

						if ( isset( $products_array[ $key ] ) ) {

							// If the same product exists, add to the qty and cost
							$found = false;
							foreach ( $products_array[ $key ]['products'] as $idx => $prod ) {
								if ( $prod['id'] == $_value['product_id'] ) {
									if ( isset( $prod['value']['variation_id'], $_value['variation_id'] ) && $prod['value']['variation_id'] != $_value['variation_id'] ) {
										continue;
									}

									if ( $meta == $prod['meta'] ) {
										$found = true;
										$products_array[ $key ]['products'][ $idx ]['value']['quantity'] += 1;
										$products_array[ $key ]['products'][ $idx ]['value']['line_total'] += $_value['line_total'];
										$products_array[ $key ]['products'][ $idx ]['value']['line_tax'] += $_value['line_tax'];
										$products_array[ $key ]['products'][ $idx ]['value']['line_subtotal'] += $_value['line_subtotal'];
										$products_array[ $key ]['products'][ $idx ]['value']['line_subtotal_tax'] += $_value['line_subtotal_tax'];
										break;
									}
								}
							}

							// Add a new product
							if ( ! $found ) {
								$products_array[ $key ]['products'][] = array(
									'id' => $_value['product_id'],
									'meta' => $meta,
									'value' => $_value
								);
							}

						} else {

							$products_array[$key] = array(
								'products'  => array(
									array(
										'id' => $_value['product_id'],
										'meta' => $meta,
										'value' => $_value
									)
								),
								'country'  => $address['country'],
								'city'     => $address['city'],
								'state'    => $address['state'],
								'postcode' => $address['postcode'],
								'address'  => $address,
							);
						}

						if ( ! empty( $method ) ) {
							$products_array[ $key ]['method'] = $method;
						}
					}
				}

				// Create packages from split products array
				if ( ! empty( $products_array ) ) {
					$wcms_packages = array();
					foreach ( $products_array as $idx => $group ) {
						$pkg = array(
							'contents'      => array(),
							'contents_cost' => 0,
							'cart_subtotal' => 0,
							'destination'   => $group['address']
						);

						if ( isset( $group['method'] ) ) {
							$pkg['method'] = $group['method'];
						}

						if ( isset( $methods[ $idx ] ) ) {
							$pkg['selected_method'] = $methods[ $idx ];
						}

						foreach ( $group['products'] as $item ) {
							$data = (array) apply_filters( 'woocommerce_add_cart_item_data', array(), $item['value']['product_id'], $item['value']['variation_id'] );

							if ( isset( $item['value']['addons'] ) ) { 
								$data['addons'] = $item['value']['addons']; 
							}

							// Composite Products support. Manually add the composite data in the cart_item_data array to match the existing cart_item_key
							if ( isset( $item['value']['composite_data'] ) ) {
								$data['composite_data'] = $item['value']['composite_data'];
							}

							if ( isset( $item['value']['composite_children'] ) ) {
								$data['composite_children'] = array();
							}

							// Gravity forms support
							if ( isset( $item['value']['_gravity_form_data'] ) ) {
								$data['_gravity_form_data'] = $item['value']['_gravity_form_data'];
							}

							if ( isset( $item['value']['_gravity_form_lead'] ) ) {
								$data['_gravity_form_lead'] = $item['value']['_gravity_form_lead'];
							}

							$cart_item_id = WC()->cart->generate_cart_id( $item['value']['product_id'], $item['value']['variation_id'], $item['value']['variation'], $data );

							$item['value']['package_idx'] = $idx;

							$pkg['contents'][ $cart_item_id ] = $item['value'];
							if ( $item['value']['data']->needs_shipping() ) {
								$pkg['contents_cost'] += $item['value']['line_total'];

								if( WC()->cart->display_prices_including_tax() ){
									$pkg['cart_subtotal'] += $item['value']['line_subtotal'] + $item['value']['line_subtotal_tax'];
								}else{
									$pkg['cart_subtotal'] += $item['value']['line_subtotal'];
								}
							}
						}

						$wcms_packages[] = $pkg;
					}

					// Return shipping packages which we created
					if ( ! empty( $wcms_packages ) ) {
						wc_enqueue_js( '_multi_shipping = true;' );
						$packages = $this->normalize_packages_address( $wcms_packages );
					}

				}

			}
		}

		wcms_session_set( 'wcms_packages', $packages );
		return $packages;
	}

	/**
	 * This method copies the destination and full address from $base_package if it exists over to the current package index
	 * @param $packages
	 * @return array modified $packages
	 */
	public function normalize_packages_address( $packages ) {
		$default = $this->get_default_shipping_address();

		if ( ! empty( $default ) ) {
			foreach ( $packages as $idx => $package ) {
				if ( ( ! isset( $package['destination'] ) || $this->wcms->is_address_empty( $package['destination'] ) ) && ! $this->wcms->is_address_empty( $default ) ) {
					$packages[ $idx ]['destination'] = $default;
				}
			}
		}

		return $packages;
	}

	/**
	 * Return a default address based on the customer information
	 * @return array default address
	 */
	public function get_default_shipping_address() {

		if ( WC_MS_Compatibility::is_wc_version_gte( '3.0' ) ) {
			$first_name = WC()->customer->get_shipping_first_name();
			$last_name  = WC()->customer->get_shipping_last_name();
			$company    = WC()->customer->get_shipping_company();
		} else {
			$user_id    = get_current_user_id();
			$first_name = $user_id ? get_user_meta( $user_id, 'shipping_first_name', true ) : '';
			$last_name  = $user_id ? get_user_meta( $user_id, 'shipping_last_name', true ) : '';
			$company    = $user_id ? get_user_meta( $user_id, 'shipping_company', true ) : '';
		}

		return array(
			'first_name'    => $first_name,
			'last_name'     => $last_name,
			'company'       => $company,
			'address_1'     => WC()->customer->get_shipping_address(),
			'address_2'     => WC()->customer->get_shipping_address_2(),
			'city'          => WC()->customer->get_shipping_city(),
			'state'         => WC()->customer->get_shipping_state(),
			'postcode'      => WC()->customer->get_shipping_postcode(),
			'country'       => WC()->customer->get_shipping_country()
		);
	}


}
