<?php
/**
 * Privacy.
 *
 * @package woocommerce-splash-popup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * WC_Splash_Popup_Privacy class
 */
class WC_Splash_Popup_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( 'Splash Popup', 'wc_splash' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		return '<p>' .
			esc_html__( 'By using this extension, you may be storing personal data or sharing data with an external service.', 'wc_splash' ) .
			' <a href="https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-splash-popup" target="_blank">' .
			esc_html__( 'Learn more about how this works, including what you may want to include in your privacy policy.', 'wc_splash' ) .
			'</a></p>
		';
	}
}

new WC_Splash_Popup_Privacy();
