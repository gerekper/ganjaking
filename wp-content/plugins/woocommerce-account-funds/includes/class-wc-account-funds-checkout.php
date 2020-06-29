<?php
/**
 * Checkout functionality
 *
 * @package WC_Account_Funds
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Checkout.
 */
class WC_Account_Funds_Checkout {

	/**
	 * Constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues the scripts.
	 *
	 * @since 2.2.0
	 */
	public function enqueue_scripts() {
		if ( ! is_checkout() ) {
			return;
		}

		$suffix = wc_account_funds_get_scripts_suffix();

		wp_enqueue_script( 'wc-account-funds-checkout', WC_ACCOUNT_FUNDS_URL . "assets/js/frontend/checkout{$suffix}.js", array( 'jquery' ), WC_ACCOUNT_FUNDS_VERSION, true );
	}
}

return new WC_Account_Funds_Checkout();
