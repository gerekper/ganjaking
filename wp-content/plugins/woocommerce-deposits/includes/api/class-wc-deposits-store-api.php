<?php
/**
 * A class to add custom data to the store API cart resource.
 *
 * @package WooCommerce Deposits
 * @since   1.6.0
 */

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * Store API integration class.
 *
 * @class    WC_Deposits_Store_API
 * @version  2.1.1
 */
class WC_Deposits_Store_API {

	/**
	 * Plugin identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'woocommerce-deposits';

	/**
	 * Class Instance
	 *
	 * @var WC_Deposits_Cart_Manager Class Instance
	 */
	private static $cart_manager;

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function initialize() {

		// Extend StoreAPI.
		self::extend_store();

		self::$cart_manager = WC_Deposits_Cart_Manager::get_instance();

		// Update prices according to deposits.
		add_filter( 'rest_request_after_callbacks', array( __CLASS__, 'filter_cart_item_data' ), 10, 3 );

		// Item data.
		add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'cart_item_data' ), 10, 2 );
	}

	/**
	 * Register cart data handler.
	 */
	public static function extend_store() {

		if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			return;
		}

		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => CartSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( __CLASS__, 'extend_cart_data' ),
				'schema_callback' => array( __CLASS__, 'extend_cart_schema' ),
				'schema_type'     => ARRAY_A,
			)
		);

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
	 * Adds extension data to cart route responses.
	 *
	 * @return array
	 */
	public static function extend_cart_data() {

		$cart_data = array(
			'has_deposit'              => false,
			'future_payment_amount'    => 0,
			'deferred_discount_amount' => 0,
			'deferred_discount_tax'    => 0,
		);

		if ( ! isset( WC()->cart ) ) {
			return $cart_data;
		}

		$has_deposit = self::$cart_manager->has_deposit( WC()->cart );
		if ( ! $has_deposit ) {
			return $cart_data;
		}

		$money_formatter       = woocommerce_store_api_get_formatter( 'money' );
		$deferred_discount_tax = 0;
		$is_tax_included       = wc_tax_enabled() && ( 'excl' !== WC()->cart->get_tax_price_display_mode() );
		$tax                   = self::$cart_manager::calculate_deferred_and_present_discount_tax();
		if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
			if ( $is_tax_included ) {
				$deferred_discount_tax = round( $tax['deferred'], wc_get_price_decimals() );
			}
		} else {
			if ( ! $is_tax_included ) {
				$deferred_discount_tax = -round( $tax['deferred'], wc_get_price_decimals() );
			}
		}

		$future_payment_amount    = self::$cart_manager->get_future_payments_amount_with_discount();
		$deferred_discount_amount = self::$cart_manager::get_deferred_discount_amount();

		$cart_data['has_deposit']              = $has_deposit;
		$cart_data['future_payment_amount']    = (int) $money_formatter->format( $future_payment_amount );
		$cart_data['deferred_discount_amount'] = (int) $money_formatter->format( $deferred_discount_amount );
		$cart_data['deferred_discount_tax']    = (int) $money_formatter->format( $deferred_discount_tax );

		return $cart_data;
	}

	/**
	 * Register schema into cart endpoint.
	 *
	 * @return  array  Registered schema.
	 */
	public static function extend_cart_schema() {

		return array(
			'has_deposit'              => array(
				'description' => __( 'True if the cart contains an item with deposit.', 'woocommerce-deposits' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'future_payment_amount'    => array(
				'description' => __( 'Amount to be paid in the future, after the deposit is paid.', 'woocommerce-deposits' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'deferred_discount_amount' => array(
				'description' => __( 'Future discounts, if a coupon is used.', 'woocommerce-deposits' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'deferred_discount_tax'    => array(
				'description' => __( 'Future discounts, if a coupon is used.', 'woocommerce-deposits' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);

	}

	/**
	 * Filter store API responses to:
	 *
	 * - filter item prices according to deposits;
	 *
	 * @param WP_REST_Response $response Response.
	 * @param WP_REST_Server   $server Server.
	 * @param WP_REST_Request  $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function filter_cart_item_data( $response, $server, $request ) {

		if ( is_wp_error( $response ) || strpos( $request->get_route(), 'wc/store' ) === false ) {
			return $response;
		}

		$data = $response->get_data();

		if (
			empty( $data['items'] )
			|| ! isset( WC()->cart )
			|| ! self::$cart_manager->has_deposit( WC()->cart )
		) {
			return $response;
		}

		$money_formatter = woocommerce_store_api_get_formatter( 'money' );
		$cart            = WC()->cart->get_cart();

		foreach ( $data['items'] as $key => $item_data ) {

			$cart_item_key = $item_data['key'];
			$cart_item     = isset( $cart[ $cart_item_key ] ) ? $cart[ $cart_item_key ] : null;

			if ( is_null( $cart_item ) || ! isset( $cart_item['full_amount'] ) || empty( $cart_item['is_deposit'] ) ) {
				continue;
			}

			/**
			 * StoreAPI returns the following fields as
			 * - object (/wc/store/v1/cart)
			 * - array (/wc/store/v1/cart/extensions)
			 *
			 * Casting them to objects, to avoid PHP8+ fatal errors.
			 *
			 * @see https://github.com/woocommerce/woocommerce-deposits/issues/478
			 * @see https://github.com/woocommerce/woocommerce-blocks/issues/7275
			 */
			$data['items'][ $key ]['quantity_limits'] = (object) $item_data['quantity_limits'];
			$data['items'][ $key ]['prices']          = (object) $item_data['prices'];
			$data['items'][ $key ]['totals']          = (object) $item_data['totals'];
			$data['items'][ $key ]['extensions']      = (object) $item_data['extensions'];

			// We need to apply this filter to the deposit amount, as it may have been affected by Memberships.
			$cart_item_deposit_amount     = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
			$deposit_amount_excluding_tax = wc_get_price_excluding_tax(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $cart_item_deposit_amount,
				)
			);

			if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
				$data['items'][ $key ]['totals']->line_subtotal     = $money_formatter->format( $deposit_amount_excluding_tax );
				$data['items'][ $key ]['totals']->line_subtotal_tax = 0;
			} else {
				$deposit_amount_including_tax = wc_get_price_including_tax(
					$cart_item['data'],
					array(
						'qty'   => $cart_item['quantity'],
						'price' => $cart_item_deposit_amount,
					)
				);

				$data['items'][ $key ]['totals']->line_subtotal     = $money_formatter->format( $deposit_amount_excluding_tax );
				$data['items'][ $key ]['totals']->line_subtotal_tax = $money_formatter->format( $deposit_amount_including_tax - $deposit_amount_excluding_tax );
			}
		}

		/**
		 * Let's calculate and set the cart taxes
		 *
		 * @see $wc_deposits_cart_manager_instance->cart_totals_taxes_total_html
		 */
		$cart_tax_totals = WC()->cart->get_tax_totals();
		$deferred_tax    = self::$cart_manager->calculate_deferred_taxes_from_cart( WC()->cart );
		if ( ! empty( $deferred_tax ) && ! empty( $cart_tax_totals ) ) {
			$tax = WC()->cart->get_taxes_total() - array_sum( self::$cart_manager->calculate_deferred_taxes_from_cart() );

			/**
			 * Casting to object, to avoid PHP8+ fatal errors.
			 *
			 * @see https://github.com/woocommerce/woocommerce-deposits/issues/478
			 * @see https://github.com/woocommerce/woocommerce-blocks/issues/7275
			 */
			$data['totals']            = (object) $data['totals'];
			$data['totals']->total_tax = $money_formatter->format( $tax );
		}

		$response->set_data( $data );

		return $response;
	}

	/**
	 * Register deposits product data into cart/items endpoint.
	 *
	 * @param  array $cart_item Current cart item data.
	 * @return array $item_data Registered deposits product data.
	 */
	public static function extend_cart_item_data( $cart_item ) {

		$item_data = array(
			'is_deposit'       => false,
			'has_payment_plan' => false,
			'plan_schedule'    => array(),
		);

		$item_data['is_deposit'] = ! empty( $cart_item['is_deposit'] );

		if ( $item_data['is_deposit'] && $cart_item['payment_plan'] ) {
			$plan     = new WC_Deposits_Plan( $cart_item['payment_plan'] );
			$schedule = $plan->get_schedule();

			foreach ( $schedule as $schedule_row ) {
				$item_data['plan_schedule'][] = $schedule_row;
			}
			$item_data['has_payment_plan'] = true;
		}

		return $item_data;
	}

	/**
	 * Register deposits product schema into cart/items endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_item_schema() {

		return array(
			'is_deposit'       => array(
				'description' => __( 'True if the cart item will be purchased with a deposit.', 'woocommerce-deposits' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'has_payment_plan' => array(
				'description' => __( 'True if the cart item will be purchased with a payment plan.', 'woocommerce-deposits' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'plan_schedule'    => array(
				'description' => __( 'Payment schedule for the associated plan.', 'woocommerce-deposits' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'schedule_id'     => array(
							'description' => __( 'Schedule ID.', 'woocommerce-deposits' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'schedule_index'  => array(
							'description' => __( 'Schedule index - if 0, then it is a deposit.', 'woocommerce-deposits' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'plan_id'         => array(
							'description' => __( 'Plan ID.', 'woocommerce-deposits' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'amount'          => array(
							'description' => __( 'Amount.', 'woocommerce-deposits' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'interval_amount' => array(
							'description' => __( 'Interval amount (number of days, weeks, months, years).', 'woocommerce-deposits' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'interval_unit'   => array(
							'description' => __( 'Interval unit - if 0, then it is a deposit.', 'woocommerce-deposits' ),
							'type'        => 'string',
							'enum'        => array( '0', 'year', 'month', 'week', 'day' ),
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
					),
				),
			),
		);

	}

	/**
	 * Add "Payable in total" cart item data to deposit items.
	 *
	 * @param array $data Cart Item data.
	 * @param array $cart_item Cart Item.
	 * @return array
	 */
	public static function cart_item_data( $data, $cart_item ) {

		// Bail out early if not serving a Store API request.
		if ( ! WC_Deposits_Core_Compatibility::is_store_api_request() ) {
			return $data;
		}

		if ( is_null( $cart_item ) || ! isset( $cart_item['full_amount'] ) || empty( $cart_item['is_deposit'] ) ) {
			return $data;
		}

		if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
			$full_amount = wc_get_price_excluding_tax(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $cart_item['full_amount'],
				)
			);
		} else {
			$full_amount = wc_get_price_including_tax(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $cart_item['full_amount'],
				)
			);
		}

		$data_key = __( 'Payable In Total', 'woocommerce-deposits' );
		if ( ! empty( $cart_item['payment_plan'] ) ) {
			$plan       = new WC_Deposits_Plan( $cart_item['payment_plan'] );
			$data_value = $plan->get_formatted_schedule( $full_amount );
		} else {
			$data_value = wc_price( $full_amount );
		}

		$data[] = array(
			'key'   => $data_key,
			'value' => $data_value,
		);

		return $data;
	}

}
