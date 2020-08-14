<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$birthday_field_pages_to_show = array(
	'my-account'    => __( 'My Account Page', 'yith-woocommerce-points-and-rewards' ),
	'register_form' => __( 'Registration Form', 'yith-woocommerce-points-and-rewards' ),
	'checkout'      => __( 'Checkout Page', 'yith-woocommerce-points-and-rewards' ),
);
$section1                     = array(
	'extra_points_review_title'         => array(
		'name' => __( 'Review', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_extra_points_review_title',
	),

	'enable_review_exp'                 => array(
		'name'      => __( 'Assign points based on the number of reviews posted', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_review_exp',
	),

	'review_exp'                        => array(
		'name'                   => '',
		'desc'                   => '',
		'type'                   => 'yith-field',
		'yith-type'              => 'options-extrapoints',
		'label'                  => __( 'Review(s) =', 'yith-woocommerce-points-and-rewards' ),
		'default'                => array(
			'list' => array(
				array(
					'number' => 1,
					'points' => 10,
					'repeat' => 0,
				),
			),
		),
		'id'                     => 'ywpar_review_exp',
		'deps'                   => array(
			'id'    => 'ywpar_enable_review_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
		'yith-sanitize-callback' => 'ywpar_option_extrapoints_sanitize',
	),


	'extra_points_review_title_end'     => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_extra_points_review_title_end',
	),

	'extra_num_order_title'             => array(
		'name' => __( 'Number of orders', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_extra_num_order_title',
	),

	'enable_num_order_exp'              => array(
		'name'      => __( 'Assign points based on the number of orders placed', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_num_order_exp',
	),

	'num_order_exp'                     => array(
		'name'                   => '',
		'desc'                   => '',
		'type'                   => 'yith-field',
		'yith-type'              => 'options-extrapoints',
		'label'                  => __( 'Order(s) =', 'yith-woocommerce-points-and-rewards' ),
		'default'                => array(
			'list' => array(
				array(
					'number' => 1,
					'points' => 10,
					'repeat' => 0,
				),
			),
		),
		'id'                     => 'ywpar_num_order_exp',
		'deps'                   => array(
			'id'    => 'ywpar_enable_num_order_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
		'yith-sanitize-callback' => 'ywpar_option_extrapoints_sanitize',
	),

	'extra_num_order_title_end'         => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_extra_num_order_title_end',
	),

	'amount_spent_title'                => array(
		'name' => __( 'Amount spent', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_amount_spent_title',
	),

	'enable_amount_spent_exp'           => array(
		'name'      => __( 'Assign points based on the total amount spent', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_amount_spent_exp',
	),


	'amount_spent_exp'                  => array(
		'name'      => '',
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'options-extrapoints',
		'label'     => sprintf( '%s (%s)', get_woocommerce_currency_symbol(), get_woocommerce_currency() ) . __( ' spent =', 'yith-woocommerce-points-and-rewards' ),
		'default'   => array(
			'list' => array(
				array(
					'number' => 1000,
					'points' => 10,
					'repeat' => 0,
				),
			),
		),
		'id'        => 'ywpar_amount_spent_exp',
		'deps'      => array(
			'id'    => 'ywpar_enable_amount_spent_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'amount_spent_title_end'            => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_amount_spent_title_end',
	),


	'checkout_threshold_title'          => array(
		'name' => __( 'Checkout total thresholds', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_checkout_threshold_title',
	),

	'enable_checkout_threshold_exp'     => array(
		'name'      => __( 'Assign points based on the Cart total', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_checkout_threshold_exp',
	),


	'checkout_threshold_exp'            => array(
		'name'        => '',
		'desc'        => '',
		'type'        => 'yith-field',
		'yith-type'   => 'options-extrapoints',
		'label'       => sprintf( '%s (%s)', get_woocommerce_currency_symbol(), get_woocommerce_currency() ) . __( ' spent =', 'yith-woocommerce-points-and-rewards' ),
		'default'     => array(
			'list' => array(
				array(
					'number' => 1000,
					'points' => 10,
					'repeat' => 0,
				),
			),
		),
		'show_repeat' => false,
		'id'          => 'ywpar_checkout_threshold_exp',
		'deps'        => array(
			'id'    => 'ywpar_enable_checkout_threshold_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'checkout_threshold_show_message'   => array(
		'name'      => __( 'Show threshold message on Cart and Checkout Pages', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Show the message on Cart & Checkout Pages', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_checkout_threshold_show_message',
	),

	'checkout_threshold_not_cumulate'   => array(
		'name'      => __( 'Disable awarding from multiple thresholds', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'When more than one of the above rules are matched, you can choose whether to apply all rules or just one. Enable the option if you want that just one is applied (highest Cart amount), or disable it if you want to let all rules apply and all points being accrued.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_checkout_threshold_not_cumulate',
	),

	'checkout_threshold_title_end'      => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_checkout_threshold_title_end',
	),



	'number_of_points_title'            => array(
		'name' => __( 'Number of points', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_number_of_points_title',
	),

	'enable_number_of_points_exp'       => array(
		'name'      => __( 'Assign extra points based on the number of points collected so far', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_number_of_points_exp',
	),

	'number_of_points_exp'              => array(
		'name'      => '',
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'options-extrapoints',
		'label'     => __( 'Point(s) =', 'yith-woocommerce-points-and-rewards' ),
		'default'   => array(
			'list' => array(
				array(
					'number' => 1,
					'points' => 10,
					'repeat' => 0,
				),
			),
		),
		'id'        => 'ywpar_number_of_points_exp',
		'deps'      => array(
			'id'    => 'ywpar_enable_number_of_points_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'number_of_points_title_end'        => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_number_of_points_title_end',
	),


	'point_on_registration_title'       => array(
		'name' => __( 'Points on registration', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_point_on_registration_title_title',
	),

	'enable_points_on_registration_exp' => array(
		'name'      => __( 'Assign points when a user registers', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_points_on_registration_exp',
	),

	'points_on_registration'            => array(
		'desc'              => __( 'Points', 'yith-woocommerce-points-and-rewards' ),
		'type'              => 'yith-field',
		'yith-type'         => 'number',
		'default'           => 1,
		'step'              => 1,
		'min'               => 1,
		'custom_attributes' => 'style="width:70px"',
		'id'                => 'ywpar_points_on_registration',
		'deps'              => array(
			'id'    => 'ywpar_enable_points_on_registration_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'point_on_registration_title_end'   => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_point_on_registration_title_end',
	),


	'point_on_birthday_title'           => array(
		'name' => __( 'Points on birthday', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_point_on_registration_title',
	),

	'enable_points_on_birthday_exp'     => array(
		'name'      => __( 'Assign points on customer\' birthday', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_points_on_birthday_exp',
	),

	'birthday_date_field_where'         => array(
		'name'      => __( 'Show Birthday Input Date Field on', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'multiple'  => true,
		'options'   => $birthday_field_pages_to_show,
		'default'   => array( 'my-account', 'register_form', 'checkout' ),
		'id'        => 'ywpar_birthday_date_field_where',
		'deps'      => array(
			'id'    => 'ywpar_enable_points_on_birthday_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
		'desc'      => __( 'Select where you want to show the Birthday Date Field.', 'yith-woocommerce-points-and-rewards' ),
	),

	'birthday_date_format'              => array(
		'name'      => __( 'Birthday Input Date Format', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'options'   => ywpar_date_placeholders(),
		'default'   => 'yy-mm-dd',
		'id'        => 'ywpar_birthday_date_format',
		'deps'      => array(
			'id'    => 'ywpar_enable_points_on_birthday_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'points_on_birthday'                => array(
		'desc'              => __( 'Points on birthday', 'yith-woocommerce-points-and-rewards' ),
		'type'              => 'yith-field',
		'yith-type'         => 'number',
		'default'           => 1,
		'step'              => 1,
		'min'               => 1,
		'custom_attributes' => 'style="width:70px"',
		'id'                => 'ywpar_points_on_birthday',
		'deps'              => array(
			'id'    => 'ywpar_enable_points_on_birthday_exp',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'point_on_birthday_title_end'       => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_point_on_birthday_title_end',
	),


);

return array( 'extra-points' => $section1 );
