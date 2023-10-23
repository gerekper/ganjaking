<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$color_value = array_merge( array(
	esc_html__( 'Default', 'js_composer' ) => '',
), array(
	esc_html__( 'Classic Grey', 'js_composer' ) => 'bar_grey',
	esc_html__( 'Classic Blue', 'js_composer' ) => 'bar_blue',
	esc_html__( 'Classic Turquoise', 'js_composer' ) => 'bar_turquoise',
	esc_html__( 'Classic Green', 'js_composer' ) => 'bar_green',
	esc_html__( 'Classic Orange', 'js_composer' ) => 'bar_orange',
	esc_html__( 'Classic Red', 'js_composer' ) => 'bar_red',
	esc_html__( 'Classic Black', 'js_composer' ) => 'bar_black',
), vc_get_shared( 'colors-dashed' ), array(
	esc_html__( 'Custom Color', 'js_composer' ) => 'custom',
) );

$bg_color_value = array_merge( array(
	esc_html__( 'Classic Grey', 'js_composer' ) => 'bar_grey',
	esc_html__( 'Classic Blue', 'js_composer' ) => 'bar_blue',
	esc_html__( 'Classic Turquoise', 'js_composer' ) => 'bar_turquoise',
	esc_html__( 'Classic Green', 'js_composer' ) => 'bar_green',
	esc_html__( 'Classic Orange', 'js_composer' ) => 'bar_orange',
	esc_html__( 'Classic Red', 'js_composer' ) => 'bar_red',
	esc_html__( 'Classic Black', 'js_composer' ) => 'bar_black',
), vc_get_shared( 'colors-dashed' ), array(
	esc_html__( 'Custom Color', 'js_composer' ) => 'custom',
) );

return array(
	'name' => esc_html__( 'Progress Bar', 'js_composer' ),
	'base' => 'vc_progress_bar',
	'icon' => 'icon-wpb-graph',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Animated progress bar', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'param_group',
			'heading' => esc_html__( 'Values', 'js_composer' ),
			'param_name' => 'values',
			'description' => esc_html__( 'Enter values for graph - value, title and color.', 'js_composer' ),
			'value' => rawurlencode( wp_json_encode( array(
				array(
					'label' => esc_html__( 'Development', 'js_composer' ),
					'value' => '90',
				),
				array(
					'label' => esc_html__( 'Design', 'js_composer' ),
					'value' => '80',
				),
				array(
					'label' => esc_html__( 'Marketing', 'js_composer' ),
					'value' => '70',
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Label', 'js_composer' ),
					'param_name' => 'label',
					'description' => esc_html__( 'Enter text used as title of bar.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Value', 'js_composer' ),
					'param_name' => 'value',
					'description' => esc_html__( 'Enter value of bar.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Color', 'js_composer' ),
					'param_name' => 'color',
					'value' => $color_value,
					'description' => esc_html__( 'Select single bar background color.', 'js_composer' ),
					'admin_label' => true,
					'param_holder_class' => 'vc_colored-dropdown',
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Custom color', 'js_composer' ),
					'param_name' => 'customcolor',
					'description' => esc_html__( 'Select custom single bar background color.', 'js_composer' ),
					'dependency' => array(
						'element' => 'color',
						'value' => array( 'custom' ),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Custom text color', 'js_composer' ),
					'param_name' => 'customtxtcolor',
					'description' => esc_html__( 'Select custom single bar text color.', 'js_composer' ),
					'dependency' => array(
						'element' => 'color',
						'value' => array( 'custom' ),
					),
				),
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Units', 'js_composer' ),
			'param_name' => 'units',
			'description' => esc_html__( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'bgcolor',
			'value' => $bg_color_value,
			'description' => esc_html__( 'Select bar color.', 'js_composer' ),
			'admin_label' => true,
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Bar custom background color', 'js_composer' ),
			'param_name' => 'custombgcolor',
			'description' => esc_html__( 'Select custom background color for bars.', 'js_composer' ),
			'dependency' => array(
				'element' => 'bgcolor',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Bar custom text color', 'js_composer' ),
			'param_name' => 'customtxtcolor',
			'description' => esc_html__( 'Select custom text color for bars.', 'js_composer' ),
			'dependency' => array(
				'element' => 'bgcolor',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Options', 'js_composer' ),
			'param_name' => 'options',
			'value' => array(
				esc_html__( 'Add stripes', 'js_composer' ) => 'striped',
				esc_html__( 'Add animation (Note: visible only with striped bar).', 'js_composer' ) => 'animated',
			),
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
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	),
);
