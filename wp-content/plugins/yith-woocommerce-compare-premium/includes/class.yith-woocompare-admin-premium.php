<?php
/**
 * Admin Premium class
 *
 * @author YITH
 * @package YITH WooCommerce Compare
 * @version 2.0.0
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_Woocompare_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_Woocompare_Admin_Premium extends YITH_Woocompare_Admin {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// Add admin tabs.
			add_filter( 'yith_woocompare_admin_tabs', array( $this, 'add_admin_tabs' ), 10, 1 );
			add_action( 'yith_woocompare_shortcode_tab', array( $this, 'shortcode_tab' ) );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WOOCOMPARE_DIR . 'plugin-fw/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WOOCOMPARE_INIT, YITH_WOOCOMPARE_SECRET_KEY, YITH_WOOCOMPARE_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WOOCOMPARE_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WOOCOMPARE_SLUG, YITH_WOOCOMPARE_INIT );
		}

		/**
		 * Add premium admin tabs
		 *
		 * @since 2.0.0
		 * @access public
		 * @author Francesco Licandro
		 * @param array $tabs An array of admin settings tabs.
		 * @return mixed
		 */
		public function add_admin_tabs( $tabs ) {

			$tabs['table']     = __( 'Comparison Table', 'yith-woocommerce-compare' );
			$tabs['share']     = __( 'Social Network Sites Sharing', 'yith-woocommerce-compare' );
			$tabs['related']   = __( 'Related Products', 'yith-woocommerce-compare' );
			$tabs['style']     = __( 'Style', 'yith-woocommerce-compare' );
			$tabs['shortcode'] = __( 'Build Shortcode', 'yith-woocommerce-compare' );

			return $tabs;
		}

		/**
		 * Content of build shortcode tab in plugin setting
		 *
		 * @access public
		 * @since 2.0.3
		 * @author Francesco Licandro
		 */
		public function shortcode_tab() {
			$shortcode_tab_template = YITH_WOOCOMPARE_TEMPLATE_PATH . '/admin/yith-woocompare-shortcode-tab.php';
			if ( file_exists( $shortcode_tab_template ) ) {
				include_once $shortcode_tab_template;
			}
		}
	}
}
