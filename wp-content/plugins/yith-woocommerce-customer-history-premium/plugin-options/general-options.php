<?php

/**
 * GENERAL ARRAY OPTIONS
 */

$general = array(

	'general' => array(

		array(
			'title' => __( 'General Options', 'yith-woocommerce-customer-history' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcch-general-options',
		),

		array(
		    'id'      => 'yith-wcch-results_per_page',
		    'title'     => __( 'Results per page', 'yith-woocommerce-customer-history' ),
		    'type'    => 'number',
		    'min'     => 0,
		    'max'     => 100,
		    'step'    => 1,
		    'default' => 20
		),

		array(
			'id'        => 'yith-wcch-default_save_admin_session',
			'title'     => __( 'Save admin sessions', 'yith-woocommerce-customer-history' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable recording of administrators sessions', 'yith-woocommerce-customer-history' ),
			'default'   => 'yes',
		),

		array(
			'id'        => 'yith-wcch-hide_users_with_no_orders',
			'title'     => __( 'Hide users with no orders', 'yith-woocommerce-customer-history' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Hide all users who have not placed orders', 'yith-woocommerce-customer-history' ),
			'default'   => 'no',
		),

		array(
			'id'        => 'yith-wcch-show_bot_sessions',
			'title'     => __( 'Show BOT sessions', 'yith-woocommerce-customer-history' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Record sessions of search engine bots', 'yith-woocommerce-customer-history' ),
			'default'   => 'yes',
		),

		array(
		    'id'        => 'yith-wcch-timezone',
		    'title'     => __( 'Timezone', 'yith-woocommerce-customer-history' ),
		    'type'      => 'yith-field',
		    'yith-type' => 'select',
		    'options'   => array(
		        '-11' => 'UTC -11',
		        '-10' => 'UTC -10',
		        '-9' => 'UTC -9',
		        '-8' => 'UTC -8',
		        '-7' => 'UTC -7',
		        '-6' => 'UTC -6',
		        '-5' => 'UTC -5',
		        '-4' => 'UTC -4',
		        '-3' => 'UTC -3',
		        '-2' => 'UTC -2',
		        '-1' => 'UTC -1',
		        '0' => 'UTC +0',
		        '+1' => 'UTC +1',
		        '+2' => 'UTC +2',
		        '+3' => 'UTC +3',
		        '+4' => 'UTC +4',
		        '+5' => 'UTC +5',
		        '+6' => 'UTC +6',
		        '+7' => 'UTC +7',
		        '+8' => 'UTC +8',
		        '+9' => 'UTC +9',
		        '+10' => 'UTC +10',
		        '+11' => 'UTC +11',
		    ),
		    'default'   => '0',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcch-general-options',
		),


	),
);

return apply_filters( 'yith_wcch_panel_general_options', $general );