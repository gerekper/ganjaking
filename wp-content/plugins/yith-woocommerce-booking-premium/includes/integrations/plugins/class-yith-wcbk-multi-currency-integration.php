<?php
/**
 * Class YITH_WCBK_Multi_Currency_Integration
 * Multi Currency integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Multi_Currency_Integration
 *
 * @since   1.0.1
 */
class YITH_WCBK_Multi_Currency_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		add_filter( 'yith_wcmcs_product_prices_options_group_classes', array( $this, 'add_class_to_multi_currency_options_group' ) );
		if ( $this->is_enabled() ) {
			add_filter( 'yith_wcmcs_apply_currency_filters', array( $this, 'apply_currency_filters_in_booking_admin_pages' ), 10, 1 );
			add_action( 'yith_wcbk_admin_booking_list_prepare_row_data', array( $this, 'switch_currency_based_on_booking_order' ), 10, 1 );
			add_filter( 'yith_wcbk_get_price_to_display', 'yith_wcmcs_convert_price' );
		}
	}

	/**
	 * Switch currency based on the booking order.
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function switch_currency_based_on_booking_order( $booking ) {
		$order = $booking->get_order();
		if ( $order ) {
			yith_wcmcs_set_currency( $order->get_currency() );
		}
	}

	/**
	 * Apply currency filters in booking admin pages.
	 *
	 * @param bool $apply Apply flag.
	 *
	 * @return bool
	 */
	public function apply_currency_filters_in_booking_admin_pages( $apply ) {
		global $pagenow;

		$post_type = wc_clean( wp_unslash( $_GET['post_type'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( YITH_WCBK_Post_Types::BOOKING === $post_type && 'edit.php' === $pagenow ) {
			$apply = true;
		}

		return $apply;
	}

	/**
	 * Add class to multi-currency options group in product edit page
	 * to hide the group for booking products.
	 *
	 * @param string[] $classes The classes.
	 *
	 * @return string[]
	 */
	public function add_class_to_multi_currency_options_group( $classes ) {
		$classes[] = 'hide_if_' . YITH_WCBK_Product_Post_Type_Admin::$prod_type;

		return $classes;
	}
}
