<?php
/**
 * Extends the store public API with container related data for each container parent and child item.
 *
 * @package  WooCommerce Free Gift Coupons/REST API
 * @since    3.4.0
 * @version  3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * WC_FGC_Store_API class
 */
class WC_FGC_Store_API {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'free_gift_coupons';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		self::extend_store();

		// Aggregate cart item prices/subtotals and filter min/max/multipleof quantities.
		add_filter( 'rest_request_after_callbacks', array( __CLASS__, 'filter_cart_item_data' ), 10, 3 );

		// Remove quantity selectors from gift items.
		add_filter( 'woocommerce_store_api_product_quantity_editable', array( __CLASS__, 'product_quantity_editable' ), 10, 3 );

		// Validate container in the Store API and add cart errors.
		add_action( 'woocommerce_store_api_validate_cart_item', array( __CLASS__, 'validate_cart_item' ), 10, 2 );

		// Prevent access to the checkout block.
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( __CLASS__, 'validate_draft_order' ) );

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

		if ( ! isset( $cart_item[ 'free_gift' ] ) ) {
			return $item_data;
		}

		$item_data[ 'free_gift' ]                   = $cart_item[ 'free_gift' ];
		$item_data[ 'fgc_quantity' ]                = $cart_item[ 'fgc_quantity' ];
		$item_data[ 'fgc_edit_in_cart' ]            = $cart_item[ 'fgc_edit_in_cart' ];
		$item_data[ 'fgc_pre_selected_attributes' ] = $cart_item[ 'fgc_pre_selected_attributes' ];

		return $item_data;
	}

	/**
	 * Register subscription product schema into cart/items endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_item_schema() {
		return array(
			'free_gift'           => array(
				'description' => __( 'Coupon code associated with this free gift product.', 'wc_free_gift_coupons', 'woocommerce-free-gift-coupons' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'fgc_quantity'        => array(
				'description' => __( 'Quantity of free gift product.', 'wc_free_gift_coupons', 'woocommerce-free-gift-coupons' ),
				'type'        => array( 'integer', 'null' ),
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'fgc_edit_in_cart'          => array(
				'description' => __( 'Whether gift product can be edited in cart.', 'wc_free_gift_coupons', 'woocommerce-free-gift-coupons' ),
				'type'        => array( 'boolean', 'null' ),
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'fgc_pre_selected_attributes'    => array(
				'description' => __( 'Pre-selected attributes of "any" variation gift.', 'wc_free_gift_coupons', 'woocommerce-free-gift-coupons' ),
				'type'        => array( 'array', 'null' ),
				'context'     => array( 'view' ),
				'readonly'    => true,
			)
			
		);
	}

	/**
	 * Modify item price.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_free_gift_cart_item_prices( &$item_data, $cart_item ) {

		if ( ! isset( $cart_item[ 'free_gift' ] ) ) {
			return;
		}

		$item_data[ 'prices' ]->raw_prices[ 'price' ]         = self::prepare_money_response( 0 );
		$item_data[ 'prices' ]->raw_prices[ 'regular_price' ] = self::prepare_money_response( 0 );
		$item_data[ 'prices' ]->raw_prices[ 'sale_price' ]    = self::prepare_money_response( 0 );
	}

	/**
	 * Modify item subtotals.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_free_gift_cart_item_totals( &$item_data, $cart_item ) {

		if ( ! isset( $cart_item[ 'free_gift' ] ) ) {
			return;
		}

		$item_data[ 'totals' ]->line_total        = self::prepare_money_response( 0 );
		$item_data[ 'totals' ]->line_total_tax    = self::prepare_money_response( 0 );
		$item_data[ 'totals' ]->line_subtotal     = self::prepare_money_response( 0 );
		$item_data[ 'totals' ]->line_subtotal_tax = self::prepare_money_response( 0 );
	}

	/**
	 * Adjust container item quantity limits to prevent child items from being edited.
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_free_gift_cart_item_quantity_limits( &$item_data, $cart_item ) {

		if ( ! isset( $cart_item[ 'free_gift' ] ) ) {
			return;
		}

		$item_data[ 'quantity_limits' ]->multiple_of = $cart_item['quantity'];
		$item_data[ 'quantity_limits' ]->minimum     = $cart_item['quantity'];
		$item_data[ 'quantity_limits' ]->maximum     = $cart_item['quantity'];

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

			if ( isset( $cart_item[ 'free_gift' ] ) ) {

				self::filter_free_gift_cart_item_prices( $item_data, $cart_item );
				self::filter_free_gift_cart_item_totals( $item_data, $cart_item );
				self::filter_free_gift_cart_item_quantity_limits( $item_data, $cart_item );
				self::filter_container_cart_item_short_description( $item_data, $cart_item );

			}
		}

		$response->set_data( $data );

		return $response;
	}

	/**
	 * Remove quantity inputs from gift items in Store API context.
	 * 
	 * @param bool $qty_is_editable
	 * @param  WC_Product  $product
	 * @param  array       $cart_item
	 * @return false
	 */
	public static function product_quantity_editable( $qty_is_editable, $product, $cart_item ) {
		if ( isset( $cart_item[ 'free_gift' ] ) ) {
			$qty_is_editable = false;
		}
		return $qty_is_editable;
	}


	/**
	 * Validate container in Store API context. @todo check if coupon is still valid??
	 *
	 * @throws RouteException
	 *
	 * @param  WC_Product  $product
	 * @param  array       $cart_item
	 */
	public static function validate_cart_item( $product, $cart_item ) {

		if ( isset( $cart_item[ 'free_gift' ] ) ) {
			try {

				$cart_coupons = (array) wc()->cart->get_applied_coupons();

				if ( ! in_array( $cart_item['free_gift'], $cart_coupons, true ) ) {

					wc()->cart->set_quantity( $cart_item['key'], 0 );

					$error = esc_html__( 'A gift item which is no longer available was removed from your cart.' ); 

					throw new Exception( $error );
				}

				//get_cart_coupons();
			} catch ( Exception $e ) {
				$notice = $e->getMessage();
				throw new RouteException( 'woocommerce_store_api_missing_gift_coupon_for_product', $notice );
			}
		}

	}

	/**
	 * Prevents access to the checkout block if a product in the cart is misconfigured.
	 *
	 * @throws RouteException
	 *
	 * @param  WC_Order  $order
	 * @return array
	 */
	public static function validate_draft_order( $order ) {

		foreach ( wc()->cart->cart_contents as $cart_item_key => $cart_item ) {
			self::validate_cart_item( $cart_item[ 'data' ], $cart_item );
		}
	}

	/**
	 * Filter cart item short description to support cart editing.
	 *
	 * @since 3.6.0
	 *
	 * @param array  $item_data
	 * @param array  $cart_item
	 */
	private static function filter_container_cart_item_short_description( &$item_data, $cart_item ) {

		if ( empty( $cart_item[ 'free_gift' ] ) || empty( $cart_item[ 'fgc_edit_in_cart' ] ) ) {
			return $item_data;
		}

		$test = $cart_item;
		unset($test['data']);
		error_log(json_encode($test));

		$trimmed_short_description = '';

		if ( $item_data['short_description'] ) {
			$trimmed_short_description = '<p class="wc-block-components-product-metadata__description-text">' . wp_trim_words( $item_data['short_description'], 12 ) . '</p>';
		}

		// Generate edit button text.
		// If variation is selected, we are in an edit mode.
		if ( $cart_item['variation_id'] > 0 && ! WC_FGC_Update_Variation_Cart::has_any_variation( $cart_item['variation'] ) ) {
			// translators: %1$s Screen reader text opening <span> %2$s Product title %3$s Closing </span>
			$edit_in_cart_text = sprintf( esc_html_x( 'Edit options %1$sfor %2$s%3$s', 'edit in cart link text', 'wc_free_gift_coupons' ),
				'<span class="screen-reader-text">',
				$cart_item['data']->get_title(),
				'</span>'
			);

		} else {
			// translators: %1$s Screen reader text opening <span> %2$s Product title %3$s Closing </span>
			$edit_in_cart_text      = sprintf( esc_html_x( 'Choose options %1$sfor %2$s%3$s', 'edit in cart link text', 'wc_free_gift_coupons' ),
				'<span class="screen-reader-text">',
				$cart_item['data']->get_title(),
				'</span>'
			);


		}

		// Get link until we can reactify the variation selection in the cart block.
		$edit_link = add_query_arg(
			array(
				'update-gift'  => $cart_item['key']
			),
			$cart_item['data']->get_permalink( $cart_item )
		);

		// Add button to end of short description response.
		$item_data['short_description'] = '<p class="wc-block-cart-item__edit"><a href="' . esc_url( $edit_link ) . '" class="components-button wc-fgc-cart-item__edit-link wc-block-components-button wp-element-button outlined contained"><span class="wc-block-components-button__text">' . wp_kses_post( $edit_in_cart_text ) . '</span></a></p>' . $trimmed_short_description;

	}

}
