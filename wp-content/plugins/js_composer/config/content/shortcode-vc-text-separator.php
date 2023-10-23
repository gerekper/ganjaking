<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once 'vc-icon-element.php';
$icon_params = vc_icon_element_params();

$icons_params = vc_map_integrate_shortcode( $icon_params, 'i_', esc_html__( 'Icon', 'js_composer' ), array(
	'exclude' => array(
		'align',
		'css',
		'el_class',
		'el_id',
		'link',
		'css_animation',
	),
	// we need only type, icon_fontawesome, icon_blabla..., NOT color and etc
), array(
	'element' => 'add_icon',
	'value' => 'true',
) );

// populate integrated vc_icons params.
if ( is_array( $icons_params ) && ! empty( $icons_params ) ) {
	foreach ( $icons_params as $key => $param ) {
		if ( is_array( $param ) && ! empty( $param ) ) {
			if ( isset( $param['admin_label'] ) ) {
				// remove admin label
				unset( $icons_params[ $key ]['admin_label'] );
			}
		}
	}
}

return array(
	'name' => esc_html__( 'Separator with Text', 'js_composer' ),
	'base' => 'vc_text_separator',
	'icon' => 'icon-wpb-ui-separator-label',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Horizontal separator line with heading', 'js_composer' ),
	'params' => array_merge( array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Title', 'js_composer' ),
			'param_name' => 'title',
			'holder' => 'div',
			'value' => esc_html__( 'Title', 'js_composer' ),
			'description' => esc_html__( 'Add text to separator.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Add icon?', 'js_composer' ),
			'param_name' => 'add_icon',
		),
	), $icons_params, array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Title position', 'js_composer' ),
			'param_name' => 'title_align',
			'value' => array(
				esc_html__( 'Center', 'js_composer' ) => 'separator_align_center',
				esc_html__( 'Left', 'js_composer' ) => 'separator_align_left',
				esc_html__( 'Right', 'js_composer' ) => 'separator_align_right',
			),
			'description' => esc_html__( 'Select title location.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Separator alignment', 'js_composer' ),
			'param_name' => 'align',
			'value' => array(
				esc_html__( 'Center', 'js_composer' ) => 'align_center',
				esc_html__( 'Left', 'js_composer' ) => 'align_left',
				esc_html__( 'Right', 'js_composer' ) => 'align_right',
			),
			'description' => esc_html__( 'Select separator alignment.', 'js_composer' ),
		),
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
			'param_name' => 'accent_color',
			'description' => esc_html__( 'Custom separator color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'color',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'param_name' => 'style',
			'value' => vc_get_shared( 'separator styles' ),
			'description' => esc_html__( 'Separator display style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Border width', 'js_composer' ),
			'param_name' => 'border_width',
			'value' => vc_get_shared( 'separator border widths' ),
			'description' => esc_html__( 'Select border width (pixels).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Element width', 'js_composer' ),
			'param_name' => 'el_width',
			'value' => vc_get_shared( 'separator widths' ),
			'description' => esc_html__( 'Separator element width in percents.', 'js_composer' ),
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
			'type' => 'hidden',
			'param_name' => 'layout',
			'value' => 'separator_with_text',
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	) ),
	'js_view' => 'VcTextSeparatorView',
);
