<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_YWBC_Frontend' ) ) {

	/**
	 *
	 * @class   YITH_YWBC_Frontend
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWBC_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;


		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {


			$this->init_hooks ();
		}

		/**
		 * Initialize all hooks used by the plugin affecting the front-end behaviour
		 */
		public function init_hooks() {
			add_action ( 'wp_enqueue_scripts', array(
				$this,
				'enqueue_scripts'
			) );

			add_action ( 'wp_enqueue_scripts', array(
				$this,
				'enqueue_style'
			) );

			add_action ( 'woocommerce_order_details_after_order_table', array(
				$this,
				'show_barcode_on_view_order_page'
			) );

			/**
			 * Show the barcode on front-end product page
			 */
			add_action ( 'woocommerce_single_product_summary', array(
				$this,
				'show_barcode_on_single_product_page'
			), 25 );

			add_filter ( 'woocommerce_available_variation', array(
				$this,
				'show_variation_barcode'
			), 10, 3 );


		}



		/**
		 * @param             $args
		 * @param             $product_variable
		 * @param  WC_Product $variation Variation product object or ID
		 *
		 * @return mixed
		 */
		public function show_variation_barcode( $args, $product_variable, $variation ) {

			$barcode = YITH_Barcode::get ( $variation->get_id () );
			if ( $barcode->exists () ) {

				$args['barcode_img']   = esc_attr ( YITH_YWBC ()->get_public_file_path ( $barcode ) );
				$args['barcode_value'] = $barcode->get_display_value ();
			}

			return $args;
		}

		/**
		 * Enqueue scripts for the front-end
		 *
		 */
		public function enqueue_scripts() {
			//  register and enqueue ajax calls related script file
			wp_register_script ( "ywbc-frontend",
                apply_filters( 'yith_ywbc_enqueue_frontend_script_source_path', YITH_YWBC_SCRIPTS_URL . yit_load_js_file ( 'ywbc-frontend.js' ) ),
				array(
					'jquery',
				),
				YITH_YWBC_VERSION,
				true );

			wp_localize_script ( 'ywbc-frontend',
				'ywbc_data',
				array(
					'loader'   => apply_filters ( 'yith_barcodes_loader', YITH_YWBC_ASSETS_URL . '/images/loading.gif' ),
					'ajax_url' => admin_url ( 'admin-ajax.php' ),
				) );

			wp_enqueue_script ( "ywbc-frontend" );
		}

		/**
		 * Enqueue style for the front-end
		 *
		 */
		public function enqueue_style() {
			if ( ( YITH_YWBC ()->show_on_product_page && is_product () ) ||
			     ( YITH_YWBC ()->show_on_order_page && is_view_order_page () )
			) {
				wp_enqueue_style ( 'ywbc-style',
					YITH_YWBC_ASSETS_URL . '/css/ywbc-style.css',
					array(),
					YITH_YWBC_VERSION );
			}
		}

		/**
		 * Show the order barcode on order page
		 *
		 * @param WC_Order $order the order being shown
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_barcode_on_view_order_page( $order ) {
			if ( ! YITH_YWBC ()->show_on_order_page ) {
				return;
			}

			YITH_YWBC ()->show_barcode ( yit_get_prop ( $order, 'id' ), true );
		}

		/**
		 * Show the barcode on the product page
		 */
		public function show_barcode_on_single_product_page() {
			if ( ! YITH_YWBC ()->show_on_product_page ) {
				return;
			}

			global $product;
			$product_id = yit_get_product_id( $product );
			$show_container = $product->is_type ( 'variable' );

			if( defined( 'ICL_SITEPRESS_VERSION' ) &&  apply_filters( 'yith_ywbc_use_original_product_barcode', false ) ){
				global $sitepress;

				$product_id = yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() );
			}

			?><div style="width: 50%"><?php
			YITH_YWBC ()->show_barcode ( $product_id, true, '', '', $show_container );
			?></div><?php
		}
	}
}
YITH_YWBC_Frontend::get_instance ();
