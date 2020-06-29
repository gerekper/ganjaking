<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return apply_filters( 'yith_wcms_order_received_options', array(

	'order_received' => array(

		'order_received_options_start' => array(
			'type' => 'sectionstart',
		),

		'order_received_options_title' => array(
			'title' => _x( 'My Account: "Order Received" and "My Account" Page', 'Panel: page title', 'yith-woocommerce-multi-step-checkout' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'order_received_enable_multistep' => array(
			'title'     => _x( 'Select style for "Thank You" page', 'Admin option: "Thank You" and "Order Received" page style', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => _x( 'Select plugin style or theme style', 'Admin option description: choose between applying theme or plugin style', 'yith-woocommerce-multi-step-checkout' ),
			'options'   => array(
				'theme'  => _x( 'Theme Style', 'Admin: Option style', 'yith-woocommerce-multi-step-checkout' ),
				'plugin' => _x( 'Plugin Style', 'Admin: Option style', 'yith-woocommerce-multi-step-checkout' ),
			),
			'id'        => 'yith_wcms_thankyou_style',
			'default'   => 'theme',
		),

		'order_received_details' => array(
			'title'     => _x( 'Text Highlight Color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'disable'
			),
			'desc'      => _x( 'Select the background color for "Order details" in "Thank You" and "Order review" pages', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#eef6ff',
			'id'        => 'yith_wcms_details_background_color',
		),

		'order_received_highlight' => array(
			'title'     => _x( 'Text Highlight Color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'disable'
			),
			'desc'      => _x( 'Select text highlight color in "Thank You" page and "Order review" pages', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#eef6ff',
			'id'        => 'yith_wcms_highlight_color',
		),

		'order_received_table_header' => array(
			'title'     => _x( 'Table Header Background Color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'disable'
			),
			'desc'      => _x( 'Select background highlight color for the header of "Order" table in "Thank You" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#619dda',
			'id'        => 'yith_wcms_table_header_backgroundcolor'
		),

		'order_received_table_header_color' => array(
			'title'     => _x( 'Text Table Header Color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'disable'
			),
			'desc'      => _x( 'Select text color for the header of order table in "Thank You" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#ffffff',
			'id'        => 'yith_wcms_table_header_color'
		),

		'order_received_table_row_color' => array(
			'title'     => _x( 'Table row background color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'disable'
			),
			'desc'      => _x( 'Select background color for rows in "Orders" table in "Thank You" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#eef6ff',
			'id'        => 'yith_wcms_table_row_backgroundcolor'
		),

		'order_received_details_color' => array(
			'title'     => _x( '"Order Details" color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'disable'
			),
			'desc'      => _x( 'Select color for "Order details" text in "Thank You" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#808080',
			'id'        => 'yith_wcms_table_details_color'
		),

		'order_received_options_end' => array(
			'type' => 'sectionend',
		),
	)
), 'order_received'
);