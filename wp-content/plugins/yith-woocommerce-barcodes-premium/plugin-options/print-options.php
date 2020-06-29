<?php
if( !defined( 'ABSPATH' ) )
    exit;


$tool = array(

    'print'  =>  array(

        'tool_section_start'   =>  array(
            'name'  => __('Print barcodes', 'yith-woocommerce-barcodes'),
            'type' =>   'title',
        ),

        'tool_print_barcodes_show_image'            => array(
	        'name'    => __( 'Show product image in printed list', 'yith-woocommerce-barcodes' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'onoff',
	        'desc'    => __( 'Enable to add the product image in the printable products list', 'yith-woocommerce-barcodes' ),
	        'id'      => 'tool_print_barcodes_show_image',
	        'default' => 'no',
        ),
        'ywbc_enable_print_barcodes_variations'                => array(
	        'name'    => __( 'Print a barcode list of: ', 'yith-woocommerce-barcodes' ),
	        'type'    => 'yith-field',
	        'yith-type' => 'select',
	        'id'      => 'ywbc_enable_print_barcodes_variations',
	        'options' => array(
		        'all_products'       => __( "All products", 'yith-woocommerce-barcodes' ),
		        'include_variations'        => __( "All products, including variations", 'yith-woocommerce-barcodes' ),
	        ),
	        'default' => 'all_products',
        ),
        'tool_print_barcodes'    => array(
            'type'  =>'print-barcodes',
            'desc' => __('Choose to print a list of barcodes of all products and if include or not the products variations', 'yith-woocommerce-barcodes' ),
            'id'    =>  'ywbc_print_product_barcode'
        ),



        'tool_print_barcodes_by_products'    => array(
	        'name' => __('Print barcodes by product', 'yith-woocommerce-barcodes'),
	        'type'  =>'print-barcodes-by-products',
	        'id'    =>  'tool_print_barcodes_by_products'
        ),

        'tool_print_section_end' =>  array(
            'type'  =>  'sectionend',
        ),

    )
);

return apply_filters( 'ywbc_tool_otions', $tool );
