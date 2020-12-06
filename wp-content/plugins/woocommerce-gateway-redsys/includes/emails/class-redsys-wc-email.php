<?php
/**
 * Class Custom_WC_Email
 */
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
class Redsys_WC_Email {
	/**
	 * Custom_WC_Email constructor.
	 */
	/*
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function __construct() {
		// Filtering the emails and adding our own email.
		add_action( 'woocommerce_email_classes', array( $this, 'register_email' ), 90, 1 );
	}
	/**
	 * @param array $emails
	 *
	 * @return array
	 */
	/*
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function register_email( $emails ) {
		require_once REDSYS_PLUGIN_PATH . 'includes/emails/templates/class-redsys-custom-email.php';
		$emails['Redsys_Customer_Email_Order'] = new Redsys_Customer_Email_Order();
		return $emails;
	}
}
new Redsys_WC_Email();
