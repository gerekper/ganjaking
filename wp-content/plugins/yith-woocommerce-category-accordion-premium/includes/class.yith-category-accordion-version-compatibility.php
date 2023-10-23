<?php //phpcs:ignore
/**
 * Manage plugin version 2.0.0 compatibility
 *
 * @since   2.0.0
 * @author  YITH
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Category_Accordion_Version_Compatibility' ) ) {

	/**
	 * YITH_WC_Category_Accordion_Version_Compatibility
	 */
	class YITH_WC_Category_Accordion_Version_Compatibility {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Category_Accordion
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Category_Accordion_Version_Compatibility
		 * @since 2.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize version compatibility class
		 *
		 * @since  2.0.0
		 */
		public function __construct() {

			// If not exist the previous version!
			if ( ! empty( get_option( 'ywcca_hide_empty_cat' ) ) && defined( 'YWCCA_VERSION' ) && empty( get_option( 'yith_ywcca_option_version' ) ) ) {
				// Adding old styles as Post Type
				add_action( 'admin_init', array( $this, 'porting_styles_to_2_0' ) );
				// Plugin version updated !
				update_option( 'yith_ywcca_option_version', '2.0.0' );
				// Exist previous version
			} elseif ( get_option( 'yith_ywcca_option_version' ) < '2.0.0' ) {
				// Compatibility functions !
				add_action( 'admin_init', array( $this, 'porting_settings_to_2_0' ) );
				add_action( 'admin_init', array( $this, 'porting_styles_to_2_0' ) );
			}

		}

		/**
		 * Porting_settings_to_2_0
		 *
		 * @return void
		 */
		public function porting_settings_to_2_0() {

			$ywcca_max_depth       = ! empty( get_option( 'ywcca_max_depth_acc' )) || get_option('ywcca_max_depth_acc') === '0' ? get_option( 'ywcca_max_depth_acc' ) : 2;
			$ywcca_limit_number    = ! empty( get_option( 'ywcca_limit_number_cat_acc' ) ) ? get_option( 'ywcca_limit_number_cat_acc' ) : 10;
			$ywcca_accordion_speed = ! empty( get_option( 'ywcca_accordion_speed' ) ) ? get_option( 'ywcca_accordion_speed' ) : 400;

			if ( isset( $ywcca_accordion_speed ) ) {
				update_option( 'ywcca_accordion_speed', array( 'number_speed' => $ywcca_accordion_speed ) );
			}

			if ( isset( $ywcca_max_depth ) ) {

				if ( '0' === $ywcca_max_depth ) {
					update_option( 'ywcca_level_depth_acc ', 'all' );
				} else {
					update_option( 'ywcca_level_depth_acc ', 'level' );
					update_option( 'ywcca_max_level_depth  ', $ywcca_max_depth );
				}

			}

			if ( isset( $ywcca_limit_number ) ) {

				if ( '-1' === $ywcca_limit_number ) {
					update_option( 'ywcca_show_cat_acc', 'all' );
				} else {
					update_option( 'ywcca_show_cat_acc', 'amount' );
					update_option( 'ywcca_amount_max_acc', array( 'number_categories' => $ywcca_limit_number ) );
				}
			}
			// Plugin version updated !
			update_option( 'yith_ywcca_option_version', '2.0.0' );

		}

		/**
		 * Porting_styles_to_2_0
		 *
		 * @return void
		 */
		public function porting_styles_to_2_0() {

			$styles = array( 'Style 1' => 'style1', 'Style 2' => 'style2', 'Style 3' => 'style3', 'Style 4' => 'style4', 'Style 5' => 'style5', 'Style 6' => 'style6' );

			foreach ( $styles as $key_style => $style ) {
				$args = array(
					'post_title'  => $key_style,
					'post_status' => 'publish',
					'post_type'   => 'yith_cacc',
				);

				$postid = wp_insert_post( $args );

				if ( $postid && false !== get_post_status( $postid ) ) {

					$styles_options = ywcca_get_style( $style, $postid);

					foreach ( $styles_options[strtolower(str_replace( ' ', '_', $key_style ))] as $key => $post_meta_value ) {
						update_post_meta( $postid, $key, $post_meta_value );
						update_option(strtolower( str_replace( ' ', '', $key_style ) ) ."_id", $postid);
					}
				}
			}
		}
	} // End Class !

}
YITH_WC_Category_Accordion_Version_Compatibility::get_instance();
