<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	protected static $_instance = null;

	/**
	 * Return the instance of class
	 *
	 * @return null|YITH_WC_Stripe_Integration
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_stripe_integration_gateway' ), 10 );

		add_filter( 'ywsbs_max_failed_attempts_list', array( $this, 'add_failed_attempts' ) );
		add_filter( 'ywsbs_get_num_of_days_between_attemps', array( $this, 'add_num_of_days_between_attempts' ) );
		add_filter( 'ywsbs_from_list', array( $this, 'add_from_list' ) );

	}

	/**
	 * Add this gateway in the list of maximum number of attempts to do.
	 *
	 * @param $list
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function add_failed_attempts( $list ) {
		$list[ YITH_WC_Subscription_WC_Stripe::$gateway_id ] = 4;
		return $list;
	}

	/**
	 * Add this gateway in the list of maximum number of attempts to do.
	 *
	 * @param $list
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function add_num_of_days_between_attempts( $list ) {
		$list[ YITH_WC_Subscription_WC_Stripe::$gateway_id ] = 5;
		return $list;
	}

	/**
	 * Add this gateway in the list "from" to understand from where the
	 * update status is requested.
	 *
	 * @param $list
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function add_from_list( $list ) {
		$list[] = YITH_WC_Subscription_WC_Stripe::instance()->get_method_title();
		return $list;
	}

	/**
	 * Replace the main gateway with the sources gateway.
	 *
	 * @param $gateways
	 *
	 * @return array
	 */
	public function add_stripe_integration_gateway( $methods ) {
		foreach ( $methods as $key => $method ) {
			/**@var WC_Payment_Gateway_CC $method * */
			if ( 'WC_Gateway_Stripe' == $method ) {
				$methods[ $key ] = 'YITH_WC_Subscription_WC_Stripe';
			}
		}

		return $methods;
	}


}
