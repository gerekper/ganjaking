<?php
if( !defined( 'ABSPATH' ) )
    exit;


$settings = array(

    'general-settings'  =>  array(

        'general_section_start'   =>  array(
            'name'  => __('General Settings', 'yith-woocommerce-watermark'),
            'type' =>   'title',
            'id' => 'ywcwat_generalsectionstart'
        ),

		'watermark_gen_backup' => array(
			'name'	=> '',
			'type'	=> 'custom-button',
			'id'	=> 'ywcwat_gen_backup'
		),
        'watermark_button_apply'    => array(
            'name' => '',
            'type'  =>'watermark-apply',
            'id'    =>  'ywcwat_watermark_apply'
        ),
    	'watermark_quality_img' => array(
    		'name' => __('Jpeg Quality', 'yith-woocommerce-watermark'),
    		'type'	=> 'number',
    		'default' => 100,
    		'id' => 'ywcwat_quality_jpg',
    		'min'	=> 0,
    		'max'	=> 100
    	),	
 
        'general_section_end' =>  array(
            'type'  =>  'sectionend',
            'id'    =>  'ywcwat_generalsectionend'
        ),

    )
);

return apply_filters( 'ywcwat_free_options', $settings );