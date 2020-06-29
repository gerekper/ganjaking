<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Pinterest', 'js_composer' ),
	'base' => 'vc_pinterest',
	'icon' => 'icon-wpb-pinterest',
	'category' => esc_html__( 'Social', 'js_composer' ),
	'description' => esc_html__( 'Pinterest button', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Button type', 'js_composer' ),
			'param_name' => 'type',
			'admin_label' => true,
			'value' => array(
				esc_html__( 'Horizontal', 'js_composer' ) => 'horizontal',
				esc_html__( 'Vertical', 'js_composer' ) => 'vertical',
				esc_html__( 'No count', 'js_composer' ) => 'none',
			),
			'description' => esc_html__( 'Select button layout.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
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
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	),
);
