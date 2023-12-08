<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function vc_btn_element_params() {
	/**
	 * New button implementation
	 * array_merge is needed due to merging other shortcode data into params.
	 * @since 4.5
	 */

	$pixel_icons = vc_pixel_icons();
	require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-icon-element.php' );

	$icons_params = vc_map_integrate_shortcode( vc_icon_element_params(), 'i_', '', array(
		'include_only_regex' => '/^(type|icon_\w*)/',
	// we need only type, icon_fontawesome, icon_blabla..., NOT color and etc
	), array(
		'element' => 'add_icon',
		'value' => 'true',
	) );
	// populate integrated vc_icons params.
	if ( is_array( $icons_params ) && ! empty( $icons_params ) ) {
		foreach ( $icons_params as $key => $param ) {
			if ( is_array( $param ) && ! empty( $param ) ) {
				if ( 'i_type' === $param['param_name'] ) {
					// append pixelicons to dropdown
					$icons_params[ $key ]['value'][ esc_html__( 'Pixel', 'js_composer' ) ] = 'pixelicons';
				}
				if ( isset( $param['admin_label'] ) ) {
					// remove admin label
					unset( $icons_params[ $key ]['admin_label'] );
				}
			}
		}
	}
	$color_value = array_merge( array(
		// Btn1 Colors
			esc_html__( 'Classic Grey', 'js_composer' ) => 'default',
		esc_html__( 'Classic Blue', 'js_composer' ) => 'primary',
		esc_html__( 'Classic Turquoise', 'js_composer' ) => 'info',
		esc_html__( 'Classic Green', 'js_composer' ) => 'success',
		esc_html__( 'Classic Orange', 'js_composer' ) => 'warning',
		esc_html__( 'Classic Red', 'js_composer' ) => 'danger',
		esc_html__( 'Classic Black', 'js_composer' ) => 'inverse',
	// + Btn2 Colors (default color set)
	), vc_get_shared( 'colors-dashed' ) );
	$params = array_merge( array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'title',
			// fully compatible to btn1 and btn2
			'value' => esc_html__( 'Text on the button', 'js_composer' ),
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to button.', 'js_composer' ),
		// compatible with btn2 and converted from href{btn1}
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'description' => esc_html__( 'Select button display style.', 'js_composer' ),
			'param_name' => 'style',
			// partly compatible with btn2, need to be converted shape+style from btn2 and btn1
			'value' => array(
				esc_html__( 'Modern', 'js_composer' ) => 'modern',
				esc_html__( 'Classic', 'js_composer' ) => 'classic',
				esc_html__( 'Flat', 'js_composer' ) => 'flat',
				esc_html__( 'Outline', 'js_composer' ) => 'outline',
				esc_html__( '3d', 'js_composer' ) => '3d',
				esc_html__( 'Custom', 'js_composer' ) => 'custom',
				esc_html__( 'Outline custom', 'js_composer' ) => 'outline-custom',
				esc_html__( 'Gradient', 'js_composer' ) => 'gradient',
				esc_html__( 'Gradient Custom', 'js_composer' ) => 'gradient-custom',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Gradient Color 1', 'js_composer' ),
			'param_name' => 'gradient_color_1',
			'description' => esc_html__( 'Select first color for gradient.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => vc_get_shared( 'colors-dashed' ),
			'std' => 'turquoise',
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'gradient' ),
			),
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Gradient Color 2', 'js_composer' ),
			'param_name' => 'gradient_color_2',
			'description' => esc_html__( 'Select second color for gradient.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => vc_get_shared( 'colors-dashed' ),
			'std' => 'blue',
			// must have default color grey
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'gradient' ),
			),
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Gradient Color 1', 'js_composer' ),
			'param_name' => 'gradient_custom_color_1',
			'description' => esc_html__( 'Select first color for gradient.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => '#dd3333',
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'gradient-custom' ),
			),
			'edit_field_class' => 'vc_col-sm-4',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Gradient Color 2', 'js_composer' ),
			'param_name' => 'gradient_custom_color_2',
			'description' => esc_html__( 'Select second color for gradient.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => '#eeee22',
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'gradient-custom' ),
			),
			'edit_field_class' => 'vc_col-sm-4',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Button Text Color', 'js_composer' ),
			'param_name' => 'gradient_text_color',
			'description' => esc_html__( 'Select button text color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => '#ffffff',
			// must have default color grey
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'gradient-custom' ),
			),
			'edit_field_class' => 'vc_col-sm-4',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Background', 'js_composer' ),
			'param_name' => 'custom_background',
			'description' => esc_html__( 'Select custom background color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'custom' ),
			),
			'edit_field_class' => 'vc_col-sm-6',
			'std' => '#ededed',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'custom_text',
			'description' => esc_html__( 'Select custom text color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'custom' ),
			),
			'edit_field_class' => 'vc_col-sm-6',
			'std' => '#666',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Outline and Text', 'js_composer' ),
			'param_name' => 'outline_custom_color',
			'description' => esc_html__( 'Select outline and text color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'outline-custom' ),
			),
			'edit_field_class' => 'vc_col-sm-4',
			'std' => '#666',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover background', 'js_composer' ),
			'param_name' => 'outline_custom_hover_background',
			'description' => esc_html__( 'Select hover background color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'outline-custom' ),
			),
			'edit_field_class' => 'vc_col-sm-4',
			'std' => '#666',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Hover text', 'js_composer' ),
			'param_name' => 'outline_custom_hover_text',
			'description' => esc_html__( 'Select hover text color for your element.', 'js_composer' ),
			'dependency' => array(
				'element' => 'style',
				'value' => array( 'outline-custom' ),
			),
			'edit_field_class' => 'vc_col-sm-4',
			'std' => '#fff',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Shape', 'js_composer' ),
			'description' => esc_html__( 'Select button shape.', 'js_composer' ),
			'param_name' => 'shape',
			// need to be converted
			'value' => array(
				esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
				esc_html__( 'Square', 'js_composer' ) => 'square',
				esc_html__( 'Round', 'js_composer' ) => 'round',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'color',
			'description' => esc_html__( 'Select button color.', 'js_composer' ),
			// compatible with btn2, need to be converted from btn1
			'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
			'value' => $color_value,
			'std' => 'grey',
			// must have default color grey
			'dependency' => array(
				'element' => 'style',
				'value_not_equal_to' => array(
					'custom',
					'outline-custom',
					'gradient',
					'gradient-custom',
				),
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Size', 'js_composer' ),
			'param_name' => 'size',
			'description' => esc_html__( 'Select button display size.', 'js_composer' ),
			// compatible with btn2, default md, but need to be converted from btn1 to btn2
			'std' => 'md',
			'value' => vc_get_shared( 'sizes' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'description' => esc_html__( 'Select button alignment.', 'js_composer' ),
			// compatible with btn2, default left to be compatible with btn1
			'value' => array(
				esc_html__( 'Inline', 'js_composer' ) => 'inline',
				// default as well
				esc_html__( 'Left', 'js_composer' ) => 'left',
				// default as well
				esc_html__( 'Right', 'js_composer' ) => 'right',
				esc_html__( 'Center', 'js_composer' ) => 'center',
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Set full width button?', 'js_composer' ),
			'param_name' => 'button_block',
			'dependency' => array(
				'element' => 'align',
				'value_not_equal_to' => 'inline',
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Add icon?', 'js_composer' ),
			'param_name' => 'add_icon',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Icon Alignment', 'js_composer' ),
			'description' => esc_html__( 'Select icon alignment.', 'js_composer' ),
			'param_name' => 'i_align',
			'value' => array(
				esc_html__( 'Left', 'js_composer' ) => 'left',
				// default as well
				esc_html__( 'Right', 'js_composer' ) => 'right',
			),
			'dependency' => array(
				'element' => 'add_icon',
				'value' => 'true',
			),
		),
	), $icons_params, array(
		array(
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'i_icon_pixelicons',
			'value' => 'vc_pixel_icon vc_pixel_icon-alert',
			'settings' => array(
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon?
				'type' => 'pixelicons',
				'source' => $pixel_icons,
			),
			'dependency' => array(
				'element' => 'i_type',
				'value' => 'pixelicons',
			),
			'description' => esc_html__( 'Select icon from library.', 'js_composer' ),
		),
	), array(
		vc_map_add_css_animation( true ),
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
			'type' => 'checkbox',
			'heading' => esc_html__( 'Advanced on click action', 'js_composer' ),
			'param_name' => 'custom_onclick',
			'description' => esc_html__( 'Insert inline onclick javascript action.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'On click code', 'js_composer' ),
			'param_name' => 'custom_onclick_code',
			'description' => esc_html__( 'Enter onclick action code.', 'js_composer' ),
			'dependency' => array(
				'element' => 'custom_onclick',
				'not_empty' => true,
			),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	) );

	/**
	 * @class WPBakeryShortCode_Vc_Btn
	 */
	return array(
		'name' => esc_html__( 'Button', 'js_composer' ),
		'base' => 'vc_btn',
		'icon' => 'icon-wpb-ui-button',
		'category' => array(
			esc_html__( 'Content', 'js_composer' ),
		),
		'description' => esc_html__( 'Eye catching button', 'js_composer' ),
		'params' => $params,
		'js_view' => 'VcButton3View',
		'custom_markup' => '{{title}}<div class="vc_btn3-container"><button class="vc_general vc_btn3 vc_btn3-size-sm vc_btn3-shape-{{ params.shape }} vc_btn3-style-{{ params.style }} vc_btn3-color-{{ params.color }}">{{{ params.title }}}</button></div>',
	);
}
