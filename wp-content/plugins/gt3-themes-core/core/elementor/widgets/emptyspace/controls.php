<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;


/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_EmptySpace $widget */

$widget->start_controls_section(
	'button',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'height',
	array(
		'label'       => esc_html__('Height', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 30,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 500,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-emptyspace .gt3_es_default' => 'height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'responsive_es',
	array(
		'label'       => esc_html__('Set Resonsive Height', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'     => '',
		'description' => esc_html__('Allow resonsive Height of the block?', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'height_sm_desktop',
	array(
		'label'       => esc_html__('Height for small Desktops', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 30,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 500,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-emptyspace .gt3_es_sm_desktop' => 'height: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'responsive_es!' => '',
		),
	)
);

$widget->add_control(
	'height_tablet',
	array(
		'label'       => esc_html__('Height for Tablet', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 30,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 500,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-emptyspace .gt3_es_tablet' => 'height: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'responsive_es!' => '',
		),
	)
);

$widget->add_control(
	'height_mobile',
	array(
		'label'       => esc_html__('Height for Mobile', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 30,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 500,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-emptyspace .gt3_es_mobile' => 'height: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'responsive_es!' => '',
		),
	)
);

$widget->end_controls_section();