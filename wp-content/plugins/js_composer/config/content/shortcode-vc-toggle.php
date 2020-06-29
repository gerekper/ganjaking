<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$cta_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'custom_', esc_html__( 'Heading', 'js_composer' ), array(
	'exclude' => array(
		'source',
		'text',
		'css',
		'link',
	),
), array(
	'element' => 'use_custom_heading',
	'value' => 'true',
) );

$params = array_merge( array(
	array(
		'type' => 'textfield',
		'holder' => 'h4',
		'class' => 'vc_toggle_title',
		'heading' => esc_html__( 'Toggle title', 'js_composer' ),
		'param_name' => 'title',
		'value' => esc_html__( 'Toggle title', 'js_composer' ),
		'description' => esc_html__( 'Enter title of toggle block.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-9',
	),
	array(
		'type' => 'checkbox',
		'heading' => esc_html__( 'Use custom font?', 'js_composer' ),
		'param_name' => 'use_custom_heading',
		'description' => esc_html__( 'Enable Google fonts.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-3',
	),
	array(
		'type' => 'textarea_html',
		'holder' => 'div',
		'class' => 'vc_toggle_content',
		'heading' => esc_html__( 'Toggle content', 'js_composer' ),
		'param_name' => 'content',
		'value' => '<p>' . esc_html__( 'Toggle content goes here, click edit button to change this text.', 'js_composer' ) . '</p>',
		'description' => esc_html__( 'Toggle block content.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'param_name' => 'style',
		'value' => vc_get_shared( 'toggle styles' ),
		'description' => esc_html__( 'Select toggle design style.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Icon color', 'js_composer' ),
		'param_name' => 'color',
		'value' => array( esc_html__( 'Default', 'js_composer' ) => 'default' ) + vc_get_shared( 'colors' ),
		'description' => esc_html__( 'Select icon color.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown',
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Size', 'js_composer' ),
		'param_name' => 'size',
		'value' => array_diff_key( vc_get_shared( 'sizes' ), array( 'Mini' => '' ) ),
		'std' => 'md',
		'description' => esc_html__( 'Select toggle size', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Default state', 'js_composer' ),
		'param_name' => 'open',
		'value' => array(
			esc_html__( 'Closed', 'js_composer' ) => 'false',
			esc_html__( 'Open', 'js_composer' ) => 'true',
		),
		'description' => esc_html__( 'Select "Open" if you want toggle to be open by default.', 'js_composer' ),
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
), $cta_custom_heading, array(
	array(
		'type' => 'css_editor',
		'heading' => esc_html__( 'CSS box', 'js_composer' ),
		'param_name' => 'css',
		'group' => esc_html__( 'Design Options', 'js_composer' ),
	),
) );

return array(
	'name' => esc_html__( 'FAQ', 'js_composer' ),
	'base' => 'vc_toggle',
	'icon' => 'icon-wpb-toggle-small-expand',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Toggle element for Q&A block', 'js_composer' ),
	'params' => $params,
	'js_view' => 'VcToggleView',
);
