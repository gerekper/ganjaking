<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$settings = array(

	'watermark-list' => array(

		'watermark_section_start' => array(
			'name' => __( 'Active Watermark', 'yith-woocommerce-watermark' ),
			'type' => 'title',
			'id'   => 'ywcwat_sectionstart'
		),

		'watermark_button_field' => array(
			'name' => __( 'Add New Watermark', 'yith-woocommerce-watermark' ),
			'type' => 'watermark-insert-new',
			'id'   => 'ywcwat_watermark_add_new'
		),

		'watermark_custom_field' => array(
			'name'    => '',
			'type'    => 'watermark-select',
			'id'      => 'ywcwat_watermark_select',
			'default' => array(),
			'value' => ''
		),

		'watermark_section_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcwat_sectionend'
		),
	)
);

return apply_filters( 'ywcwat_free_options', $settings );