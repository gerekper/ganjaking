<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Events $widget */

$widget->start_controls_section(
	'section',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'show_type',
	array(
		'label'   => esc_html__('Type', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'type1' => esc_html__('Type 1 (List)', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2 (Grid without Image)', 'gt3_themes_core'),
			'type3' => esc_html__('Type 3 (Grid with Image)', 'gt3_themes_core'),
		),
		'default' => 'type1',
	)
);

$widget->add_control(
	'post_to_show',
	array(
		'label'   => esc_html__('Events to Show','gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1'   => esc_html__('1', 'gt3_themes_core'),
			'2'   => esc_html__('2', 'gt3_themes_core'),
			'3'   => esc_html__('3', 'gt3_themes_core'),
			'4'   => esc_html__('4', 'gt3_themes_core'),
			'5'   => esc_html__('5', 'gt3_themes_core'),
			'6'   => esc_html__('6', 'gt3_themes_core'),
			'7'   => esc_html__('7', 'gt3_themes_core'),
			'8'   => esc_html__('8', 'gt3_themes_core'),
			'9'   => esc_html__('9', 'gt3_themes_core'),
			'10'   => esc_html__('10', 'gt3_themes_core'),
		),
		'default' => '3',
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
		'condition' => array(
			'show_type!' => 'type1',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-wrap .gt3-tribe-item' => 'width: calc(100%/{{VALUE}});',
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
		'condition' => array(
			'show_type!' => 'type1',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-events-wrap .gt3-tribe-item .item_wrapper' => 'padding-left:{{VALUE}}px; padding-top:{{VALUE}}px;',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list' => 'margin-left:-{{VALUE}}px; margin-top:-{{VALUE}}px;',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-all-events-link' => 'padding-left:{{VALUE}}px;',
		),
	)
);

$widget->add_control(
	'order',
	array(
		'label'   => esc_html__('Order', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'ASC'    => esc_html__('ASC', 'gt3_themes_core'),
			'DESC' => esc_html__('DESC', 'gt3_themes_core'),
		),
		'default' => 'ASC',
	)
);

$widget->add_control(
	'featured_events_only',
	array(
		'label'       => esc_html__('Featured Events', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, limit to featured events only', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'show_date',
	array(
		'label'       => esc_html__('Show Date', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, events date is visible', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'show_venue',
	array(
		'label'       => esc_html__('Show Venue', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, events venue is visible', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'show_post_button',
	array(
		'label'       => esc_html__('Show Event Button', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, event button is visible', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'show_more_button',
	array(
		'label'       => esc_html__('Show Veiw More Button', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, view more button is visible', 'gt3_themes_core'),
		'default'     => 'yes',
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
	'day_color',
	array(
		'label'       => esc_html__('Item Date Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type1 .gt3-tribe-events-wrap .gt3-tribe-item > div.gt3-tribe-date > div.gt3-tribe-day' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type3 .gt3-tribe-date' => 'background-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type3 .gt3-tribe-venue > *:before' => 'background-color: {{VALUE}};',
		),
		'condition'   => array(
			'show_type' => array(
				'type1',
				'type3'
			),
		),
	)
);
$widget->add_control(
	'more_button_bg',
	array(
		'label'       => esc_html__('Veiw More Button Background Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-all-events-link a' => 'background-color: {{VALUE}};',
		),
	)
);
/* Tabs */
$widget->start_controls_tabs('style_tabs');

$widget->start_controls_tab('default_tab',
	array(
		'label' => esc_html__('Default', 'gt3_themes_core'),
	)
);
$widget->add_control(
	'item_bg_color',
	array(
		'label'       => esc_html__('Item Background Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type1 .gt3-tribe-item' => 'background-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-item .item_inner_wrap' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'item_text_color',
	array(
		'label'       => esc_html__('Item Text Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type1 .gt3-tribe-item' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-item .item_inner_wrap' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'title_color',
	array(
		'label'       => esc_html__('Item Title Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-events-wrap .gt3-tribe-title h4' => 'color: {{VALUE}};',
		),
		'condition'   => array(
			'show_type' => array(
				'type1',
				'type3'
			),
		),
	)
);
$widget->end_controls_tab();

$widget->start_controls_tab('hover_tab',
	array(
		'label' => esc_html__('Hover', 'gt3_themes_core'),
	)
);
$widget->add_control(
	'item_bg_color_hover',
	array(
		'label'       => esc_html__('Item Background Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type1 .gt3-tribe-item:hover' => 'background-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-item:hover .item_inner_wrap' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'item_text_color_hover',
	array(
		'label'       => esc_html__('Item Text Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list.type1 .gt3-tribe-item:hover' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-item:hover .item_inner_wrap' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'title_color_hover',
	array(
		'label'       => esc_html__('Item Title Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-events .gt3-tribe-events-list .gt3-tribe-events-wrap .gt3-tribe-title:hover h4' => 'color: {{VALUE}};',
		),
		'condition'   => array(
			'show_type' => array(
				'type1',
				'type3'
			),
		),
	)
);
$widget->end_controls_tab();
$widget->end_controls_tabs();
/* Tabs End */

$widget->end_controls_section();
