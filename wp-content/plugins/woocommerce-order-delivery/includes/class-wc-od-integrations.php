<?php
/**
 * Handles the plugin integrations.
 *
 * @package WC_OD
 * @since   1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Integrations.
 */
class WC_OD_Integrations {

	/**
	 * Registered integrations.
	 *
	 * @var array
	 */
	protected $integrations = array();

	/**
	 * Constructor.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {
		add_action( 'plugin_loaded', array( $this, 'register_integrations' ) );
		add_action( 'init', array( $this, 'init_integrations' ) );
	}

	/**
	 * Registers the plugin integrations.
	 *
	 * @since 1.9.0
	 */
	public function register_integrations() {
		$integrations = array(
			'WC_OD_Integration_Ship_Multiple',
			'WC_OD_Integration_PIP',
			'WC_OD_Integration_PDF_Invoices_Packing_Slips',
		);

		/**
		 * Filters the plugin integrations.
		 *
		 * @since 1.9.0
		 *
		 * @param array $integrations The plugin integrations.
		 */
		$this->integrations = apply_filters( 'wc_od_integrations', $integrations );
	}

	/**
	 * Init integrations.
	 *
	 * @since 1.9.0
	 */
	public function init_integrations() {
		foreach ( $this->integrations as $integration ) {
			$implements = class_implements( $integration );

			if ( ! is_array( $implements ) || ! in_array( 'WC_OD_Integration', $implements, true ) ) {
				continue;
			}

			$plugin_basename = call_user_func( array( $integration, 'get_plugin_basename' ) );

			if ( ! WC_OD_Utils::is_plugin_active( $plugin_basename ) ) {
				continue;
			}

			call_user_func( array( $integration, 'init' ) );
		}
	}
}

return new WC_OD_Integrations();
