<?php
if( !defined('ABSPATH')){
	exit;
}
$meta_boxes_options = array(
	'label' => __( 'Quantity Table settings', 'yith-woocommerce-delivery-date' ),
	'pages' => 'yith_product_table', //or array( 'post-type1', 'post-type2')
	'context' => 'normal', //('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs' => array(
		'delivery_table_settings' => array(
			'label' => __( 'Settings', 'yith-woocommerce-delivery-date' ),
			'fields' => array(

				'ywcdd_quantity_table' => array(
					'label' => __( 'Table Content', 'yith-woocommerce-delivery-date' ),
					'type' => 'quantity-table',
					'id' => 'ywcdd_qty_product_table'
				),
				'ywcdd_enable_quantity_rule_table' => array(
					'label' => __( 'Enable Table','yith-woocommerce-delivery-date'),
					'desc' => __( 'Enable or disable this product table', 'yith-woocommerce-delivery-date'),
					'type' => 'onoff',
					'id' => 'ywcdd_enable_quantity_rule_table',
					'std' => 'yes'
				),
				'ywcdd_table_product' => array(
					'label' => __( 'Assign this table to', 'yith-woocommerce-delivery-date' ),
					'desc' => __('Choose how to set this table in your shop','yith-woocommerce-delivery-date'),
					'type' => 'radio',
					'options' => array(
						'product_cat' => __( 'Product Categories', 'yith-woocommerce-delivery-date'),
						'product' => __( 'Products', 'yith-woocommerce-delivery-date' )
					),
					'std' => 'product',
					'id' => 'ywcdd_table_how_set_table'
				),

				'ywcdd_select_product' => array(
					'label' => __( 'Select Products', 'yith-woocommerce-delivery-date'),
					'id' => 'ywcdd_table_select_product',
					'type' => 'ajax-products',
					'data' => array(
						'action' => 'woocommerce_json_search_products_and_variations',
						'security' => wp_create_nonce( 'search-products' )
					),
					'desc' => __('Select in which products you want to show the table', 'yith-woocommerce-delivery-date'),
					'multiple' => true
				),
				'ywcdd_select_product_category' => array(
					'label' => __( 'Select Product Categories', 'yith-woocommerce-delivery-date'),
					'id' => 'ywcdd_table_select_product_cat',
					'type' => 'ajax-terms',
					'data' => array(
						'taxonomy' => 'product_cat'
					),
					'desc' => __('Select on which product categories you want to show the table', 'yith-woocommerce-delivery-date'),
					'multiple' => true
				),
				'ywcdd_need_day' => array(
					'label' => __( 'Required days', 'yith-woocommerce-delivery-date' ),
					'id' => 'ywcdd_table_need_days',
					'type' => 'number',
					'min' =>0,
					'step' => 1,
					'std' => 0,
					'desc' => __( 'Set the number of days needed to prepare the product belonging to this table', 'yith-woocommerce-delivery-date')
				),
				'ywcdd_select_carrier' => array(
					'label' => __( 'Set carrier for this table ', 'yith-woocommerce-delivery-date'),
					'id' => 'ywcdd_table_select_carrier',
					'type' => 'select',
					'class' => 'wc-enhanced-select',
					'desc' => __('Select a carrier for this table', 'yith-woocommerce-delivery-date'),
					'multiple' => false,
					'options' => YITH_Delivery_Date_Carrier()->get_all_formatted_carriers()
				)
			),
		),
	)
);

return $meta_boxes_options;