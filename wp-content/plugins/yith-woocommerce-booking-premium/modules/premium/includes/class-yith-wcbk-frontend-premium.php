<?php
/**
 * Class YITH_WCBK_Frontend_Premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Frontend_Premium' ) ) {
	/**
	 * YITH_WCBK_Frontend_Premium class.
	 */
	class YITH_WCBK_Frontend_Premium extends YITH_WCBK_Frontend {

		/**
		 * The constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_filter( 'yith_wcbk_show_booking_form', array( $this, 'maybe_filter_show_booking_form_for_logged_users_only' ), 10, 1 );
			add_filter( 'yith_wcbk_booking_form_totals_html', array( $this, 'show_booking_form_totals_html' ), 10, 5 );

			if ( 'yes' === get_option( 'yith-wcbk-hide-add-to-cart-button-in-loop', 'no' ) ) {
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_in_loop' ), 10, 2 );
			}
		}

		/**
		 * Hide add-to-cart button in loop for Booking products.
		 *
		 * @param string     $link_html The HTML link.
		 * @param WC_Product $product   The product.
		 */
		public function hide_add_to_cart_in_loop( $link_html, $product ) {
			if ( $product && yith_wcbk_is_booking_product( $product ) ) {
				$link_html = '';
			}

			return $link_html;
		}

		/**
		 * Filter show booking form.
		 *
		 * @param bool $should_show Should show booking form.
		 *
		 * @return bool
		 */
		public function maybe_filter_show_booking_form_for_logged_users_only( $should_show ) {
			if ( yith_wcbk()->settings->show_booking_form_to_logged_users_only() && ! is_user_logged_in() ) {
				echo wp_kses_post( apply_filters( 'yith_wcbk_show_booking_form_to_logged_users_only_non_logged_text', '<p>' . __( 'You must be logged in to book this product!', 'yith-booking-for-woocommerce' ) . '</p>' ) );

				if ( apply_filters( 'yith_wcbk_show_booking_form_to_logged_users_only_show_login_form', true ) ) {
					yith_wcbk_print_login_form( false, false );
				}

				$should_show = false;
			}

			return $should_show;
		}

		/**
		 * Show booking form totals.
		 *
		 * @param string             $totals_html The totals HTML (default: '').
		 * @param array              $totals      Calculated totals.
		 * @param string             $price_html  The price HTML.
		 * @param WC_Product_Booking $product     The product.
		 * @param array              $request     The AJAX request.
		 *
		 * @return string
		 */
		public function show_booking_form_totals_html( $totals_html, $totals, $price_html, $product, $request ) {
			if ( yith_wcbk()->settings->show_totals() ) {
				$args        = apply_filters( 'yith_wcbk_booking_form_totals_list', compact( 'totals', 'price_html', 'product' ), $totals, $price_html, $product, $request );
				$totals_html = yith_wcbk_get_module_template_html( 'premium', 'booking-form/totals-list.php', $args, 'single-product/add-to-cart/' );
			}

			return $totals_html;
		}

	}
}
