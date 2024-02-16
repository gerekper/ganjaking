<?php
/**
 * WooCommerce 360° Shortcode
 *
 * @package WC_360_Image
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_360_Image_Shortcode' ) ) {
	/**
	 * WC360 Shortcode Class.
	 */
	class WC_360_Image_Shortcode {

		protected static $instance;

		private function __construct() {

			add_shortcode( 'wc360', array( $this, 'shortcode' ) );
		}

		/**
		 * Start the Class when called
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Make Shortcode
		 *
		 * @since 1.0.0
		 */
		public function shortcode( $atts ) {

			// Load in an instance of the WC_360_Image_Display Class
			$image_display = new WC_360_Image_Display();

			// Array of default dimensions (used when shortcode doesn't define any)
			$dimensions = $image_display->default_width();

			$default_width = $dimensions;

			// Shortcode Attributes
			extract(
				shortcode_atts(
					array(
						'width'  => $default_width, // Width of rotator (default: image size width)
						'height' => '', // Height of rotator (default: image size height)
						'id'     => '', // ID of post/product to get rotato for (default: none / current product)
					),
					$atts
				)
			);

			// If ID passed use that, if not get the current page's ID
			if ( $id ) {
				$id = $id;
			} else {
				$id = get_the_ID();
			}

			// Enqueue the JS / CSS we need
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'wc360-threesixty-js' );
			wp_enqueue_script( 'wc360-threesixty-fullscreen-js' );
			wp_enqueue_style( 'wc360-threesixty-css' );
			wp_enqueue_script( 'wc360-js' );
			wp_enqueue_style( 'wc360-css' );

			wp_localize_script( 'wc360-js', 'wc360_vars', $image_display->js_data_all( $width, $height, $id ) );

			// Start Shortcode Output
			ob_start();

			// We can only currently have one instance of the rotator per page, so only show it if the 'Replace Image with 360 Image' is unchecked
			if ( get_post_meta( get_the_ID(), 'wc360_enable', true ) !== 'yes' ) {

				// Only continue if the ID received is a product
				if ( 'product' === get_post_type( $id ) ) {

					$product        = wc_get_product( $id );
					$attachment_ids = WC_360_Image_Utils::get_gallery_ids( $product );

					// Only continue if there are gallery images
					if ( ! empty( $attachment_ids ) ) {

						do_action( 'wc360_shortcode_before_image' );

						$content              = '<div id="container" class="wc360-container shortcode" style="width:' . $width . 'px">';
							$content         .= '<div class="wc360 threesixty">';
								$content     .= '<div class="spinner">';
									$content .= '<span>0%</span>';
								$content     .= '</div>';
								$content     .= '<ol class="threesixty_images"></ol>';
							$content         .= '</div>';
						$content             .= '</div>';

						echo apply_filters( 'wc360_shortcode_image_output', $content );

						do_action( 'wc360_shortcode_after_image' );

					} else {

						// Error
						printf( esc_html__( '%s There are no gallery images for this product!', 'woocommerce-360-image' ), '<strong>' . esc_html__( 'Note:', 'woocommerce-360-image' ) . '</strong>' );

					}
				} else {

					// Error
					printf( esc_html__( '%s This is not a valid product ID!', 'woocommerce-360-image' ), '<strong>' . esc_html__( 'Note:', 'woocommerce-360-image' ) . '</strong>' );

				}
			} else {

				// Error
				printf( esc_html__( '%s There can only be one rotator per page!', 'woocommerce-360-image' ), '<strong>' . esc_html__( 'Note:', 'woocommerce-360-image' ) . '</strong>' );

			}

			$data = ob_get_clean();
			// End Shortcode Output

			return $data;
		}
	}

}
