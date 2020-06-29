<?php
/**
 * WooCommerce 360° Image Display
 *
 * @package   WooCommerce 360° Image
 * @author    Captain Theme <info@captaintheme.com>
 * @license   GPL-2.0+
 * @link      http://captaintheme.com
 * @copyright 2014 Captain Theme
 * @since     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC360 Display Class
 *
 * @package  WooCommerce 360° Image
 * @author   Captain Theme <info@captaintheme.com>
 * @since    1.0.1
 */

if ( ! class_exists( 'WC_360_Image_Display' ) ) {

	class WC_360_Image_Display {

		protected static $instance = null;

		public function __construct() {

			// Scripts & Styles
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_footer_scripts' ) );

			// Inline Styles
			add_action( 'wp_head', array( $this, 'inline_styles' ) );

			if ( $this->display_bool() == true ) {

				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				add_action( 'woocommerce_before_single_product_summary', array( $this, 'output_image' ) );

				add_filter( 'post_class', array( $this, 'append_post_class' ) );

			}

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
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
		 * Should Display 360 or Not
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.1
		 */

		public function display_bool() {

			/*
			 * We only want to do the following on the front-end, so rule out if it's in the admin or not
			 */

			if ( ! is_admin() ) {

				// Vars for Gallery Images
				global $post;
				$product = wc_get_product( $post );

				// Only do this for products
				if ( $product ) {

					$attachment_ids = WC_360_Image_Utils::get_gallery_ids( $product );

					if ( is_product() && get_post_meta( get_the_ID(), 'wc360_enable', true ) && $attachment_ids ) {
						$bool = true;
					} else {
						$bool = false;
					}

				} else {
					$bool = false;
				}

			} else {

				$bool = false;

			}

			return $bool;

		}


		/**
		 * Load Scripts
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public function load_scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Register Scripts / Styles.
			$threesixty_lib_version = '2.0.4';
			wp_register_script( 'wc360-threesixty-js', plugins_url( 'lib/threesixty/js/threesixty' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ), $threesixty_lib_version, true );
			wp_register_script( 'wc360-threesixty-fullscreen-js', plugins_url( 'lib/threesixty/js/plugins/threesixty.fullscreen.js', dirname( __FILE__ ) ), array( 'jquery', 'wc360-threesixty-js' ), $threesixty_lib_version, true );
			wp_register_style( 'wc360-threesixty-css', plugins_url( 'lib/threesixty/css/threesixty.css', dirname( __FILE__ ) ), array(), $threesixty_lib_version );

			wp_register_script( 'wc360-js', plugins_url( 'assets/js/wc360' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ), WC_360_IMAGE_VERSION, true );
			wp_register_style( 'wc360-css', plugins_url( 'assets/css/wc360.css', dirname( __FILE__ ) ), array(), WC_360_IMAGE_VERSION );

			// Enqueue Scripts / Styles
			if ( $this->display_bool() == true ) {

				// Enqueue jQuery
				wp_enqueue_script( 'jquery' );

				wp_enqueue_script( 'wc360-threesixty-js' );
				wp_enqueue_script( 'wc360-threesixty-fullscreen-js' );
				wp_enqueue_style( 'wc360-threesixty-css' );
				wp_enqueue_script( 'wc360-js' );
				wp_enqueue_style( 'wc360-css' );

			}

		}


		/**
		 * Load Footer Scripts (localize JS)
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public function load_footer_scripts() {

			// Check if Single Product
			if ( $this->display_bool() == true ) {

				// Localize JS with Data
				wp_localize_script( 'wc360-js', 'wc360_vars', $this->js_data_all() );

			}

		}


		/**
		 * Inline Styles
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.1
		 */

		public function inline_styles() {

			// Check if Single Product
			if ( $this->display_bool() == true ) {
				?>

				<style type="text/css">

					#container.wc360-container {
						width: 41%;
					}

				</style>


				<?php
				// Show Full Screen Plugin (if enabled)
				if ( get_option( 'wc360_fullscreen_enable' ) == 'no' ) { ?>

					<style type="text/css">

						#container.wc360-container .fullscreen-button {
							display: none !important;
						}

					</style>

				<?php } ?>

			<?php }

		}


		/**
		 * HTML for Image
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public function output_image() {

			do_action( 'wc360_before_image' );

			$content = '<div id="container" class="wc360-container">';
				$content .= '<div class="wc360 threesixty">';
					$content .= '<div class="spinner">';
						$content .= '<span>0%</span>';
					$content .= '</div>';
			 		$content .= '<ol class="threesixty_images"></ol>';
				$content .= '</div>';
			$content .= '</div>';

			echo apply_filters( 'wc360_image_output', $content );

			// The following inserts the code for Google Rich Snippets / schema.org ?>

			<a href="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" itemprop="image" style="display: none;"></a>

			<?php do_action( 'wc360_after_image' );

		}


		/**
		 * ALL Data for 360 JS
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public function js_data_all( $shortcode_width = '', $shortcode_height = '', $post_id = '' ) {

			// Array of product's gallery images
			$images_array = wp_json_encode( $this->js_data_images( $post_id ) );

			// Add Navigation (if enabled)
			if ( get_option( 'wc360_navigation_enable' ) == 'yes' ) {
				$navigation = true;
			} else {
				$navigation = false;
			}

			// Responsiveness
			$responsive = apply_filters( 'wc360_js_responsive', true );

			// Drag / Touch
			$drag = apply_filters( 'wc360_js_drag', true );

			// Spin
			$spin = apply_filters( 'wc360_js_spin', false );

			// 360° Image Frames p/s.
			// Kept wc360_js_speed for backward compat.
			$speed     = apply_filters( 'wc360_js_speed', 60 );
			$framerate = apply_filters( 'wc360_js_framerate', $speed );

			// Control the speed of play button rotation.
			$playspeed = apply_filters( 'wc360_js_playspeed', 100 );

			// Image Sizes array
			$image_size = $this->image_size( $this->image_size_name(), $shortcode_width, $shortcode_height, $post_id );

			// 360° Image Width
			if ( $shortcode_width ) {
				$width = $shortcode_width;
			} else {
				$width = $image_size['width'];
			}

			// 360° Image Height
			if ( $shortcode_height ) {
				$height = $shortcode_height;
			} else {
				$height = $image_size['height'];
			}

			return array(
				'images'     => $images_array,
				'navigation' => $navigation,
				'responsive' => $responsive,
				'drag'       => $drag,
				'spin'       => $spin,
				'width'      => $width,
				'height'     => $height,
				'framerate'  => $framerate,
				'playspeed'  => $playspeed,
			);

		}


		/**
		 * Image Size Name to use
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 * @return 	string
		 */

		public function image_size_name() {

			$name = apply_filters( 'wc360_image_size', 'shop_single' );

			return $name;

		}


		/**
		 * Returns an array of the sizes to use
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 * @return 	array ( width, height, crop )
		 * @todo 	Factor in shortcode height (only) to calculation too
		 */

		public function image_size( $name = '', $shortcode_width = '', $shortcode_height = '', $id = '' ) {

			global $_wp_additional_image_sizes;

			if ( isset( $_wp_additional_image_sizes[$name] ) ) {

				// Image size array based on set image size
				$image_size_array = $_wp_additional_image_sizes[$name];

				// Array of all Images
				$images = $this->js_data_images( $id );

				// If image size set and have gallery images
				if ( isset( $image_size_array['width'] ) && $images ) {

					// Dimensions of Image (based on set image size)
					if ( $shortcode_width ) {
						$width = $shortcode_width;
					} else {
						$width = $image_size_array['width'];
					}
					// Hard crop setting for Image (based on set image size)
					$crop = $image_size_array['crop'];

					// If NOT hard cropped, calculate height
					if ( ! $crop ) {

						// Width / Height of Real Image (first in gallery)
						list( $real_width, $real_height ) = getimagesize( $images[0] );

						// Ratio between Real Width / Image Size Width
						$ratio = $real_width / $width;

						// Height of Image Size based on Real Height & Ratio
						$height = $real_height / $ratio;

					} else {

						// If it's cropped & the shortcode defines a width, calculate the height
						if ( $shortcode_width ) {

							$ratio = $image_size_array['width'] / $shortcode_width;

							$height = $image_size_array['height'] / $ratio;

						} else {

							// If hard cropped and no shortcode width, height is the set image size's height
							$height = $image_size_array['height'];

						}

					}

					// Array of the final sizes
					$image_sizes_final = array(
						'width' 	=> $width,
						'height' 	=> $height,
						'crop'		=> $crop,
					);

					$image_sizes = $image_sizes_final;

				} else {

					$image_sizes = false;

				}

			} else {

				$image_sizes = false;

			}

			return $image_sizes;

		}


		/**
		 * Image Data for 360 JS
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public function js_data_images( $post_id = '' ) {

			if ( $post_id ) {
				$id = $post_id;
			} else {
				global $post;
				$id = get_the_ID( $post );
			}

			$product = wc_get_product( $id );

			$attachment_ids = WC_360_Image_Utils::get_gallery_ids( $product );

			if ( $attachment_ids ) {

				$image_js_array = array();

				foreach ( $attachment_ids as $attachment_id ) {

					$image_size = apply_filters( 'wc360_output_image_size', 'full' );

					$image_src = wp_get_attachment_image_src( $attachment_id, $image_size );

					$image_link = $image_src[0];

					$image_js_array[] = $image_link;

				}

				return $image_js_array;

			}

		}


		/**
		 * Returns Default Width (needed for shortcode)
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.
		 * @todo 	Find better solution than this
		 */

		public function default_width() {

			global $_wp_additional_image_sizes;

			$name = $this->image_size_name();

			if ( isset( $_wp_additional_image_sizes['shop_single'] ) ) {

				// Image size array based on set image size
				$image_size_array = $_wp_additional_image_sizes['shop_single'];

				$default_width = $image_size_array['width'];

			} else {

				$default_width = 300; // Just in case

			}

			return $default_width;

		}

	}

}
