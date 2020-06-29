<?php
/**
 * Admin class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Popup_Admin' ) ) {
	/**
	 * YITH_Popup_Admin class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Popup_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-popup/';

		/**
		 * @var string Panel page
		 */
		protected $_panel_page = 'yith_woocommerce_popup';

		/**
		 * @var string Doc Url
		 */
		public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-popup/';

		/**
		 * The name for the plugin options
		 *
		 * @access public
		 * @var string
		 * @since 1.0.0
		 */
		public $plugin_options = 'yit_ypop_options';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Popup_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @return \YITH_Popup_Admin
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->create_menu_items();

			// Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YPOP_DIR . '/' . basename( YITH_YPOP_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// custom styles and javascript
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 20 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_metaboxes' ), 10, 2 );

			add_action( 'wp_ajax_ypop_change_status', array( $this, 'change_status' ) );
			add_action( 'wp_ajax_nopriv_ypop_change_status', array( $this, 'change_status' ) );

			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'textarea_metabox' ) );
			add_filter( 'yith_plugin_fw_metabox_class', array( $this, 'add_custom_metabox_class' ), 10, 2 );

			add_action( 'plugins_loaded', array( $this, 'load_privacy_dpa' ), 20 );

		}

		/**
		 * Load the class
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function load_privacy_dpa() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once YITH_YPOP_INC . 'class.yith-popup-privacy-dpa.php';
			}
		}



		/**
		 * Change value in a metabox
		 *
		 * Modify the metabox value in a textarea-editor when the value is empty
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		function textarea_metabox( $args ) {
			if ( ! isset( $_REQUEST['post'] ) ) {
				return $args;
			}
			$post_id = $_REQUEST['post'];

			if ( $args['type'] == 'textarea-editor' ) {
				$meta_value                    = YITH_Popup()->get_meta( $args['args']['args']['id'], $post_id );
				$args['args']['args']['value'] = $meta_value;
			}

			return $args;
		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */

		private function create_menu_items() {
			// Add a panel under YITH Plugins tab
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'ypop_premium_tab', array( $this, 'premium_tab' ) );
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
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );
			return $links;
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'yith_ypop_admin', YITH_YPOP_ASSETS_URL . '/js/backend' . $suffix . '.js', array( 'jquery', 'yith-plugin-fw-fields' ), YITH_YPOP_VERSION, true );
				wp_enqueue_style( 'yith_ypop_backend', YITH_YPOP_ASSETS_URL . '/css/backend.css', array( 'yith-plugin-fw-fields' ), YITH_YPOP_VERSION );
				wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_localize_script( 'yith_ypop_admin', 'ypop_backend', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
			if ( ypop_check_valid_admin_page( YITH_Popup()->post_type_name ) ) {
				wp_dequeue_style( 'ultimate-vc-backend-style' );
			}

			if ( ! wp_script_is( 'selectWoo' ) ) {
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );
				wp_enqueue_style( 'select2' );
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
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YPOP_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_YPOP_SLUG;
				$new_row_meta_args['is_premium'] = true;
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
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */

		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'popups'   => __( 'Popups', 'yith-woocommerce-popup' ),
				'settings' => __( 'Settings', 'yith-woocommerce-popup' ),
			);

			if ( defined( 'YITH_YPOP_FREE_INIT' ) ) {
				$admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-popup' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'YITH WooCommerce Popup', 'Plugin name. Do not translate.', 'yith-woocommerce-popup' ),
				'menu_title'       => _x( 'Popup', 'Plugin name. Do not translate.', 'yith-woocommerce-popup' ),
				'capability'       => 'manage_options',
				'parent'           => 'ypop',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YPOP_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel' ) ) {
				require_once YITH_YPOP_DIR . '/plugin-fw/lib/yit-plugin-panel.php';
			}

			$this->_panel = new YIT_Plugin_Panel( $args );
		}




		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function premium_tab() {
			$premium_tab_template = YITH_YPOP_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}



		/**Enable custom metabox type
		 *
		 * @author YITH
		 * @param $args
		 * @use yit_fw_metaboxes_type_args
		 * @return mixed
		 */
		public function add_custom_metaboxes( $field_template, $field ) {

			if ( 'iconlist' == $field['type'] ) {
				$field_template = YITH_YPOP_TEMPLATE_PATH . '/metaboxes/types/' . $field['type'] . '.php';
			}

			return $field_template;
		}


		public function change_status() {

			if ( ! isset( $_REQUEST['post_id'] ) ) {
				return false;
			}

			$post_id = $_REQUEST['post_id'];
			if ( $_REQUEST['status'] == 'enable' ) {
				$updated = update_post_meta( $post_id, '_enable_popup', 1 );
			} else {
				$updated = update_post_meta( $post_id, '_enable_popup', 0 );
			}

			echo $updated; //phpcs:ignore

			die();

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_YPOP_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_YPOP_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_YPOP_INIT, YITH_YPOP_SECRET_KEY, YITH_YPOP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_YPOP_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YPOP_SLUG, YITH_YPOP_INIT );
		}

		/**
		 * Add new plugin-fw style.
		 *
		 * @param $class
		 * @param $post
		 *
		 * @return string
		 */
		public function add_custom_metabox_class( $class, $post ) {

			$allow_post_types = array( 'yith_popup' );

			if ( in_array( $post->post_type, $allow_post_types ) ) {
				$class .= ' ' . yith_set_wrapper_class();
			}
			return $class;
		}

	}

	/**
	 * Unique access to instance of YITH_Popup_Admin class
	 *
	 * @return \YITH_Popup_Admin
	 */
	function YITH_Popup_Admin() {
		return YITH_Popup_Admin::get_instance();
	}
}
