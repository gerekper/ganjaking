<?php
/**
 * Common function
 *
 * @author  YITH
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

if ( ! function_exists( 'yith_wccl_update_db_check' ) ) {
	function yith_wccl_update_db_check() {
		if ( get_option( 'yith_wccl_db_version' ) != YITH_WCCL_DB_VERSION ) {

			if ( ! function_exists( 'yith_wccl_activation' ) ) {
				require_once( 'yith-wccl-activation.php' );
			}

			yith_wccl_activation();
		}
	}
}


if ( ! function_exists( 'ywccl_get_term_meta' ) ) {
	/**
	 * Get term meta. If WooCommerce version is >= 2.6 use get_term_meta else use get_woocommerce_term_meta
	 *
	 * @author Francesco Licandro
	 * @param      $key
	 * @param bool $single
	 *
	 * @param      $term_id
	 * @return mixed
	 */
	function ywccl_get_term_meta( $term_id, $key, $single = true ) {
		return function_exists( 'get_term_meta' ) ? get_term_meta( $term_id, $key, $single ) : get_metadata( 'woocommerce_term', $term_id, $key, $single );
	}
}

if ( ! function_exists( 'ywccl_update_term_meta' ) ) {
	/**
	 * Get term meta. If WooCommerce version is >= 2.6 use update_term_meta else use update_woocommerce_term_meta
	 *
	 * @author Francesco Licandro
	 * @param string     $meta_key
	 * @param mixed      $meta_value
	 * @param mixed      $prev_value
	 *
	 * @param string|int $term_id
	 * @return bool
	 */
	function ywccl_update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		return function_exists( 'update_term_meta' ) ? update_term_meta( $term_id, $meta_key, $meta_value, $prev_value ) : update_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value, $prev_value );
	}
}

if ( ! function_exists( 'ywccl_get_custom_tax_types' ) ) {
	/**
	 * Return custom product's attributes type
	 *
	 * @since  1.2.0
	 * @author Francesco Licandro
	 * @return mixed|void
	 */
	function ywccl_get_custom_tax_types() {
		return apply_filters( 'yith_wccl_get_custom_tax_types', array(
			'colorpicker' => __( 'Colorpicker', 'yith-woocommerce-color-label-variations' ),
			'image'       => __( 'Image', 'yith-woocommerce-color-label-variations' ),
			'label'       => __( 'Label', 'yith-woocommerce-color-label-variations' ),
		) );
	}
}

if ( ! function_exists( 'ywccl_check_wc_version' ) ) {
	/**
	 * Check installed WooCommerce version
	 *
	 * @since  1.3.0
	 * @author Francesco Licandro
	 * @param string $version
	 * @param string $operator
	 * @return boolean
	 * @deprecated
	 */
	function ywccl_check_wc_version( $version, $operator ) {
		return version_compare( WC()->version, $version, $operator );
	}
}

if ( ! function_exists( 'yith_wccl_hide_add_to_cart' ) ) {
	/**
	 * Check if catalog mode is active or if RAQ option "Hide add to cart" as enabled
	 *
	 * @since  1.6.0
	 * @author Francesco Licandro
	 * @return boolean
	 */
	function yith_wccl_hide_add_to_cart() {
		$catalog_mode = defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM && get_option( 'ywctm_enable_plugin ' ) == 'yes';
		$raq          = defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM && get_option( 'ywraq_hide_add_to_cart' ) == 'yes';

		return $catalog_mode || $raq;
	}
}

if ( ! function_exists( 'yith_wccl_get_variation_gallery' ) ) {
	/**
	 * Get gallery images for given variation
	 *
	 * @since  1.8.0
	 * @author Francesco Licandro
	 * @param \WP_Post|\WC_Product_Variation $variation
	 * @return array
	 */
	function yith_wccl_get_variation_gallery( $variation ) {

		global $sitepress;

		$variation instanceof WC_Product || $variation = wc_get_product( $variation->ID );
		if ( ! $variation ) {
			return array();
		}

		$gallery = $variation->get_meta( '_yith_wccl_gallery', true );
		if ( empty( $gallery ) && function_exists( 'wpml_object_id_filter' ) && ! empty( $sitepress ) && apply_filters( 'yith_wccl_use_parent_gallery_for_translated_products', true ) ) {
			$parent_id = wpml_object_id_filter( $variation->get_id(), 'product_variation', false, $sitepress->get_default_language() );
			if ( ! empty( $parent_id ) ) {
				$variation = wc_get_product( $parent_id );
				$variation && $gallery = $variation->get_meta( '_yith_wccl_gallery', true );
			}
		}

		return $gallery;
	}
}

if( ! function_exists( 'yith_wccl_get_frontend_selectors' ) ) {
	/**
	 * Return correct selectors to use in frontend JS based on theme installed
	 *
	 * @since 1.10.2
	 * @author Francesco Licandro
	 * @param string $section
	 * @return string
	 */
	function yith_wccl_get_frontend_selectors( $section ){

		// get the current theme
		$theme = yith_wccl_get_current_theme();

		switch ( $section ) {
			case 'single_gallery_selector':
				$value = '.woocommerce-product-gallery';
				// search for theme
				if( 'flatsome' === $theme ) {
					$value = '.product-gallery';
				}
				break;
			case 'wrapper_container_shop':
				$value = 'li.product';
				if( 'flatsome' === $theme ) {
					$value = 'div.product.product-small';
				}
				break;
			case 'image_selector':
				$value = 'img.wp-post-image, img.attachment-woocommerce_thumbnail';
				break;
			default:
				$value = '';
				break;
		}

		return $value;
	}
}

if( ! function_exists( 'yith_wccl_get_current_theme' ) ) {
	/**
	 * Return current active theme
	 *
	 * @since 1.10.2
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wccl_get_current_theme() {

		// get the installed theme
		$theme = wp_cache_get( 'yith_wccl_current_theme', 'yith_wccl' );
		if( false === $theme ) {
			$theme = '';
			if ( function_exists( 'wp_get_theme' ) ) {
				if ( is_child_theme() ) {
					$temp_obj  = wp_get_theme();
					$theme_obj = wp_get_theme( $temp_obj->get( 'Template' ) );
				} else {
					$theme_obj = wp_get_theme();
				}

				$theme = $theme_obj->get( 'TextDomain' );
				empty( $theme ) && $theme = $theme_obj->get( 'Name' );
			}

			wp_cache_set( 'yith_wccl_current_theme', $theme, 'yith_wccl' );
		}

		return $theme;
	}
}