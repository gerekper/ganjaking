<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Role_Based_Prices' ) ) {

	class YITH_Role_Based_Prices {

		/**
		 * @var YITH_Role_Based_Prices instance
		 */
		protected static $instance;

		/**
		 * @var YIT_Plugin_Panel_WooCommerce role based prices panel
		 */
		protected $_panel;

		/**
		 * @var string panel name instance
		 */
		protected $_panel_page = 'yith_wcrbp_panel';


		/**
		 * YITH_Role_Based_Prices constructor.
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 25 );
			add_filter( 'plugin_action_links_' . plugin_basename( YWCRBP_DIR . '/' . basename( YWCRBP_FILE ) ), array(
				$this,
				'action_links'
			) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			//manage plugin activation license
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			add_action( 'admin_menu', array( $this, 'add_plugin_menu' ), 5 );
			add_filter( 'yit_plugin_panel_sidebar_widgets', array( $this, 'add_role_based_widget' ), 10, 2 );

			YITH_Role_Based_Type();

			if ( is_admin()  ) {

				$this->admin = YITH_Role_Based_Admin();
			}

			$this->role_base_product = YITH_Role_Based_Prices_Product();

			add_action( 'ywcrbp_price_rules', array( $this, 'show_price_rule_tab' ) );


		}

		/**
		 * return single instance
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_Role_Based_Prices
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * load plugin framework 2.0
		 * @author YITHEMES
		 * @since 1.0.0
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

			$links  = yith_add_action_links( $links, $this->_panel_page, true );
			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status , $init_file = 'YWCRBP_INIT') {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YWCRBP_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}
			return $new_row_meta_args;
		}



		/** Register plugins for activation tab
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@YIThemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YWCRBP_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YWCRBP_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}
			YIT_Plugin_Licence()->register( YWCRBP_INIT, YWCRBP_SECRET_KEY, YWCRBP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( YWCRBP_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWCRBP_SLUG, YWCRBP_INIT );
		}


		/**
		 * add Role Based Prices menu in YIT Plugin
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_plugin_menu() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general-settings' => __( 'General settings', 'yith-woocommerce-role-based-prices' ),
				'user-settings'    => __( 'User settings', 'yith-woocommerce-role-based-prices' ),
				'price-rules'      => __( 'Price rules', 'yith-woocommerce-role-based-prices' ),
				'text-role'        => __( 'Label settings', 'yith-woocommerce-role-based-prices' )
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Role based prices', 'yith-woocommerce-role-based-prices' ),
				'menu_title'       =>  'Role based prices',
				'capability'       => apply_filters( 'ywcrpb_change_capability', 'manage_options' ),
				'parent'           => 'yith-woocommerce-role-based-prices',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,

				'options-path'     => YWCRBP_DIR . '/plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YWCRBP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}



		/**
		 * @author YITHEMES
		 * @since 1.0.4
		 *
		 * @param array $widgets
		 * @param string $page_name
		 *
		 * @return array
		 */
		public function add_role_based_widget( $widgets, $page_name ) {

			if ( $page_name === $this->_panel_page ) {

				$widgets['yith_role_based_prices'] = array(
					'title'       => __( 'Role-based Prices Premium has new options !', 'yith-woocommerce-role-based-prices' ),
					'title_class' => 'orange center dashicons dashicons-update',
					'content'     => wc_get_template_html( 'sidebar_widget/yith-role-based-new-options.php', array(), '', YWCRBP_TEMPLATE_PATH ),
					'priority'    => 29,
					'expiration'  => '2016-05-31'
				);
			}

			return $widgets;
		}

		/**
		 * show all price rule
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function show_price_rule_tab() {

			wc_get_template( 'admin/price-rules.php', array(), '', YWCRBP_TEMPLATE_PATH );
		}


	}
}
