<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * WC_Currency_Converter_Privacy
 *
 * @since 1.6.9
 * @deprecated 2.1.0
 */
class WC_Currency_Converter_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( __( 'WooCommerce Currency Converter', 'woocommerce-currency-converter-widget' ) );
		wc_deprecated_function( __FUNCTION__, '2.1.0' );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		return wpautop(
			sprintf(
				/* translators: %s: WooCommerce Marketplace Privacy link */
				__( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-currency-converter-widget' ),
				'https://woocommerce.com/document/marketplace-privacy/#woocommerce-currency-converter-widget'
			)
		);
	}
}

new WC_Currency_Converter_Privacy();
