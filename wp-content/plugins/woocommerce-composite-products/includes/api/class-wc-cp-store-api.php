<?php
/**
 * WC_CP_Store_API class
 *
 * @package  Woo Composite Products
 * @since    8.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * Extends the store public API with composite related data for each composite parent and child item.
 *
 * @version 8.10.3
 */
class WC_CP_Store_API {

	/**
	 * Stores the cart item key of the last composited item.
	 *
	 * @var string
	 */
	private static $last_composited_item_key;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'composites';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		self::extend_store();

		// Aggregate cart item prices/subtotals.
		add_filter( 'rest_request_after_callbacks', array( __CLASS__, 'filter_cart_item_data' ), 10, 3 );

		// Validate composites in the Store API and add cart errors.
		add_action( 'woocommerce_store_api_validate_cart_item', array( __CLASS__, 'validate_cart_item' ), 10, 2 );

		// Prevent access to the checkout block.
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( __CLASS__, 'validate_draft_order' ) );

		// Validate removal of mandatory components.
		add_action( 'woocommerce_remove_mandatory_composited_cart_item', array( __CLASS__, 'validate_mandatory_composited_cart_item_removal' ), 10, 2 );
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
	 * Register subscription product data into cart/items endpoint.
	 *
	 * @param  array  $cart_item
	 * @return array  $item_data
	 */
	public static function extend_cart_item_data( $cart_item ) {

		$item_data = array();

		if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {

			if ( ! $cart_item[ 'data' ]->is_type( 'composite' ) ) {
				return $item_data;
			}

			$is_price_visible    = true;
			$is_subtotal_visible = true;
			$aggregate_prices    = self::is_container_cart_item_price_aggregated( $cart_item );
			$aggregate_subtotals = self::is_container_cart_item_subtotal_aggregated( $cart_item );

			// Reset last item key.
			$composited_cart_items          = wc_cp_get_composited_cart_items( $cart_item );
			self::$last_composited_item_key = end( $composited_cart_items )[ 'key' ];

			if ( empty( $cart_item[ 'line_subtotal' ] ) && ! $aggregate_prices ) {
				$is_price_visible    = false;
			}

			if ( empty( $cart_item[ 'line_subtotal' ] ) && ! $aggregate_subtotals ) {
				$is_subtotal_visible = false;
			}

			$composite                         = $cart_item[ 'data' ];
			$item_data[ 'composite_children' ] = $cart_item[ 'composite_children' ];

			/**
			 * Filter data passed by the store API.
			 * Use this filter to customize the appearance of this composite product in the front-end.
			 *
			 * @since 8.4.0
			 *
			 * @param  array  $data
			 * @param  array  $cart_item
			 */
			$item_data[ 'composite_data' ] = apply_filters(
				'woocommerce_checkout_blocks_api_composite_data',
				array(
					'configuration'             => $cart_item[ 'composite_data' ],
					'is_editable'               => $composite->is_editable_in_cart(),
					'is_price_hidden'           => ! $is_price_visible,
					'is_subtotal_hidden'        => ! $is_subtotal_visible,
					'is_meta_hidden_in_cart'    => true,
					'is_meta_hidden_in_summary' => false
				),
				$cart_item
			);

		} elseif ( $container_item = wc_cp_get_composited_cart_item_container( $cart_item ) ) {

			$composite = $container_item[ 'data' ];

			if ( ! $composite->is_type( 'composite' ) ) {
				return $item_data;
			}
			$component_id     = $cart_item[ 'composite_item' ];
			$component_option = $composite->get_component_option( $component_id, $cart_item[ 'product_id' ] );
			$component        = $composite->get_component( $component_id );
			$component_title  = $component->get_title();
			$product_id       = $cart_item[ 'data' ]->get_id();

			if ( ! $component_option ) {
				return $item_data;
			}

			$is_subtotal_hidden  = false;
			$is_removable        = false;
			$is_price_hidden     = false;
			$aggregate_prices    = self::is_container_cart_item_price_aggregated( $container_item );
			$aggregate_subtotals = self::is_container_cart_item_subtotal_aggregated( $container_item );
			$is_last             = self::$last_composited_item_key === $cart_item[ 'key' ];

			// Removable?
			if ( $component->is_optional() || $component->get_quantity( 'min' ) === 0 ) {
				$is_removable = true;
			}

			// Hide price/subtotal?
			if ( false === $component->is_priced_individually() ) {
				$is_price_hidden    = $aggregate_prices && empty( $cart_item[ 'data' ]->get_price() );
				$is_subtotal_hidden = $aggregate_subtotals && empty( $cart_item[ 'line_subtotal' ] );
			} elseif ( false === $component->is_subtotal_visible( 'cart' ) ) {
				$is_subtotal_hidden = true;
			}

			if ( empty( $cart_item[ 'data' ]->get_price() ) ) {
				$is_price_hidden = ! $cart_item[ 'data' ]->is_type( 'bundle' );
			}

			if ( empty( $cart_item[ 'line_subtotal' ] ) ) {
				$is_subtotal_hidden = ! $cart_item[ 'data' ]->is_type( 'bundle' );
			}

			$item_data[ 'composite_parent' ] = $cart_item[ 'composite_parent' ];

			/**
			 * Filter data passed by the store API.
			 * Use this filter to customize the appearance of this component in the front-end.
			 *
			 * @since 8.4.0
			 *
			 * @param  array  $data
			 * @param  array  $cart_item
			 */
			$item_data[ 'composited_item_data' ] = apply_filters(
				'woocommerce_checkout_blocks_api_composited_item_data',
				array(
					'composite_id'              => $container_item[ 'product_id' ],
					'component_id'              => $component_id,
					'composited_product_id'     => $product_id,
					'component_title'           => $component_title,
					'component_title_sanitized' => sanitize_title( $component_title ),
					'is_indented'               => true,
					'is_removable'              => $is_removable,
					'is_subtotal_aggregated'    => $aggregate_subtotals,
					'is_subtotal_hidden'        => $is_subtotal_hidden,
					'is_price_hidden'           => $is_price_hidden,
					'is_last'                   => $is_last,
					'is_hidden_in_cart'         => false,
					'is_hidden_in_summary'      => true,
					'is_meta_hidden_in_cart'    => true,
					'is_meta_hidden_in_summary' => true
				),
				$cart_item
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
			'composited_by'        => array(
				'description' => __( 'Cart item key of composite that contains this item.', 'woocommerce-composite-products' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'composited_children'  => array(
				'description' => __( 'List of cart item keys grouped by this composite.', 'woocommerce-composite-products' ),
				'type'        => array( 'array', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'composite_data'       => array(
				'description' => __( 'Composite data.', 'woocommerce-composite-products' ),
				'type'        => array( 'object', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'composited_item_data' => array(
				'description' => __( 'Data of this composited item.', 'woocommerce-composite-products' ),
				'type'        => array( 'object', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			)
		);
	}

	/**
	 * Aggregates composite container item prices.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_prices( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'composite' ) ) {
			return;
		}

		if ( ! self::is_container_cart_item_price_aggregated( $cart_item ) ) {
			return;
		}

		do_action( 'woocommerce_store_api_before_composite_aggregated_totals_calculation', $item_data, $cart_item );

		$item_data[ 'prices' ]->raw_prices[ 'price' ]         = self::prepare_money_response( WC_CP()->display->get_container_cart_item_price_amount( $cart_item, 'price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
		$item_data[ 'prices' ]->raw_prices[ 'regular_price' ] = self::prepare_money_response( WC_CP()->display->get_container_cart_item_price_amount( $cart_item, 'regular_price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
		$item_data[ 'prices' ]->raw_prices[ 'sale_price' ]    = self::prepare_money_response( WC_CP()->display->get_container_cart_item_price_amount( $cart_item, 'sale_price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );

		do_action( 'woocommerce_store_api_after_composite_aggregated_totals_calculation', $item_data, $cart_item );
	}

	/**
	 * Aggregates composite container item subtotals.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_totals( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'composite' ) ) {
			return;
		}

		if ( ! self::is_container_cart_item_subtotal_aggregated( $cart_item ) ) {
			return;
		}

		$decimals = isset( $item_data[ 'totals' ]->currency_minor_unit ) ? $item_data[ 'totals' ]->currency_minor_unit : wc_get_price_decimals();

		$item_data[ 'totals' ]->line_total        = self::prepare_money_response( WC_CP()->display->get_container_cart_item_subtotal_amount( $cart_item, 'total' ), $decimals );
		$item_data[ 'totals' ]->line_total_tax    = self::prepare_money_response( WC_CP()->display->get_container_cart_item_subtotal_amount( $cart_item, 'tax' ), $decimals );
		$item_data[ 'totals' ]->line_subtotal     = self::prepare_money_response( WC_CP()->display->get_container_cart_item_subtotal_amount( $cart_item, 'subtotal' ), $decimals );
		$item_data[ 'totals' ]->line_subtotal_tax = self::prepare_money_response( WC_CP()->display->get_container_cart_item_subtotal_amount( $cart_item, 'subtotal_tax' ), $decimals );
	}

	/**
	 * Adjust Composite container item quantity limits to keep max quantity limited by composited item stock.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_quantity_limits( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'composite' ) ) {
			return;
		}

		if ( $composited_cart_items = wc_cp_get_composited_cart_items( $cart_item ) ) {

			foreach ( $composited_cart_items as $composited_cart_item_key => $composited_cart_item ) {

				$component_id           = $composited_cart_item[ 'composite_item' ];
				$composite              = $cart_item[ 'data' ];
				$component              = $composite->get_component( $component_id );
				$min_component_quantity = $component->get_quantity( 'min' );
				$max_component_quantity = $component->get_quantity( 'max' );
				$composite_quantity     = $cart_item[ 'quantity' ];

				// Let's cache this now, as we'll need it later.
				WC_CP_Helpers::cache_set( 'component_quantity_limits_' . $composited_cart_item_key, array(
					'multiple_of' => $composite_quantity,
					'minimum'     => $composite_quantity * $min_component_quantity,
					'maximum'     => '' !== $max_component_quantity ? $composite_quantity * $max_component_quantity : false
				) );

				if ( $composited_cart_item[ 'data' ]->managing_stock() && ! $composited_cart_item[ 'data' ]->backorders_allowed() ) {

					$max_component_quantity = $composited_cart_item[ 'data' ]->get_stock_quantity();

					if ( $max_component_quantity > 0 && ! is_null( $max_component_quantity ) ) {
						// Limit container max quantity based on child item stock quantity.
						$item_data[ 'quantity_limits' ]->maximum = min( $item_data[ 'quantity_limits' ]->maximum, floor( $max_component_quantity / $composited_cart_item[ 'quantity' ] ) * $composite_quantity );
					}
				}
			}
		}
	}

	/**
	 * Filter container cart item permalink to support cart editing.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_permalink( &$item_data, $cart_item ) {

		if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {

			$composite = $cart_item[ 'data' ];

			if ( ! $composite->is_type( 'composite' ) ) {
				return;
			}

			$item_data[ 'permalink' ] = add_query_arg( array( 'quantity' => $cart_item[ 'quantity' ] ), $composite->get_permalink( $cart_item ) );

			if ( $composite->is_editable_in_cart( $cart_item ) ) {

				$trimmed_short_description = '';

				if ( $item_data[ 'short_description' ] ) {
					$trimmed_short_description = '<p class="wc-block-components-product-metadata__description-text">' . wp_trim_words( $item_data[ 'short_description' ], 12 ) . '</p>';
				}

				$edit_link                        = esc_url( add_query_arg( array( 'update-composite' => $cart_item[ 'key' ] ), $item_data[ 'permalink' ] ) );
				$item_data[ 'short_description' ] = '<p class="wc-block-cart-item__edit"><a class="wc-block-cart-item__edit-link" href="' . $edit_link . '">' . __( 'Edit item', 'woocommerce-composite-products' ) . '</a></p>' . $trimmed_short_description;
			}
		}
	}

	/**
	 * Adjust composited item quantity limits to account for min/max quantity settings and parent quantity.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_composited_cart_item_quantity_limits( &$item_data, $cart_item ) {

		$composited_cart_item_key  = $cart_item[ 'key' ];
		$component_quantity_limits = WC_CP_Helpers::cache_get( 'component_quantity_limits_' . $composited_cart_item_key );

		if ( is_null( $component_quantity_limits ) ) {
			return;
		}

		$step = $item_data[ 'quantity_limits' ]->multiple_of;
		$min  = $component_quantity_limits[ 'minimum' ];
		$max  = $component_quantity_limits[ 'maximum' ];

		$item_data[ 'quantity_limits' ]->multiple_of = $step * $component_quantity_limits[ 'multiple_of' ];
		$item_data[ 'quantity_limits' ]->minimum     = $min;

		if ( $max ) {
			// Limit child item max quantity.
			$item_data[ 'quantity_limits' ]->maximum = min( $item_data[ 'quantity_limits' ]->maximum, $max );
			$item_data[ 'quantity_limits' ]->maximum = floor( $item_data[ 'quantity_limits' ]->maximum / $step ) * $step;
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

	/**
	 * Controls whether composite container cart item prices include the prices of their components.
	 *
	 * @param  array  $cart_item
	 * @return boolean
	 */
	public static function is_container_cart_item_price_aggregated( $cart_item ) {
		return apply_filters( 'woocommerce_add_composited_cart_item_prices', true, $cart_item, $cart_item[ 'key' ] );
	}

	/**
	 * Controls whether composite container cart item subtotals include the subtotals of their components.
	 *
	 * @param  array  $cart_item
	 * @return boolean
	 */
	public static function is_container_cart_item_subtotal_aggregated( $cart_item ) {
		return apply_filters( 'woocommerce_add_composited_cart_item_subtotals', true, $cart_item, $cart_item[ 'key' ] );
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter store API responses to:
	 *
	 * - aggregate composite container prices/subtotals;
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
			 * @see https://github.com/woocommerce/woocommerce-composite-products/issues/886
			 * @see https://github.com/woocommerce/woocommerce-blocks/issues/7275
			 */
			$item_data[ 'quantity_limits' ] = (object) $item_data[ 'quantity_limits' ];
			$item_data[ 'prices' ]          = (object) $item_data[ 'prices' ];
			$item_data[ 'totals' ]          = (object) $item_data[ 'totals' ];
			$item_data[ 'extensions' ]      = (object) $item_data[ 'extensions' ];

			if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {

				self::filter_container_cart_item_prices( $item_data, $cart_item );
				self::filter_container_cart_item_totals( $item_data, $cart_item );
				self::filter_container_cart_item_quantity_limits( $item_data, $cart_item );
				self::filter_container_cart_item_permalink( $item_data, $cart_item );

			} elseif ( wc_cp_is_composited_cart_item( $cart_item ) ) {

				self::filter_composited_cart_item_quantity_limits( $item_data, $cart_item );
			}
		}

		$response->set_data( $data );

		return $response;
	}

	/**
	 * Validate composite in Store API context.
	 *
	 * @throws RouteException
	 *
	 * @param  array       $data
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function validate_cart_item( $composite, $cart_item ) {

		if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {
			try {
				WC_CP()->cart->validate_composite_configuration( $cart_item[ 'data' ], $cart_item[ 'quantity' ], $cart_item[ 'composite_data' ], 'cart' );
			} catch ( Exception $e ) {
				$notice = $e->getMessage();
				throw new RouteException( 'woocommerce_store_api_invalid_composite_configuration', $notice );
			}
		}
	}

	/**
	 * Prevents access to the checkout block if a composite in the cart is misconfigured.
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
	 * Prevent removal of mandatory components.
	 *
	 * @throws RouteException
	 *
	 * @param  string   $cart_item_key
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public static function validate_mandatory_composited_cart_item_removal( $cart_item_key, $cart ) {

		if ( ! WC_CP_Core_Compatibility::is_store_api_request( 'cart/remove-item' ) ) {
			return;
		}

		$notice = __( 'This product is a mandatory part of a composite product and cannot be removed.', 'woocommerce-composite-products' );
		throw new RouteException( 'woocommerce_store_api_mandatory_composited_item', $notice );
	}
}
