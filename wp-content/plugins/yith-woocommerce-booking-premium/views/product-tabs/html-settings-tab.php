<?php
/**
 * Settings tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$settings_fields = array(
	'duration'                     => array(
		'class'     => '_yith_booking_duration_field yith_booking_multi_fields',
		'title'     => __( 'Booking Duration', 'yith-booking-for-woocommerce' ),
		'label_for' => '_yith_booking_duration',
		'desc'      => __( 'Set if your customers can book for minutes, hours, days or months', 'yith-booking-for-woocommerce' ),
		'fields'    => array(
			array(
				'type'    => 'select',
				'value'   => $booking_product ? $booking_product->get_duration_type( 'edit' ) : 'customer',
				'id'      => '_yith_booking_duration_type',
				'class'   => 'select short',
				'options' => array(
					'customer' => __( 'Customer can book units of', 'yith-booking-for-woocommerce' ),
					'fixed'    => __( 'Fixed units of', 'yith-booking-for-woocommerce' ),
				),
			),
			array(
				'type'              => 'number',
				'value'             => $booking_product ? $booking_product->get_duration( 'edit' ) : 1,
				'id'                => '_yith_booking_duration',
				'class'             => 'mini',
				'custom_attributes' => 'step="1" min="1"',
			),
			array(
				'type'    => 'select',
				'value'   => $booking_product ? $booking_product->get_duration( 'edit' ) : '',
				'id'      => '_yith_booking_duration_minute_select',
				'name'    => false,
				'class'   => 'mini',
				'options' => apply_filters(
					'yith_wcbk_duration_minute_select_options',
					array(
						'15' => '15',
						'30' => '30',
						'45' => '45',
						'60' => '60',
						'90' => '90',
					)
				),
			),
			array(
				'type'    => 'select',
				'value'   => $booking_product ? $booking_product->get_duration_unit( 'edit' ) : 'day',
				'id'      => '_yith_booking_duration_unit',
				'class'   => 'select',
				'options' => array(
					'month'  => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
					'day'    => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
					'hour'   => __( 'Hour(s)', 'yith-booking-for-woocommerce' ),
					'minute' => __( 'Minute(s)', 'yith-booking-for-woocommerce' ),
				),
			),
		),
	),
	'enable-calendar-range-picker' => array(
		'class'  => '_yith_booking_enable_calendar_range_picker bk_show_if_customer_one_day',
		'title'  => __( 'Enable calendar range picker', 'yith-booking-for-woocommerce' ),
		'desc'   => __( 'Enable or disable the calendar range picker in product page.', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'yith-field' => true,
			'type'       => 'onoff',
			'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_enable_calendar_range_picker( 'edit' ) : false ),
			'id'         => '_yith_booking_enable_calendar_range_picker',
		),
	),
	'default-start-date'           => array(
		'class'  => '_yith_booking_default_start_date',
		'title'  => __( 'Default start date in booking form', 'yith-booking-for-woocommerce' ),
		'desc'   => __( 'Insert the day to show as default in Start Date field of booking form.', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'type'    => 'select',
			'class'   => 'select short',
			'value'   => $booking_product ? $booking_product->get_default_start_date( 'edit' ) : '',
			'id'      => '_yith_booking_default_start_date',
			'options' => array(
				''                => __( 'None', 'yith-booking-for-woocommerce' ),
				'today'           => __( 'Current day', 'yith-booking-for-woocommerce' ),
				'tomorrow'        => __( 'Current day + 1', 'yith-booking-for-woocommerce' ),
				'first-available' => __( 'First available', 'yith-booking-for-woocommerce' ),
				'custom'          => __( 'Custom Date', 'yith-booking-for-woocommerce' ),
			),
		),
	),
	'default-start-date-custom'    => array(
		'class'  => '_yith_booking_default_start_date_custom yith-wcbk-show-conditional',
		'data'   => array(
			'field-id' => '_yith_booking_default_start_date',
			'value'    => 'custom',
		),
		'title'  => __( 'Custom default start date', 'yith-booking-for-woocommerce' ),
		'desc'   => __( 'Insert the custom date to show as default in Start Date field of booking form.', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'type'  => 'text',
			'class' => 'yith-wcbk-date-input-field yith-wcbk-admin-date-picker',
			'name'  => '_yith_booking_default_start_date_custom',
			'value' => $booking_product ? $booking_product->get_default_start_date_custom( 'edit' ) : '',
		),
	),
	'default-start-time'           => array(
		'class'  => '_yith_booking_default_start_time bk_show_if_time',
		'title'  => __( 'Default start time in booking form', 'yith-booking-for-woocommerce' ),
		'desc'   => __( 'Insert the time to show as default in Time field of booking form.', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'type'    => 'select',
			'class'   => 'select short',
			'value'   => $booking_product ? $booking_product->get_default_start_time( 'edit' ) : '',
			'id'      => '_yith_booking_default_start_time',
			'options' => array(
				''                => __( 'None', 'yith-booking-for-woocommerce' ),
				'first-available' => __( 'First available', 'yith-booking-for-woocommerce' ),
			),
		),
	),
	'all-day'                      => array(
		'class'  => '_yith_booking_all_day bk_show_if_day',
		'title'  => __( 'Full day booking', 'yith-booking-for-woocommerce' ),
		'desc'   => __( 'Choose whether the booking will be active or not for the full day (Example: for a booking from day 1 to day 2, day 2 will be fully booked only if this option is active)', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'yith-field' => true,
			'type'       => 'onoff',
			'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_full_day( 'edit' ) : false ),
			'id'         => '_yith_booking_all_day',
		),
	),
	'allowed-start-days'           => array(
		'class'  => '_yith_booking_allowed_start_days_field',
		'title'  => __( 'Allowed Start Days', 'yith-booking-for-woocommerce' ),
		'desc'   => __( 'Select on which days the booking can start. Leave empty if it can start without any limit on any day of the week.', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			'yith-field'        => true,
			'type'              => 'select',
			'class'             => 'wc-enhanced-select select short',
			'multiple'          => true,
			'name'              => '_yith_booking_allowed_start_days',
			'options'           => yith_wcbk_get_days_array(),
			'value'             => $booking_product ? $booking_product->get_allowed_start_days( 'edit' ) : array(),
			'custom_attributes' => array(
				'style' => 'width:400px',
			),
		),
	),
);

?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Booking Settings', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			foreach ( $settings_fields as $key => $field ) {
				yith_wcbk_form_field( $field );
			}
			?>
		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Booking Terms', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_available_max_per_block_field',
					'title'  => __( 'Max bookings per unit', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Select the maximum number of bookings allowed for each unit. Set 0 (zero) for unlimited.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'value'             => $booking_product ? $booking_product->get_max_bookings_per_unit( 'edit' ) : 1,
							'id'                => '_yith_booking_max_per_block',
							'name'              => '_yith_booking_max_per_block',
							'class'             => 'yith-wcbk-mini-field',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
						),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => 'bk_show_if_customer_chooses_blocks yith_booking_multi_fields align-baseline',
					'title'  => __( 'Min/Max booking duration', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Set the minimum and the maximum booking duration that customers can select.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Min', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field'        => true,
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_minimum_duration( 'edit' ) : 1,
									'id'                => '_yith_booking_minimum_duration',
									'name'              => '_yith_booking_minimum_duration',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 1,
									),
								),
							),
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Max', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field'        => true,
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_maximum_duration( 'edit' ) : 0,
									'id'                => '_yith_booking_maximum_duration',
									'name'              => '_yith_booking_maximum_duration',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 0,
									),
								),
							),
						),
						array(
							'type'  => 'html',
							'value' => yith_wcbk_product_metabox_dynamic_duration_qty(),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => 'yith_booking_multi_fields align-baseline',
					'title'  => __( 'Min/Max advance reservation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Insert the minimum and maximum advance reservation for the booking.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Min', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field'        => true,
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_minimum_advance_reservation( 'edit' ) : 0,
									'id'                => '_yith_booking_allow_after',
									'name'              => '_yith_booking_allow_after',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 0,
									),
								),
							),
						),
						array(
							'yith-field' => true,
							'type'       => 'select',
							'value'      => $booking_product ? $booking_product->get_minimum_advance_reservation_unit( 'edit' ) : 'day',
							'id'         => '_yith_booking_allow_after_unit',
							'name'       => '_yith_booking_allow_after_unit',
							'class'      => 'wc-enhanced-select',
							'options'    => array(
								'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
								'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
								'hour'  => __( 'Hour(s)', 'yith-booking-for-woocommerce' ),
							),
						),
						array(
							'yith-field'                     => true,
							'type'                           => 'html',
							'html'                           => '<span style="width: 10px;"></span>',
							'yith-wcbk-field-show-container' => false,
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Max', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field'        => true,
									'type'              => 'number',
									'value'             => $booking_product ? $booking_product->get_maximum_advance_reservation( 'edit' ) : 1,
									'id'                => '_yith_booking_allow_until',
									'name'              => '_yith_booking_allow_until',
									'class'             => 'yith-wcbk-mini-field',
									'custom_attributes' => array(
										'step' => 1,
										'min'  => 1,
									),
								),
							),
						),
						array(
							'yith-field' => true,
							'type'       => 'select',
							'value'      => $booking_product ? $booking_product->get_maximum_advance_reservation_unit( 'edit' ) : 'year',
							'id'         => '_yith_booking_allow_until_unit',
							'name'       => '_yith_booking_allow_until_unit',
							'class'      => 'wc-enhanced-select',
							'options'    => array(
								'year'  => __( 'Year(s)', 'yith-booking-for-woocommerce' ),
								'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
								'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
							),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => 'yith_booking_multi_fields align-baseline',
					'title'  => __( 'Check-in/Check-out time', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Insert check-in and check-out time for your customers.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Check-in', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field' => true,
									'type'       => 'text',
									'value'      => $booking_product ? $booking_product->get_check_in( 'edit' ) : '',
									'id'         => '_yith_booking_checkin',
									'name'       => '_yith_booking_checkin',
									'class'      => 'yith-wcbk-mini-field',
								),
							),
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-field-with-top-label',
							'fields' => array(
								array(
									'type'  => 'label',
									'value' => __( 'Check-out', 'yith-booking-for-woocommerce' ),
								),
								array(
									'yith-field' => true,
									'type'       => 'text',
									'value'      => $booking_product ? $booking_product->get_check_out( 'edit' ) : '',
									'id'         => '_yith_booking_checkout',
									'name'       => '_yith_booking_checkout',
									'class'      => 'yith-wcbk-mini-field',
								),
							),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_buffer_field yith_booking_multi_fields',
					'title'  => __( 'Buffer time', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Time for preparation or cleanup between two bookings.', 'yith-booking-for-woocommerce' ),
					'fields' => array(
						array(
							'yith-field'        => true,
							'type'              => 'number',
							'value'             => $booking_product ? $booking_product->get_buffer( 'edit' ) : 0,
							'id'                => '_yith_booking_buffer',
							'name'              => '_yith_booking_buffer',
							'custom_attributes' => apply_filters( 'yith_wcbk_buffer_field_custom_attributes', 'step="1" min="0"' ),
							'class'             => 'yith-wcbk-mini-field',
						),
						array(
							'yith-field' => true,
							'type'       => 'html',
							'html'       => yith_wcbk_product_metabox_dynamic_duration_unit(),
						),
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_time_increment_including_buffer bk_show_if_fixed_and_time',
					'title'  => __( 'Time increment including buffer', 'yith-booking-for-woocommerce' ),
					'desc'   => __( "Select if you want to include buffer time to the time increment. Example: if enabled and the booking duration is 1 hour and you set a buffer of 1 hour, the time increment will be 1 hour + 1 hour, so you'll see the following time slots: 8:00 - 10:00 - 12:00 - 14:00", 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'onoff',
						'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_time_increment_including_buffer( 'edit' ) : false ),
						'id'         => '_yith_booking_time_increment_including_buffer',
						'name'       => '_yith_booking_time_increment_including_buffer',
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_time_increment_based_on_duration bk_show_if_time',
					'title'  => __( 'Time increment based on duration', 'yith-booking-for-woocommerce' ),
					'desc'   => __( "Select if the time increment of your booking is based on booking duration. By default the time increment is 1 hour for hourly bookings and 15 minutes for per-minute bookings. Example: if enabled and your booking duration is 3 hours, the time increment will be 3 hours, so you'll see the following time slots: 8:00 - 11:00 - 14:00 - 17:00", 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'onoff',
						'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_time_increment_based_on_duration( 'edit' ) : false ),
						'id'         => '_yith_booking_time_increment_based_on_duration',
						'name'       => '_yith_booking_time_increment_based_on_duration',
					),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_request_confirmation_field',
					'title'  => __( 'Require confirmation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Enable if the admin has to confirm a booking before accepting it.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							'yith-field' => true,
							'type'       => 'onoff',
							'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_confirmation_required( 'edit' ) : false ),
							'id'         => '_yith_booking_request_confirmation',
							'name'       => '_yith_booking_request_confirmation',
						),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_can_be_cancelled_field',
					'title'  => __( 'Allow cancellation', 'yith-booking-for-woocommerce' ),
					'desc'   => __( 'Enable if the customer can cancel the booking.', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							'yith-field' => true,
							'type'       => 'onoff',
							'value'      => wc_bool_to_string( $booking_product ? $booking_product->get_cancellation_available( 'edit' ) : false ),
							'id'         => '_yith_booking_can_be_cancelled',
							'name'       => '_yith_booking_can_be_cancelled',
						),
				)
			);

			yith_wcbk_form_field(
				array(
					'class'  => '_yith_booking_cancelled_time_field bk_show_if_can_be_cancelled yith_booking_multi_fields',
					'title'  => __( 'The booking can be cancelled up to', 'yith-booking-for-woocommerce' ),
					'fields' =>
						array(
							array(
								'yith-field'        => true,
								'type'              => 'number',
								'value'             => $booking_product ? $booking_product->get_cancellation_available_up_to( 'edit' ) : '0',
								'id'                => '_yith_booking_cancelled_duration',
								'name'              => '_yith_booking_cancelled_duration',
								'class'             => 'yith-wcbk-mini-field',
								'custom_attributes' => array(
									'step' => 1,
									'min'  => 0,
								),
							),
							array(
								'yith-field' => true,
								'type'       => 'select',
								'value'      => $booking_product ? $booking_product->get_cancellation_available_up_to_unit( 'edit' ) : 'day',
								'id'         => '_yith_booking_cancelled_unit',
								'name'       => '_yith_booking_cancelled_unit',
								'class'      => 'wc-enhanced-select',
								'options'    => yith_wcbk_get_cancel_duration_units(),
							),
							array(
								'yith-field' => true,
								'type'       => 'html',
								'html'       => __( 'before the booking start date', 'yith-booking-for-woocommerce' ),
							),
						),
				)
			);
			?>
		</div>
	</div>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_product_tab_settings_after
	 * Hook to output something at the end of the "settings" tab in the bookable product edit page.
	 *
	 * @param WC_Product_Booking|false $booking_product The bookable product.
	 */
	do_action( 'yith_wcbk_product_tab_settings_after', $booking_product );
	?>
</div>
