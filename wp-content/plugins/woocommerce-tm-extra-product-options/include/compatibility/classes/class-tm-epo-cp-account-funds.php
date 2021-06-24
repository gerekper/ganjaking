<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Account Funds 
 * https://woocommerce.com/products/account-funds/
 * 
 * @package Extra Product Options/Compatibility
 * @version 5.0.12.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_account_funds {

	/**
	 * The single instance of the class
	 *
	 * @since 5.0.12.9
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 5.0.12.9
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0.12.9
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ) );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 5.0.12.9
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'WC_Account_Funds' ) ) {
			return;
		}

		add_filter( 'wc_epo_update_cart_action_cart_updated', array($this, 'wc_epo_update_cart_action_cart_updated'), 10, 1 );

	}

	/**
	 * Skip altering cart update action
	 *
	 * @since 5.0.12.9
	 */
	public function wc_epo_update_cart_action_cart_updated( $ret ) {

        if ( ! empty( $_POST['wc_account_funds_apply'] ) || ! empty( $_GET['remove_account_funds'] ) ) {
			return true;
		}

        return $ret;

	}

}
