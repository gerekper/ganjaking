<?php
/**
 * YWSBS_WC_Payments_Integration integration with WooCommerce Payments Plugin
 *
 * @class   YWSBS_WC_Payments_Integration
 * @since   2.4.0
 * @author  YITH
 * @package YITH/Subscription/Gateways
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for  WooCommerce Payments.
 */
class YWSBS_WC_Payments_Integration {


	/**
	 * Instance of YWSBS_WC_Payments_Integration
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Return the instance of class
	 *
	 * @return null|YWSBS_WC_Payments_Integration
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_wp_payments_integration_gateway' ), 100 );
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
		$list['woocommerce-payments'] = 4;

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
		$list['woocommerce_payments'] = 5;

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
		$list[] = __( 'WooCommerce Payments', 'yith-woocommerce-subscription' );

		return $list;
	}

	/**
	 * Replace the main gateway with the sources gateway.
	 *
	 * @param array $methods List of gateways.
	 *
	 * @return array
	 */
	public function add_wp_payments_integration_gateway( $methods ) {

		if ( ( isset( $_GET['page'], $_GET['tab'] ) && 'wc-settings' === $_GET['page'] && 'checkout' === $_GET['tab'] ) ) { //phpcs:ignore
			return $methods;
		}

		if (
			! class_exists( 'WC_Payments' ) ||
			! class_exists( 'WC_Payments_Customer_Service' ) ||
			! class_exists( 'WC_Payments_Token_Service' ) ||
			! class_exists( 'WC_Payments_Order_Service' ) ||
			! class_exists( 'WC_Payments_Action_Scheduler_Service' ) ||
			! class_exists( 'WCPay\Session_Rate_Limiter' )
		) {
			return $methods;
		}

		$api_client                      = WC_Payments::create_api_client();
		$account                         = WC_Payments::get_account_service();
		$customer_service                = new WC_Payments_Customer_Service( $api_client, $account, WC_Payments::get_database_cache() );
		$token_service                   = new WC_Payments_Token_Service( $api_client, $customer_service );
		$action_scheduler_service        = new WC_Payments_Action_Scheduler_Service( $api_client );
		$failed_transaction_rate_limiter = new WCPay\Session_Rate_Limiter( WCPay\Session_Rate_Limiter::SESSION_KEY_DECLINED_CARD_REGISTRY, 5, 10 * MINUTE_IN_SECONDS );
		$order_service                   = new WC_Payments_Order_Service( $api_client );
		$gateway                         = new YWSBS_WC_Payments( $api_client, $account, $customer_service, $token_service, $action_scheduler_service, $failed_transaction_rate_limiter, $order_service );

		foreach ( $methods as $key => $method ) {

			if ( class_exists( 'WC_Payment_Gateway_WCPay_Subscriptions_Compat' ) && $method instanceof WC_Payment_Gateway_WCPay_Subscriptions_Compat ) {
				$methods[ $key ] = $gateway;
			}
			if ( class_exists( 'WCPay\Payment_Methods\CC_Payment_Gateway' ) && $method instanceof WCPay\Payment_Methods\CC_Payment_Gateway ) {
				$methods[ $key ] = $gateway;
			}
		}

		return $methods;
	}


}

