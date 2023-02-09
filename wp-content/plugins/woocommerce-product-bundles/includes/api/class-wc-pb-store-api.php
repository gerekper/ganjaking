<?php
/**
 * WC_PB_Store_API class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.15.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * Extends the store public API with bundle related data for each bundle parent and child item.
 *
 * @version 6.17.2
 */
class WC_PB_Store_API {

	/**
	 * Stores the cart item key of the last bundled item.
	 *
	 * @var string
	 */
	private static $last_bundled_item_key;

	/**
	 * Stores the cart item key of the last bundled item.
	 *
	 * @var string
	 */
	private static $faked_parent_bundled_item_key;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'bundles';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		self::extend_store();

		// Aggregate cart item prices/subtotals and filter min/max/multipleof quantities.
		add_filter( 'rest_request_after_callbacks', array( __CLASS__, 'filter_cart_item_data' ), 10, 3 );

		// Validate bundles in the Store API and add cart errors.
		add_action( 'woocommerce_store_api_validate_cart_item', array( __CLASS__, 'validate_cart_item' ), 10, 2 );

		// Prevent access to the checkout block.
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( __CLASS__, 'validate_draft_order' ) );

		// Validate removal of mandatory bundled items.
		add_action( 'woocommerce_remove_mandatory_bundled_cart_item', array( __CLASS__, 'validate_mandatory_bundled_cart_item_removal' ), 10, 2 );
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
	 * @param array  $cart_item
	 * @return array $item_data
	 */
	public static function extend_cart_item_data( $cart_item ) {

		$item_data = array();

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			if ( ! $cart_item[ 'data' ]->is_type( 'bundle' ) ) {
				return $item_data;
			}

			$bundle = $cart_item[ 'data' ];

			// Reset last item key.
			self::$last_bundled_item_key = false;
			// Reset faked parent item key.
			self::$faked_parent_bundled_item_key = false;

			$is_price_visible        = true;
			$is_subtotal_visible     = true;
			$is_visible              = true;
			$is_cart_meta_visible    = false;
			$is_summary_meta_visible = false;

			$bundled_cart_items     = wc_pb_get_bundled_cart_items( $cart_item );
			$bundled_cart_item_keys = array_keys( $bundled_cart_items );

			// Hide entire item?
			if ( false === WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_item' ) ) {
				$is_visible = false;
			}

			// Find and store last item.
			if ( $bundled_cart_items ) {
				foreach ( $bundled_cart_items as $bundled_cart_item_key => $bundled_cart_item ) {

					$bundled_item = $bundle->get_bundled_item( $bundled_cart_item[ 'bundled_item_id' ] );

					if ( $bundled_item && $bundled_item->is_visible( 'cart' ) ) {
						self::$last_bundled_item_key = $bundled_cart_item_key;
					}

					if ( $bundled_cart_item_keys[ 0 ] === $bundled_cart_item_key && WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'faked_parent_item' ) ) {
						self::$faked_parent_bundled_item_key = $bundled_cart_item_key;
					}
				}
			}

			// Hide price?
			if ( empty( $cart_item[ 'data' ]->get_price() ) && false === WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'aggregated_prices' ) && WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'component_multiselect' ) ) {
				$is_price_visible = false;
			}

			// Hide subtotal?
			if ( empty( $cart_item[ 'line_subtotal' ] ) && false === WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'aggregated_subtotals' ) && WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'component_multiselect' ) ) {
				$is_subtotal_visible = false;
			}

			// Is bundled item meta visible?
			if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_cart_item_meta' ) ) {
				$is_cart_meta_visible    = true;
				$is_summary_meta_visible = true;
			}

			if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_item' ) && WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'child_item_indent' ) ) {
				$is_summary_meta_visible = true;
			}

			$item_data[ 'bundled_items' ] = $cart_item[ 'bundled_items' ];
			$item_data[ 'bundle_data' ]   = array(
				'configuration'             => $cart_item[ 'stamp' ],
				'is_editable'               => $bundle->is_editable_in_cart(),
				'is_price_hidden'           => ! $is_price_visible,
				'is_subtotal_hidden'        => ! $is_subtotal_visible,
				'is_hidden'                 => ! $is_visible,
				'is_meta_hidden_in_cart'    => ! $is_cart_meta_visible,
				'is_meta_hidden_in_summary' => ! $is_summary_meta_visible
			);

		} elseif ( $container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundle = $container_item[ 'data' ];

			if ( ! $bundle->is_type( 'bundle' ) ) {
				return $item_data;
			}

			$bundled_item = $bundle->get_bundled_item( $cart_item[ 'bundled_item_id' ] );

			if ( ! $bundled_item ) {
				return $item_data;
			}

			$is_indented                  = false;
			$is_price_hidden              = false;
			$is_subtotal_hidden           = false;
			$is_thumbnail_hidden          = false;
			$is_visible                   = true;
			$is_last                      = self::$last_bundled_item_key === $cart_item[ 'key' ];
			$is_faked_parent_item         = self::$faked_parent_bundled_item_key === $cart_item[ 'key' ];
			$is_parent_visible            = false;
			$is_removable                 = false;
			$is_subtotal_aggregated       = false;
			$has_parent_with_cart_meta    = false;
			$has_parent_with_summary_meta = false;

			if ( ! $is_faked_parent_item  ) {

				// Hide thumbnail?
				if ( false === $bundled_item->is_thumbnail_visible() ) {
					$is_thumbnail_hidden = true;
				}

				// Hide entire item?
				if ( false === $bundled_item->is_visible( 'cart' ) ) {
					$is_visible = false;
				}

				// Indent item?
				if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'child_item_indent' ) ) {
					$is_indented                  = true;
					$has_parent_with_summary_meta = true;
				}

				// Parent item has meta?
				if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_cart_item_meta' ) ) {
					$has_parent_with_cart_meta    = true;
					$has_parent_with_summary_meta = true;
				}

				// Removable?
				if ( $bundled_item->is_optional() || $bundled_item->get_quantity( 'min' ) === 0 ) {
					$is_removable = true;
				}

				// Removes parent?
				$is_parent_visible = WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_item' );
				if ( ! $is_removable && ! $is_parent_visible ) {
					$is_removable = true;
				}
			}

			// Aggregate subtotals?
			if ( WC_Product_Bundle::group_mode_has( $container_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' ) ) {
				$is_subtotal_aggregated = true;
			}

			// Hide price/subtotal?
			if ( false === $bundled_item->is_priced_individually() ) {

				$is_price_hidden    = WC_Product_Bundle::group_mode_has( $container_item[ 'data' ]->get_group_mode(), 'aggregated_prices' ) && empty( $cart_item[ 'data' ]->get_price() );
				$is_subtotal_hidden = WC_Product_Bundle::group_mode_has( $container_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' ) && empty( $cart_item[ 'line_subtotal' ] );

			} elseif ( false === $bundled_item->is_price_visible( 'cart' ) ) {

				$is_price_hidden = $is_subtotal_hidden = true;
			}

			if ( empty( $cart_item[ 'data' ]->get_price() ) ) {
				$is_price_hidden = true;
			}

			if ( empty( $cart_item[ 'line_subtotal' ] ) ) {
				$is_subtotal_hidden = true;
			}

			$item_data[ 'bundled_by' ]        = $cart_item[ 'bundled_by' ];
			$item_data[ 'bundled_item_data' ] = array(
				'bundle_id'              => $container_item[ 'product_id' ],
				'bundled_item_id'        => $cart_item[ 'bundled_item_id' ],
				'is_removable'           => $is_removable,
				'is_indented'            => $is_indented,
				'is_subtotal_aggregated' => $is_subtotal_aggregated,
				'is_parent_visible'      => $is_parent_visible,
				'is_last'                => $is_last,
				'is_price_hidden'        => $is_price_hidden,
				'is_subtotal_hidden'     => $is_subtotal_hidden,
				'is_thumbnail_hidden'    => $is_thumbnail_hidden,
				'is_hidden_in_cart'      => ! $is_visible || $has_parent_with_cart_meta,
				'is_hidden_in_summary'   => ! $is_visible || $has_parent_with_summary_meta
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
			'bundled_by'           => array(
				'description' => __( 'Cart item key of bundle that contains this item.', 'woocommerce-product-bundles' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'bundled_items'        => array(
				'description' => __( 'List of cart item keys grouped by this bundle.', 'woocommerce-product-bundles' ),
				'type'        => array( 'array', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'bundle_data'          => array(
				'description' => __( 'Bundle data.', 'woocommerce-product-bundles' ),
				'type'        => array( 'object', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'bundled_item_data'    => array(
				'description' => __( 'ID of this bundled item.', 'woocommerce-product-bundles' ),
				'type'        => array( 'object', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			)
		);
	}

	/**
	 * Aggregates bundle container item prices.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_prices( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'bundle' ) ) {
			return;
		}

		if ( ! WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_prices' ) ) {
			return;
		}

		$item_data[ 'prices' ]->raw_prices[ 'price' ]         = self::prepare_money_response( WC_PB()->display->get_container_cart_item_price_amount( $cart_item, 'price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
		$item_data[ 'prices' ]->raw_prices[ 'regular_price' ] = self::prepare_money_response( WC_PB()->display->get_container_cart_item_price_amount( $cart_item, 'regular_price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
		$item_data[ 'prices' ]->raw_prices[ 'sale_price' ]    = self::prepare_money_response( WC_PB()->display->get_container_cart_item_price_amount( $cart_item, 'sale_price' ), wc_get_rounding_precision(), PHP_ROUND_HALF_UP );
	}

	/**
	 * Aggregates bundle container item subtotals.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_totals( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'bundle' ) ) {
			return;
		}

		if ( ! WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' ) ) {
			return;
		}

		$item_data[ 'totals' ]->line_total        = self::prepare_money_response( WC_PB()->display->get_container_cart_item_subtotal_amount( $cart_item, 'total' ) );
		$item_data[ 'totals' ]->line_total_tax    = self::prepare_money_response( WC_PB()->display->get_container_cart_item_subtotal_amount( $cart_item, 'tax' ) );
		$item_data[ 'totals' ]->line_subtotal     = self::prepare_money_response( WC_PB()->display->get_container_cart_item_subtotal_amount( $cart_item, 'subtotal' ) );
		$item_data[ 'totals' ]->line_subtotal_tax = self::prepare_money_response( WC_PB()->display->get_container_cart_item_subtotal_amount( $cart_item, 'subtotal_tax' ) );
	}

	/**
	 * Adjust bundle container item quantity limits to keep max quantity limited by bundled item stock.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_quantity_limits( &$item_data, $cart_item ) {

		if ( ! $cart_item[ 'data' ]->is_type( 'bundle' ) ) {
			return;
		}

		if ( $bundled_cart_items = wc_pb_get_bundled_cart_items( $cart_item ) ) {

			foreach ( $bundled_cart_items as $bundled_cart_item_key => $bundled_cart_item ) {

				$bundled_item_id = $bundled_cart_item[ 'bundled_item_id' ];
				$bundled_item    = $cart_item[ 'data' ]->get_bundled_item( $bundled_item_id );

				$min_bundled_item_quantity = $bundled_item->get_quantity( 'min' );
				$max_bundled_item_quantity = $bundled_item->get_quantity( 'max' );

				$bundle_quantity = $cart_item[ 'quantity' ];

				// Let's cache this now, as we'll need it later.
				WC_PB_Helpers::cache_set( 'bundled_item_quantity_limits_' . $bundled_cart_item_key, array(
					'multiple_of' => $bundle_quantity,
					'minimum'     => $bundle_quantity * $min_bundled_item_quantity,
					'maximum'     => '' !== $max_bundled_item_quantity ? $bundle_quantity * $max_bundled_item_quantity : false
				) );

				if ( $bundled_cart_item[ 'data' ]->managing_stock() && ! $bundled_cart_item[ 'data' ]->backorders_allowed() ) {

					$max_bundled_item_stock_quantity = $bundled_cart_item[ 'data' ]->get_stock_quantity();

					if ( $max_bundled_item_stock_quantity > 0 && ! is_null( $max_bundled_item_stock_quantity ) ) {
						// Limit container max quantity based on child item stock quantity.
						$item_data[ 'quantity_limits' ]->maximum = min( $item_data[ 'quantity_limits' ]->maximum, floor( $max_bundled_item_stock_quantity / $bundled_cart_item[ 'quantity' ] ) * $bundle_quantity );
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

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			$bundle = $cart_item[ 'data' ];

			if ( ! $bundle->is_type( 'bundle' ) ) {
				return;
			}

			$item_data[ 'permalink' ] = add_query_arg( array( 'quantity' => $cart_item[ 'quantity' ] ), $bundle->get_permalink( $cart_item ) );

			if ( $bundle->is_editable_in_cart( $cart_item ) ) {

				$trimmed_short_description = '';

				if ( $item_data[ 'short_description' ] ) {
					$trimmed_short_description = '<p class="wc-block-components-product-metadata__description-text">' . wp_trim_words( $item_data[ 'short_description' ], 12 ) . '</p>';
				}

				$edit_link                        = esc_url( add_query_arg( array( 'update-bundle' => $cart_item[ 'key' ] ), $item_data[ 'permalink' ] ) );
				$item_data[ 'short_description' ] = '<p class="wc-block-cart-item__edit"><a class="wc-block-cart-item__edit-link" href="' . $edit_link . '">' . __( 'Edit item', 'woocommerce-product-bundles' ) . '</a></p>' . $trimmed_short_description;
			}
		}
	}

	/**
	 * Adjust bundled item quantity limits to account for min/max quantity settings and parent quantity.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_bundled_cart_item_quantity_limits( &$item_data, $cart_item ) {

		$bundled_cart_item_key        = $cart_item[ 'key' ];
		$bundled_item_quantity_limits = WC_PB_Helpers::cache_get( 'bundled_item_quantity_limits_' . $bundled_cart_item_key );

		if ( is_null( $bundled_item_quantity_limits ) ) {
			return;
		}

		$step = $item_data[ 'quantity_limits' ]->multiple_of;
		$min  = $bundled_item_quantity_limits[ 'minimum' ];
		$max  = $bundled_item_quantity_limits[ 'maximum' ];

		$item_data[ 'quantity_limits' ]->multiple_of = $step * $bundled_item_quantity_limits[ 'multiple_of' ];
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

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter store API responses to:
	 *
	 * - aggregate bundle container prices/subtotals;
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

			if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

				self::filter_container_cart_item_prices( $item_data, $cart_item );
				self::filter_container_cart_item_totals( $item_data, $cart_item );
				self::filter_container_cart_item_quantity_limits( $item_data, $cart_item );
				self::filter_container_cart_item_permalink( $item_data, $cart_item );

			} elseif ( wc_pb_is_bundled_cart_item( $cart_item ) ) {

				self::filter_bundled_cart_item_quantity_limits( $item_data, $cart_item );
			}
		}

		$response->set_data( $data );

		return $response;
	}

	/**
	 * Validate bundle in Store API context.
	 *
	 * @throws RouteException
	 *
	 * @param  array       $data
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function validate_cart_item( $bundle, $cart_item ) {

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
			try {
				WC_PB()->cart->validate_bundle_configuration( $cart_item[ 'data' ], $cart_item[ 'quantity' ], $cart_item[ 'stamp' ], 'cart' );
			} catch ( Exception $e ) {
				$notice = $e->getMessage();
				throw new RouteException( 'woocommerce_store_api_invalid_bundle_configuration', $notice );
			}
		}
	}

	/**
	 * Prevents access to the checkout block if a bundle in the cart is misconfigured.
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
	 * Prevent removal of mandatory bundled items with a visible parent.
	 *
	 * @throws RouteException
	 *
	 * @param  string   $cart_item_key
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public static function validate_mandatory_bundled_cart_item_removal( $cart_item_key, $cart ) {

		if ( ! WC_PB_Core_Compatibility::is_store_api_request( 'cart/remove-item' ) ) {
			return;
		}

		$notice = __( 'This product is a mandatory part of a bundle and cannot be removed.', 'woocommerce-product-bundles' );
		throw new RouteException( 'woocommerce_store_api_mandatory_bundled_item', $notice );
	}
}
