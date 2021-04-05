<?php
/**
 * Storefront Powerpack Frontend Layout Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Layout' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Layout extends SP_Frontend {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'script' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 999 );
			add_action( 'init', array( $this, 'reregister_theme_support' ) );

			add_filter( 'woocommerce_get_image_size_single', array( $this, 'change_image_size' ), 10 );
			add_filter( 'woocommerce_get_image_size_thumbnail', array( $this, 'change_image_size' ), 10 );
		}

		/**
		 * Storefront Powerpack Body Class
		 *
		 * @param array $classes array of classes applied to the body tag.
		 * @see get_theme_mod()
		 */
		public function body_class( $classes ) {
			if ( 'max-width' === get_theme_mod( 'sp_max_width' ) ) {
				$classes[] = 'sp-max-width';
			}

			if ( 'frame' === get_theme_mod( 'sp_content_frame' ) ) {
				$classes[] = 'sp-fixed-width';
			}

			return $classes;
		}

		/**
		 * Enqueue styles and scripts.
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function script() {
			if ( 'max-width' === get_theme_mod( 'sp_max_width' ) || 'frame' === get_theme_mod( 'sp_content_frame' ) ) {
				wp_enqueue_style( 'sp-layout', SP_PLUGIN_URL . 'includes/customizer/layout/assets/css/layout.css', '', storefront_powerpack()->version );
			}
		}

		/**
		 * Add CSS in <head> for styles handled by the Customizer
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_customizer_css() {
			$content_background_color = get_theme_mod( 'sp_content_frame_background' );

			$wc_style = '
				.sp-fixed-width .site {
					background-color:' . $content_background_color . ';
				}
			';

			wp_add_inline_style( 'storefront-style', $wc_style );
		}

		/**
		 * Reregister theme features to remove declared image sizes
		 *
		 * @return void
		 * @since 1.4.12
		 */
		public function reregister_theme_support() {
			if ( 'max-width' !== get_theme_mod( 'sp_max_width' ) ) {
				return;
			}

			$theme_support = get_theme_support( 'woocommerce' );
			$theme_support = is_array( $theme_support ) ? $theme_support[0] : false;

			if ( $theme_support ) {
				$sizes = array(
					'single_image_width',
					'thumbnail_image_width',
					'woocommerce_gallery_thumbnail'
				);

				foreach ( $sizes as $size ) {
					if ( isset( $theme_support[ $size ] ) ) {
						unset( $theme_support[ $size ] );
					}
				}

				add_theme_support( 'woocommerce', $theme_support );
			}
		}

		/**
		 * Change image size when the layout is set to "Max Width"
		 *
		 * The `reregister_theme_support()` method above overrides the default theme features,
		 * but all that does is bring back the WooCommerce settings in the Customizer.
		 *
		 * Here we're overriding the custom sizes declared by the theme, and replacing them
		 * with the custom sizes from the Customizer settings.
		 *
		 * Theme features are registered before plugins are able to filter them properly
		 * so this method is required to actually override the image sizes.
		 *
		 * @param array $size Array of image dimensions.
		 * @return array $size Filtered array of image dimensions.
		 * @since 1.4.5
		 */
		public function change_image_size( $size ) {
			if ( 'max-width' !== get_theme_mod( 'sp_max_width' ) ) {
				return $size;
			}

			$image_size = str_replace( 'woocommerce_get_image_size_', '', current_filter() );

			if ( 'single' === $image_size ) {
				$size['width'] = absint( get_option( 'woocommerce_single_image_width', 600 ) );
			} elseif ( 'thumbnail' === $image_size ) {
				$size['width'] = absint( get_option( 'woocommerce_thumbnail_image_width', 300 ) );
				$cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );

				if ( 'custom' === $cropping ) {
					$width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
					$height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
					$size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
				} else {
					$cropping_split = explode( ':', $cropping );
					$width          = max( 1, current( $cropping_split ) );
					$height         = max( 1, end( $cropping_split ) );
					$size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
				}
			}

			return $size;
		}
	}

endif;

return new SP_Frontend_Layout();