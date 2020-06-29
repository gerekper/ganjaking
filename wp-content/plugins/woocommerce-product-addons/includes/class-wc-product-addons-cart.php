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
 * WC_Product_Addons_Cart class.
 */
class WC_Product_Addons_Cart {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Load cart data per page load.
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

		// Add item data to the cart.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20 );

		// Get item data to display.
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		// Validate when adding to cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 999, 3 );

		// Add meta to order.
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 10, 3 );

		// Order again functionality.
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 're_add_cart_item_data' ), 10, 3 );
	}

	/**
	 * Validate add cart item. Note: Fires before add_cart_item_data.
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
			include_once( dirname( __FILE__ ) . '/fields/abstract-wc-product-addons-field.php' );

			foreach ( $product_addons as $addon ) {
				// If type is heading, skip.
				if ( 'heading' === $addon['type'] ) {
					continue;
				}

				$value = isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '';

				if ( is_array( $value ) ) {
					$value = array_map( 'stripslashes', $value );
				} else {
					$value = stripslashes( $value );
				}

				switch ( $addon['type'] ) {
					case 'checkbox':
						include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php' );
						$field = new WC_Product_Addons_Field_List( $addon, $value );
						break;
					case 'multiple_choice':
						switch ( $addon['display'] ) {
							case 'radiobutton':
								include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php' );
								$field = new WC_Product_Addons_Field_List( $addon, $value );
								break;
							case 'images':
							case 'select':
								include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-select.php' );
								$field = new WC_Product_Addons_Field_Select( $addon, $value );
								break;
						}
						break;
					case 'custom_text':
					case 'custom_textarea':
					case 'custom_price':
					case 'input_multiplier':
						include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-custom.php' );
						$field = new WC_Product_Addons_Field_Custom( $addon, $value );
						break;
					case 'file_upload':
						include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-file-upload.php' );
						$field = new WC_Product_Addons_Field_File_Upload( $addon, $value );
						break;
					default:
						// Continue to the next field in case the type is not recognized (instead of causing a fatal error)
						continue 2;
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
	 * Add cart item data action. Fires before add to cart action and add cart item filter.
	 *
	 * @param array $cart_item_data Cart item meta data.
	 * @param int   $product_id     Product ID.
	 * @param int   $variation_id
	 * @param int   $quantity
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id ) {
		if ( isset( $_POST ) && ! empty( $product_id ) ) {
			$post_data = $_POST;
		} else {
			return;
		}

		$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_id );

		if ( empty( $cart_item_data['addons'] ) ) {
			$cart_item_data['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( dirname( __FILE__ ) . '/fields/abstract-wc-product-addons-field.php' );

			foreach ( $product_addons as $addon ) {
				// If type is heading, skip.
				if ( 'heading' === $addon['type'] ) {
					continue;
				}

				$value = isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '';

				if ( is_array( $value ) ) {
					$value = array_map( 'stripslashes', $value );
				} else {
					$value = stripslashes( $value );
				}

				switch ( $addon['type'] ) {
					case 'checkbox':
						include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php' );
						$field = new WC_Product_Addons_Field_List( $addon, $value );
						break;
					case 'multiple_choice':
						switch ( $addon['display'] ) {
							case 'radiobutton':
								include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php' );
								$field = new WC_Product_Addons_Field_List( $addon, $value );
								break;
							case 'images':
							case 'select':
								include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-select.php' );
								$field = new WC_Product_Addons_Field_Select( $addon, $value );
								break;
						}
						break;
					case 'custom_text':
					case 'custom_textarea':
					case 'custom_price':
					case 'input_multiplier':
						include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-custom.php' );
						$field = new WC_Product_Addons_Field_Custom( $addon, $value );
						break;
					case 'file_upload':
						include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-file-upload.php' );
						$field = new WC_Product_Addons_Field_File_Upload( $addon, $value );
						break;
				}

				$data = $field->get_cart_item_data();

				if ( is_wp_error( $data ) ) {
					// Throw exception for add_to_cart to pickup.
					throw new Exception( $data->get_error_message() );
				} elseif ( $data ) {
					$cart_item_data['addons'] = array_merge( $cart_item_data['addons'], apply_filters( 'woocommerce_product_addon_cart_item_data', $data, $addon, $product_id, $post_data ) );
				}
			}
		}

		return $cart_item_data;
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
				$key           = $addon['name'];
				$price_type    = $addon['price_type'];
				$product       = $item->get_product();
				$product_price = $product->get_price();

				/*
				 * For percentage based price type we want
				 * to show the calculated price instead of
				 * the price of the add-on itself and in this
				 * case its not a price but a percentage.
				 * Also if the product price is zero, then there
				 * is nothing to calculate for percentage so
				 * don't show any price.
				 */
				if ( $addon['price'] && 'percentage_based' === $price_type && 0 != $product_price ) {
					$addon_price = $product_price * ( $addon['price'] / 100 );
				} else {
					$addon_price = $addon['price'];
				}
				$price = html_entity_decode(
					strip_tags( wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon_price, $values['data'] ) ) ),
					ENT_QUOTES,
					get_bloginfo( 'charset' )
				);

				/*
				 * If there is an add-on price, add the price of the add-on
				 * to the label name.
				 */
				if ( $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
					$key .= ' (' . $price . ')';
				}

				if ( 'custom_price' === $addon['field_type'] ) {
					$addon['value'] = $addon['price'];
				}

				$item->add_meta_data( $key, $addon['value'] );
			}
		}
	}

	/**
	 * Re-order.
	 *
	 * @since 3.0.0
	 * @param array    $cart_item_meta Cart item data.
	 * @param array    $item           Cart item.
	 * @param WC_order $order          Order object.
	 *
	 * @return array Cart item data
	 */
	public function re_add_cart_item_data( $cart_item_data, $item, $order ) {
		// Disable validation.
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 999, 3 );

		// Get addon data.
		$product_addons = WC_Product_Addons_Helper::get_product_addons( $item['product_id'] );

		if ( empty( $cart_item_data['addons'] ) ) {
			$cart_item_data['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/abstract-wc-product-addons-field.php' );

			foreach ( $product_addons as $addon ) {
				$value = '';
				$field = '';

				// If type is heading, skip.
				if ( 'heading' === $addon['type'] ) {
					continue;
				}

				switch ( $addon['type'] ) {
					case 'checkbox' :
						include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php' );

						$value = array();

						foreach ( $item->get_meta_data() as $meta ) {
							if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
								if ( is_array( $meta->value ) && ! empty( $meta->value ) ) {
									$value[] = array_map( 'sanitize_title', $meta->value );
								} else {
									$value[] = sanitize_title( $meta->value );
								}
							}
						}

						if ( empty( $value ) ) {
							continue 2; // Skip to next addon in foreach loop.
						}

						$field = new WC_Product_Addons_Field_List( $addon, $value );
						break;
					case 'multiple_choice' :
						$value = array();
						switch ( $addon['display'] ) {
							case 'radiobutton':
								include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php' );

								$value = array();

								foreach ( $item->get_meta_data() as $meta ) {
									if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
										if ( is_array( $meta->value ) && ! empty( $meta->value ) ) {
											$value[] = array_map( 'sanitize_title', $meta->value );
										} else {
											$value[] = sanitize_title( $meta->value );
										}
									}
								}

								if ( empty( $value ) ) {
									continue 3; // Skip to next addon in foreach loop. Need to use 3 because we have two nested switch statements.
								}

								$field = new WC_Product_Addons_Field_List( $addon, $value );
								break;
							case 'images':
							case 'select':
								include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-select.php' );

								foreach ( $item->get_meta_data() as $meta ) {
									if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
										$value = sanitize_title( $meta->value );
										break;
									}
								}

								if ( empty( $value ) ) {
									continue 3; // Skip to next addon in foreach loop. Need to use 3 because we have two nested switch statements.
								}

								$loop = 0;

								foreach ( $addon['options'] as $option ) {
									$loop++;

									if ( sanitize_title( $option['label'] ) == $value ) {
										$value = $value . '-' . $loop;
										break;
									}
								}

								$field = new WC_Product_Addons_Field_Select( $addon, $value );
								break;
						}
						break;
					case 'select' :
						include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-select.php' );

						foreach ( $item->get_meta_data() as $meta ) {
							if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
								$value = sanitize_title( $meta->value );

								break;
							}
						}

						if ( empty( $value ) ) {
							continue 2; // Skip to next addon in foreach loop.
						}

						$loop = 0;

						foreach ( $addon['options'] as $option ) {
							$loop++;

							if ( sanitize_title( $option['label'] ) == $value ) {
								$value = $value . '-' . $loop;
								break;
							}
						}

						$field = new WC_Product_Addons_Field_Select( $addon, $value );
						break;
					case 'custom_text' :
					case 'custom_textarea' :
					case 'custom_price' :
					case 'input_multiplier':
						include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-custom.php' );

						foreach ( $item->get_meta_data() as $meta ) {
							if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
								$value = sanitize_title( $meta->value );

								break;
							}
						}

						if ( empty( $value ) ) {
							continue 2; // Skip to next addon in foreach loop.
						}

						$field = new WC_Product_Addons_Field_Custom( $addon, $value );
						break;
					case 'file_upload':
						include_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-file-upload.php' );

						foreach ( $item->get_meta_data() as $meta ) {
							if ( stripos( $meta->key, $addon['name'] ) === 0 ) {
								$value = wc_clean( $meta->value );
								break;
							}
						}

						if ( empty( $value ) ) {
							continue 2; // Skip to next addon in foreach loop.
						}

						$field = new WC_Product_Addons_Field_File_Upload( $addon, $value );
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

						$cart_item_data['addons'] = array_merge( $cart_item_data['addons'], apply_filters( 'woocommerce_product_addon_reorder_cart_item_data', $data, $addon, $item['product_id'], $post_data ) );
					}
				}
			}
		}

		return $cart_item_data;
	}

	/**
	 * Add cart item. Fires after add cart item data filter.
	 *
	 * @since 3.0.0
	 * @param array $cart_item_data Cart item meta data.
	 *
	 * @return array
	 */
	public function add_cart_item( $cart_item_data ) {
		$quantity = $cart_item_data['quantity'];

		if ( ! empty( $cart_item_data['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item_data ) ) {
			$price = (float) $cart_item_data['data']->get_price( 'edit' );

			// Compatibility with Smart Coupons self declared gift amount purchase.
			if ( empty( $price ) && ! empty( $_POST['credit_called'] ) ) {
				// $_POST['credit_called'] is an array.
				if ( isset( $_POST['credit_called'][ $cart_item_data['data']->get_id() ] ) ) {
					$price = (float) $_POST['credit_called'][ $cart_item_data['data']->get_id() ];
				}
			}

			if ( empty( $price ) && ! empty( $cart_item_data['credit_amount'] ) ) {
				$price = (float) $cart_item_data['credit_amount'];
			}

			// Save the price before price type calculations to be used later.
			$cart_item_data['addons_price_before_calc'] = (float) $price;

			foreach ( $cart_item_data['addons'] as $addon ) {
				$price_type  = $addon['price_type'];
				$addon_price = $addon['price'];

				switch ( $price_type ) {
					case 'percentage_based':
						$price += (float) ( $cart_item_data['data']->get_price( 'edit' ) * ( $addon_price / 100 ) );
						break;
					case 'flat_fee':
						$price += (float) ( $addon_price / $quantity );
						break;
					default:
						$price += (float) $addon_price;
						break;
				}
			}

			$cart_item_data['data']->set_price( $price );
		}

		return $cart_item_data;
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
				$price = isset( $cart_item['addons_price_before_calc'] ) ? $cart_item['addons_price_before_calc'] : $addon['price'];
				$name  = $addon['name'];

				if ( 0 == $addon['price'] ) {
					$name .= '';
				} elseif ( 'percentage_based' === $addon['price_type'] && 0 == $price ) {
					$name .= '';
				} elseif ( 'percentage_based' !== $addon['price_type'] && $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', '__return_true' ) ) {
					$name .= ' (' . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon['price'], $cart_item['data'], true ) ) . ')';
				} else {
					$_product = new WC_Product( $cart_item['product_id'] );
					$_product->set_price( $price * ( $addon['price'] / 100 ) );
					$name .= ' (' . WC()->cart->get_product_price( $_product ) . ')';
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
}
