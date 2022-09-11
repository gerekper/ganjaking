<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH_WC_Stripe_Integration integration with WooCommerce Stripe Plugin
 *
 * @class   YITH_WC_Stripe_Integration
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for WooCommerce Gateway Stripe.
 */
class YITH_WC_Stripe_Integration {


	/**
	 * Instance of YITH_WC_Stripe_Integration
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Return the instance of class
	 *
	 * @return null|YITH_WC_Stripe_Integration
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_stripe_integration_gateway' ), 11 );
		add_filter( 'ywsbs_max_failed_attempts_list', array( $this, 'add_failed_attempts' ) );
		add_filter( 'ywsbs_get_num_of_days_between_attemps', array( $this, 'add_num_of_days_between_attempts' ) );
		add_filter( 'ywsbs_from_list', array( $this, 'add_from_list' ) );

	}

	/**
	 * Add this gateway in the list of maximum number of attempts to do.
	 *
	 * @param array $list List of gateways.
	 *
	 * @return mixed
	 */
	public function add_failed_attempts( $list ) {
		$list[ YITH_WC_Subscription_WC_Stripe::$gateway_id ] = 4;
		return $list;
	}

	/**
	 * Add this gateway in the list of maximum number of attempts to do.
	 *
	 * @param array $list List of gateways.
	 *
	 * @return mixed
	 */
	public function add_num_of_days_between_attempts( $list ) {
		$list[ YITH_WC_Subscription_WC_Stripe::$gateway_id ] = 5;
		return $list;
	}

	/**
	 * Add this gateway in the list "from" to understand from where the
	 * update status is requested.
	 *
	 * @param array $list List of gateways.
	 *
	 * @return mixed
	 */
	public function add_from_list( $list ) {
		$list[] = YITH_WC_Subscription_WC_Stripe::get_instance()->get_method_title();
		return $list;
	}

	/**
	 * Replace the main gateway with the sources gateway.
	 *
	 * @param array $methods List of gateways.
	 *
	 * @return array
	 */
	public function add_stripe_integration_gateway( $methods ) {
		foreach ( $methods as $key => $method ) {
			if ( 'WC_Gateway_Stripe' === $method || $method instanceof WC_Gateway_Stripe ) {
				$methods[ $key ] = 'YITH_WC_Subscription_WC_Stripe';
			}
			if ( 'WC_Gateway_Stripe_Sepa' === $method || $method instanceof WC_Gateway_Stripe_Sepa ) {
				$methods[ $key ] = 'YITH_WC_Subscription_WC_Stripe_Sepa';
			}
		}

		return $methods;
	}


}
