<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Countdown $widget */

$widget->start_controls_section(
	'main',
	array(
		'label' => esc_html__('Main Settings', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'countdown_date',
	array(
		'label' => __( 'Сountdown Date', 'gt3_themes_core' ),
		'type' => Controls_Manager::DATE_TIME,
		'picker_options' => array(
			'enableTime' => true
		)
	)
);

$widget->add_control(
	'show_day',
	array(
		'label'   => esc_html__('Show Days?', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'show_hours',
	array(
		'label'   => esc_html__('Show Hours?', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'show_minutes',
	array(
		'label'   => esc_html__('Show Minutes?', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'show_seconds',
	array(
		'label'   => esc_html__('Show Seconds?', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_responsive_control(
	'size',
	array(
		'label'        => esc_html__( 'Size', 'gt3_themes_core' ),
		'type'         => Controls_Manager::SELECT,
		'options'      => array(
			'small'   =>  esc_html__( 'Small', 'gt3_themes_core' ),
			'medium' =>  esc_html__( 'Medium', 'gt3_themes_core' ),
			'large'  => esc_html__( 'Large', 'gt3_themes_core' ),
			'e_large'  => esc_html__( 'Extra Large', 'gt3_themes_core' ),
		),
		'default'      => 'small',
		'prefix_class' => 'gt3_countdown--size_',
	)
);


$widget->add_responsive_control(
	'align',
	array(
		'label'        => esc_html__( 'Alignment', 'gt3_themes_core' ),
		'type'         => Controls_Manager::CHOOSE,
		'options'      => array(
			'left'   => array(
				'title' => esc_html__( 'Left', 'gt3_themes_core' ),
				'icon'  => 'fa fa-align-left',
			),
			'center' => array(
				'title' => esc_html__( 'Center', 'gt3_themes_core' ),
				'icon'  => 'fa fa-align-center',
			),
			'right'  => array(
				'title' => esc_html__( 'Right', 'gt3_themes_core' ),
				'icon'  => 'fa fa-align-right',
			),
		),
		'prefix_class' => 'countdown_wrapper--',
		'default'      => 'center',
	)
);

$widget->end_controls_section();


$widget->start_controls_section(
	'style',
	array(
		'label' => esc_html__('Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->start_controls_tabs('style_tabs');
$widget->start_controls_tab('digit_tab',
	array(
		'label' => esc_html__('Digits', 'gt3_themes_core'),
	));

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'digit_typography',
		'label'    => esc_html__('Typography', 'gt3_themes_core'),
		'selector' => '{{WRAPPER}} .countdown-section, {{WRAPPER}} .countdown-section .countdown-amount'
	)
);

$widget->add_control(
	'digit_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .countdown-section' => 'color: {{VALUE}}',
		),
	)
);

$widget->add_group_control(
	Group_Control_Background::get_type(),
	array(
		'name' => 'header_background',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-counter .counter_text .counter',
		'label' => 'Color',
		'title' => 'Title',
		'fields_options' => array(
			'background' => array(
				'label' => esc_html__('Text Color', 'gt3_themes_core'),
			),
		),
		'condition' => array(
			'text_color_gradient!' => '',
		),
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab('description_tab',
	array(
		'label' => esc_html__('Description', 'gt3_themes_core'),
	));

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'description_typography',
		'label'    => esc_html__('Typography', 'gt3_themes_core'),
		'selector' => '{{WRAPPER}} .countdown-period'
	)
);

$widget->add_control(
	'description_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .countdown-period' => 'color: {{VALUE}}',
		)
	)
);

$widget->end_controls_tab();
$widget->end_controls_tabs();
$widget->end_controls_section();
