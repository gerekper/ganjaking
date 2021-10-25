<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Accordion $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Items', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(
			array(
				'title'   => esc_html__('Accordion #1', 'gt3_themes_core'),
				'content' => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
			),
			array(
				'title'   => esc_html__('Accordion #2', 'gt3_themes_core'),
				'content' => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
			),
			array(
				'title'   => esc_html__('Accordion #3', 'gt3_themes_core'),
				'content' => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
			),
			array(
				'title'   => esc_html__('Accordion #4', 'gt3_themes_core'),
				'content' => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
			),
		),
		'fields'      => array_values($widget->get_repeater_fields()),
		'title_field' => '{{{ title }}}',
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

$widget->add_control(
	'header_title',
	array(
		'label' => esc_html__('Title:', 'gt3_themes_core'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_responsive_control(
	'title_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-accordion .accordion_wrapper .item_title' => 'color: {{VALUE}}',
		),
	)
);

$widget->add_responsive_control(
	'title_background_color',
	array(
		'label'     => esc_html__('Background', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-accordion .accordion_wrapper .item_title' => 'background-color: {{VALUE}}',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-accordion .accordion_wrapper .item_title',
	)
);

$widget->add_control(
	'header_content',
	array(
		'label' => esc_html__('Content:', 'gt3_themes_core'),
		'type'  => Controls_Manager::HEADING,
	)
);

$widget->add_responsive_control(
	'content_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-accordion .accordion_wrapper .item_content' => 'color: {{VALUE}}',
		),
	)
);

$widget->add_responsive_control(
	'content_background_color',
	array(
		'label'     => esc_html__('Background', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-accordion .accordion_wrapper .item_content' => 'background-color: {{VALUE}}',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'content_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-accordion .accordion_wrapper .item_content',
	)
);

$widget->end_controls_section();