<?php
/**
 * Product Add-ons cart
 *
 * @package WC_Product_Addons/Classes/Cart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_Cart class.
 */
class Product_Addon_Cart {

	/**
	 * Constructor.
	 */
	function __construct() {
		// Add to cart.
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 1 );

		// Load cart data per page load.
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

		// Get item data to display.
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		// Add item data to the cart.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

		// Validate when adding to cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 999, 3 );

		// Add meta to order.
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 10, 3 );

		// order again functionality.
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 're_add_cart_item_data' ), 10, 3 );
	}

	/**
	 * Adjust add-on proce if set on cart.
	 *
	 * @since 2.7.0
	 * @version 2.9.0
	 * @param array $cart_item Cart item data.
	 * @return array
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item ) ) {
			$price = (float) $cart_item['data']->get_price( 'edit' );

			// Compatibility with Smart Coupons self declared gift amount purchase.
			if ( empty( $price ) && ! empty( $_POST['credit_called'] ) ) {
				// $_POST['credit_called'] is an array.
				if ( isset( $_POST['credit_called'][ $cart_item['data']->get_id() ] ) ) {
					$price = (float) $_POST['credit_called'][ $cart_item['data']->get_id() ];
				}
			}

			if ( empty( $price ) && ! empty( $cart_item['credit_amount'] ) ) {
				$price = (float) $cart_item['credit_amount'];
			}

			foreach ( $cart_item['addons'] as $addon ) {
				if ( $addon['price'] ) {
					$price += (float) $addon['price'];
				}
			}

			$cart_item['data']->set_price( $price );
		}

		return $cart_item;
	}

	/**
	 * Get cart item from session.
	 *
	 * @param array $cart_item Cart item data.
	 * @param array $values    Cart item values.
	 * @return array
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			$cart_item['addons'] = $values['addons'];
			$cart_item = $this->add_cart_item( $cart_item );
		}

		return $cart_item;
	}

	/**
	 * Get item data.
	 *
	 * @param array $other_data Other data.
	 * @param array $cart_item  Cart item data.
	 * @return array
	 */
	public function get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['addons'] ) ) {
			foreach ( $cart_item['addons'] as $addon ) {
				$name = $addon['name'];

				if ( $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', '__return_true' ) ) {
					$name .= ' (' . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon['price'], $cart_item['data'], true ) ) . ')';
				}

				$other_data[] = array(
					'name'    => $name,
					'value'   => $addon['value'],
					'display' => isset( $addon['display'] ) ? $addon['display'] : '',
				);
			}
		}

		return $other_data;
	}

	/**
	 * Checks if the added product is a grouped product.
	 *
	 * @param int $product_id Product ID.
	 * @return bool
	 */
	public function is_grouped_product( $product_id ) {
		$product = wc_get_product( $product_id );

		return $product->is_type( 'grouped' );
	}

	/**
	 * Add cart item data.
	 *
	 * @param array $cart_item_meta Cart item meta data.
	 * @param int   $product_id     Product ID.
	 * @param bool  $test           If this is a test i.e. just getting data but not adding to cart. Used to prevent uploads.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id, $post_data = null, $test = false ) {
		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}

		// Technically we could just use $post_data['add-to-cart'] for product id
		// however since we don't know if $product_id has been filtered higher up the chain
		// and to be on safe side, use this check.
		if ( ! empty( $post_data['add-to-cart'] ) && $this->is_grouped_product( $post_data['add-to-cart'] ) ) {
			$product_id = $post_data['add-to-cart'];
		}

		$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_id );

		if ( empty( $cart_item_meta['addons'] ) ) {
			$cart_item_meta['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( dirname( __FILE__ ) . '/fields/abstract-class-product-addon-field.php' );

			foreach ( $product_addons as $addon ) {

				$value = isset( $post_data[ 'addon-' . $addon['field-name'] ] ) ? $post_data[ 'addon-' . $addon['field-name'] ] : '';

				if ( is_array( $value ) ) {
					$value = array_map( 'stripslashes', $value );
				} else {
					$value = stripslashes( $value );
				}

				switch ( $addon['type'] ) {
					case 'checkbox' :
					case 'radiobutton' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-list.php' );
						$field = new Product_Addon_Field_List( $addon, $value );
						break;
					case 'custom' :
					case 'custom_textarea' :
					case 'custom_price' :
					case 'custom_letters_only' :
					case 'custom_digits_only' :
					case 'custom_letters_or_digits' :
					case 'custom_email' :
					case 'input_multiplier' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-custom.php' );
						$field = new Product_Addon_Field_Custom( $addon, $value );
						break;
					case 'select' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-select.php' );
						$field = new Product_Addon_Field_Select( $addon, $value );
						break;
					case 'file_upload' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-file-upload.php' );
						$field = new Product_Addon_Field_File_Upload( $addon, $value, $test );
						break;
				}

				$data = $field->get_cart_item_data();

				if ( is_wp_error( $data ) ) {
					// Throw exception for add_to_cart to pickup.
					throw new Exception( $data->get_error_message() );
				} elseif ( $data ) {
					$cart_item_meta['addons'] = array_merge( $cart_item_meta['addons'], apply_filters( 'woocommerce_product_addon_cart_item_data', $data, $addon, $product_id, $post_data ) );
				}
			}
		}

		return $cart_item_meta;
	}

	/**
	 * Validate add cart item.
	 *
	 * @param bool $passed     If passed validation.
	 * @param int  $product_id Product ID.
	 * @param int  $qty        Quantity.
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = null ) {
		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}

		$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_id );

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( dirname( __FILE__ ) . '/fields/abstract-class-product-addon-field.php' );

			foreach ( $product_addons as $addon ) {

				$value = isset( $post_data[ 'addon-' . $addon['field-name'] ] ) ? $post_data[ 'addon-' . $addon['field-name'] ] : '';

				if ( is_array( $value ) ) {
					$value = array_map( 'stripslashes', $value );
				} else {
					$value = stripslashes( $value );
				}

				switch ( $addon['type'] ) {
					case 'checkbox' :
					case 'radiobutton' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-list.php' );
						$field = new Product_Addon_Field_List( $addon, $value );
						break;
					case 'custom' :
					case 'custom_textarea' :
					case 'custom_price' :
					case 'custom_letters_only' :
					case 'custom_digits_only' :
					case 'custom_letters_or_digits' :
					case 'custom_email' :
					case 'input_multiplier' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-custom.php' );
						$field = new Product_Addon_Field_Custom( $addon, $value );
						break;
					case 'select' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-select.php' );
						$field = new Product_Addon_Field_Select( $addon, $value );
						break;
					case 'file_upload' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-file-upload.php' );
						$field = new Product_Addon_Field_File_Upload( $addon, $value );
						break;
				}

				$data = $field->validate();

				if ( is_wp_error( $data ) ) {
					wc_add_notice( $data->get_error_message(), 'error' );
					return false;
				}

				do_action( 'woocommerce_validate_posted_addon_data', $addon );
			}
		}

		return $passed;
	}

	/**
	 * Include add-ons line item meta.
	 *
	 * @param  WC_Order_Item_Product $item          Order item data.
	 * @param  string                $cart_item_key Cart item key.
	 * @param  array                 $values        Order item values.
	 */
	public function order_line_item( $item, $cart_item_key, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			foreach ( $values['addons'] as $addon ) {
				$key = $addon['name'];

				if ( $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
					$key .= ' (' . strip_tags( wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon['price'], $values['data'], true ) ) ) . ')';
				}

				$item->add_meta_data( $key, $addon['value'] );
			}
		}
	}

	/**
	 * Re-order.
	 *
	 * @param arary    $cart_item_meta Cart item meta.
	 * @param array    $product        Cart item.
	 * @param WC_order $order          Order object.
	 *
	 * @return array Cart item meta
	 */
	public function re_add_cart_item_data( $cart_item_meta, $product, $order ) {
		$is_pre_30 = version_compare( WC_VERSION, '3.0.0', '<' );

		// Disable validation.
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 999, 3 );

		// Get addon data.
		$product_addons = WC_Product_Addons_Helper::get_product_addons( $product['product_id'] );

		if ( empty( $cart_item_meta['addons'] ) ) {
			$cart_item_meta['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( dirname( __FILE__ ) . '/fields/abstract-class-product-addon-field.php' );

			foreach ( $product_addons as $addon ) {
				$value = '';
				$field = '';

				switch ( $addon['type'] ) {
					case 'checkbox' :
					case 'radiobutton' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-list.php' );

						$value = array();

						if ( $is_pre_30 ) {
							foreach ( $product['item_meta'] as $key => $meta ) {
								if ( stripos( $key, $addon['name'] ) === 0 ) {
									if ( 1 < count( $meta ) ) {
										$value[] = array_map( 'sanitize_title', $meta );
									} else {
										$value[] = sanitize_title( $meta );
									}
								}
							}
						} else {
							foreach ( $product->get_meta_data() as $meta ) {
								if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
									if ( 1 < count( $meta->value ) ) {
										$value[] = array_map( 'sanitize_title', $meta->value );
									} else {
										$value[] = sanitize_title( $meta->value );
									}
								} 
							}
						}

						if ( empty( $value ) ) {
							break;
						}

						$field = new Product_Addon_Field_List( $addon, $value );
						break;
					case 'select' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-select.php' );

						$value = '';

						if ( $is_pre_30 ) {
							foreach ( $product['item_meta'] as $key => $meta ) {
								if ( stripos( $key, $addon['name'] ) === 0 ) {
									$value = sanitize_title( $meta );

									break;
								}
							}
						} else {
							foreach ( $product->get_meta_data() as $meta ) {
								if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
									$value = sanitize_title( $meta->value );

									break;
								}
							}
						}

						if ( empty( $value ) ) {
							break;
						}

						$loop = 0;

						foreach ( $addon['options'] as $option ) {
							$loop++;

							if ( sanitize_title( $option['label'] ) == $value ) {
								$value = $value . '-' . $loop;
								break;
							}
						}

						$field = new Product_Addon_Field_Select( $addon, $value );
						break;
					case 'custom' :
					case 'custom_textarea' :
					case 'custom_price' :
					case 'input_multiplier' :
					case 'custom_letters_only' :
					case 'custom_digits_only' :
					case 'custom_letters_or_digits' :
					case 'custom_email' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-custom.php' );

						$value = array();

						if ( $is_pre_30 ) {
							foreach ( $product['item_meta'] as $key => $meta ) {
								foreach ( $addon['options'] as $option ) {
									if ( stripos( $key, $addon['name'] ) === 0 && stristr( $key, $option['label'] ) ) {
										$value[ sanitize_title( $option['label'] ) ] = $meta;
									}
								}
							}
						} else {
							foreach ( $product->get_meta_data() as $meta ) {
								foreach ( $addon['options'] as $option ) {
									if ( stripos( $meta->key, $addon['name'] ) === 0 && stristr( $meta->key, $option['label'] ) ) {
										$value[ sanitize_title( $option['label'] ) ] = $meta->value;
									}
								}
							}
						}

						if ( empty( $value ) ) {
							break;
						}

						$field = new Product_Addon_Field_Custom( $addon, $value );
						break;
					case 'file_upload' :
						include_once( dirname( __FILE__ ) . '/fields/class-product-addon-field-file-upload.php' );

						$value = array();

						if ( $is_pre_30 ) {
							foreach ( $product['item_meta'] as $key => $meta ) {
								foreach ( $addon['options'] as $option ) {
									if ( stripos( $key, $addon['name'] ) === 0 && stristr( $key, $option['label'] ) ) {
										$value[ sanitize_title( $option['label'] ) ] = $meta;
									}
								}
							}
						} else {
							foreach ( $product->get_meta_data() as $meta ) {
								foreach ( $addon['options'] as $option ) {
									if ( stripos( $meta->key, $addon['name'] ) === 0 && stristr( $meta->key, $option['label'] ) ) {
										$value[ sanitize_title( $option['label'] ) ] = $meta->value;
									}
								}
							}
						}

						if ( empty( $value ) ) {
							break;
						}

						$field = new Product_Addon_Field_File_Upload( $addon, $value );
						break;
				}

				// Make sure a field is set (if not it could be product with no add-ons).
				if ( $field ) {

					$data = $field->get_cart_item_data();

					if ( is_wp_error( $data ) ) {
						wc_add_notice( $data->get_error_message(), 'error' );
					} elseif ( $data ) {
						// Get the post data.
						$post_data = $_POST;

						$cart_item_meta['addons'] = array_merge( $cart_item_meta['addons'], apply_filters( 'woocommerce_product_addon_reorder_cart_item_data', $data, $addon, $product['product_id'], $post_data ) );
					}
				}
			}
		}

		return $cart_item_meta;
	}
}
