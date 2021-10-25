<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Divider $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('General', 'gt3_themes_core')
	)
);

$widget->add_responsive_control(
	'align',
	array(
		'label'        => esc_html__('Alignment', 'gt3_themes_core'),
		'type'         => Controls_Manager::CHOOSE,
		'options'      => array(
			'left'   => array(
				'title' => esc_html__('Left', 'gt3_themes_core'),
				'icon'  => 'fa fa-align-left',
			),
			'center' => array(
				'title' => esc_html__('Center', 'gt3_themes_core'),
				'icon'  => 'fa fa-align-center',
			),
			'right'  => array(
				'title' => esc_html__('Right', 'gt3_themes_core'),
				'icon'  => 'fa fa-align-right',
			),
		),
		'prefix_class' => 'elementor%s-align-',
		'default'      => '',
	)
);

$widget->add_control(
	'line_left',
	array(
		'label' => esc_html__('Line left', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'line_right',
	array(
		'label' => esc_html__('Line right', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'text',
	array(
		'label' => esc_html__('Text', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'color',
	array(
		'label'     => esc_html__('Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-divider h6' => 'color: {{VALUE}};',
		),
		'separator' => 'none',

	)
);

$widget->add_control(
	'color_line',
	array(
		'label'     => esc_html__('Line Background Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor span.gt3_divider_line' => 'background: {{VALUE}};',
		),
		'separator' => 'none',
		'default' => '#777777'
	)
);

$widget->add_responsive_control(
	'line_width',
	array(
		'label'       => esc_html__('Line Width', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'size_units' => array( 'px','%' ),
		'default'     => array(
			'size' => 20,
			'unit' => 'px',
		),
		'tablet_default' => [
			'unit' => 'px',
		],
		'mobile_default' => [
			'unit' => 'px',
		],
		'range'       => array(
			'px' => array(
				'max'  => 1000,
				'step' => 1,
			),
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor span.gt3_divider_line' => 'width: calc({{SIZE}}{{UNIT}} / 2);',
		),
	)
);

$widget->add_control(
	'line_height',
	array(
		'label'       => esc_html__('Line Height', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 2,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 1,
				'max'  => 10,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor span.gt3_divider_line' => 'height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'divider_icon_type',
	array(
		'label'   => esc_html__('Icon', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'none'    => esc_html__('None', 'gt3_themes_core'),
			'icon'    => esc_html__('Icon', 'gt3_themes_core'),
			'image'   => esc_html__('Image', 'gt3_themes_core'),
		),
		'default' => 'none',
	)
);

$widget->add_control(
	'icon_position',
	array(
		'label'     => esc_html__('Icon position', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'left'  => esc_html__('Left', 'gt3_themes_core'),
			'right' => esc_html__('Right', 'gt3_themes_core'),
		),
		'default'   => 'left',
		'condition' => array(
			'divider_icon!' => 'none',
		),
	)
);

$widget->add_control(
	'divider_icon',
	array(
		'label'     => esc_html__('Icon:', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'divider_icon_type' => 'icon',
		),
	)
);

$widget->add_control(
	'image_size',
	array(
		'label'      => esc_html__('Image Width', 'gt3_themes_core'),
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
		'size_units' => array( 'px' ),
		'condition'  => array(
			'divider_icon_type' => 'image'
		),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor .elementor_divider_icon_container img' => 'width: {{SIZE}}{{UNIT}} !important;',
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor .icon_svg_btn' => 'width: {{SIZE}}{{UNIT}} !important;',

		),
	)
);

$widget->add_responsive_control(
	'icon_height',
	array(
		'label' => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'  => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'em',
		),
		'range'      => array(
			'px'  => array(
				'min'  => 8,
				'max'  => 64,
				'step' => 1,
			),
			'em'  => array(
				'min'  => 0.1,
				'max'  => 5,
				'step' => 0.1,
			),
			'rem' => array(
				'min'  => 0.1,
				'max'  => 5,
				'step' => 0.1,
			),

		),
		'size_units' => array( 'px', 'em', 'rem' ),
		'condition'  => array(
			'divider_icon_type' => 'icon',
		),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor .elementor_gt3_divider_icon' => 'font-size: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-divider .gt3_divider_wrapper-elementor .elementor_divider_icon_container .elementor_gt3_divider_icon' => 'font-size: {{SIZE}}{{UNIT}};',
		),
	)
);


$widget->add_control(
	'image',
	array(
		'label'     => esc_html__('Divider Image', 'gt3_themes_core'),
		'type'      => Controls_Manager::MEDIA,
		'default'   => array(
			'url' => Utils::get_placeholder_image_src(),
		),
		'condition' => array(
			'divider_icon_type' => 'image'
		),
	)
);




$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-divider h6',
		'condition'  => array(
			'text!' => ''
		),
	)
);

$widget->end_controls_section();