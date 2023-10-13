<?php
/**
 * Class YITH_WCBK_Search_Forms_Frontend
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Forms_Frontend' ) ) {
	/**
	 * Class YITH_WCBK_Search_Forms_Frontend
	 * handle Search Forms in frontend
	 */
	class YITH_WCBK_Search_Forms_Frontend {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Search_Form_Frontend constructor.
		 */
		protected function __construct() {
			add_action( 'yith_wcbk_booking_search_form_print_field', array( $this, 'print_field' ), 10, 3 );

			add_action( 'pre_get_posts', array( $this, 'filter_search_results_in_shop' ) );
			add_filter( 'woocommerce_loop_product_link', array( $this, 'add_booking_data_in_search_result_links' ), 10, 2 );
			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_booking_data_in_search_result_links' ), 10, 2 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'show_price_based_on_search_params' ), 10, 2 );
		}

		/**
		 * Is searching?
		 *
		 * @return bool
		 * @since 2.1.9
		 */
		protected function is_search(): bool {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return isset( $_REQUEST['yith-wcbk-booking-search'] ) && 'search-bookings' === $_REQUEST['yith-wcbk-booking-search'];
		}

		/**
		 * Show prices in Shop page based on search parameters
		 *
		 * @param string             $price_html Price HTML.
		 * @param WC_Product_Booking $product    The product.
		 *
		 * @return string
		 * @since 2.1.9
		 */
		public function show_price_based_on_search_params( $price_html, $product ) {
			if ( $this->is_search() && yith_wcbk_is_booking_product( $product ) && 'day' === $product->get_duration_unit() ) {
				// If search results are shown in Popup, the price is already calculated and set.
				// If search results are shown in Shop, we need to calculate the price based on search params.
				if ( function_exists( 'is_shop' ) && is_shop() ) {
					$booking_request                = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$booking_request['add-to-cart'] = $product->get_id();
					$booking_data                   = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );

					$booking_data['bk-sf-res'] = true;

					if ( isset( $booking_data['duration'] ) ) {
						$booking_data['duration'] = intdiv( $booking_data['duration'], $product->get_duration() );
					}

					$booking_data = $product->parse_booking_data_args( $booking_data );

					if ( ! empty( $booking_request['from'] ) && ! empty( $booking_request['to'] ) ) {
						$the_price = $product->calculate_price( $booking_data );

						$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $the_price ) );
					}
				}

				$price_html = apply_filters( 'yith_wcbk_get_price_based_on_search_param', $price_html, $product );
			}

			return $price_html;
		}

		/**
		 * Add booking data in product links when showing results in Shop Page
		 *
		 * @param string             $permalink The permalink.
		 * @param WC_Product_Booking $product   The product.
		 *
		 * @return string
		 * @since 2.0.6
		 */
		public function add_booking_data_in_search_result_links( $permalink, $product ) {
			if ( $this->is_search() && yith_wcbk_is_booking_product( $product ) && 'day' === $product->get_duration_unit() ) {
				$booking_request               = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$booking_request['product_id'] = $product->get_id();
				if ( isset( $booking_request['services'] ) ) {
					$booking_request['booking_services'] = $booking_request['services'];
				}

				if ( isset( $booking_request['duration'] ) ) {
					$booking_request['duration'] = intdiv( $booking_request['duration'], $product->get_duration() );
				}

				$booking_data              = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );
				$booking_data['bk-sf-res'] = true;

				if ( $product->is_full_day() && isset( $booking_data['to'] ) ) {
					// We need to take time at midnight, to prevent issues with duration.
					$booking_data['to'] = strtotime( 'midnight', $booking_data['to'] );
				}

				if ( isset( $booking_data['duration'] ) ) {
					// Duration is set in "units" (i.e. days), but we need it to be relative to the booking duration.
					$booking_data['duration'] = intdiv( $booking_data['duration'], $product->get_duration() );
				}

				$permalink = $product->get_permalink_with_data( $booking_data );
			}

			return $permalink;
		}

		/**
		 * Filter search results in shop
		 *
		 * @param WP_Query $query The query.
		 */
		public function filter_search_results_in_shop( $query ) {
			if ( $query->is_main_query() && $this->is_search() ) {
				$product_ids = yith_wcbk_search_booking_products( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( ! $product_ids ) {
					$product_ids = array( 0 );
				}

				$query->set( 'post__in', $product_ids );
			}
		}

		/**
		 * Print field.
		 *
		 * @param string                $field_name  Field name.
		 * @param array                 $field_data  Field data.
		 * @param YITH_WCBK_Search_Form $search_form Search form.
		 */
		public function print_field( $field_name, $field_data, $search_form ) {
			$template = $field_name;

			if ( ! empty( $field_data['type'] ) ) {
				$template .= '-' . $field_data['type'];
			}

			$template .= '.php';

			$args = array(
				'field_name'  => $field_name,
				'field_data'  => $field_data,
				'search_form' => $search_form,
			);

			yith_wcbk_get_module_template( 'search-forms', 'fields/' . $template, $args, 'booking/search-form/' );
		}
	}
}
