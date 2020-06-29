<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Raw HTML', 'js_composer' ),
	'base' => 'vc_raw_html',
	'icon' => 'icon-wpb-raw-html',
	'category' => esc_html__( 'Structure', 'js_composer' ),
	'wrapper_class' => 'clearfix',
	'description' => esc_html__( 'Output raw HTML code on your page', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textarea_raw_html',
			'holder' => 'div',
			'heading' => esc_html__( 'Raw HTML', 'js_composer' ),
			'param_name' => 'content',
			// @codingStandardsIgnoreLine
			'value' => base64_encode( '<p>I am raw html block.<br/>Click edit button to change this html</p>' ),
			'description' => esc_html__( 'Enter your HTML content.', 'js_composer' ),
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
