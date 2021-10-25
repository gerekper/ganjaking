<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogBoxed $widget */

$widget->start_controls_section(
	'query',
	array(
		'label' => esc_html__('Build Query', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'query',
	array(
		'label'       => esc_html__('Query', 'gt3_themes_core'),
		'type'        => GT3_Core_Elementor_Control_Query::type(),
		'settings'    => array(
			'showCategory'  => true,
			'showUser'      => true,
			'showPost'      => true,
			'post_type'     => $widget->POST_TYPE,
			'post_taxonomy' => $widget->TAXONOMY,
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'general',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);

$widget->add_control(
	'module_type',
	array(
		'label'       => esc_html__('Type', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'type1' => esc_html__('Type 1', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2', 'gt3_themes_core'),
		),
		'default'     => 'type1',
	)
);

$widget->add_control(
	'content_cut',
	array(
		'label'       => esc_html__('Cut off text in blog listing', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, cut off text in blog listing', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'symbol_count',
	array(
		'label'       => esc_html__('Symbol count', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 110,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 500,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'condition'   => array(
			'content_cut!' => '',
		)
	)
);

$widget->add_control(
	'meta_author',
	array(
		'label'       => esc_html__('Show post-meta author?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have author', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'meta_comments',
	array(
		'label'       => esc_html__('Show post-meta comments?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have comments', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_categories',
	array(
		'label'       => esc_html__('Show post-meta categories?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have categories', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_date',
	array(
		'label'       => esc_html__('Show post-meta date?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have date', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'items_per_line',
	array(
		'label'       => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
		),
		'default'     => 3,
		'description' => esc_html__('Select the number of items per line', 'gt3_themes_core'),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .boxed_block_item' => 'width: calc(100%/{{VALUE}});',
		),
		'condition' => array(
			'module_type' => 'type1',
		),
	)
);

$widget->add_control(
	'items_per_line_type2',
	array(
		'label'       => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'1' => '1',
			'2' => '2',
		),
		'default'     => 2,
		'description' => esc_html__('Select the number of items per line', 'gt3_themes_core'),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .boxed_block_item' => 'width: calc(100%/{{VALUE}});',
		),
		'condition' => array(
			'module_type' => 'type2',
		),
	)
);

$widget->add_control(
	'spacing_beetween_items',
	array(
		'label'       => esc_html__('Spacing beetween items', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'0'  => '0px',
			'5'  => '5px',
			'10' => '10px',
			'15' => '15px',
			'20' => '20px',
			'25' => '25px',
			'30' => '30px',
		),
		'default'     => '30',
		'description' => esc_html__('Select spacing beetween items', 'gt3_themes_core'),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .item_wrapper' => 'margin-left:{{VALUE}}px; margin-top:{{VALUE}}px;',
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .gt3_module_blogboxed' => 'margin-left:-{{VALUE}}px; margin-top:-{{VALUE}}px;',
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .items1 .item_wrapper' => 'margin-top:{{VALUE}}px; margin-left:0;',
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .gt3_module_blogboxed.items1' => 'margin-top:-{{VALUE}}px; margin-left:0;',
		)
	)
);

$widget->add_control(
	'meta_position',
	array(
		'label'       => esc_html__('Post meta position', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'before_title' => esc_html__('Before Title', 'gt3_themes_core'),
			'after_title' => esc_html__('After Title', 'gt3_themes_core'),
		),
		'default'     => 'before_title',
		'description' => esc_html__('Select post-meta position', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'post_featured_bg',
	array(
		'label'       => esc_html__('Featured image?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked and post have featured image, post will have featured image', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'featured_bg_opacity',
	[
		'label' => __('Opacity', 'gt3_themes_core'),
		'type' => Controls_Manager::SLIDER,
		'default' => [
			'size' => 0.5,
		],
		'range' => [
			'px' => [
				'max' => 1,
				'min' => 0,
				'step' => 0.01,
			],
		],
		'selectors' => [
			'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .blogboxed_img_block' => 'opacity: {{SIZE}};',
		],
		'condition' => [
			'post_featured_bg!' => '',
		],
		'description' => esc_html__('Featured image opacity', 'gt3_themes_core'),
	]
);

$widget->add_control(
	'image_position',
	array(
		'label'       => esc_html__('Image Position', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'left' => esc_html__('Left', 'gt3_themes_core'),
			'right' => esc_html__('Right', 'gt3_themes_core'),
		),
		'default'     => 'right',
		'condition' => array(
			'module_type' => 'type2',
			'post_featured_bg!' => '',
		),
	)
);

$widget->add_control(
	'image_optimization',
	array(
		'label'       => esc_html__('Image Optimization?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked featured image optimization enable', 'gt3_themes_core'),
		'condition' => array(
			'post_featured_bg!' => '',
		),
	)
);

$widget->add_control(
	'image_optimization_width',
	array(
		'label'       => esc_html__('Image Optimization Width, px', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 1170,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 200,
				'max'  => 1200,
				'step' => 100,
			),
		),
		'size_units'  => array( 'px' ),
		'condition'   => array(
			'image_optimization!' => '',
		)
	)
);

$widget->add_control(
	'border_box',
	array(
		'label'       => esc_html__('Show border of the post?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('The border is not visible if the featured image added to the post', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'post_content',
	array(
		'label'       => esc_html__('Show post content on front?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('The post content is displayed in default state', 'gt3_themes_core'),
		'condition' => array(
			'module_type' => 'type1',
		),
	)
);

$widget->add_control(
	'post_btn_link',
	array(
		'label'       => esc_html__('Show post button?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post will have button', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'post_btn_link_title',
	array(
		'label'     => esc_html__('Post Button Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('Read More', 'gt3_themes_core'),
		'condition' => array(
			'post_btn_link!' => '',
		),
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

	$widget->add_group_control(
		\Elementor\Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'box_shadow',
			'label' => __( 'Box Shadow', 'gt3_themes_core' ),
			'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper',
			'condition' => array(
				'module_type' => 'type1',
			),
		]
	);

	$widget->start_controls_tabs('style_block');

		$widget->start_controls_tab('default_state',
			array(
				'label' => esc_html__('Default', 'gt3_themes_core'),
			)
		);

		$widget->add_control(
			'color_block',
			array(
				'label'       => esc_html__('Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper' => 'color: {{VALUE}};',
				),
				'default' => '#ffffff',
				'condition' => array(
					'module_type' => 'type1',
				),
			)
		);

		$widget->add_control(
			'border_color_block',
			array(
				'label'       => esc_html__('Border Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .blogboxed_content' => 'border-color: {{VALUE}};',
				),
				'default' => '',
				'condition' => array(
					'module_type' => 'type1',
				),
			)
		);

		$widget->add_control(
			'background_color',
			array(
				'label'       => esc_html__('Background color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper' => 'background: {{VALUE}};',
				),
				'default' => '',
				'condition' => array(
					'module_type' => 'type1',
				),
			)
		);

		$widget->add_control(
			'color_block_type2',
			array(
				'label'       => esc_html__('Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .item_wrapper' => 'color: {{VALUE}};',
				),
				'default' => '#232325',
				'condition' => array(
					'module_type' => 'type2',
				),
			)
		);

		$widget->add_control(
			'background_color_type2',
			array(
				'label'       => esc_html__('Background color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .item_wrapper' => 'background: {{VALUE}};',
				),
				'default' => '#ffffff',
				'condition' => array(
					'module_type' => 'type2',
				),
			)
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab('hover_state',
			array(
				'label' => esc_html__('Hover', 'gt3_themes_core'),
			)
		);

		$widget->add_control(
			'color_block_hover',
			array(
				'label'       => esc_html__('Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .item_wrapper:hover' => 'color: {{VALUE}};',
				),
				'default' => '#232325',
			)
		);

		$widget->add_control(
			'border_color_block_hover',
			array(
				'label'       => esc_html__('Border Color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper:hover .blogboxed_content' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'module_type' => 'type1',
				),
				'default' => '',
			)
		);

		/*$widget->add_control(
			'background_color_hover',
			array(
				'label'       => esc_html__('Background color', 'gt3_themes_core'),
				'type'        => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .item_wrapper:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .blogboxed_content:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .blogboxed_content:hover' => 'border-color: {{VALUE}};',
				),
				'default' => '#ffffff',
			)
		);*/

		$widget->add_group_control(
			Elementor\Group_Control_Background::get_type(),
			[
				'name'           => 'background_color_hover',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'image'          => [
						'condition' => [
							'show' => 'never',
						],
					],
					'color'          => [
						'selectors' => [
							'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper:hover, {{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .item_wrapper:hover' => 'background-color: {{VALUE}}; background-image: none;',
							'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .blogboxed_content:hover, {{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .blogboxed_content:hover' => 'border-color: {{VALUE}};',
						],
					],
					'gradient_angle' => [
						'default'   => [
							'unit' => 'deg',
							'size' => 90,
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type1 .item_wrapper:hover, {{WRAPPER}}.elementor-widget-gt3-core-blogboxed .module_type2 .item_wrapper:hover' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
						],
					],
				),
			]
		);

		$widget->end_controls_tab();

	$widget->end_controls_tabs();

$widget->end_controls_section();


