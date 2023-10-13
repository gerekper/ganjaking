<?php
/**
 * Class YITH_WCBK_Premium_Products
 * Handle products for the Premium module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Premium_Products' ) ) {
	/**
	 * YITH_WCBK_Premium_Products class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Premium_Products {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			add_filter( 'yith_wcbk_booking_product_pre_get_price_html', array( $this, 'filter_pre_get_price_html' ), 10, 2 );
			add_filter( 'yith_wcbk_booking_product_pre_get_price_to_store', array( $this, 'filter_pre_get_price_to_store' ), 10, 2 );
		}

		/**
		 * Filter price HTML before calculating, to allow showing price based on "partial totals".
		 *
		 * @param string|null        $price_html Price HTML.
		 * @param WC_Product_Booking $product    The booking product.
		 *
		 * @return string|null
		 */
		public function filter_pre_get_price_html( $price_html, WC_Product_Booking $product ) {
			$costs_included = yith_wcbk()->settings->get_costs_included_in_shown_price();

			if ( yith_wcbk()->settings->show_duration_unit_in_price() ) {
				$prices = array();

				if ( in_array( 'base-price', $costs_included, true ) && $product->get_base_price() ) {
					$base_price    = wc_price( yith_wcbk_get_price_to_display( $product, $product->get_base_price() ) );
					$duration      = $product->get_duration();
					$duration_unit = $product->get_duration_unit();
					if ( 'day' === $duration_unit && $duration && 0 === $duration % 7 && yith_wcbk()->settings->replace_days_with_weeks_in_price() ) {
						$duration      = $duration / 7;
						$duration_unit = 'week';
					}

					$prices[] = sprintf(
						'%s<span class="yith-wcbk-booking-product-price-unit"> / %s</span>',
						$base_price,
						wp_kses_post( yith_wcbk_format_duration( $duration, $duration_unit, 'period' ) )
					);
				}

				if ( in_array( 'fixed-base-fee', $costs_included, true ) && $product->get_fixed_base_fee() ) {
					$prices[] = wc_price( yith_wcbk_get_price_to_display( $product, $product->get_fixed_base_fee() ) );
				}

				if ( $prices ) {
					$price_html = implode( ' + ', $prices );
				}
			} else {
				$price      = $product->calculate_partial_price( $costs_included );
				$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $price ) );
			}

			return $price_html;
		}

		/**
		 * Filter price to store, to allow string price based on "partial totals".
		 *
		 * @param float|string|null  $price   Price HTML.
		 * @param WC_Product_Booking $product The booking product.
		 *
		 * @return float|string
		 */
		public function filter_pre_get_price_to_store( $price, WC_Product_Booking $product ): string {
			$included = yith_wcbk()->settings->get_costs_included_in_shown_price();

			return $product->calculate_partial_price( $included );
		}
	}
}
