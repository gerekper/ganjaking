<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Pricing table
 * @since 7.0
 */

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$heading_list = array(
	'heading' => esc_html__( 'Heading', 'js_composer' ),
	'subheading' => esc_html__( 'Subheading', 'js_composer' ),
);

$heading_integration = array();
foreach ( $heading_list as $heading_name => $heading_title ) {
	$heading = vc_map_integrate_shortcode(
		vc_custom_heading_element_params(),
		$heading_name . '_',
		$heading_title,
		array(
			'exclude' => array(
				'source',
				'text',
				'css',
			),
		),
		array(
			'element' => 'use_custom_fonts_' . $heading_name,
			'value' => 'true',
		)
	);

	// This is needed to remove custom heading _tag and _align options.
	if ( is_array( $heading ) && ! empty( $heading ) ) {
		foreach ( $heading as $key => $param ) {
			if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
				$heading[ $key ]['value'] = '';
			}
		}
	}

	$heading_integration[ $heading_name ] = $heading;
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-btn-element.php' );
$vc_btn_element_params = vc_btn_element_params();

// we change some predefined values
$change_param_list = array(
	'title' => array( 'value' => __( 'Get now', 'js_composer' ) ),
	'color' => array( 'std' => 'primary' ),
	'align' => array( 'value' => array( 'center', 'inline', 'left', 'right' ) ),
	'button_block' => array( 'std' => 'true' ),
	'style' => array(
		'value' =>
				array(
					'Classic' => 'classic',
					'Modern'  => 'modern',
					'Flat' => 'flat',
					'Outline' => 'outline',
					'3d' => '3d',
					'Custom' => 'custom',
					'Outline custom' => 'outline-custom',
					'Gradient' => 'gradient',
					'Gradient Custom' => 'gradient-custom',
				),
	),
);

foreach ( $change_param_list as $param_name => $param_value ) {
	$key = array_search( $param_name, array_column( $vc_btn_element_params['params'], 'param_name' ) );

	if ( false === $key ) {
		continue;
	}

	$change_to = array_key_first( $param_value );
	$vc_btn_element_params['params'][ $key ][ $change_to ] = $param_value[ $change_to ];

	if ( 'button_block' === $param_name ) {
		$vc_btn_element_params['params'][ $key ]['value'] = array( 'Yes' => 'true' );
	}
}

$params = array_merge(
	array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Heading', 'js_composer' ),
			'admin_label' => true,
			'param_name' => 'heading',
			'value' => esc_html__( 'Growth', 'js_composer' ),
			'description' => esc_html__( 'Enter text for heading line.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use custom font?', 'js_composer' ),
			'param_name' => 'use_custom_fonts_heading',
			'description' => esc_html__( 'Enable Google fonts.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-3',
		),
	),
	$heading_integration['heading'],
	array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Subheading', 'js_composer' ),
			'param_name' => 'subheading',
			'value' => 'For business',
			'description' => esc_html__( 'Enter text for subheading line.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use custom font?', 'js_composer' ),
			'param_name' => 'use_custom_fonts_subheading',
			'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-3',
		),
	),
	$heading_integration['subheading'],
	array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Currency', 'js_composer' ),
			'param_name' => 'currency',
			'value' => '$',
			'description' => esc_html__( 'Enter your price currency.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
	),
	array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Price', 'js_composer' ),
			'param_name' => 'price',
			'value' => '99',
			'description' => esc_html__( 'Enter your price.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
	),
	array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Period', 'js_composer' ),
			'param_name' => 'period',
			'value' => '/mo',
			'description' => esc_html__( 'Enter your price action period.', 'js_composer' ),
			'edit_field_class' => 'vc_col-sm-9',
		),
	),
	array(
		array(
			'type' => 'textarea_html',
			'heading' => esc_html__( 'Text', 'js_composer' ),
			'param_name' => 'content',
			'value' => wp_kses(
			'<ul class="wpb-plan-features">' .
						'<li>' . __( 'All premium features', 'js_composer' ) . '</li>' .
						'<li>' . __( 'Online support', 'js_composer' ) . '</li>' .
						'<li>' . __( 'Regular updates', 'js_composer' ) . '</li>' .
						'<li>' . __( 'Personal training', 'js_composer' ) . '</li>' .
					'</ul>',
					array(
						'ul' => array( 'class' => array() ),
						'li' => array( 'class' => array() ),
					)
				),
		),
		array(
			'type' => 'colorpicker',
			'value' => '#5188F1',
			'heading' => esc_html__( 'Markers color', 'js_composer' ),
			'param_name' => 'markers_color',
			'description' => esc_html__( 'Select custom color for your list markers.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Add button', 'js_composer' ) . '?',
			'description' => esc_html__( 'Add button for call to action.', 'js_composer' ),
			'param_name' => 'add_button',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'yes',
				esc_html__( 'No', 'js_composer' ) => '',
			),
		),
	),
	vc_map_integrate_shortcode($vc_btn_element_params, 'btn_', esc_html__( 'Button', 'js_composer' ),
		array(
			'title' => 'Get Now',
			'exclude' => array( 'css' ),
		),
		array(
			'element' => 'add_button',
			'not_empty' => true,
		)
	),
	array(
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
	)
);

return array(
	'name' => esc_html__( 'Pricing Table', 'js_composer' ),
	'base' => 'vc_pricing_table',
	'icon' => 'icon-wpb-pricing-table',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Output pricing table on your page', 'js_composer' ),
	'since' => '7.0',
	'params' => $params,
);
