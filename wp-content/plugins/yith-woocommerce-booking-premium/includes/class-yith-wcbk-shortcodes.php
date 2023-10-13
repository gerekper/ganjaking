<?php
/**
 * Class YITH_WCBK_Shortcodes
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Shortcodes' ) ) {
	/**
	 * Class YITH_WCBK_Shortcodes
	 * register and manage shortcodes
	 */
	class YITH_WCBK_Shortcodes {

		/**
		 * Init.
		 */
		public static function init() {
			$shortcodes = array(
				'booking_form' => __CLASS__ . '::booking_form',
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( $shortcode, $function );
			}
		}

		/**
		 * Booking Form
		 *
		 * @param array $atts Attributes.
		 *
		 * @return string
		 */
		public static function booking_form( $atts ) {
			global $product;
			ob_start();
			$booking_id = $atts['id'] ?? 0;

			if ( ! $booking_id && $product && $product->get_id() ) {
				$booking_id = $product->get_id();
			}

			if ( $booking_id ) {
				$booking_product = wc_get_product( $booking_id );
				if ( $booking_product && $booking_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
					global $product, $post;
					$old_product = $product;
					$old_post    = $post;
					$post        = get_post( $booking_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$product     = $booking_product;
					wc_get_template( 'shortcodes/booking-form.php', array(), '', YITH_WCBK_TEMPLATE_PATH );
					$product = $old_product;
					$post    = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				}
			}

			return ob_get_clean();
		}
	}
}
