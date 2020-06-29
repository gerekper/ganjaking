<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Empty Space', 'js_composer' ),
	'base' => 'vc_empty_space',
	'icon' => 'icon-wpb-ui-empty_space',
	'show_settings_on_create' => true,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Blank space with custom height', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Height', 'js_composer' ),
			'param_name' => 'height',
			'value' => '32px',
			'admin_label' => true,
			'description' => esc_html__( 'Enter empty space height (Note: CSS measurement units allowed).', 'js_composer' ),
		),
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
