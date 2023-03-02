<?php
/**
 * Handles the plugin integrations.
 *
 * @package WC_Store_Credit
 * @since   4.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Integrations.
 */
class WC_Store_Credit_Integrations {

	/**
	 * Registered integrations.
	 *
	 * @var array
	 */
	protected $integrations = array();

	/**
	 * Constructor.
	 *
	 * @since 4.1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'register_integrations' ) );
		add_action( 'init', array( $this, 'init_integrations' ) );
	}

	/**
	 * Registers the plugin integrations.
	 *
	 * @since 4.1.0
	 */
	public function register_integrations() {
		$integrations = array(
			'WC_Store_Credit_Integration_Shipping_Tax',
			'WC_Store_Credit_Integration_Avatax',
		);

		/**
		 * Filters the plugin integrations.
		 *
		 * @since 4.1.0
		 *
		 * @param array $integrations The plugin integrations.
		 */
		$this->integrations = apply_filters( 'wc_store_credit_integrations', $integrations );
	}

	/**
	 * Init integrations.
	 *
	 * @since 4.1.0
	 */
	public function init_integrations() {
		foreach ( $this->integrations as $integration ) {
			if ( ! is_a( $integration, 'WC_Store_Credit_Integration', true ) ) {
				continue;
			}

			$plugin_basename = call_user_func( array( $integration, 'get_plugin_basename' ) );

			if ( ! wc_store_credit_is_plugin_active( $plugin_basename ) ) {
				continue;
			}

			call_user_func( array( $integration, 'init' ) );
		}
	}
}

return new WC_Store_Credit_Integrations();
