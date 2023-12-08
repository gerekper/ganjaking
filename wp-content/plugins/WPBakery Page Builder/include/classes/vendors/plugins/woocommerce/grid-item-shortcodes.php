<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'vc_gitem_wocommerce' => array(
		'name' => esc_html__( 'WooCommerce field', 'js_composer' ),
		'base' => 'vc_gitem_wocommerce',
		'icon' => 'icon-wpb-woocommerce',
		'category' => esc_html__( 'Content', 'js_composer' ),
		'description' => esc_html__( 'Woocommerce', 'js_composer' ),
		'php_class_name' => 'Vc_Gitem_Woocommerce_Shortcode',
		'params' => array(
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Content type', 'js_composer' ),
				'param_name' => 'post_type',
				'value' => array(
					esc_html__( 'Product', 'js_composer' ) => 'product',
					esc_html__( 'Order', 'js_composer' ) => 'order',
				),
				'save_always' => true,
				'description' => esc_html__( 'Select Woo Commerce post type.', 'js_composer' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Product field name', 'js_composer' ),
				'param_name' => 'product_field_key',
				'value' => Vc_Vendor_Woocommerce::getProductsFieldsList(),
				'dependency' => array(
					'element' => 'post_type',
					'value' => array( 'product' ),
				),
				'save_always' => true,
				'description' => esc_html__( 'Choose field from product.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Product custom key', 'js_composer' ),
				'param_name' => 'product_custom_key',
				'description' => esc_html__( 'Enter custom key.', 'js_composer' ),
				'dependency' => array(
					'element' => 'product_field_key',
					'value' => array( '_custom_' ),
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order fields', 'js_composer' ),
				'param_name' => 'order_field_key',
				'value' => Vc_Vendor_Woocommerce::getOrderFieldsList(),
				'dependency' => array(
					'element' => 'post_type',
					'value' => array( 'order' ),
				),
				'save_always' => true,
				'description' => esc_html__( 'Choose field from order.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Order custom key', 'js_composer' ),
				'param_name' => 'order_custom_key',
				'dependency' => array(
					'element' => 'order_field_key',
					'value' => array( '_custom_' ),
				),
				'description' => esc_html__( 'Enter custom key.', 'js_composer' ),
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Show label', 'js_composer' ),
				'param_name' => 'show_label',
				'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
				'save_always' => true,
				'description' => esc_html__( 'Enter label to display before key value.', 'js_composer' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Align', 'js_composer' ),
				'param_name' => 'align',
				'value' => array(
					esc_attr__( 'left', 'js_composer' ) => 'left',
					esc_attr__( 'right', 'js_composer' ) => 'right',
					esc_attr__( 'center', 'js_composer' ) => 'center',
					esc_attr__( 'justify', 'js_composer' ) => 'justify',
				),
				'save_always' => true,
				'description' => esc_html__( 'Select alignment.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Extra class name', 'js_composer' ),
				'param_name' => 'el_class',
				'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			),
		),
		'post_type' => Vc_Grid_Item_Editor::postType(),
	),
);
