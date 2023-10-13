<?php
/**
 * Class YITH_WCBK_Quick_View_Integration
 * Quick View integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Quick_View_Integration
 *
 * @since   1.0.7
 */
class YITH_WCBK_Quick_View_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			$booking_form_position = get_option( 'yith-wcbk-booking-form-position', 'default' );
			$show_add_to_cart      = get_option( 'yith-wcqv-product-show-add-to-cart', 'yes' ) === 'yes';

			if ( 'default' !== $booking_form_position && $show_add_to_cart ) {
				add_action( 'yith_wcqv_product_summary', array( $this, 'print_add_to_cart_template' ), 25 );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_booking_map_in_frontend' ), 20 );
		}
	}

	/**
	 * Enqueue booking map script to allow showing booking map in quick view on Shop pages
	 *
	 * @since 2.0.8
	 */
	public function enqueue_booking_map_in_frontend() {
		wp_enqueue_script( 'yith-wcbk-booking-map' );
	}

	/**
	 * Print the add to cart template
	 *
	 * @use yith_wcbk_booking_add_to_cart_form hook
	 */
	public function print_add_to_cart_template() {
		do_action( 'yith_wcbk_booking_add_to_cart_form' );
	}
}
