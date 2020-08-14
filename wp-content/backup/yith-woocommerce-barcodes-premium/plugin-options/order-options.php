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


$general_options = array(

	'order' => array(
		array(
			'name' => __( 'Order barcodes', 'yith-woocommerce-barcodes' ),
			'type' => 'title',
		),
		'ywbc_enable_on_orders'                  => array(
			'name'    => __( 'Generate and apply barcodes on orders', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Enable to apply barcodes on orders. If enabled, you can manually create barcodes in your old orders', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_enable_on_orders',
			'default' => 'yes',
		),

		'ywbc_create_on_orders'                  => array(
			'name'    => __( 'Automatically generate and apply barcodes on new orders', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Enable to automatically apply a barcode in all new orders', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_create_on_orders',
			'default' => 'no',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_orders',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'order_barcode_or_qr'  => array(
			'id'        =>  'order_barcode_or_qr',
			'name'      =>  __('Code type', 'yith-woocommerce-barcodes'),
			'type'    => 'yith-field',
			'yith-type' => 'select-images',
			'options' => array(
				'barcode' => array(
					'label' => esc_html__( 'Barcode', 'yith-woocommerce-barcodes' ),
					'image' => YITH_YWBC_ASSETS_URL . '/images/barcode.svg'
				),
				'qr_code' => array(
					'label' => esc_html__( 'QR Code', 'yith-woocommerce-barcodes' ),
					'image' => YITH_YWBC_ASSETS_URL . '/images/qr_code.svg'
				)
			),
			'std'       =>  'barcode',
			'desc'      =>  __('Choose the code type to be generated in the products', 'yith-woocommerce-barcodes'),
			'deps'    => array(
				'id'    => 'ywbc_enable_on_orders',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'ywbc_orders_default_barcode_protocol' => array(
			'name'    => __( 'Order barcode protocol', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'select',
			'desc'    => __( 'Choose the barcode protocol you want to use to generate a new barcode on orders', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_orders_protocol',
			'options' => YITH_Barcode::get_protocols(),
			'class'   => 'wc-enhanced-select',
			'default' => 'EAN13',
			'deps'    => array(
				'id'    => 'order_barcode_or_qr',
				'value' => 'barcode',
				'type'  => 'hide'
			)
		),


		'ywbc_order_barcode_type'                => array(
			'name'    => __( 'Generate the barcode using: ', 'yith-woocommerce-barcodes' ),
			'type' => 'yith-field',
			'yith-type' => 'radio',
			'id'      => 'ywbc_order_barcode_type',
			'options' => array(
				'id'        => __( "The order ID", 'yith-woocommerce-barcodes' ),
				'number'       => __( "The order number", 'yith-woocommerce-barcodes' ),
				'custom_field'  => __( "A custom field", 'yith-woocommerce-barcodes' ),
			),
			'desc'    => __( 'Choose which order info used to generate the barcodes', 'yith-woocommerce-barcodes' ),
			'default' => 'id',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_orders',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),
		'ywbc_order_barcode_type_custom_field' => array(
			'name'     => __( 'Custom field to use for barcode generation', 'yith-woocommerce-barcodes' ),
			'desc'     => __( 'Enter the custom field name to use to generate the barcodes', 'yith-woocommerce-barcodes' ),
			'id'       => 'ywbc_order_barcode_type_custom_field',
			'type' => 'yith-field',
			'yith-type' => 'text',
			'default'  => '',
			'deps'    => array(
				'id'    => 'ywbc_order_barcode_type',
				'value' => 'custom_field',
				'type'  => 'hide'
			)
		),
		'ywbc_show_on_order_page'                => array(
			'name'    => __( "Show barcodes in order page", 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Enable to show the order barcode in the order page', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_show_on_order_page',
			'default' => 'yes',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_orders',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),
		'ywbc_show_on_emails'                    => array(
			'name'    => __( 'Show order barcode in order email', 'yith-woocommerce-barcodes' ),
            'type' => 'yith-field',
            'yith-type' => 'radio',
			'id'      => 'ywbc_show_on_emails',
			'options' => array(
				'no'        => __( "Never show order barcode in emails", 'yith-woocommerce-barcodes' ),
				'completed' => __( "Show only in emails sent after the order is set to 'completed'", 'yith-woocommerce-barcodes' ),
				'all'       => __( "Show in all email", 'yith-woocommerce-barcodes' ),
			),
			'desc'     => __( 'Choose if show or not the order barcode in order emails', 'yith-woocommerce-barcodes' ),
			'default' => 'no',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_orders',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),
		array(
			'type' => 'sectionend',
		),
	),
);

return $general_options;
