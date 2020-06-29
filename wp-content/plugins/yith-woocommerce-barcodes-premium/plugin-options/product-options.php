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

	'product' => array(

		array(
			'name' => __( 'Product barcodes', 'yith-woocommerce-barcodes' ),
			'type' => 'title',
		),
		'ywbc_enable_on_products'                => array(
			'name'    => __( 'Generate and apply barcodes to products', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'If enabled, you can choose to apply barcodes to all products or only to products without a barcode', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_enable_on_products',
			'default' => 'yes',
		),

		'product_barcode_or_qr'  => array(
			'id'        =>  'product_barcode_or_qr',
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
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'ywbc_products_default_barcode_protocol' => array(
			'name'    => __( 'Product barcode protocol', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'select',
			'desc'    => __( 'Choose the barcode protocol you want to use to generate a new barcode on products', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_products_protocol',
			'options' => YITH_Barcode::get_protocols(),
			'class'   => 'wc-enhanced-select',
			'default' => 'EAN13',
			'deps'    => array(
				'id'    => 'product_barcode_or_qr',
				'value' => 'barcode',
				'type'  => 'hide'
			)
		),

		'ywbc_product_barcode_type'                => array(
			'name'    => __( 'Generate the barcode using: ', 'yith-woocommerce-barcodes' ),
			'type' => 'yith-field',
			'yith-type' => 'radio',
			'id'      => 'ywbc_product_barcode_type',
			'options' => array(
				'id'        => __( "The product ID", 'yith-woocommerce-barcodes' ),
				'sku'       => __( "The product SKU", 'yith-woocommerce-barcodes' ),
				'custom_field'  => __( "A custom field", 'yith-woocommerce-barcodes' ),
			),
			'desc'    => __( 'Choose which product info used to generate the barcodes', 'yith-woocommerce-barcodes' ),
			'default' => 'id',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'ywbc_product_barcode_type_custom_field' => array(
			'name'     => __( 'Custom field used for barcode generation', 'yith-woocommerce-barcodes' ),
			'desc'     => __( 'Enter the custom field name used to generate the barcodes', 'yith-woocommerce-barcodes' ),
			'id'       => 'ywbc_product_barcode_type_custom_field',
			'type' => 'yith-field',
			'yith-type' => 'text',
			'default'  => '',
			'deps'    => array(
				'id'    => 'ywbc_product_barcode_type',
				'value' => 'custom_field',
				'type'  => 'hide'
			)
		),

		'tool_regenerate_barcodes'                => array(
			'name'    => __( 'Generate and apply barcodes to: ', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'select',
			'id'      => 'ywbc_tool_regenerate_barcodes',
			'options' => array(
				'regenerate'       => __( "All products (also products that already have a barcode)", 'yith-woocommerce-barcodes' ),
				'generate'        => __( "Only products without a barcode", 'yith-woocommerce-barcodes' ),
			),
			'default' => 'generate',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'tool_apply_barcodes_product'    => array(
			'type'  =>'apply-barcodes',
			'desc' => __('Apply barcodes automatically to the products', 'yith-woocommerce-barcodes' ),
			'id'    =>  'ywbc_apply_product_barcode',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)

		),

		'ywbc_product_manual_barcode_product'            => array(
			'name'    => __( 'Enable manual barcode', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'If enabled, a form will be displayed in product page where a barcode value can be entered', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_product_manual_barcode_product',
			'default' => 'no',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'ywbc_create_on_products'                => array(
			'name'    => __( 'Automatically generate and apply barcodes on new products', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Enable to automatically apply a barcode in all new products', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_create_on_products',
			'default' => 'no',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

		'ywbc_show_on_product_page'              => array(
			'name'    => __( 'Show barcodes in product page', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Enable to show the product barcode in product detail page', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_show_on_product_page',
			'default' => 'yes',
			'deps'    => array(
				'id'    => 'ywbc_enable_on_products',
				'value' => 'yes',
				'type'  => 'hide'
			)
		),

        'ywbc_show_product_barcode_on_emails'                    => array(
            'name'    => __( 'Show product barcodes in order emails', 'yith-woocommerce-barcodes' ),
            'desc'    => __( 'Enable to show the product barcode in the orders emails', 'yith-woocommerce-barcodes' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywbc_show_product_barcode_on_emails',
            'default' => 'no',
            'deps'    => array(
	            'id'    => 'ywbc_enable_on_products',
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
