<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Tabs $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'tabs_type',
	array(
		'label'   => esc_html__('Type','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'horizontal' => esc_html__('Horizontal', 'gt3_themes_core'),
			'vertical' => esc_html__('Vertical', 'gt3_themes_core'),
		),
		'default' => 'horizontal'
	)
);

$widget->add_control(
	'tabs_aligment_h',
	array(
		'label'   => esc_html__('Aligment','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'left'   => esc_html__('Left', 'gt3_themes_core'),
			'center'   => esc_html__('Center', 'gt3_themes_core'),
			'right'   => esc_html__('Right', 'gt3_themes_core'),
		),
		'default' => 'center',
		'condition' => array(
			'tabs_type' => 'horizontal'
		),
	)
);

$widget->add_control(
	'tabs_aligment_v',
	array(
		'label'   => esc_html__('Aligment','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'left_pos'   => esc_html__('Left', 'gt3_themes_core'),
			'right_pos'   => esc_html__('Right', 'gt3_themes_core'),
		),
		'default' => 'left_pos',
		'condition' => array(
			'tabs_type' => 'vertical'
		),
	)
);

$widget->add_control(
	'active_tab',
	array(
		'label'   => esc_html__('Active tab','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1'   => esc_html__('1', 'gt3_themes_core'),
			'2'   => esc_html__('2', 'gt3_themes_core'),
			'3'   => esc_html__('3', 'gt3_themes_core'),
			'4'   => esc_html__('4', 'gt3_themes_core'),
			'5'   => esc_html__('5', 'gt3_themes_core'),
			'6'   => esc_html__('6', 'gt3_themes_core'),
			'7'   => esc_html__('7', 'gt3_themes_core'),
		),
		'default' => '1',
		'description' => esc_html__('If you select a number greater than the total number of tabs, the first tab will be active.', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Items', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(
			array(
				'title'   => esc_html__('Tab #1', 'gt3_themes_core'),
				'content' => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
				'icon' => '',
			),
			array(
				'title'   => esc_html__('Tab #2', 'gt3_themes_core'),
				'content' => esc_html__('Curabitur sodales ligula in libero. Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique.', 'gt3_themes_core'),
				'icon' => '',
			),
			array(
				'title'   => esc_html__('Tab #3', 'gt3_themes_core'),
				'content' => esc_html__('Mauris ipsum. Nulla metus metus, ullamcorper vel, tincidunt sed, euismod in, nibh. Quisque volutpat condimentum velit. Class aptent taciti sociosqu ad litora torquent conubia nostra.', 'gt3_themes_core'),
				'icon' => '',
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

$widget->start_controls_tabs('style_tabs');
$widget->start_controls_tab(
	'style_tab',
	array(
		'label' => esc_html__('Tab','gt3_themes_core'),
	)
);
$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'tab_typography',
		'label'    => esc_html__('Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a',
	)
);
$widget->add_control(
	'tab_padding',
	array(
		'label'      => esc_html__('Padding','gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'default'     => array(
			'top' => '10',
			'right' => '10',
			'bottom' => '10',
			'left' => '10',
		),
	)
);
$widget->add_control(
	'tab_margin',
	array(
		'label'      => esc_html__('Margin','gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'default'     => array(
			'top' => '0',
			'right' => '0',
			'bottom' => '0',
			'left' => '0',
		),
	)
);
$widget->add_control(
	'tab_border',
	array(
		'label'       => esc_html__('Border', 'gt3_themes_core'),
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
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'border-style: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_border_color',
	array(
		'label'       => esc_html__('Border Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'tab_border!' => '',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'border-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_border_color_hover',
	array(
		'label'       => esc_html__('Border Color (Hover state)', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'tab_border!' => '',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a:hover' => 'border-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_border_color_active',
	array(
		'label'       => esc_html__('Border Color (Active state)', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'tab_border!' => '',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a' => 'border-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a:hover' => 'border-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_border_width',
	array(
		'label'      => esc_html__('Border Width', 'gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'border-top-width: {{TOP}}{{UNIT}}; border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width: {{LEFT}}{{UNIT}};',
		),
		'default'     => array(
			'top' => '1',
			'right' => '1',
			'bottom' => '1',
			'left' => '1',
		),
		'condition'   => array(
			'tab_border!' => '',
		),
		'label_block' => true,
	)
);
$widget->add_control(
	'tab_border_radius',
	array(
		'label'       => esc_html__('Border Radius', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 3,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 2,
			),
			'%'  => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'label_block' => true,
		'size_units'  => array( 'px', '%' ),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);
$widget->add_control(
	'tab_color',
	array(
		'label'   => esc_html__('Text Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_background',
	array(
		'label'   => esc_html__('Background','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_color_hover',
	array(
		'label'   => esc_html__('Text Color (Hover state)','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a:hover' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_background_hover',
	array(
		'label'   => esc_html__('Background (Hover state)','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a:hover' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_color_active',
	array(
		'label'   => esc_html__('Text Color (Active state)','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a:hover' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'tab_background_active',
	array(
		'label'   => esc_html__('Background (Active state)','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a' => 'background-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a:hover' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->end_controls_tab();

$widget->start_controls_tab(
	'style_icon',
	array(
		'label' => esc_html__('Icon','gt3_themes_core'),
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
$widget->add_control(
	'icon_size',
	array(
		'label'       => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 16,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 10,
				'max'  => 50,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a .icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
		),
	)
);
$widget->add_control(
	'icon_color',
	array(
		'label'   => esc_html__('Icon Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a .icon' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color_hover',
	array(
		'label'   => esc_html__('Icon Color (Hover state)','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li a:hover .icon' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color_active',
	array(
		'label'   => esc_html__('Icon Color (Active state)','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a .icon' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .gt3_tabs_nav li.ui-tabs-active a:hover .icon' => 'color: {{VALUE}};',
		),
	)
);
$widget->end_controls_tab();

$widget->start_controls_tab(
	'style_container',
	array(
		'label' => esc_html__('Container','gt3_themes_core'),
	)
);
$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'container_typography',
		'label'    => esc_html__('Typography','gt3_themes_core'),
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel',
	)
);
$widget->add_control(
	'container_padding',
	array(
		'label'      => esc_html__('Padding', 'gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'default'     => array(
			'top' => '0',
			'right' => '0',
			'bottom' => '0',
			'left' => '0',
		),
	)
);
$widget->add_control(
	'container_margin',
	array(
		'label'      => esc_html__('Margin', 'gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		),
		'default'     => array(
			'top' => '0',
			'right' => '0',
			'bottom' => '0',
			'left' => '0',
		),
	)
);
$widget->add_control(
	'container_border',
	array(
		'label'       => esc_html__('Border', 'gt3_themes_core'),
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
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'border-style: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'container_border_color',
	array(
		'label'       => esc_html__('Border Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition'   => array(
			'container_border!' => '',
		),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'border-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'container_border_width',
	array(
		'label'      => esc_html__('Border Width', 'gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'border-top-width: {{TOP}}{{UNIT}}; border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width: {{LEFT}}{{UNIT}};',
		),
		'default'     => array(
			'top' => '1',
			'right' => '1',
			'bottom' => '1',
			'left' => '1',
		),
		'condition'   => array(
			'container_border!' => '',
		),
		'label_block' => true,
	)
);
$widget->add_control(
	'container_border_radius',
	array(
		'label'       => esc_html__('Border Radius', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 3,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 2,
			),
			'%'  => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'label_block' => true,
		'size_units'  => array( 'px', '%' ),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);
$widget->add_control(
	'container_color',
	array(
		'label'   => esc_html__('Text Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'container_background',
	array(
		'label'   => esc_html__('Background','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-tabs .ui-tabs-panel' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->end_controls_tab();

$widget->end_controls_tabs();

$widget->end_controls_section();