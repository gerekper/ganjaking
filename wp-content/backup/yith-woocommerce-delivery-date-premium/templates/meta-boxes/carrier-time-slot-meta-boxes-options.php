<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$carrier_worksday = array_keys( YITH_Delivery_Date_Carrier()->get_work_days( $post_id ) );
$days          = yith_get_worksday();
$days          = wp_array_slice_assoc( $days, $carrier_worksday );
$html = 'checkout' !== get_option( 'ywcdd_processing_type' , 'checkout' ) ? yith_delivery_date_get_disabled_checkout_option_message() : '';
$meta_boxes_options = array(
	'label'    => __( 'Time Slots', 'yith-woocommerce-delivery-date' ),
	'pages'    => 'yith_carrier', //or array( 'post-type1', 'post-type2')
	'context'  => 'normal', //('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		'time_slot_settings' => array(
			'label'  => __( 'Time Slot', 'yith-woocommerce-delivery-date' ),
			'fields' => array(
				'ywcdd_timeslot_message' => array(
					'type' => 'html',
					'html' => $html
				),
				'ywcdd_addtimeslot' => array(
					'id'               => '_ywcdd_addtimeslot',
					'type'             => 'toggle-element',
					'title' => '%%slot_name%%',
					'add_button'       => __( 'Add a Time Slot', 'yith-woocommerce-delivery-date' ),
					'sortable'         => false,
					'yith-display-row' => false,
					'subtitle'         => __( 'From %%timefrom%% - To %%timeto%%', 'yith-woocommerce-delivery-date' ),
					'onoff_field'      => array(
						'id'      => 'enabled',
						'name'    => 'ywcdd_enable_timeslot',
						'default' => 'yes'
					),
					'elements'         => array(
						array(
							'id'   => 'slot_name',
							'type' => 'text',
							'name' => __( 'Time slot name', 'yith-woocommerce-delivery-date' ),
							'class' => 'yith-required-field ywcdd_timeslot_name',
							'default' => ''
						),
						array(
							'id'    => 'timefrom',
							'type'  => 'text',
							'class' => 'yith_timepicker yith_timepicker_from yith-required-field',
							'name'  => __( 'Time From', 'yith-woocommerce-delivery-date' ),
							'default' => ''
						),
						array(
							'id'    => 'timeto',
							'type'  => 'text',
							'class' => 'yith_timepicker yith_timepicker_to yith-required-field',
							'name'  => __( 'Time To', 'yith-woocommerce-delivery-date' ),
							'default' => ''
						),
						array(
							'id'    => 'max_order',
							'type'  => 'number',
							'class' => 'yith_max_tot_order',
							'name'  => __( 'Lockout', 'yith-woocommerce-delivery-date' ),
							'desc'  => __( 'Max number of orders accepted for this time slot', 'yith-woocommerce-delivery-date' ),
							'default' => '',
							'min' => 0,
							'step' => 1
						),
						array(
							'id'    => 'fee_name',
							'type'  => 'text',
							'name'  => __( 'Fee Name', 'yith-woocommerce-delivery-date' ),
							'class' => 'yith_fee_name',
							'default' => ''
						),
						array(
							'id'    => 'fee',
							'type'  => 'number',
							'name'  => __( 'Fee Price', 'yith-woocommerce-delivery-date' ),
							'class' => 'yith_fee',
							'min'   => 0,
							'step'  => 'any',
							'desc' => __('Add a fee for this time slot. If you don\'t need a fee, leave these fields empty.', 'yith-woocommerce-delivery-date' ),
							'default' => ''
						),
						array(
							'id' => 'override_days',
							'type' => 'onoff',
							'name' => __( 'Set Workdays', 'yith-woocommerce-delivery-date' ),
							'class' => 'yith_override_day',
							'default' => 'no'
						),
						array(
							'id' => 'day_selected',
							'type' => 'select-buttons',
							'class' => 'yith_dayworkselect wc-enhanced-select',
							'options' => $days,
							'name' => '',
							'default' => '',
							'desc' => __( 'If enabled, this time slot will be available only for the follow selected workdays', 'yith-woocommerce-delivery-date' )
						)
					),
					'save_button'      => array(
						'id'    => 'yith_update_time_slot',
						'name'  => __( 'Save', 'yith-woocommerce-delivery-date' ),
						'class' => 'yith_update_time_slot'
					),
					'delete_button'    => array(
						'id'    => 'ywcdd_delete_time_slot',
						'name'  => __( 'Delete', 'yith-woocommerce-delivery-date' ),
						'class' => 'ywcdd_delete_time_slot'
					)
				),

			)
		)
	)
);

return $meta_boxes_options;