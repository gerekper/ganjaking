<?php

/**
 * Bookings ajax callbacks.
 */
class WC_Bookings_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// TODO: Switch from `wp_ajax` to `wc_ajax`
		add_action( 'wp_ajax_woocommerce_add_bookable_resource', array( $this, 'add_bookable_resource' ) );
		add_action( 'wp_ajax_woocommerce_remove_bookable_resource', array( $this, 'remove_bookable_resource' ) );
		add_action( 'wp_ajax_woocommerce_add_bookable_person', array( $this, 'add_bookable_person' ) );
		add_action( 'wp_ajax_woocommerce_unlink_bookable_person', array( $this, 'unlink_bookable_person' ) );
		add_action( 'wp_ajax_wc-booking-confirm', array( $this, 'mark_booking_confirmed' ) );
		add_action( 'wp_ajax_wc_bookings_calculate_costs', array( $this, 'calculate_costs' ) );
		add_action( 'wp_ajax_nopriv_wc_bookings_calculate_costs', array( $this, 'calculate_costs' ) );
		add_action( 'wp_ajax_wc_bookings_get_blocks', array( $this, 'get_time_blocks_for_date' ) );
		add_action( 'wp_ajax_nopriv_wc_bookings_get_blocks', array( $this, 'get_time_blocks_for_date' ) );
		add_action( 'wp_ajax_wc_bookings_get_end_time_html', array( $this, 'get_end_time_html' ) );
		add_action( 'wp_ajax_nopriv_wc_bookings_get_end_time_html', array( $this, 'get_end_time_html' ) );
		add_action( 'wp_ajax_wc_bookings_json_search_order', array( $this, 'json_search_order' ) );
		add_action( 'wp_ajax_wc_bookings_add_store_availability_rule', array( $this, 'add_store_availability_rule' ) );
		add_action( 'wp_ajax_wc_bookings_get_store_availability_rules', array( $this, 'get_store_availability_rules' ) );
		add_action( 'wp_ajax_wc_bookings_update_store_availability_rule', array( $this, 'update_store_availability_rule' ) );
		add_action( 'wp_ajax_wc_bookings_delete_store_availability_rules', array( $this, 'delete_store_availability_rules' ) );
	}

	/**
	 * Add resource link to product.
	 */
	public function add_bookable_resource() {
		check_ajax_referer( 'add-bookable-resource', 'security' );

		$post_id           = intval( $_POST['post_id'] );
		$loop              = intval( $_POST['loop'] );
		$add_resource_id   = intval( $_POST['add_resource_id'] );
		$add_resource_name = wc_clean( $_POST['add_resource_name'] );

		if ( ! $add_resource_id ) {
			$resource = new WC_Product_Booking_Resource();
			$resource->set_name( $add_resource_name );
			$add_resource_id = $resource->save();
		} else {
			$resource = new WC_Product_Booking_Resource( $add_resource_id );
		}

		if ( $add_resource_id ) {
			$product        = get_wc_product_booking( $post_id );
			$resource_ids   = $product->get_resource_ids();

			if ( in_array( $add_resource_name, $resource_ids ) ) {
				wp_send_json( array( 'error' => __( 'The resource has already been linked to this product', 'woocommerce-bookings' ) ) );
			}

			$resource_ids[] = $add_resource_id;
			$product->set_resource_ids( $resource_ids );
			$product->save();

			// get the post object due to it is used in the included template
			$post = get_post( $post_id );

			ob_start();
			include( 'views/html-booking-resource.php' );
			wp_send_json( array( 'html' => ob_get_clean() ) );
		}

		wp_send_json( array( 'error' => __( 'Unable to add resource', 'woocommerce-bookings' ) ) );
	}

	/**
	 * Remove resource link from product.
	 */
	public function remove_bookable_resource() {
		check_ajax_referer( 'delete-bookable-resource', 'security' );

		$post_id      = absint( $_POST['post_id'] );
		$resource_id  = absint( $_POST['resource_id'] );
		$product      = get_wc_product_booking( $post_id );
		$resource_ids = $product->get_resource_ids();
		$resource_ids = array_diff( $resource_ids, array( $resource_id ) );
		$product->set_resource_ids( $resource_ids );
		$product->save();
		die();
	}

	/**
	 * Add person type.
	 */
	public function add_bookable_person() {
		check_ajax_referer( 'add-bookable-person', 'security' );

		$post_id = intval( $_POST['post_id'] );
		$loop    = intval( $_POST['loop'] );

		$person_type = new WC_Product_Booking_Person_Type();
		$person_type->set_parent_id( $post_id );
		$person_type->set_sort_order( $loop );
		$person_type_id = $person_type->save();

		if ( $person_type_id ) {
			include( 'views/html-booking-person.php' );
		}
		die();
	}

	/**
	 * Remove person type.
	 */
	public function unlink_bookable_person() {
		check_ajax_referer( 'unlink-bookable-person', 'security' );

		$person_type_id = intval( $_POST['person_id'] );
		$person_type    = new WC_Product_Booking_Person_Type( $person_type_id );
		$person_type->set_parent_id( 0 );
		$person_type->save();
		die();
	}

	/**
	 * Mark a booking confirmed.
	 */
	public function mark_booking_confirmed() {
		if ( ! current_user_can( 'edit_wc_bookings' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'woocommerce-bookings' ) );
		}
		if ( ! check_admin_referer( 'wc-booking-confirm' ) ) {
			wp_die( esc_html__( 'You have taken too long. Please go back and retry.', 'woocommerce-bookings' ) );
		}
		$booking_id = isset( $_GET['booking_id'] ) && (int) $_GET['booking_id'] ? (int) $_GET['booking_id'] : '';
		if ( ! $booking_id ) {
			die;
		}

		$booking = get_wc_booking( $booking_id );

		if ( 'confirmed' !== $booking->get_status() ) {
			$booking->update_status( 'confirmed' );
		}

		wp_safe_redirect( wp_get_referer() );
		die();
	}

	/**
	 * Calculate costs.
	 *
	 * Take posted booking form values and then use these to quote a price for what has been chosen.
	 * Returns a string which is appended to the booking form.
	 */
	public function calculate_costs() {
		$posted = array();

		parse_str( $_POST['form'], $posted );

		$booking_id = $posted['add-to-cart'];
		$product    = wc_get_product( $booking_id );

		if ( ! $product ) {
			wp_send_json( array(
				'result' => 'ERROR',
				'html'   => apply_filters( 'woocommerce_bookings_calculated_booking_cost_error_output', '<span class="booking-error">' . __( 'This booking is unavailable.', 'woocommerce-bookings' ) . '</span>', null, null ),
			) );
		}

		$booking_data = wc_bookings_get_posted_data( $posted, $product );
		$cost = WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product );

		if ( is_wp_error( $cost ) ) {
			wp_send_json( array(
				'result' => 'ERROR',
				'html'   => apply_filters( 'woocommerce_bookings_calculated_booking_cost_error_output', '<span class="booking-error">' . $cost->get_error_message() . '</span>', $cost, $product ),
			) );
		}

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

		if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$display_price = wc_get_price_including_tax( $product, array( 'price' => $cost ) );
			} else {
				$display_price = $product->get_price_including_tax( 1, $cost );
			}
		} else {
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$display_price = wc_get_price_excluding_tax( $product, array( 'price' => $cost ) );
			} else {
				$display_price = $product->get_price_excluding_tax( 1, $cost );
			}
		}

		if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
			$price_suffix = $product->get_price_suffix( $cost, 1 );
		} else {
			$price_suffix = $product->get_price_suffix();
		}

		// Build the output
		$output = apply_filters( 'woocommerce_bookings_booking_cost_string', __( 'Booking cost', 'woocommerce-bookings' ), $product ) . ': <strong>' . wc_price( $display_price ) . $price_suffix . '</strong>';

		// Send the output
		wp_send_json( array(
			'result' => 'SUCCESS',
			'html'   => apply_filters( 'woocommerce_bookings_calculated_booking_cost_success_output', $output, $display_price, $product ),
		) );
	}

	/**
	 * Get a list of time blocks available on a date.
	 */
	public function get_time_blocks_for_date() {

		// clean posted data
		$posted = array();
		parse_str( $_POST['form'], $posted );
		if ( empty( $posted['add-to-cart'] ) ) {
			return false;
		}

		// Product Checking
		$booking_id   = $posted['add-to-cart'];
		$product      = get_wc_product_booking( wc_get_product( $booking_id ) );
		if ( ! $product ) {
			return false;
		}

		// Check selected date.
		if ( ! empty( $posted['wc_bookings_field_start_date_year'] ) && ! empty( $posted['wc_bookings_field_start_date_month'] ) && ! empty( $posted['wc_bookings_field_start_date_day'] ) ) {
			$year      = max( date( 'Y' ), absint( $posted['wc_bookings_field_start_date_year'] ) );
			$month     = absint( $posted['wc_bookings_field_start_date_month'] );
			$day       = absint( $posted['wc_bookings_field_start_date_day'] );
			$timestamp = strtotime( "{$year}-{$month}-{$day}" );
		}
		if ( empty( $timestamp ) ) {
			die( '<li>' . esc_html__( 'Please enter a valid date.', 'woocommerce-bookings' ) . '</li>' );
		}

		if ( ! empty( $posted['wc_bookings_field_duration'] ) ) {
			$interval = (int) $posted['wc_bookings_field_duration'] * $product->get_duration();
		} else {
			$interval = $product->get_duration();
		}

		$base_interval = $product->get_duration();

		if ( 'hour' === $product->get_duration_unit() ) {
			$interval      = $interval * 60;
			$base_interval = $base_interval * 60;
		}

		$first_block_time     = $product->get_first_block_time();
		$from                 = strtotime( $first_block_time ? $first_block_time : 'midnight', $timestamp );
		$standard_from        = $from;

		// Get an extra day before/after so front-end can get enough blocks to fill out 24 hours in client time.
		if ( isset( $posted['get_prev_day'] ) ) {
			$from = strtotime( '- 1 day', $from );
		}
		$to = strtotime( '+ 1 day', $standard_from ) + $interval;
		if ( isset( $posted['get_next_day'] ) ) {
			$to = strtotime( '+ 1 day', $to );
		}

		// cap the upper range
		$to                   = strtotime( 'midnight', $to ) - 1;

		$resource_id_to_check = ( ! empty( $posted['wc_bookings_field_resource'] ) ? $posted['wc_bookings_field_resource'] : 0 );
		$resource             = $product->get_resource( absint( $resource_id_to_check ) );
		$resources            = $product->get_resources();

		if ( $resource_id_to_check && $resource ) {
			$resource_id_to_check = $resource->ID;
		} elseif ( $product->has_resources() && $resources && count( $resources ) === 1 ) {
			$resource_id_to_check = current( $resources )->ID;
		} else {
			$resource_id_to_check = 0;
		}

		$booking_form = new WC_Booking_Form( $product );
		$blocks       = $product->get_blocks_in_range( $from, $to, array( $interval, $base_interval ), $resource_id_to_check );
		$block_html   = $booking_form->get_time_slots_html( $blocks, array( $interval, $base_interval ), $resource_id_to_check, $from, $to );

		if ( empty( $block_html ) ) {
			$block_html .= '<li>' . __( 'No blocks available.', 'woocommerce-bookings' ) . '</li>';
		}

		die( $block_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Gets the end time html dropdown.
	 *
	 * @since 1.13.0
	 * @return HTML
	 */
	public function get_end_time_html() {
		$nonce = $_POST['security'];

		if ( ! wp_verify_nonce( $nonce, 'get_end_time_html' ) ) {
			// This nonce is not valid.
			wp_die( esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-bookings' ) );
		}

		$start_date_time      = wc_clean( $_POST['start_date_time'] );
		$product_id           = intval( $_POST['product_id'] );
		$blocks               = wc_clean( $_POST['blocks'] );
		$bookable_product     = wc_get_product( $product_id );
		$booking_form         = new WC_Booking_Form( $bookable_product );
		$resource_id_to_check = absint( wc_clean( $_POST['resource_id'] ) );
		$html                 = $booking_form->get_end_time_html( $blocks, $start_date_time, array(), $resource_id_to_check );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
		exit;
	}

	/**
	 * Search for customers and return json.
	 */
	public function json_search_order() {
		global $wpdb;

		check_ajax_referer( 'search-booking-order', 'security' );

		$term = wc_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		$found_orders = array();

		$term = apply_filters( 'woocommerce_booking_json_search_order_number', $term );

		$query_orders = $wpdb->get_results( $wpdb->prepare( "
			SELECT ID, post_title FROM {$wpdb->posts} AS posts
			WHERE posts.post_type = 'shop_order'
			AND posts.ID LIKE %s
			LIMIT 10
		", $term . '%' ) );

		if ( $query_orders ) {
			foreach ( $query_orders as $item ) {
				$order = wc_get_order( $item->ID );
				if ( $order ) {
					$found_orders[ ( is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id ) ] = $order->get_order_number() . ' &ndash; ' . date_i18n( wc_bookings_date_format(), strtotime( is_callable( array( $order, 'get_date_created' ) ) ? $order->get_date_created() : $order->post_date ) );
				}
			}
		}

		wp_send_json( $found_orders );
	}

	/**
	 * Adds a store availability rule.
	 *
	 * @since 1.16.0
	 * @return array
	 */
	public function add_store_availability_rule() {
		check_ajax_referer( 'add-store-availability-rule', 'security' );

		if ( empty( $_POST['event_data'] ) || ! is_array( $_POST['event_data'] ) ) {
			wp_send_json_error( 'Missing or malformed event data' );
		}

		$event = new WC_Global_Availability();
		$event_data = wc_clean( $_POST['event_data'] );

		foreach ( $event_data as $field => $value ) {
			if ( ! is_callable( array( $event, 'set_' . $field ) ) ) {
				continue;
			}

			$event->{"set_{$field}"}( $value );
		}

		$event->set_range_type( 'store_availability' );

		$data_store = WC_Data_Store::load( 'booking-global-availability' );

		try {
			$data_store->create( $event );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		wp_send_json( array(
			'success' => true,
			'id'      => $event->get_id(),
		) );
	}

	/**
	 * Reads a store availability rule.
	 *
	 * @since 1.16.0
	 * @return array
	 */
	public function get_store_availability_rules() {
		check_ajax_referer( 'get-store-availability-rules', 'security' );

		$start_time = ! empty( $_GET['start_time'] ) ? wc_clean( $_GET['start_time'] ) : 'today';
		$end_time   = ! empty( $_GET['end_time'] )   ? wc_clean( $_GET['end_time'] )   : 'tomorrow';

		$rules = WC_Data_Store::load( 'booking-global-availability' )->get_all_as_array(
			array( array(
				'key'     => 'range_type',
				'value'   => 'store_availability',
				'compare' => '=',
			) ),
			date( 'Y-m-d', strtotime( $start_time ) ),
			date( 'Y-m-d', strtotime( $end_time ) )
		);

		wp_send_json( $rules );
	}

	/**
	 * Updates a store availability rule.
	 *
	 * @since 1.16.0
	 * @return array
	 */
	public function update_store_availability_rule() {
		check_ajax_referer( 'update-store-availability-rule', 'security' );

		if ( empty( $_POST['event_data'] ) || empty( $_POST['event_data']['id'] ) ) {
			wp_send_json_error( 'Missing event data and/or id' );
		}

		try {
			$event = new WC_Global_Availability( absint( $_POST['event_data']['id'] ) );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		if ( ! $event->is_store_availability() ) {
			wp_send_json_error( 'Event is not a store availability' );
		}

		$event_data = wc_clean( $_POST['event_data'] );

		foreach ( $event_data as $field => $value ) {
			$field = strtolower( $field );

			if ( in_array( $field, array( 'id' ) ) ) {
				continue;
			}

			if ( ! is_callable( array( $event, 'set_' . $field ) ) ) {
				continue;
			}

			$event->{"set_{$field}"}( $value );
		}

		$data_store = WC_Data_Store::load( 'booking-global-availability' );

		try {
			$data_store->update( $event );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		wp_send_json( array(
			'success' => true,
			'id'      => $event->get_id(),
		) );
	}

	/**
	 * Deletes a store availability rule.
	 *
	 * @since 1.16.0
	 * @return array
	 */
	public function delete_store_availability_rules() {
		check_ajax_referer( 'delete-store-availability-rules', 'security' );

		if ( empty( $_POST['ids'] ) ) {
			wp_send_json_error( 'Missing event IDs' );
		}

		$ids 	= explode( ',', wc_clean( $_POST['ids'] ) );

		$events = array_map( function( $event_id ) {
			return new WC_Global_Availability( absint( $event_id ) );
		}, $ids );

		$events = array_filter( $events, function( $event ) {
			return $event->is_store_availability();
		} );

		$data_store = WC_Data_Store::load( 'booking-global-availability' );

		foreach ( $events as $event ) {
			$data_store->delete( $event );
		}

		wp_send_json( array( 'success' => true ) );
	}
}
