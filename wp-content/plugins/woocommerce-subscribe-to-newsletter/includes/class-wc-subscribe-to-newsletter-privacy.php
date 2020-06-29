<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Subscribe_To_Newsletter_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Subscribe to Newsletter', 'woocommerce-subscribe-to-newsletter' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-subscribe-to-newsletter' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-subscribe-to-newsletter' ) );
	}
}

new WC_Subscribe_To_Newsletter_Privacy();
