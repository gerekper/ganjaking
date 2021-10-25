<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_GoogleMap $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('General', 'gt3_themes_core')
	)
);

$widget->add_control(
	'section_map_height',
	array(
		'label' => esc_html__('Allow the map to cover the height of the section?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'map_height',
	array(
		'label'      => esc_html__('Map height (percentage of width)', 'gt3_themes_core'),
		'type'       => Controls_Manager::SELECT,
		'options'    => array(
			'20'   => esc_html__('20%', 'gt3_themes_core'),
			'25'   => esc_html__('25%', 'gt3_themes_core'),
			'30'   => esc_html__('30%', 'gt3_themes_core'),
			'35'   => esc_html__('35%', 'gt3_themes_core'),
			'40'   => esc_html__('40%', 'gt3_themes_core'),
			'45'   => esc_html__('45%', 'gt3_themes_core'),
			'50'   => esc_html__('50%', 'gt3_themes_core'),
			'55'   => esc_html__('55%', 'gt3_themes_core'),
			'60'   => esc_html__('60%', 'gt3_themes_core'),
			'65'   => esc_html__('65%', 'gt3_themes_core'),
			'70'   => esc_html__('70%', 'gt3_themes_core'),
			'75'   => esc_html__('75%', 'gt3_themes_core'),
			'80'   => esc_html__('80%', 'gt3_themes_core'),
			'85'   => esc_html__('85%', 'gt3_themes_core'),
			'90'   => esc_html__('90%', 'gt3_themes_core'),
			'95'   => esc_html__('95%', 'gt3_themes_core'),
			'100'   => esc_html__('100%', 'gt3_themes_core'),
			'105'   => esc_html__('105%', 'gt3_themes_core'),
			'110'   => esc_html__('110%', 'gt3_themes_core'),
			'115'   => esc_html__('115%', 'gt3_themes_core'),
			'120'   => esc_html__('120%', 'gt3_themes_core'),
			'125'   => esc_html__('125%', 'gt3_themes_core'),
			'130'   => esc_html__('130%', 'gt3_themes_core'),
		),
		'default'    => '30',
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-googlemap .gt3_core_elementor_map' => 'padding-bottom: {{VALUE}}%;',
		),
		'condition' => array(
			'section_map_height' => '',
		),
	)
);

$widget->add_control(
	'custom_coordinates',
	array(
		'label' => esc_html__('Custom Coordinates?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'custom_latitude',
	array(
		'label'   => esc_html__('Custom Latitude', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '',
		'condition' => array(
			'custom_coordinates!' => '',
		),
	)
);

$widget->add_control(
	'custom_longitude',
	array(
		'label'   => esc_html__('Custom Longitude', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '',
		'condition' => array(
			'custom_coordinates!' => '',
		),
	)
);

$widget->add_control(
	'custom_map_marker_info',
	array(
		'label'     => esc_html__('Map Marker Info', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'hide'    => esc_html__('Hide', 'gt3_themes_core'),
			'show'    => esc_html__('Show', 'gt3_themes_core'),
			'default' => esc_html__('Default', 'gt3_themes_core'),
		),
		'default'   => 'default',
	)
);

$widget->add_control(
	'custom_marker_info',
	array(
		'label' => esc_html__('Custom Marker Info?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'custom_marker_info_street_number',
	array(
		'label'   => esc_html__('Custom Street Number', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '',
		'condition' => array(
			'custom_marker_info!' => '',
		),
	)
);

$widget->add_control(
	'custom_marker_info_street',
	array(
		'label'   => esc_html__('Custom Street', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXT,
		'default' => '',
		'condition' => array(
			'custom_marker_info!' => '',
		),
	)
);

$widget->add_control(
	'custom_marker_info_descr',
	array(
		'label'   => esc_html__('Custom Description', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXTAREA,
		'default' => '',
		'condition' => array(
			'custom_marker_info!' => '',
		),
		'description' => esc_html__('The optimal number of characters is 35', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'module_custom_map_style',
	array(
		'label' => esc_html__('Custom Map Style?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'module_custom_map_code',
	array(
		'label'   => esc_html__('JavaScript Style Array', 'gt3_themes_core'),
		'type'    => Controls_Manager::TEXTAREA,
		'default' => '',
		'condition' => array(
			'module_custom_map_style!' => '',
		),
		'description' => esc_html__( 'To change the style of the map, you must insert the JavaScript Style Array code from ', 'gt3_themes_core' ) .' <a href="https://snazzymaps.com/" target="_blank">'.esc_html__('Snazzy Maps', 'gt3_themes_core')
.'</a>',
	)
);

$widget->end_controls_section();
