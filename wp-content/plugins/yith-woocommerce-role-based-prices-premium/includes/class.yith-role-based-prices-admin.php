<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( ' YITH_Role_Based_Prices_Admin' ) ) {

	class YITH_Role_Based_Prices_Admin {

		protected static $instance;

		/**
		 * YITH_Role_Based_Prices_Admin constructor.
		 */
		public function __construct() {
			add_action( 'woocommerce_admin_field_select-customer-role', array( $this, 'show_custom_type' ) );
			add_action( 'woocommerce_admin_field_show-prices-user-role', array( $this, 'show_prices_user_type' ) );
			add_action( 'pre_update_option', array( $this, 'update_custom_message' ), 20, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_script' ) );
			add_action( 'save_post', array( $this, 'ywcrb_delete_role_based_transient' ), 10 ,1 );
			add_action( 'wp_trash_post', array( $this, 'ywcrb_delete_role_based_transient' ), 10 ,1 );
			add_action( 'untrash_post', array( $this, 'ywcrb_delete_role_based_transient' ), 10 ,1 );
			add_action( 'delete_post', array( $this, 'ywcrb_delete_role_based_transient' ), 10 ,1 );

			add_action( 'wp_ajax_delete_role_price_transient', array( $this, 'ywcrbp_ajax_delete_role_based_transient' ) );


		}

		/**
		 * Returns single instance of the class
		 * @author YITHEMES
		 * @return \YITH_Role_Based_Prices_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**show custom type
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function show_custom_type( $option ) {

			wc_get_template( 'admin/select-customer-role.php', array( 'option' => $option ), '', YWCRBP_TEMPLATE_PATH );
		}

		/**show custom type
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function show_prices_user_type( $option ) {

			wc_get_template( 'admin/show-prices-user-role.php', array( 'option' => $option ), '', YWCRBP_TEMPLATE_PATH );
		}

		/**
		 * add script and style in admin
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function include_admin_script() {
			if ( ! isset( $_GET['post'] ) ) {
				global $post;
			} else {
				$post = $_GET['post'];
			}
			$right_post_type = ( isset( $post ) && get_post_type( $post ) === 'yith_price_rule' ) || ( isset( $_GET['post_type'] ) && 'yith_price_rule' === $_GET['post_type'] ) || ( isset( $_GET['page'] ) && 'yith_vendor_role_based_prices_settings' === $_GET['page'] );
			$suffix          = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';

			wp_register_script( 'ywcrbp_admin', YWCRBP_ASSETS_URL . 'js/ywcrbp_admin' . $suffix . '.js', array( 'jquery' ), YWCRBP_VERSION );

			$js_params = array(
				'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'actions' => array(
					'delete_role_price_transient' => 'delete_role_price_transient',
				),
			);

			wp_localize_script( 'ywcrbp_admin', 'ywcrbp_panel', $js_params );


			wp_register_style( 'ywcrbp_style', YWCRBP_ASSETS_URL . 'css/ywrbp_admin.css', array(), YWCRBP_VERSION );


			if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcrbp_panel' ) ) {

				wp_enqueue_style( 'ywcrbp_style');
				wp_enqueue_script( 'ywcrbp_admin' );
			}

			if ( $right_post_type ) {

				wp_enqueue_style( 'ywcrbp_style' );

				if( !wp_script_is( 'wc-enhanced-select' ) ){

					$args = array( 'jquery', 'select2' );

					if( version_compare( WC()->version, '3.2.0', '>=' ) ){
						$args[] = 'selectWoo';
					}
					wp_enqueue_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', $args , WC_VERSION );
				}


			}
		}

		/**
		 * before update custom message, remove html tag
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $value
		 * @param $option
		 * @param $old_value
		 *
		 * @return string
		 */
		public function update_custom_message( $value, $option, $old_value ) {

			if ( 'ywcrbp_message_user' === $option ) {
				$value = htmlspecialchars( stripslashes( $value ) );
			}

			return $value;
		}

		public function ywcrb_delete_role_based_transient( $post_id ){

			$post_type = get_post_type( $post_id );
			$post_types = array( 'product','product_variation' );

			if( ( 'yith_price_rule' == $post_type ) || ( in_array( $post_type, $post_types )  ) ){

                ywcrbp_delete_transient();

			}
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.1.2
		 * delete role based price in ajax
		 */
		public function ywcrbp_ajax_delete_role_based_transient(){

			$res = ywcrbp_delete_transient();

			$message = apply_filters( 'ywcrbp_deleted_message', __( 'Deleted!', 'yith-woocommerce-role-based-prices' ) );

			wp_send_json( array( 'message' => $message ) );
		}
	}
}

/**
 *
 * @author YITHEMES
 * @return YITH_Role_Based_Prices_Admin
 * @since 1.0.0
 */
function YITH_Role_Based_Admin() {
	return YITH_Role_Based_Prices_Admin::get_instance();
}

