<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined ( 'YITH_WCMG' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists ( 'YITH_WCMG_Frontend' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMG_Frontend {
		
		
		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			
			// add the action only when the loop is initializate
			add_action ( 'template_redirect', array( $this, 'render' ) );
		}
		
		public function render() {
			if ( yith_wcmg_is_enabled () && ! apply_filters ( 'yith_wczm_featured_video_enabled', false ) ) {
				
				//change the templates
				remove_action ( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				remove_action ( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
				add_action ( 'woocommerce_before_single_product_summary', array( $this, 'show_product_images' ), 20 );
				add_action ( 'woocommerce_product_thumbnails', array( $this, 'show_product_thumbnails' ), 20 );
				
				//custom styles and javascripts
				add_action ( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
				
				//add attributes to product variations
				add_filter ( 'woocommerce_available_variation', array( $this, 'available_variation' ), 10, 3 );
			}
		}
		
		
		/**
		 * Change product-single.php template
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function show_product_images() {
			
			/** FIX WOO 2.1 */
			$wc_get_template = function_exists ( 'wc_get_template' ) ? 'wc_get_template' : 'woocommerce_get_template';
			
			$wc_get_template( 'single-product/product-image-magnifier.php', array(), '', YITH_YWZM_DIR . 'templates/' );
		}
		
		
		/**
		 * Change product-thumbnails.php template
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function show_product_thumbnails() {
			
			/** FIX WOO 2.1 */
			$wc_get_template = function_exists ( 'wc_get_template' ) ? 'wc_get_template' : 'woocommerce_get_template';
			
			$wc_get_template( 'single-product/product-thumbnails-magnifier.php', array(), '', YITH_YWZM_DIR . 'templates/' );
		}
		
		
		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {
			global $post;
			
			wp_register_script ( 'ywzm-magnifier',
			  apply_filters( 'ywzm_magnifier_script_register_path', YITH_WCMG_URL . 'assets/js/' . yit_load_js_file ( 'yith_magnifier.js' ) ),
				array( 'jquery' ),
				YITH_YWZM_VERSION,
				true );

            wp_localize_script( 'ywzm-magnifier', 'yith_wc_zoom_magnifier_storage_object', apply_filters( 'yith_wc_zoom_magnifier_front_magnifier_localize', array(
                'ajax_url'    => admin_url( 'admin-ajax.php' ),
            ) ) );
			
			wp_register_script ( 'ywzm_frontend',
				YITH_WCMG_URL . 'assets/js/' . yit_load_js_file ( 'ywzm_frontend.js' ),
				array(
					'jquery',
					'ywzm-magnifier',
				),
				YITH_YWZM_VERSION,
				true );
			
			wp_register_style ( 'ywzm-magnifier', YITH_WCMG_URL . 'assets/css/yith_magnifier.css' );
			
			if ( is_product () || ( ! empty( $post->post_content ) && strstr ( $post->post_content, '[product_page' ) ) ) {
				
				$suffix = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
				wp_localize_script ( 'ywzm_frontend',
					'ywzm_data', array(
						'wc_before_3_0' => version_compare ( WC ()->version, '3.0', '<' ),
						'expand_label'  => apply_filters ( 'yith_zoom_magnifier_expand_image_label', esc_html__( 'Expand the image', 'yith-woocommerce-zoom-magnifier' ) ),
					)
				);
				
				//  Enqueue PrettyPhoto style and script
				$wc_assets_path = str_replace ( array( 'http:', 'https:' ), '', WC ()->plugin_url () ) . '/assets/';
				
				//  Enqueue scripts
				wp_enqueue_script ( 'prettyPhoto', $wc_assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.6', true );
				wp_enqueue_script ( 'ywzm-magnifier-slider' );
				wp_enqueue_script ( 'ywzm-magnifier' );
				wp_enqueue_script ( 'ywzm_frontend' );
				
				//  Enqueue Style
				$css = file_exists ( get_stylesheet_directory () . '/woocommerce/yith_magnifier.css' ) ? get_stylesheet_directory_uri () . '/woocommerce/yith_magnifier.css' : YITH_WCMG_URL . 'assets/css/frontend.css';
				wp_enqueue_style ( 'ywzm-prettyPhoto', $wc_assets_path . 'css/prettyPhoto.css' );
				wp_enqueue_style ( 'ywzm-magnifier' );
				wp_enqueue_style ( 'ywzm_frontend', $css );
			}
		}
		
		/**
		 * Add attributes to product variations
		 *
		 * @param array                $data
		 * @param WC_Product_Variable  $wc_prod
		 * @param WC_Product_Variation $variation
		 *
		 * @return mixed
		 */
		public function available_variation( $data, $wc_prod, $variation ) {
			
			$attachment_id = get_post_thumbnail_id ( version_compare ( WC ()->version, '3.0', '<' ) ? $variation->get_variation_id () : $variation->get_id () );
			$attachment    = wp_get_attachment_image_src ( $attachment_id, 'shop_magnifier' );
			
			$data['image_magnifier'] = $attachment ? current ( $attachment ) : '';
			
			return $data;
		}
	}
}