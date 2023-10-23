<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'ZigZag Separator', 'js_composer' ),
	'base' => 'vc_zigzag',
	'icon' => 'vc_icon-vc-zigzag',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Horizontal zigzag separator line', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'value' => array_merge( vc_get_shared( 'colors' ), array( esc_html__( 'Custom color', 'js_composer' ) => 'custom' ) ),
			'std' => 'grey',
			'description' => esc_html__( 'Select color of separator.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Custom Color', 'js_composer' ),
			'param_name' => 'custom_color',
			'description' => esc_html__( 'Select color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'color',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'value' => array(
				esc_html__( 'Center', 'js_composer' ) => 'center',
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Right', 'js_composer' ) => 'right',
			),
			'description' => esc_html__( 'Select separator alignment.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Element width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => array(
				'100%' => '100',
				'90%' => '90',
				'80%' => '80',
				'70%' => '70',
				'60%' => '60',
				'50%' => '50',
				'40%' => '40',
				'30%' => '30',
				'20%' => '20',
				'10%' => '10',
			),
			'description' => esc_html__( 'Select separator width (percentage).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Border width', 'js_composer' ),
			'param_name' => 'el_border_width',
			'std' => '12',
			'value' => array(
				esc_html__( 'Extra small', 'js_composer' ) => '8',
				esc_html__( 'Small', 'js_composer' ) => '10',
				esc_html__( 'Medium', 'js_composer' ) => '12',
				esc_html__( 'Large', 'js_composer' ) => '15',
				esc_html__( 'Extra large', 'js_composer' ) => '20',
			),
			'description' => esc_html__( 'Select separator border width.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
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
