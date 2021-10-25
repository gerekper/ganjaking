<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_InfoList $widget */

$widget->start_controls_section(
	'repeater',
	array(
		'label' => esc_html__('Items', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'list',
	array(
		'label'       => esc_html__('Items', 'gt3_themes_core'),
		'show_label'  => false,
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(),
		'fields'      => array_values($widget->get_repeater_fields()),
		'title_field' => '{{ title }}',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'base_style',
	array(
		'label' => esc_html__('Base Settings', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'icon_position',
	array(
		'label'   => esc_html__('Icon Position', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'default' => 'left',
		'options' => array(
			'left'  => esc_html__('Left', 'gt3_themes_core'),
			'right' => esc_html__('Right', 'gt3_themes_core'),
		),
	)
);

$widget->add_responsive_control(
	'item_margin',
	array(
		'label'       => esc_html__('Size Between Items', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 30,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .timeline-item > div' => 'padding-bottom: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .infolist-wrapper.position-left .content_block' => 'padding-left: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .infolist-wrapper.position-right .content_block' => 'padding-right: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'style',
	array(
		'label' => esc_html__('Icon/Image Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_responsive_control(
	'icon_border',
	array(
		'label'       => esc_html__('Icon border', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'default'     => '',
		'options'     => array(
			''       => esc_html__('None', 'gt3_themes_core'),
			'dotted' => esc_html__('Dotted', 'gt3_themes_core'),
			'dashed' => esc_html__('Dashed', 'gt3_themes_core'),
			'solid'  => esc_html__('Solid', 'gt3_themes_core'),
			'double' => esc_html__('Double', 'gt3_themes_core'),
			'groove' => esc_html__('Groove', 'gt3_themes_core'),
			'ridge'  => esc_html__('Ridge', 'gt3_themes_core'),
			'inset'  => esc_html__('Inset', 'gt3_themes_core'),
			'outset' => esc_html__('Outset', 'gt3_themes_core'),
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_border_color',
	array(
		'label'       => esc_html__('Icon Border Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'icon_border!' => '',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_border_width',
	array(
		'label'       => esc_html__('Icon Border Width', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 8,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 1,
				'max'  => 20,
				'step' => 1,
			),
		),
		'condition'   => array(
			'icon_border!' => '',
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'border-width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_border_radius',
	array(
		'label'       => esc_html__('Icon Border Radius', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 15,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 2,
			),
			'%'  => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'condition'   => array(
			'icon_border!' => '',
		),
		'label_block' => true,
		'size_units'  => array( 'px', '%' ),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_background_color',
	array(
		'label'       => esc_html__('Icon Background Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'icon_border!' => '',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'background-color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_color',
	array(
		'label'       => esc_html__('Icon Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_image_size',
	array(
		'label'       => esc_html__('Icon/Image Wrapper Size', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 140,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 2,
			),
		),
		'label_block' => true,
		'size_units'  => array( 'px' ),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_width',
	array(
		'label'       => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 80,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 130,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper .icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
		),
	)
);

$widget->add_control(
	'image_size_width',
	array(
		'label'       => esc_html__('Image Width', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 80,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 130,
				'step' => 2,
			),
		),
		'label_block' => true,
		'size_units'  => array( 'px' ),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .icon-wrapper .image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'line_style',
	array(
		'label' => esc_html__('Line Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_responsive_control(
	'line_border',
	array(
		'label'       => esc_html__('Line Style', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'default'     => 'solid',
		'options'     => array(
			'none'   => esc_html__('None', 'gt3_themes_core'),
			'dotted' => esc_html__('Dotted', 'gt3_themes_core'),
			'dashed' => esc_html__('Dashed', 'gt3_themes_core'),
			'solid'  => esc_html__('Solid', 'gt3_themes_core'),
			'double' => esc_html__('Double', 'gt3_themes_core'),
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .line' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'line_border_color',
	array(
		'label'       => esc_html__('Line Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'icon_border!' => 'none',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .line' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'line_border_width',
	array(
		'label'       => esc_html__('Line Width', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 8,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 1,
				'max'  => 20,
				'step' => 1,
			),
		),
		'condition'   => array(
			'icon_border!' => 'none',
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .line' => 'border-width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'typography_style',
	array(
		'label' => esc_html__('Typography', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'title_style_heading',
	array(
		'label' => esc_html__('Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_responsive_control(
	'title_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .content_block .title' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-info-list .content_block .title',
	)
);

$widget->add_control(
	'description_style_heading',
	array(
		'label' => esc_html__('Description', 'gt3_themes_core'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_responsive_control(
	'description_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-info-list .content_block .description' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'description_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-info-list .content_block .description',
	)
);

$widget->end_controls_section();