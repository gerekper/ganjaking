<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Custom_Thankyou_Page' ) ) {
	/**
	 * Main Plugin Class
	 *
	 * The class manage all the admin behaviors.
	 *
	 * @class      YITH_Custom_Thankyou_Page
	 * @package    YITH Custom ThankYou Page for Woocommerce
	 * @since      1.0.0
	 * @author     YITH
	 */
	class YITH_Custom_Thankyou_Page {
		/**
		 * Plugin Version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_CTPW_VERSION;

		/**
		 * Main Class Instance
		 *
		 * @var YITH_Custom_Thankyou_Page
		 * @since 1.0.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Admin class instance
		 *
		 * @var YITH_Custom_Thankyou_Page_Admin
		 * @since 1.0.0
		 */
		public $admin = null;

		/**
		 * Frontend class instance
		 *
		 * @var YITH_Custom_Thankyou_Page_Frontend
		 * @since 1.0.0
		 */
		public $frontend = null;

		/**
		 * Check if the plugin is activated or not
		 *
		 * @var bool
		 * @since 1.0.0
		 */
		public $is_plugin_enabled = false;

		/**
		 * Initialize Plugin
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->is_plugin_enabled = 'yes' === get_option( 'yith_ctpw_enable', 'no' );

			/* === Require Main Files === */

			// APPLY_FILTER: yith_ctpw_require_class : load classes for the plugin : parameter array.
			$require = apply_filters(
				'yith_ctpw_require_class',
				array(
					'common'   => array( 'includes/functions.yith-ctpw-common.php' ),
					'admin'    => array( 'includes/class.yith-custom-thankyou-page-admin.php' ),
					'frontend' => array(
						'includes/class.yith-custom-thankyou-page-frontend.php',
						'includes/class.yith-custom-thankyou-page-pdf.php',
					),
				)
			);

			$this->_require( $require );

			/* === Load Plugin Framework === */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_filter( 'body_class', array( $this, 'body_class' ) );

			/* == Plugins Init === */
			add_action( 'init', array( $this, 'init' ) );

		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Custom_Thankyou_Page
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;

		}


		/**
		 *
		 * Include the admin and frontend classes
		 *
		 * @param array $main_classes The required classes files path.
		 *
		 * @return void
		 * @access protected
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		protected function _require( $main_classes ) {
			foreach ( $main_classes as $section => $classes ) {
				foreach ( $classes as $class ) {
					if ( 'common' === $section || ( 'frontend' === $section && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' === $section && is_admin() ) && file_exists( YITH_CTPW_PATH . $class ) ) {
						require_once( YITH_CTPW_PATH . $class );
					}
				}
			}

		}


		/**
		 * Load plugin framework
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}

		}

		/**
		 * Classes Initialization
		 *
		 * Initialize the admin or frontend classes
		 *
		 * @return void
		 * @access protected
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function init() {

			if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && $_REQUEST['context'] === 'frontend' ) ) { //phpcs:ignore
				$this->admin = new YITH_Custom_Thankyou_Page_Admin();
			} elseif ( $this->is_plugin_enabled ) {
				$this->frontend = new YITH_Custom_Thankyou_Page_Frontend();
			}

		}

		/**
		 * Add body class(es)
		 *
		 * @param array $classes The classes array.
		 *
		 * @return array
		 * @since 1.0.0
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function body_class( $classes ) {
			$classes[] = 'yith-ctpw';

			return $classes;
		}

	} // End of class
}
