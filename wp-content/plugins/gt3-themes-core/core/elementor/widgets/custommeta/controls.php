<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_CustomMeta $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'select_layout',
	array(
		'label'   => esc_html__('Select Layout','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'horizontal' => esc_html__('Horizontal', 'gt3_themes_core'),
			'vertical' => esc_html__('Vertical', 'gt3_themes_core'),
		),
		'default' => 'vertical'
	)
);

$widget->add_control(
	'select_alignment',
	array(
		'label'   => esc_html__('Select Alignment','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'align_left' => esc_html__('Left', 'gt3_themes_core'),
			'align_center' => esc_html__('Center', 'gt3_themes_core'),
			'align_right' => esc_html__('Right', 'gt3_themes_core'),
		),
		'default' => 'align_left'
	)
);

$widget->add_control(
	'spacing_between_items',
	array(
		'label'       => esc_html__('Spacing between items', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 25,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter spacing in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-custommeta .vertical .gt3_meta_values_item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-custommeta .horizontal .gt3_meta_values_item' => 'padding-right: {{SIZE}}{{UNIT}};',
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
				'custom_meta_label'   => '',
				'custom_meta_type' => 'type_custom',
				'custom_meta_value' => '',
				'custom_meta_icon' => '',
				'custom_colors' => '',
				'custom_label_color' => '',
				'custom_value_color' => '',
				'custom_icon_color' => '',
			),
		),
		'fields'      => array_values($widget->get_repeater_fields()),
		'title_field' => '{{{custom_meta_label}}}',
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

	$widget->start_controls_tabs('style_items');

		$widget->start_controls_tab(
			'style_label',
			array(
				'label' => esc_html__('Label','gt3_themes_core'),
			)
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__('Label Typography','gt3_themes_core'),
				'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-custommeta .gt3_meta_label_title',
			)
		);

		$widget->add_responsive_control(
			'label_color',
			array(
				'label'       => esc_html__('Label Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-custommeta .gt3_meta_label_title' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'style_value',
			array(
				'label' => esc_html__('Value','gt3_themes_core'),
			)
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'label'    => esc_html__('Value Typography','gt3_themes_core'),
				'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-custommeta .gt3_meta_value',
			)
		);

		$widget->add_responsive_control(
			'value_color',
			array(
				'label'       => esc_html__('Value Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-custommeta .gt3_meta_value' => 'color: {{VALUE}};',
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
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units'  => array( 'px' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-custommeta .custom_meta_icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$widget->add_responsive_control(
			'icon_color',
			array(
				'label'       => esc_html__('Icon Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-custommeta .custom_meta_icon' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->end_controls_tab();

	$widget->end_controls_tabs();

$widget->end_controls_section();
