<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Pre_Order' ) ) {
	/**
	 * Class YITH_Pre_Order
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order {
		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WCPO_VERSION;

		/**
		 * Main Instance
		 *
		 * @var YITH_Pre_Order
		 * @since  1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Pre_Order_Admin
		 * @since 1.0
		 */
		public $admin = null;

		/**
		 * Main Frontpage Instance
		 *
		 * @var YITH_Pre_Order_Frontend
		 * @since 1.0
		 */
		public $frontend = null;

		/**
		 * Main My Account Instance
		 *
		 * @var YITH_Pre_Order_Frontend
		 * @since 1.0
		 */
		public $myaccount = null;

		/**
		 * Main Download Links manager Instance
		 *
		 * @var YITH_Pre_Order_Download_Links
		 * @since 1.3.0
		 */
		public $download_links = null;


		/**
		 * Construct
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 */
		protected function __construct() {
            if ( version_compare( WC()->version, '2.6', '>=' ) ) {
                add_action( 'init', array( $this, 'add_endpoints' ), 1 );
            }
            $this->init_includes();
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->init_my_account();
			}
            $this->init();
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Pre_Order Main instance
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init_includes() {
			if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || defined( 'YITH_WCFM_PREMIUM' ) ) {
				require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-admin.php' );
				require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-edit-product-page.php' );
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-frontend.php' );
				require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-my-account.php' );
			}

            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-product.php' );
			require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-download-links.php' );
        }


		/**
		 * Class Initializzation
		 *
		 * Instance the admin or frontend classes
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @access protected
		 */

		public function init() {

			$this->download_links = new YITH_Pre_Order_Download_Links();

			if ( is_admin() ) {
				$this->admin = new YITH_Pre_Order_Admin();
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Pre_Order_Frontend();
			}
		}

		/**
		 * Add waiting list account endpoints for WC 2.6
		 *
		 * @author Lorenzo Giuffrida
		 * @access public
		 */
		public function add_endpoints() {
            add_rewrite_endpoint( 'my-pre-orders', EP_ROOT | EP_PAGES );
		}

		public function init_my_account() {
			$this->myaccount = new YITH_Pre_Order_My_Account();
        }

		/**
		 * Load plugin framework
		 *
		 * @author Carlos Mora <cjmora.yithemes@gmail.com>
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if (!defined('YIT_CORE_PLUGIN')) {
				global $plugin_fw_data;
				if (!empty($plugin_fw_data)) {
					$plugin_fw_file = array_shift($plugin_fw_data);
					require_once($plugin_fw_file);
				}
			}
		}
	}

	if ( ! function_exists( 'wc_help_tip' ) && version_compare( WC()->version, '2.5.0', '<' ) ) {

		/**
		 * Display a WooCommerce help tip. (Added for compatibility with WC 2.4)
		 *
		 * @since  2.5.0
		 *
		 * @param  string $tip        Help tip text
		 * @param  bool   $allow_html Allow sanitized HTML if true or escape
		 *
		 * @return string
		 */
		function wc_help_tip( $tip, $allow_html = false ) {
			if ( $allow_html ) {
				$tip = wc_sanitize_tooltip( $tip );
			} else {
				$tip = esc_attr( $tip );
			}

			return '<span class="woocommerce-help-tip" data-tip="' . $tip . '"></span>';
		}

	}
}