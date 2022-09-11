<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Stripe Gateway
 * Version tested: 4.1.1.
 *
 * @since 0.7.2.
 */
class PLLWC_Stripe {

	/**
	 * Constructor
	 *
	 * @since 0.7.2
	 */
	public function __construct() {
		if ( PLL() instanceof PLL_Frontend ) {
			add_filter( 'option_woocommerce_stripe_settings', array( $this, 'set_apple_pay_lang' ) );

			if ( version_compare( WC_STRIPE_VERSION, '4.1.1' ) ) {
				/*
				 * Prior to v4.1.1, Stripe doesn't use the standard get_description() function, bypassing the woocommerce_gateway_description filter.
				 * The versions 4.0 to 4.1 will have a buggy description for all payment methods except the credit card.
				 */
				add_filter( 'wc_stripe_description', array( $this, 'get_description' ) );
			}
		}
	}

	/**
	 * Sets the language for the Apple Pay button.
	 * Not tested as we don't have an Apple device ;-)
	 *
	 * @since 0.7.2
	 *
	 * @param array $options WooCommerce Stripe Settings.
	 * @return array
	 */
	public function set_apple_pay_lang( $options ) {
		// It is expected that the user will use ISO 639-1 language codes.
		$options['apple_pay_button_lang'] = pll_current_language();
		return $options;
	}

	/**
	 * Works around the bypassed 'woocommerce_gateway_description' filter.
	 *
	 * @since 0.9.3
	 *
	 * @param string $description Stripe gateway description.
	 * @return string
	 */
	public function get_description( $description ) {
		$gateways = WC_Payment_Gateways::instance()->payment_gateways();

		if ( isset( $gateways['stripe'] ) && $gateways['stripe'] instanceof WC_Gateway_Stripe ) {
			// We must get the option and apply the filter as the description may be already modified with the testmode string.
			$description = apply_filters( 'woocommerce_gateway_description', $gateways['stripe']->get_option( 'description' ), $gateways['stripe']->id );

			// Add testmode string as done in WC_Gateway_Stripe class.
			if ( $gateways['stripe']->testmode ) {
				/* translators: %s is a link to the Stripe documentation */
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date or check the documentation "<a href="%s">Testing Stripe</a>" for more card numbers.', 'polylang-wc' ), 'https://stripe.com/docs/testing' );
				$description  = trim( $description );
			}
		}
		return $description;
	}
}
