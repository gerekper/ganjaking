<?php
/**
 * Provider that needs a WordPress plugin to work.
 *
 * @package WC_Newsletter_Subscription/Traits
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Newsletter_Subscription_Provider_Require_Plugin.
 */
trait WC_Newsletter_Subscription_Provider_Require_Plugin {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * The plugin URL.
	 *
	 * @var string
	 */
	protected $plugin_url = '';

	/**
	 * The plugin path.
	 *
	 * @var string
	 */
	protected $plugin_path = '';

	/**
	 * Gets the plugin name.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Gets the plugin URL.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	/**
	 * Gets if the required plugin is active.
	 *
	 * @since 3.0.0
	 *
	 * return bool
	 */
	public function is_plugin_active() {
		return wc_newsletter_subscription_is_plugin_active( $this->plugin_path );
	}

	/**
	 * Validates the credentials.
	 *
	 * Providers that depend on a WordPress plugin don't use credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials An array with the credentials to validate.
	 * @return bool
	 */
	public function validate_credentials( $credentials ) {
		return true;
	}
}
