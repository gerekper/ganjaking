<?php
/**
 * Admin Settings
 *
 * @package WC_Newsletter_Subscription/Admin
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Newsletter_Subscription_Settings_Page', false ) ) {
	include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/abstracts/abstract-wc-newsletter-subscription-settings-page.php';
}

if ( class_exists( 'WC_Newsletter_Subscription_Admin_Settings', false ) ) {
	return new WC_Newsletter_Subscription_Admin_Settings();
}

/**
 * Class WC_Newsletter_Subscription_Admin_Settings.
 */
class WC_Newsletter_Subscription_Admin_Settings extends WC_Newsletter_Subscription_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$this->id    = 'newsletter';
		$this->label = __( 'Newsletter', 'woocommerce-subscribe-to-newsletter' );

		parent::__construct();
	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 2.8.0
	 */
	public function init_settings_api() {
		include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/admin/settings/class-wc-newsletter-subscription-settings-general.php';

		$this->settings_api = new WC_Newsletter_Subscription_Settings_General();
	}
}

return new WC_Newsletter_Subscription_Admin_Settings();
