<?php
/**
 * YWSBS_WC_EWAY_Integration integration with WooCommerce Payments Plugin
 *
 * @class   YWSBS_WC_EWAY_Integration
 * @package YITH/Subscription/Gateways
 * @since   2.4.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for  WooCommerce Payments.
 */
class YWSBS_WC_EWAY_Integration {


	/**
	 * Instance of YWSBS_WC_EWAY_Integration
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Return the instance of class
	 *
	 * @return null|YWSBS_WC_EWAY_Integration
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_eway_integration_gateway' ), 100 );
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
		$list['eway'] = 4;
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
		$list['eway'] = 5;
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
		$list[] = __( 'eWAY', 'yith-woocommerce-subscription' );
		return $list;
	}

	/**
	 * Replace the main gateway with the sources gateway.
	 *
	 * @param array $methods List of gateways.
	 *
	 * @return array
	 */
	public function add_eway_integration_gateway( $methods ) {

		if ( ( isset( $_GET['page'], $_GET['tab'] ) && $_GET['page'] === 'wc-settings' && $_GET['tab'] === 'checkout' ) ) { //phpcs:ignore
			return $methods;
		}

		foreach ( $methods as $key => $method ) {

			if ( 'WC_Gateway_EWAY' === $method ) {
				$methods[ $key ] = 'YWSBS_WC_EWAY';
			}
		}

		return $methods;
	}


}
