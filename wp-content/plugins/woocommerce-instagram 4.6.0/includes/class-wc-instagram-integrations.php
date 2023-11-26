<?php
/**
 * Handles the plugin integrations.
 *
 * @package WC_Instagram
 * @since   4.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Integrations.
 */
class WC_Instagram_Integrations {

	/**
	 * Registered integrations.
	 *
	 * @var array
	 */
	protected $integrations = array();

	/**
	 * Constructor.
	 *
	 * @since 4.5.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'register_integrations' ) );
		add_action( 'init', array( $this, 'init_integrations' ) );
	}

	/**
	 * Registers the plugin integrations.
	 *
	 * @since 4.5.0
	 */
	public function register_integrations() {
		$integrations = array(
			'WC_Instagram_Integration_Additional_Variation_Images',
		);

		/**
		 * Filters the plugin integrations.
		 *
		 * @since 4.5.0
		 *
		 * @param array $integrations The plugin integrations.
		 */
		$this->integrations = apply_filters( 'wc_instagram_integrations', $integrations );
	}

	/**
	 * Init integrations.
	 *
	 * @since 4.5.0
	 */
	public function init_integrations() {
		foreach ( $this->integrations as $integration ) {
			if ( ! is_a( $integration, 'WC_Instagram_Plugin_Integration', true ) ) {
				continue;
			}

			$plugin_basename = call_user_func( array( $integration, 'get_plugin_basename' ) );

			if ( ! wc_instagram_is_plugin_active( $plugin_basename ) ) {
				continue;
			}

			call_user_func( array( $integration, 'init' ) );
		}
	}
}

return new WC_Instagram_Integrations();
