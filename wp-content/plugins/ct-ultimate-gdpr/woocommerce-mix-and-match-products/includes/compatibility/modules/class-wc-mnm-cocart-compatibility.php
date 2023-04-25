<?php
/**
 * CoCart Compatibility
 *
 * Adds compatibility with CoCart.
 *
 * @package WooCommerce Mix and Match Products/Compatibility
 * @since   1.10.0
 * @version 2.4.0
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
		add_filter( 'cocart_cart_contents', array( __CLASS__, 'cart_item_quantity' ), 10, 3 ); // API v1
		add_filter( 'cocart_cart_item_quantity', array( __CLASS__, 'cart_item_quantity_v2' ), 10, 3 ); // API v2

		// Filters item price and subtotal.
		add_filter( 'cocart_cart_contents', array( __CLASS__, 'cart_item_price' ), 10, 4 ); // API v1
		add_filter( 'cocart_cart_contents', array( __CLASS__, 'cart_item_subtotal' ), 10, 3 ); // API v1
		add_filter( 'cocart_cart_item_price', array( __CLASS__, 'cart_item_price_v2' ), 10, 3 ); // API v2
		add_filter( 'cocart_cart_item_subtotal', array( __CLASS__, 'cart_item_subtotal_v2' ), 10, 3 ); // API v2

		// Filters the cart schema.
		add_filter( 'cocart_cart_schema', array( __CLASS__, 'add_additional_cart_schema' ) ); // API v1
		add_filter( 'cocart_cart_items_schema', array( __CLASS__, 'add_additional_cart_schema_v2' ) ); // API v2

		// Adds Mix and Match Product data to the products API.
		add_filter( 'cocart_products_get_price_range', array( __CLASS__, 'price_range' ), 10, 2 ); // API v2
		add_filter( 'cocart_products_add_to_cart_rest_url', array( __CLASS__, 'add_to_cart_rest_url' ), 10, 2 ); // API v2
		add_filter( 'cocart_prepare_product_object', array( __CLASS__, 'add_mnm_product_data' ), 10, 2 ); // API v1
		add_filter( 'cocart_prepare_product_object_v2', array( __CLASS__, 'add_mnm_product_data_v2' ), 10, 2 ); // API v2
		add_filter( 'cocart_product_schema', array( __CLASS__, 'add_additional_fields_schema' ) );
	} // END __construct()

	/**
	 * Overrides the handler used for adding a Mix and Match product.
	 *
	 * @param  string     $handler The name of the original handler to use when adding product to the cart.
	 * @param  WC_Product $product Product object.
	 * @return string     $handler The name of the new handler to use when adding product to the cart.
	 */
	public static function add_to_cart_handler( $handler, $product ) {
		switch ( $handler ) {
			case 'mix-and-match':
				$handler = 'simple';
				break;
		}

		return $handler;
	} // END add_to_cart_handler()

	/**
	 * Validates the product before being added to the cart.
	 *
	 * @throws CoCart_Data_Exception Exception if invalid data is detected.
	 *
	 * @since  1.10.0 Introduced.
	 * @since  2.0.0  No longer requesting `wc_get_product()` twice. Using `$product_type` to identify product type.
	 * @param  bool   $passed_validation - The current status of validation.
	 * @param  int    $product_id        - Contains the ID of the product.
	 * @param  float  $quantity          - Contains the quantity of the item.
	 * @param  int    $variation_id      - Contains the ID of the variation.
	 * @param  array  $variation         - Attribute values.
	 * @param  array  $cart_item_data    - Extra cart item data we want to pass into the item.
	 * @param  string $product_type      - The product type.
	 * @return bool
	 */
	public static function add_to_cart_validation( $passed_validation, $product_id, $quantity, $variation_id = '', $variation = array(), $cart_item_data = array(), $product_type = '' ) {

		try {

			if ( 'mix-and-match' === $product_type && isset( $cart_item_data['mnm_config'] ) ) {

				$product = wc_get_product( $product_id );

				try {
					$passed_validation = wc_mix_and_match()->cart->validate_container_configuration( $product, $quantity, $cart_item_data['mnm_config'], array( 'context' => 'cart', 'throw_exception' => true ) );
				} catch ( Exception $e ) {
					throw new CoCart_Data_Exception( $e->getCode(), $e->getMessage(), 404, array( 'plugin' => 'woocommerce-mix-and-match' ) );
				}
				
			}

			return $passed_validation;

		} catch ( CoCart_Data_Exception $e ) {
			return CoCart_Response::get_error_response( $e->getErrorCode(), $e->getMessage(), $e->getCode(), $e->getAdditionalData() );
		}
	} // END add_to_cart_validation()

	/**
	 * Reconfigures the MNM configuration so cart item data is still valid for cart.
	 *
	 * @param  array $cart_item_data
	 * @return array $cart_item_data
	 */
	public static function reconfigure_mnm_configuration( $cart_item_data ) {
		// If the cart item data does not have a MNM configuration then just return.
		if ( ! isset( $cart_item_data['mnm_config'] ) ) {
			return $cart_item_data;
		}

		// Create temporary item data to re-organize data.
		if ( ! isset( $cart_item_data['new_mnm_config'] ) ) {
			$cart_item_data['new_mnm_config'] = array();
		}

		foreach ( $cart_item_data['mnm_config'] as $child_item_data ) {
			$child_id = isset( $child_item_data['variation_id'] ) && $child_item_data['variation_id'] > 0 ? $child_item_data['variation_id'] : $child_item_data['product_id'];
			$cart_item_data['new_mnm_config'][ $child_id ] = $child_item_data;
		}

		// Remove old MNM config.
		unset( $cart_item_data['mnm_config'] );

		// Set valid MNM config.
		$cart_item_data['mnm_config'] = $cart_item_data['new_mnm_config'];

		// Remove temporary item data.
		unset( $cart_item_data['new_mnm_config'] );

		return $cart_item_data;
	} // END reconfigure_mnm_configuration()


	/**
	 * Validates add to cart for MNM containers.
	 *
	 * Basically ensures that stock for all child products exists before attempting to add them to cart.
	 * 
	 * @deprecated 2.2.0
	 *
	 * @throws CoCart_Data_Exception Exception if invalid data is detected.
	 *
	 * @since  1.10.0  Introduced.
	 * @since  2.0.0   Now using `CoCart_Data_Exception` for error handling.
	 * @param  mixed   $container int|WC_Product_Mix_and_Match
	 * @param  int     $container_quantity
	 * @param  array   $configuration
	 * @return boolean
	 */
	public static function validate_container_configuration( $container, $container_quantity, $configuration ) {
		wc_deprecated_function( 'WC_MNM_COCART_Compatibility::validate_container_configuration()', '2.2.0', 'Function is no longer used.' );

		try {
			return wc_mix_and_match()->cart->validate_container_configuration( $container, $container_quantity, $configuration, array( 'context' => 'cart', 'throw_exception' => true ) );
		} catch ( Exception $e ) {
			throw new CoCart_Data_Exception( $e->getCode(), $e->getMessage(), 404, array( 'plugin' => 'woocommerce-mix-and-match' ) );
		}

	} // END validate_container_configuration()


	/**
	 * Validates the item when being updated.
	 *
	 * @throws CoCart_Data_Exception Exception if invalid data is detected.
	 *
	 * @param  bool   $passed_validation - The current status of validation.
	 * @param  string $cart_item_key     - The item in the cart we are updating.
	 * @param  array  $values            - Product data of item in cart.
	 * @param  float  $product_quantity  - The quantity of the item we want.
	 * @return bool
	 */
	public static function update_cart_validation( $passed_validation, $cart_item_key, $values, $product_quantity ) {
		try {
			$product = $values['data'];

			if ( ! $product ) {
				throw new CoCart_Data_Exception( 'wc_mnm_cocart_update_cart_validation_missing', __( 'Missing product data to validate. Please try again!', 'woocommerce-mix-and-match-products' ), 404, array( 'plugin' => 'woocommerce-mix-and-match' ) );
			}

			if ( $product->is_type( 'mix-and-match' ) && wc_mnm_is_container_cart_item( $values ) ) {
				
				try {
					$additional_quantity = $product_quantity - $values['quantity'];
					$passed_validation = wc_mix_and_match()->cart->validate_container_configuration( $product, $additional_quantity, $values['mnm_config'], array( 'context' => 'cart', 'throw_exception' => true ) );
				} catch ( Exception $e ) {
					throw new CoCart_Data_Exception( $e->getCode(), $e->getMessage(), 404, array( 'plugin' => 'woocommerce-mix-and-match' ) );
				}

			}
			
			return $passed_validation;

		} catch ( CoCart_Data_Exception $e ) {
			return CoCart_Response::get_error_response( $e->getErrorCode(), $e->getMessage(), $e->getCode(), $e->getAdditionalData() );
		}
	} // END update_cart_validation()

	/**
	 * Returns the item quantity based on MNM settings.
	 *
	 * @param  array  $cart_contents
	 * @param  int    $item_key
	 * @param  array  $cart_item
	 * @return array  $cart_contents
	 */
	public static function cart_item_quantity( $cart_contents, $item_key, $cart_item ) {
		if ( wc_mnm_get_cart_item_container( $cart_item ) ) {
			$cart_contents[ $item_key ]['quantity'] = $cart_item['quantity'];
		}

		return $cart_contents;
	} // END cart_item_quantity()

	/**
	 * Returns the item quantity based on MNM settings.
	 *
	 * @since  2.0.0 Introduced
	 * @param  int   $quantity
	 * @param  int   $item_key
	 * @param  array $cart_item
	 * @return array $quantity
	 */
	public static function cart_item_quantity_v2( $quantity, $item_key, $cart_item ) {
		if ( wc_mnm_get_cart_item_container( $cart_item ) ) {
			$quantity = $cart_item['quantity'];
		}

		return $quantity;
	} // END cart_item_quantity()

	/**
	 * Gets the cart item child price.
	 *
	 * @since  2.0.0 Introduced
	 * @param  int|string $price
	 * @param  array      $cart_item
	 * @param  int        $item_key
	 * @return int|string $price
	 */
	public static function get_cart_item_price( $price, $cart_item, $item_key ) {
		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			if ( ! $container_cart_item['data']->is_priced_per_product() ) {
				$price = 0;
			}

		// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			if ( $cart_item['data']->is_priced_per_product() ) {
				$child_items_price   = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item['data'] ) : wc_get_price_including_tax( $cart_item['data'] );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $child_item_key => $child_item ) {
					$child_item_price  = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $child_item['data'], array( 'qty' => $child_item['quantity'] ) ) : wc_get_price_including_tax( $child_item['data'], array( 'qty' => $child_item['quantity'] ) );
					$child_items_price += (double) $child_item_price;
				}

				$aggregate_price = $mnm_container_price + $child_items_price / $cart_item['quantity'];
				$price = $aggregate_price;
			}
		}

		return $price;
	} // END get_cart_item_price()

	/**
	 * Returns the item price based on MNM settings.
	 *
	 * @param  array $cart_contents
	 * @param  int   $item_key
	 * @param  array $cart_item
	 * @return array $cart_contents
	 */
	public static function cart_item_price( $cart_contents, $item_key, $cart_item ) {
		$price = $cart_contents[ $item_key ]['line_total'] / $cart_item['quantity'];

		$cart_contents[ $item_key ]['line_total'] = self::get_cart_item_price( $price, $cart_item, $item_key );

		return $cart_contents;
	} // END cart_item_price()

	/**
	 * Returns the item price based on MNM settings.
	 *
	 * @since  2.0.0 Introduced
	 * @param  string $price
	 * @param  array  $cart_item
	 * @param  int    $item_key
	 * @return string $price
	 */
	public static function cart_item_price_v2( $price, $cart_item, $item_key ) {
		if ( wc_mnm_get_cart_item_container( $cart_item ) ) {
			$price = self::get_cart_item_price( $price, $cart_item, $item_key );
		}

		return $price;
	} // END cart_item_price_v2()

	/**
	 * Gets the cart item child subtotal.
	 *
	 * @since  2.0.0 Introduced
	 * @param  int|string $subtotal
	 * @param  array      $cart_item
	 * @param  int        $item_key
	 * @return int|string $subtotal
	 */
	public static function get_cart_item_subtotal( $subtotal, $cart_item, $item_key ) {
		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			// If not priced per product return zero.
			if ( ! $container_cart_item['data']->is_priced_per_product() ) {
				$subtotal = 0;
			}

		// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			if ( $cart_item['data']->is_priced_per_product() ) {
				$child_items_price   = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'] ) ) : wc_get_price_including_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'] ) );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $child_item_key => $child_item ) {
					$child_item_price  = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $child_item['data'], array( 'qty' => $child_item['quantity'] ) ) : wc_get_price_including_tax( $child_item['data'], array( 'qty' => $child_item['quantity'] ) );
					$child_items_price += (double) $child_item_price;
				}

				$aggregate_subtotal = (double) $mnm_container_price + $child_items_price;

				$subtotal = $aggregate_subtotal;
			}
		}

		return $subtotal;
	} // END get_cart_item_subtotal()

	/**
	 * Returns the item subtotal based on MNM configuration.
	 *
	 * @param  array $cart_contents
	 * @param  int   $item_key
	 * @param  array $cart_item
	 * @return array $cart_contents
	 */
	public static function cart_item_subtotal( $cart_contents, $item_key, $cart_item ) {
		$subtotal = $cart_contents[ $item_key ]['line_subtotal'];

		$cart_contents[ $item_key ]['line_subtotal'] = self::get_cart_item_subtotal( $subtotal, $cart_item, $item_key );

		return $cart_contents;
	} // END cart_item_subtotal()

	/**
	 * Returns the item subtotal based on MNM configuration.
	 *
	 * @since  2.0.0 Introduced
	 * @param  array  $subtotal
	 * @param  int    $item_key
	 * @param  array  $cart_item
	 * @return string $subtotal
	 */
	public static function cart_item_subtotal_v2( $subtotal, $item_key, $cart_item ) {
		if ( wc_mnm_get_cart_item_container( $cart_item ) ) {
			$subtotal = cocart_prepare_money_response( self::get_cart_item_subtotal( $subtotal, $cart_item, $item_key ) );
		}

		return $subtotal;
	} // END cart_item_subtotal_v2()

	/**
	 * Gets the Mix and Match Product schema for the cart.
	 *
	 * @since  2.0.0 Introduced
	 * @return array
	 */
	public static function get_additional_cart_schema() {
		return array(
			'description' => __( 'Mix and Match Product Configuration.', 'woocommerce-mix-and-match-products' ),
			'type'        => 'object',
			'context'     => array( 'view' ),
			'properties'  => array(
				'product_id'      => array(
					'description' => __( 'Unique identifier for the product in the configuration.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'variation_id' => array(
					'description' => __( 'Unique identifier for the variation in the configuration.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'quantity'        => array(
					'description' => __( 'Quantity of this item in the configuration.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'float',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'variation'       => array(
					'description' => __( 'Chosen attributes (for variations).', 'woocommerce-mix-and-match-products' ),
					'type'        => 'array',
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'attribute' => array(
								'description' => __( 'Variation attribute slug.', 'woocommerce-mix-and-match-products' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'value'     => array(
								'description' => __( 'Variation attribute value.', 'woocommerce-mix-and-match-products' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
			),
			'readonly' => true,
		);
	} // END get_additional_cart_schema()

	/**
	 * Adds Mix and Match Product schema to the cart.
	 *
	 * @param  array $schema Before schema is altered.
	 * @return array $schema After schema is altered.
	 */
	public static function add_additional_cart_schema( $schema ) {
		$schema['items']['properties']['mnm_config'] = self::get_additional_cart_schema();

		return $schema;
	} // END add_additional_cart_schema()

	/**
	 * Adds Mix and Match Product schema to the cart.
	 *
	 * @param  array $schema Before schema is altered.
	 * @return array $schema After schema is altered.
	 */
	public static function add_additional_cart_schema_v2( $schema ) {
		$schema['mnm_config'] = self::get_additional_cart_schema();

		return $schema;
	} // END add_additional_cart_schema_v2()

	/**
	 * Returns the REST URL for adding product to the cart.
	 *
	 * @param  string     $rest_url Default REST URL.
	 * @param  WC_Product $product  Product Object.
	 * @return string     $rest_url New REST URL.
	 */
	public static function add_to_cart_rest_url( $rest_url, $product ) {
		if ( $product->is_type( 'mix-and-match' ) ) {
			$rest_url = '';
		}

		return $rest_url;
	} // END add_to_cart_rest_url()

	/**
	 * Add Mix and Match Product data to products that
	 * are a mix and match product only.
	 *
	 * @param object     $response
	 * @param WC_Product $product Product object.
	 */
	public static function add_mnm_product_data( $response, $product ) {
		if ( $product->is_type( 'mix-and-match' ) ) {
			$products = array();

			if ( $product->has_child_items() ) {
				foreach ( $product->get_child_items() as $child_item ) {
					$products[] = array(
						'child_id'          => $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id(),
						'child_item_id'     => $child_item->get_child_item_id(),
						'product_id'        => $child_item->get_product()->get_parent_id() > 0 ? $child_item->get_product()->get_parent_id() : $child_item->get_product()->get_id(),
						'variation_id'      => $child_item->get_product()->get_parent_id() > 0 ? $child_item->get_product()->get_id() : 0,
						'name'              => $child_item->get_product()->get_name( 'view' ),
						'raw_price'         => cocart_prepare_money_response( $child_item->get_product()->get_price( 'view' ), wc_get_price_decimals() ),
						'raw_regular_price' => cocart_prepare_money_response( $child_item->get_product()->get_regular_price( 'view' ), wc_get_price_decimals() ),
						'raw_sale_price'    => cocart_prepare_money_response( $child_item->get_product()->get_sale_price( 'view' ), wc_get_price_decimals() ),
						'price'             => wc_format_decimal( $child_item->get_product()->get_price( 'view' ), wc_get_price_decimals() ),
						'regular_price'     => wc_format_decimal( $child_item->get_product()->get_regular_price( 'view' ), wc_get_price_decimals() ),
						'sale_price'        => wc_format_decimal( $child_item->get_product()->get_sale_price( 'view' ), wc_get_price_decimals() ),
						'on_sale'           => $child_item->get_product()->is_on_sale( 'view' ),
					);
				}
			}

			$response->data['mnm_data'] = self::mnm_data( $product, $products );
		}

		return $response;
	} // add_mnm_product_data()

	/**
	 * Add Mix and Match Product data to products that
	 * are a mix and match product only.
	 *
	 * @param object     $response
	 * @param WC_Product $product Product object.
	 */
	public static function add_mnm_product_data_v2( $response, $product ) {
		if ( $product->is_type( 'mix-and-match' ) ) {
			$children = $product->get_child_items();

			$products = array();

			foreach ( $children as $child_item ) {
				$products[] = array(
					'id'     => $child_item->get_product()->get_id(),
					'name'   => $child_item->get_product()->get_name( 'view' ),
					'prices' => array(
						'raw_price'         => cocart_prepare_money_response( $child_item->get_product()->get_price( 'view' ), wc_get_price_decimals() ),
						'raw_regular_price' => cocart_prepare_money_response( $child_item->get_product()->get_regular_price( 'view' ), wc_get_price_decimals() ),
						'raw_sale_price'    => cocart_prepare_money_response( $child_item->get_product()->get_sale_price( 'view' ), wc_get_price_decimals() ),
						'price'             => wc_format_decimal( $child_item->get_product()->get_price( 'view' ), wc_get_price_decimals() ),
						'regular_price'     => wc_format_decimal( $child_item->get_product()->get_regular_price( 'view' ), wc_get_price_decimals() ),
						'sale_price'        => wc_format_decimal( $child_item->get_product()->get_sale_price( 'view' ), wc_get_price_decimals() ),
						'on_sale'           => $child_item->get_product()->is_on_sale( 'view' ),
					),
				);
			}

			$data = self::mnm_data( $product, $products );

			unset( $data[ 'per_product_shipping' ] );

			$response->data['mnm_data'] = $data;
		}

		return $response;
	} // add_mnm_product_data_v2()

	/**
	 * Returns Mix and Match Product data.
	 *
	 * @param  WC_Product $product  Returns product details of the parent product.
	 * @param  array      $products List of available products applied to the container.
	 * @return array
	 */
	public static function mnm_data( $product, $products ) {
		return array(
			'base_raw_price'         => cocart_prepare_money_response( $product->get_price(), wc_get_price_decimals() ),
			'base_raw_regular_price' => cocart_prepare_money_response( $product->get_regular_price(), wc_get_price_decimals() ),
			'base_raw_sale_price'    => cocart_prepare_money_response( $product->get_sale_price(), wc_get_price_decimals() ),
			'base_price'             => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
			'base_regular_price'     => wc_format_decimal( $product->get_regular_price(), wc_get_price_decimals() ),
			'base_sale_price'        => wc_format_decimal( $product->get_sale_price(), wc_get_price_decimals() ),
			'has_discount'           => $product->has_discount(),
			'min_raw_price'          => cocart_prepare_money_response( $product->get_min_raw_price(), wc_get_price_decimals() ),
			'min_raw_regular_price'  => cocart_prepare_money_response( $product->get_min_raw_regular_price(), wc_get_price_decimals() ),
			'max_raw_price'          => cocart_prepare_money_response( $product->get_max_raw_price(), wc_get_price_decimals() ),
			'max_raw_regular_price'  => cocart_prepare_money_response( $product->get_max_raw_regular_price(), wc_get_price_decimals() ),
			'min_price'              => wc_format_decimal( $product->get_min_raw_price(), wc_get_price_decimals() ),
			'min_regular_price'      => wc_format_decimal( $product->get_min_raw_regular_price(), wc_get_price_decimals() ),
			'max_price'              => wc_format_decimal( $product->get_max_raw_price(), wc_get_price_decimals() ),
			'max_regular_price'      => wc_format_decimal( $product->get_max_raw_regular_price(), wc_get_price_decimals() ),
			'min_container_size'     => $product->get_min_container_size(),
			'max_container_size'     => $product->get_max_container_size(),
			'products'               => $products,
			'per_product_pricing'    => $product->get_priced_per_product(),
			'per_product_discount'   => empty( $product->get_discount() ) ? 0 : $product->get_discount(),
			'per_product_shipping'   => $product->get_shipped_per_product(),
			'per_product_layout'     => $product->has_layout_override(),
			'product_layout'         => $product->get_layout(),
			'product_form_location'  => $product->get_add_to_cart_form_location(),
			'packing_mode'           => $product->get_packing_mode(),
			'weight_cumulative'      => $product->is_weight_cumulative(),
		);
	} // END mnm_data()

	/**
	 * Returns the price range for the Mix and Match Product.
	 *
	 * @param  array      $price   Original price range if any.
	 * @param  WC_Product $product Product object.
	 * @return array      $price   Price range returned.
	 */
	public static function price_range( $price, $product ) {
		if ( $product->is_type( 'mix-and-match' ) && $product->get_priced_per_product() ) {
			$price = array(
				'from'         => cocart_prepare_money_response( $product->get_min_raw_price(), wc_get_price_decimals() ),
				'to'           => cocart_prepare_money_response( $product->get_max_raw_price(), wc_get_price_decimals() ),
				'from_regular' => cocart_prepare_money_response( $product->get_min_raw_regular_price(), wc_get_price_decimals() ),
				'to_regular'   => cocart_prepare_money_response( $product->get_max_raw_regular_price(), wc_get_price_decimals() ),
			);
		}

		return $price;
	} // END price_range()

	/**
	 * Adds Mix and Match Product schema to Products.
	 *
	 * @param  array $schema Before schema is altered.
	 * @return array $schema After schema is altered.
	 */
	public static function add_additional_fields_schema( $schema ) {
		$mnm_fields = array(
			'description' => __( 'Mix and Match Product Data.', 'woocommerce-mix-and-match-products' ),
			'type'        => 'object',
			'context'     => array( 'view' ),
			'properties'  => array(
				'base_raw_price' => array(
					'description' => __( 'Base raw price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'base_raw_regular_price' => array(
					'description' => __( 'Base raw regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'base_raw_sale_price' => array(
					'description' => __( 'Base raw sale price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'base_price' => array(
					'description' => __( 'Base price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'base_regular_price' => array(
					'description' => __( 'Base regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'base_sale_price' => array(
					'description' => __( 'Base sale price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'has_discount' => array(
					'description' => __( 'Product has discount?', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'min_raw_price' => array(
					'description' => __( 'Minimum raw price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'min_raw_regular_price' => array(
					'description' => __( 'Minimum raw regular_price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'max_raw_price' => array(
					'description' => __( 'Maximum raw price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'max_raw_regular_price' => array(
					'description' => __( 'Maximum raw regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'min_price' => array(
					'description' => __( 'Minimum price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'min_regular_price' => array(
					'description' => __( 'Minimum regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'max_price' => array(
					'description' => __( 'Maximum price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'max_regular_price' => array(
					'description' => __( 'Maximum regular price.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'min_container_size' => array(
					'description' => __( 'Minimum container size.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'max_container_size' => array(
					'description' => __( 'Maximum container size.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'products' => array(
					'description' => __( 'Products available for this container.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'per_product_pricing' => array(
					'description' => __( 'Pricing per product.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'per_product_discount' => array(
					'description' => __( 'Discount per product.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'per_product_shipping' => array(
					'description' => __( 'Deprecated: Shipping per product, use packing mode instead.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' )
				),
				'per_product_layout' => array(
					'description' => __( 'Has product-specific layouts that override global setting.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'product_layout' => array(
					'description' => __( 'Single-product layout.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'product_form_location' => array(
					'description' => __( 'Single-product add to cart form location.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'packing_mode' => array(
					'description' => __( 'Packing mode.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'string',
					'context'     => array( 'view' )
				),
				'weight_cumulative' => array(
					'description' => __( 'Shipping weight is cumulative.', 'woocommerce-mix-and-match-products' ),
					'type'        => 'boolean',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			)
		);

		// Check if we are adding to a newer schema.
		if ( isset( $schema['products'] ) ) {
			$schema['products']['properties']['mnm_data'] = $mnm_fields;
		}
		else {
			$schema['mnm_data'] = $mnm_fields;
		}

		return $schema;
	} // END add_additional_fields_schema()

} // END class

WC_MNM_COCART_Compatibility::init();
