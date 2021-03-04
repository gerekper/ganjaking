<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Additional_Variation_Images_Frontend {
	private static $_this;

	/**
	 * init
	 *
	 * @since 1.0.0
	 * @version 1.7.9
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		}

		add_action( 'wc_ajax_wc_additional_variation_images_get_images', array( $this, 'ajax_get_images' ) );
	}

	/**
	 * public function to get instance
	 *
	 * @since 1.1.1
	 * @version 1.7.9
	 * @return instance object
	 */
	public static function get_instance() {
		return self::$_this;
	}

	/**
	 * load frontend scripts
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wc_additional_variation_images_script', plugins_url( 'assets/js/variation-images-frontend' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ), WC_ADDITIONAL_VARIATION_IMAGES_VERSION, true );

		$localized_vars = apply_filters(
			'wc_ajax_wc_additional_variation_images_localized_vars',
			array(
				'ajax_url'             => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'ajaxImageSwapNonce'   => wp_create_nonce( '_wc_additional_variation_images_nonce' ),
				'gallery_images_class' => apply_filters( 'wc_additional_variation_images_gallery_images_class', '.product .images .flex-control-nav, .product .images .thumbnails' ),
				'main_images_class'    => apply_filters( 'wc_additional_variation_images_main_images_class', '.woocommerce-product-gallery' ),
				'lightbox_images'      => apply_filters( 'wc_additional_variation_images_main_lightbox_images_class', '.product .images a.zoom' ),
			)
		);

		wp_localize_script( 'wc_additional_variation_images_script', 'wc_additional_variation_images_local', $localized_vars );

		return true;
	}

	/**
	 * Load variation images frontend ajax.
	 *
	 * @since 1.0.0
	 * @version 1.7.9
	 */
	public function ajax_get_images() {
		check_ajax_referer( '_wc_additional_variation_images_nonce', 'security' );

		if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
			wp_send_json_error();
		}

		$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
		$variation    = $variation_id ? wc_get_product( $variation_id ) : false;

		if ( ! $variation ) {
			wp_send_json_error();
		}

		$image_ids            = array_filter( explode( ',', get_post_meta( $variation_id, '_wc_additional_variation_images', true ) ) );
		$variation_main_image = $variation->get_image_id();

		if ( ! empty( $variation_main_image ) ) {
			array_unshift( $image_ids, $variation_main_image );
		}

		if ( empty( $image_ids ) ) {
			wp_send_json_error();
		}

		$gallery_html  = '<div class="woocommerce-product-gallery woocommerce-product-gallery--wcavi woocommerce-product-gallery--variation-' . absint( $variation_id ) . ' woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-' . esc_attr( apply_filters( 'woocommerce_product_thumbnails_columns', 4 ) ) . ' images" data-columns="' . esc_attr( apply_filters( 'woocommerce_product_thumbnails_columns', 4 ) ) . '" style="opacity: 0; transition: opacity .25s ease-in-out;">';
		$gallery_html .= '<figure class="woocommerce-product-gallery__wrapper">';

		foreach ( $image_ids as $id ) {
			$gallery_html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $id, false ), $id );
		}

		$gallery_html .= '</figure></div>';

		wp_send_json( array( 'main_images' => $gallery_html ) );
	}
}

new WC_Additional_Variation_Images_Frontend();
