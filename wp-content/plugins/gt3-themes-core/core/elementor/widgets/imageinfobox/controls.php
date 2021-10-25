<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageInfoBox $widget */

$widget->start_controls_section(
	'button',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'module_type',
	array(
		'label'     => esc_html__('Module Type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'type1' => esc_html__('Type 1', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2', 'gt3_themes_core'),
			'type3' => esc_html__('Type 3', 'gt3_themes_core'),
		),
		'default'   => 'type1',
	)
);

$widget->add_responsive_control(
	'module_height',
	array(
		'label'       => esc_html__('Module Height', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 270,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 160,
				'max'  => 600,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter module height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .gt3_imageinfobox' => 'height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'module_overlay',
	array(
		'label'       => esc_html__('Background Overlay', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'     => 'yes',
		'description' => esc_html__('Allow the overlay of the block?', 'gt3_themes_core'),
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
	)
);

$widget->add_control(
	'link_title',
	array(
		'label' => esc_html__('Link Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'image',
	array(
		'label'   => esc_html__('Image', 'gt3_themes_core'),
		'type'    => Controls_Manager::MEDIA,
		'default' => array(
			'url' => Utils::get_placeholder_image_src(),
		),
	)
);

$widget->add_control(
	'index_number',
	array(
		'label'       => esc_html__('Index Number', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__('Enter text for index number line.', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'title',
	array(
		'label'       => esc_html__('Title', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__('Enter text for title line.', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'subtitle',
	array(
		'label'       => esc_html__('Subtitle', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__('Enter text for subtitle line.', 'gt3_themes_core'),
		'condition'   => array(
			'module_type!' => 'type3',
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'title_style',
	array(
		'label' => esc_html__('Title', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_responsive_control(
	'title_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .box_title' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .gt3_imageinfobox_divider:after' => 'border-top-color: {{VALUE}};',
		),
		'label_block' => true,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .box_title',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'subtitle_style',
	array(
		'label' => esc_html__('Subtitle', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
		'condition'   => array(
			'module_type!' => 'type3',
		),
	)
);

$widget->add_responsive_control(
	'subtitle_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .box_subtitle' => 'color: {{VALUE}};',
		),
		'label_block' => true,
		'condition'   => array(
			'module_type!' => 'type3',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'subtitle_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .box_subtitle',
		'condition'   => array(
			'module_type!' => 'type3',
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'index_style',
	array(
		'label' => esc_html__('Index Number', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_responsive_control(
	'index_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .index_number' => 'color: {{VALUE}};',
		),
		'label_block' => true,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'index_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-imageinfobox .index_number',
	)
);

$widget->end_controls_section();

