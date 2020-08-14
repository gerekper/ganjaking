<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce One-Click Checkout Premium
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOCC' ) ) {
	/**
	 * YITH WooCommerce One-Click Checkout Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WOCC {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOCC_VERSION;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @return mixed YITH_WOCC_Admin_Premium | YITH_WOCC_Frontend_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			
			// Install
			add_action( 'init', array( $this, 'install' ) );
            add_action( 'init', array( $this, 'add_endpoints' ), 1 );

            // Load Plugin Framework
            add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			// Class admin
			if ( $this->is_admin() ) {
				
				// require class
				require_once('class.yith-wocc-admin.php');
				require_once('class.yith-wocc-admin-premium.php');
				// admin table
				require_once('admin-tables/class.yith-wocc-custom-table.php');
				require_once('admin-tables/class.yith-wocc-exclusions-table.php');
				
				YITH_WOCC_Admin_Premium();
				YITH_WOCC_Exclusions_Table();
			}
			else {
				// require class
				require_once('class.yith-wocc-frontend.php');
				require_once('class.yith-wocc-frontend-premium.php');
				require_once('class.yith-wocc-user-account.php');
				
				YITH_WOCC_Frontend_Premium();
				YITH_WOCC_User_Account();
			}
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if( ! empty( $plugin_fw_data ) ){
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Install plugin
		 *
		 * @since 1.0.0
		 * @access private
		 * @return void
		 * @author Francesco Licandro
		 */
		public function install() {

			$do_flush = get_option( 'yith-wocc-flush-rewrite-rules', 1 );

			add_rewrite_endpoint( 'custom-address', EP_ROOT | EP_PAGES );

			if( $do_flush ) {
				// change option
				update_option( 'yith-wocc-flush-rewrite-rules', 0 );
				// the flush rewrite rules
				flush_rewrite_rules();
			}
		}

		/**
		 * Add endpoint for WC 2.6
		 *
		 * @since 1.0.4
		 * @author Francesco Licandro
		 */
		public function add_endpoints() {
			WC()->query->query_vars['one-click']        = get_option( 'woocommerce_myaccount_one_click_endpoint', 'one-click' );
			WC()->query->query_vars['custom-address']   = get_option( 'woocommerce_myaccount_custom_address_endpoint', 'custom-address' );
		}
		
		/**
		 * Check if is admin section
		 * 
		 * @author Francesco Licandro
		 * @since 1.0.5
		 * @return boolean
		 */
		public function is_admin(){
		    $is_admin = is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend' );
			return apply_filters( 'yith_wocc_check_id_admin', $is_admin );
		}
	}
}

/**
 * Unique access to instance of YITH_WOCC class
 *
 * @return \YITH_WOCC
 * @since 1.0.0
 */
function YITH_WOCC(){
	return YITH_WOCC::get_instance();
}