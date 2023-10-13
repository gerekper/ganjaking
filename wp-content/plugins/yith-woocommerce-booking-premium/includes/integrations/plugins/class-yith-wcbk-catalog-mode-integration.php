<?php
/**
 * Class YITH_WCBK_Catalog_Mode_Integration
 * Catalog Mode integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Catalog_Mode_Integration
 *
 * @since   1.0.1
 */
class YITH_WCBK_Catalog_Mode_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			add_filter( 'yith_wcbk_search_form_item_add_to_cart_allowed', array( $this, 'check_add_to_cart_in_search_form_results' ), 10, 3 );
			add_filter( 'ywctm_ajax_admin_check', array( $this, 'check_admin_for_booking_ajax_call' ), 999 );
			add_filter( 'yith_wcbk_booking_product_get_calculated_price_html', array( $this, 'filter_booking_product_calculated_price_html' ), 10, 3 );
		}
	}

	/**
	 * Filter calculated price html for booking product through Catalog Mode
	 * to hide prices everywhere (also in AJAX call)
	 *
	 * @param string             $price_html Price HTML.
	 * @param string             $price      The price.
	 * @param WC_Product_Booking $product    The product.
	 *
	 * @return string
	 * @since 2.1.4
	 */
	public function filter_booking_product_calculated_price_html( $price_html, $price, $product ) {
		return YITH_WCTM()->show_product_price( $price_html, $product );
	}

	/**
	 * Check add-to-cart in search form results.
	 *
	 * @param bool               $add_to_cart_allowed True if add-to-cart is allowed.
	 * @param WC_Product_Booking $product             The booking product.
	 * @param array              $booking_data        The booking data.
	 *
	 * @return bool
	 */
	public function check_add_to_cart_in_search_form_results( $add_to_cart_allowed, $product, $booking_data ) {
		$hide = YITH_WCTM()->check_add_to_cart_single( true, yit_get_base_product_id( $product ) );

		return ! $hide;
	}

	/**
	 * Return False if it's a Booking AJAX call to hide the price correctly.
	 *
	 * @param bool $is_admin True if is admin side.
	 *
	 * @return bool
	 */
	public function check_admin_for_booking_ajax_call( $is_admin ) {
		if ( defined( 'YITH_WCBK_DOING_AJAX_FRONTEND' ) && YITH_WCBK_DOING_AJAX_FRONTEND ) {
			return false;
		}

		return $is_admin;
	}
}
