<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Stripes $widget */

$widget->start_controls_section(
	'general',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_responsive_control(
	'height',
	array(
		'label'     => esc_html__('Height (% of window height)', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'30'  => esc_html__('30%', 'gt3_themes_core'),
			'35'  => esc_html__('35%', 'gt3_themes_core'),
			'40'  => esc_html__('40%', 'gt3_themes_core'),
			'45'  => esc_html__('45%', 'gt3_themes_core'),
			'50'  => esc_html__('50%', 'gt3_themes_core'),
			'55'  => esc_html__('55%', 'gt3_themes_core'),
			'60'  => esc_html__('60%', 'gt3_themes_core'),
			'65'  => esc_html__('65%', 'gt3_themes_core'),
			'70'  => esc_html__('70%', 'gt3_themes_core'),
			'75'  => esc_html__('75%', 'gt3_themes_core'),
			'80'  => esc_html__('80%', 'gt3_themes_core'),
			'85'  => esc_html__('85%', 'gt3_themes_core'),
			'90'  => esc_html__('90%', 'gt3_themes_core'),
			'95'  => esc_html__('95%', 'gt3_themes_core'),
			'100' => esc_html__('100%', 'gt3_themes_core'),
		),
		'default'   => '55',
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-stripes' => 'height: {{VALUE}}vh;',
		),
	)
);

$widget->add_control(
	'item_align',
	array(
		'label'          => esc_html__('Alignment', 'gt3_themes_core'),
		'type'           => Controls_Manager::CHOOSE,
		'options'        => array(
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
		'default'        => 'left',
		'label_block'    => false,
		'style_transfer' => true,
	)
);

$widget->add_control(
	'stripes_divider',
	array(
		'label'       => esc_html__('Show divider of the stripes?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, stripes will have divider', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'stripes_enable_active_state',
	array(
		'label'       => esc_html__('Enable active state of the stripe?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, stripe info is visible', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'items',
	array(
		'label' => esc_html__('Items', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_CONTENT,
	)
);

$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Items', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(),
		'fields'      => array_values($widget->get_repeater_fields()),
		'title_field' => '{{{ title }}}',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'section_style_stripes',
	array(
		'label' => __('Content', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'overlay_color',
	array(
		'label'     => esc_html__('Background Overlay', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'default'   => 'rgba(37,43,49, 0.1)',
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-stripes .gt3-stripes-list:before' => 'background: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'color_title',
	array(
		'label'     => esc_html__('Title Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3-stripe-title' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'color_description',
	array(
		'label'     => esc_html__('Description Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3-stripe-text' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'divider_color',
	array(
		'label'     => esc_html__('Divider Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3-stripes-list.stripes_divider .gt3-stripe-content:after' => 'border-right-color: {{VALUE}};',
		),
		'default'   => 'rgba(255,255,255, 0.3)',
		'condition' => array(
			'stripes_divider!' => '',
		),
	)
);

$widget->add_control(
	'button_style_header',
	array(
		'label' => esc_html__('Button', 'gt3_themes_core'),
		'type'  => Controls_Manager::HEADING,
	)
);
$widget->start_controls_tabs('style_tabs');
$widget->start_controls_tab('default_tab',
	array(
		'label' => esc_html__('Default', 'gt3_themes_core'),
	)
);
$widget->add_control(
	'button_color',
	array(
		'label'     => esc_html__('Button Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3-stripe-more' => 'color: {{VALUE}};',
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
	'button_color_hover',
	array(
		'label'     => esc_html__('Button Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3-stripe-more:hover' => 'color: {{VALUE}};',
		),
	)
);
$widget->end_controls_tab();
$widget->end_controls_tabs();

$widget->end_controls_section();
