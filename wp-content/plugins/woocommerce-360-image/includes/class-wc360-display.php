<?php
/**
 * WooCommerce 360° Image Display
 *
 * @package WC_360_image
 * @since   1.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_360_Image_Display' ) ) {
	/**
	 * WC360 Display Class.
	 */
	class WC_360_Image_Display {

		protected static $instance;

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_action( 'wp_head', array( $this, 'inline_styles' ) );

			if ( $this->display_bool() ) {
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				add_action( 'woocommerce_before_single_product_summary', array( $this, 'output_image' ) );
				add_filter( 'post_class', array( $this, 'append_post_class' ) );
			}
		}

		/**
		 * Start the Class when called
		 *
		 * @since   1.0.0
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Adds a 'wc360-product' class to 360-enabled products.
		 *
		 * @param $classes
		 * @return array
		 */
		public function append_post_class( $classes ) {
			$classes[] = 'wc360-product';
			return $classes;
		}

		/**
		 * Should Display 360 or Not.
		 */
		public function display_bool() {
			if ( is_admin() || ! is_product() || get_post_meta( get_the_ID(), 'wc360_enable', true ) !== 'yes' ) {
				return false;
			}

			$product        = wc_get_product();
			$attachment_ids = WC_360_Image_Utils::get_gallery_ids( $product );

			return ! empty( $attachment_ids );
		}

		/**
		 * Load Scripts
		 *
		 * @since   1.0.0
		 */
		public function load_scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Register Scripts / Styles.
			$threesixty_lib_version = '2.0.4';
			wp_register_script( 'wc360-threesixty-js', WC_360_IMAGE_URL . 'lib/threesixty/js/threesixty' . $suffix . '.js', array( 'jquery' ), $threesixty_lib_version, true );
			wp_register_script( 'wc360-threesixty-fullscreen-js', WC_360_IMAGE_URL . 'lib/threesixty/js/plugins/threesixty.fullscreen.js', array( 'jquery', 'wc360-threesixty-js' ), $threesixty_lib_version, true );
			wp_register_style( 'wc360-threesixty-css', WC_360_IMAGE_URL . 'lib/threesixty/css/threesixty.css', array(), $threesixty_lib_version );
			wp_register_script( 'wc360-js', WC_360_IMAGE_URL . 'assets/js/wc360' . $suffix . '.js', array( 'jquery', 'wc360-threesixty-js', 'wc360-threesixty-fullscreen-js' ), WC_360_IMAGE_VERSION, true );
			wp_register_style( 'wc360-css', WC_360_IMAGE_URL . 'assets/css/wc360.css', array( 'wc360-threesixty-css' ), WC_360_IMAGE_VERSION );

			if ( $this->display_bool() ) {
				wp_enqueue_script( 'wc360-js' );
				wp_enqueue_style( 'wc360-css' );
				wp_localize_script( 'wc360-js', 'wc360_vars', $this->js_data_all() );
			}
		}

		/**
		 * Inline Styles
		 *
		 * @since   1.0.1
		 */
		public function inline_styles() {
			?>
			<style type="text/css">
				#container.wc360-container {
					width: 41%;
				}
				<?php
				// Show Full Screen Plugin (if enabled)
				if ( get_option( 'wc360_fullscreen_enable', 'no' ) === 'no' ) {
					echo '#container.wc360-container .fullscreen-button {
							display: none !important;
						}';
				}
				?>
			</style>
			<?php
		}

		/**
		 * HTML for Image
		 *
		 * @since   1.0.0
		 */
		public function output_image() {

			do_action( 'wc360_before_image' );

			$content              = '<div id="container" class="wc360-container">';
				$content         .= '<div class="wc360 threesixty">';
					$content     .= '<div class="spinner">';
						$content .= '<span>0%</span>';
					$content     .= '</div>';
					$content     .= '<ol class="threesixty_images"></ol>';
				$content         .= '</div>';
			$content             .= '</div>';

			echo apply_filters( 'wc360_image_output', $content );

			// The following inserts the code for Google Rich Snippets / schema.org
			?>

			<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>" itemprop="image" style="display: none;"></a>

			<?php
			do_action( 'wc360_after_image' );
		}

		/**
		 * ALL Data for 360 JS
		 *
		 * @since   1.0.0
		 */
		public function js_data_all( $shortcode_width = '', $shortcode_height = '', $post_id = '' ) {
			$image_size = $this->image_size( $this->image_size_name(), $shortcode_width, $shortcode_height, $post_id );

			return array(
				'images'     => wp_json_encode( $this->js_data_images( $post_id ) ),
				'navigation' => get_option( 'wc360_navigation_enable', 'yes' ) === 'yes',
				'responsive' => apply_filters( 'wc360_js_responsive', true ),
				'drag'       => apply_filters( 'wc360_js_drag', true ),
				'spin'       => apply_filters( 'wc360_js_spin', false ),
				// 360° Image Width
				'width'      => $shortcode_width ? $shortcode_width : $image_size['width'],
				// 360° Image Height
				'height'     => $shortcode_height ? $shortcode_height : $image_size['height'],
				// 360° Image Frames p/s. Kept wc360_js_speed for backward compat.
				'framerate'  => apply_filters( 'wc360_js_framerate', apply_filters( 'wc360_js_speed', 60 ) ),
				// Control the speed of play button rotation.
				'playspeed'  => apply_filters( 'wc360_js_playspeed', 100 ),
			);
		}

		/**
		 * Image Size Name to use
		 *
		 * @since   1.0.0
		 * @return  string
		 */
		public function image_size_name() {
			return apply_filters( 'wc360_image_size', 'woocommerce_single' );
		}

		/**
		 * Returns an array of the sizes to use
		 *
		 * @since   1.0.0
		 * @return  array ( width, height, crop )
		 */
		public function image_size( $name = '', $shortcode_width = '', $shortcode_height = '', $post_id = '' ) {
			// Image size array based on set image size
			$image_size_array = wc_get_image_size( $name );

			// Get attachments.
			$product        = wc_get_product( $post_id ? $post_id : get_the_ID() );
			$attachment_ids = WC_360_Image_Utils::get_gallery_ids( $product );

			// If image size set and have gallery images
			if ( isset( $image_size_array['width'] ) && ! empty( $attachment_ids ) ) {
				// Dimensions of Image (based on set image size)
				$width = $shortcode_width ? $shortcode_width : $image_size_array['width'];
				$crop  = $image_size_array['crop'];

				// If NOT hard cropped, calculate height
				if ( ! $crop ) {
					$real_image_path = get_attached_file( $attachment_ids[0] );

					// Width / Height of Real Image (first in gallery)
					list( $real_width, $real_height ) = getimagesize( $real_image_path );

					// Ratio between Real Width / Image Size Width
					$ratio = $real_width / $width;

					// Height of Image Size based on Real Height & Ratio
					$height = $real_height / $ratio;
				} else {
					// If it's cropped & the shortcode defines a width, calculate the height
					if ( $shortcode_width ) {
						$ratio  = $image_size_array['width'] / $shortcode_width;
						$height = $image_size_array['height'] / $ratio;
					} else {
						// If hard cropped and no shortcode width, height is the set image size's height
						$height = $image_size_array['height'];
					}
				}

				// Array of the final sizes
				$image_size = array(
					'width'  => ceil( $width ),
					'height' => ceil( $height ),
					'crop'   => $crop,
				);
			} else {
				$image_size = $image_size_array;
			}

			return $image_size;
		}

		/**
		 * Image Data for 360 JS
		 *
		 * @return array
		 */
		public function js_data_images( $post_id = '' ) {
			$product        = wc_get_product( $post_id ? $post_id : get_the_ID() );
			$attachment_ids = WC_360_Image_Utils::get_gallery_ids( $product );
			$image_js_array = array();

			foreach ( $attachment_ids as $attachment_id ) {
				$image_src        = wp_get_attachment_image_src( $attachment_id, apply_filters( 'wc360_output_image_size', 'full' ) );
				$image_js_array[] = $image_src[0];
			}

			return $image_js_array;
		}

		/**
		 * Returns Default Width (needed for shortcode)
		 *
		 * @package WooCommerce 360° Image
		 * @todo    Find better solution than this
		 */
		public function default_width() {
			global $_wp_additional_image_sizes;

			$name          = $this->image_size_name();
			$default_width = 300;

			if ( isset( $_wp_additional_image_sizes['woocommerce_single'] ) ) {
				// Image size array based on set image size
				$image_size_array = $_wp_additional_image_sizes['woocommerce_single'];
				$default_width    = $image_size_array['width'];
			}

			return $default_width;
		}
	}
}
