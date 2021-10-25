<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_AdvancedTabs $widget */

$widget->start_controls_section(
	'tab_section',
	array(
		'tab'   => Elementor\Controls_Manager::TAB_LAYOUT,
		'label' => __('Tabs', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'gt3_tabs_type',
	array(
		'label'     => esc_html__('Type', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::SELECT,
		'options'   => array(
			'horizontal' => esc_html__('Horizontal', 'gt3_themes_core'),
			'vertical'   => esc_html__('Vertical', 'gt3_themes_core'),
		),
		'default'   => 'horizontal',
		'separator' => 'before',
	)
);

$widget->add_control(
	'gt3_tabs_active',
	array(
		'label'   => esc_html__('Active Tab', 'gt3_themes_core'),
		'type'    => Elementor\Controls_Manager::SELECT,
		'options' => array(
			'0' => esc_html__('1', 'gt3_themes_core'),
			'1' => esc_html__('2', 'gt3_themes_core'),
			'2' => esc_html__('3', 'gt3_themes_core'),
			'3' => esc_html__('4', 'gt3_themes_core'),
			'4' => esc_html__('5', 'gt3_themes_core'),
			'5' => esc_html__('6', 'gt3_themes_core'),
			'6' => esc_html__('7', 'gt3_themes_core'),
		),
		'default' => '0',
	)
);

$widget->add_control(
	'gt3_tabs_size',
	array(
		'label'        => esc_html__('Tab Size', 'gt3_themes_core'),
		'type'         => Elementor\Controls_Manager::SELECT,
		'options'      => array(
			'mini'   => esc_html__('Mini', 'gt3_themes_core'),
			'small'  => esc_html__('Small', 'gt3_themes_core'),
			'normal' => esc_html__('Normal', 'gt3_themes_core'),
			'large'  => esc_html__('Large', 'gt3_themes_core'),
		),
		'default'      => 'normal',
		'prefix_class' => 'gt3_tabs_size-',
	)

);

$widget->add_control(
	'gt3_tabs_alignment',
	array(
		'label'        => esc_html__('Tab Alignment', 'gt3_themes_core'),
		'type'         => Elementor\Controls_Manager::SELECT,
		'options'      => array(
			'left'    => esc_html__('Left', 'gt3_themes_core'),
			'center'  => esc_html__('Center', 'gt3_themes_core'),
			'right'   => esc_html__('Right', 'gt3_themes_core'),
			'stretch' => esc_html__('Stretch', 'gt3_themes_core'),
		),
		'default'      => 'center',
		'prefix_class' => 'gt3_tabs_alignment-',
		'condition'    => array(
			'gt3_tabs_type' => 'horizontal'
		),
	)
);

$widget->add_responsive_control(
	'gt3_tabs_border_radius',
	array(
		'label'      => esc_html__('Tab Border Radius', 'gt3_themes_core'),
		'type'       => Elementor\Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 5,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),

		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li > a' => 'border-radius: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} .gt3_advanced_tabs_nav'          => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'gt3_tabs_space',
	array(
		'label'        => esc_html__('Space Between Tab', 'gt3_themes_core'),
		'type'         => Elementor\Controls_Manager::SELECT,
		'options'      => array(
			'0'  => esc_html__('0', 'gt3_themes_core'),
			'10' => esc_html__('10', 'gt3_themes_core'),
			'20' => esc_html__('20', 'gt3_themes_core'),
			'30' => esc_html__('30', 'gt3_themes_core'),
			'40' => esc_html__('40', 'gt3_themes_core'),
		),
		'default'      => '0',
		'prefix_class' => 'gt3_tabs_space-',
	)
);

$widget->add_control(
	'gt3_tabs_marker',
	array(
		'label'        => esc_html__('Show Triangle Marker', 'gt3_themes_core'),
		'type'         => Elementor\Controls_Manager::SWITCHER,
		'prefix_class' => 'gt3_tabs_marker-',
		'separator'    => 'after',
	)
);

$widget->start_controls_tabs('tabs_colors');
$widget->start_controls_tab('tabs_colors_normal_state',
	[
		'label' => __('Normal', 'gt3_themes_core'),
	]
);

$widget->add_control(
	'tabs_title_color',
	[
		'label'     => __('Title Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li > a' => 'color: {{VALUE}};',
		],
	]
);
$widget->add_control(
	'tabs_icon_color',
	[
		'label'     => __('Icon Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li > a i' => 'color: {{VALUE}};',
		],
	]
);

$widget->add_group_control(
	Elementor\Group_Control_Background::get_type(),
	[
		'name'           => 'tabs_background',
		'types'          => array( 'classic', 'gradient' ),
		'fields_options' => array(
			'image'          => [
				'condition' => [
					'show' => 'never',
				],
			],
			'color'          => [
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li > a, {{WRAPPER}} .gt3_advanced_tabs_nav' => 'background-color: {{VALUE}}; background-image: none;',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li > a:after'         => 'color: {{VALUE}}; background-image: none;',
				],
			],
			'gradient_angle' => [
				'default'   => [
					'unit' => 'deg',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li > a, {{WRAPPER}} .gt3_advanced_tabs_nav' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li > a:after'         => 'background-color: transparent; background-image: none; opacity: 0;',
				],
			],
		),
	]
);

$widget->end_controls_tab();

$widget->start_controls_tab('tabs_colors_hover_state',
	[
		'label' => __('Active', 'gt3_themes_core'),
	]
);

$widget->add_control(
	'tabs_hover_title_color',
	[
		'label'     => __('Title Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active > a, {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li > a' => 'color: {{VALUE}};',
		],
	]
);
$widget->add_control(
	'tabs_hover_icon_color',
	[
		'label'     => __('Icon Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active > a .gt3_tabs_nav__icon,{{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li > a .gt3_tabs_nav__icon' => 'color: {{VALUE}};',
		],
	]
);

$widget->add_group_control(
	Elementor\Group_Control_Background::get_type(),
	[
		'name'           => 'tabs_hover_background',
		'types'          => array( 'classic', 'gradient' ),
		'fields_options' => array(
			'image'          => [
				'condition' => [
					'show' => 'never',
				],
			],
			'color'          => [
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active > a, {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li > a'                                                     => 'background-color: {{VALUE}}; background-image: none;',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li.ui-tabs-active > a:after, {{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li > a:after' => 'color: {{VALUE}}; background-image: none;',
				],
			],
			'gradient_angle' => [
				'default'   => [
					'unit' => 'deg',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active > a, {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li > a'                                                     => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li.ui-tabs-active > a:after, {{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li > a:after' => 'background-color: transparent; background-image: none; opacity: 0;',
				],
			],
		),
	]
);

$widget->end_controls_tab();

$widget->end_controls_tabs();

$tabs            = new \WP_Query(
	array(
		'post_type'      => 'elementor_library',
		'posts_per_page' => '-1',
		'meta_query'     => array_merge(
			array(
				'relation' => 'AND',
			),
			array(
				array(
					'key'   => '_elementor_template_type',
					'value' => 'gt3-tabs',
				),
			)
		),
	)
);
$tabs_array = array();
foreach($tabs->posts as $_post) {
	$tabs_array[$_post->ID] = $_post->post_title;
}

// Repeater
$repeater = new \Elementor\Repeater();

$repeater->add_control(
	'tab_title',
	[
		'label'       => __('Title', 'gt3_themes_core'),
		'type'        => Elementor\Controls_Manager::TEXT,
		'default'     => __('Tab Title', 'elementor'),
		'dynamic'     => [
			'active' => true,
		],
		'label_block' => true,
	]
);



$repeater->add_control(
	'template_id',
	array(
		'label' => 'Template',
		'label_block' => true,
		'type' => Elementor\Controls_Manager::SELECT2,
		'options'   => $tabs_array,
	)
);

$repeater->add_control(
	'type',
	[
		'label'   => __('Choose Icon Type', 'gt3_themes_core'),
		'type'    => Elementor\Controls_Manager::CHOOSE,
		'default' => 'icon',
		'options' => [
			'empty' => [
				'title' => __('No', 'gt3_themes_core'),
				'icon'  => 'fa fa-times',
			],
			'icon'  => [
				'title' => __('Icon', 'gt3_themes_core'),
				'icon'  => 'fa fa-star',
			],
			'image' => [
				'title' => __('Image', 'gt3_themes_core'),
				'icon'  => 'fa fa-image',
			],

		],
		'default' => 'empty',
	]
);

$repeater->add_control(
	'icon',
	[
		'label'     => __('Icon', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::ICON,
		'default'   => 'fa fa-star',
		'condition' => [
			'type' => 'icon',
		],
	]
);

$repeater->add_control(
	'gt3_tabs_icon_size',
	array(
		'label'     => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::SELECT,
		'options'   => array(
			'mini'   => esc_html__('Mini', 'gt3_themes_core'),
			'small'  => esc_html__('Small', 'gt3_themes_core'),
			'normal' => esc_html__('Normal', 'gt3_themes_core'),
			'large'  => esc_html__('Large', 'gt3_themes_core'),
			'custom' => esc_html__('Custom', 'gt3_themes_core'),
		),
		'default'   => 'normal',
		'condition' => array(
			'type' => array( 'icon', 'image' ),
		),
	)
);

$repeater->add_responsive_control(
	'gt3_tabs_icon_custom_size',
	array(
		'label'      => esc_html__('Custom Icon Size', 'gt3_themes_core'),
		'type'       => Elementor\Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 32,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 8,
				'max'  => 250,
				'step' => 1,
			),

		),
		'size_units' => array( 'px' ),
		'condition'  => array(
			'gt3_tabs_icon_size' => 'custom',
			'type'               => array( 'icon', 'image' ),
		),
		'selectors'  => array(
			'{{WRAPPER}} {{CURRENT_ITEM}} .gt3_tabs_nav__image_container' => 'max-width: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} {{CURRENT_ITEM}} .gt3_tabs_nav__icon'            => 'font-size: {{SIZE}}{{UNIT}};',
		),
	)
);

$repeater->start_controls_tabs('tab_colors');
$repeater->start_controls_tab('tab_colors_normal_state',
	[
		'label' => __('Normal', 'gt3_themes_core'),
	]
);

$repeater->add_control(
	'image',
	[
		'label'     => __('Choose Image', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::MEDIA,
		'dynamic'   => [
			'active' => true,
		],
		'default'   => [
			'url' => Elementor\Utils::get_placeholder_image_src(),
		],
		'condition' => [
			'type' => 'image',
		],
	]
);

$repeater->add_control(
	'tab_title_color',
	[
		'label'     => __('Title Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a' => 'color: {{VALUE}};',
		],
	]
);
$repeater->add_control(
	'tab_icon_color',
	[
		'label'     => __('Icon Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a .gt3_tabs_nav__icon' => 'color: {{VALUE}};',
		],
		'condition' => [
			'type' => 'icon',
		],
	]
);

$repeater->add_group_control(
	Elementor\Group_Control_Background::get_type(),
	[
		'name'           => 'tab_background',
		'types'          => array( 'classic', 'gradient' ),
		'fields_options' => array(
			'image'          => [
				'condition' => [
					'show' => 'never',
				],
			],
			'color'          => [
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a'                           => 'background-color: {{VALUE}}; background-image: none;',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a:after' => 'color: {{VALUE}}; background-image: none;',
				],
			],
			'gradient_angle' => [
				'default'   => [
					'unit' => 'deg',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a'                           => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a:after' => 'background-color: transparent; background-image: none; opacity: 0;',
				],
			],
		),
	]
);

$repeater->end_controls_tab();

$repeater->start_controls_tab('tab_colors_hover_state',
	[
		'label' => __('Active', 'gt3_themes_core'),
	]
);

$repeater->add_control(
	'image_hover',
	[
		'label'     => __('Choose Image', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::MEDIA,
		'dynamic'   => [
			'active' => true,
		],
		'condition' => [
			'type' => 'image',
		],
	]
);

$repeater->add_control(
	'tab_hover_title_color',
	[
		'label'     => __('Title Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active{{CURRENT_ITEM}} > a, {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a' => 'color: {{VALUE}};',
		],
	]
);
$repeater->add_control(
	'tab_hover_icon_color',
	[
		'label'     => __('Icon Color', 'gt3_themes_core'),
		'type'      => Elementor\Controls_Manager::COLOR,
		'scheme'    => [
			'type'  => Elementor\Scheme_Color::get_type(),
			'value' => Elementor\Scheme_Color::COLOR_1,
		],
		'default'   => '',
		'selectors' => [
			'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active{{CURRENT_ITEM}} > a .gt3_tabs_nav__icon,
                    {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a .gt3_tabs_nav__icon' => 'color: {{VALUE}};',
		],
		'condition' => [
			'type' => 'icon',
		],
	]
);

$repeater->add_group_control(
	Elementor\Group_Control_Background::get_type(),
	[
		'name'           => 'tab_hover_background',
		'types'          => array( 'classic', 'gradient' ),
		'fields_options' => array(
			'image'          => [
				'condition' => [
					'show' => 'never',
				],
			],
			'color'          => [
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active{{CURRENT_ITEM}} > a, {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a'                                                     => 'background-color: {{VALUE}}; background-image: none;',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav > li.ui-tabs-active{{CURRENT_ITEM}} > a:after, {{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a:after' => 'color: {{VALUE}}; background-image: none;',
				],
			],
			'gradient_angle' => [
				'default'   => [
					'unit' => 'deg',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .gt3_advanced_tabs_nav > li.ui-tabs-active{{CURRENT_ITEM}} > a, {{WRAPPER}} .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					'{{WRAPPER}}.gt3_tabs_marker-yes .gt3_advanced_tabs_nav_wrapper.ui-state-active .gt3_advanced_tabs_nav > li{{CURRENT_ITEM}} > a:after'                                                 => 'background-color: transparent; background-image: none; opacity: 0;',
				],
			],
		),
	]
);

$repeater->end_controls_tab();

$repeater->end_controls_tabs();

$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Tabs Title', 'gt3_themes_core'),
		'type'        => Elementor\Controls_Manager::REPEATER,
		'default'     => array(
			array(
				'tab_title' => esc_html__('Tab Title', 'gt3_themes_core'),
				'content'   => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
				'icon'      => '',
			),
		),
		'separator'   => 'before',
		'fields'      => $repeater->get_controls(),
		'title_field' => '{{{ tab_title }}}',
	)
);
$widget->end_controls_section();
