<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageProcessBar $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'type',
	array(
		'label'   => esc_html__('Type','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'horizontal'   => esc_html__('horizontal', 'gt3_themes_core'),
			'vertical'   => esc_html__('vertical', 'gt3_themes_core'),
		),
		'default' => 'horizontal',
	)
);

$widget->add_control(
	'steps',
	array(
		'label'   => esc_html__('Items Per Line','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'2'   => esc_html__('2', 'gt3_themes_core'),
			'3'   => esc_html__('3', 'gt3_themes_core'),
			'4'   => esc_html__('4', 'gt3_themes_core'),
			'5'   => esc_html__('5', 'gt3_themes_core'),
		),
		'default' => '4',
		'condition'  => array(
			'type' => 'horizontal',
		),
	)
);

$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Items', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(
			array(
				'proc_heading'   => esc_html__('Process #1', 'gt3_themes_core'),
				'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
				'proc_number' => '1',
			),
			array(
				'proc_heading'   => esc_html__('Process #2', 'gt3_themes_core'),
				'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
				'proc_number' => '2',
			),
			array(
				'proc_heading'   => esc_html__('Process #3', 'gt3_themes_core'),
				'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
				'proc_number' => '3',
			),
			array(
				'proc_heading'   => esc_html__('Process #4', 'gt3_themes_core'),
				'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
				'proc_number' => '4',
			),
		),
		'fields'      => array_values($widget->get_repeater_fields()),
		'title_field' => '{{{ proc_heading }}}',
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
	'tab_color',
	array(
		'label'   => esc_html__('Item Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'alpha'	=> false,
		'selectors'   => array(
			'{{WRAPPER}} .gt3_process_item .gt3_process_item__circle_wrapp .gt3_process_item__circle:before, {{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item .gt3_process_item__circle_line_after,{{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item .gt3_process_item__circle_line_before' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'   => esc_html__('Title Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item__heading' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'label'    => esc_html__('Title Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item__heading h3',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'number_typography',
		'label'    => esc_html__('Number Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item__heading .gt3_process_item__number',
	)
);

$widget->add_control(
	'content_color',
	array(
		'label'   => esc_html__('Content Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item__description' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'content_typography',
		'label'    => esc_html__('Content Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}} .gt3_image_process_bar_wrapper .gt3_process_item__description',
	)
);

$widget->end_controls_section();