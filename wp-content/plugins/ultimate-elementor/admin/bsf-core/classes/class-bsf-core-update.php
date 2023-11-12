<?php
/**
 * BSF Core Update
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        http://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! class_exists( 'BSF_Core_Update' ) ) {

	/**
	 * BSF_Core_Update initial setup
	 *
	 * @since 1.0.0
	 */
	class BSF_Core_Update {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			// Theme Updates.
			add_action( 'admin_init', __CLASS__ . '::init', 0 );
			add_filter( 'all_plugins', array( $this, 'update_products_slug' ), 10, 1 );
		}

		/**
		 * Implement theme update logic.
		 *
		 * @since 1.0.0
		 */
		public static function init() {
			do_action( 'astra_update_before' );

			// Get auto saved version number.
			$saved_version = get_option( 'bsf-updater-version', false );

			// If equals then return.
			if ( version_compare( $saved_version, BSF_UPDATER_VERSION, '=' ) ) {
				return;
			}

			// // Update auto saved version number.
			update_option( 'bsf-updater-version', BSF_UPDATER_VERSION );

			do_action( 'astra_update_after' );
		}

		/**
		 * Update bsf product slug in WP installed plugins data which will be used in enable/disablestaged updates products.
		 *
		 * @param array $plugins All installed plugins.
		 *
		 * @return array
		 */
		public function update_products_slug( $plugins ) {
			$bsf_products = bsf_get_brainstorm_products( true );

			foreach ( $bsf_products as $product => $data ) {
				$plugin_file = isset( $data['template'] ) ? sanitize_text_field( $data['template'] ) : '';
				if ( isset( $plugins[ $plugin_file ] ) && ! empty( $data['slug'] ) ) {
					$plugins[ $plugin_file ]['slug'] = $data['slug'];
				}
			}

			return $plugins;
		}

	}

}

/**
 * Kicking this off by calling 'get_instance()' method
 */
BSF_Core_Update::get_instance();
