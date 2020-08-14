<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Sequential_Order_Number_Admin' ) ) {

	class YITH_Sequential_Order_Number_Admin {

		/**
		 * @var YITH_Sequential_Order_Number_Admin $instance
		 */
		protected static $instance;
		/**
		 * @var YITH_Panel $_panel
		 */
		protected $_panel;
		/**
		 * @var string $_panel_page
		 */
		protected $_panel_page = 'yith_wc_sequential_order_number_panel';

		public function __construct() {

			add_filter( 'plugin_action_links_' . plugin_basename( YWSON_DIR . '/' . basename( YWSON_FILE ) ), array(
				$this,
				'action_links'
			), 5 );
			//add row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			//  Add action menu
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );

			add_action( 'ywson_tools_tab', array( $this, 'print_tools_tab' ) );

			add_action( 'wp_ajax_import_old_order_number', array( $this, 'import_old_order_number' ) );

			add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'add_custom_search_fields' ) );

			//Add compatibility with Quick Export
			add_filter( 'yith_quick_export_orders_columns_order', array(
				$this,
				'add_export_orders_column_sequential_order'
			) );
		}

		/**
		 * Returns single instance of the class
		 * @author Salvatore Strano
		 * @return YITH_Sequential_Order_Number_Admin
		 * @since 1.1.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
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
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWSON_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YWSON_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YWSON_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YWSON_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWSON_INIT, YWSON_SECRET_KEY, YWSON_SLUG );
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
				require_once( YWSON_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWSON_SLUG, YWSON_INIT );
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
		public function add_menu_page() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general' => __( 'Modules', 'yith-woocommerce-sequential-order-number' ),
				'tools'   => __( 'Tools', 'yith-woocommerce-sequential-order-number' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class' => yith_set_wrapper_class(),
				'page_title'       => __( 'Sequential Order Number', 'yith-woocommerce-sequential-order-number' ),
				'menu_title'       => 'Sequential Order Number',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWSON_DIR . '/plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * add script in admin
		 * @author YITHEMES
		 * @since 1.1.0
		 * @use admin_enqueue_scripts
		 */
		public function enqueue_admin_script() {
			wp_register_style( 'yith_sequential_order_number', YWSON_ASSETS_URL . 'css/ywson_admin.css', array(), YWSON_VERSION );
			wp_register_script( 'yith_sequential_order_number', YWSON_ASSETS_URL . 'js/'.yit_load_js_file('ywson_admin.js'), array(
				'jquery',
				'jquery-ui-dialog'
			), YWSON_VERSION, true );

			$yith_son_params = array(
				'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'actions'  => array(
					'import_old_order_number' => 'import_old_order_number',
				)
			);

			wp_localize_script( 'yith_sequential_order_number', 'yith_son_params', $yith_son_params );

			if ( isset( $_GET['page'] ) && 'yith_wc_sequential_order_number_panel' == $_GET['page'] ) {
				wp_enqueue_style( 'yith_sequential_order_number' );
				wp_enqueue_script( 'yith_sequential_order_number' );

			}

		}


		/**
		 * include the custom tab layout
		 * @author Salvatore Strano
		 * @since 1.1.0
		 */
		public function print_tools_tab() {

			require_once( YWSON_DIR . '/templates/admin/tools-tab.php' );
		}

		/**
		 * import the custom order number generated by WooCommerce Sequential Order Numbers plugin
		 * @author Salvatore Strano
		 * @since 1.1.0
		 */
		public function import_old_order_number() {

			check_ajax_referer( 'ywson-import-numbers', 'security' );


			add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'ywson_get_order_with_custom_order_number', 20, 2 );
			$page = 0;

			$order_args = apply_filters( 'ywson_order_args', array(
				'_ywson_custom_number_order_complete' => '',
				'page'                                => $page,
				'limit'                               => 15
			) );
			$orders     = wc_get_orders( $order_args );
			$tot_orders = 0;

			while ( count( $orders ) > 0 ) {

				foreach ( $orders as $order ) {

					$old_meta = $order->get_meta( '_order_number_formatted' );

					if ( empty( $old_meta ) ) {
						$old_meta = $order->get_id();
					}
					$order->add_meta_data( '_ywson_custom_number_order_complete', $old_meta );

					$order->save();
					$tot_orders ++;
				}

				$order_args['page'] = $page ++;
				$orders             = wc_get_orders( $order_args );

			}

			remove_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'ywson_get_order_with_custom_order_number', 20 );

			$message = sprintf( '%d %s ', $tot_orders, _n( 'Order imported', 'Orders imported', $tot_orders, 'yith-woocommerce-sequential-order-number' ) );
			wp_send_json( array( 'message' => $message ) );
		}

		/**add custom order meta to search fields
		 * @author Salvatore Strano
		 * @since 1.1.0
		 * @param array $search_fields
		 *
		 * @return array
		 * @use woocommerce_shop_order_search_fields
		 */
		public function add_custom_search_fields( $search_fields ) {

			$search_fields[] = '_ywson_custom_number_order_complete';
			$search_fields[] = 'ywson_custom_quote_number_order';

			return $search_fields;
		}

		/**
		 * add custom column to export
		 * @author Salvatore Strano
		 * @since 1.1.0
		 * @param array $columns
		 *
		 * @return array
		 */
		public function add_export_orders_column_sequential_order( $columns ) {

			$new_colum = array( '_ywson_custom_number_order_complete', 'ywson_custom_quote_number_order' );
			$columns   = array_merge( $new_colum, $columns );

			return $columns;
		}

		public function get_parent_order_id( $query ){
			$vendor = yith_get_vendor( 'current', 'user' );

			if(  is_admin() && $vendor->is_valid() && $vendor->has_limited_access() ) {
				if ( isset( $_GET['s'] ) ) {

					$parent_ids = wc_order_search( $_GET['s'] );
					if ( count( $parent_ids ) > 1 ) {
						$post_in = array();
						foreach ( $parent_ids as $parent_id ) {
							$suborders_ids = YITH_Orders::get_suborder( $parent_id );
							$post_in       = array_merge( $post_in, $suborders_ids );
						}
						$quotes = array();
						if ( 'no' == get_option( 'yith_wpv_vendors_enable_request_quote', 'no' ) && ! empty( YITH_Vendors()->addons ) && YITH_Vendors()->addons->has_plugin( 'request-quote' ) ) {
							$quotes = $vendor->get_orders( 'quote', YITH_YWRAQ_Order_Request()->raq_order_status );
						}
						$query['post__in'] = ! empty( $quotes ) ? array_diff( $post_in, $quotes ) : $post_in;
					} else {
						$query['post_parent'] = current( $parent_ids );
					}
					$query['s'] = '';


				}
			}

			return $query;
		}

	}
}

function YITH_Sequential_Order_Admin() {
	return YITH_Sequential_Order_Number_Admin::get_instance();
}
