<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_DesignDraw $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('General', 'gt3_themes_core')
	)
);

$widget->add_control(
	'position',
	array(
		'label'      => esc_html__('Position', 'gt3_themes_core'),
		'type'       => Controls_Manager::SELECT,
		'options'    => array(
			'left'   => esc_html__('Left', 'gt3_themes_core'),
			'right'  => esc_html__('Right', 'gt3_themes_core'),
			'top'    => esc_html__('Top', 'gt3_themes_core'),
			'bottom' => esc_html__('Bottom', 'gt3_themes_core'),
		),
		'default'    => 'left',
	)
);

$widget->add_control(
	'enable_link',
	array(
		'label' => esc_html__('Enable Link?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'link',
	array(
		'label'   => esc_html__('Link', 'gt3_themes_core'),
		'type'    => Controls_Manager::URL,
		'default' => array(
			'url'         => '#',
			'is_external' => false,
			'nofollow'    => false,
		),
		'condition' => array(
			'enable_link!' => '',
		),
	)
);

$widget->add_control(
	'draw_bg',
	array(
		'label'     => esc_html__('Element Background Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-designdraw .gt3_svg_line svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-designdraw .gt3_svg_line svg g > *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
		),
		'separator' => 'none',
		'default' => '#ffffff'
	)
);

$widget->add_control(
	'enable_icon',
	array(
		'label' => esc_html__('Enable Icon?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'     => 'yes',
	)
);

$widget->add_control(
	'element_icon',
	array(
		'label'     => esc_html__('Icon:', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'enable_icon!' => '',
		),
	)
);

$widget->add_control(
	'color',
	array(
		'label'     => esc_html__('Icon Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-designdraw .gt3_svg_line_icon' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
		'condition' => array(
			'enable_icon!' => '',
		),
	)
);

$widget->end_controls_section();