<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'cmb2_admin_init', 'theplus_custom_field_video_in_product_metaboxes' );
function theplus_custom_field_video_in_product_metaboxes() {
	$cmb = new_cmb2_box(
		array(
			'id'         => 'theplus_custom_field_video_in_product_metaboxes',
			'title'      => esc_html__('Video', 'theplus'),
			'object_types'      => 'product',
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,
		)
	);
	$cmb->add_field( 
		array(
		   'name'	=> esc_html__('Video Upload', 'theplus'),
			   'desc'	=> 'You can use Self Hosted Video URL here.',
			   'id'	=> 'theplus_custom_field_video',
			   'type'	=> 'file',
		)
	);
}