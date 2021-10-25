<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team $widget */

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
		'label' => esc_html__('Main Settings', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);

$widget->add_control(
	'type',
	array(
		'label'   => esc_html__('Type', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'type1' => esc_html__('Type 1', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2', 'gt3_themes_core'),
			'type3' => esc_html__('Type 3', 'gt3_themes_core'),
			'type4' => esc_html__('Type 4', 'gt3_themes_core'),
			'type5' => esc_html__('Type 5', 'gt3_themes_core'),
		),
		'default' => 'type1',
	)
);

$widget->add_control(
	'use_filter',
	array(
		'label' => esc_html__('Use Filter?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_responsive_control(
	'filter_align',
	array(
		'label'     => esc_html__('Alignment', 'gt3_themes_core'),
		'type'      => Controls_Manager::CHOOSE,
		'options'   => array(
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
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-team .isotope-filter' => 'text-align: {{VALUE}};',
		),
		'default'   => '',
		'condition' => array(
			'use_filter!' => '',
		)
	)
);

$widget->add_control(
	'link_post',
	array(
		'label' => esc_html__('Enable Link to Post', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'posts_per_line',
	array(
		'label'   => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
		),
		'default' => '1',
	)
);

$widget->add_control(
	'grid_gap',
	array(
		'label'     => esc_html__('Grid Gap', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'0'  => '0',
			'1'  => '1px',
			'2'  => '2px',
			'3'  => '3px',
			'4'  => '4px',
			'5'  => '5px',
			'10' => '10px',
			'15' => '15px',
			'20' => '20px',
			'25' => '25px',
			'30' => '30px',
			'35' => '35px',
		),
		'default'   => '0',
		'prefix_class' => 'grid_gap-',
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-team .item_list'        => 'margin-right:-{{VALUE}}px; margin-bottom:-{{VALUE}}px;',
			'{{WRAPPER}}.elementor-widget-gt3-core-team .item-team-member' => 'padding-right: {{VALUE}}px; padding-bottom:{{VALUE}}px;'
		)
	)
);

$widget->add_control(
	'show_title',
	array(
		'label' => esc_html__('Show Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'show_position',
	array(
		'label' => esc_html__('Show Position', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'show_description',
	array(
		'label' => esc_html__('Show Description', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'show_social',
	array(
		'label' => esc_html__('Show Social', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'show_fields_type5',
	array(
		'label' => esc_html__('Show Fields', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'condition' => array(
			'type' => 'type5',
		)
	)
);

$widget->add_control(
	'custom_item_height',
	array(
		'label' => esc_html__('Enable Custom Item Height?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'condition' => array(
			'type!' => 'type5',
		)
	)
);

$widget->add_responsive_control(
	'item_img_height',
	array(
		'label'       => esc_html__('Item Image Height', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 440,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 300,
				'max'  => 600,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter item image height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-team .team_image_cover' => 'height: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'custom_item_height!' => '',
			'type!' => 'type5',
		)
	)
);


$widget->add_control(
	'pagination_en',
	array(
		'label'       => esc_html__('Pagination', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, pagination will be enabled', 'gt3_themes_core'),
		'separator' => 'before',
	)
);

$widget->add_control(
	'show_view_all',
	array(
		'label' => esc_html__('Show "See More" Button', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'condition' => array(
			'pagination_en' => '',
		)
	)
);

$widget->add_control(
	'load_items',
	array(
		'label'     => esc_html__('See Items', 'gt3_themes_core'),
		'type'      => Controls_Manager::NUMBER,
		'min'       => 1,
		'step'      => 1,
		'default'   => '4',
		'condition' => array(
			'pagination_en' => '',
			'show_view_all!' => ''
		)
	)
);

$widget->add_control(
	'button_type',
	array(
		'label'     => esc_html__('Button Type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'default' => esc_html__('Default', 'gt3_themes_core'),
			'icon'    => esc_html__('With Icon', 'gt3_themes_core'),
		),
		'default'   => 'default',
		'condition' => array(
			'pagination_en' => '',
			'show_view_all!' => '',
		)
	)
);

$widget->add_control(
	'button_icon',
	array(
		'label'     => esc_html__('Button Icon', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'pagination_en' => '',
			'button_type'    => 'icon',
			'show_view_all!' => '',
		)
	)
);

$widget->add_control(
	'button_title',
	array(
		'label'     => esc_html__('Button Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('See More', 'gt3_themes_core'),
		'condition' => array(
			'pagination_en' => '',
			'show_view_all!' => '',
		)
	)
);

$widget->add_responsive_control(
	'icon_space',
	array(
		'label'     => esc_html__('Icon Spacing', 'gt3_themes_core'),
		'type'      => Controls_Manager::SLIDER,
		'default'   => array(
			'size' => 16,
		),
		'range'     => array(
			'px' => array(
				'min' => 0,
				'max' => 100,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} .team_view_more_link .elementor_btn_icon_container' => 'padding-left: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'pagination_en' => '',
			'button_type!'   => 'none',
			'show_view_all!' => '',
		)
	)
);


$widget->end_controls_section();

$widget->start_controls_section(
	'style_section',
	array(
		'label' => esc_html__( 'Style', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->start_controls_tabs( 'style_tabs' );

$widget->start_controls_tab( 'default_tab',
	array(
		'label' => esc_html__( 'Default', 'gt3_themes_core' ),
	)
);

$widget->add_group_control(
	Group_Control_Background::get_type(),
	array(
		'name' => 'background',
		'types' => array(
			'classic',
			'gradient'
		),
		'selector' => '{{WRAPPER}} .module_team.type5 .item_wrapper:before',
		'fields_options' => array(
			'background' => array(
				'frontend_available' => true,
			),
		),
		'condition' => array(
			'type' => 'type5',
		)
	)
);

$widget->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name' => 'box_shadow',
		'selector' => '{{WRAPPER}} .module_team.type5 .item_wrapper',
		'condition' => array(
			'type' => 'type5',
		)
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'     => esc_html__( 'Title color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .team_title__text' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'job_color',
	array(
		'label'     => esc_html__( 'Member Job color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .team-positions' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'desc_color',
	array(
		'label'     => esc_html__( 'Short Description color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .member-short-desc' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color',
	array(
		'label'     => esc_html__( 'Icons color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper .member-icon' => 'color: {{VALUE}} !important;',
		),
	)
);
$widget->add_control(
	'link_color',
	array(
		'label'     => esc_html__( 'Link color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .module_team.type5 .item_wrapper .team_link' => 'color: {{VALUE}};',
		),
		'condition' => array(
			'type' => 'type5',
			'link_post!' => '',
		)
	)
);
$widget->add_control(
	'fields_color_type5',
	array(
		'label'     => esc_html__( 'Info color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .module_team.type5 .item_wrapper .gt3_single_team_info' => 'color: {{VALUE}};',
		),
		'condition' => array(
			'type' => 'type5',
			'show_fields_type5!' => '',
		)
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab( 'hover_tab',
	array(
		'label' => esc_html__( 'Hover', 'gt3_themes_core' ),
	)
);

$widget->add_group_control(
	Group_Control_Background::get_type(),
	array(
		'name' => 'background_hover',
		'types' => array(
			'classic',
			'gradient'
		),
		'selector' => '{{WRAPPER}} .module_team.type5 .item_wrapper:after',
		'fields_options' => array(
			'background' => array(
				'frontend_available' => true,
			),
		),
		'condition' => array(
			'type' => 'type5',
		)
	)
);

$widget->add_group_control(
	Group_Control_Box_Shadow::get_type(),
	array(
		'name' => 'box_shadow_hover',
		'selector' => '{{WRAPPER}} .module_team.type5 .item_wrapper:hover',
		'condition' => array(
			'type' => 'type5',
		)
	)
);

$widget->add_control(
	'title_color_hover',
	array(
		'label'     => esc_html__( 'Title color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .team_title__text' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'job_color_hover',
	array(
		'label'     => esc_html__( 'Member Job color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .team-positions' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'desc_color_hover',
	array(
		'label'     => esc_html__( 'Short Description color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .member-short-desc' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color_hover',
	array(
		'label'     => esc_html__( 'Icons color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .member-icon' => 'color: {{VALUE}} !important;',
		),
	)
);
$widget->add_control(
	'link_color_hover',
	array(
		'label'     => esc_html__( 'Link color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .module_team.type5 .item_wrapper:hover .team_link' => 'color: {{VALUE}};',
		),
		'condition' => array(
			'type' => 'type5',
			'link_post!' => '',
		)
	)
);
$widget->add_control(
	'fields_color_hover_type5',
	array(
		'label'     => esc_html__( 'Info color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .module_team.type5 .item_wrapper:hover .gt3_single_team_info' => 'color: {{VALUE}};',
		),
		'condition' => array(
			'type' => 'type5',
			'show_fields_type5!' => '',
		)
	)
);

$widget->end_controls_tab();

$widget->end_controls_section();
