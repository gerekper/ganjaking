<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Gutenberg Editor', 'js_composer' ),
	'icon' => 'vc_icon-vc-gutenberg',
	'wrapper_class' => 'clearfix',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Insert Gutenberg editor in your layout', 'js_composer' ),
	'weight' => - 10,
	'params' => array(
		array(
			'type' => 'gutenberg',
			'holder' => 'div',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'content',
			'value' => '<!-- wp:paragraph --><p>Hello! This is the Gutenberg block you can edit directly from the WPBakery Page Builder.</p><!-- /wp:paragraph -->',
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
