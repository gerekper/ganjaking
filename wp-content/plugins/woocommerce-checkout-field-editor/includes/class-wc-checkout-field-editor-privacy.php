<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Checkout_Field_Editor_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Checkout Field Editor', 'woocommerce-checkout-field-editor' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-checkout-field-editor' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-checkout-field-editor' ) );
	}
}

new WC_Checkout_Field_Editor_Privacy();
