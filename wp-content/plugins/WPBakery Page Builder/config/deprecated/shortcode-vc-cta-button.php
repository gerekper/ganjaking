<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$colors_arr = vc_colors_arr();
$icons_arr = vc_icons_arr();
$size_arr = vc_size_arr();
return array(
	'name' => esc_html__( 'Old Call to Action', 'js_composer' ),
	'base' => 'vc_cta_button',
	'icon' => 'icon-wpb-call-to-action',
	'deprecated' => '4.5',
	'content_element' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Catch visitors attention with CTA block', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textarea',
			'admin_label' => true,
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'call_text',
			'value' => esc_html__( 'Click edit button to change this text.', 'js_composer' ),
			'description' => esc_html__( 'Enter text content.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Text on the button', 'js_composer' ),
			'param_name' => 'title',
			'value' => esc_html__( 'Text on the button', 'js_composer' ),
			'description' => esc_html__( 'Enter text on the button.', 'js_composer' ),
		),
		array(
			'type' => 'href',
			'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
			'param_name' => 'href',
			'description' => esc_html__( 'Enter button link.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Target', 'js_composer' ),
			'param_name' => 'target',
			'value' => vc_target_param_list(),
			'dependency' => array(
				'element' => 'href',
				'not_empty' => true,
				'callback' => 'vc_cta_button_param_target_callback',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'value' => $colors_arr,
			'description' => esc_html__( 'Select button color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Button icon', 'js_composer' ),
			'param_name' => 'icon',
			'value' => $icons_arr,
			'description' => esc_html__( 'Select icon to display on button.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Size', 'js_composer' ),
			'param_name' => 'size',
			'value' => $size_arr,
			'description' => esc_html__( 'Select button size.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Button position', 'js_composer' ),
			'param_name' => 'position',
			'value' => array(
				esc_html__( 'Right', 'js_composer' ) => 'cta_align_right',
				esc_html__( 'Left', 'js_composer' ) => 'cta_align_left',
				esc_html__( 'Bottom', 'js_composer' ) => 'cta_align_bottom',
			),
			'description' => esc_html__( 'Select button alignment.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
	'js_view' => 'VcCallToActionView',
);
