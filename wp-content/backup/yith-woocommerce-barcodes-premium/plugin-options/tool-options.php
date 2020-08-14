<?php
if( !defined( 'ABSPATH' ) )
    exit;


$tool = array(

    'tool'  =>  array(

        'tool_section_start'   =>  array(
            'name'  => __('Generate Barcode', 'yith-woocommerce-barcodes'),
            'type' =>   'title',
        ),
        'tool_apply_barcodes_enable_variations'                => array(
            'name'    => __( 'Apply Barcodes to variations', 'yith-woocommerce-barcodes' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Include product variations in the automatic apply barcodes process', 'yith-woocommerce-barcodes' ),
            'id'      => 'ywbc_enable_apply_barcodes_variations',
            'default' => 'yes',
        ),
        'tool_apply_barcodes_product'    => array(
            'name' => __('Apply Barcodes to products', 'yith-woocommerce-barcodes'),
            'type'  =>'apply-barcodes',
            'desc' => __('Apply automatically barcode to all products', 'yith-woocommerce-barcodes' ),
            'id'    =>  'ywbc_apply_product_barcode'
        ),
        'tool_print_barcodes_enable_variations'                => array(
            'name'    => __( 'Print variations Barcodes', 'yith-woocommerce-barcodes' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Include the variations barcodes in the document', 'yith-woocommerce-barcodes' ),
            'id'      => 'ywbc_enable_print_barcodes_variations',
            'default' => 'yes',
        ),
        'tool_print_barcodes'    => array(
            'name' => __('Print product Barcodes', 'yith-woocommerce-barcodes'),
            'type'  =>'print-barcodes',
            'desc' => __('Print the Barcodes of all products in a document', 'yith-woocommerce-barcodes' ),
            'id'    =>  'ywbc_print_product_barcode'
        ),

        'tool_section_end' =>  array(
            'type'  =>  'sectionend',
        ),

    )
);

return apply_filters( 'ywbc_tool_ptions', $tool );