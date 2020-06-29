<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @var $tag - shortcode tag;
 */
return array(
	'name' => __( 'Inner Column', 'js_composer' ),
	'base' => 'vc_column_inner',
	'icon' => 'icon-wpb-row',
	'class' => '',
	'wrapper_class' => '',
	'controls' => 'full',
	'allowed_container_element' => false,
	'content_element' => false,
	'is_container' => true,
	'description' => esc_html__( 'Place content elements inside the inner column', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'value' => '',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Width', 'js_composer' ),
			'param_name' => 'width',
			'value' => array(
				esc_html__( '1 column - 1/12', 'js_composer' ) => '1/12',
				esc_html__( '2 columns - 1/6', 'js_composer' ) => '1/6',
				esc_html__( '3 columns - 1/4', 'js_composer' ) => '1/4',
				esc_html__( '4 columns - 1/3', 'js_composer' ) => '1/3',
				esc_html__( '5 columns - 5/12', 'js_composer' ) => '5/12',
				esc_html__( '6 columns - 1/2', 'js_composer' ) => '1/2',
				esc_html__( '7 columns - 7/12', 'js_composer' ) => '7/12',
				esc_html__( '8 columns - 2/3', 'js_composer' ) => '2/3',
				esc_html__( '9 columns - 3/4', 'js_composer' ) => '3/4',
				esc_html__( '10 columns - 5/6', 'js_composer' ) => '5/6',
				esc_html__( '11 columns - 11/12', 'js_composer' ) => '11/12',
				esc_html__( '12 columns - 1/1', 'js_composer' ) => '1/1',
				esc_html__( '20% - 1/5', 'js_composer' ) => '1/5',
				esc_html__( '40% - 2/5', 'js_composer' ) => '2/5',
				esc_html__( '60% - 3/5', 'js_composer' ) => '3/5',
				esc_html__( '80% - 4/5', 'js_composer' ) => '4/5',
			),
			'group' => esc_html__( 'Responsive Options', 'js_composer' ),
			'description' => esc_html__( 'Select column width.', 'js_composer' ),
			'std' => '1/1',
		),
		array(
			'type' => 'column_offset',
			'heading' => esc_html__( 'Responsiveness', 'js_composer' ),
			'param_name' => 'offset',
			'group' => esc_html__( 'Responsive Options', 'js_composer' ),
			'description' => esc_html__( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'js_composer' ),
		),
	),
	'js_view' => 'VcColumnView',
);
