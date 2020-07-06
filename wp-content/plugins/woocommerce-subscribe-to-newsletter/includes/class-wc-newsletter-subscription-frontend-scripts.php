<?php
/**
 * Register functionality
 *
 * This class handles the subscription process in the register form.
 *
 * @package WC_Newsletter_Subscription
 * @since   2.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Frontend_Scripts.
 */
class WC_Newsletter_Subscription_Frontend_Scripts {

	/**
	 * Init.
	 *
	 * @since 2.9.0
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @since 2.9.0
	 */
	public static function enqueue_scripts() {
		if ( ! is_checkout() && ( ! is_account_page() || is_user_logged_in() ) ) {
			return;
		}

		$css = 'input#subscribe_to_newsletter + .optional { display: none; }';

		wp_register_style( 'wc-newsletter-subscription', false, array(), WC_NEWSLETTER_SUBSCRIPTION_VERSION );
		wp_enqueue_style( 'wc-newsletter-subscription' );
		wp_add_inline_style( 'wc-newsletter-subscription', $css );
	}
}

WC_Newsletter_Subscription_Frontend_Scripts::init();
