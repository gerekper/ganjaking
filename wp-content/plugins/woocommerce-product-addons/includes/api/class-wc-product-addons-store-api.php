<?php
/**
 * WC_Product_Addons_Store_API class
 *
 * @package WC_Product_Addons/Classes/Store_API
 * @since   6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * Extends the store public API with add-ons related data.
 *
 * @version 6.4.4
 */
class WC_Product_Addons_Store_API {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'addons';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		self::extend_store();

		// Aggregate cart item prices/subtotals and filter min/max/multipleof quantities.
		add_filter( 'rest_request_after_callbacks', array( __CLASS__, 'filter_cart_item_data' ), 20, 3 );

		// Bundles.
		add_action( 'woocommerce_store_api_before_bundle_aggregated_totals_calculation', array( __CLASS__, 'remove_flat_fees_from_aggregated_container_prices' ), -PHP_INT_MAX, 2 );
		add_action( 'woocommerce_store_api_after_bundle_aggregated_totals_calculation', array( __CLASS__, 'add_flat_fees_to_aggregated_container_prices' ), +PHP_INT_MAX, 2 );

		// Composites.
		add_action( 'woocommerce_store_api_before_composite_aggregated_totals_calculation', array( __CLASS__, 'remove_flat_fees_from_aggregated_container_prices' ), -PHP_INT_MAX, 2 );
		add_action( 'woocommerce_store_api_after_composite_aggregated_totals_calculation', array( __CLASS__, 'add_flat_fees_to_aggregated_container_prices' ), +PHP_INT_MAX, 2 );
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

		if ( ! isset( $cart_item[ 'addons' ] ) || empty( $cart_item[ 'addons' ] ) ) {
			return $item_data;
		}

		if ( ! isset( $cart_item[ 'addons_flat_fees_sum' ] ) || empty( $cart_item[ 'addons_flat_fees_sum' ] ) ) {
			return $item_data;
		}

		$item_data[ 'addons_data' ] = array(
			'addons_flat_fees_sum' => $cart_item[ 'addons_flat_fees_sum' ]
		);

		return $item_data;
	}


	/**
	 * Register subscription product schema into cart/items endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_item_schema() {
		return array(
			'addons_data'       => array(
				'description' => __( 'Addons data.', 'woocommerce-product-addons' ),
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
	private static function filter_cart_item_prices( &$item_data, $cart_item ) {

		if ( ! isset( $item_data[ 'extensions' ]->addons ) || empty( $item_data[ 'extensions' ]->addons ) ) {
			return;
		}

		// Product Bundles handles aggregated prices internally.
		$is_bundle                    = $cart_item[ 'data' ]->is_type( 'bundle' );
		$has_bundle_aggregated_prices = $is_bundle && class_exists( 'WC_Product_Bundle' ) && WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' );

		if ( $has_bundle_aggregated_prices ) {
			return;
		}

		// Composite Products handles aggregated prices internally.
		$is_composite                    = $cart_item[ 'data' ]->is_type( 'composite' );
		$has_composite_aggregated_prices = $is_composite && class_exists( 'WC_CP_Store_API' ) && WC_CP_Store_API::is_container_cart_item_price_aggregated( $cart_item );

		if ( $has_composite_aggregated_prices ) {
			return;
		}

		self::remove_flat_fees_from_cart_item_price( $item_data, $cart_item );

		$product_price         = $cart_item[ 'data' ]->get_price();
		$product_regular_price = $cart_item[ 'data' ]->get_regular_price();
		$product_sale_price    = $cart_item[ 'data' ]->get_sale_price();

		$item_data[ 'prices' ]->raw_prices[ 'price' ]         = self::prepare_money_response( WC_Product_Addons_Helper::get_product_addon_price_for_display( $product_price, $cart_item[ 'data' ] ), wc_get_rounding_precision() );
		$item_data[ 'prices' ]->raw_prices[ 'regular_price' ] = self::prepare_money_response( WC_Product_Addons_Helper::get_product_addon_price_for_display( $product_regular_price, $cart_item[ 'data' ] ), wc_get_rounding_precision() );
		$item_data[ 'prices' ]->raw_prices[ 'sale_price' ]    = self::prepare_money_response( WC_Product_Addons_Helper::get_product_addon_price_for_display( $product_sale_price, $cart_item[ 'data' ] ), wc_get_rounding_precision() );
	}

	/**
	 * Removes flat fee add-ons from product prices in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 */
	public static function remove_flat_fees_from_cart_item_price( &$item_data, $cart_item ) {

		if ( ! isset( $item_data[ 'extensions' ]->addons ) || empty( $item_data[ 'extensions' ]->addons ) ) {
			return;
		}

		$flat_fees = $item_data[ 'extensions' ]->addons[ 'addons_data' ][ 'addons_flat_fees_sum' ];

		// Composite Products compatibility: remove flat fees from the price offset that Composite Products uses to calculate discounted prices.
		if ( isset( $cart_item[ 'data' ]->composited_price_offset ) ) {

			$cart_item[ 'data' ]->composited_price_offset -= $flat_fees;

		// Product Bundles compatibility: remove flat fees from the price offset that Product Bundles uses to calculate discounted prices.
		} elseif ( isset( $cart_item[ 'data' ]->bundled_price_offset ) ) {

			$cart_item[ 'data' ]->bundled_price_offset -= $flat_fees;

		} else {
			// Get price data.
			$product_price         = $cart_item[ 'data' ]->get_price( 'edit' );
			$product_regular_price = $cart_item[ 'data' ]->get_regular_price( 'edit' );
			$product_sale_price    = $cart_item[ 'data' ]->get_sale_price( 'edit' );

			// Subtract flat fees from product prices and set new prices to the product object.
			$product_price = $product_price - $flat_fees;
			$cart_item[ 'data' ]->set_price( $product_price );

			if ( is_numeric( $product_regular_price ) ) {
				$product_regular_price = $product_regular_price - $flat_fees;
				$cart_item[ 'data' ]->set_regular_price( $product_regular_price );
			}

			if ( is_numeric( $product_sale_price ) ) {
				$product_sale_price = $product_sale_price - $flat_fees;
				$cart_item[ 'data' ]->set_sale_price( $product_sale_price );
			}
		}

		/**
		 * All Products for WooCommerce Subscriptions compatibility.
		 *
		 * If All Products for WooCommerce Subscriptions shouldn't discount add-ons, then remove flat fees from the price offset used to
		 * calculate discounts.
		 */
		if ( class_exists( 'WCS_ATT_Integration_PAO' ) && class_exists( 'WCS_ATT_Product' ) ) {
			if ( ! WCS_ATT_Integration_PAO::discount_addons( $cart_item[ 'data' ] ) ) {
				$runtime_meta = WCS_ATT_Product::get_runtime_meta( $cart_item[ 'data' ], 'price_offset' );
				if ( '' !== $runtime_meta ) {
					WCS_ATT_Product::set_runtime_meta( $cart_item[ 'data' ], 'price_offset', $runtime_meta - $cart_item[ 'addons_flat_fees_sum' ] );
				}
			}
		}
	}

	/**
	 * Re-adds flat fee add-ons from product prices in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 */
	public static function add_flat_fees_from_cart_item_price( &$item_data, $cart_item ) {

		if ( ! isset( $item_data[ 'extensions' ]->addons ) || empty( $item_data[ 'extensions' ]->addons ) ) {
			return;
		}

		$flat_fees = $item_data[ 'extensions' ]->addons[ 'addons_data' ][ 'addons_flat_fees_sum' ];

		// Composite Products compatibility: re-add flat fees to the price offset that Composite Products uses to calculate discounted prices.
		if ( isset( $cart_item[ 'data' ]->composited_price_offset ) ) {

			$cart_item[ 'data' ]->composited_price_offset += $flat_fees;

		// Product Bundles compatibility: re-add flat fees to the price offset that Product Bundles uses to calculate discounted prices.
		} elseif ( isset( $cart_item[ 'data' ]->bundled_price_offset ) ) {

			$cart_item[ 'data' ]->bundled_price_offset += $flat_fees;

		} else {

			// Get price data.
			$product_price         = $cart_item[ 'data' ]->get_price( 'edit' );
			$product_regular_price = $cart_item[ 'data' ]->get_regular_price( 'edit' );
			$product_sale_price    = $cart_item[ 'data' ]->get_sale_price( 'edit' );

			// Re-add flat fees to product prices and set new prices to the product object.
			$product_price = $product_price + $flat_fees;
			$cart_item[ 'data' ]->set_price( $product_price );

			if ( is_numeric( $product_regular_price ) ) {
				$product_regular_price = $product_regular_price + $flat_fees;
				$cart_item[ 'data' ]->set_regular_price( $product_regular_price );
			}

			if ( is_numeric( $product_sale_price ) ) {
				$product_sale_price = $product_sale_price + $flat_fees;
				$cart_item[ 'data' ]->set_sale_price( $product_sale_price );
			}
		}

		/**
		 * All Products for WooCommerce Subscriptions compatibility.
		 *
		 * If All Products for WooCommerce Subscriptions shouldn't discount add-ons, then remove flat fees from the price offset used to
		 * calculate discounts.
		 */
		if ( class_exists( 'WCS_ATT_Integration_PAO' ) && class_exists( 'WCS_ATT_Product' ) ) {
			if ( ! WCS_ATT_Integration_PAO::discount_addons( $cart_item[ 'data' ] ) ) {
				$runtime_meta = WCS_ATT_Product::get_runtime_meta( $cart_item[ 'data' ], 'price_offset' );
				if ( '' !== $runtime_meta ) {
					WCS_ATT_Product::set_runtime_meta( $cart_item[ 'data' ], 'price_offset', $runtime_meta + $cart_item[ 'addons_flat_fees_sum' ] );
				}
			}
		}
	}

	/**
	 * Removes flat fee add-ons from aggregated Bundle/Composite prices in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 */
	public static function remove_flat_fees_from_aggregated_container_prices( $item_data, $cart_item ) {
		self::remove_flat_fees_from_cart_item_price($item_data, $cart_item );
	}

	/**
	 * Re-adds flat fee add-ons to aggregated Bundle/Composite prices in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 */
	public static function add_flat_fees_to_aggregated_container_prices( $item_data, $cart_item ) {
		self::add_flat_fees_from_cart_item_price($item_data, $cart_item );
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
			$item_data[ 'prices' ] = (object) $item_data[ 'prices' ];

			self::filter_cart_item_prices( $item_data, $cart_item );
		}

		$response->set_data( $data );

		return $response;
	}

}
