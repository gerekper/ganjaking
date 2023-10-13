<?php
/**
 * Calendars options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$options = array(
	'calendar-options'                              => array(
		'title' => __( 'Plugin Calendar', 'yith-booking-for-woocommerce' ),
		'type'  => 'title',
		'desc'  => '',
	),
	'calendar-day-default-time-step'                => array(
		'id'        => 'yith-wcbk-calendar-day-default-time-step',
		'name'      => __( 'Default time step in Daily Calendar', 'yith-booking-for-woocommerce' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'default'   => '1h',
		'options'   => YITH_WCBK_Booking_Calendar::get_time_steps(),
	),
	'calendar-day-default-start-time'               => array(
		'id'                => 'yith-wcbk-calendar-day-default-start-time',
		'name'              => __( 'Default start time in Daily Calendar', 'yith-booking-for-woocommerce' ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'desc'              => __( 'Format: <code>hh:mm</code>', 'yith-booking-for-woocommerce' ),
		'default'           => '00:00',
		'custom_attributes' => array(
			'pattern' => '([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}',
		),
	),
	'booking-name-format-in-calendar'               => array(
		'id'        => 'yith-wcbk-booking-name-format-in-calendar',
		'name'      => __( 'Booking name in calendar will include', 'yith-booking-for-woocommerce' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'options'   => array(
			'#{id} {product_name}'               => esc_html__( 'Booking ID and product name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 Rome Apartment</code>',
			'#{id} {user_name}'                  => esc_html__( 'Booking ID and user name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 John Doe</code>',
			'#{id} {product_name} ({user_name})' => esc_html__( 'Booking ID, product name and user name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 Rome Apartment (John Doe)</code>',
			'#{id} {user_name} ({product_name})' => esc_html__( 'Booking ID, user name and product name', 'yith-booking-for-woocommerce' ) . ' - <code>#314 John Doe (Rome Apartment)</code>',
		),
		'default'   => '#{id} {user_name}',
	),
	'calendar-options-end'                          => array(
		'type' => 'sectionend',
	),
	'external-calendars-options'                    => array(
		'title'            => __( 'External Calendars', 'yith-booking-for-woocommerce' ),
		'type'             => 'title',
		'desc'             => '',
		'yith-wcbk-module' => 'external-sync',
	),
	'external-calendars-sync-expiration'            => array(
		'id'               => 'yith-wcbk-external-calendars-sync-expiration',
		'name'             => __( 'Calendar synchronization expires after', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'yith-type'        => 'select',
		'desc'             => implode(
			'<br />',
			array(
				__( 'Choose the sync expiration for external calendars.', 'yith-booking-for-woocommerce' ),
				__( 'Once the expiration is reached, external calendars will be automatically updated when a user accesses the product page.', 'yith-booking-for-woocommerce' ),
			)
		),
		'default'          => 6 * HOUR_IN_SECONDS,
		'options'          => class_exists( 'YITH_WCBK_Booking_Externals' ) ? YITH_WCBK_Booking_Externals::get_sync_expiration_times() : array(),
		'yith-wcbk-module' => 'external-sync',
	),
	'external-calendars-show-externals-in-calendar' => array(
		'id'               => 'yith-wcbk-external-calendars-show-externals-in-calendar',
		'name'             => __( 'Show bookings of external calendars in plugin calendar', 'yith-booking-for-woocommerce' ),
		'type'             => 'yith-field',
		'desc'             => __( 'Enable to also show external bookings (set in the Booking Sync tab of your products) in the plugin calendar.', 'yith-booking-for-woocommerce' ),
		'yith-type'        => 'onoff',
		'default'          => 'no',
		'yith-wcbk-module' => 'external-sync',
	),
	'external-calendars-options-end'                => array(
		'type'             => 'sectionend',
		'yith-wcbk-module' => 'external-sync',
	),
	'google-calendar-options'                       => array(
		'title'            => __( 'Google Calendar', 'yith-booking-for-woocommerce' ),
		'type'             => 'title',
		'desc'             => '',
		'yith-wcbk-module' => 'google-calendar',
	),
	'google-calendar-options-page'                  => array(
		'type'             => 'yith-field',
		'yith-type'        => 'custom',
		'yith-display-row' => false,
		'action'           => 'yith_wcbk_print_google_calendar_tab',
		'yith-wcbk-module' => 'google-calendar',
	),
	'google-calendar-options-end'                   => array(
		'type'             => 'sectionend',
		'yith-wcbk-module' => 'google-calendar',
	),
);

$options = yith_wcbk_filter_options( $options );

$tab_options = array(
	'settings-calendars' => $options,
);

return apply_filters( 'yith_wcbk_panel_calendars_options', $tab_options );
