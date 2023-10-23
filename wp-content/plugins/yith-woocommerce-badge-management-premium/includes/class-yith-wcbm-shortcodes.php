<?php
/**
 * Shortcodes Class
 *
 * @package  YITH\BadgeManagement
 * @version  1.0.0
 * @author   YITH <plugins@yithemes.com>
 */

defined( 'YITH_WCBM' ) || exit;

if ( ! class_exists( 'YITH_WCBM_Shortcodes' ) ) {
	/**
	 * YITH_WCBM_Shortcodes
	 *
	 * @since 1.2.31
	 */
	class YITH_WCBM_Shortcodes {

		/**
		 * Init function.
		 */
		public static function init() {
			$shortcodes = array(
				'yith_badge_container' => __CLASS__ . '::badge_container',
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( $shortcode, $function );
			}
		}

		/**
		 * Print badge container
		 *
		 * @param array       $attrs   The shortcode attributes.
		 * @param string|null $content The shortcode content.
		 *
		 * @return string
		 */
		public static function badge_container( $attrs, $content = null ) {
			if ( ! $content ) {
				return '';
			}

			global $post;

			$default_attrs = array(
				'product_id' => ! ! $post && ! empty( $post->ID ) ? $post->ID : 0,
				'class'      => '',
			);

			$attrs      = wp_parse_args( $attrs, $default_attrs );
			$product_id = absint( $attrs['product_id'] );
			$class      = $attrs['class'];

			return "<div class='yith-wcbm-shortcode-badge-container $class'>" . apply_filters( 'yith_wcbm_product_thumbnail_container', do_shortcode( $content ), $product_id ) . '</div>';
		}

	}
}
