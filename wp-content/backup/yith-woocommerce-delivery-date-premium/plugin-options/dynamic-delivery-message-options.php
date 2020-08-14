<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$settings = array(
	'dynamic-delivery-message' => array(
		'ddm_section_start' => array(
			'name' => __( 'Dynamic Delivery Message', 'yith-woocommerce-delivery-date' ),
			'type' => 'title',
		),

		'ddm_enable_shipping_message' => array(
			'name'      => __( 'Enable prompt delivery to carrier', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable this option if you want to show the date when the product is usually shipped to carrier', 'yith-woocommerce-delivery-date' ),
			'default'   => 'no',
			'id'        => 'ywcdd_ddm_enable_shipping_message'
		),
		'ddm_shipping_message_txt'    => array(
			'name'      => __( 'Prompt delivery to carrier', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => __( 'Insert the text to show. E.g. This product will be ready and picked up by carrier within March 20th, 2019 . Use the placeholder {shipping_date} to show the date', 'yith-woocommerce-delivery-date' ),
			'default'   => __( "This product will be picked up by carrier within {shipping_date}", 'yith-woocommerce-delivery-date' ),
			'id'        => 'ywcdd_ddm_shipping_message',
			'deps'      => array(
				'id'    => 'ywcdd_ddm_enable_shipping_message',
				'value' => 'yes',
				'type'  => 'disable'
			),
		),
		'ddm_enable_delivery_message' => array(
			'name'      => __( 'Enable delivery to customer', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable this option if you want to show the delivery date', 'yith-woocommerce-delivery-date' ),
			'default'   => 'no',
			'id'        => 'ywcdd_ddm_enable_delivery_message'
		),
		'ddm_delivery_message_txt'    => array(
			'name'      => __( 'Delivery to customer', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => __( 'Insert the text to show. E.g. This product will be shipped on March 20th, 2019 . Use the placeholder {delivery_date} to show the date . Use {time_limit} to show the time limit.', 'yith-woocommerce-delivery-date' ),
			'default'   => __( "Place your order by {time_limit} to receive the product within {delivery_date}", 'yith-woocommerce-delivery-date' ),
			'id'        => 'ywcdd_ddm_delivery_message',
			'deps'      => array(
				'id'    => 'ywcdd_ddm_enable_delivery_message',
				'value' => 'yes',
				'type'  => 'disable'
			),
		),
		'ddm_time_limit_alternative_txt'    => array(
			'name'      => __( 'Alternative delivery text to customer', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => __( 'If it is not possible to calculate the time limit, insert an alternative text.  E.g. This product will be delivered on March 20th, 2019 . Use the placeholder {delivery_date} to show the date', 'yith-woocommerce-delivery-date'),
			'default'   => '',
			'id'        => 'ywcdd_ddm_time_limit_alternative_txt',
			'deps'      => array(
				'id'    => 'ywcdd_ddm_enable_delivery_message',
				'value' => 'yes',
				'type'  => 'disable'
			),
		),
		'ddm_where_show_delivery_message'    => array(
			'name'      => __( 'Position for the delivery message', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => __( 'Choose where you want to show the messages', 'yith-woocommerce-delivery-date'),
			'default'   => 15,
			'options' => array(
				15 => __('After Price', 'yith-woocommerce-delivery-date'),
				25 => __('Before Add to cart button' , 'yith-woocommerce-delivery-date'),
				35 => __('After Add to cart button', 'yith-woocommerce-delivery-date'),
				-1 => __( 'With Widget or Shortcode', 'yith-woocommerce-delivery-date' )
			),
			'id'        => 'ywcdd_ddm_where_show_delivery_message',

		),

		'ddm_section_end'             => array(
			'type' => 'sectionend'
		),
		'ddm_customization_section_start' => array(
			'name' => __( 'Dynamic Delivery Message Customization', 'yith-woocommerce-delivery-date' ),
			'type' => 'title',
		),
		'ddm_customization_ready_bg' => array(
			'name' => __('Prompt delivery to carrier background','yith-woocommerce-delivery-date'),
			'type' => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc' => __('Set a background color for prompt delivery to carrier message', 'yith-woocommerce-delivery-date'),
			'id'        => 'ywcdd_dm_customization_ready_bg',
			'default' => '#eff3f5'
		),
		'ddm_customization_ready_icon' => array(
			'name' =>  __('Prompt delivery to carrier icon','yith-woocommerce-delivery-date'),
			'type' => 'yith-field',
			'yith-type' => 'upload',
			'default' => YITH_DELIVERY_DATE_ASSETS_URL.'images/truck.png',
			'desc' => __( 'Upload a custom icon if you want replace the default one', 'yith-woocommerce-delivery-date'),
			'id' => 'ywcdd_dm_customization_ready_icon'
		),
		'ddm_customization_delivery_bg' => array(
			'name' => __('Delivery to customer background','yith-woocommerce-delivery-date'),
			'type' => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc' => __('Set a background color for delivery to customer message', 'yith-woocommerce-delivery-date'),
			'id'        => 'ywcdd_dm_customization_customer_bg',
			'default' => '#ffdea5'
		),
		'ddm_customization_delivery_icon' => array(
			'name' =>  __('Delivery to customer icon','yith-woocommerce-delivery-date'),
			'type' => 'yith-field',
			'yith-type' => 'upload',
			'default' => YITH_DELIVERY_DATE_ASSETS_URL.'images/clock.png',
			'desc' => __( 'Upload a custom icon if you want replace the default one', 'yith-woocommerce-delivery-date'),
			'id' => 'ywcdd_dm_customization_customer_icon'
		),
		'ddm_customization_section_end'             => array(
			'type' => 'sectionend'
		),
	)
);

return $settings;