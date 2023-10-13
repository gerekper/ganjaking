<?php
/**
 * Template Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

/** -------------------------
 *          HOOKS
 * --------------------------
 */

/**
 * Booking form
 */
add_action( 'yith_wcbk_booking_form_meta', 'yith_wcbk_booking_form_meta', 10, 1 );
add_action( 'yith_wcbk_booking_form_start', 'yith_wcbk_booking_form_start', 10, 1 );
add_action( 'yith_wcbk_booking_form_content', 'yith_wcbk_booking_form_dates', 10, 1 );
add_action( 'yith_wcbk_booking_form_message', 'yith_wcbk_booking_form_message', 10, 1 );
add_action( 'yith_wcbk_booking_form_price', 'yith_wcbk_booking_form_price', 10, 1 );
add_action( 'yith_wcbk_booking_form_end', 'yith_wcbk_booking_form_end', 10, 1 );
add_action( 'yith_wcbk_booking_form_dates_duration', 'yith_wcbk_booking_form_dates_duration', 10, 1 );
add_action( 'yith_wcbk_booking_form_dates_date_fields', 'yith_wcbk_booking_form_dates_date_fields', 10, 1 );

add_action( 'yith_wcbk_booking_form', 'yith_wcbk_booking_form', 10, 2 );

/**
 * Emails
 */
add_action( 'yith_wcbk_email_booking_details', 'yith_wcbk_email_booking_details', 10, 4 );

/**
 * PDF Booking
 */
add_action( 'yith_wcbk_booking_pdf_template_footer', 'yith_wcbk_booking_pdf_footer', 10, 2 );
add_action( 'yith_wcbk_booking_pdf_template_header', 'yith_wcbk_booking_pdf_header', 10, 2 );
add_action( 'yith_wcbk_booking_pdf_template_content', 'yith_wcbk_booking_pdf_booking_details', 10, 2 );
add_action( 'yith_wcbk_booking_pdf_template_content', 'yith_wcbk_booking_pdf_user_info', 10, 2 );

/**
 * Booking Search Form Results
 */

add_action( 'yith_wcbk_search_form_item_thumbnails', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'yith_wcbk_search_form_item_thumbnails', 'yith_wcbk_search_form_item_thumbnails', 10 );

add_action( 'yith_wcbk_before_search_form_item_title', 'yith_wcbk_search_form_item_link_open', 10, 1 );

add_action( 'yith_wcbk_search_form_item_title', 'yith_wcbk_search_form_item_title', 10 );

add_action( 'yith_wcbk_after_search_form_item_title', 'yith_wcbk_search_form_item_link_close', 5 );

add_action( 'yith_wcbk_search_form_item_price', 'woocommerce_template_loop_price', 10 );

add_action( 'yith_wcbk_search_form_item_add_to_cart', 'yith_wcbk_search_form_item_add_to_cart', 10, 1 );


/**
 * View Booking in frontend
 */
add_action( 'yith_wcbk_show_bookings_table', 'yith_wcbk_show_bookings_table', 10 );
add_action( 'yith_wcbk_view_booking', 'yith_wcbk_booking_details_table', 10 );
add_action( 'yith_wcbk_booking_details_after_booking_table', 'yith_wcbk_booking_actions', 10 );
add_action( 'yith_wcbk_show_booking_actions', 'yith_wcbk_booking_actions', 10, 2 );

/**
 * Booking Form shortcode summary
 */
add_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form', 'woocommerce_template_single_title', 5 );
add_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form', 'woocommerce_template_single_rating', 10 );
add_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form', 'woocommerce_template_single_price', 10 );
add_action( 'yith_wcbk_booking_form_shortcode_after_add_to_cart_form', 'woocommerce_template_single_meta', 10 );
add_action( 'yith_wcbk_booking_form_shortcode_after_add_to_cart_form', 'woocommerce_template_single_sharing', 20 );

/**
 * Widget Booking Form shortcode summary
 */
add_action( 'yith_wcbk_widget_booking_form_head', 'woocommerce_template_single_price', 10 );
add_action( 'yith_wcbk_widget_booking_form_head', 'woocommerce_template_single_rating', 20 );


/** -------------------------
 *          FUNCTIONS
 * --------------------------
 */

if ( ! function_exists( 'yith_wcbk_search_form_item_thumbnails' ) ) {
	/**
	 * Search form item thumbnails
	 */
	function yith_wcbk_search_form_item_thumbnails() {
		if ( yith_wcbk_is_search_forms_module_active() ) {
			yith_wcbk_get_module_template( 'search-forms', 'results/single/thumbnails.php', array(), 'booking/search-form/' );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_search_form_item_add_to_cart' ) ) {
	/**
	 * Search form item add-to-cart
	 *
	 * @param array $booking_data Booking data.
	 */
	function yith_wcbk_search_form_item_add_to_cart( $booking_data ) {
		if ( yith_wcbk_is_search_forms_module_active() ) {
			yith_wcbk_get_module_template( 'search-forms', 'results/single/add-to-cart.php', compact( 'booking_data' ), 'booking/search-form/' );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_search_form_item_title' ) ) {
	/**
	 * Search form item title
	 */
	function yith_wcbk_search_form_item_title() {
		if ( yith_wcbk_is_search_forms_module_active() ) {
			yith_wcbk_get_module_template( 'search-forms', 'results/single/title.php', array(), 'booking/search-form/' );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_search_form_item_link_open' ) ) {
	/**
	 * Search form item link open.
	 *
	 * @param array $booking_data Booking data.
	 */
	function yith_wcbk_search_form_item_link_open( $booking_data = array() ) {
		if ( yith_wcbk_is_search_forms_module_active() ) {
			global $product;

			if ( isset( $booking_data['person_types'] ) ) {
				if ( $product->has_people_types_enabled() ) {
					$booking_data['person_types'] = yith_wcbk_booking_person_types_to_id_number_array( $booking_data['person_types'] );
				} else {
					unset( $booking_data['person_types'] );
				}
			}

			$link = $product->get_permalink_with_data( $booking_data );

			echo '<a href="' . esc_url( $link ) . '">';
		}
	}
}

if ( ! function_exists( 'yith_wcbk_search_form_item_link_close' ) ) {
	/**
	 * Search form item link close.
	 */
	function yith_wcbk_search_form_item_link_close() {
		if ( yith_wcbk_is_search_forms_module_active() ) {
			echo '</a>';
		}
	}
}

if ( ! function_exists( 'yith_wcbk_email_booking_details' ) ) {
	/**
	 * Email booking details.
	 *
	 * @param YITH_WCBK_Booking $booking       The booking.
	 * @param false             $sent_to_admin Sent to admin flag.
	 * @param false             $plain_text    Plain text flag.
	 * @param WC_Email|null     $email         The Email.
	 */
	function yith_wcbk_email_booking_details( $booking, $sent_to_admin = false, $plain_text = false, $email = null ) {
		if ( $plain_text ) {
			wc_get_template(
				'emails/plain/email-booking-details.php',
				array(
					'booking'       => $booking,
					'sent_to_admin' => $sent_to_admin,
					'plain_text'    => $plain_text,
					'email'         => $email,
				),
				'',
				YITH_WCBK_TEMPLATE_PATH
			);
		} else {
			wc_get_template(
				'emails/email-booking-details.php',
				array(
					'booking'       => $booking,
					'sent_to_admin' => $sent_to_admin,
					'plain_text'    => $plain_text,
					'email'         => $email,
				),
				'',
				YITH_WCBK_TEMPLATE_PATH
			);
		}
	}
}

if ( ! function_exists( 'yith_wcbk_booking_pdf_footer' ) ) {
	/**
	 * Booking PDF Footer.
	 *
	 * @param YITH_WCBK_Booking $booking  The booking.
	 * @param bool              $is_admin Is admin flag.
	 */
	function yith_wcbk_booking_pdf_footer( $booking, $is_admin ) {
		if ( ! $booking ) {
			return;
		}

		$args = array(
			'footer'   => '',
			'booking'  => $booking,
			'is_admin' => $is_admin,
		);
		wc_get_template( 'booking/pdf/footer.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_pdf_header' ) ) {
	/**
	 * Booking PDF Header.
	 *
	 * @param YITH_WCBK_Booking $booking  The booking.
	 * @param bool              $is_admin Is admin flag.
	 */
	function yith_wcbk_booking_pdf_header( $booking, $is_admin ) {
		if ( ! $booking ) {
			return;
		}

		$args = array(
			'booking'  => $booking,
			'is_admin' => $is_admin,

		);
		wc_get_template( 'booking/pdf/header.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_pdf_booking_details' ) ) {
	/**
	 * Booking PDF details.
	 *
	 * @param YITH_WCBK_Booking $booking  The booking.
	 * @param bool              $is_admin Is admin flag.
	 */
	function yith_wcbk_booking_pdf_booking_details( $booking, $is_admin ) {
		if ( ! $booking ) {
			return;
		}

		$args = array(
			'booking'  => $booking,
			'is_admin' => $is_admin,

		);
		wc_get_template( 'booking/pdf/booking-details.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_pdf_user_info' ) ) {
	/**
	 * Booking PDF User info.
	 *
	 * @param YITH_WCBK_Booking $booking  The booking.
	 * @param bool              $is_admin Is admin flag.
	 */
	function yith_wcbk_booking_pdf_user_info( $booking, $is_admin ) {
		if ( ! $booking ) {
			return;
		}

		$args = array(
			'booking'  => $booking,
			'is_admin' => $is_admin,

		);
		wc_get_template( 'booking/pdf/user-info.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
	}
}


if ( ! function_exists( 'yith_wcbk_show_bookings_table' ) ) {
	/**
	 * Displays bookings in a table.
	 *
	 * @param array $bookings Bookings.
	 */
	function yith_wcbk_show_bookings_table( $bookings ) {
		if ( ! $bookings ) {
			return;
		}
		if ( ! is_array( $bookings ) ) {
			$bookings = array( $bookings );
		}

		$args = array(
			'bookings'     => $bookings,
			'has_bookings' => ! ! $bookings,
		);
		wc_get_template( 'myaccount/bookings-table.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
	}
}


if ( ! function_exists( 'yith_wcbk_booking_details_table' ) ) {
	/**
	 * Displays booking details in a table.
	 *
	 * @param int $booking_id Booking ID.
	 */
	function yith_wcbk_booking_details_table( $booking_id ) {
		if ( ! $booking_id ) {
			return;
		}

		$booking = yith_get_booking( $booking_id );

		if ( ! $booking || ! $booking->is_valid() ) {
			return;
		}

		wc_get_template(
			'booking/booking-details.php',
			array(
				'booking_id' => $booking_id,
				'booking'    => $booking,
			),
			'',
			YITH_WCBK_TEMPLATE_PATH
		);
	}
}

if ( ! function_exists( 'yith_wcbk_booking_actions' ) ) {
	/**
	 * Displays booking actions.
	 *
	 * @param YITH_WCBK_Booking $booking          Booking.
	 * @param bool              $show_view_action Show 'view' action flag.
	 */
	function yith_wcbk_booking_actions( $booking, $show_view_action = false ) {
		if ( ! $booking || ! $booking->is_valid() ) {
			return;
		}

		wc_get_template(
			'booking/booking-actions.php',
			array(
				'booking'          => $booking,
				'show_view_action' => $show_view_action,
			),
			'',
			YITH_WCBK_TEMPLATE_PATH
		);
	}
}

/**
 * Print booking form.
 *
 * @param WC_Product $product The product.
 * @param array      $args    Arguments.
 */
function yith_wcbk_booking_form( $product, $args = array() ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}

	$defaults = array(
		'show_price'      => false,
		'additional_data' => array(),
	);
	$args     = wp_parse_args( $args, $defaults );

	/**
	 * Hook yith_wcbk_booking_form_start
	 *
	 * @hooked yith_wcbk_booking_form_start - 10
	 */
	do_action( 'yith_wcbk_booking_form_start', $product );

	foreach ( $args['additional_data'] as $_key => $_value ) {
		$_key   = sanitize_key( $_key );
		$_value = sanitize_text_field( $_value );
		echo sprintf(
			'<input type="hidden" class="yith-wcbk-booking-form-additional-data" name="%s" value="%s" />',
			esc_attr( $_key ),
			esc_attr( $_value )
		);
	}

	/**
	 * Hook yith_wcbk_booking_form_fields
	 *
	 * @hooked yith_wcbk_booking_form_dates - 10
	 */
	do_action( 'yith_wcbk_booking_form_content', $product );

	if ( $args['show_price'] ) {
		/**
		 * Hook yith_wcbk_booking_form_price
		 *
		 * @hooked yith_wcbk_booking_form_price - 10
		 */
		do_action( 'yith_wcbk_booking_form_price', $product );
	}

	/**
	 * Hook yith_wcbk_booking_form_message
	 *
	 * @hooked yith_wcbk_booking_form_message - 10
	 */
	do_action( 'yith_wcbk_booking_form_message', $product );

	/**
	 * Hook yith_wcbk_booking_form_end
	 *
	 * @hooked yith_wcbk_booking_form_end - 10
	 */
	do_action( 'yith_wcbk_booking_form_end', $product );
}

/**
 * Booking form meta.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_meta( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}
	wc_get_template( 'single-product/add-to-cart/booking-form/meta.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * Booking form start.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_start( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}
	wc_get_template( 'single-product/add-to-cart/booking-form/start.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * Booking form persons.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_persons( $product ) {
	if ( $product instanceof WC_Product_Booking && $product->has_people() && yith_wcbk_is_people_module_active() ) {
		yith_wcbk_get_module_template( 'people', 'booking-form/persons.php', compact( 'product' ), 'single-product/add-to-cart/' );
	}
}

/**
 * Booking form dates.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_dates( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}
	wc_get_template( 'single-product/add-to-cart/booking-form/dates.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * Booking form dates duration.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_dates_duration( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}

	$unit                = $product->get_duration_unit();
	$show_duration_field = 'month' === $unit || ! $product->has_calendar_picker_enabled();

	if ( $show_duration_field ) {
		wc_get_template( 'single-product/add-to-cart/booking-form/dates/duration.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
	}
}

/**
 * Booking form date fields.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_dates_date_fields( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}

	wc_get_template( 'single-product/add-to-cart/booking-form/dates/dates.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * Booking form services.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_services( $product ) {
	if ( $product instanceof WC_Product_Booking && $product->has_services() ) {
		yith_wcbk_get_module_template( 'services', 'booking-form/services.php', compact( 'product' ), 'single-product/add-to-cart/' );
	}
}

/**
 * Booking form message.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_message( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}
	wc_get_template( 'single-product/add-to-cart/booking-form/message.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * Booking form price.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_price( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}
	wc_get_template( 'single-product/add-to-cart/booking-form/price.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * Booking form end.
 *
 * @param WC_Product $product The product.
 */
function yith_wcbk_booking_form_end( $product ) {
	if ( ! $product || ! $product instanceof WC_Product_Booking ) {
		return;
	}
	wc_get_template( 'single-product/add-to-cart/booking-form/end.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}
