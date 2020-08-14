<?php

$settings = array(
	'delivery-table' => array(
		'delivery_table_section_start' => array(
			'type' => 'title',
			'name' => ''
		),
		'delivery_table_list_table'                          => array(
			'type'                 => 'yith-field',
			'yith-type'            => 'list-table',
			'post_type'            => 'yith_product_table',
			'class'                => 'quantity_table',
			'list_table_class'     => 'YITH_Quantity_Table_List_Table',
			'list_table_class_dir' => YITH_DELIVERY_DATE_INC . 'admin-tables/class.yith-delivery-date-quantity-table-list.php',
			'title'                => __( 'Quantity Tables', 'yith-woocommerce-delivery-date' ),
			'add_new_button'       => YITH_Delivery_Product_Quantity_Table()->get_taxonomy_label('add_new'),
			'id'                   => 'ywcdd_quantity_table_list',

		),
		'delivery_table_section_end' => array(
			'type' => 'sectionend'
		),
		'delivery_table_customization_section_start' => array(
			'type' => 'title',
			'name' => __( 'Customization', 'yith-woocommerce-delivery-date' )
		),
		'delivery_table_customization_selected_day'            => array(
			'name'      => __( 'Color for the selected day', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcdd_table_customization_selected_day_color',
			'default'   => '#a46497',
		),
		'delivery_table_customization_selected_quantity'         => array(
			'name'      => __( 'Color for the selected quantity', 'yith-woocommerce-delivery-date' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'ywcdd_table_customization_selected_quantity_color',
			'default'   => '#c8b4c4'
		),
		'delivery_table_customization_section_end' => array(
			'type' => 'sectionend'
		)
	)
);

return $settings;