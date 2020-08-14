<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$holiday_title = sprintf('<p>%s</p><p class="info-box"><strong>%s</strong> %s</p>',
	__( 'Add customized holidays to your calendar. This day will not counted in the "day of processing".', 'yith-woocommerce-delivery-date' ),
	_x( 'Example:', 'Part of: Example: if you add an holiday for 21,22 and 23 December and you get a new order day 21, processing time will start from day 24 and delivery date will be delayed according your holidays', 'yith-woocommerce-delivery-date' ),
	__( ' if you add an holiday for 21,22 and 23 December and you get a new order day 21, processing time will start from day 24 and delivery date will be delayed according your holidays', 'yith-woocommerce-delivery-date' )

	);
$calendar_settings = array(
	'general-calendar' => array(
		'calendar_holidays_section_start' => array(
			'type' => 'title',
			'name' => __( 'Holidays', 'yith-woocommerce-delivery-date' )
		),
		'calendar_holidays_section_start_desc' => array(
			'type' => 'yith-field',
			'yith-type' => 'html',
			'html' => $holiday_title
		),
		'calendar_holidays_option'        => array(
			'id'            => 'ywcdd_holidays_option',
			'type'          => 'yith-field',
			'yith-type'     => 'toggle-element',
			'add_button'    => __( 'Add a new holiday', 'yith-woocommerce-delivery-date' ),
			'class'         => 'ywcdd_list_row',
			'sortable'      => false,
			'yith-display-row' => false,
			'title'         => '',
			'subtitle'      => '',
			'onoff_field'   => array(
				'id'          => 'enabled',
				'name'        => 'ywcdd_enable_holiday',
				'ajax_action' => 'enable_disable_holidays',
				'default'     => 'yes'
			),
			'elements'      => array(
				array(
					'id'      => 'event_name',
					'class'   => 'ywcdd_holiday_name yith-required-field',
					'type'    => 'text',
					'name'    => __( 'Holiday name', 'yith-woocommerce-delivery-date' ),
					'default' => ''
				),
				array(
					'id'       => 'how_add_holiday',
					'type'     => 'select-group',
					'name'     => __( 'Holiday for', 'yith-woocommerce-delivery-date' ),
					'groups'   => array(
						'group_1' => array(
							'label'   => __( 'Processing Method', 'yith-woocommerce-delivery-date' ),
							'options' => YITH_Delivery_Date_Processing_Method()->get_formatted_processing_method()
						),
						'group_2' => array(
							'label'   => __( 'Carrier', 'yith-woocommerce-delivery-date' ),
							'options' => YITH_Delivery_Date_Carrier()->get_all_formatted_carriers()
						)
					),
					'class'    => 'holiday_for yith-required-field',
					'default'  => '',
					'multiple' => true
				),
				array(
					'id'      => 'start_event',
					'class'   => 'ywcdd_datepicker holiday_from yith-plugin-fw-datepicker yith-required-field',
					'type'    => 'datepicker',
					'name'    => __( 'From', 'yith-woocommerce-delivery-date' ),
					'default' => ''
				),
				array(
					'id'      => 'end_event',
					'class'   => 'ywcdd_datepicker holiday_to yith-plugin-fw-datepicker yith-required-field',
					'type'    => 'datepicker',
					'name'    => __( 'To', 'yith-woocommerce-delivery-date' ),
					'default' => ''
				),
			),
			'save_button'   => array(
				'id'    => 'yith_save_holiday',
				'name'  => __( 'Save', 'yith-woocommerce-delivery-date' ),
				'class' => 'ywcdd_update_holiday'
			),
			'delete_button' => array(
				'id'          => 'yith_delete_holiday',
				'name'        => __( 'Delete', 'yith-woocommerce-delivery-date' ),
				'ajax_action' => 'delete_holiday',
				'class'       => 'ywcdd_delete_holiday'
			),
			'value' => ''

		),
		'calendar_holidays_section_end'   => array(
			'type' => 'sectionend'
		),
		'calendar_section_start'          => array(
			'name' => __( 'Calendar', 'yith-woocommerce-delivery-date' ),
			'type' => 'title'
		),
		'calendar_display'                => array(
			'type' => 'calendar',
			'value' => ''
		),
		'calendar_section_end'            => array(
			'type' => 'sectionend'
		),
		'color_label_section_start'       => array(
			'name' => __( 'Calendar Customization', 'yith-woocommerce-delivery-date' ),
			'type' => 'title',
		),
		'calendar_color_shipp'            => array(
			'name'      => __( 'Ship to Carrier Event Color', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcdd_shipping_to_carrier_color',
			'default'   => '#ff643e',
			'desc'      => __( 'Set a background color for your ship to carrier events', 'yith-woocommerce-delivery-dae' )
		),
		'calendar_color_delivery'         => array(
			'name'      => __( 'Delivery Event Color', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcdd_delivery_day_color',
			'default'   => '#a3c401',
			'desc'      => __( 'Set a background color for your delivery to customer events', 'yith-woocommerce-delivery-date' )
		),
		'calendar_color_holiday'          => array(
			'name'      => __( 'Holiday Event Color', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcdd_holiday_color',
			'default'   => '#1197C1',
			'desc'      => __( 'Set a background color for your holiday events', 'yith-woocommerce-delivery-date' )
		),
		'color_label_section_end'         => array(
			'type' => 'sectionend'
		),

	)
);

return $calendar_settings;