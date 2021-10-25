<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Testimonials $widget */

$widget->start_controls_section(
	'general',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'nav',
	array(
		'label'   => esc_html__('Navigation', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'none'   => esc_html__('None', 'gt3_themes_core'),
			'arrows' => esc_html__('Arrows', 'gt3_themes_core'),
			'dots'   => esc_html__('Dots', 'gt3_themes_core'),
		),
		'default' => 'arrows',
	)
);

$widget->add_control(
	'type',
	array(
		'label'   => esc_html__('Style', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'style1'   => esc_html__('Style 1', 'gt3_themes_core'),
			'style2'   => esc_html__('Style 2', 'gt3_themes_core'),
			'style3'   => esc_html__('Style 3', 'gt3_themes_core'),
			'style4'   => esc_html__('Style 4', 'gt3_themes_core'),
		),
		'default' => 'style4',
	)
);

$widget->add_control(
	'quote_marker',
	array(
		'label' => esc_html__('Enable Quote Marker Icon?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'condition' => array(
			'type' => array('style2', 'style4')
		),
	)
);

$widget->add_control(
	'items_per_line',
	array(
		'label'   => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1'   => esc_html__('1', 'gt3_themes_core'),
			'2'   => esc_html__('2', 'gt3_themes_core'),
			'3'   => esc_html__('3', 'gt3_themes_core'),
		),
		'default' => '1',
	)
);

$widget->add_control(
	'autoplay',
	array(
		'label' => esc_html__('Autoplay', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'autoplay_time',
	array(
		'label'     => esc_html__('Autoplay time', 'gt3_themes_core'),
		'type'      => Controls_Manager::NUMBER,
		'default'   => 4000,
		'min'       => '0',
		'step'      => 100,
		'condition' => array(
			'autoplay' => 'yes'
		),
	)
);

$widget->add_control(
	'round_imgs',
	array(
		'label' => esc_html__('Circular Images?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'items',
	array(
		'label' => esc_html__('Items', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_CONTENT,
	)
);

$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Items', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(),
		'fields'      => array_values($widget->get_repeater_fields()),
		'title_field' => '{{{ name }}}',
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
	'image_align',
	array(
		'label'   => esc_html__('Image Author', 'gt3_themes_core'),
		'type'    => Controls_Manager::CHOOSE,
		'options' => array(
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
		'default' => '',
		'condition' => array(
			'type' => array('style1', 'style3')
		),
	)
);

$widget->add_control(
	'image_size_title',
	array(
		'label'     => esc_html__('Image size', 'gt3_themes_core'),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'none',
	)
);

$widget->add_control(
	'image_size',
	array(
		'label'       => esc_html__('Image size', 'gt3_themes_core'),
		'type'        => Controls_Manager::IMAGE_DIMENSIONS,
		'default'     => array(
			'width'  => 64,
			'height' => 64,
		),
		'description' => esc_html__('Press APPLY for save settings.', 'gt3_themes_core'),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .testimonials_photo img' => 'width: {{WIDTH}}px; height: {{HEIGHT}}px;',
		),
		'separator'   => 'none',
	)
);

$widget->add_control(
	'text_align',
	array(
		'label'   => esc_html__('Text align', 'gt3_themes_core'),
		'type'    => Controls_Manager::CHOOSE,
		'options' => array(
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
		'default' => '',
	)
);

$widget->add_control(
	'color_title',
	array(
		'label'     => esc_html__('Text Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .testimonials-text' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .module_testimonial.style4.nav-arrows .slick-slider:before' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .module_testimonial .slick-arrow:hover:after' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .module_testimonial.style4.nav-arrows .slick-slider:after' => 'border-top-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .module_testimonial.style4 .svg_icon' => 'color: {{VALUE}};',
		)
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-testimonials .testimonials-text',
	)
);

$widget->add_control(
	'author_align',
	array(
		'label'   => esc_html__('Alignment Author', 'gt3_themes_core'),
		'type'    => Controls_Manager::CHOOSE,
		'options' => array(
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
		'default' => '',
	)
);

$widget->add_control(
	'color_author',
	array(
		'label'     => esc_html__('Color Author', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .testimonials_title' => 'color: {{VALUE}};',
		)
	)
);

$widget->add_control(
	'color_author_position',
	array(
		'label'     => esc_html__('Color Author Position', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .testimonials-sub_name' => 'color: {{VALUE}};',
		)
	)
);

$widget->add_control(
	'color_slider_arrow',
	array(
		'label'     => esc_html__('Slider Arrow Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .module_testimonial.style4.nav-arrows .slick-arrow' => 'border: 1px solid {{VALUE}} !important; color: {{VALUE}};',
		),
		'condition' => array(
			'type' => 'style4'
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'author_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-testimonials .testimonials_title',
	)
);

$widget->add_control(
	'icon_heading',
	array(
		'label'     => esc_html__('Icons', 'gt3_themes_core'),
		'type'      => Controls_Manager::HEADING,
		'separator' => 'none',
	)
);

$widget->add_control(
	'icon_size',
	array(
		'label'     => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'      => Controls_Manager::SLIDER,
		'default'   => array(
			'size' => 32,
		),
		'range'     => array(
			'px' => array(
				'min'  => 10,
				'max'  => 128,
				'step' => 1,
			),
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-testimonials .icons .social' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
		),
	)
);

$widget->end_controls_section();