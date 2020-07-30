<?php
/**
 * CoCart Compatibility
 * 
 * Adds compatibility with CoCart.
 *
 * @author   Sébastien Dumont
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.10.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_COCART_Compatibility Class.
 *
 * Adds compatibility with CoCart.
 */
class WC_MNM_COCART_Compatibility {

	public static function init() {
		// Filters the handler used to add item to the cart.
		add_filter( 'cocart_add_to_cart_handler', array( __CLASS__, 'add_to_cart_handler' ), 0, 2 );

		// Validates the product before being added to the cart.
		add_filter( 'cocart_add_to_cart_validation', array( __CLASS__, 'add_to_cart_validation' ), 0, 7 );
		add_filter( 'cocart_add_cart_item_data', array( __CLASS__, 'reconfigure_mnm_configuration' ), 0 );

		// Validates the item container and configuration when being updated.
		add_filter( 'cocart_update_cart_validation', array( __CLASS__, 'update_cart_validation' ), 0, 4 );

		// Change packed item quantity.
		add_filter( 'cocart_cart_contents', array( __CLASS__, 'cart_item_quantity' ), 10, 4 );

		// Filters item price and subtotal.
		add_filter( 'cocart_cart_contents', array( __CLASS__, 'cart_item_price' ), 10, 4 );
		add_filter( 'cocart_cart_contents', array( __CLASS__, 'cart_item_subtotal' ), 10, 4 );

		// Filters the cart schema.
		add_filter( 'cocart_cart_schema', array( __CLASS__, 'add_additional_cart_schema' ) );

		// Adds Mix and Match Product data to the products API.
		add_filter( 'cocart_prepare_product_object', array( __CLASS__, 'add_mnm_product_data' ), 10, 2 );
		add_filter( 'cocart_product_schema', array( __CLASS__, 'add_additional_fields_schema' ) );
	} // END __construct()

	/**
	 * Overrides the handler used for adding a Mix and Match product.
	 *
	 * @access public
	 * @static
	 * @param  string     $handler - The name of the original handler to use when adding product to the cart.
	 * @param  WC_Product $product
	 * @return string     $handler - The name of the new handler to use when adding product to the cart.
	 */
	public static function add_to_cart_handler( $handler, $product ) {
		switch( $handler ) {
			case 'mix-and-match':
				$handler = 'simple';
				break;
		}

		return $handler;
	} // END add_to_cart_handler()

	/**
	 * Validates the product before being added to the cart.
	 *
	 * @access public
	 * @param  bool   $passed_validation - The current status of validation.
	 * @param  int    $product_id        - Contains the ID of the product.
	 * @param  float  $quantity          - Contains the quantity of the item.
	 * @param  int    $variation_id      - Contains the ID of the variation.
	 * @param  array  $variation         - Attribute values.
	 * @param  array  $cart_item_data    - Extra cart item data we want to pass into the item.
	 * @param  string $product_type      - The product type.
	 * @return bool|WP_Error
	 */
	public static function add_to_cart_validation( $passed_validation, $product_id, $quantity, $variation_id = '', $variation = array(), $cart_item_data = array(), $product_type ) {
		$container = wc_get_product( $product_id );

		if ( 'mix-and-match' === $container->get_type() ) {
			$product = wc_get_product( $product_id );

			$min_container_size = $product->get_min_container_size();
			$max_container_size = $product->get_max_container_size();
			$available_products = $product->get_available_children();

			/**
			 * Trim Zeros setting.
			 *
			 * @param bool
			 */
			$trim_zeros = apply_filters( 'woocommerce_price_trim_zeros', false );

			$strings = WC_Mix_and_Match_Display::get_add_to_cart_parameters( $trim_zeros );

			$error_message = '';
			$error         = array();

			// Get the total quantity of all items.
			$total_qty = 0;

			// Reconfigure MNM Configuration.
			$cart_item_data = self::reconfigure_mnm_configuration( $cart_item_data );

			// Count item total.
			foreach ( $cart_item_data[ 'mnm_config' ] as $child_id => $child_item ) {
				$child_quantity = $child_item['quantity'];
				$total_qty +=$child_quantity;
			}

			// Validate that the container has at least 1 item.
			if ( $min_container_size === $max_container_size && $total_qty !== $min_container_size ) {
				$error_message = $min_container_size === 1 ? $strings['i18n_qty_error_single'] : $string['i18n_qty_error'];
				$error_message = str_replace( '%s', $min_container_size, $error_message );

				$error = array( 'error_code' => $min_container_size === 1 ? 'i18n_qty_error_single' : 'i18n_qty_error', 'message' => $error_message );
			}

			// Validate a range.
			else if ( $max_container_size > 0 && $min_container_size > 0 && ( $total_qty < $min_container_size || $total_qty > $max_container_size ) ) {
				$error_message = $strings['i18n_min_max_qty_error'];
				$error_message = str_replace( '%max', $max_container_size, $error_message );
				$error_message = str_replace( '%min', $min_container_size, $error_message );

				$error = array( 'error_code' => 'i18n_min_max_qty_error', 'message' => $error_message );
			}

			// Validate that a container has minimum number of items.
			else if( $min_container_size > 0 && $total_qty < $min_container_size ) {
				$error_message = $min_container_size > 1 ? $strings['i18n_min_qty_error'] : $strings['i18n_min_qty_error_singular'];
				$error_message = str_replace( '%min', $min_container_size, $error_message );

				$error = array( 'error_code' => $min_container_size > 1 ? 'i18n_min_qty_error' : 'i18n_min_qty_error_singular', 'message' => $error_message );
			}

			// Validate that a container has fewer than the maximum number of items.
			else if ( $max_container_size > 0 && $total_qty > $max_container_size ) {
				$error_message = $max_container_size > 1 ? $strings['i18n_max_qty_error'] : $strings['i18n_max_qty_error_singular'];
				$error_message = str_replace( '%max', $max_container_size, $error_message );

				$error = array( 'error_code' => $max_container_size > 1 ? 'i18n_max_qty_error' : 'i18n_max_qty_error_singular', 'message' => $error_message );
			}

			// If quantity validation failed.
			if ( ! empty( $error ) ) {
				$selected_qty_message = $total_qty === 1 ? $strings['i18n_qty_message_single'] : $strings['i18n_qty_message'];
				$error['message'] = str_replace( '%v', $selected_qty_message, $error['message'] );
				$error['message'] = str_replace( '%s', $total_qty, $error['message'] );

				CoCart_Logger::log( $error['message'], 'error', 'woocommerce-mix-and-match' );

				return new WP_Error( $error['error_code'], $error['message'], array( 'status' => 403 ) );
			}

			foreach( $cart_item_data[ 'mnm_config' ] as $child_id => $child_item ) {

				// Validate that the product is available for this container.
				if ( ! $product->is_child_available( $child_id ) ) {
					$error_message = sprintf( __( 'A product in your configuration is not available for %s.', 'woocommerce-mix-and-match-products' ), $product->get_name( 'view' ) );

					CoCart_Logger::log( $error_message, 'error', 'woocommerce-mix-and-match' );

					return new WP_Error( 'wc_mnm_cocart_child_product_not_available_in_container.', $error_message, array( 'status' => 403 ) );
				}

			}

			// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
			$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// Find the cart item key in the existing cart.
			$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

			// Validate container.
			if ( self::validate_container_configuration( $product, $quantity, $cart_item_data[ 'mnm_config' ] ) ) {
				return true;
			} else {
				return false;
			}
		}

		return $passed_validation;
	} // END add_to_cart_validation()

	/**
	 * Reconfigures the MNM configuration so cart item data is still valid for cart.
	 *
	 * @access public
	 * @static
	 * @param  array $cart_item_data
	 * @return array $cart_item_data
	 */
	public static function reconfigure_mnm_configuration( $cart_item_data ) {
		// If the cart item data does not have a MNM configuration then just return.
		if ( ! isset( $cart_item_data[ 'mnm_config' ] ) ) {
			return $cart_item_data;
		}

		// Create temporary item data to re-organize data.
		if ( ! isset( $cart_item_data[ 'new_mnm_config' ] ) ) {
			$cart_item_data[ 'new_mnm_config' ] = array();
		}

		foreach ( $cart_item_data[ 'mnm_config' ] as $mnm_item_data ) {
			$child_id = $mnm_item_data['product_id'];
			$cart_item_data[ 'new_mnm_config' ][ $child_id ] = $mnm_item_data;
		}

		// Remove old MNM config.
		unset( $cart_item_data[ 'mnm_config' ] );

		// Set valid MNM config.
		$cart_item_data[ 'mnm_config' ] = $cart_item_data[ 'new_mnm_config' ];

		// Remove temporary item data.
		unset( $cart_item_data[ 'new_mnm_config' ] );

		return $cart_item_data;
	} // END reconfigure_mnm_configuration()

	/**
	 * Validates add to cart for MNM containers.
	 * Basically ensures that stock for all child products exists before attempting to add them to cart.
	 *
	 * @param  mixed   $container int|WC_Product_Mix_and_Match
	 * @param  int     $container_quantity
	 * @param  array   $configuration
	 * @return boolean
	 */
	public static function validate_container_configuration( $container, $container_quantity, $configuration ) {
		$is_configuration_valid = true;

		// Count the total child items.
		$total_items_in_container = 0;

		$container_id    = $container->get_id();
		$container_title = $container->get_title();

		// If a stock-managed product / variation exists in the container multiple times, its stock will be checked only once for the sum of all child quantities.
		// The stock manager class keeps a record of stock-managed product / variation ids.
		$mnm_stock = new WC_Mix_and_Match_Stock_Manager( $container );

		// Grab child items.
		$mnm_items = $container->get_children();

		$error_message = '';
		$error         = array();

		if ( sizeof( $mnm_items ) ) {

			// Loop through the items.
			foreach ( $mnm_items as $id => $mnm_item ) {

				// Check that a product has been selected.
				if ( isset( $configuration[ $id ] ) && $configuration[ $id ] !== '' ) {
					$item_quantity = $configuration[ $id ][ 'quantity' ];
				} else {
					continue;
				}

				// Total quantity in single container.
				$total_items_in_container += $item_quantity;

				// Total quantity of items in all containers: for stock purposes.
				$quantity = $item_quantity * $container_quantity;

				// Product is purchasable - only for per item pricing.
				if ( $container->is_priced_per_product() && ! $mnm_item->is_purchasable() ) {
					$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart since &quot;%s&quot; cannot be purchased.', 'woocommerce-mix-and-match-products' ), $mnm_item->get_title() );

					$error = array( 'error_code' => 'wc_mnm_cocart_configuration_cannot_purchase', 'message' => $error_message );
				}

				// Check individual min/max quantities
				$min_quantity  = $container->get_child_quantity( 'min', $id );
				$max_quantity  = $container->get_child_quantity( 'max', $id );
				$step_quantity = $container->get_child_quantity( 'step', $id );

				if ( $max_quantity && $item_quantity > $max_quantity ) {
					// translators: %s is the product title. %d is the maximum quantity.
					$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart since you cannot select more than %d of &quot;%s&quot;.', 'woocommerce-mix-and-match-products' ), $max_quantity, $mnm_item->get_title() );

					$error = array( 'error_code' => 'wc_mnm_cocart_configuration_cannot_select_more', 'message' => $error_message );
				} elseif( $min_quantity && $item_quantity < $min_quantity ) {
					// translators: %s is the product title. %d is the minimum quantity.
					$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart since you must select at least %d of &quot;%s&quot;.', 'woocommerce-mix-and-match-products' ), $min_quantity, $mnm_item->get_title() );

					$error = array( 'error_code' => 'wc_mnm_cocart_configuration_cannot_select_less', 'message' => $error_message );
				} elseif ( $step_quantity > 1 && $item_quantity % $step_quantity ) {
					// translators: %s is the product title. %d is the step quantity.
					$error_message = sprintf( __( 'The configuration you have selected cannot be added to the cart since you must select &quot;%s&quot; in quantities of %d.', 'woocommerce-mix-and-match-products' ), $mnm_item->get_title(), $step_quantity );

					$error = array( 'error_code' => 'wc_mnm_cocart_configuration_must_select_quantities', 'message' => $error_message );
				}

				if ( ! empty( $error ) ) {
					CoCart_Logger::log( $error['message'], 'error', 'woocommerce-mix-and-match' );

					return new WP_Error( $error['error_code'], $error['message'], array( 'status' => 403 ) );
				}

				// Stock management.
				if ( $mnm_item->is_type( 'variation' ) ) {
					$mnm_stock->add_item( $mnm_item->get_parent_id(), $id, $quantity );
				} else {
					$mnm_stock->add_item( $id, false, $quantity );
				}

				/**
				 * Individual item validation.
				 *
				 * @param bool $is_valid
				 * @param obj  $container          WC_Product_Mix_and_Match of parent container.
				 * @param obj  $mnm_item           WC_Product of child item.
				 * @param int  $item_quantity      Quantity of child item.
				 * @param int  $container_quantity Quantity of parent container.
				 */
				if ( ! apply_filters( 'woocommerce_mnm_cocart_item_add_to_cart_validation', true, $container, $mnm_item, $item_quantity, $container_quantity ) ) {
					$is_configuration_valid = false;
					break;
				}

			} // END foreach.

		}

		if ( $is_configuration_valid ) {
			// The number of items allowed to be in the container.
			$min_container_size = $container->get_min_container_size();
			$max_container_size = $container->get_max_container_size();

			// Validate the max number of items in the container.
			if ( $max_container_size > 0 && $total_items_in_container > $max_container_size ) {
				$error_message = sprintf( _n( 'You have selected too many items. Please choose %d item for &quot;%s&quot;.', 'You have selected too many items. Please choose %d items for &quot;%s&quot;.', $max_container_size, 'woocommerce-mix-and-match-products' ), $max_container_size, $container->get_title() );

				$error = array( 'error_code' => 'wc_mnm_cocart_container_too_many_items', 'message' => $error_message );
			}

			// Validate the min number of items in the container.
			if ( $min_container_size > 0 && $total_items_in_container < $min_container_size ) {
				$error_message = sprintf( _n( 'You have selected too few items. Please choose %d item for &quot;%s&quot;.', 'You have selected too few items. Please choose %d items for &quot;%s&quot;.', $min_container_size, 'woocommerce-mix-and-match-products' ), $min_container_size, $container->get_title() );

				$error = array( 'error_code' => 'wc_mnm_cocart_container_too_few_items', 'message' => $error_message );
			}

			if ( ! empty( $error ) ) {
				CoCart_Logger::log( $error['message'], 'error', 'woocommerce-mix-and-match' );

				return new WP_Error( $error['error_code'], $error['message'], array( 'status' => 403 ) );
			}

		}

		return $is_configuration_valid;
	} // END validate_container_configuration()

	/**
	 * Validates the item when being updated.
	 *
	 * @access public
	 * @static
	 * @param  bool   $passed_validation - The current status of validation.
	 * @param  string $cart_item_key     - The item in the cart we are updating.
	 * @param  array  $values            - Product data of item in cart.
	 * @param  float  $product_quantity  - The quantity of the item we want.
	 * @return bool
	 */
	public static function update_cart_validation( $passed_validation, $cart_item_key, $values, $product_quantity ) {
		$product = $values[ 'data' ];

		if ( ! $product ) {
			return false;
		}

		$existing_quantity   = $values[ 'quantity' ];
		$additional_quantity = $product_quantity - $existing_quantity;

		// Don't check child items individually, will be checked by parent container.
		if ( wc_mnm_maybe_is_child_cart_item( $values ) ) {
			return $passed_validation;
		}

		if ( $product->is_type( 'mix-and-match' ) && wc_mnm_is_container_cart_item( $values ) ) {

			// Grab child items.
			$mnm_items = $product->get_children();

			if ( empty( $mnm_items ) ) {
				return false;
			}

			// If a stock-managed product / variation exists in the container multiple times, its stock will be checked only once for the sum of all child quantities.
			// The stock manager class keeps a record of stock-managed product / variation ids.
			$mnm_stock = new WC_Mix_and_Match_Stock_Manager( $product );

			// Loop through the items.
			foreach ( $values[ 'mnm_config' ] as $id => $data ) {

				// Double check it is an allowed item - is this needed? Wasn't it checked on its way into the cart?
				if ( ! array_key_exists( $data['product_id'], $mnm_items ) ) {
					return false;
				}

				// Quantity per container.
				$item_quantity = $data[ 'quantity' ];

				// Total quantity.
				$quantity = $item_quantity * $additional_quantity;

				// Get the child product/variation.
				$mnm_item = wc_get_product( $data['product_id'] );

				// Must be some kinda fake product.
				if ( ! $mnm_item ) {
					return false;
				}

				// Stock management.
				if ( $mnm_item->is_type( 'variation' ) ) {
					$mnm_stock->add_item( $mnm_item->get_parent_id(), $data['product_id'], $quantity );
				} else {
					$mnm_stock->add_item( $data['product_id'], false, $quantity );
				}

			} // End foreach.

			// Check stock for stock-managed child items.
			// If out of stock, don't proceed.
			if ( ! $mnm_stock->validate_stock( true ) ) {
				return false;
			}
		}

		return $passed_validation;
	} // END update_cart_validation()

	/**
	 * Returns the item quantity based on MNM settings.
	 *
	 * @access public
	 * @static
	 * @param  array  $cart_contents
	 * @param  int    $item_key
	 * @param  array  $cart_item
	 * @param  object $_product
	 * @return array  $cart_contents
	 */
	public static function cart_item_quantity( $cart_contents, $item_key, $cart_item, $_product ) {
		if ( wc_mnm_get_cart_item_container( $cart_item ) ) {
			$cart_contents[ $item_key ][ 'quantity' ] = $cart_item[ 'quantity' ];
		}

		return $cart_contents;
	} // END cart_item_quantity()

	/**
	 * Returns the item price based on MNM settings.
	 *
	 * @access public
	 * @static
	 * @param  array  $cart_contents
	 * @param  int    $item_key
	 * @param  array  $cart_item
	 * @param  object $_product
	 * @return array  $cart_contents
	 */
	public static function cart_item_price( $cart_contents, $item_key, $cart_item, $_product ) {
		$price = $cart_contents[ $item_key ]['line_total'] / $cart_item[ 'quantity' ];

		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			if ( ! $container_cart_item[ 'data' ]->is_priced_per_product() ) {
				$price = 0;
			}

		// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( $cart_item[ 'data' ]->is_priced_per_product() ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item[ 'data' ] ) : wc_get_price_including_tax( $cart_item[ 'data' ] );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $mnm_item_key => $mnm_item ) {
					$child_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $mnm_item[ 'data' ], array( 'qty' => $mnm_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $mnm_item[ 'data' ], array( 'qty' => $mnm_item[ 'quantity' ] ) );
					$mnm_items_price  += (double) $child_item_price;
				}

				$aggregate_price = $mnm_container_price + $mnm_items_price / $cart_item[ 'quantity' ];
				$price = $aggregate_price;
			}
		}

		$cart_contents[ $item_key ]['line_total'] = $price;

		return $cart_contents;
	} // END cart_item_price()

	/**
	 * Returns the item subtotal based on MNM configuration.
	 *
	 * @access public
	 * @static
	 * @param  array  $cart_contents
	 * @param  int    $item_key
	 * @param  array  $cart_item
	 * @param  object $_product
	 * @return array  $cart_contents
	 */
	public static function cart_item_subtotal( $cart_contents, $item_key, $cart_item, $_product ) {
		$subtotal = $cart_contents[ $item_key ]['line_subtotal'];

		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			// If not priced per product return zero.
			if ( ! $container_cart_item[ 'data' ]->is_priced_per_product() ) {
				$subtotal = 0;
			}

		// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( $cart_item[ 'data' ]->is_priced_per_product() ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item[ 'data' ], array( 'qty' => $cart_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $cart_item[ 'data' ], array( 'qty' => $cart_item[ 'quantity' ] ) );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $mnm_item_key => $mnm_item ) {
					$child_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $mnm_item[ 'data' ], array( 'qty' => $mnm_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $mnm_item[ 'data' ], array( 'qty' => $mnm_item[ 'quantity' ] ) );
					$mnm_items_price  += (double) $child_item_price;
				}

				$aggregate_subtotal = (double) $mnm_container_price + $mnm_items_price;

				$subtotal = $aggregate_subtotal;
			}
		}

		$cart_contents[ $item_key ]['line_subtotal'] = $subtotal;

		return $cart_contents;
	} // END cart_item_subtotal()

	/**
	 * Adds Mix and Match Product schema to the cart.
	 *
	 * @access public
	 * @static
	 * @param  array $schema - Before schema is altered.
	 * @return array $schema - After schema is altered.
	 */
	public static function add_additional_cart_schema( $schema ) {
		$schema['items']['mnm_config'] = array(
			'description' => __( 'Mix and Match Product Configuration.', 'woocommerce-mix-and-match-products' ),
			'type'        => 'object',
			'context'     => array( 'view' ),
			'properties'  => array(
				'product_id'      => array(
					'description' => __( 'Unique identifier for the product in the configuration.', 'cart-rest-api-for-woocommerce', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'variation_id' => array(
					'description' => __( 'Unique identifier for the variation in the configuration.', 'cart-rest-api-for-woocommerce', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'variation'       => array(
					'description' => __( 'Chosen attributes (for variations).', 'cart-rest-api-for-woocommerce', 'woocommerce-mix-and-match-products' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'attribute' => array(
								'description' => __( 'Variation attribute slug.', 'cart-rest-api-for-woocommerce', 'woocommerce-mix-and-match-products' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'value'     => array(
								'description' => __( 'Variation attribute value.', 'cart-rest-api-for-woocommerce', 'woocommerce-mix-and-match-products' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
				'quantity'        => array(
					'description' => __( 'Quantity of this item in the configuration.', 'cart-rest-api-for-woocommerce', 'woocommerce-mix-and-match-products' ),
					'type'        => 'float',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
			'readonly' => true,
		);

		return $schema;
	} // END add_additional_cart_schema()

	/**
	 * Add Mix and Match Product data to products that 
	 * are a mix and match product only.
	 * 
	 * @access public
	 * @static
	 * @param  object     $response
	 * @param  WC_Product $object
	 */
	public static function add_mnm_product_data( $response, $object ) {
		if ( $object->is_type( 'mix-and-match' ) ) {

			$contents = $object->get_children();

			$products = array();

			foreach ( $contents as $child_id => $child_item ) {
				$products[] = array(
					'child_id'          => $child_id,
					'product_id'        => $child_item->get_parent_id() > 0 ? $child_item->get_parent_id() : $child_item->get_id(),
					'variation_id'      => $child_item->get_parent_id() > 0 ? $child_item->get_id() : 0,
					'name'              => $child_item->get_name( 'view' ),
					'raw_price'         => $child_item->get_price( 'view' ),
					'raw_regular_price' => $child_item->get_regular_price( 'view' ),
					'raw_sale_price'    => $child_item->get_sale_price( 'view' ),
					'price'             => html_entity_decode( strip_tags( wc_price( $child_item->get_price( 'view' ) ) ) ),
					'regular_price'     => html_entity_decode( strip_tags( wc_price( $child_item->get_regular_price( 'view' ) ) ) ),
					'sale_price'        => html_entity_decode( strip_tags( wc_price( $child_item->get_sale_price( 'view' ) ) ) ),
					'on_sale'           => $child_item->is_on_sale( 'view' ),
				);
			}

			$response->data['mnm_data'] = self::mnm_data( $object, $products );
		}

		return $response;
	} // add_mnm_product_data()

	/**
	 * Returns Mix and Match Product data.
	 *
	 * @access public
	 * @static
	 * @param  WC_Product $object   - Returns product details of the parent product.
	 * @param  array      $products - List of available products applied to the container.
	 * @return array
	 */
	public static function mnm_data( $object, $products ) {
		return array(
			'base_raw_price'            => $object->get_price(),
			'base_raw_regular_price'    => $object->get_regular_price(),
			'base_raw_sale_price'       => $object->get_sale_price(),
			'base_price'                => html_entity_decode( strip_tags( wc_price( $object->get_price() ) ) ),
			'base_regular_price'        => html_entity_decode( strip_tags( wc_price( $object->get_regular_price() ) ) ),
			'base_sale_price'           => html_entity_decode( strip_tags( wc_price( $object->get_sale_price() ) ) ),
			'has_discount'              => $object->has_discount(),
			'min_raw_price'             => $object->get_min_raw_price(),
			'min_raw_regular_price'     => $object->get_min_raw_regular_price(),
			'max_raw_price'             => $object->get_max_raw_price(),
			'max_raw_regular_price'     => $object->get_max_raw_regular_price(),
			'min_price'                 => html_entity_decode( strip_tags( wc_price( $object->get_min_raw_price() ) ) ),
			'min_regular_price'         => html_entity_decode( strip_tags( wc_price( $object->get_min_raw_regular_price() ) ) ),
			'max_price'                 => html_entity_decode( strip_tags( wc_price( $object->get_max_raw_price() ) ) ),
			'max_regular_price'         => html_entity_decode( strip_tags( wc_price( $object->get_max_raw_regular_price() ) ) ),
			'min_container_size'        => $object->get_min_container_size(),
			'max_container_size'        => $object->get_max_container_size(),
			'products'                  => $products,
			'per_product_pricing'       => $object->get_priced_per_product(),
			'per_product_discount'      => empty( $object->get_discount() ) ? 0 : $object->get_discount(),
			'per_product_shipping'      => $object->get_shipped_per_product(),
		);
	} // END mnm_data()

	/**
	 * Adds Mix and Match Product schema to Products.
	 *
	 * @access public
	 * @static
	 * @param  array $schema - Before schema is altered.
	 * @return array $schema - After schema is altered.
	 */
	public static function add_additional_fields_schema( $schema ) {
		$schema['mnm_data'] = array(
			'description' => __( 'Mix and Match Product Data.', 'woocommerce-mix-and-match-products' ),
			'type'        => 'object',
			'context'     => array( 'view' ),
			'properties'  => array(
				'base_raw_price' => array(
					'description' => __( 'Base raw price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'base_raw_regular_price' => array(
					'description' => __( 'Base raw regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'base_raw_sale_price' => array(
					'description' => __( 'Base raw sale price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'base_price' => array(
					'description' => __( 'Base price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'base_regular_price' => array(
					'description' => __( 'Base regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'base_sale_price' => array(
					'description' => __( 'Base sale price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'has_discount' => array(
					'description' => __( 'Product has discount?', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' )
				),
				'min_raw_price' => array(
					'description' => __( 'Minimum raw price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'min_raw_regular_price' => array(
					'description' => __( 'Minimum raw regular_price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'max_raw_price' => array(
					'description' => __( 'Maximum raw price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'max_raw_regular_price' => array(
					'description' => __( 'Maximum raw regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'min_price' => array(
					'description' => __( 'Minimum price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'min_regular_price' => array(
					'description' => __( 'Minimum regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'max_price' => array(
					'description' => __( 'Maximum price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'max_regular_price' => array(
					'description' => __( 'Maximum regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'min_container_size' => array(
					'description' => __( 'Minimum container size.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'max_container_size' => array(
					'description' => __( 'Maximum container size.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'products' => array(
					'description' => __( 'Products available for this container.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' )
				),
				'per_product_pricing' => array(
					'description' => __( 'Pricing per product.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' )
				),
				'per_product_discount' => array(
					'description' => __( 'Discount per product.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'per_product_shipping' => array(
					'description' => __( 'Shipping per product.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' )
				),
			)
		);

		return $schema;
	} // END add_additional_fields_schema()

} // END class

WC_MNM_COCART_Compatibility::init();