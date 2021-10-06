<?php
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\ExtendRestApi;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;

/**
 * WooCommerce Points and Rewards Extend Store API.
 *
 * A class to extend the store public API with points and rewards related data.
 *
 * @package WC-Points-Rewards/Classes
 * @since 1.7.0
 */
class WC_Points_Rewards_Extend_Store_Endpoint {
	/**
	 * Stores Rest Extending instance.
	 *
	 * @var ExtendRestApi
	 */
	private static $extend;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'points-and-rewards';

	/**
	 * Bootstraps the class and hooks required data.
	 *
	 */
	public static function init() {
		self::$extend = Package::container()->get( ExtendRestApi::class );
		self::extend_store();
	}

	/**
	 * Registers the actual data into each endpoint.
	 *
	 */
	public static function extend_store() {
		// We need to ensure this class is loaded so Blocks can check the method is callable.
		require_once __DIR__ . '/class-wc-points-rewards-cart-checkout.php';


		if ( is_callable( [ self::$extend, 'register_endpoint_data' ] ) ) {
			self::$extend->register_endpoint_data(
				array(
					'endpoint'        => CartSchema::IDENTIFIER,
					'namespace'       => self::IDENTIFIER,
					'data_callback'   => array( 'WC_Points_Rewards_Extend_Store_Endpoint', 'extend_cart_data' ),
					'schema_callback' => array( 'WC_Points_Rewards_Extend_Store_Endpoint', 'extend_cart_schema' ),
					'schema_type'     => ARRAY_N,
				)
			);
		}

		if ( is_callable( [ self::$extend, 'register_update_callback' ] ) ) {
			self::$extend->register_update_callback(
				array(
					'namespace' => self::IDENTIFIER,
					'callback'  => array( 'WC_Points_Rewards_Cart_Checkout', 'rest_apply_discount' ),
				)
			);
		}
	}

	/**
	 * Register points and reward data into cart endpoint.
	 *
	 * @return array Registered data or empty array if condition is not satisfied.
	 *
	 */
	public static function extend_cart_data() {
		global $wc_points_rewards;
		$cart_max_points           = (int) $wc_points_rewards->cart->calculate_cart_max_points();
		$data                      = [
			'cart_max_points' => $cart_max_points,
		];
		$redeem_points_message     = strip_tags(
			$wc_points_rewards->cart->generate_redeem_points_message()
		);
		$earn_points_message     = strip_tags(
			$wc_points_rewards->cart->generate_earn_points_message(),
			'<a><b><strong><i><em><small><sub><del><ins><mark><sup><h1><h2><h3><h4><h5><h6><img>'
		);
		if ( null !== $earn_points_message ) {
			$data['earn_points_message'] = $earn_points_message;
		}
		if ( null !== $redeem_points_message ) {
			$data['redeem_points_message'] = $redeem_points_message;
		}
		return $data;
	}

	/**
	 * Register points and rewards schema into cart endpoint.
	 *
	 * @return array Registered schema.
	 *
	 */
	public static function extend_cart_schema() {
		return [
			'earn_points_message'   => [
				'description' => __( 'Points generated message', 'woocommerce-points-and-rewards' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			],
			'points_label_singular' => [
				'description' => __( 'The label used to refer to points on the frontend, singular.', 'woocommerce-points-and-rewards' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			],
			'points_label_plural'   => [
				'description' => __( 'The label used to refer to points on the frontend, plural.', 'woocommerce-points-and-rewards' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			],
		];
	}
}
