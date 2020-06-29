<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Shipping_Per_Product_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Shipping Per Product', 'woocommerce-shipping-per-product' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-shipping-per-product' ), 'https://docs.woocommerce.com/document/privacy-shipping/#woocommerce-shipping-per-product' ) );
	}
}

new WC_Shipping_Per_Product_Privacy();
