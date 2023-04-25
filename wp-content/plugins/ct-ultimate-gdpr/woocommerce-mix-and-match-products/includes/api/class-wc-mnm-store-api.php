<?php
/**
 * WC_MNM_Store_API class
 *
 * @package  WooCommerce Mix and Match Products/REST API
 * @since    2.0.0
 * @version  2.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * Extends the store public API with container related data for each container parent and child item.
 */
class WC_MNM_Store_API {

	/**
	 * Stores the cart item key of the last child item.
	 *
	 * @var string
	 */
	private static $last_child_item_key;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'mix_and_match';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		self::extend_store();

		// Aggregate cart item prices/subtotals and filter min/max/multipleof quantities.
		add_filter( 'rest_request_after_callbacks', array( __CLASS__, 'filter_cart_item_data' ), 10, 3 );

		// Validate container add to cart in the Store API and add cart errors.
		add_action( 'woocommerce_store_api_validate_add_to_cart', array( __CLASS__, 'validate_add_to_cart_item' ), 10, 2 );

		// Validate container in the Store API and add cart errors.
		add_action( 'woocommerce_store_api_validate_cart_item', array( __CLASS__, 'validate_cart_item' ), 10, 2 );

		// Remove quantity selectors from child items.
		add_filter( 'woocommerce_store_api_product_quantity_editable', array( __CLASS__, 'product_quantity_editable' ), 10, 3 );
		 
		// Prevent access to the checkout block.
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( __CLASS__, 'validate_draft_order' ) );

		// Prevent removal of child items.
		add_action( 'woocommerce_remove_cart_item', array( __CLASS__, 'prevent_child_item_removal' ), 10, 2 );
	}

	/**
	 * Registers the actual data into each endpoint.
	 */
	public static function extend_store() {

		if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			return;
		}

		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => CartItemSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( __CLASS__, 'extend_cart_item_data' ),
				'schema_callback' => array( __CLASS__, 'extend_cart_item_schema' ),
				'schema_type'     => ARRAY_A,
			)
		);
	}

	/**
	 * Register parent/child product data into cart/items endpoint.
	 *
	 * @param array  $cart_item
	 * @return array $item_data
	 */
	public static function extend_cart_item_data( $cart_item ) {

		$item_data = array();

		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( ! wc_mnm_is_product_container_type( $cart_item[ 'data' ] ) ) {
				return $item_data;
			}

			$container = $cart_item[ 'data' ];

			// Reset last item key.
			self::$last_child_item_key = false;

			$child_cart_keys = wc_mnm_get_child_cart_items( $cart_item, false, true );

			// Find and store last item.
			if ( $child_cart_keys ) {
				self::$last_child_item_key = end( $child_cart_keys );
			}

			$item_data[ 'child_items' ] = $cart_item[ 'mnm_contents' ];
			$item_data[ 'container_data' ] = array(
				'configuration'         => $cart_item[ 'mnm_config' ],
				'is_priced_per_product' => $container->is_priced_per_product(),
				'is_editable'           => apply_filters( 'wc_mnm_show_edit_it_cart_link', true, $cart_item, $cart_item[ 'key' ] ),
			);

		} elseif ( $container_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			$container = $container_item[ 'data' ];

			if ( ! wc_mnm_is_product_container_type( $container ) ) {
				return $item_data;
			}

			$child_item = $container->get_child_item( $cart_item[ 'child_item_id' ] );

			if ( ! $child_item ) {
				return $item_data;
			}

			$item_data[ 'container' ]   = $cart_item[ 'mnm_container' ];

			$child_config_qty      = $cart_item[ 'quantity' ] / $container_item[ 'quantity' ];

			$item_data[ 'child_item_data' ] = array(
				'container_id'          => $container_item[ 'product_id' ],
				'child_item_id'         => $cart_item[ 'child_item_id' ],
				'child_qty'             => $child_config_qty,
				'is_priced_per_product' => $container->is_priced_per_product(),
				'is_last'               => self::$last_child_item_key === $cart_item[ 'key' ],
			);
		}

		return $item_data;
	}

	/**
	 * Register subscription product schema into cart/items endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_item_schema() {
		return array(
			'mnm_container'           => array(
				'description' => __( 'Cart item key of mix and match product that contains this item.', 'woocommerce-mix-and-match-products' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'child_items'        => array(
				'description' => __( 'List of cart item keys grouped by this mix and match product.', 'woocommerce-mix-and-match-products' ),
				'type'        => array( 'array', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'container_data'          => array(
				'description' => __( 'Mix and Match product data.', 'woocommerce-mix-and-match-products' ),
				'type'        => array( 'object', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'child_item_data'    => array(
				'description' => __( 'ID of this child item.', 'woocommerce-mix-and-match-products' ),
				'type'        => array( 'object', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			)
		);
	}

	/**
	 * Aggregates container item prices.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_prices( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'mix-and-match' ) || ! $cart_item[ 'data' ]->is_priced_per_product() ) {
			return;
		}

		$item_data[ 'prices' ]->raw_prices[ 'price' ]         = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_price_amount( $cart_item, 'price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
		$item_data[ 'prices' ]->raw_prices[ 'regular_price' ] = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_price_amount( $cart_item, 'regular_price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
		$item_data[ 'prices' ]->raw_prices[ 'sale_price' ]    = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_price_amount( $cart_item, 'sale_price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
	}

	/**
	 * Aggregates container item subtotals.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_totals( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'mix-and-match' ) || ! $cart_item[ 'data' ]->is_priced_per_product() ) {
			return;
		}

		$item_data[ 'totals' ]->line_total        = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_subtotal_amount( $cart_item, 'total' ) );
		$item_data[ 'totals' ]->line_total_tax    = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_subtotal_amount( $cart_item, 'tax' ) );
		$item_data[ 'totals' ]->line_subtotal     = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_subtotal_amount( $cart_item, 'subtotal' ) );
		$item_data[ 'totals' ]->line_subtotal_tax = self::prepare_money_response( WC_Mix_and_Match()->display->get_container_cart_item_subtotal_amount( $cart_item, 'subtotal_tax' ) );
	}

	/**
	 * Adjust container item quantity limits to prevent child items from being edited.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_quantity_limits( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'mix-and-match' ) ) {
			return;
		}

		if ( $child_cart_items = wc_mnm_get_child_cart_items( $cart_item ) ) {

			foreach ( $child_cart_items as $child_cart_item_key => $child_cart_item ) {

				// Let's cache this now, as we'll need it later.
				WC_MNM_Helpers::cache_set(
                    'child_item_quantity_limits_' . $child_cart_item_key,
                    array(
					'multiple_of' => $child_cart_item['quantity'],
					'minimum'     => $child_cart_item['quantity'],
					'maximum'     => $child_cart_item['quantity'],
                    ) 
                );

			}
		}
	}

	/**
	 * Filter container cart item permalink to support cart editing.
	 *
	 * @since 2.0.7
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_short_description( &$item_data, $cart_item ) {

		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			$container = $cart_item[ 'data' ];

			if ( ! $container->is_type( 'mix-and-match' ) ) {
				return;
			}

			if ( apply_filters( 'wc_mnm_show_edit_it_cart_link', true, $cart_item, $cart_item[ 'key' ] ) ) {

				$trimmed_short_description = '';

				if ( $item_data[ 'short_description' ] ) {
					$trimmed_short_description = '<p class="wc-block-components-product-metadata__description-text">' . wp_trim_words( $item_data[ 'short_description' ], 12 ) . '</p>';
				}

				$edit_in_cart_link = esc_url( $container->get_cart_edit_link( $cart_item ) );
				$item_data[ 'short_description' ] = '<p class="wc-block-cart-item__edit"><a class="components-button wc-block-components-button wc-block-cart-item__edit-link contained" href="' . $edit_in_cart_link . '"><span class="wc-block-components-button__text">' .  _x( 'Edit selections', 'edit in cart link text', 'woocommerce-mix-and-match-products' ) . '</span></a></p>' . $trimmed_short_description;
			}

		}
	}

	/**
	 * Disable editing for child item quantities.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_child_cart_item_quantity_limits( &$item_data, $cart_item ) {

		$child_cart_item_key        = $cart_item[ 'key' ];
		$child_item_quantity_limits = WC_MNM_Helpers::cache_get( 'child_item_quantity_limits_' . $child_cart_item_key );

		if ( is_null( $child_item_quantity_limits ) ) {
			return;
		}

		$step = $child_item_quantity_limits[ 'multiple_of' ];
		$min  = $child_item_quantity_limits[ 'minimum' ];
		$max  = $child_item_quantity_limits[ 'maximum' ];

		$item_data[ 'quantity_limits' ]->multiple_of = $step;
		$item_data[ 'quantity_limits' ]->minimum     = $min;
		$item_data[ 'quantity_limits' ]->maximum     = $max;

	}

	/**
	 * Disable permalinks for child items.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_child_cart_item_permalink( &$item_data, $cart_item ) {

		$container_item = wc_mnm_get_cart_item_container( $cart_item );

		if ( $container_item ) {

			$container = $container_item[ 'data' ];

			if ( ! $container->is_type( 'mix-and-match' ) ) {
				return;
			}

			$item_data[ 'catalog_visibility' ] = 'hidden';
		}

	}


	/**
	 * Convert monetary values from WooCommerce to string based integers, using
	 * the smallest unit of a currency.
	 *
	 * @param string|float  $amount
	 * @param int           $decimals
	 * @param int           $rounding_mode
	 * @return string
	 */
	private static function prepare_money_response( $amount, $decimals = 2, $rounding_mode = PHP_ROUND_HALF_UP ) {
		return woocommerce_store_api_get_formatter( 'money' )->format(
			$amount,
			array(
				'decimals'      => $decimals,
				'rounding_mode' => $rounding_mode,
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter store API responses to:
	 *
	 * - aggregate container prices/subtotals;
	 * - keep min/max/step quantity fields in sync.
	 *
	 * @param  $response  WP_REST_Response
	 * @param  $server    WP_REST_Server
	 * @param  $request   WP_REST_Request
	 * @return WP_REST_Response
	 */
	public static function filter_cart_item_data( $response, $server, $request ) {

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( strpos( $request->get_route(), 'wc/store' ) === false ) {
			return $response;
		}

		$data = $response->get_data();

		if ( empty( $data[ 'items' ] ) ) {
			return $response;
		}

		$cart = WC()->cart->get_cart();

		foreach ( $data[ 'items' ] as &$item_data ) {

			$cart_item_key = $item_data[ 'key' ];
			$cart_item     = isset( $cart[ $cart_item_key ] ) ? $cart[ $cart_item_key ] : null;

			if ( is_null( $cart_item ) ) {
				continue;
			}

			/**
			 * StoreAPI returns the following fields as
			 * - object (/wc/store/v1/cart)
			 * - array (/wc/store/v1/cart/extensions)
			 *
			 * Casting them to objects, to avoid PHP8+ fatal errors.
			 *
			 * @see https://github.com/woocommerce/woocommerce-product-bundles/issues/1096
			 * @see https://github.com/woocommerce/woocommerce-blocks/issues/7275
			 */
			
			$item_data[ 'quantity_limits' ] = (object) $item_data[ 'quantity_limits' ];
			$item_data[ 'prices' ]          = (object) $item_data[ 'prices' ];
			$item_data[ 'totals' ]          = (object) $item_data[ 'totals' ];
			$item_data[ 'extensions' ]      = (object) $item_data[ 'extensions' ];

			if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

				self::filter_container_cart_item_prices( $item_data, $cart_item );
				self::filter_container_cart_item_totals( $item_data, $cart_item );
				self::filter_container_cart_item_quantity_limits( $item_data, $cart_item );
				self::filter_container_cart_item_short_description( $item_data, $cart_item );

			} elseif ( wc_mnm_is_child_cart_item( $cart_item ) ) {

				self::filter_child_cart_item_quantity_limits( $item_data, $cart_item );
				self::filter_child_cart_item_permalink( $item_data, $cart_item );
			}
		}

		$response->set_data( $data );

		return $response;
	}


	/**
	 * Validate container in Store API context.
	 *
	 * @throws RouteException
	 *
	 * @param  WC_Product  $product
	 * @param array       $request Add to cart request params including id, quantity, and variation attributes.
	 */
	public static function validate_add_to_cart_item( $product, $request ) {

		if ( wc_mnm_is_product_container_type( $product ) ) {
			try {
				WC_Mix_and_Match()->cart->validate_container_in_cart( $request );
			} catch ( Exception $e ) {
				$notice = $e->getMessage();
				throw new RouteException( 'woocommerce_store_api_invalid_container_configuration', $notice );
			}
		}
	}



	/**
	 * Validate container in Store API context.
	 *
	 * @throws RouteException
	 *
	 * @param  WC_Product  $product
	 * @param  array       $cart_item
	 */
	public static function validate_cart_item( $product, $cart_item ) {

		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			try {
				WC_Mix_and_Match()->cart->validate_container_in_cart( $cart_item );
			} catch ( Exception $e ) {
				$notice = $e->getMessage();
				throw new RouteException( 'woocommerce_store_api_invalid_container_configuration', $notice );
			}
		}
	}

	/**
	 * Remove quantity inputs from child items in Store API context.
	 * 
	 * @param bool $qty_is_editable
	 * @param  WC_Product  $product
	 * @param  array       $cart_item
	 * @return false
	 */
	public static function product_quantity_editable( $qty_is_editable, $product, $cart_item ) {
		if ( wc_mnm_is_child_cart_item( $cart_item ) ) {
			$qty_is_editable = false;
		}
		return $qty_is_editable;
	}

	/**
	 * Prevents access to the checkout block if a container in the cart is misconfigured.
	 *
	 * @throws RouteException
	 *
	 * @param  WC_Order  $order
	 * @return array
	 */
	public static function validate_draft_order( $order ) {

		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
			self::validate_cart_item( $cart_item[ 'data' ], $cart_item );
		}
	}

	/**
	 * Prevent removal of child items.
	 *
	 * @throws RouteException
	 *
	 * @param  string   $cart_item_key
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public static function prevent_child_item_removal( $cart_item_key, $cart ) {

		if ( ! WC_MNM_Core_Compatibility::is_store_api_request( 'cart/remove-item' ) || ! $cart->find_product_in_cart( $cart_item_key ) || ! wc_mnm_is_child_cart_item( $cart->cart_contents[$cart_item_key] ) ) {
			return;
		}

		$notice = __( 'This product is part of a mix and match container and cannot be removed independently.', 'woocommerce-mix-and-match-products' );
		throw new RouteException( 'woocommerce_store_api_mnm_child_item', $notice );
	}

	/**
	 * Filter container cart item permalink to support cart editing.
	 *
	 * @deprecated 2.0.7
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_permalink( &$item_data, $cart_item ) {
		wc_deprecated_function( 'WC_MNM_Store_API::filter_container_cart_item_permalink()', '2.0.7', 'WC_MNM_Store_API::filter_container_cart_item_short_description()' );
		return self::filter_container_cart_item_short_description( $item_data, $cart_item );
	}

}
