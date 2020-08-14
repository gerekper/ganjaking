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

	'thankyou' => array(

		'chekout_page_options_start' => array(
			'type' => 'sectionstart',
		),

		'chekout_page_options_title' => array(
			'title' => _x( 'Checkout page', 'Panel: page title', 'yith-woocommerce-multi-step-checkout' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'chekout_container_width_options_type' => array(
			'type'      => 'yith-field',
			'yith-type' => 'slider',
			'title'     => esc_html_x( 'Checkout container width', 'Option: Title. Please, do not translate FadeIn/FadeOut', 'yith-woocommerce-multi-step-checkout' ),
			'desc'      => esc_html_x( 'Set the checkout page width in relation to the width of the steps timeline. Ex.  If your timeline is 100% and you set it to 50%, the form fields will take 50% of the space in the centre.', 'Option: description', 'yith-woocommerce-multi-step-checkout' ),
			'id'        => 'yith_wcms_checkout_container_width',
			'option'    => array( 'min' => 0, 'max' => 100 ),
			'default'   => 100,
			'step'      => 5,
		),

		'chekout_page_options_end' => array(
			'type' => 'sectionend',
		),

		'order_received_options_start' => array(
			'type' => 'sectionstart',
		),

		'order_received_options_title' => array(
			'title' => _x( '"Order received" and "My account" page', 'Panel: page title', 'yith-woocommerce-multi-step-checkout' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'order_received_enable_multistep' => array(
			'title'     => _x( 'Select style for "Order received" page', 'Admin option: "Order Received" page style', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => _x( 'Select plugin style or theme style', 'Admin option description: choose between applying theme or plugin style', 'yith-woocommerce-multi-step-checkout' ),
			'options'   => array(
				'theme'  => _x( 'Theme style', 'Admin: Option style', 'yith-woocommerce-multi-step-checkout' ),
				'plugin' => _x( 'Plugin style', 'Admin: Option style', 'yith-woocommerce-multi-step-checkout' ),
			),
			'id'        => 'yith_wcms_thankyou_style',
			'default'   => 'theme',
		),

		'order_received_details' => array(
			'title'     => _x( 'Order details background color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'hide'
			),
			'desc'      => _x( 'Select the background color for "Order details" in "Order received" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#eef6ff',
			'id'        => 'yith_wcms_details_background_color',
		),

		'order_received_highlight' => array(
			'title'     => _x( 'Order details highlight color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'hide'
			),
			'desc'      => _x( 'Select text highlight color for "Order details" in "Order review" pages', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#eef6ff',
			'id'        => 'yith_wcms_highlight_color',
		),

		'order_received_table_header' => array(
			'title'     => _x( 'Table header background color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'hide'
			),
			'desc'      => _x( 'Select background highlight color for the header of "Order" table in "Order received" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#619dda',
			'id'        => 'yith_wcms_table_header_backgroundcolor'
		),

		'order_received_table_header_color' => array(
			'title'     => _x( 'Text table header color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'hide'
			),
			'desc'      => _x( 'Select text color for the header of order table in "Order received" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
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
				'type'  => 'hide'
			),
			'desc'      => _x( 'Select background color for rows in "Orders" table in "Order received" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#eef6ff',
			'id'        => 'yith_wcms_table_row_backgroundcolor'
		),

		'order_received_details_color' => array(
			'title'     => _x( '"Order details" color', 'Admin: Option', 'yith-woocommerce-multi-step-checkout' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'deps'      => array(
				'id'    => 'yith_wcms_thankyou_style',
				'value' => 'plugin',
				'type'  => 'hide'
			),
			'desc'      => _x( 'Select color for "Order details" text in "Order received" page and "Order review" page', 'Admin: Option description', 'yith-woocommerce-multi-step-checkout' ),
			'default'   => '#808080',
			'id'        => 'yith_wcms_table_details_color'
		),

		'order_received_options_end' => array(
			'type' => 'sectionend',
		),
	)
), 'order_received'
);
