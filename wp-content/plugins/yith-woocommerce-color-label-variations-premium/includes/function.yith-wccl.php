<?php
/**
 * Common functions
 *
 * @author  Francesco Licandro
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

defined( 'YITH_WCCL' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wccl_update_db_check' ) ) {
	function yith_wccl_update_db_check() {
		if ( YITH_WCCL_DB_VERSION !== get_option( 'yith_wccl_db_version', '' ) ) {

			if ( ! function_exists( 'yith_wccl_activation' ) ) {
				require_once 'function.yith-wccl-activation.php';
			}

			yith_wccl_activation();
		}
	}
}

if ( ! function_exists( 'ywccl_get_term_meta' ) ) {
	/**
	 * Get term meta.
	 *
	 * @author Francesco Licandro
	 * @param integer|string $term_id The term ID.
	 * @param string         $key The term meta key.
	 * @param boolean        $single Optional. Whether to return a single value.
	 * @param string         $taxonomy Optional. The taxonomy slug.
	 * @return mixed
	 */
	function ywccl_get_term_meta( $term_id, $key, $single = true, $taxonomy = '' ) {
		$value = get_term_meta( $term_id, $key, $single );

		// Compatibility with old format. To be removed on next version.
		if ( apply_filters( 'yith_wccl_get_term_meta', true, $term_id ) && ( false === $value || '' === $value ) && ! empty( $taxonomy ) )  {
			$value = get_term_meta( $term_id, $taxonomy . $key, $single );
			// If meta is not empty, save it with the new key.
			if ( false !== $value && '' !== $value ) {
				ywccl_update_term_meta( $term_id, $key, $value );
				// Delete old meta.
				// delete_term_meta( $term_id, $taxonomy . $key );
			}
		}

		return $value;
	}
}

if ( ! function_exists( 'ywccl_update_term_meta' ) ) {
	/**
	 * Update term meta.
	 *
	 * @author Francesco Licandro
	 * @param integer|string $term_id The term ID.
	 * @param string         $key The term meta key.
	 * @param mixed          $meta_value Metadata value.
	 * @param mixed          $prev_value Optional. Previous value to check before updating.
	 * @return mixed
	 */
	function ywccl_update_term_meta( $term_id, $key, $meta_value, $prev_value = '' ) {
		if ( '' === $meta_value || false === $meta_value ) {
			return delete_term_meta( $term_id, $key );
		}

		return update_term_meta( $term_id, $key, $meta_value, $prev_value );
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
		return apply_filters(
			'yith_wccl_get_custom_tax_types',
			array(
				'colorpicker' => __( 'Colorpicker', 'yith-woocommerce-color-label-variations' ),
				'image'       => __( 'Image', 'yith-woocommerce-color-label-variations' ),
				'label'       => __( 'Label', 'yith-woocommerce-color-label-variations' ),
			)
		);
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
	function yith_wccl_hide_add_to_cart( $product = "" ) {

		$catalog_mode = defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM && $product && YITH_WCTM()->check_hide_add_cart( false, $product->get_id(), true );

		$raq          = defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM && 'yes' === get_option( 'ywraq_hide_add_to_cart', 'no' );

		return $catalog_mode || $raq;
	}
}

if ( ! function_exists( 'yith_wccl_get_variation_gallery' ) ) {
	/**
	 * Get gallery images for given variation
	 *
	 * @since  1.8.0
	 * @author Francesco Licandro
	 * @param WP_Post|WC_Product_Variation $variation Instance WP_Post or WC_Product_Variation.
	 * @return array
	 */
	function yith_wccl_get_variation_gallery( $variation ) {

		global $sitepress;

		if ( ! ( $variation instanceof WC_Product ) ) {
			$variation = wc_get_product( $variation->ID );
		}

		if ( ! $variation ) {
			return array();
		}

		$gallery = $variation->get_meta( '_yith_wccl_gallery', true );
		if ( empty( $gallery ) && function_exists( 'wpml_object_id_filter' ) && ! empty( $sitepress ) && apply_filters( 'yith_wccl_use_parent_gallery_for_translated_products', true ) ) {
			$parent_id = wpml_object_id_filter( $variation->get_id(), 'product_variation', false, $sitepress->get_default_language() );
			if ( ! empty( $parent_id ) ) {
				$variation = wc_get_product( $parent_id );
				if ( $variation ) {
					$gallery = $variation->get_meta( '_yith_wccl_gallery', true );
				}
			}
		}

		return $gallery;
	}
}

if ( ! function_exists( 'yith_wccl_get_frontend_selectors' ) ) {
	/**
	 * Return correct selectors to use in frontend JS based on theme installed
	 *
	 * @since 1.10.2
	 * @author Francesco Licandro
	 * @param string $section Current section.
	 * @return string
	 */
	function yith_wccl_get_frontend_selectors( $section ) {

		// Get the current theme.
		$theme     = yith_wccl_get_current_theme();
		$selectors = array();

		switch ( $section ) {
			case 'single_gallery_selector':
				// Search for theme.
				if ( 'flatsome' === $theme ) {
					$selectors[] = '.product-gallery';
				} elseif ( 'salient' === $theme ) {
					$selectors[] = '.single-product-main-image > .images';
				} else {
					$selectors[] = '.woocommerce-product-gallery';
				}
				break;
			case 'wrapper_container_shop':
				if ( 'flatsome' === $theme ) {
					$selectors[] = 'div.product.product-small';
				} else {
					$selectors[] = 'li.product';
				}

				// Append YITH Wishlist container.
				if ( defined( 'YITH_WCWL' ) ) {
					$selectors[] = '.wishlist-items-wrapper .product-add-to-cart';
				}

				break;
			case 'image_selector':
				$selectors = array( 'img.wp-post-image', 'img.attachment-woocommerce_thumbnail' );
				break;
			default:
				break;
		}

		return implode( ',', $selectors );
	}
}

if ( ! function_exists( 'yith_wccl_get_current_theme' ) ) {
	/**
	 * Return current active theme
	 *
	 * @since 1.10.2
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wccl_get_current_theme() {

		// Get the installed theme.
		$theme = wp_cache_get( 'yith_wccl_current_theme', 'yith_wccl' );
		if ( false === $theme ) {
			$theme = '';
			if ( function_exists( 'wp_get_theme' ) ) {
				if ( is_child_theme() ) {
					$temp_obj  = wp_get_theme();
					$theme_obj = wp_get_theme( $temp_obj->get( 'Template' ) );
				} else {
					$theme_obj = wp_get_theme();
				}

				$theme = $theme_obj->get( 'TextDomain' );
				if ( empty( $theme ) ) {
					$theme = $theme_obj->get( 'Name' );
				}
			}

			wp_cache_set( 'yith_wccl_current_theme', $theme, 'yith_wccl' );
		}

		return $theme;
	}
}
