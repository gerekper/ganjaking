<?php
/**
 * Privacy integration.
 *
 * @package woocommerce-product-addons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * WC_Product_Addons_Privacy class
 */
class WC_Product_Addons_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( 'Product Add-ons', 'woocommerce-product-addons' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		$html_open_a  = '<a href="https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-product-addons" target="_blank">';
		$html_close_a = '</a>';
		/* translators: %1$s is the HTML for the opening tag of the link element. %2$s is the closing tag. The text between will be a hyperlink. */
		return wpautop( sprintf( esc_html__( 'By using this extension, you may be storing personal data or sharing data with an external service. %1$sLearn more about how this works, including what you may want to include in your privacy policy.%2$s', 'woocommerce-product-addons' ), $html_open_a, $html_close_a ) );
	}
}

new WC_Product_Addons_Privacy();
