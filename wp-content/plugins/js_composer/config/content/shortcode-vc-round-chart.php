<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Round Chart', 'js_composer' ),
	'base' => 'vc_round_chart',
	'class' => '',
	'icon' => 'icon-wpb-vc-round-chart',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Pie and Doughnut charts', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Design', 'js_composer' ),
			'param_name' => 'type',
			'value' => array(
				esc_html__( 'Pie', 'js_composer' ) => 'pie',
				esc_html__( 'Doughnut', 'js_composer' ) => 'doughnut',
			),
			'description' => esc_html__( 'Select type of chart.', 'js_composer' ),
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Style', 'js_composer' ),
			'description' => esc_html__( 'Select chart color style.', 'js_composer' ),
			'param_name' => 'style',
			'value' => array(
				esc_html__( 'Flat', 'js_composer' ) => 'flat',
				esc_html__( 'Modern', 'js_composer' ) => 'modern',
				esc_html__( 'Custom', 'js_composer' ) => 'custom',
			),
			'dependency' => array(
				'callback' => 'vcChartCustomColorDependency',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Gap', 'js_composer' ),
			'param_name' => 'stroke_width',
			'value' => array(
				0 => 0,
				1 => 1,
				2 => 2,
				5 => 5,
			),
			'description' => esc_html__( 'Select gap size.', 'js_composer' ),
			'std' => 2,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Outline color', 'js_composer' ),
			'param_name' => 'stroke_color',
			'value' => vc_get_shared( 'colors-dashed' ) + array( esc_html__( 'Custom', 'js_composer' ) => 'custom' ),
			'description' => esc_html__( 'Select outline color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
			'std' => 'white',
			'dependency' => array(
				'element' => 'stroke_width',
				'value_not_equal_to' => '0',
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Custom outline color', 'js_composer' ),
			'param_name' => 'custom_stroke_color',
			'description' => esc_html__( 'Select custom outline color.', 'js_composer' ),
			'dependency' => array(
				'element' => 'stroke_color',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Show legend?', 'js_composer' ),
			'param_name' => 'legend',
			'description' => esc_html__( 'If checked, chart will have legend.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
			'std' => 'yes',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Legend color', 'js_composer' ),
			'param_name' => 'legend_color',
			'value' => vc_get_shared( 'colors-dashed' ) + array( esc_html__( 'Custom', 'js_composer' ) => 'custom' ),
			'description' => esc_html__( 'Select legend color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
			'std' => 'black',
			'dependency' => array(
				'element' => 'legend',
				'value' => 'yes',
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Custom legend color', 'js_composer' ),
			'param_name' => 'custom_legend_color',
			'description' => esc_html__( 'Select custom legend color.', 'js_composer' ),
			'dependency' => array(
				'element' => 'legend_color',
				'value' => array( 'custom' ),
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Show hover values?', 'js_composer' ),
			'param_name' => 'tooltips',
			'description' => esc_html__( 'If checked, chart will show values on hover.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
			'std' => 'yes',
		),
		array(
			'type' => 'param_group',
			'heading' => esc_html__( 'Values', 'js_composer' ),
			'param_name' => 'values',
			'value' => rawurlencode( wp_json_encode( array(
				array(
					'title' => esc_html__( 'One', 'js_composer' ),
					'value' => '60',
					'color' => 'blue',
				),
				array(
					'title' => esc_html__( 'Two', 'js_composer' ),
					'value' => '40',
					'color' => 'pink',
				),
			) ) ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title', 'js_composer' ),
					'param_name' => 'title',
					'description' => esc_html__( 'Enter title for chart area.', 'js_composer' ),
					'admin_label' => true,
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Value', 'js_composer' ),
					'param_name' => 'value',
					'description' => esc_html__( 'Enter value for area.', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Color', 'js_composer' ),
					'param_name' => 'color',
					'value' => vc_get_shared( 'colors-dashed' ),
					'description' => esc_html__( 'Select area color.', 'js_composer' ),
					'param_holder_class' => 'vc_colored-dropdown',
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Custom color', 'js_composer' ),
					'param_name' => 'custom_color',
					'description' => esc_html__( 'Select custom area color.', 'js_composer' ),
				),
			),
			'callbacks' => array(
				'after_add' => 'vcChartParamAfterAddCallback',
			),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Animation', 'js_composer' ),
			'description' => esc_html__( 'Select animation style.', 'js_composer' ),
			'param_name' => 'animation',
			'value' => vc_get_shared( 'animation styles' ),
			'std' => 'easeInOutCubic',
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
