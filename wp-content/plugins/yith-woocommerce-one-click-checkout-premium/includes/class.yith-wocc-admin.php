<?php
/**
 * Admin class
 *
 * @author YITH
 * @package YITH WooCommerce One-Click Checkout Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOCC_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WOCC_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin options
		 *
		 * @var array
		 * @access public
		 * @since 1.0.0
		 */
		public $options = array();

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOCC_VERSION;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = '#';

		/**
		 * @var string Added to Cart Popup panel page
		 */
		protected $_panel_page = 'yith_wocc_panel';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC_Admin
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
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5) ;

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WOCC_DIR . '/' . basename( YITH_WOCC_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general' => __( 'Settings', 'yith-woocommerce-one-click-checkout' ),
			);

			if ( ! ( defined( 'YITH_WOCC_PREMIUM' ) && YITH_WOCC_PREMIUM ) ) {
				$admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-one-click-checkout' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'YITH WooCommerce One-Click Checkout', 'yith-woocommerce-one-click-checkout' ),
				'menu_title'       => __( 'One-Click Checkout', 'yith-woocommerce-one-click-checkout' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => apply_filters( 'yith_wocc_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_WOCC_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class()
			);

			/* === Fixed: not updated theme  === */
			if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WOCC_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			// add upload type
			add_action( 'woocommerce_admin_field_yith_wocc_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WOCC_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if( file_exists( $premium_tab_template ) ) {
				include_once($premium_tab_template);
			}

		}

        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use plugin_row_meta
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {

            if ( defined( 'YITH_WOCC_INIT' ) && YITH_WOCC_INIT == $plugin_file ) {
                $new_row_meta_args['slug']   = YITH_WOCC_SLUG;

                if( defined( 'YITH_WOCC_PREMIUM' ) ){
                    $new_row_meta_args['is_premium'] = true;
                }
            }

            return $new_row_meta_args;
        }

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing.'?refer_id=1030585';
		}
	}
}
/**
 * Unique access to instance of YITH_WOCC_Admin class
 *
 * @return \YITH_WOCC_Admin
 * @since 1.0.0
 */
function YITH_WOCC_Admin(){
	return YITH_WOCC_Admin::get_instance();
}