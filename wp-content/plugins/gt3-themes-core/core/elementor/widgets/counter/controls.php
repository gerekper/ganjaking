<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Counter $widget */

$widget->start_controls_section(
	'main',
	array(
		'label' => esc_html__('Main Settings', 'gt3_themes_core'),
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
		'prefix_class' => 'elementor%s-align-',
		'default'      => 'center',
	)
);

$widget->add_control(
	'start',
	array(
		'label'   => esc_html__('Start number', 'gt3_themes_core'),
		'type'    => Controls_Manager::NUMBER,
		'default' => 10,
		'min'     => 0,
	)
);

$widget->add_control(
	'end',
	array(
		'label'   => esc_html__('End number', 'gt3_themes_core'),
		'type'    => Controls_Manager::NUMBER,
		'default' => 100000,
		'min'     => 0,
	)
);

$widget->add_control(
	'description',
	array(
		'label'   => esc_html__('Description', 'gt3_themes_core'),
		'type'    => Controls_Manager::WYSIWYG,
		'default' => '',
	)
);

$widget->add_control(
	'duration',
	array(
		'label'   => esc_html__('Time (ms)', 'gt3_themes_core'),
		'type'    => Controls_Manager::NUMBER,
		'min'     => 400,
		'step'    => 100,
		'default' => 5000,
	)
);
$widget->end_controls_section();

$widget->start_controls_section(
	'option',
	array(
		'label' => esc_html__('Options', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);
$widget->add_control(
	'useEasing',
	array(
		'label'   => esc_html__('Use Easing', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			''               => esc_html__('No', 'gt3_themes_core'),
			'easeInQuad'     => 'In-Quad',
			'easeOutQuad'    => 'Out-Quad',
			'easeInOutQuad'  => 'In-Out-Quad',
			'easeInCubic'    => 'In-Cubic',
			'easeOutCubic'   => 'Out-Cubic',
			'easeInOutCubic' => 'In-Out-Cubic',
			'easeInQuart'    => 'In-Quart',
			'easeOutQuart'   => 'Out-Quart',
			'easeInOutQuart' => 'In-Out-Quart',
			'easeInQuint'    => 'In-Quint',
			'easeOutQuint'   => 'Out-Quint',
			'easeInSine'     => 'In-Sine',
			'easeOutSine'    => 'Out-Sine',
			'easeInOutSine'  => 'In-Out-Sine',
			'easeInExpo'     => 'In-Expo',
			'easeOutExpo'    => 'Out-Expo',
			'easeInOutExpo'  => 'In-Out-Expo',
		),
		'default' => '',
	)
);

$widget->add_control(
	'prefix',
	array(
		'label'   => esc_html__('Prefix', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '',
	)
);

$widget->add_control(
	'suffix',
	array(
		'label'   => esc_html__('Suffix', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '',
	)
);

$widget->add_control(
	'separator_enabled',
	array(
		'label'   => esc_html__('Thousands Separator', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'decimal',
	array(
		'label'   => esc_html__('Decimal', 'gt3_themes_core'),
		'type'    => Controls_Manager::NUMBER,
		'min'     => 0,
		'max'     => 8,
		'step'    => 1,
		'default' => '0',
	)
);

$widget->add_control(
	'separator',
	array(
		'label'     => esc_html__('Separator', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => ',',
		'condition' => array(
			'separator_enabled!' => '',
		),
	)
);

$widget->add_control(
	'decimal_point',
	array(
		'label'   => esc_html__('Decimal Point', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '.',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'icon',
	array(
		'label' => esc_html__('Icon', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);

$widget->add_control(
	'show_icon',
	array(
		'label' => esc_html__('Show Icon', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'icon_position',
	array(
		'label'     => esc_html__('Icon position', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'default'   => 'left',
		'options'   => array(
			'top'    => esc_html__('Top', 'gt3_themes_core'),
			'left'   => esc_html__('Left', 'gt3_themes_core'),
			'right'  => esc_html__('Right', 'gt3_themes_core'),
			'bottom' => esc_html__('Bottom', 'gt3_themes_core'),
		),
		'condition' => array(
			'show_icon!' => '',
		),
	)
);

//////////////// Icon
$widget->add_control(
	'icon_image',
	array(
		'label'     => esc_html__('Icon type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'default'   => 'icon',
		'options'   => array(
			'icon'  => esc_html__('Icon', 'gt3_themes_core'),
			'image' => esc_html__('Image', 'gt3_themes_core'),
		),
		'condition' => array(
			'show_icon!' => '',
		),
	)
);

$widget->add_control(
	'icon',
	array(
		'label'     => esc_html__('Icon', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'default'   => 'fa fa-bank',
		'condition' => array(
			'icon_image' => 'icon',
			'show_icon!' => '',
		),
	)
);

$widget->add_control(
	'image',
	array(
		'label'     => esc_html__('Select Image', 'gt3_themes_core'),
		'type'      => Controls_Manager::MEDIA,
		'default'   => array(
			'url' => Utils::get_placeholder_image_src(),
		),
		'condition' => array(
			'icon_image' => 'image',
			'show_icon!' => '',
		),
	)
);

$widget->add_control(
	'image_size',
	array(
		'type'        => Controls_Manager::IMAGE_DIMENSIONS,
		'default'     => array(
			'width'  => 64,
			'height' => 64,
		),
		'description' => esc_html__('Press APPLY for save settings', 'gt3_themes_core'),
		'condition'   => array(
			'icon_image' => 'image',
			'show_icon!' => '',
		),
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
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-counter .counter_text .counter, {{WRAPPER}}.elementor-widget-gt3-core-counter .counter_text .hidden_end'
	)
);

$widget->add_control(
	'digit_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter_text .counter' => 'color: {{VALUE}}',
		),
		'condition' => array(
			'text_color_gradient' => '',
		),
	)
);

$widget->add_control(
	'text_color_gradient',
	array(
		'label' => esc_html__('Text Color Gradient', 'gt3_themes_core'),
		'type' => Controls_Manager::SWITCHER,
		'description' => esc_html__('When selected this option background used as text color', 'gt3_themes_core'),
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
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-counter .description'
	)
);

$widget->add_control(
	'description_color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .description' => 'color: {{VALUE}}',
		)
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab('icon_tab',
	array(
		'label' => esc_html__('Icon', 'gt3_themes_core'),
	)
);

$widget->add_responsive_control(
	'icon_size',
	array(
		'label'      => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 32,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 8,
				'max'  => 64,
				'step' => 1,
			),
		),
		'condition'  => array(
			'icon_image' => 'icon',
			'icon!'      => '',
			'show_icon!' => '',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .gt3_icon' => 'font-size: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'icon_color',
	array(
		'label'     => esc_html__('Icon color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'icon_image' => 'icon',
			'icon!'      => '',
			'show_icon!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .gt3_icon' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'icon_background',
	array(
		'label'     => esc_html__('Icon Background color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'icon_image'   => 'icon',
			'show_icon!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .icon_container' => 'background-color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'icon_border',
	array(
		'label'     => esc_html__('Icon border', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'default'   => '',
		'options'   => array(
			''       => esc_html__('None', 'gt3_themes_core'),
			'dotted' => esc_html__('Dotted', 'gt3_themes_core'),
			'dashed' => esc_html__('Dashed', 'gt3_themes_core'),
			'solid'  => esc_html__('Solid', 'gt3_themes_core'),
			'double' => esc_html__('Double', 'gt3_themes_core'),
		),
		'condition' => array(
			'icon_image' => 'icon',
			'icon!'      => '',
			'show_icon!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .icon_container' => 'border-style: {{VALUE}};',
		),

	)
);

$widget->add_control(
	'icon_border_color',
	array(
		'label'     => esc_html__('Icon Border Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'icon_image'   => 'icon',
			'icon_border!' => '',
			'icon!'        => '',
			'show_icon!'   => '',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .icon_container' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'icon_border_width',
	array(
		'label'      => esc_html__('Icon Border Width', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 1,
				'max'  => 10,
				'step' => 1,
			),
		),
		'condition'  => array(
			'icon_image'   => 'icon',
			'icon_border!' => '',
			'icon!'        => '',
			'show_icon'    => 'yes',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .icon_container' => 'border-width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'icon_border_radius',
	array(
		'label'      => esc_html__('Border Radius', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 50,
			'unit' => '%',
		),
		'range'      => array(
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
		'condition'  => array(
			'icon_image'   => 'icon',
			'show_icon'  => 'yes',
		),
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .icon_container' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'icon_background_size',
	array(
		'label'      => esc_html__('Icon Background size', 'gt3_themes_core'),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 100,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 2,
			),
		),
		'condition'  => array(
			'icon_image'   => 'icon',
			'show_icon!' => '',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-counter .counter-wrapper.icon_type-icon .icon_container' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->end_controls_tab();
$widget->end_controls_tabs();
$widget->end_controls_section();
