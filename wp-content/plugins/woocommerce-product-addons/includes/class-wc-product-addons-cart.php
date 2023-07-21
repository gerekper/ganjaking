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
 *
 * @class    WC_Product_Addons_Cart
 * @version  6.4.4
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

		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_price_on_quantity_update' ), 20, 4 );

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
			include_once dirname( __FILE__ ) . '/fields/abstract-wc-product-addons-field.php';

			foreach ( $product_addons as $addon ) {
				// If type is heading, skip.
				if ( 'heading' === $addon['type'] ) {
					continue;
				}

				$value = wp_unslash( isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '' );

				switch ( $addon['type'] ) {
					case 'checkbox':
						include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php';
						$field = new WC_Product_Addons_Field_List( $addon, $value );
						break;
					case 'multiple_choice':
						switch ( $addon['display'] ) {
							case 'radiobutton':
								include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php';
								$field = new WC_Product_Addons_Field_List( $addon, $value );
								break;
							case 'images':
							case 'select':
								include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-select.php';
								$field = new WC_Product_Addons_Field_Select( $addon, $value );
								break;
						}
						break;
					case 'custom_text':
					case 'custom_textarea':
					case 'custom_price':
					case 'input_multiplier':
						include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-custom.php';
						$field = new WC_Product_Addons_Field_Custom( $addon, $value );
						break;
					case 'file_upload':
						include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-file-upload.php';
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
			include_once dirname( __FILE__ ) . '/fields/abstract-wc-product-addons-field.php';

			foreach ( $product_addons as $addon ) {
				// If type is heading, skip.
				if ( 'heading' === $addon['type'] ) {
					continue;
				}

				$value = wp_unslash( isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '' );

				switch ( $addon['type'] ) {
					case 'checkbox':
						include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php';
						$field = new WC_Product_Addons_Field_List( $addon, $value );
						break;
					case 'multiple_choice':
						switch ( $addon['display'] ) {
							case 'radiobutton':
								include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php';
								$field = new WC_Product_Addons_Field_List( $addon, $value );
								break;
							case 'images':
							case 'select':
								include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-select.php';
								$field = new WC_Product_Addons_Field_Select( $addon, $value );
								break;
						}
						break;
					case 'custom_text':
					case 'custom_textarea':
					case 'custom_price':
					case 'input_multiplier':
						include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-custom.php';
						$field = new WC_Product_Addons_Field_Custom( $addon, $value );
						break;
					case 'file_upload':
						include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-file-upload.php';
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

			$ids = array();

			foreach ( $values['addons'] as $addon ) {
				$value         = $addon[ 'value' ];
				$price_type    = $addon['price_type'];
				$product       = $item->get_product();
				$product_price = $product->get_price();

				/*
				 * Create a clone of the current cart item and set its price equal to the add-on price.
				 * This will allow extensions to discount the add-on price.
				 */
				$cloned_product = WC_Product_Addons_Helper::create_product_with_filtered_addon_prices( $values[ 'data' ], $addon[ 'price' ] );
				$addon['price'] = $cloned_product->get_price();

				/*
				 * Deprecated 'woocommerce_addons_add_price_to_name' since v6.4.0.
				 * New filter: 'woocommerce_addons_add_order_price_to_value'
				 *
				 * Use this filter to display the price next to each selected add-on option.
				 * By default, add-on prices show up only next to flat fee add-ons.
				 *
				 * @param boolean
				 */
				apply_filters_deprecated( 'woocommerce_addons_add_price_to_name', array( false, $item ), '6.4.0', 'woocommerce_addons_add_order_price_to_value' );

				$add_price_to_value = apply_filters( 'woocommerce_addons_add_order_price_to_value', false, $item );

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

				$GLOBALS[ 'product' ] = $cloned_product;

				$price = html_entity_decode(
					strip_tags( wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon_price ) ) ),
					ENT_QUOTES,
					get_bloginfo( 'charset' )
				);

				unset( $GLOBALS[ 'product' ] );

				/*
				 * If there is an add-on price, add the price of the add-on
				 * to the selected option.
				 */
				if ( 'flat_fee' === $price_type && $addon['price'] && $add_price_to_value ) {
					$value .= sprintf( _x( ' (+ %1$s)', 'flat fee addon price in order', 'woocommerce-product-addons' ), $price );
				} elseif ( ( 'quantity_based' === $price_type || 'percentage_based' === $price_type ) && $addon['price'] && $add_price_to_value ) {
					$value .= sprintf( _x( ' (%1$s)', 'addon price in order', 'woocommerce-product-addons' ), $price );
				} elseif ( 'custom_price' === $addon['field_type'] ) {
					$value = sprintf( _x( ' (%1$s)', 'custom addon price in order', 'woocommerce-product-addons' ), $price );
				}

				$meta_data = [
					'key'        => $addon[ 'name' ],
					'value'      => $value,
					'id'         => $addon[ 'id' ]
				];
				$meta_data = apply_filters( 'woocommerce_product_addons_order_line_item_meta', $meta_data, $addon, $item, $values );

				$item->add_meta_data( $meta_data['key'], $meta_data['value'] );

				$ids[] = $meta_data;
			}

			$item->add_meta_data( '_pao_ids', $ids );
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

		// When renewing a subscription, add-on data are already part of $cart_item_data[ 'subscription_renewal' ][ 'custom_line_item_meta' ][ '_pao_ids' ].
		// WooCommerce Subscriptions is responsible for rendering these add-ons.
		if ( isset( $cart_item_data[ 'subscription_renewal' ] ) ) {
			return $cart_item_data;
		}

		// Get addon data.
		$product_addons   = WC_Product_Addons_Helper::get_product_addons( $item['product_id'] );
		$ids              = $item->get_meta( '_pao_ids', true );

		// Backwards compatibility for orders with Addons without ID.
		if ( empty( $ids ) ) {
			$ids = $item->get_meta_data();
		}

		if ( empty( $cart_item_data['addons'] ) ) {
			$cart_item_data['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/abstract-wc-product-addons-field.php';

			foreach ( $product_addons as $addon ) {
				$value = '';
				$field = '';

				// If type is heading, skip.
				if ( 'heading' === $addon['type'] ) {
					continue;
				}

				switch ( $addon['type'] ) {
					case 'checkbox':
						include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php';

						$value = $this->get_addon_meta_value( $ids, $addon, 'checkbox' );

						if ( empty( $value ) ) {
							continue 2; // Skip to next addon in foreach loop.
						}

						$field = new WC_Product_Addons_Field_List( $addon, $value );
						break;
					case 'multiple_choice':
						$value = array();
						switch ( $addon['display'] ) {
							case 'radiobutton':
								include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php';

								$value = $this->get_addon_meta_value( $ids, $addon, 'radiobutton' );

								if ( empty( $value ) ) {
									continue 3; // Skip to next addon in foreach loop. Need to use 3 because we have two nested switch statements.
								}

								$field = new WC_Product_Addons_Field_List( $addon, $value );
								break;
							case 'images':
							case 'select':
								include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-select.php';

								$value = $this->get_addon_meta_value( $ids, $addon, 'select' );

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
					case 'select':
						include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-select.php';

						$value = $this->get_addon_meta_value( $ids, $addon, 'select' );


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
					case 'custom_text':
					case 'custom_textarea':
					case 'custom_price':
					case 'input_multiplier':
						include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-custom.php';

						$value = $this->get_addon_meta_value( $ids, $addon, 'input_multiplier' );

						if ( empty( $value ) ) {
							continue 2; // Skip to next addon in foreach loop.
						}

						$field = new WC_Product_Addons_Field_Custom( $addon, $value );
						break;
					case 'file_upload':
						include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-file-upload.php';

						$value = $this->get_addon_meta_value( $ids, $addon, 'file_upload' );

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
	 * Updates the product price based on the add-ons and the quantity.
	 *
	 * @param array $cart_item_data Cart item meta data.
	 * @param int   $quantity       Quantity of products in that cart item.
	 * @param array $prices         Array of prices for that product to use in
	 *                              calculations.
	 *
	 * @return array
	 */
	public function update_product_price( $cart_item_data, $quantity, $prices ) {
		if ( ! empty( $cart_item_data['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item_data ) ) {
			$price         = $prices['price'];
			$regular_price = $prices['regular_price'];
			$sale_price    = $prices['sale_price'];

			// Compatibility with Smart Coupons self declared gift amount purchase.
			if ( empty( $price ) && ! empty( $_POST['credit_called'] ) ) {
				// $_POST['credit_called'] is an array.
				if ( isset( $_POST['credit_called'][ $cart_item_data['data']->get_id() ] ) ) {
					$price         = (float) $_POST['credit_called'][ $cart_item_data['data']->get_id() ];
					$regular_price = $price;
					$sale_price    = $price;
				}
			}

			if ( empty( $price ) && ! empty( $cart_item_data['credit_amount'] ) ) {
				$price         = (float) $cart_item_data['credit_amount'];
				$regular_price = $price;
				$sale_price    = $price;
			}

			// Save the price before price type calculations to be used later.
			$cart_item_data['addons_price_before_calc']         = (float) $price;
			$cart_item_data['addons_regular_price_before_calc'] = (float) $regular_price;
			$cart_item_data['addons_sale_price_before_calc']    = (float) $sale_price;
			$cart_item_data[ 'addons_flat_fees_sum' ]           = 0;

			foreach ( $cart_item_data['addons'] as $addon ) {
				$price_type  = $addon['price_type'];
				$addon_price = $addon['price'];

				switch ( $price_type ) {
					case 'percentage_based':
						$price         += (float) ( $cart_item_data['addons_price_before_calc'] * ( $addon_price / 100 ) );
						$regular_price += (float) ( $cart_item_data['addons_regular_price_before_calc'] * ( $addon_price / 100 ) );
						$sale_price    += (float) ( $cart_item_data['addons_sale_price_before_calc'] * ( $addon_price / 100 ) );
						break;
					case 'flat_fee':
						$flat_fee = $quantity > 0 ? (float) ( $addon_price / $quantity ) : 0;
						$price                                    += $flat_fee;
						$regular_price                            += $flat_fee;
						$sale_price                               += $flat_fee;
						$cart_item_data[ 'addons_flat_fees_sum' ] += $flat_fee;
						break;
					default:
						$price         += (float) $addon_price;
						$regular_price += (float) $addon_price;
						$sale_price    += (float) $addon_price;
						break;
				}
			}

			$updated_product_prices = [
				'price'         => $price,
				'regular_price' => $regular_price,
				'sale_price'    => $sale_price,
			];
			$updated_product_prices = apply_filters( 'woocommerce_product_addons_update_product_price', $updated_product_prices, $cart_item_data, $prices );

			$cart_item_data['data']->set_price( $updated_product_prices['price'] );

			// Only update regular price if it was defined.
			$has_regular_price = is_numeric( $cart_item_data['data']->get_regular_price( 'edit' ) );
			if ( $has_regular_price ) {
				$cart_item_data['data']->set_regular_price( $updated_product_prices['regular_price'] );
			}

			// Only update sale price if it was defined.
			$has_sale_price = is_numeric( $cart_item_data['data']->get_sale_price( 'edit' ) );
			if ( $has_sale_price ) {
				$cart_item_data['data']->set_sale_price( $updated_product_prices['sale_price'] );
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
		$prices = array(
			'price'         => (float) $cart_item_data['data']->get_price( 'edit' ),
			'regular_price' => (float) $cart_item_data['data']->get_regular_price( 'edit' ),
			'sale_price'    => (float) $cart_item_data['data']->get_sale_price( 'edit' ),
		);

		return $this->update_product_price( $cart_item_data, $cart_item_data['quantity'], $prices );
	}

	/**
	 * Update cart item quantity.
	 *
	 * @param array    $cart_item_key Cart item key.
	 * @param integer  $quantity      New quantity of the product.
	 * @param integer  $old_quantity  Old quantity of the product.
	 * @param \WC_Cart $cart          WC Cart object.
	 *
	 * @return array
	 */
	public function update_price_on_quantity_update( $cart_item_key, $quantity, $old_quantity, $cart ) {
		$cart_item_data = $cart->get_cart_item( $cart_item_key );

		if ( ! empty( $cart_item_data['addons'] ) ) {
			$prices = array(
				'price'         => $cart_item_data['addons_price_before_calc'],
				'regular_price' => $cart_item_data['addons_regular_price_before_calc'],
				'sale_price'    => $cart_item_data['addons_sale_price_before_calc'],
			);

			// Set new cart item data, when cart item quantity changes.
			$cart_item_data             = $this->update_product_price( $cart_item_data, $quantity, $prices );
			$contents                   = $cart->get_cart_contents();
			$contents[ $cart_item_key ] = $cart_item_data;
			$cart->set_cart_contents( $contents );
		}
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
			$prices              = array(
				'price'         => (float) $cart_item['data']->get_price( 'edit' ),
				'regular_price' => (float) $cart_item['data']->get_regular_price( 'edit' ),
				'sale_price'    => (float) $cart_item['data']->get_sale_price( 'edit' ),
			);
			$cart_item['addons'] = $values['addons'];
			$cart_item           = $this->update_product_price( $cart_item, $cart_item['quantity'], $prices );
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

				$price = isset( $cart_item[ 'addons_price_before_calc' ] ) ? $cart_item[ 'addons_price_before_calc' ] : $addon[ 'price' ];
				$value = $addon[ 'value' ];

				/*
				 * Create a clone of the current cart item and set its price equal to the add-on price.
				 * This will allow extensions to discount the add-on price.
				 */
				$cloned_cart_item = WC_Product_Addons_Helper::create_product_with_filtered_addon_prices( $cart_item[ 'data' ], $addon[ 'price' ] );
				$addon[ 'price' ] = $cloned_cart_item->get_price();

				/*
				 * Deprecated 'woocommerce_addons_add_price_to_name' since v6.4.0.
				 * New filter: 'woocommerce_addons_add_cart_price_to_value'
				 *
				 * Use this filter to display the price next to each selected add-on option.
				 * By default, add-on prices show up only next to flat fee add-ons.
				 *
				 * @param boolean
				 */
				apply_filters_deprecated( 'woocommerce_addons_add_price_to_name', array( false, $cart_item ), '6.4.0', 'woocommerce_addons_add_cart_price_to_value' );

				$add_price_to_value = apply_filters( 'woocommerce_addons_add_cart_price_to_value', false, $cart_item );

				$GLOBALS[ 'product' ] = $cloned_cart_item;

				if ( 0 == $addon['price'] ) {
					$value .= '';
				} elseif ( 'percentage_based' === $addon['price_type'] && 0 == $price ) {
					$value .= '';
				} elseif ( 'flat_fee' === $addon['price_type'] && $addon['price'] ) {

					$addon_price = wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon[ 'price' ], $cart_item[ 'data' ], true ) );
					$value      .= sprintf( _x( ' (+ %1$s)', 'flat fee addon price in cart', 'woocommerce-product-addons' ), $addon_price );

				}  elseif ( 'custom_price' === $addon['field_type'] && $addon['price'] ) {

					$addon_price        = wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon[ 'price' ], $cart_item[ 'data' ], true ) );
					$value             .= sprintf( _x( ' (%1$s)', 'custom price addon price in cart', 'woocommerce-product-addons' ), $addon_price );
					$addon[ 'display' ] = $value;
				} elseif ( 'quantity_based' === $addon['price_type'] && $addon['price'] && $add_price_to_value ) {
					$addon_price = wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon[ 'price' ], $cart_item[ 'data' ], true ) );
					$value      .= sprintf( _x( ' (%1$s)', 'quantity based addon price in cart', 'woocommerce-product-addons' ), $addon_price );

				} elseif ( 'percentage_based' === $addon['price_type'] && $addon['price'] && $add_price_to_value ) {

					$_product = wc_get_product( $cart_item['product_id'] );
					$_product->set_price( $price * ( $addon['price'] / 100 ) );
					$addon_price = WC()->cart->get_product_price( $_product );
					$value      .= sprintf( _x( ' (%1$s)', 'percentage based addon price in cart', 'woocommerce-product-addons' ), $addon_price );
				}

				unset( $GLOBALS[ 'product' ] );

				$addon_data = array(
					'name'    => $addon['name'],
					'value'   => $value,
					'display' => isset( $addon['display'] ) ? $addon['display'] : '',
				);
				$other_data[] = apply_filters( 'woocommerce_product_addons_get_item_data', $addon_data, $addon, $cart_item );
			}
		}

		return $other_data;
	}

	/**
	 * Grabs the value of a product addon from order item meta.
	 *
	 * @param array  $ids Array of addon meta that include id, name and value.
	 * @param array  $addon
	 * @param string $type Addon type.
	 * @return array
	 */
	public function get_addon_meta_value( $ids, $addon, $type ) {
		$value = array();

		if ( 'checkbox' === $type || 'radiobutton' === $type ) {
			foreach ( $ids as $meta ) {
				if ( $this->is_matching_addon( $addon, $meta ) ) {
					$meta_value = is_object( $meta ) ? $meta->value : $meta[ 'value' ];
					if ( is_array( $meta_value ) && ! empty( $meta_value ) ) {
						$value[] = array_map( 'sanitize_title', $meta_value );
					} else {
						$value[] = sanitize_title( $meta_value );
					}
				}
			}
		} elseif( 'select' === $type ) {
			foreach ( $ids as $meta ) {
				if ( $this->is_matching_addon( $addon, $meta ) ) {
					$meta_value = is_object( $meta ) ? $meta->value : $meta[ 'value' ];
					$value      = sanitize_title( $meta_value );
					break;
				}
			}
		} else {
			foreach ( $ids as $meta ) {
				if ( $this->is_matching_addon( $addon, $meta ) ) {
					$meta_value = is_object( $meta ) ? $meta->value : $meta[ 'value' ];
					$value      = wc_clean( $meta_value );
					break;
				}
			}
		}

		return $value;
	}

	/**
	 * Checks if an order item addon meta matches a product level addon.
	 *
	 * @param array  $addon
	 * @param array|object  $meta
	 * @return boolean
	 */
	public function is_matching_addon( $addon, $meta ) {

		if (
			is_array( $meta )
			&& isset( $addon[ 'id' ] )
			&& isset( $meta[ 'id' ] )
			&& 0 !== $addon[ 'id' ]
			&& 0 !== $meta[ 'id' ] ) {
			$match = $addon[ 'id' ] === $meta[ 'id' ] && stripos( $meta[ 'key' ], $addon['name'] ) === 0;
		} else {
			// Backwards compatibility for addons without ID.
			$meta_key = is_object( $meta ) ? $meta->key : $meta[ 'key' ];
			$match    = stripos( $meta_key, $addon['name'] ) === 0;
		}

		return $match;
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
