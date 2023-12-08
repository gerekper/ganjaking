<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Old Button', 'js_composer' ) . ' 2',
	'base' => 'vc_button2',
	'icon' => 'icon-wpb-ui-button',
	'deprecated' => '4.5',
	'content_element' => false,
	'category' => array(
		esc_html__( 'Content', 'js_composer' ),
	),
	'description' => esc_html__( 'Eye catching button', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to button.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'holder' => 'button',
			'class' => 'vc_btn',
			'param_name' => 'title',
			'value' => esc_html__( 'Text on the button', 'js_composer' ),
			'description' => esc_html__( 'Enter text on the button.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'value' => array(
				esc_html__( 'Inline', 'js_composer' ) => 'inline',
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Center', 'js_composer' ) => 'center',
				esc_html__( 'Right', 'js_composer' ) => 'right',
			),
			'description' => esc_html__( 'Select button alignment.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'param_name' => 'style',
			'value' => vc_get_shared( 'button styles' ),
			'description' => esc_html__( 'Select button display style and shape.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'value' => vc_get_shared( 'colors' ),
			'description' => esc_html__( 'Select button color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Size', 'js_composer' ),
			'param_name' => 'size',
			'value' => vc_get_shared( 'sizes' ),
			'std' => 'md',
			'description' => esc_html__( 'Select button size.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
	'js_view' => 'VcButton2View',
);
