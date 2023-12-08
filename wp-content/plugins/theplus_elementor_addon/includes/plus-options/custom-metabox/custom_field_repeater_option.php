<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'cmb2_admin_init', 'theplus_acf_custom_field_repeater_metaboxes' );
function theplus_acf_custom_field_repeater_metaboxes() {

	$prefix = 'tp_';
	$acf_field = new_cmb2_box(
		array(
			'id'         => 'tp_acf_repeater_metaboxes',
			'title'      => esc_html__('ThePlus Elementor Options', 'theplus'),
			'object_types'      => array('elementor_library'),
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,
		)
	);
	$acf_field->add_field( array(
		'name'	=> esc_html__('Render Mode', 'theplus'),
		'desc'	=> '',
		'id'	=> $prefix . 'render_mode_type',
		'type'	=> 'select',
		'options'          => array(
			'default' => __( 'Default', 'theplus' ),
			'acf_repeater'   => __( 'ACF Repeater Content', 'theplus' ),
		),
	) );
	$acf_field->add_field( array(
		'name'          => __( 'Select Preview Post', 'theplus' ),		
		'id'            => $prefix . 'preview_post',
		'type'          => 'post_ajax_search',
		'query_args'	=> array(
			'post_type'			=> 'any',
			'posts_per_page'	=> -1,
			'post_status'		=> 'publish',
		)
	) );
	$acf_field->add_field( array(
			'name'		=> esc_html__('Select ACF Field', 'theplus'),
			'desc'		=> '',
			'id'		=> $prefix . 'acf_field_name',
			'type'		=> 'select',
			'options'	=> get_acf_repeater_field(),
		)
	);
}