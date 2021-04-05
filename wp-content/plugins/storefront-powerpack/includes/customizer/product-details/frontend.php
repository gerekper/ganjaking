<?php
/**
 * Storefront Powerpack Frontend Product Details Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Product_Details' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Product_Details extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'shop_layout' ), 999 );
			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_filter( 'storefront_product_thumbnail_columns', array( $this, 'product_thumbnails' ) );
			add_action( 'wp', array( $this, 'single_product_layout_sidebar' ), 999 );
			add_filter( 'woocommerce_gallery_image_size', array( $this, 'stacked_image_size' ) );
		}

		/**
		 * Shop Layout
		 * Tweaks the WooCommerce layout based on settings
		 */
		public function shop_layout() {
			global $post;

			$product_layout         = get_theme_mod( 'sp_product_layout', 'default' );
			$product_gallery_layout = get_theme_mod( 'sp_product_gallery_layout', 'default' );
			$product_details_tabs   = get_theme_mod( 'sp_product_details_tab', true );
			$product_related        = get_theme_mod( 'sp_related_products', true );
			$product_meta           = get_theme_mod( 'sp_product_meta', true );
			$product_description    = get_theme_mod( 'sp_product_description', true );

			if ( is_product() ) {
				if ( 'full-width' === $product_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}

				if ( 'hidden' === $product_gallery_layout ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}

				if ( false === $product_details_tabs ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				}

				if ( false === $product_related ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				}

				if ( false === $product_meta ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				}

				if ( false === $product_description ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				}

				if ( $post ) {
					$sf_gallery_layout = get_post_meta( $post->ID, '_sp_sf_gallery_layout', true );

					if ( $sf_gallery_layout && 'hide' === $sf_gallery_layout ) {
						remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
					}
				}
			}
		}

		/**
		 * Storefront Powerpack Body Class
		 *
		 * @param array $classes array of classes applied to the body tag.
		 * @see get_theme_mod()
		 */
		public function body_class( $classes ) {
			global $post;

			$product_layout         = get_theme_mod( 'sp_product_layout', 'default' );
			$product_gallery_layout = get_theme_mod( 'sp_product_gallery_layout', 'default' );

			if ( is_product() ) {
				if ( 'full-width' === $product_layout ) {
					$classes[] = 'storefront-full-width-content';
				}

				if ( 'hidden' === $product_gallery_layout ) {
					$classes[] = 'sp-product-gallery-hidden';
				}

				if ( 'stacked' === $product_gallery_layout ) {
					$classes[] = 'sp-product-gallery-stacked';
				}

				$sf_layout         = get_post_meta( $post->ID, '_sp_sf_product_layout', true );
				$sf_gallery_layout = get_post_meta( $post->ID, '_sp_sf_gallery_layout', true );

				if ( $sf_layout ) {

					if ( 'default' === $sf_layout ) {

						$key = array_search( 'storefront-full-width-content', $classes );

						if ( false !== $key ) {
							unset( $classes[ $key ] );
						}
					} elseif ( 'full-width' === $sf_layout ) {

						$key = array_search( 'storefront-full-width-content', $classes );

						if ( false === $key ) {
							$classes[] = 'storefront-full-width-content';
						}
					}
				}

				if ( $sf_gallery_layout ) {
					if ( 'default' === $sf_gallery_layout ) {
						$key1 = array_search( 'sp-product-gallery-stacked', $classes );
						$key2 = array_search( 'sp-product-gallery-hidden', $classes );

						if ( false !== $key1 ) {
							unset( $classes[ $key1 ] );
						}

						if ( false !== $key2 ) {
							unset( $classes[ $key2 ] );
						}
					} elseif ( 'stacked' === $sf_gallery_layout  ) {
						$key1 = array_search( 'sp-product-gallery-hidden', $classes );
						$key2 = array_search( 'sp-product-gallery-stacked', $classes );

						if ( false !== $key1 ) {
							unset( $classes[ $key1 ] );
						}

						if ( false === $key2 ) {
							$classes[] = 'sp-product-gallery-stacked';
						}
					} elseif ( 'hide' === $sf_gallery_layout ) {
						$key1 = array_search( 'sp-product-gallery-stacked', $classes );
						$key2 = array_search( 'sp-product-gallery-hidden', $classes );

						if ( false !== $key1 ) {
							unset( $classes[ $key1 ] );
						}

						if ( false === $key2 ) {
							$classes[] = 'sp-product-gallery-hidden';
						}
					}
				}
			}

			return $classes;
		}

		/**
		 * Remove sidebar on single products if layout is set to full width
		 *
		 * @return void
		 */
		public function single_product_layout_sidebar() {
			global $post;

			if ( is_product() ) {
				$sf_layout = get_post_meta( $post->ID, '_sp_sf_product_layout', true );

				if ( 'full-width' === $sf_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}

				// Product tabs.
				$sf_product_tabs = get_post_meta( $post->ID, '_sp_sf_product_tabs', true );

				if ( 'show' === $sf_product_tabs ) {
					$product_details_tabs = get_theme_mod( 'sp_product_details_tab', true );

					if ( false === $product_details_tabs ) {
						add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
					}
				} elseif ( 'hide' === $sf_product_tabs ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				}

				// Product Related.
				$sf_product_related = get_post_meta( $post->ID, '_sp_sf_product_related', true );

				if ( 'show' === $sf_product_related ) {
					$product_related = get_theme_mod( 'sp_related_products', true );

					if ( false === $product_related ) {
						add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
					}
				} elseif ( 'hide' === $sf_product_related ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				}

				// Product Descrition.
				$sf_product_description = get_post_meta( $post->ID, '_sp_sf_product_description', true );

				if ( 'show' === $sf_product_description ) {
					$product_description = get_theme_mod( 'sp_product_description', true );

					if ( false === $product_description ) {
						add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
					}
				} elseif ( 'hide' === $sf_product_description ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				}

				// Product Meta.
				$sf_product_meta = get_post_meta( $post->ID, '_sp_sf_product_meta', true );

				if ( 'show' === $sf_product_meta ) {
					$product_meta = get_theme_mod( 'sp_product_meta', true );

					if ( false === $product_meta ) {
						add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
					}
				} elseif ( 'hide' === $sf_product_meta ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				}
			}
		}

		/**
		 * Product thumbnail layout
		 * Tweak the number of columns thumbnails are arranged into based on settings
		 *
		 * @param int $cols the number of product thumbnail columns.
		 */
		public function product_thumbnails( $cols ) {
			$product_layout 	 	= get_theme_mod( 'sp_product_layout', 'default' );
			$product_gallery_layout = get_theme_mod( 'sp_product_gallery_layout', 'default' );

			$cols = 4;

			if ( 'full-width' === $product_layout && 'stacked' === $product_gallery_layout ) {
				$cols = 6;
			}

			if ( 'default' === $product_layout && 'stacked' === $product_gallery_layout ) {
				$cols = 3;
			}

			return $cols;
		}

		/**
		 * Set custom single image width when gallery layout is set to 'stacked'
		 *
		 * @param string $size the image size.
		 * @return string $size the edited image size.
		 */
		public function stacked_image_size( $size ) {
			global $post;

			if ( $size !== 'woocommerce_single' ) {
				return $size;
			}

			$is_stacked = false;

			// Global
			$product_gallery_layout = get_theme_mod( 'sp_product_gallery_layout', 'default' );

			if ( 'stacked' === $product_gallery_layout ) {
				$is_stacked = true;
			}

			// Product
			$product = $post->ID;

			if ( $product && 'product' === get_post_type( $product ) ) {
				$product_gallery_layout = get_post_meta( $product, '_sp_sf_gallery_layout', true );

				if ( $product_gallery_layout ) {
					switch ( $product_gallery_layout ) {
						case 'stacked':
							$is_stacked = true;
							break;

						case 'default':
						case 'hide':
							$is_stacked = false;
							break;
					}
				}
			}

			if ( true === $is_stacked ) {
				$size = 'full';
			}

			return $size;
		}
	}

endif;

return new SP_Frontend_Product_Details();