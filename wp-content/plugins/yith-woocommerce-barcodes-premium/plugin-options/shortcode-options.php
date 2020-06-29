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

	'shortcode' => array(
		array(
			'name' => __( 'Order shortcode settings', 'yith-woocommerce-barcodes' ),
			'type' => 'title',
			'desc' => __( 'You can scan and manage your orders using the shortcode ', 'yith-woocommerce-barcodes' ) . ' [yith_order_barcode]',

		),

		'ywbc_order_shortcode_capability'                => array(
			'name'    => __( 'Shortcode capability', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'desc'    => __( 'Write the capabilities, separated by a comma, that will have access to the shortcode. The default value is "manage_woocommerce"', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_order_shortcode_capability',
			'default' => 'manage_woocommerce',
		),

		'ywbc_order_shortcode_processing_button'                => array(
			'name'    => __( 'Show Processing button', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Show a button to change the order to Processing in the order shortcode table', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_order_shortcode_processing_button',
			'default' => 'yes',
		),
		'ywbc_order_shortcode_completed_button'                => array(
			'name'    => __( 'Show Completed button', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Show a button to change the order to Completed in the order shortcode table', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_order_shortcode_completed_button',
			'default' => 'yes',
		),

		'ywbc_automatic_order_status'                => array(
			'name'    => __( 'Update an order status after scanning', 'yith-woocommerce-barcodes' ),
			'type' => 'yith-field',
			'yith-type' => 'radio',
			'id'      => 'ywbc_automatic_order_status',
			'options' => array(
				'no'        => __( "Don't update the order status", 'yith-woocommerce-barcodes' ),
				'processing' => __( "Process the order automatically", 'yith-woocommerce-barcodes' ),
				'completed' => __( "Complete the order automatically", 'yith-woocommerce-barcodes' ),
			),
			'default' => 'no',
		),
		array(
			'type' => 'sectionend',
		),



		///////////////// Product shortcode settings

		array(
			'name' => __( 'Product shortcode settings', 'yith-woocommerce-barcodes' ),
			'type' => 'title',
			'desc' => __( 'You can scan and manage your products using the shortcode ', 'yith-woocommerce-barcodes' ) . ' [yith_product_barcode]',

		),

		'ywbc_product_shortcode_capability'                => array(
			'name'    => __( 'Shortcode capability', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'desc'    => __( 'Write the capabilities, separated by a comma, that will have access to the shortcode. The default value is "manage_woocommerce"', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_product_shortcode_capability',
			'default' => 'manage_woocommerce',
		),
		'ywbc_product_shortcode_stock_buttons'                => array(
			'name'    => __( 'Show Stock buttons', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Show stock buttons in the product shortcode table', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_product_shortcode_stock_buttons',
			'default' => 'yes',
		),
		'ywbc_automatic_stock'                => array(
			'name'    => __( 'Stock behavior on product scan', 'yith-woocommerce-barcodes' ),
			'type' => 'yith-field',
			'yith-type' => 'radio',
			'id'      => 'ywbc_automatic_stock',
			'options' => array(
				'no'        => __( "Don't update the stock", 'yith-woocommerce-barcodes' ),
				'decrease'  => __( "Decrease the product stock automatically", 'yith-woocommerce-barcodes' ),
				'increase'  => __( "Increase the product stock automatically", 'yith-woocommerce-barcodes' ),
			),
			'default' => 'no',
		),
		'ywbc_product_shortcode_add_to_cart'                => array(
			'name'    => __( 'Show Add to Cart', 'yith-woocommerce-barcodes' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'desc'    => __( 'Show an Add to Cart button in the product shortcode table', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_product_shortcode_add_to_cart',
			'default' => 'no',
		),


		array(
			'type' => 'sectionend',
		),

	),
);

return $general_options;
