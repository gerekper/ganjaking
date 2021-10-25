<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\GT3_Core_Elementor_Control_Query;

$theme_color = esc_attr( gt3_option( "theme-custom-color" ) );
/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_TeamSearch $widget */

$widget->start_controls_section(
	'general',
	array(
		'label' => esc_html__( 'Main Settings', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'team_names',
	array(
		'label'   => esc_html__( 'Show Names Field?', 'gt3_themes_core' ),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'team_names_placeholder',
	array(
		'label'     => esc_html__( 'Names Placeholder', 'gt3_themes_core' ),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__( 'Team member', 'gt3_themes_core' ),
		'condition' => array(
			'team_names!' => '',
		),
	)
);

$widget->add_control(
	'team_categories',
	array(
		'label'   => esc_html__( 'Show Team Categories Field?', 'gt3_themes_core' ),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'team_category_placeholder',
	array(
		'label'     => esc_html__( 'Categories Placeholder', 'gt3_themes_core' ),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__( 'Category', 'gt3_themes_core' ),
		'condition' => array(
			'team_categories!' => '',
		),
	)
);


$args       = array(
	'name' => 'team_location'
);
$taxonomies = get_taxonomies( $args );

if ( ! empty( $taxonomies ) ) {
	$widget->add_control(
		'team_locations',
		array(
			'label'   => esc_html__( 'Show Team Locations Field?', 'gt3_themes_core' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		)
	);

	$widget->add_control(
		'team_location_placeholder',
		array(
			'label'     => esc_html__( 'Locations Placeholder', 'gt3_themes_core' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Location', 'gt3_themes_core' ),
			'condition' => array(
				'team_locations!' => '',
			),
		)
	);
}

$widget->add_control(
	'team_search_button_text',
	array(
		'label'   => esc_html__( 'Search Button Text', 'gt3_themes_core' ),
		'type'    => Controls_Manager::TEXT,
		'default' => esc_html__( 'Search', 'gt3_themes_core' ),
	)
);
$widget->end_controls_section();


$widget->start_controls_section(
	'btn_setting',
	array(
		'label' => esc_html__( 'Button Settings', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);

$widget->add_responsive_control(
	'padding_size',
	array(
		'label'      => esc_html__('Padding', 'gt3_themes_core'),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}} .submit_box button[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'btn_typography',
		'selector' => '{{WRAPPER}} .submit_box button[type="submit"]',
	)
);

$widget->add_control(
	'btn_border_style',
	array(
		'label'       => esc_html__( 'Button Border Style', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'none'   => esc_html__( 'None', 'gt3_themes_core' ),
			'solid'  => esc_html__( 'Solid', 'gt3_themes_core' ),
			'double' => esc_html__( 'Double', 'gt3_themes_core' ),
			'dotted' => esc_html__( 'Dotted', 'gt3_themes_core' ),
			'dashed' => esc_html__( 'Dashed', 'gt3_themes_core' ),
			'groove' => esc_html__( 'Groove', 'gt3_themes_core' ),
		),
		'description' => esc_html__( 'Select button style.', 'gt3_themes_core' ),
		'default'     => 'solid',
		'selectors'   => array(
			'{{WRAPPER}} .submit_box button[type="submit"]' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'btn_border_rounded',
	array(
		'label'     => esc_html__( 'Border Rounded', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SWITCHER,
		'condition' => array(
			'btn_border_style!' => 'none',
		),
	)
);

$widget->add_control(
	'btn_border_radius',
	array(
		'label'       => esc_html__( 'Button Border Radius', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'none' => esc_html__( 'None', 'gt3_themes_core' ),
			'1px'  => esc_html__( '1px', 'gt3_themes_core' ),
			'2px'  => esc_html__( '2px', 'gt3_themes_core' ),
			'3px'  => esc_html__( '3px', 'gt3_themes_core' ),
			'4px'  => esc_html__( '4px', 'gt3_themes_core' ),
			'5px'  => esc_html__( '5px', 'gt3_themes_core' ),
			'10px' => esc_html__( '10px', 'gt3_themes_core' ),
			'15px' => esc_html__( '15px', 'gt3_themes_core' ),
			'20px' => esc_html__( '20px', 'gt3_themes_core' ),
			'25px' => esc_html__( '25px', 'gt3_themes_core' ),
			'30px' => esc_html__( '30px', 'gt3_themes_core' ),
			'35px' => esc_html__( '20px', 'gt3_themes_core' ),
			'40px' => esc_html__( '25px', 'gt3_themes_core' ),
			'45px' => esc_html__( '30px', 'gt3_themes_core' ),
			'50px' => esc_html__( '30px', 'gt3_themes_core' ),
		),
		'description' => esc_html__( 'Select button radius.', 'gt3_themes_core' ),
		'default'     => 'none',
		'selectors'   => array(
			'{{WRAPPER}} .submit_box button[type="submit"],
			{{WRAPPER}} .select2-selection--single' => 'border-radius: {{VALUE}};',
		),
		'condition'   => array(
			'btn_border_style!'   => 'none',
			'btn_border_rounded!' => '',
		),
	)
);

$widget->add_control(
	'btn_border_width',
	array(
		'label'       => esc_html__( 'Button Border Width', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'0'    => esc_html__( 'None', 'gt3_themes_core' ),
			'1px'  => esc_html__( '1px', 'gt3_themes_core' ),
			'2px'  => esc_html__( '2px', 'gt3_themes_core' ),
			'3px'  => esc_html__( '3px', 'gt3_themes_core' ),
			'4px'  => esc_html__( '4px', 'gt3_themes_core' ),
			'5px'  => esc_html__( '5px', 'gt3_themes_core' ),
			'6px'  => esc_html__( '6px', 'gt3_themes_core' ),
			'7px'  => esc_html__( '7px', 'gt3_themes_core' ),
			'8px'  => esc_html__( '8px', 'gt3_themes_core' ),
			'9px'  => esc_html__( '9px', 'gt3_themes_core' ),
			'10px' => esc_html__( '10px', 'gt3_themes_core' ),
		),
		'description' => esc_html__( 'Select button border width.', 'gt3_themes_core' ),
		'default'     => '1px',
		'selectors'   => array(
			'{{WRAPPER}} .submit_box button[type="submit"]' => 'border-width: {{VALUE}};',
		),
		'condition'   => array(
			'btn_border_style!' => 'none',
		),
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
	'btn_text_color',
	array(
		'label'     => esc_html__( 'Button Text color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'default'   => '#fff',
		'selectors' => array(
			'{{WRAPPER}} .submit_box button[type="submit"]' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'btn_bg_color',
	array(
		'label'     => esc_html__( 'Button Background color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'default'   => $theme_color,
		'selectors' => array(
			'{{WRAPPER}} .submit_box button[type="submit"]' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'btn_border_color',
	array(
		'label'     => esc_html__( 'Border color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'default'   => $theme_color,
		'condition' => array(
			'btn_border_style!' => 'none',
		),
		'selectors' => array(
			'{{WRAPPER}} .submit_box button[type="submit"]' => 'border-color: {{VALUE}};',
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
	'hover_btn_text_color',
	array(
		'label'     => esc_html__( 'Button Text color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'default'   => $theme_color,
		'selectors' => array(
			'{{WRAPPER}} .submit_box button[type="submit"]:hover' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'hover_btn_bg_color',
	array(
		'label'     => esc_html__( 'Button Background color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .submit_box button[type="submit"]:hover' => 'background-color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'btn_hover_border_color',
	array(
		'label'     => esc_html__( 'Border color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'default'   => $theme_color,
		'condition' => array(
			'btn_border_style!' => 'none',
		),
		'selectors' => array(
			'{{WRAPPER}} .submit_box button[type="submit"]:hover' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->end_controls_tab();

$widget->end_controls_section();
