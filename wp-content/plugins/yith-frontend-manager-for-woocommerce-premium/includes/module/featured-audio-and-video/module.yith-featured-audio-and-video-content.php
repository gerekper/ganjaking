<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Frontend_Manager_For_Featured_Audio_Video
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Frontend_Manager_For_Featured_Audio_Video' ) ) {

	/**
	 * YITH_Frontend_Manager_For_Featured_Audio_Video Class
	 */
	class YITH_Frontend_Manager_For_Featured_Audio_Video {

		/**
		 * Main instance
		 */
		private static $_instance = null;

		/**
		 * YITH WooCommerce Audio and Video Content
		 */
		public $yith_wcfav  = null;

		/**
		 * Construct
		 */
		public function __construct(){
			global $YITH_Featured_Audio_Video;

			$this->yith_wcfav = $YITH_Featured_Audio_Video;

			if( ! empty( $this->yith_wcfav ) ) {

				if ( defined( 'YWCFAV_PREMIUM' ) ) {
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

					//Add metaboxes in woocommerce product
					add_action( 'yith_wcfm_show_product_metaboxes', array(
						$this,
						'featured_audio_video_meta_box_content'
					) );

					//Save option
				}
				add_action( 'yith_wcfm_product_save', array( $this, 'save_featured_meta'), 25, 3);

			}
		}

		/**
		 * @param $post_id
		 * @param $post
		 * @param WC_Product $product
		 */
		public function save_featured_meta( $post_id, $post, $product ){

			YITH_Featured_Audio_Video_Admin()->set_custom_product_meta( $product );
			$product->save();
		}

		/**
		 *
		 */
		public function featured_audio_video_meta_box_content(){

			ob_start();
			YITH_Featured_Audio_Video_Admin()->featured_audio_video_meta_box_content();
			$featured_audio_video_meta_box_template = ob_get_contents();
			ob_end_clean();

			$title = __( 'Featured Video or Audio', 'yith-frontend-manager-for-woocommerce' );

			printf( '<div class="form-field"><label>%s</label>%s</div>', $title, $featured_audio_video_meta_box_template );
		}

		/**
		 *
		 */
		public function enqueue_scripts(){
			if( ! empty( $this->yith_wcfav  ) ){
				$obj = YITH_Frontend_Manager()->gui->get_current_section_obj();
				$subsections = $obj->get_subsections();

				if( ! empty( $subsections ) && ! empty( $subsections['product'] ) ){
					if( ! empty( $obj ) && $obj->is_current( $subsections['product']['slug'] ) ){
						YITH_Featured_Audio_Video_Admin()->enqueue_premium_style_script();
						wp_enqueue_script( 'ywcfav_script' );
						wp_enqueue_style( 'ywcfav_admin_style' );
					}
				}
			}
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Frontend_Manager_For_Featured_Audio_Video Main instance
		 *
		 * @since  1.7
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_For_Featured_Audio_Video
 * @since  1.9
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Frontend_Manager_For_Featured_Audio_Video' ) ) {
	function YITH_Frontend_Manager_For_Featured_Audio_Video() {
		return YITH_Frontend_Manager_For_Featured_Audio_Video::instance();
	}
}