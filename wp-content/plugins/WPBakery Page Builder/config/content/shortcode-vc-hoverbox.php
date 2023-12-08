<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Call to action
 * @since 4.5
 */
require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$h2_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'primary_title_', esc_html__( 'Primary Title', 'js_composer' ), array(
	'exclude' => array(
		'source',
		'text',
		'css',
	),
), array(
	'element' => 'use_custom_fonts_primary_title',
	'value' => 'true',
) );

// This is needed to remove custom heading _tag and _align options.
if ( is_array( $h2_custom_heading ) && ! empty( $h2_custom_heading ) ) {
	foreach ( $h2_custom_heading as $key => $param ) {
		if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
			$h2_custom_heading[ $key ]['value'] = '';
			if ( isset( $param['settings'] ) && is_array( $param['settings'] ) && isset( $param['settings']['fields'] ) ) {
				$sub_key = array_search( 'tag', $param['settings']['fields'], true );
				if ( false !== $sub_key ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['tag'] ) ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields']['tag'] );
				}
				$sub_key = array_search( 'text_align', $param['settings']['fields'], true );
				if ( false !== $sub_key ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['text_align'] ) ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields']['text_align'] );
				}
			}
		}
	}
}
$h4_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'hover_title_', esc_html__( 'Hover Title', 'js_composer' ), array(
	'exclude' => array(
		'source',
		'text',
		'css',
	),
), array(
	'element' => 'use_custom_fonts_hover_title',
	'value' => 'true',
) );

// This is needed to remove custom heading _tag and _align options.
if ( is_array( $h4_custom_heading ) && ! empty( $h4_custom_heading ) ) {
	foreach ( $h4_custom_heading as $key => $param ) {
		if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
			$h4_custom_heading[ $key ]['value'] = '';
			if ( isset( $param['settings'] ) && is_array( $param['settings'] ) && isset( $param['settings']['fields'] ) ) {
				$sub_key = array_search( 'tag', $param['settings']['fields'], true );
				if ( false !== $sub_key ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['tag'] ) ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields']['tag'] );
				}
				$sub_key = array_search( 'text_align', $param['settings']['fields'], true );
				if ( false !== $sub_key ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['text_align'] ) ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields']['text_align'] );
				}
			}
		}
	}
}
$params = array_merge( array(
	array(
		'type' => 'attach_image',
		'heading' => esc_html__( 'Image', 'js_composer' ),
		'param_name' => 'image',
		'value' => '',
		'description' => esc_html__( 'Select image from media library.', 'js_composer' ),
		'admin_label' => true,
	),
	array(
		'type' => 'textfield',
		'heading' => esc_html__( 'Primary title', 'js_composer' ),
		'admin_label' => true,
		'param_name' => 'primary_title',
		'value' => esc_html__( 'Hover Box Element', 'js_composer' ),
		'description' => esc_html__( 'Enter text for heading line.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-9',
	),
	array(
		'type' => 'checkbox',
		'heading' => esc_html__( 'Use custom font?', 'js_composer' ),
		'param_name' => 'use_custom_fonts_primary_title',
		'description' => esc_html__( 'Enable Google fonts.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-3',
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Primary title alignment', 'js_composer' ),
		'param_name' => 'primary_align',
		'value' => vc_get_shared( 'text align' ),
		'std' => 'center',
		'description' => esc_html__( 'Select text alignment for primary title.', 'js_composer' ),
	),
), $h2_custom_heading, array(
	array(
		'type' => 'textfield',
		'heading' => esc_html__( 'Hover title', 'js_composer' ),
		'param_name' => 'hover_title',
		'value' => 'Hover Box Element',
		'description' => esc_html__( 'Hover Box Element', 'js_composer' ),
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-9',
	),
	array(
		'type' => 'checkbox',
		'heading' => esc_html__( 'Use custom font?', 'js_composer' ),
		'param_name' => 'use_custom_fonts_hover_title',
		'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-3',
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Hover title alignment', 'js_composer' ),
		'param_name' => 'hover_align',
		'value' => vc_get_shared( 'text align' ),
		'std' => 'center',
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'description' => esc_html__( 'Select text alignment for hovered title.', 'js_composer' ),
	),
	array(
		'type' => 'textarea_html',
		'heading' => esc_html__( 'Hover text', 'js_composer' ),
		'param_name' => 'content',
		'value' => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ),
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'description' => esc_html__( 'Hover part text.', 'js_composer' ),
	),
), $h4_custom_heading, array(
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Shape', 'js_composer' ),
		'param_name' => 'shape',
		'std' => 'rounded',
		'value' => array(
			esc_html__( 'Square', 'js_composer' ) => 'square',
			esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
			esc_html__( 'Round', 'js_composer' ) => 'round',
		),
		'description' => esc_html__( 'Select block shape.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Background Color', 'js_composer' ),
		'param_name' => 'hover_background_color',
		'value' => vc_get_shared( 'colors-dashed' ) + array( esc_html__( 'Custom', 'js_composer' ) => 'custom' ),
		'description' => esc_html__( 'Select color schema.', 'js_composer' ),
		'std' => 'grey',
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_cta3-colored-dropdown',
	),
	array(
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Background color', 'js_composer' ),
		'param_name' => 'hover_custom_background',
		'description' => esc_html__( 'Select custom background color.', 'js_composer' ),
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'dependency' => array(
			'element' => 'hover_background_color',
			'value' => array( 'custom' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Width', 'js_composer' ),
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
		'description' => esc_html__( 'Select block width (percentage).', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'param_name' => 'align',
		'description' => esc_html__( 'Select block alignment.', 'js_composer' ),
		'value' => array(
			esc_html__( 'Left', 'js_composer' ) => 'left',
			esc_html__( 'Right', 'js_composer' ) => 'right',
			esc_html__( 'Center', 'js_composer' ) => 'center',
		),
		'std' => 'center',
	),
	array(
		'type' => 'checkbox',
		'heading' => esc_html__( 'Add button', 'js_composer' ) . '?',
		'description' => esc_html__( 'Add button for call to action.', 'js_composer' ),
		'group' => esc_html__( 'Hover Block', 'js_composer' ),
		'param_name' => 'hover_add_button',
	),
	array(
		'type' => 'checkbox',
		'heading' => esc_html__( 'Reverse blocks', 'js_composer' ),
		'param_name' => 'reverse',
		'description' => esc_html__( 'Reverse hover and primary block.', 'js_composer' ),
	),
), vc_map_integrate_shortcode( 'vc_btn', 'hover_btn_', esc_html__( 'Hover Button', 'js_composer' ), array(
	'exclude' => array( 'css' ),
), array(
	'element' => 'hover_add_button',
	'not_empty' => true,
) ), array(
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
) );

return array(
	'name' => esc_html__( 'Hover Box', 'js_composer' ),
	'base' => 'vc_cta',
	'icon' => 'vc_icon-vc-hoverbox',
	'category' => array( esc_html__( 'Content', 'js_composer' ) ),
	'description' => esc_html__( 'Animated flip box with image and text', 'js_composer' ),
	'params' => $params,
);
