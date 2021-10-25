<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_FlipBox $widget */

$widget->start_controls_section(
	'button',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'flip_style',
	array(
		'label'       => esc_html__('Block Rotation', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'     => 'yes',
		'description' => esc_html__('Allow the rotation of the block?', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'flip_type',
	array(
		'label'     => esc_html__('Flip Style', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'type1' => esc_html__('Type 1', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2', 'gt3_themes_core'),
		),
		'default'   => 'type1',
		'condition' => array(
			'flip_style' => '',
		),
	)
);

$widget->add_group_control(
	\Elementor\Group_Control_Background::get_type(),
	array(
		'label'     => esc_html__('Background', 'gt3_themes_core'),
		'name'      => 'hover_border_type1',
		'types'     => [ 'gradient' ],
		'condition' => array(
			'flip_style' => '',
			'flip_type'  => 'type1',
		),
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-flipbox .gt3_services_box .gt3_services_img_bg:before',
	)
);

$widget->add_control(
	'flip_effect',
	array(
		'label'     => esc_html__('Flip effect', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'left'   => esc_html__('Left', 'gt3_themes_core'),
			'right'  => esc_html__('Right', 'gt3_themes_core'),
			'top'    => esc_html__('Top', 'gt3_themes_core'),
			'bottom' => esc_html__('Bottom', 'gt3_themes_core'),
		),
		'default'   => 'left',
		'condition' => array(
			'flip_style!' => '',
		),
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
			'{{WRAPPER}}.elementor-widget-gt3-core-flipbox .gt3_services_box_content' => 'min-height: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'box_hover_bg',
	array(
		'label'       => esc_html__('Box Background (Hover State)', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-flipbox .gt3_services_box_content.services_box-back' => 'background-color: {{VALUE}};',
		),
		'condition' => array(
			'flip_style!' => '',
		),
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
	'index_number',
	array(
		'label'       => esc_html__('Index Number', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__('Enter text for index number line.', 'gt3_themes_core'),
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
	)
);

$widget->add_control(
	'content_text',
	array(
		'label'       => esc_html__('Content Text', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXTAREA,
		'description' => esc_html__('Enter text.', 'gt3_themes_core'),
		'rows'        => 12,
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
			'{{WRAPPER}}.elementor-widget-gt3-core-flipbox .box_title' => 'color: {{VALUE}};',
		),
		'label_block' => true,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-flipbox .box_title',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'subtitle_style',
	array(
		'label' => esc_html__('Subtitle', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_responsive_control(
	'subtitle_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-flipbox .box_subtitle' => 'color: {{VALUE}};',
		),
		'label_block' => true,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'subtitle_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-flipbox .box_subtitle',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-flipbox .index_number' => 'color: {{VALUE}};',
		),
		'label_block' => true,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'index_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-flipbox .index_number',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'content_style',
	array(
		'label' => esc_html__('Content', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_responsive_control(
	'content_color',
	array(
		'label'       => esc_html__('Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-flipbox .gt3_services_box_content .text_wrap' => 'color: {{VALUE}};',
		),
		'label_block' => true,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'content_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-flipbox .gt3_services_box_content .text_wrap',
	)
);

$widget->end_controls_section();