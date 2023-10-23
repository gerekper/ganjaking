<?php
/**
 * Implements features of FREE version of YITH WooCommerce Badge Management
 *
 * @class   YITH_WCBM_Premium
 * @package YITH\BadgeManagementPremium\Classes
 * @since   1.0.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCBM_PREMIUM' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WCBM_Premium' ) ) {
	/**
	 * YITH WooCommerce Badge Management
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBM_Premium extends YITH_WCBM {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Premium
		 */
		protected static $instance;

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			yith_wcbm_badge_rules();

			// Register plugin to license/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * Load CPT Admin Class
		 */
		public function load_cpt_admin_class() {
			parent::load_cpt_admin_class();

			require_once YITH_WCBM_DIR . '/includes/class-yith-wcbm-badge-rule-post-type-admin.php';
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( function_exists( 'YIT_Plugin_Licence' ) ) {
				YIT_Plugin_Licence()->register( YITH_WCBM_INIT, YITH_WCBM_SECRET_KEY, YITH_WCBM_SLUG );
			}
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( function_exists( 'YIT_Upgrade' ) ) {
				YIT_Upgrade()->register( YITH_WCBM_SLUG, YITH_WCBM_INIT );
			}
		}

		/**
		 * Declare support for WooCommerce features.
		 *
		 * @since 2.18.0
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) && defined( 'YITH_WCBM_INIT' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_WCBM_INIT );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCBM_Premium class
 *
 * @return YITH_WCBM_Premium
 * @since      1.0.0
 * @deprecated since 1.3.0 use YITH_WCBM() instead
 */
function YITH_WCBM_Premium() {
	return YITH_WCBM();
}
