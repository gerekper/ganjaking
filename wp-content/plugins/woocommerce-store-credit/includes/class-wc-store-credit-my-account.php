<?php
/**
 * Class to customize the 'My Account' page.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_My_Account.
 */
class WC_Store_Credit_My_Account {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_query_vars', array( $this, 'query_vars' ) );
		add_filter( 'woocommerce_endpoint_store-credit_title', array( $this, 'store_credit_title' ) );
		add_action( 'woocommerce_account_store-credit_endpoint', array( $this, 'store_credit_content' ) );
		add_action( 'woocommerce_account_dashboard', array( $this, 'account_dashboard' ) );
	}

	/**
	 * Registers custom query vars.
	 *
	 * @since 3.0.0
	 *
	 * @param array $query_vars The query vars.
	 * @return array
	 */
	public function query_vars( $query_vars ) {
		$query_vars['store-credit'] = 'store-credit';

		return $query_vars;
	}

	/**
	 * Filters the 'store-credit' endpoint title.
	 *
	 * @since 3.0.0
	 *
	 * @return string.
	 */
	public function store_credit_title() {
		return _x( 'Store credit', 'my account: page title', 'woocommerce-store-credit' );
	}

	/**
	 * Outputs the 'store-credit' endpoint content.
	 *
	 * @since 3.0.0
	 */
	public function store_credit_content() {
		wc_store_credit_get_template( 'myaccount/store-credit.php' );
	}

	/**
	 * Outputs the 'Store Credit' section on the 'My Account' dashboard page.
	 *
	 * @since 3.0.0
	 */
	public function account_dashboard() {
		wc_store_credit_get_template( 'myaccount/dashboard-store-credit.php' );
	}
}

return new WC_Store_Credit_My_Account();
