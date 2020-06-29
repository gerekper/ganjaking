<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WooCommerce_Order_Barcodes_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Order Barcodes', 'woocommerce-order-barcodes' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-order-barcodes' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-order-barcodes' ) );
	}
}

new WooCommerce_Order_Barcodes_Privacy();
