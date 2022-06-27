<?php
/**
 * Register functionality
 *
 * @package WC_Account_Funds
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Register.
 */
class WC_Account_Funds_Register {

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		add_action( 'woocommerce_created_customer', array( $this, 'funds_on_register' ) );
	}

	/**
	 * Adds funds to new customers.
	 *
	 * @since 2.6.0
	 *
	 * @param int $customer_id Customer ID.
	 */
	public function funds_on_register( $customer_id ) {
		$funds = get_option( 'account_funds_add_on_register', 0 );

		WC_Account_Funds_Manager::increase_user_funds( $customer_id, $funds );
	}
}

return new WC_Account_Funds_Register();
