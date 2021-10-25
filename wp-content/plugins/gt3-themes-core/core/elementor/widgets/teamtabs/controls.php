<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_TeamTabs $widget */

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
	'link_post',
	array(
		'label' => esc_html__('Enable Link to Post', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
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

$widget->add_control(
	'title_color',
	array(
		'label'     => esc_html__( 'Title color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .team_title__text' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'job_color',
	array(
		'label'     => esc_html__( 'Member Job color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .team-positions' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'desc_color',
	array(
		'label'     => esc_html__( 'Description color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .member-short-desc' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color',
	array(
		'label'     => esc_html__( 'Icons color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .item_wrapper .member-icon' => 'color: {{VALUE}} !important;',
		),
	)
);


$widget->end_controls_tab();

$widget->start_controls_tab( 'hover_tab',
	array(
		'label' => esc_html__( 'Hover', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'title_color_hover',
	array(
		'label'     => esc_html__( 'Title color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .item_wrapper:hover .team_title__text' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'job_color_hover',
	array(
		'label'     => esc_html__( 'Member Job color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .item_wrapper:hover .team-positions' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'desc_color_hover',
	array(
		'label'     => esc_html__( 'Description color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .item_wrapper:hover .member-short-desc' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color_hover',
	array(
		'label'     => esc_html__( 'Icons color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .item_wrapper:hover .member-icon' => 'color: {{VALUE}} !important;',
		),
	)
);

$widget->end_controls_tab();

$widget->end_controls_section();
