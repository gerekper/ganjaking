<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Save_For_Later_Premium' ) ) {

	class YITH_WC_Save_For_Later_Premium extends YITH_WC_Save_For_Later {

		/**static instance of the class
		 * @var YITH_WC_Save_For_Later_Premium
		 */
		protected static $instance;

		public function __construct() {

			parent::__construct();

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_filter( 'ywsfl_add_plugin_tab', array( $this, 'add_premium_tab' ), 20, 1 );
			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'redirect_to_cart' ), 10, 2 );


			$show_button_in_product_page = get_option( 'ywsfl_show_button_single_product' );

			if ( 'yes' == $show_button_in_product_page ) {

				add_action( 'wp_enqueue_scripts', array( $this, 'include_single_product_script' ), 20 );
				add_action( 'woocommerce_single_product_summary', array( $this, 'show_single_product_page' ), 35 );

				add_action( 'wp_ajax_check_if_variation_is_in_list', array(
					$this,
					'variation_in_save_for_later_list'
				) );
				add_action( 'wp_ajax_nopriv_check_if_variation_is_in_list', array(
					$this,
					'variation_in_save_for_later_list'
				) );
				add_action( 'wp_ajax_add_single_product_save_list', array(
					$this,
					'ajax_add_single_product_save_list'
				) );
				add_action( 'wp_ajax_nopriv_add_single_product_save_list', array(
					$this,
					'ajax_add_single_product_save_list'
				) );

				add_action( 'wp_ajax_remove_after_add_list', array( $this, 'remove_from_cart_after_save_list' ) );
				add_action( 'wp_ajax_nopriv_remove_after_add_list', array( $this, 'remove_from_cart_after_save_list' ) );
			}


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
				require_once YWSFL_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YWSFL_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWSFL_INIT, YWSFL_SECRET_KEY, YWSFL_SLUG );
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
				require_once( YWSFL_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWSFL_SLUG, YWSFL_INIT );
		}

		/**Returns single instance of the class
		 * @return YITH_WooCommerce_Save_for_Later_Premium
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function add_premium_tab( $tabs ) {

			unset( $tabs['premium-landing'] );

			$tabs['single-product'] = __( 'Product Settings', 'yith-woocommerce-save-for-later' );

			return $tabs;
		}


		/**
		 * redirect to cart after "Add to cart" button pressed on savelist table
		 *
		 * @param $url string Original redirect url
		 *
		 * @return string Redirect url
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_product_add_to_cart_url
		 */
		public function redirect_to_cart( $url, $product ) {

			global $yith_wsfl_is_savelist;


			if ( $yith_wsfl_is_savelist ) {

				if ( ! ( defined( 'AJAX_DOING' ) && DOING_AJAX ) ) {
					$product_id = yit_get_product_id( $product );
					$url        = add_query_arg(
						array(
							'remove_from_savelist' => $product_id,
						),
						$url
					);
				}
			}


			return apply_filters( 'yit_wsfl_add_to_cart_redirect_url', esc_url( $url ) );
		}


		/**
		 * @param $variation_id
		 */
		public function remove_no_available_product_form_save_list( $product_id, $variation_id ) {

			$user_id = is_user_logged_in() ? get_current_user_id() : false;

			if( $user_id ){

				global  $wpdb;
				$sql       = "DELETE FROM {$wpdb->yith_wsfl_table} WHERE {$wpdb->yith_wsfl_table}.user_id=%d AND {$wpdb->yith_wsfl_table}.product_id=%d";
				$sql_parms = array(
					$user_id,
					$product_id
				);

				if( $variation_id > 0 ){
					$sql .= " AND {$wpdb->yith_wsfl_table}.variation_id=%d";

					$sql_parms[] = $variation_id;
				}

				$wpdb->query( $wpdb->prepare( $sql, $sql_parms ) );
			}else{
				$savelist_cookie = yith_getcookie( 'yith_wsfl_savefor_list' );
				$key_to_check = 'product_id';
				if( $variation_id  > 0){
					$product_id = $variation_id;
					$key_to_check = 'variation_id';
				}

				foreach ( $savelist_cookie as $key => $item ) {
					if ( isset( $item[$key_to_check] ) && $product_id == $item[ $key_to_check ]  ) {
						unset( $savelist_cookie[ $key ] );
						break;
					}
				}
				yith_setcookie( 'yith_wsfl_savefor_list', $savelist_cookie );
			}
		}

		public function include_single_product_script() {
			if ( is_product() ) {

				wp_enqueue_script( 'ywsfl_single_product', YWSFL_ASSETS_URL . '/js/' . yit_load_js_file( 'ywsfl_single_product.js' ), array( 'jquery' ), YWSFL_VERSION, true );
				$ywsfl_single_param = array(
					'actions' => array(
						'add_single_product_save_list' => 'add_single_product_save_list',
						'remove_after_add_list'        => 'remove_after_add_list',
						'remove_from_savelist'         => 'remove_from_savelist'
					),

					'ajax_url'  => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'view_list' => array(
						'label' => __( 'View List', 'yith-woocommerce-save-for-later' ),
						'url'   => get_post_permalink( get_option( 'yith-wsfl-page-id' ) )
					)
				);
				wp_localize_script( 'ywsfl_single_product', 'ywsfl_single_product_args', $ywsfl_single_param );
				wp_enqueue_style( 'ywsfl_single_product_style', YWSFL_ASSETS_URL . 'css/ywsfl_singleproduct.css', array(), YWSFL_VERSION );

			}
		}

		public function show_single_product_page() {

			global $product;

			if ( ( $product instanceof WC_Product ) && $product->is_type( array( 'simple', 'variable' ) ) ) {
				wc_get_template( 'single-product/saveforlater-button.php', array(), '', YWSFL_TEMPLATE_PATH );
			}
		}


		public function ajax_add_single_product_save_list() {

			$product_id      = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : - 1;
			$variation_id    = ! empty( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : - 1;
			$variation       = isset( $_REQUEST['variation'] ) ? $_REQUEST['variation'] : array();
			$last_item_id = $this->add( $product_id, $variation_id, 1, '', $variation );
				if ( $last_item_id ) {
					$message = yith_save_for_later_get_message( 'added' );
					$type    = 'success';
				} else {
					$message = yith_save_for_later_get_message( 'error' );
					$type    = 'error';
				}

			ob_start();
			wc_print_notice( $message, $type );
			$message = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				'last_item_id' => $last_item_id,
				'template'     => YITH_WSFL_Shortcode::saveforlater( array() ),
				'notice'       => $message
			) );
		}

		public function variation_in_save_for_later_list() {

			$product_id = isset( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;

			$variation_id = isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : - 1;
			$variation    = isset( $_REQUEST['variation'] ) ? $_REQUEST['variation'] : array();

			$is_in_save_list = $this->is_variation_in_savelist( $product_id, $variation_id, $variation );

			wp_send_json( array( 'in_save_list' => $is_in_save_list ) );
		}

		public function is_variation_in_savelist( $product_id, $variation_id, $variation ) {

			$user_id = is_user_logged_in() ? get_current_user_id() : false;

			if ( $user_id ) {

				global $wpdb;

				$query = "SELECT ID, variations FROM {$wpdb->yith_wsfl_table} WHERE {$wpdb->yith_wsfl_table}.user_id = %d AND {$wpdb->yith_wsfl_table}.product_id = %d AND {$wpdb->yith_wsfl_table}.variation_id = %d  ";

				$query = $wpdb->prepare( $query, $user_id, $product_id, $variation_id );

				$results = $wpdb->get_results( $query, ARRAY_A );

				$found = false;
				foreach ( $results as $result ) {

					$current_variation = maybe_unserialize( $result['variations'] );
					foreach ( $current_variation as $attribute_key => $value ) {

						if ( ! isset( $variation[ $attribute_key ] ) || $value !== $variation[ $attribute_key ] ) {
							$found = false;
							break;
						} else {
							$found = true;
						}
					}

					if ( $found ) {
						return $result['ID'];
					}
				}
			} else {
				$savelist_cookie = yith_getcookie( 'yith_wsfl_savefor_list' );
				$found           = false;
				foreach ( $savelist_cookie as $key => $item ) {

					$item_product_id   = $item['product_id'];
					$item_variation_id = $item['variation_id'];
					$item_variations   = maybe_unserialize( $item['variations'] );

					if ( $product_id == $item_product_id && $item_variation_id == $variation_id ) {

						foreach ( $item_variations as $attribute_key => $value ) {

							if ( isset( $variation[ $attribute_key ] ) && $value == $variation[ $attribute_key ] ) {
								$found = true;
							} else {
								$found = false;
							}
						}
						if ( $found ) {
							return $key;
						}
					}
				}
			}

			return false;
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
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWSFL_INIT' ) {

			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		public function remove_from_cart_after_save_list(){

			if( isset( $_REQUEST['product_id'] ) && !is_null( WC()->cart )  ){

				$product_id = $_REQUEST['product_id'];
				$variation_id = !empty( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : 0;
				$variation = !empty( $_REQUEST['variation'] ) ? $_REQUEST['variation'] : array();
				$cart_item_data = array();

				$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

				$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

				wp_send_json( array( 'cart_item_key' => $cart_item_key, 'result' => true ) );


			}
		}
	}
}