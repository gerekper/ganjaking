<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Account Funds
 * https://woocommerce.com/products/account-funds/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Account_Funds {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Account_Funds|null
	 * @since 5.0.12.9
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_Account_Funds
	 * @since 5.0.12.9
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0.12.9
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 5.0.12.9
	 */
	public function add_compatibility() {
		if ( ! class_exists( 'WC_Account_Funds' ) ) {
			return;
		}

		add_filter( 'wc_epo_update_cart_action_cart_updated', [ $this, 'wc_epo_update_cart_action_cart_updated' ], 10, 1 );
	}

	/**
	 * Skip altering cart update action
	 *
	 * @param boolean $ret If cart was updated.
	 * @return boolean
	 * @since 5.0.12.9
	 */
	public function wc_epo_update_cart_action_cart_updated( $ret ) {
		if ( ! empty( $_POST['wc_account_funds_apply'] ) || ! empty( $_GET['remove_account_funds'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}

		return $ret;
	}
}
