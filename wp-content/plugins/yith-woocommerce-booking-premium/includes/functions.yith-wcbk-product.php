<?php
/**
 * Product Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_is_booking_product' ) ) {
	/**
	 * Return true if the product is Booking Product
	 *
	 * @param bool|int|WP_Post|WC_Product $product The product.
	 *
	 * @return bool
	 */
	function yith_wcbk_is_booking_product( $product = null ): bool {
		return YITH_WCBK_Product_Post_Type_Admin::is_booking( $product );
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_product' ) ) {
	/**
	 * Get a booking product.
	 *
	 * @param bool|int|WP_Post|WC_Product $product The product.
	 *
	 * @return WC_Product_Booking|false
	 */
	function yith_wcbk_get_booking_product( $product = false ) {
		$product = wc_get_product( $product );

		return ! ! $product && yith_wcbk_is_booking_product( $product ) ? $product : false;
	}
}

if ( ! function_exists( 'yith_wcbk_get_product_availability_per_units' ) ) {
	/**
	 * Get product availability per nits.
	 *
	 * @param WC_Product_Booking $product Booking product.
	 * @param int                $from    From timestamp.
	 * @param int                $to      To timestamp.
	 *
	 * @return array
	 * @since 2.0.3
	 */
	function yith_wcbk_get_product_availability_per_units( WC_Product_Booking $product, $from, $to ) {
		$_args     = array(
			'product_id'                => $product->get_id(),
			'from'                      => $from,
			'to'                        => $to,
			'include_externals'         => $product->has_external_calendars(),
			'unit'                      => $product->get_duration_unit(),
			'count_persons_as_bookings' => $product->has_count_people_as_separate_bookings_enabled(),
		);
		$_booked   = yith_wcbk_booking_helper()->count_max_booked_bookings_per_unit_in_period( $_args );
		$_max      = $product->get_max_bookings_per_unit();
		$_bookable = $_max - $_booked;

		return array(
			'booked'   => $_booked,
			'bookable' => $_bookable,
			'max'      => $_max,
		);
	}
}

if ( ! function_exists( 'yith_wcbk_get_calendar_product_availability_per_units_html' ) ) {
	/**
	 * Get calendar product availability per unit HTML.
	 *
	 * @param WC_Product_Booking $product Booking product.
	 * @param int                $from    From timestamp.
	 * @param int                $to      To timestamp.
	 * @param string             $step    The step.
	 *
	 * @return string
	 * @since 2.0.3
	 */
	function yith_wcbk_get_calendar_product_availability_per_units_html( WC_Product_Booking $product, $from, $to, $step = '' ) {
		$html = '';
		if ( $product->can_show_availability( $step ) ) {
			$classes = array( 'yith-wcbk-booking-calendar-availability' );

			$_args = array(
				'from'                        => $from,
				'exclude_booked'              => true,
				'check_non_available_in_past' => false,
			);

			if ( $product->is_available( $_args ) ) {
				$availability = yith_wcbk_get_product_availability_per_units( $product, $from, $to );
				$classes[]    = 'yith-wcbk-booking-calendar-availability--left';
				$classes      = apply_filters( 'yith_wcbk_booking_calendar_availability_classes', $classes, $availability, $product );
				// translators: %s is the number of 'seats' left.
				$availability_text = sprintf( __( '%s left', 'yith-booking-for-woocommerce' ), $availability['bookable'] );
			} else {
				$classes[]         = 'yith-wcbk-booking-calendar-availability--non-bookable';
				$availability_text = __( 'Non Bookable', 'yith-booking-for-woocommerce' );
			}

			$classes = implode( ' ', $classes );
			$html    = '<div class="' . esc_attr( $classes ) . '">' . esc_html( $availability_text ) . '</div>';
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_wcbk_generate_external_calendars_key' ) ) {
	/**
	 * Generate external calendars key.
	 *
	 * @return false|string
	 */
	function yith_wcbk_generate_external_calendars_key() {
		return wp_hash( wp_generate_password() );
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_form_date_info' ) ) {
	/**
	 * Get booking form date info.
	 *
	 * @param WC_Product_Booking $product Booking product.
	 * @param array              $args    Arguments.
	 *
	 * @return array
	 */
	function yith_wcbk_get_booking_form_date_info( $product, $args = array() ) {
		$default_args                     = array(
			'include_default_start_date' => true,
			'include_default_end_date'   => true,
			'start'                      => 'now',
			'months_to_load'             => false,
		);
		$args                             = wp_parse_args( $args, $default_args );
		$minimum_advance_reservation      = apply_filters( 'yith_wcbk_get_minimum_advance_reservation', $product->get_minimum_advance_reservation() );
		$minimum_advance_reservation_unit = apply_filters( 'yith_wcbk_get_minimum_advance_reservation_unit', $product->get_minimum_advance_reservation_unit() );
		$allow_until                      = apply_filters( 'yith_wcbk_get_maximum_advance_reservation', $product->get_maximum_advance_reservation(), $product );
		$allow_until_unit                 = apply_filters( 'yith_wcbk_get_maximum_advance_reservation_unit', $product->get_maximum_advance_reservation_unit(), $product );
		$default_start_date               = '';
		$default_end_date                 = '';
		$start                            = strtotime( $args['start'] );

		if ( $args['include_default_start_date'] ) {
			$default_start_date = yith_wcbk_get_query_string_param( 'from' );
			$default_start_date = ! ! $default_start_date ? $default_start_date : $product->get_calculated_default_start_date();
		}

		if ( $args['include_default_end_date'] ) {
			$default_end_date = yith_wcbk_get_query_string_param( 'to' );
			$default_end_date = ! ! $default_end_date ? $default_end_date : '';
		}

		$min_date           = '';
		$min_date_timestamp = $start;
		if ( in_array( $minimum_advance_reservation_unit, array( 'month', 'day', 'hour' ), true ) ) {
			if ( in_array( $minimum_advance_reservation_unit, array( 'month', 'day' ), true ) ) {
				$min_date = '+' . $minimum_advance_reservation . strtoupper( substr( $minimum_advance_reservation_unit, 0, 1 ) );
			} else {
				$min_date = $product->has_time() ? '+0D' : '+1D';
			}
			$min_date_midnight  = in_array( $minimum_advance_reservation_unit, array( 'month', 'day' ), true ) ? 'midnight' : '';
			$min_date_timestamp = strtotime( "+{$minimum_advance_reservation} {$minimum_advance_reservation_unit}s {$min_date_midnight}", yith_wcbk_get_local_timezone_timestamp() );
		}

		$min_date = apply_filters( 'yith_wcbk_min_date', $min_date, $product, $args );
		$min_date = apply_filters( 'yith_wcbk_get_booking_form_date_info_min_date', $min_date, $product, $args );

		$current_year  = absint( gmdate( 'Y', $start ) );
		$current_month = absint( gmdate( 'm', $start ) );

		/**
		 * Force $months_to_load to 3 months MAX (if hourly booking) or 1 month MAX (if minutely booking)
		 * to prevent loading issue if disable-day-if-no-time-available is active
		 * set default months to 12 if duration unit is month
		 */
		$months_to_load = false !== $args['months_to_load'] ? $args['months_to_load'] : yith_wcbk()->settings->get_months_loaded_in_calendar();
		if ( $product->has_time() && 'yes' === yith_wcbk()->settings->get( 'disable-day-if-no-time-available', 'no' ) ) {
			$max_months_to_load = yith_wcbk_get_max_months_to_load( $product->get_duration_unit() );
			$months_to_load     = min( $months_to_load, $max_months_to_load );
		} elseif ( 'month' === $product->get_duration_unit() ) {
			$months_to_load = 12;
		}

		$max_date           = '';
		$max_date_timestamp = strtotime( 'first day of this month', strtotime( "+ $months_to_load months", $start ) );
		$ajax_load_months   = true;
		if ( in_array( $allow_until_unit, array( 'year', 'month', 'day', 'hour' ), true ) ) {
			$max_date         = '+' . $allow_until . strtoupper( substr( $allow_until_unit, 0, 1 ) );
			$ajax_load_months = $max_date_timestamp < strtotime( '+' . $allow_until . ' ' . $allow_until_unit . 's' );

			if ( 'month' === $product->get_duration_unit() ) {
				$max_date_timestamp = strtotime( '+' . $allow_until . ' ' . $allow_until_unit . 's' );
			}
		}

		$max_date = apply_filters( 'yith_wcbk_get_booking_form_date_info_max_date', $max_date, $product, $args );

		$next_year  = absint( gmdate( 'Y', $max_date_timestamp ) );
		$next_month = absint( gmdate( 'm', $max_date_timestamp ) );

		if ( 'month' === $product->get_duration_unit() ) {
			$next_year ++;
			$next_month = 1;
		}

		$loaded_months = array();
		for ( $year = $current_year; $year <= $next_year; $year ++ ) {
			$first_month = $year === $current_year ? $current_month : 1;
			$last_month  = $year === $next_year ? ( $next_month - 1 ) : 12; // last month is not included.
			for ( $month = $first_month; $month <= $last_month; $month ++ ) {
				$loaded_months[] = $year . '-' . ( $month > 9 ? $month : ( '0' . $month ) );
			}
		}

		return compact( 'current_year', 'current_month', 'next_year', 'next_month', 'min_date', 'min_date_timestamp', 'max_date', 'max_date_timestamp', 'default_start_date', 'default_end_date', 'months_to_load', 'ajax_load_months', 'loaded_months' );
	}
}

if ( ! function_exists( 'yith_wcbk_sync_booking_product_prices' ) ) {
	/**
	 * Sync prices for booking products
	 *
	 * @return bool
	 * @since 2.0.8
	 */
	function yith_wcbk_sync_booking_product_prices(): bool {
		if ( yith_wcbk_sync_booking_product_prices_is_running() ) {
			return true;
		}

		$product_ids = wc_get_products(
			array(
				'type'   => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
				'limit'  => - 1,
				'return' => 'ids',
			)
		);

		if ( ! ! $product_ids ) {
			$index  = 1;
			$is_cli = defined( 'WP_CLI' ) && WP_CLI;

			while ( $product_ids ) {
				$batch_ids = array_slice( $product_ids, 0, 20 );

				if ( $is_cli ) {
					yith_wcbk_sync_booking_product_prices_batch( $batch_ids );
				} else {
					WC()->queue()->schedule_single(
						time() + $index,
						'yith_wcbk_run_callback',
						array(
							'callback' => 'yith_wcbk_sync_booking_product_prices_batch',
							'args'     => array( $batch_ids ),
						),
						'yith_wcbk_sync_booking_product_prices'
					);
				}

				$index ++;

				$product_ids = array_diff( $product_ids, $batch_ids );
			}

			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_sync_booking_product_prices_is_running' ) ) {
	/**
	 * Check if we're sync booking product prices.
	 *
	 * @return bool
	 * @since 3.0.0
	 */
	function yith_wcbk_sync_booking_product_prices_is_running(): bool {
		$table_updates_pending = WC()->queue()->search(
			array(
				'status'   => 'pending',
				'group'    => 'yith_wcbk_sync_booking_product_prices',
				'per_page' => 1,
			)
		);

		return (bool) count( $table_updates_pending );
	}
}

if ( ! function_exists( 'yith_wcbk_sync_booking_product_prices_batch' ) ) {
	/**
	 * Trigger next batch sync for booking product prices.
	 *
	 * @param int[] $product_ids Product IDs.
	 *
	 * @since 3.0.0
	 */
	function yith_wcbk_sync_booking_product_prices_batch( $product_ids ) {
		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			yith_wcbk_product_price_sync( $product );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_product_price_sync' ) ) {
	/**
	 * Sync booking product price
	 *
	 * @param int|WC_Product_Booking $product Booking product.
	 *
	 * @return bool
	 * @since 2.1.0
	 */
	function yith_wcbk_product_price_sync( $product ) {
		$product = wc_get_product( $product );

		if ( $product ) {
			try {
				/**
				 * The booking product data store.
				 *
				 * @var YITH_WCBK_Product_Booking_Data_Store_CPT $data_store
				 */
				$data_store = WC_Data_Store::load( 'product-booking' );

				return $data_store->sync_booking_price( $product );
			} catch ( Exception $e ) {
				$message = sprintf( 'Error when trying to sync bookable product #%s price. Exception: %s', $product->get_id(), $e->getMessage() );
				yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_product_update_external_calendars_last_sync' ) ) {
	/**
	 * Update the last sync for external calendars
	 *
	 * @param WC_Product_Booking $product   Booking product.
	 * @param null|int           $last_sync Last sync timestamp.
	 *
	 * @return bool
	 */
	function yith_wcbk_product_update_external_calendars_last_sync( $product, $last_sync = null ) {
		$product = wc_get_product( $product );

		if ( $product ) {
			try {
				/**
				 * The booking product data store.
				 *
				 * @var YITH_WCBK_Product_Booking_Data_Store_CPT $data_store
				 */
				$data_store = WC_Data_Store::load( 'product-booking' );

				return $data_store->update_external_calendars_last_sync( $product, $last_sync );
			} catch ( Exception $e ) {
				$message = sprintf( 'Error when trying to update last sync for external calendars on product #%s. Exception: %s', $product->get_id(), $e->getMessage() );
				yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_product_delete_external_calendars_last_sync' ) ) {
	/**
	 * Delete the last sync for external calendars
	 *
	 * @param WC_Product_Booking $product Booking product.
	 *
	 * @return bool
	 */
	function yith_wcbk_product_delete_external_calendars_last_sync( $product ) {
		return yith_wcbk_product_update_external_calendars_last_sync( $product, 0 );
	}
}

if ( ! function_exists( 'yith_wcbk_get_price_to_display' ) ) {
	/**
	 * Get price to display based on tax settings
	 *
	 * @param WC_Product $product        Booking product.
	 * @param string     $price          The price.
	 * @param bool       $allow_negative Allow negative price flag.
	 *
	 * @return float|int
	 * @since 2.1
	 */
	function yith_wcbk_get_price_to_display( $product, $price, $allow_negative = true ) {
		$price_to_display = $price;
		$is_negative      = $price < 0;

		$allow_negative && $is_negative && ( $price_to_display *= - 1 );
		$old_price = $product->get_price( 'edit' );
		$product->set_price( 1 ); // Prevent memory issues when getting prices (avoid calculate_price was fired).

		$price_to_display = wc_get_price_to_display( $product, array( 'price' => $price_to_display ) );

		$product->set_price( $old_price );
		$allow_negative && $is_negative && ( $price_to_display *= - 1 );

		return apply_filters( 'yith_wcbk_get_price_to_display', $price_to_display, $product, $price, $allow_negative );
	}
}

if ( ! function_exists( 'yith_wcbk_get_formatted_price_to_display' ) ) {
	/**
	 * Get formatted price to display based on tax settings
	 *
	 * @param WC_Product $product        Booking product.
	 * @param string     $price          The price.
	 * @param bool       $allow_negative Allow negative price flag.
	 *
	 * @return string
	 * @since 2.1
	 */
	function yith_wcbk_get_formatted_price_to_display( $product, $price, $allow_negative = true ) {
		return wc_price( yith_wcbk_get_price_to_display( $product, $price, $allow_negative ) );
	}
}

if ( ! function_exists( 'yith_wcbk_form_field' ) ) {
	/**
	 * Print a form field.
	 *
	 * @param array $field The field.
	 *
	 * @since 2.1.0
	 */
	function yith_wcbk_form_field( $field ) {
		$defaults  = array(
			'class'     => '',
			'title'     => '',
			'label_for' => '',
			'desc'      => '',
			'inline'    => false,
			'data'      => array(),
			'fields'    => array(),
		);
		$field     = wp_parse_args( $field, $defaults );
		$class     = $field['class'];
		$title     = $field['title'];
		$label_for = $field['label_for'];
		$desc      = $field['desc'];
		$data      = is_array( $field['data'] ) ? $field['data'] : array();
		$inline    = ! ! $field['inline'];
		$fields    = is_array( $field['fields'] ) ? $field['fields'] : array();

		if ( isset( $fields['type'] ) ) {
			$fields = array( $fields );
		}

		if ( ! $label_for && $fields ) {
			$first_field = current( $fields );
			if ( isset( $first_field['id'] ) ) {
				$label_for = $first_field['id'];
			}
		}

		$data_html = yith_plugin_fw_html_data_to_string( $data, false );

		if ( $inline ) {
			$class .= ' yith-wcbk-form-field--inline';
		}

		$html = '<div class="yith-wcbk-form-field ' . esc_attr( $class ) . '" ' . $data_html . '>';

		$html .= '<label class="yith-wcbk-form-field__label" for="' . esc_attr( $label_for ) . '">' . wp_kses_post( $title ) . '</label>';

		$html .= "<div class='yith-wcbk-form-field__content'>";
		$html .= "<div class='yith-wcbk-form-field__container'>";
		ob_start();
		yith_wcbk_print_fields( $fields );
		$html .= ob_get_clean();
		$html .= '</div><!-- yith-wcbk-form-field__container -->';

		if ( $desc ) {
			$html .= '<div class="yith-wcbk-form-field__description">' . wp_kses_post( $desc ) . '</div>';
		}

		$html .= '</div><!-- yith-wcbk-form-field__content -->';
		$html .= '</div><!-- yith-wcbk-form-field -->';

		echo apply_filters( 'yith_wcbk_product_metabox_form_field_html', $html, $field ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_dynamic_duration' ) ) {
	/**
	 * Print an element that updates his text based on product duration
	 *
	 * @return string
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_dynamic_duration() {
		return "<span class='yith-wcbk-product-metabox-dynamic-duration'></span>";
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_dynamic_duration_qty' ) ) {
	/**
	 * Print an element that updates his text based on product duration
	 *
	 * @return string
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_dynamic_duration_qty() {
		return "<span class='yith-wcbk-product-metabox-dynamic-duration-qty'></span>";
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_dynamic_duration_unit' ) ) {
	/**
	 * Print an element that updates his text based on product duration
	 *
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_dynamic_duration_unit() {
		return "<span class='yith-wcbk-product-metabox-dynamic-duration-unit'></span>";
	}
}

if ( ! function_exists( 'yith_wcbk_product_booking_of_name' ) ) {
	/**
	 * Prepend "Booking of" to a product name.
	 *
	 * @param string $name The product name.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_product_booking_of_name( $name ) {
		return '<span class="yith-wcbk-booking-of">' . wp_kses_post( yith_wcbk_get_label( 'booking-of' ) ) . '</span> ' . $name;
	}
}

if ( ! function_exists( 'yith_wcbk_regenerate_product_data' ) ) {
	/**
	 * Clear cache and schedule data regeneration for a specific product
	 *
	 * @param WC_Product|int $product The product object or the product ID.
	 *
	 * @since 3.0.0
	 */
	function yith_wcbk_regenerate_product_data( $product ) {
		$product_id = is_a( $product, 'WC_Product' ) ? $product->get_id() : absint( $product );
		if ( $product_id ) {
			$additional_cache_contexts = array( 'admin' );
			foreach ( $additional_cache_contexts as $context ) {
				yith_wcbk_cache()->set_context( $context );
				yith_wcbk_cache()->delete_product_data( $product_id );
			}

			yith_wcbk_cache()->set_default_context();
			yith_wcbk_cache()->delete_product_data( $product_id );

			yith_wcbk()->background_processes->schedule_product_data_update( $product_id );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_costs_included_in_shown_price_options' ) ) {
	/**
	 * Retrieve the options for costs included in shown price.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	function yith_wcbk_get_costs_included_in_shown_price_options(): array {
		$options = array(
			'base-price'     => __( 'Base Price', 'yith-booking-for-woocommerce' ),
			'fixed-base-fee' => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
		);

		return apply_filters( 'yith_wcbk_costs_included_in_shown_price_options', $options );
	}
}
