<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Product_Enquiry_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Product Enquiry Form', 'wc_enquiry_form' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'wc_enquiry_form' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-product-enquiry-form' ) );
	}
}

new WC_Product_Enquiry_Privacy();
