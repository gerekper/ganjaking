<?php
/**
 * Class to handle the plugin emails.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Emails class.
 */
class WC_Store_Credit_Emails {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_actions', array( $this, 'email_actions' ) );
		add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );
	}

	/**
	 * Registers custom emails actions.
	 *
	 * @since 3.0.0
	 *
	 * @param array $actions The email actions.
	 * @return array
	 */
	public function email_actions( $actions ) {
		$actions[] = 'wc_store_credit_send_credit_to_customer';

		return $actions;
	}

	/**
	 * Registers custom emails classes.
	 *
	 * @since 3.0.0
	 *
	 * @param array $emails The email classes.
	 * @return array
	 */
	public function email_classes( $emails ) {
		$emails['WC_Store_Credit_Email_Send_Credit'] = include 'emails/class-wc-store-credit-email-send-credit.php';

		return $emails;
	}
}

return new WC_Store_Credit_Emails();
