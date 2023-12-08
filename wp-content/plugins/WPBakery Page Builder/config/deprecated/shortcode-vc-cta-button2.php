<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Old Call to Action Button', 'js_composer' ) . ' 2',
	'base' => 'vc_cta_button2',
	'icon' => 'icon-wpb-call-to-action',
	'deprecated' => '4.5',
	'content_element' => false,
	'category' => array( esc_html__( 'Content', 'js_composer' ) ),
	'description' => esc_html__( 'Catch visitors attention with CTA block', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Heading', 'js_composer' ),
			'admin_label' => true,
			'param_name' => 'h2',
			'value' => esc_html__( 'Hey! I am first heading line feel free to change me', 'js_composer' ),
			'description' => esc_html__( 'Enter text for heading line.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subheading', 'js_composer' ),
			'param_name' => 'h4',
			'value' => '',
			'description' => esc_html__( 'Enter text for subheading line.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'param_name' => 'style',
			'value' => vc_get_shared( 'cta styles' ),
			'description' => esc_html__( 'Select display shape and style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => vc_get_shared( 'cta widths' ),
			'description' => esc_html__( 'Select element width (percentage).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Text alignment', 'js_composer' ),
			'param_name' => 'txt_align',
			'value' => vc_get_shared( 'text align' ),
			'description' => esc_html__( 'Select text alignment in "Call to Action" block.', 'js_composer' ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Background color', 'js_composer' ),
			'param_name' => 'accent_color',
			'description' => esc_html__( 'Select background color.', 'js_composer' ),
		),
		array(
			'type' => 'textarea_html',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'content',
			'value' => esc_html__( 'I am promo text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ),
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to button (Important: adding link automatically adds button).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Text on the button', 'js_composer' ),
			'param_name' => 'title',
			'value' => esc_html__( 'Text on the button', 'js_composer' ),
			'description' => esc_html__( 'Add text on the button.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'param_name' => 'btn_style',
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
			'type' => 'dropdown',
			'heading' => esc_html__( 'Button position', 'js_composer' ),
			'param_name' => 'position',
			'value' => array(
				esc_html__( 'Right', 'js_composer' ) => 'right',
				esc_html__( 'Left', 'js_composer' ) => 'left',
				esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
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
);
