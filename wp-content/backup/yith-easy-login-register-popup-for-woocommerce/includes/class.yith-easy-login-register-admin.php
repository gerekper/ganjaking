<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register_Admin' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Register_Admin {

		/**
		 * @var $panel Panel Object
		 */
		protected $panel = null;

		/**
		 * @var string Plugin panel page
		 */
		protected $panel_page = 'yith_welrp_panel';

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			// Add panel options
			add_action( 'admin_menu', [ $this, 'register_panel' ], 5 );
			// Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WELRP_PATH . '/' . basename( YITH_WELRP_FILE ) ), [ $this, 'action_links' ] );
			add_filter( 'yith_show_plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 5 );

			// Register plugin to licence/update system
			add_action( 'wp_loaded', [ $this, 'register_plugin_for_activation' ], 99 );
			add_action( 'admin_init', [ $this, 'register_plugin_for_updates' ] );

			// enqueue custom css
			add_action( 'admin_enqueue_scripts', [ $this, 'custom_css' ], 99 );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0.0
		 * @author   Francesco Licandro <francesco.licandro@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'          => __( 'General Settings', 'yith-easy-login-register-popup-for-woocommerce' ),
				'first-step'       => __( 'First Step Options', 'yith-easy-login-register-popup-for-woocommerce' ),
				'login'            => __( 'Login Options', 'yith-easy-login-register-popup-for-woocommerce' ),
				'register'         => __( 'Register Options', 'yith-easy-login-register-popup-for-woocommerce' ),
				'lost-password'    => __( 'Lost Password Options', 'yith-easy-login-register-popup-for-woocommerce' ),
				'additional-popup' => __( 'Additional Popup', 'yith-easy-login-register-popup-for-woocommerce' ),
			);

			$args = array(
				'create_menu_page'   => true,
				'parent_slug'        => '',
				'page_title'         => _x( 'YITH Easy Login & Register Popup for WooCommerce', 'plugin name on options page', 'yith-easy-login-register-popup-for-woocommerce' ),
				'plugin_description' => _x( 'Makes the login, registration and password reset processes easier during the checkout and reduces the cart abandonment rate.', 'plugin description on options page', 'yith-easy-login-register-popup-for-woocommerce' ),
				'menu_title'         => _x( 'Easy Login Register Popup', 'plugin name in YITH menu', 'yith-easy-login-register-popup-for-woocommerce' ),
				'capability'         => 'manage_options',
				'parent'             => '',
				'parent_page'        => 'yith_plugin_panel',
				'page'               => $this->panel_page,
				'admin-tabs'         => $admin_tabs,
				'options-path'       => YITH_WELRP_PATH . '/plugin-options',
				'class'              => yith_set_wrapper_class(),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Custom admin css plugin
		 *
		 * @since   1.0.0
		 * @author  Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function custom_css() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->panel_page ) {
				$custom_css = "#yith_welrp_additional_popup_options-description p{background:none!important;font-weight:400!important;width:auto!important;padding:0 10px 0 0!important;}
                #yith_welrp_additional_popup_options-description p:before{display:none!important;}";
				wp_add_inline_style( 'yith-plugin-fw-fields', $custom_css );
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @since    1.0.0
		 * @author   Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $links | links plugin array
		 *
		 * @return   mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true );
			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since    1.0.0
		 * @author   Francesco Licandro <francesco.licandro@yithemes.com>
		 * @use      plugin_row_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @return   array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WELRP_INIT' ) && YITH_WELRP_INIT == $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_WELRP_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since    1.0.0
		 * @author   Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return   void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WELRP_INIT, YITH_WELRP_SECRET_KEY, YITH_WELRP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since    2.0.0
		 * @author   Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return   void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WELRP_SLUG, YITH_WELRP_INIT );
		}

	}
}
