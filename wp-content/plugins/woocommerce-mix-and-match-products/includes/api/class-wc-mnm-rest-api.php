<?php
/**
 * WooCommerce REST API
 *
 * Adds Mix and Match Product data to the WooCommerce REST API.
 *
 * @package  WooCommerce Mix and Match Products/REST API
 * @since    1.10.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_REST_API Class.
 *
 * Adds WooCommerce Mix and Match product data to WC REST API.
 */
class WC_Mix_and_Match_REST_API {

	/**
	 * Setup API class.
	 *
	 * @return object
	 */
	public static function init() {

		// Register WP REST API custom product fields.
		add_filter( 'woocommerce_rest_product_schema', array( __CLASS__, 'filter_product_schema' ) );
		add_filter( 'woocommerce_rest_prepare_product_object', array( __CLASS__, 'prepare_product_response' ), 10, 3 );
		add_filter( 'woocommerce_rest_pre_insert_product_object', array( __CLASS__, 'prepare_insert_product' ), 10, 2 );

		// Order Schema.
		add_filter( 'woocommerce_rest_shop_order_schema', array( __CLASS__, 'filter_order_schema' ) );
		add_filter( 'woocommerce_rest_shop_subscription_schema', array( __CLASS__, 'filter_order_schema' ) );

		// Order Response.
		add_filter( 'woocommerce_rest_prepare_shop_order_object', array( __CLASS__, 'prepare_order_response' ), 10, 3 );
		add_filter( 'woocommerce_rest_prepare_shop_subscription_object', array( __CLASS__, 'prepare_order_response' ), 10, 3 );

		// Add configuration data as meta for later post-processing.
		add_action( 'woocommerce_rest_set_order_item', array( __CLASS__, 'set_order_item' ), 10, 2 );

		// Add a configured container to order.
		add_filter( 'woocommerce_rest_pre_insert_shop_order_object', array( __CLASS__, 'add_container_to_order' ), 10, 2 );
		add_filter( 'woocommerce_rest_pre_insert_shop_subscription_object', array( __CLASS__, 'add_container_to_order' ), 10, 2 );
	}

	/*
	|--------------------------------------------------------------------------
	| Products.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter the data for a response.
	 *
	 * The dynamic portion of the hook name, $this->post_type,
	 * refers to object type being prepared for the response.
	 *
	 * @param array $schema Schema array.
	 * @return array
	 */
	public static function filter_product_schema( $schema ) {
		return array_merge( $schema, self::get_extended_product_schema() );
	}

	/**
	 * Gets extended (unprefixed) schema properties for products.
	 *
	 * @return array
	 */
	private static function get_extended_product_schema() {

		return array(
			'mnm_layout_override' => array(
				'description' => __( 'Has product-specific layouts that override global setting. Applicable only for Mix and Match type products.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' )
			),
			'mnm_layout'              => array(
				'description' => __( 'Single-product details page layout. Applicable only for Mix and Match type products.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'string',
				'enum'        => array_keys( WC_Product_Mix_and_Match::get_layout_options() ),
				'context'     => array( 'view', 'edit' )
			),
			'mnm_form_location' => array(
				'description' => __( 'Single-product details page add to cart form location. Applicable only for Mix and Match type products.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'string',
				'enum'        => array_keys( WC_Product_Mix_and_Match::get_add_to_cart_form_location_options() ),
				'context'     => array( 'view', 'edit' )
			),
			'mnm_content_source' => array(
				'description' => __( 'Source of child products. Applicable only for Mix and Match type products.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'string',
				'enum'        => array( 'products', 'categories' ),
				'context'     => array( 'view', 'edit' )
			),
			'mnm_child_category_ids' => array(
				'description' => __( 'List of child categories allowed in this product.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' )
			),
			'mnm_child_items'              => array(
				'description' => __( 'List of child items contained in this product.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'child_id'            => array(
							'description' => __( 'Child product|variation ID. Deprecated 2.0, use child_item_id instead.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true
						),
						'child_item_id'            => array(
							'description' => __( 'Child item ID.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'view' ),
							'readonly'    => true
						),
						'delete'       => array(
							'description' => __( 'Set to true to delete the child item with the specified child item ID.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'boolean',
							'context'     => array( 'edit' )
						),
						'product_id'   => array(
							'description' => __( 'Child product ID.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' )
						),
						'variation_id' => array(
							'description' => __( 'Child variation ID.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' )
						),
					)
				)
			),
			'mnm_min_container_size'   => array(
				'description' => __( 'Minimum container size.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' )
			),
			'mnm_max_container_size'   => array(
				'description' => __( 'Maximum container quantity.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'mixed',
				'oneOf'       => array(
					'type' => 'integer',
					'type' => null,
				),
				'context'     => array( 'view', 'edit' )
			),
			'mnm_discount' => array(
				'description' => __( 'Indicates the percentage discount to apply to each child product when per-product pricing is enabled.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' )
			),
			'mnm_priced_per_product' => array(
				'description' => __( 'Indicates whether the container price is calculated from the price of the selected child products.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' )
			),
			'mnm_packing_mode' => array(
				'description' => __( 'Indicates how the child products are packed/shipped.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'boolean',
				'enum'        => array( 'virtual', 'together', 'separate', 'separate_plus' ),
				'context'     => array( 'view', 'edit' ),
			),
			'mnm_shipped_per_product' => array(
				'description' => __( 'Deprecated: Indicates whether the child products are shipped individually.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'boolean',
				'context'     => array( 'view' )
			),
			'mnm_weight_cumulative' => array(
				'description' => __( 'Shipping weight calculation mode.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' )
			),
		);
	}


	/**
	 * Filter the data for a response.
	 *
	 * The dynamic portion of the hook name, $this->post_type,
	 * refers to object type being prepared for the response.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WC_Product     $product  Product object.
	 * @return WP_REST_Response $response The response object.
	 */
	public static function prepare_product_response( $response, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {
			$response->data['mnm_layout_override']     = $product->has_layout_override();
			$response->data['mnm_layout']              = $product->get_layout();
			$response->data['mnm_form_location']       = $product->get_add_to_cart_form_location();
			$response->data['mnm_content_source']      = $product->get_content_source();
			$response->data['mnm_child_category_ids']  = $product->get_child_category_ids();
			$response->data['mnm_min_container_size']  = $product->get_min_container_size();
			$response->data['mnm_max_container_size']  = $product->get_max_container_size();
			$response->data['mnm_priced_per_product']  = $product->is_priced_per_product();
			$response->data['mnm_packing_mode']        = $product->get_packing_mode();
			$response->data['mnm_shipped_per_product'] = ! $product->is_packed_together();
			$response->data['mnm_weight_cumulative']   = $product->is_weight_cumulative();
			$response->data['mnm_discount']            = $product->get_discount();
			$response->data['mnm_child_items']         = self::prepare_child_items_response( $product );
		} else {
			$response->data['mnm_layout_override']     = '';
			$response->data['mnm_layout']              = '';
			$response->data['mnm_form_location']       = '';
			$response->data['mnm_content_source']      = '';
			$response->data['mnm_child_category_ids']  = '';
			$response->data['mnm_min_container_size']  = '';
			$response->data['mnm_max_container_size']  = '';
			$response->data['mnm_priced_per_product']  = '';
			$response->data['mnm_packing_mode']        = '';
			$response->data['mnm_shipped_per_product'] = '';
			$response->data['mnm_weight_cumulative']   = '';
			$response->data['mnm_discount']            = '';
			$response->data['mnm_child_items']         = '';
		}

		return $response;
	}

	/**
	 * Convert child items into a REST readable array.
	 *
	 * @since 2.0.0
	 * 
	 * @param WC_Product     $product  Product object.
	 * @return array
	 */
	private static function prepare_child_items_response( $product ) {
		$child_items = $product->get_child_items();
		$response_items = array();
		foreach( $child_items as $child_item ) {
			$response_items[] = array(
				'child_item_id' => $child_item->get_id(),
				'child_id'      => $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id(),
				'product_id'    => $child_item->get_product_id(),
				'variation_id'  => $child_item->get_variation_id(),
			);
		}
		return $response_items;
	}

	/**
	 * Filters an object before it is inserted via the REST API.
	 *
	 * @param WC_Product     $product  Product object.
	 * @param WP_REST_Request $request  Request object.
	 * @return WC_Product     $product  Product object.
	 */

	public static function prepare_insert_product( $product, $request ) {

		if ( isset( $request['mnm_layout_override'] ) ) {
			$product->set_layout_override( $request['mnm_layout_override'] );
		}

		if ( isset( $request['mnm_layout'] ) ) {
			$product->set_layout( $request['mnm_layout'] );
		}

		if ( isset( $request['mnm_form_location'] ) ) {
			$product->set_add_to_cart_form_location( $request['mnm_form_location'] );
		}

		if ( isset( $request['mnm_content_source'] ) ) {
			$product->set_content_source( $request['mnm_content_source'] );
		}

		if ( isset( $request['mnm_child_category_ids'] ) && is_array( $request['mnm_child_category_ids'] ) ) {
			$product->set_child_category_ids( $request['mnm_child_category_ids'] );
		}

		if ( isset( $request['mnm_min_container_size'] ) ) {
			$product->set_min_container_size( $request['mnm_min_container_size'] );
		}

		if ( isset( $request['mnm_max_container_size'] ) ) {
			$product->set_max_container_size( $request['mnm_max_container_size'] );
		}

		if ( isset( $request['mnm_priced_per_product'] ) ) {
			$product->set_priced_per_product( $request['mnm_priced_per_product'] );
		}

		if ( isset( $request['mnm_packing_mode'] ) ) {
			$product->set_packing_mode( $request['mnm_packing_mode'] );
		}

		if ( isset( $request['mnm_weight_cumulative'] ) ) {
			$product->set_weight_cumulative( $request['mnm_weight_cumulative'] );
		}

		if ( isset( $request['mnm_discount'] ) ) {
			$product->set_discount( $request['mnm_discount'] );
		}

		if ( isset( $request['mnm_child_items'] ) && is_array( $request['mnm_child_items'] ) ) {
			$new           = array();
			$deleted       = array(); // For back-compat deletion by product ID.
			$deleted_items = array();

			$allowed_contents = $product->get_child_product_ids( 'edit' );

			foreach ( $request['mnm_child_items'] as $data ) {

				$action             = '';
				$child_item_id      = isset( $data['child_item_id'] ) ? absint( $data['child_item_id'] ) : 0;
				$child_product_id   = isset( $data['product_id'] ) ? absint( $data['product_id'] ) : 0;
				$child_variation_id = isset( $data['variation_id'] ) ? absint( $data['variation_id'] ) : 0;
				$child_id           = isset( $data['child_id'] ) ? absint( $data['child_id'] ) : 0;

				// Updating/deleting item.
				if ( $child_item_id > 0 ) {
					if ( ! array_key_exists( $child_item_id, $allowed_contents ) ) {
						// translators: %d is the child item ID.
						throw new WC_REST_Exception( 'woocommerce_rest_invalid_child_item_id', sprintf( __( 'Child item ID #%d does not exist in mix and match container.', 'woocommerce-mix-and-match-products' ), $child_item_id ), 400 );
					}

					// Add item to 'deleted' array.
					if ( isset( $data['delete'] ) && true === $data['delete'] ) {
						$action = 'delete';
						$deleted_items[] = $child_item_id;
						continue;
					}

				// Updating/deleting item by child product ID, for back-compatibility.
				} elseif ( $child_id > 0 ) {

					// Add product to 'deleted' array.
					if ( isset( $data['delete'] ) && true === $data['delete'] ) {

						if ( ! in_array( $child_id, $allowed_contents ) ) {
							// translators: %d is the product|variation ID.
							throw new WC_REST_Exception( 'woocommerce_rest_invalid_child_id', sprintf( __( 'Child product or variation ID #%d does not exist in mix and match container.', 'woocommerce-mix-and-match-products' ), $child_id ), 400 );
						}

						$action = 'delete';
						$deleted[] = $child_id;
						continue;
					}

				// Creating item.
				} elseif ( $child_item_id === 0 ) {
					$action = 'create';
				}

				// Validate the child product.
				if ( ! $child_id ) {
					$child_id      = $child_variation_id ? $child_variation_id : $child_product_id;
				}			
				$child_product = wc_get_product( $child_id );

				// Ensure product exists when updating/creating.
				if ( false === $child_product ) {
					// translators: %d is the product|variation ID.
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_child_product', sprintf( __( 'Product or variation ID #%d is invalid.', 'woocommerce-mix-and-match-products' ), $child_id ), 400 );
				}

				// Ensure product is not the container.
				if ( $child_id === $product->get_id() ) {
					// translators: %d is the product|variation ID.
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_contents', sprintf( __( 'Cannot add product ID #%d to contents for itself.', 'woocommerce-mix-and-match-products' ), $child_id ), 400 );
				}

				// Ensure the product is a supported type.
				if ( ! WC_MNM_Helpers::is_child_supported_product_type( $child_product ) ) {
					// translators: %d is the product|variation ID.
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_child_product_type', sprintf( __( 'Product or variation ID #%d not a supported product type for Mix and Match contents.', 'woocommerce-mix-and-match-products' ), $child_id ), 400 );
				}

				// Not stored in meta if defined and other than true.
				if ( isset( $data['delete'] ) ) {
					unset( $data['delete'] );
				}

				// Add item to 'new' array.
				if ( 'create' === $action ) {
					// Sanitize the input.
					$child_data_item_array = array(
						'product_id'   => $child_product->get_parent_id() > 0 ? $child_product->get_parent_id() : $child_product->get_id(),
						'variation_id' => $child_product->get_parent_id() > 0 ? $child_product->get_id() : 0,
					);
					$new[ $child_product->get_id() ] = $child_data_item_array;
				}
			}

			$child_data_items       = $product->get_child_items( 'edit' );
			$child_data_items_array = array();

			if ( ! empty( $child_data_items ) ) {
				foreach ( $child_data_items as $child_item_id => $child_item ) {

					$child_id = $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id();

					// Omit item data if item deleted.
					if ( in_array( $child_id, $deleted ) || in_array( $child_item_id, $deleted_items ) ) {
						continue;
					// Preserve item unless updated/deleted.
					} else {

						$child_data_items_array[ $child_item_id ] = $child_item;

					}
				}
			}

			// Add new items. array_merge() does not preserve numeric keys.
			$child_data_items_array = $child_data_items_array + $new;

			// Set child items on object. Neat.
			$product->set_child_items( $child_data_items_array );
		}

		return $product;
	}


	/*
	|--------------------------------------------------------------------------
	| Orders.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Gets extended (unprefixed) schema properties for order line items.
	 *
	 * @return array
	 */
	private static function get_extended_order_line_item_schema() {

		return array(
			'mnm_child_of'     => array(
				'description' => __( 'Item ID of parent line item, applicable if the item is part of a mix and match container.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true
			),
			'mnm_child_items' => array(
				'description' => __( 'Item IDs of mix and match child line items, applicable if the item is a mix and match container.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'integer'
				),
				'readonly'    => true
			),
			'mnm_configuration' => array(
				'description' => __( 'Mix and match configuration array. Must be defined when adding a mix and match-type line item to an order, to ensure child line items are added to the order as well.', 'woocommerce-mix-and-match-products' ),
				'type'        => 'array',
				'context'     => array( 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'product_id'        => array(
							'description' => __( 'Child product ID.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
						'quantity'          => array(
							'description' => __( 'Chosen child item quantity.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
						'variation_id'      => array(
							'description' => __( 'Chosen variation ID, if applicable.', 'woocommerce-mix-and-match-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
					)
				)
			)
		);
	}


	/**
	 * Adds 'mnm_child_of' and 'mnm_child_items' schema properties to line items.
	 *
	 * @param  array  $schema
	 * @return array
	 */
	public static function filter_order_schema( $schema ) {

		foreach ( self::get_extended_order_line_item_schema() as $field_name => $field_content ) {
			$schema['line_items']['properties'][ $field_name ] = $field_content;
		}

		return $schema;
	}


	/**
	 * Filter the data for a response.
	 *
	 * The dynamic portion of the hook name, $this->post_type,
	 * refers to object type being prepared for the response.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WC_Data          $order   Object data.
	 * @return WP_REST_Response $response The response object.
	 */
	public static function prepare_order_response( $response, $order ) {

		if ( ! empty( $response->data['line_items'] ) ) {

			$order_items = $order->get_items();

			foreach ( $response->data['line_items'] as $order_data_item_index => $order_data_item ) {

				// Default values.
				$response->data['line_items'][ $order_data_item_index ]['mnm_child_of']         = '';
				$response->data['line_items'][ $order_data_item_index ]['mnm_child_items']      = array();

				$order_data_item_id = $order_data_item['id'];

				// Add relationship references.
				if ( ! isset( $order_items[ $order_data_item_id ] ) ) {
					continue;
				}

				$parent_id    = wc_mnm_get_order_item_container( $order_items[ $order_data_item_id ], $order, true );
				$children_ids = wc_mnm_get_child_order_items( $order_items[ $order_data_item_id ], $order, true );

				if ( false !== $parent_id ) {
					$response->data['line_items'][ $order_data_item_index ]['mnm_child_of'] = $parent_id;
				} elseif ( ! empty( $children_ids ) ) {
					$response->data['line_items'][ $order_data_item_index ]['mnm_child_items'] = $children_ids;
				} else {
					continue;
				}
			}
		}

		return $response;
	}


	/**
	 * Save container configuration data on item for later processing.
	 *
	 * @param  WC_Order_Item  $item
	 * @param  array          $posted_item_data
	 */
	public static function set_order_item( $item, $posted_item_data ) {

		$action = ! empty( $posted_item_data['id'] ) ? 'update' : 'create';

		if ( 'create' === $action && ! empty( $posted_item_data['mnm_configuration'] ) && is_array( $posted_item_data['mnm_configuration'] ) ) {

			$product  = $item->get_product();
			$quantity = $item->get_quantity();

			if ( $product && $product->is_type( 'mix-and-match' ) ) {

				$configuration = self::parse_posted_container_configuration( $product, $posted_item_data['mnm_configuration'] );

				if ( WC_Mix_and_Match()->cart->validate_container_configuration( $product, $quantity, $configuration, 'add-to-order' ) ) {

					$item->update_meta_data( '_mnm_configuration', $configuration );

				} else {

					$message = __( 'The submitted mix and match container configuration could not be added to this order.', 'woocommerce-mix-and-match-products' );
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_mnm_configuration', $message, 400 );
				}

			} else {
				$message = __( 'A mix and match container with this ID does not exist.', 'woocommerce-mix-and-match-products' );
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_mnm', $message, 400 );
			}
		}
	}


	/**
	 * Filters order contents to add child items to order.
	 *
	 * @param WC_Order        $order  Object object.
	 * @param WP_REST_Request $request  Request object.
	 * @return WC_Order       $order  Object object.
	 */

	public static function add_container_to_order( $order, $request ) {

		$items_to_remove = array();

		foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
			if ( $apply_configuration = $item->get_meta( '_mnm_configuration', true ) ) {

				$container = $item->get_product();
				$quantity  = $item->get_quantity();

				// Preserve props and meta.

				$args = array(
					'configuration' => $apply_configuration,
					'meta_data'     => $item->get_meta_data()
				);

				foreach ( $item->get_data_keys() as $key ) {

					$fn = 'get_' . $key;

					if ( in_array( $key, array( 'name', 'tax_class', 'subtotal', 'subtotal_tax', 'total', 'total_tax', 'taxes' ) ) && is_callable( array( $item, $fn ) ) ) {
						$args[ $key ] = $item->$fn( 'edit' );
					}
				}

				// Add new configuration... this will add the container, so we'll remove it later.
				$result = WC_Mix_and_Match()->order->add_container_to_order( $container, $order, $quantity, $args );

				$item->delete_meta_data( '_mnm_configuration' );

				if ( ! is_wp_error( $result ) ) {
					$items_to_remove[] = $item;
				}
			}
		}

		// Need to save here to prevent duplicate containers.
		$order->save();

		// Remove now-duplicate MNM line items.
		foreach ( $items_to_remove as $remove_item ) {
			$order->remove_item( $remove_item->get_id() );
			$remove_item->delete();
		}

		return $order;
	}


	/**
	 * Converts a posted container configuration to a format understood by 'WC_Mix_and_Match_Cart::validate_container_configuration'.
	 *
	 * @param  WC_Product_Mix_and_Match  $container
	 * @param  array                     $posted_configuration
	 * @return array
	 */
	public static function parse_posted_container_configuration( $container, $posted_configuration ) {

		$configuration = array();

		foreach ( $posted_configuration as $child_configuration ) {

			// 'WC_Mix_and_Match_Cart::validate_container_configuration' expects the array to be indexed by child item ID.
			$child_configuration['variation_id'] = ! empty( $child_configuration['variation_id'] ) ? absint( $child_configuration['variation_id'] ) : 0;
			$child_configuration['product_id']   = ! empty( $child_configuration['product_id'] ) ? absint( $child_configuration['product_id'] ) : 0;
			$child_configuration['quantity']     = ! empty( $child_configuration['quantity'] ) ? absint( $child_configuration['quantity'] ) : 0;

			$child_id     = $child_configuration['variation_id'] > 0 ? $child_configuration['variation_id'] : $child_configuration['product_id'];

			if ( 0 === $child_id || 0 === $child_configuration['quantity'] ) {
				continue;
			}

			$configuration[ $child_id ] = array_diff_key( $child_configuration, array( 'attributes' => 1 ) );

		}

		return $configuration;
	}

}
WC_Mix_and_Match_REST_API::init();
