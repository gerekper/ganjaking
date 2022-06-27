<?php
/**
 * Handles the plugin integrations.
 *
 * @package WC_Account_Funds
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Integrations.
 */
class WC_Account_Funds_Integrations {

	/**
	 * Registered integrations.
	 *
	 * @var array
	 */
	protected $integrations = array();

	/**
	 * Constructor.
	 *
	 * @since 2.5.0
	 */
	public function __construct() {
		add_action( 'plugin_loaded', array( $this, 'register_integrations' ) );
		add_action( 'init', array( $this, 'init_integrations' ) );
	}

	/**
	 * Registers the plugin integrations.
	 *
	 * @since 2.5.0
	 */
	public function register_integrations() {
		$integrations = array(
			'WC_Account_Funds_Integration_All_Products_Subscriptions',
			'WC_Account_Funds_Integration_Square',
		);

		/**
		 * Filters the plugin integrations.
		 *
		 * @since 2.5.0
		 *
		 * @param array $integrations The plugin integrations.
		 */
		$this->integrations = apply_filters( 'wc_account_funds_integrations', $integrations );
	}

	/**
	 * Init integrations.
	 *
	 * @since 2.5.0
	 */
	public function init_integrations() {
		foreach ( $this->integrations as $integration ) {
			if ( ! is_a( $integration, 'WC_Account_Funds_Integration', true ) ) {
				continue;
			}

			$plugin_basename = call_user_func( array( $integration, 'get_plugin_basename' ) );

			if ( ! WC_Account_Funds_Utils::is_plugin_active( $plugin_basename ) ) {
				continue;
			}

			call_user_func( array( $integration, 'init' ) );
		}
	}
}

return new WC_Account_Funds_Integrations();
