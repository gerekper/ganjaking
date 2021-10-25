<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PieChart $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'widget_title',
	array(
		'label' => esc_html__('Widget Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
		'description' => esc_html__('Enter text used as widget title (Note: located below content element).', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'graph_value',
	array(
		'label'       => esc_html__('Value', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 50,
			'unit' => '%',
		),
		'range'       => array(
			'%' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		),
		'size_units'  => array( '%' ),
		'description' => esc_html__('Enter value for graph (Note: choose range from 0 to 100).', 'gt3_themes_core'),
		'label_block' => true,
	)
);

$widget->add_control(
	'label_value',
	array(
		'label' => esc_html__('Label value', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
		'description' => esc_html__('Enter label for pie chart (Note: leaving empty will set value from "Value" field).', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'chart_units',
	array(
		'label' => esc_html__('Units', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
		'description' => esc_html__('Enter units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'static_label_enable',
	array(
		'label'     => esc_html__('Enable Static Label Text?', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'static_label_value',
	array(
		'label' => esc_html__('Static Label Text', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
		'description' => esc_html__('Enter static label for pie chart.', 'gt3_themes_core'),
		'condition' => array(
			'static_label_enable!' => '',
		),
	)
);

$widget->add_control(
	'graph_size',
	array(
		'label'       => esc_html__('Size of the circle', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 125,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 100,
				'max'  => 300,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter value for size of the circle.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart' => 'min-height: {{SIZE}}{{UNIT}}; ',
		),
	)
);

$widget->add_control(
	'graph_thickness',
	array(
		'label'       => esc_html__('Thickness of the arc', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 5,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 20,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Select width of the arc.', 'gt3_themes_core'),
		'label_block' => true, 
	)
);

$widget->add_control(
	'line_cap',
	array(
		'label'      => esc_html__('Line Cap', 'gt3_themes_core'),
		'type'       => Controls_Manager::SELECT,
		'options'    => array(
			'round'  => esc_html__('Round', 'gt3_themes_core'),
			'square'    => esc_html__('Square', 'gt3_themes_core'),
		),
		'default'    => 'square',
		'description' => esc_html__('Select line cap of the arc.', 'gt3_themes_core'),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'style',
	array(
		'label' => esc_html__('Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->start_controls_tabs('style_items');

$widget->start_controls_tab(
	'style_title',
	array(
		'label' => esc_html__('Title','gt3_themes_core'),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'label'    => esc_html__('Title Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart_text',
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'       => esc_html__('Title Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart_text' => 'color: {{VALUE}};',
		),
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab(
	'style_circle',
	array(
		'label' => esc_html__('Circle','gt3_themes_core'),
	)
);

$widget->add_control(
	'circle_bg',
	array(
		'label'       => esc_html__('Circle Background', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart' => 'background: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'circle_border_color',
	array(
		'label'       => esc_html__('Circle Border Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'default'   => '#f7f7f7',
	)
);

$widget->add_control(
	'circle_arc_color_type',
	array(
		'label' => esc_html__('Gradient?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'     => 'yes',
	)
);

$widget->add_control(
	'circle_arc_bg',
	array(
		'label'       => esc_html__('The arc fill Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'default'   => '#5a81b7',
		'condition' => array(
			'circle_arc_color_type' => ''
		),
	)
);

$widget->add_control(
	'circle_arc_gradient1',
	array(
		'label'       => esc_html__('The arc fill Gradient Color1', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'default'   => '#5a81b7',
		'condition' => array(
			'circle_arc_color_type!' => ''
		),
	)
);

$widget->add_control(
	'circle_arc_gradient2',
	array(
		'label'       => esc_html__('The arc fill Gradient Color2', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'default'   => '#69dae3',
		'condition' => array(
			'circle_arc_color_type!' => ''
		),
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab(
	'style_label',
	array(
		'label' => esc_html__('Label','gt3_themes_core'),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'label_typography',
		'label'    => esc_html__('Label Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart .element_typography',
	)
);

$widget->add_control(
	'label_color',
	array(
		'label'       => esc_html__('Label Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart strong' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-piechart .gt3_elementor_pie_chart .static_label_text' => 'color: {{VALUE}};',
		),
	)
);

$widget->end_controls_tab();

$widget->end_controls_tabs();

$widget->end_controls_section();

