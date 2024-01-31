<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option         = get_option( 'ywcdd_processing_type', 'checkout' );
$add_new_button = YITH_Delivery_Date_Processing_Method()->get_taxonomy_label( 'add_new' );
$desc           = '';
$hide_button_class = '';
$extra_desc     = '';

if ( 'product' == $option ) {
	$query_args['meta_query'] = array(
		array(
			'key'     => '_ywcdd_type_checkout',
			'value'   => 'no',
			'compare' => '='
		)
	);

	$processing_method = YITH_Delivery_Date_Processing_Method()->get_processing_method( $query_args );

	if ( count( $processing_method ) == 0 ) {
		$desc = __( ' You can\'t find the old processing method when the "Product quantity table mode" is enabled. Please, add a new Processing method!', 'yith-woocommerce-delivery-date' );

	}else{
		$hide_button_class = 'ywcdd_processing_method_hide';
	}

	$extra_desc = '<p>' . yith_delivery_date_get_disabled_checkout_option_message() . '</p>';
}
$settings = array(
	'processing-method' => array(
		'processing_method_table_section_start' => array(
			'type' => 'title',
			'name' => ''
		),

		'processing_method_table'                          => array(
			'type'                 => 'yith-field',
			'yith-type'            => 'list-table',
			'post_type'            => 'yith_proc_method',
			'class'                => $hide_button_class,
			'list_table_class'     => 'YITH_Processing_Method_Table',
			'list_table_class_dir' => YITH_DELIVERY_DATE_INC . 'admin-tables/class.yith-delivery-date-processing-method-table.php',
			'title'                => __( 'Processing Methods', 'yith-woocommerce-delivery-date' ),
			'add_new_button'       => $add_new_button,
			'id'                   => 'ywcdd_processing_method_table',
			'desc'                 => $desc
		),
		'processing_method_table_section_end'              => array(
			'type' => 'sectionend'
		),
		'custom_processing_product_day_section_start'      => array(
			'type' => 'title',
			'name' => __( 'Custom Processing day for product', 'yith-woocommerce-delivery-date' ),
			'id'   => 'ywcdd_custom_processing_product',
			'desc' => __( 'Create customized processing day for specific products of your store.', 'yith-woocommerce-delivery-date' ) . $extra_desc,
		),
		'custom_processing_product_day'                    => array(
			'type'             => 'yith-field',
			'yith-type'        => 'toggle-element',
			'id'               => 'yith_new_shipping_day_prod',
			'add_button'       => __( 'Add New', 'yith-woocommerce-delivery-date' ),
			'title'            => '',
			'yith-display-row' => false,
			'subtitle'         => '',
			'sortable'         => false,
			'onoff_field'      => array(
				'id'      => 'enabled',
				'default' => 'yes'
			),
			'elements'         => array(

				array(
					'id'      => 'product',
					'type'    => 'ajax-products',
					'default' => '',
					'class' => 'yith-post-search yith-required-field',
					'name'    => __( 'Product', 'yith-woocommerce-delivery-date' ),
					'data' => array(
						'action' => 'woocommerce_json_search_products_and_variations',
						'security' => wp_create_nonce( 'search-products' )
					),
				),
				array(
					'id'        => 'need_process_day',
					'yith-type' => 'quantity-range-field',
					'type'      => 'yith-field',
					'name'      => _x( 'For quantity', 'Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' ),
					'default'   => array()
				)
			),
			'save_button'      => array(
				'id'    => 'yith_save_processing_product',
				'name'  => __( 'Save', 'yith-woocommerce-delivery-date' ),
				'class' => 'yith_save_processing_product'
			),
			'delete_button'    => array(
				'id'    => 'yith_delete_processing_product',
				'name'  => __( 'Delete', 'yith-woocommerce-delivery-date' ),
				'class' => 'yith_delete_processing_product'
			),
			'value' => ''

		),
		'custom_processing_product_day_section_end'        => array(
			'type' => 'sectionend'
		),
		'custom_processing_product_category_section_start' => array(
			'type' => 'title',
			'name' => __( 'Custom Processing day for category', 'yith-woocommerce-delivery-date' ) ,
			'desc' =>  __( 'Create customized processing day for specific product categories of your store.', 'yith-woocommerce-delivery-date' ).$extra_desc,
			'id'   => 'ywcdd_custom_processing_product_category'
		),
		'custom_processing_product_category'               => array(
			'type'             => 'yith-field',
			'yith-type'        => 'toggle-element',
			'id'               => 'yith_new_shipping_day_cat',
			'add_button'       => __( 'Add New', 'yith-woocommerce-delivery-date' ),
			'title'            => '',
			'yith-display-row' => false,
			'subtitle'         => '',
			'sortable'         => false,
			'onoff_field'      => array(
				'id'      => 'enabled',
				'default' => 'yes'
			),
			'elements'         => array(

				array(
					'id'       => 'category',
					'type'     => 'ajax-terms',
					'default'  => '',
					'multiple' => false,
					'data'     => array(
						'placeholder' => __( 'Search for a category&hellip;', 'yith-woocommerce-delivery-date' ),
						'taxonomy'    => 'product_cat',
					),
					'class' => 'yith-term-search yith-required-field',
					'name'     => __( 'Product Category', 'yith-woocommerce-delivery-date' ),
				),
				array(
					'id'        => 'need_process_day',
					'yith-type' => 'quantity-range-field',
					'type'      => 'yith-field',
					'name'      => _x( 'For quantity', 'Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' ),
					'default'   => array()
				)
			),
			'save_button'      => array(
				'id'    => 'yith_save_processing_product_category',
				'name'  => __( 'Save', 'yith-woocommerce-delivery-date' ),
				'class' => 'yith_save_processing_product_category'
			),
			'delete_button'    => array(
				'id'    => 'yith_delete_processing_product_category',
				'name'  => __( 'Delete', 'yith-woocommerce-delivery-date' ),
				'class' => 'yith_delete_processing_product_category'
			),
			'value' => ''
		),
		'custom_processing_product_category_section_end'   => array(
			'type' => 'sectionend'
		)
	)
);

return $settings;