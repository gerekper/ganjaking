<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multiple_Addresses_Shipping_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Multiple_Addresses_Shipping_Admin' ) ) {
	/**
	 * Class YITH_Multiple_Addresses_Shipping_Admin
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Multiple_Addresses_Shipping_Admin {

		/**
		 * @var Panel object
		 */
		protected $_panel = null;

		/**
		 * @var Panel page
		 */
		protected $_panel_page = 'yith_wcmas_panel';

		/**
		 * @var bool Show the premium landing page
		 */
		public $show_premium_landing = true;

		/**
		 * @var string Official plugin documentation
		 */
		protected $_official_documentation = 'http://docs.yithemes.com/yith-multiple-shipping-addresses-for-woocommerce/';

		/**
		 * @var string Official plugin landing page
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-multiple-shipping-addresses-for-woocommerce/';

		/**
		 * @var string Official plugin live demo
		 */
		protected $_premium_live = 'http://plugins.yithemes.com/yith-multiple-shipping-addresses-for-woocommerce/';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function __construct() {
			/* === Register Panel Settings === */
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			/* === Show Plugin Information === */
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMAS_PATH . '/' . basename( YITH_WCMAS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );


			add_action( 'wp_loaded', array ( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array ( $this, 'register_plugin_for_updates' ) );


			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'yith_wcmas_product_exclusion_table', array( $this, 'product_exclusion_table' ) );
			add_action( 'yith_wcmas_category_exclusion_table', array( $this, 'category_exclusion_table' ) );
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'shipping_item_details' ), 10, 3 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_shipping_item_meta' ) );
			add_action( 'woocommerce_before_save_order_items', array( $this, 'save_shipping_item_meta' ), 10, 2 );
			add_action( 'wp_ajax_ywcmas_admin_get_user_address', array( $this, 'get_user_address' ) );
			add_action( 'wp_ajax_ywcmas_save_shipping_item_address', array( $this, 'save_shipping_item_address' ) );
			add_action( 'wp_ajax_yith_wcmas_search_product_cat', array( $this, 'search_product_cat_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_wcmas_search_product_cat', array( $this, 'search_product_cat_ajax' ) );
			add_filter( 'manage_edit-shop_order_columns',  array( $this, 'add_mas_column' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_mas_column_content' ), 10, 2 );
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

			$menu_title = 'Multiple Shipping Addresses';

			$admin_tabs = apply_filters( 'yith_wcmas_admin_tabs', array(
					'settings' => esc_html__( 'Settings', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'product-exclusion' => esc_html__( 'Exclude products', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'cat-exclusion' => esc_html__( 'Exclude categories', 'yith-multiple-shipping-addresses-for-woocommerce' ),
				)
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_WCMAS_SLUG,
				'page_title'       => $menu_title,
				'menu_title'       => $menu_title,
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'class'            => yith_set_wrapper_class(),
				'options-path'     => YITH_WCMAS_OPTIONS_PATH
			);


			/* === Fixed: not updated theme/old plugin framework  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCMAS_PATH . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed array
		 * @since    1.0.0
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
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCMAS_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCMAS_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since    1.0.0
		 */
		public function register_plugin_for_activation () {

			if ( ! class_exists ( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WCMAS_PATH . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_WCMAS_PATH . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}

			YIT_Plugin_Licence()->register ( YITH_WCMAS_INIT, YITH_WCMAS_SECRETKEY, YITH_WCMAS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since    1.0.0
		 */
		public function register_plugin_for_updates () {
			if ( ! class_exists ( 'YIT_Upgrade' ) ) {
				require_once( YITH_WCMAS_PATH . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade ()->register ( YITH_WCMAS_SLUG, YITH_WCMAS_INIT );
		}

		/**
		 * Enqueue the scripts on admin pages
		 * Callback of action 'admin_enqueue_scripts'
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 * @param string $hook_suffix The current admin page.
		 */
		public function enqueue_scripts( $hook_suffix ) {
			wp_enqueue_script(
				'ywcmas-admin',
				YITH_WCMAS_ASSETS_JS_URL . yit_load_js_file( 'ywcmas-admin.js' ),
				array( 'jquery', 'jquery-blockui', 'jquery-ui-dialog', 'jquery-effects-scale', 'wc-admin-meta-boxes' ),
				YITH_WCMAS_VERSION );
			$params = array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'save_button'   => esc_html__( 'Save', 'yith-multiple-shipping-addresses-for-woocommerce' ),
				'cancel_button' => esc_html__( 'Cancel', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			);
			wp_localize_script( 'ywcmas-admin', 'ywcmas_admin', $params );

			wp_enqueue_style( 'ywcmas-admin-style',
				YITH_WCMAS_ASSETS_URL . 'css/ywcmas-admin.css',
				array(),
				YITH_WCMAS_VERSION );
		}

		/**
		 * Prints product exclusion table
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
		 */
		public function product_exclusion_table() {
			if( file_exists( YITH_WCMAS_TEMPLATE_PATH . 'admin/excluded-products-tab.php' ) ) {
				include_once( YITH_WCMAS_TEMPLATE_PATH . 'admin/excluded-products-tab.php' );
			}
		}

		/**
		 * Prints category exclusion table
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
		 */
		public function category_exclusion_table() {
			if( file_exists( YITH_WCMAS_TEMPLATE_PATH . 'admin/excluded-categories-tab.php' ) ) {
				include_once( YITH_WCMAS_TEMPLATE_PATH . 'admin/excluded-categories-tab.php' );
			}
		}


		/**
		 * Show the shipping address of the shipping item and the status selector
		 * See action 'woocommerce_before_order_itemmeta'
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 * @param int $item_id int
		 * @param WC_Order_Item_Product|WC_Order_Item_Shipping $item
		 * @param WC_Product_Simple|null $type
		 */
		public function shipping_item_details( $item_id, $item, $type ) {
			$destination = $item->get_meta( 'ywcmas_shipping_destination' );
			$shipping_status  = $item->get_meta( 'ywcmas_shipping_status' );
			if ( is_null( $type ) && $destination ) {
				ob_start();

				wc_get_template( 'admin/shipping-item-details.php',
                    array(
                            'item_id' => $item_id,
                            'item' => $item,
                            'destination' => $destination,
                            'shipping_status' => $shipping_status
                    ),
                    '',
                    YITH_WCMAS_TEMPLATE_PATH );

				echo ob_get_clean();
            }
		}

		/**
		 * Filter hidden_order_itemmeta array for hiding shipping item metas
		 * See filter 'woocommerce_hidden_order_itemmeta'
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 * @param array $hidden_order_itemmeta The array of order item meta that should be hidden
		 * @return array The array of the filter
		 */
		public function hide_shipping_item_meta( $hidden_order_itemmeta ) {
			$hidden_order_itemmeta[] = 'ywcmas_shipping_destination';
			$hidden_order_itemmeta[] = 'ywcmas_shipping_status';
			return $hidden_order_itemmeta;
		}

		/**
		 * Save the shipping item meta of the plugin
		 * See action 'woocommerce_before_save_order_items'
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 * @param int $order_id
		 * @param array $items $_POST array with the Edit Order form fields
		 */
		public function save_shipping_item_meta( $order_id, $items ) {
			$order = wc_get_order( $order_id );
			if ( ! empty( $items['ywcmas_shipping_status'] ) ) {
				foreach ( $items['ywcmas_shipping_status'] as $shipping_item_id => $item_status ) {
					if ( ! $shipping_item = $order->get_item( absint( $shipping_item_id ) ) )
						continue;
					$old_status = $shipping_item->get_meta( 'ywcmas_shipping_status' );
					$new_status = $items['ywcmas_shipping_status'][$shipping_item_id];
					$contents = $shipping_item->get_meta( 'ywcmas_shipping_contents' );
					if ( $old_status != $new_status ) {
						$shipping_item->update_meta_data( 'ywcmas_shipping_status', $new_status, $shipping_item_id );
						WC()->mailer();
						do_action( 'ywcmas_shipping_status_change_email', $order_id, $shipping_item_id, $old_status, $new_status, $contents );
					}
					$shipping_item->save();
				}
			}
		}

		/**
		 * Get the address data of a customer by its address id
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function get_user_address() {
			$address_id = ! empty( $_POST['address_id'] ) ? $_POST['address_id'] : '';
			$order_id = ! empty( $_POST['order_id'] ) ? $_POST['order_id'] : '';

			if ( ! $address_id || ! $order_id ) {
				wp_send_json_error();
			}
			$order = wc_get_order( $order_id );
			$user_id = $order->get_customer_id();

			$address = yith_wcmas_get_user_address_by_id( $address_id, $user_id );
			wp_send_json_success( $address );
		}

		/**
		 * Update the address of a shipping item
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function save_shipping_item_address() {
			$shipping_id = ! empty( $_POST['shipping_id'] ) ? $_POST['shipping_id'] : '';
			$order_id = ! empty( $_POST['order_id'] ) ? $_POST['order_id'] : '';
			$address = ! empty( $_POST['address'] ) ? $_POST['address'] : '';
			if ( ! $shipping_id || ! $order_id || ! $address )
				wp_send_json_error();
			$order = wc_get_order( $order_id );
			if ( ! $shipping_item = $order->get_item( absint( $shipping_id ) ) )
				wp_send_json_error();

			$destination = $shipping_item->get_meta( 'ywcmas_shipping_destination' );
			foreach ( $address as $field_id => $value ) {
				$destination[$field_id] = $value;
			}

			$shipping_item->update_meta_data( 'ywcmas_shipping_destination', $destination, $shipping_id );
			$shipping_item->save();
			wp_send_json_success();
		}

		/**
		 * Search product categories for select2
         *
         * @author Francesco Licandro
         * @since 1.0.0
		 */
		public function search_product_cat_ajax(){
			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'orderby'           => 'name',
				'order'             => 'ASC',
				'hide_empty'        => false,
				'exclude'           => array(),
				'exclude_tree'      => array(),
				'include'           => array(),
				'number'            => '',
				'fields'            => 'all',
				'slug'              => '',
				'parent'            => '',
				'hierarchical'      => true,
				'child_of'          => 0,
				'childless'         => false,
				'get'               => '',
				'name__like'        => $term,
				'pad_counts'        => false,
				'offset'            => '',
				'search'            => '',
			);

			$terms = get_terms( 'product_cat', $args);
			$found_categories = array();

			if ( $terms ) {
				foreach ( $terms as $term ) {
					$found_categories[ $term->term_id ] = rawurldecode( $term->name );
				}
			}

			wp_send_json( $found_categories );
		}

		/**
         * Adds the column Multi Shipping in Orders table
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         *
		 * @param $column_array
		 *
		 * @return mixed
		 */
		public function add_mas_column( $column_array ) {
			$column_array['yith_mas'] = esc_html__( 'Multi Shipping', 'yith-multiple-shipping-addresses-for-woocommerce' );
			return $column_array;
        }

		/**
         * Adds the content for the column Multi Shipping
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         *
		 * @param $column_name
		 * @param $post_id
		 */
		public function add_mas_column_content( $column_name, $post_id ) {
			if ( 'yith_mas' != $column_name ) {
			    return;
			}
			$order = wc_get_order( $post_id );
			if ( yith_wcmas_order_has_multi_shipping( $order ) ) {
				echo '<img src="' . YITH_WCMAS_ASSETS_URL . 'images/check-circle.png' . '"> </img>';
			}
		}

	}
}