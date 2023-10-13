<?php
/**
 * Booking Search Form Results List Template
 * Shows list of booking search form results
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/results-list.php.
 *
 * @var WP_Query $products        WP Query for products.
 * @var array    $booking_request Booking request.
 * @var int      $current_page    Current page number.
 * @var array    $product_ids     Product IDs.
 *
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( $products->have_posts() ) {

	while ( $products->have_posts() ) {
		$products->the_post();
		/**
		 * The booking product.
		 *
		 * @var WC_Product_Booking $product
		 */
		global $product;

		$booking_request['add-to-cart'] = $product->get_id();
		$booking_data                   = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );
		$booking_data['bk-sf-res']      = true;

		if ( $product->is_full_day() && isset( $booking_data['to'] ) ) {
			// We need to take time at midnight, to prevent issues with duration.
			$booking_data['to'] = strtotime( 'midnight', $booking_data['to'] );
		}

		if ( isset( $booking_data['duration'] ) ) {
			// Duration is set in "units" (i.e. days), but we need it to be relative to the booking duration.
			$booking_data['duration'] = intdiv( $booking_data['duration'], $product->get_duration() );
		}

		$booking_data = $product->parse_booking_data_args( $booking_data );
		$the_price    = '';

		if ( ! empty( $booking_request['from'] ) && ! empty( $booking_request['to'] ) && 'day' === $product->get_duration_unit() ) {
			$the_price = $product->calculate_price( $booking_data );
			$product->set_price( $the_price );
		} else {
			unset( $booking_data['from'] );
			unset( $booking_data['to'] );
			unset( $booking_data['duration'] );
		}

		yith_wcbk_get_module_template( 'search-forms', 'results/single.php', compact( 'product', 'booking_data', 'the_price' ), 'booking/search-form/' );
	}

	wp_reset_postdata();
}
