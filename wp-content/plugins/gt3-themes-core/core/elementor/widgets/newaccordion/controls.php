<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Controls_Manager;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_NewAccordion $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__( 'Basic', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'items',
	[
		'label'       => esc_html__( 'Items', 'gt3_themes_core' ),
		'type'        => Controls_Manager::REPEATER,
		'default'     => [
			[
				'title'   => esc_html__( 'Accordion #1', 'gt3_themes_core' ),
				'content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core' ),
			],
			[
				'title'   => esc_html__( 'Accordion #2', 'gt3_themes_core' ),
				'content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core' ),
			],
			[
				'title'   => esc_html__( 'Accordion #3', 'gt3_themes_core' ),
				'content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core' ),
			],
			[
				'title'   => esc_html__( 'Accordion #4', 'gt3_themes_core' ),
				'content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core' ),
			],
		],
		'fields'      => array_values( $widget->get_repeater_fields() ),
		'title_field' => '{{{ title }}}',
	]
);

$icons = array( 'gt3_default' => __( 'Default', 'gt3_themes_core' ) );
array_merge( $icons, \Elementor\Control_Icon::get_icons() );
$widget->add_control(
	'icon',
	[
		'label'     => __( 'Icon', 'gt3_themes_core' ),
		'type'      => Controls_Manager::ICON,
		'default'   => 'gt3_default',
		'separator' => 'before',
		'options'   => $icons,
	]
);

$widget->add_control(
	'icon_active',
	[
		'label'     => __( 'Active Icon', 'gt3_themes_core' ),
		'type'      => Controls_Manager::ICON,
		'default'   => 'gt3_default',
		'condition' => [
			'icon!' => '',
		],
		'options'   => $icons,
	]
);

$widget->end_controls_section();


$widget->start_controls_section(
	'section_title_style',
	[
		'label' => __( 'Accordion', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE,
	]
);

$widget->add_responsive_control(
	'title_border_width',
	[
		'label'      => __( 'Title Border Width', 'gt3_themes_core' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => [ 'px' ],
		'selectors'  => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title' => 'border-top: {{TOP}}{{UNIT}} solid; border-right: {{RIGHT}}{{UNIT}} solid; border-bottom: {{BOTTOM}}{{UNIT}} solid; border-left: {{LEFT}}{{UNIT}} solid;',
		],
	]
);

$widget->add_responsive_control(
	'content_border_width',
	[
		'label'      => __( 'Content Border Width', 'gt3_themes_core' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => [ 'px' ],
		'selectors'  => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_content' => 'border-top: {{TOP}}{{UNIT}} solid; border-right: {{RIGHT}}{{UNIT}} solid; border-bottom: {{BOTTOM}}{{UNIT}} solid; border-left: {{LEFT}}{{UNIT}} solid;',
		],
	]
);

$widget->add_control(
	'border_color',
	[
		'label'     => __( 'Border Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title'   => 'border-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_content' => 'border-color: {{VALUE}};',
		],
	]
);

$widget->add_control(
	'active_border_color',
	[
		'label'     => __( 'Active Border Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title.ui-accordion-header-active'    => 'border-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_content.ui-accordion-content-active' => 'border-color: {{VALUE}};',
		],
	]
);

$widget->add_responsive_control(
	'spacing_items',
	[
		'label'     => __( 'Spacing between items', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SLIDER,
		'range'     => [
			'px' => [
				'min' => - 10,
				'max' => 50,
			],
		],
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_content + .item_title' => 'margin-top: {{SIZE}}{{UNIT}};',
		],
	]
);

$widget->end_controls_section();


$widget->start_controls_section(
	'section_toggle_style_title',
	[
		'label' => __( 'Title', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE,
	]
);

$widget->add_control(
	'title_background',
	[
		'label'     => __( 'Background', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title' => 'background-color: {{VALUE}};',
		],
	]
);

$widget->add_control(
	'title_background_active',
	[
		'label'     => __( 'Active Background', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title.ui-accordion-header-active' => 'background-color: {{VALUE}};',
		],
	]
);

$widget->add_control(
	'title_color',
	[
		'label'     => __( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title' => 'color: {{VALUE}}',
		),
		'scheme'    => [
			'type'  => Scheme_Color::get_type(),
			'value' => Scheme_Color::COLOR_1,
		],
	]
);

$widget->add_control(
	'tab_active_color',
	[
		'label'     => __( 'Active Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title.ui-accordion-header-active' => 'color: {{VALUE}};',
		],
		'scheme'    => [
			'type'  => Scheme_Color::get_type(),
			'value' => Scheme_Color::COLOR_4,
		],
	]
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	[
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .newaccordion_wrapper .item_title',
		'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
	]
);

$widget->add_responsive_control(
	'title_padding',
	[
		'label'      => __( 'Padding', 'gt3_themes_core' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => [ 'px', 'em', '%' ],
		'selectors'  => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .newaccordion_wrapper .item_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		],
	]
);

$widget->end_controls_section();

$widget->start_controls_section(
	'section_toggle_style_icon',
	[
		'label'     => __( 'Icon', 'gt3_themes_core' ),
		'tab'       => Controls_Manager::TAB_STYLE,
		'condition' => [
			'icon!' => '',
		],
	]
);

$widget->add_control(
	'icon_align',
	[
		'label'       => __( 'Alignment', 'gt3_themes_core' ),
		'type'        => Controls_Manager::CHOOSE,
		'options'     => [
			'left'  => [
				'title' => __( 'Start', 'gt3_themes_core' ),
				'icon'  => 'eicon-h-align-left',
			],
			'right' => [
				'title' => __( 'End', 'gt3_themes_core' ),
				'icon'  => 'eicon-h-align-right',
			],
		],
		'default'     => is_rtl() ? 'right' : 'left',
		'toggle'      => false,
		'label_block' => false,
		'condition'   => [
			'icon!' => '',
		],
	]
);

$widget->add_control(
	'icon_color',
	[
		'label'     => __( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title .fa:before' => 'color: {{VALUE}};',
		],
		'condition' => [
			'icon!' => '',
		],
	]
);

$widget->add_control(
	'icon_active_color',
	[
		'label'     => __( 'Active Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .item_title.ui-accordion-header-active .fa:before' => 'color: {{VALUE}};',
		],
		'condition' => [
			'icon!' => '',
		],
	]
);

$widget->add_responsive_control(
	'icon_space',
	[
		'label'     => __( 'Spacing', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SLIDER,
		'range'     => [
			'px' => [
				'min' => 0,
				'max' => 100,
			],
		],
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .elementor-accordion-icon.elementor-accordion-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .elementor-accordion-icon.elementor-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
		],
		'condition' => [
			'icon!' => '',
		],
	]
);

$widget->end_controls_section();

$widget->start_controls_section(
	'section_toggle_style_content',
	[
		'label'     => __( 'Content', 'gt3_themes_core' ),
		'tab'       => Controls_Manager::TAB_STYLE,
		'condition' => [
			'icon!' => '',
		],
	]
);

$widget->add_control(
	'content_background_color',
	[
		'label'     => __( 'Background', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .newaccordion_wrapper .item_content' => 'background-color: {{VALUE}}',
		),
	]
);

$widget->add_control(
	'content_color',
	[
		'label'     => __( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .newaccordion_wrapper .item_content' => 'color: {{VALUE}}',
		),
		'scheme'    => [
			'type'  => Scheme_Color::get_type(),
			'value' => Scheme_Color::COLOR_3,
		],
	]
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	[
		'name'     => 'content_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .newaccordion_wrapper .item_content',
		'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
	]
);

$widget->add_responsive_control(
	'content_padding',
	[
		'label'      => __( 'Padding', 'gt3_themes_core' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => [ 'px', 'em', '%' ],
		'selectors'  => [
			'{{WRAPPER}}.elementor-widget-gt3-core-newaccordion .newaccordion_wrapper .item_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		],
	]
);

$widget->end_controls_section();