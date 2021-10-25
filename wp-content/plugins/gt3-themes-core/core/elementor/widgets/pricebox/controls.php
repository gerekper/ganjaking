<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PriceBox $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__( 'General', 'gt3_themes_core' )
	)
);

$widget->add_control(
	'header_img',
	array(
		'label'       => esc_html__( 'Header Image', 'gt3_themes_core' ),
		'type'        => Controls_Manager::MEDIA,
		'default'     => array(
			'url' => Utils::get_placeholder_image_src(),
		),
		'description' => esc_html__( 'Select header image', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'pre_title',
	array(
		'label' => esc_html__( 'Pre Title', 'gt3_themes_core' ),
		'type'  => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'title',
	array(
		'label'       => esc_html__( 'Package Name / Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__( "Enter title of price block", 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'header_img_2',
	array(
		'label'       => esc_html__( 'Header Image Before The Price', 'gt3_themes_core' ),
		'type'        => Controls_Manager::MEDIA,
		'default'     => array(
			'url' => Utils::get_placeholder_image_src(),
		),
		'description' => esc_html__( 'Select header image', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'hover_effect_block',
	array(
		'label'        => esc_html__( 'Activate hover effect', 'gt3_themes_core' ),
		'type'         => Controls_Manager::SWITCHER,
		'prefix_class' => 'hover_effect-',
	)
);

$widget->add_responsive_control(
	'price_block_height',
	array(
		'label'     => esc_html__( 'Height of Price block', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SLIDER,
		'default'   => array(
			'size' => 540,
		),
		'range'     => array(
			'px' => array(
				'min' => 10,
				'max' => 700,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} .elementor-widget-container' => 'min-height: {{SIZE}}px;',
		),
		'condition' => array(
			'hover_effect_block!' => '',
		)
	)
);

$widget->add_control(
	'package_is_active',
	array(
		'label'        => esc_html__( 'Active Package', 'gt3_themes_core' ),
		'type'         => Controls_Manager::SWITCHER,
		'prefix_class' => 'active-package-',
	)
);

$widget->add_control(
	'add_label',
	array(
		'label' => esc_html__( 'Add Label', 'gt3_themes_core' ),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'label_text',
	array(
		'label'     => esc_html__( 'Label Text', 'gt3_themes_core' ),
		'type'      => Controls_Manager::TEXT,
		'condition' => array(
			'add_label!' => '',
		)
	)
);

$widget->add_control(
	'price_prefix',
	array(
		'label'       => esc_html__( 'Price Prefix ', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__( 'Enter the price prefix for this package. e.g. "$"', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'price',
	array(
		'label'       => esc_html__( 'Package Price', 'gt3_themes_core' ),
		'type'        => Controls_Manager::NUMBER,
		'min'         => '0',
		'description' => esc_html__( 'Enter the price for this package. e.g. "157"', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'price_suffix',
	array(
		'label'       => esc_html__( 'Price Suffix', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__( 'Enter the price suffix for this package. e.g. "/ person"', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'content',
	array(
		'label' => esc_html__( 'Price Field', 'gt3_themes_core' ),
		'type'  => Controls_Manager::WYSIWYG,
	)
);

$widget->add_control(
	'button_text',
	array(
		'label' => esc_html__( 'Button Text', 'gt3_themes_core' ),
		'type'  => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'button_link',
	array(
		'label'   => esc_html__( 'Link to', 'gt3_themes_core' ),
		'type'    => Controls_Manager::URL,
		'default' => array(
			'url'         => '#',
			'is_external' => false,
			'nofollow'    => false,
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'image_style_section',
	array(
		'label' => esc_html__( 'Image', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'image_size',
	array(
		'label'     => esc_html__( 'Width', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SLIDER,
		'default'   => array(
			'size' => 60,
		),
		'range'     => array(
			'px' => array(
				'min' => 10,
				'max' => 200,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper img' => 'width: {{SIZE}}px;',
		),
	)
);

$widget->add_control(
	'image_border',
	array(
		'label'     => esc_html__( 'Border', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SELECT,
		'default'   => '',
		'options'   => array(
			''       => esc_html__( 'None', 'gt3_themes_core' ),
			'dotted' => esc_html__( 'Dotted', 'gt3_themes_core' ),
			'dashed' => esc_html__( 'Dashed', 'gt3_themes_core' ),
			'solid'  => esc_html__( 'Solid', 'gt3_themes_core' ),
			'double' => esc_html__( 'Double', 'gt3_themes_core' ),
		),
		'selectors' => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'image_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 15,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 1,
			),
			'%'  => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'condition'  => array(
			'image_border!' => '',
		),
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'image_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'image_border!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'image_border_width',
	array(
		'label'      => esc_html__( 'Border Width', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 1,
				'max'  => 10,
				'step' => 1,
			),
		),
		'condition'  => array(
			'image_border!' => '',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price' => 'border-width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->end_controls_section();



$widget->start_controls_section(
	'pre_title_style_section',
	array(
		'label' => esc_html__( 'Pre Title', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'pre_title_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .price_item_description-elementor' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'pre_title_typography',
		'selector' => '{{WRAPPER}} .price_item_description-elementor',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'text_style_section',
	array(
		'label' => esc_html__( 'Title', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .price_item_title-elementor h3' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}} .price_item_title-elementor h3',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'image_style_section_2',
	array(
		'label' => esc_html__( 'Image', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'image_size_2',
	array(
		'label'     => esc_html__( 'Width', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SLIDER,
		'default'   => array(
			'size' => 60,
		),
		'range'     => array(
			'px' => array(
				'min' => 10,
				'max' => 200,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price_2 img' => 'width: {{SIZE}}px;',
		),
	)
);

$widget->add_control(
	'image_border_2',
	array(
		'label'     => esc_html__( 'Border', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SELECT,
		'default'   => '',
		'options'   => array(
			''       => esc_html__( 'None', 'gt3_themes_core' ),
			'dotted' => esc_html__( 'Dotted', 'gt3_themes_core' ),
			'dashed' => esc_html__( 'Dashed', 'gt3_themes_core' ),
			'solid'  => esc_html__( 'Solid', 'gt3_themes_core' ),
			'double' => esc_html__( 'Double', 'gt3_themes_core' ),
		),
		'selectors' => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price_2' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'image_border_radius_2',
	array(
		'label'      => esc_html__( 'Border Radius', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 15,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 0,
				'max'  => 40,
				'step' => 1,
			),
			'%'  => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'condition'  => array(
			'image_border_2!' => '',
		),
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price_2' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'image_border_color_2',
	array(
		'label'     => esc_html__( 'Border Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'image_border_2!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price_2' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'image_border_width_2',
	array(
		'label'      => esc_html__( 'Border Width', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 1,
				'max'  => 10,
				'step' => 1,
			),
		),
		'condition'  => array(
			'image_border_2!' => '',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}} .gt3_item_cost_wrapper .img_wrapper-price_2' => 'border-width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'price_item_prefix_style_section',
	array(
		'label' => esc_html__( 'Price Prefix', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'price_item_prefix_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .price_item_prefix-elementor' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'price_item_prefix_typography',
		'selector' => '{{WRAPPER}} .price_item_prefix-elementor',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'price_item_suffix_style_section',
	array(
		'label' => esc_html__( 'Price Suffix', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'price_item_suffix_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .price_item_suffix-elementor' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'price_item_suffix_typography',
		'selector' => '{{WRAPPER}} .price_item_suffix-elementor',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'price_style_section',
	array(
		'label' => esc_html__( 'Price', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'price_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3_price_item-cost-elementor' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'price_typography',
		'selector' => '{{WRAPPER}} .gt3_price_item-cost-elementor',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'content_style_section',
	array(
		'label' => esc_html__( 'Price Field', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
	'content_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3_price_item_body-elementor .items_text-price' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'content_typography',
		'selector' => '{{WRAPPER}} .gt3_price_item_body-elementor .items_text-price',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'button_style_section',
	array(
		'label' => esc_html__( 'Button', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'button_typography',
		'selector' => '{{WRAPPER}} .price_button-elementor .shortcode_button',
	)
);

$widget->add_control(
	'button_border_en',
	array(
		'label' => esc_html__( 'Button Border', 'gt3_themes_core' ),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'button_border',
	array(
		'label'     => esc_html__( 'Border Type', 'gt3_themes_core' ),
		'type'      => Controls_Manager::SELECT,
		'default'   => '',
		'options'   => array(
			''       => esc_html__( 'None', 'gt3_themes_core' ),
			'dotted' => esc_html__( 'Dotted', 'gt3_themes_core' ),
			'dashed' => esc_html__( 'Dashed', 'gt3_themes_core' ),
			'solid'  => esc_html__( 'Solid', 'gt3_themes_core' ),
			'double' => esc_html__( 'Double', 'gt3_themes_core' ),
		),
		'condition' => array(
			'button_border_en!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}} .price_button-elementor .bordered' => 'border-style: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'button_border_width',
	array(
		'label'      => esc_html__( 'Border Width', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 1,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 1,
				'max'  => 10,
				'step' => 1,
			),
		),
		'condition'  => array(
			'button_border_en!' => '',
			'button_border!'    => '',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}} .price_button-elementor .bordered' => 'border-width: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_responsive_control(
	'button_border_radius',
	array(
		'label'      => esc_html__( 'Border Radius', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 15,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 2,
			),
			'%'  => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
		),
		'condition'  => array(
			'button_border_en!' => '',
			'button_border!'    => '',
		),
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} .price_button-elementor .bordered' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->add_control(
	'button_icon_en',
	array(
		'label' => esc_html__( 'Icon', 'gt3_themes_core' ),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'button_icon_position',
	array(
		'label'     => esc_html__( 'Icon Position' ),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'left'  => esc_html__( 'Left', 'gt3_themes_core' ),
			'right' => esc_html__( 'Right', 'gt3_themes_core' ),
		),
		'default'   => 'left',
		'condition' => array(
			'button_icon_en!' => '',
		),
	)
);

$widget->add_control(
	'button_icon',
	array(
		'label'     => esc_html__( 'Button Icon', 'gt3_themes_core' ),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'button_icon_en!' => '',
		),
	)
);

$widget->add_responsive_control(
	'icon_font_size',
	array(
		'label'      => esc_html__( 'Icon font-size', 'gt3_themes_core' ),
		'type'       => Controls_Manager::SLIDER,
		'default'    => array(
			'size' => 20,
			'unit' => 'px',
		),
		'range'      => array(
			'px' => array(
				'min'  => 1,
				'max'  => 200,
				'step' => 1,
			),
		),
		'condition'  => array(
			'button_icon_en!' => '',
		),
		'size_units' => array( 'px' ),
		'selectors'  => array(
			'{{WRAPPER}} .price-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
		),
	)
);

$widget->start_controls_tabs( 'style_tabs' );
$widget->start_controls_tab( 'digit_tab',
	array(
		'label' => esc_html__( 'Default', 'gt3_themes_core' ),
	)
);

$widget->add_control(
	'btn_color',
	array(
		'label'       => esc_html__( 'Button color', 'gt3_themes_core' ),
		'type'        => Controls_Manager::COLOR,
		'description' => esc_html__( 'Select custom color for button', 'gt3_themes_core' ),
		'selectors'   => array(
			'{{WRAPPER}} .price_button-elementor a' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'btn_bg_color',
	array(
		'label'       => esc_html__( 'Button Background Color', 'gt3_themes_core' ),
		'type'        => Controls_Manager::COLOR,
		'description' => esc_html__( 'Select custom color for button', 'gt3_themes_core' ),
		'selectors'   => array(
			'{{WRAPPER}} .price_button-elementor a' => 'background-color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'button_border_color',
	array(
		'label'     => esc_html__( 'Border Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'button_border_en!' => '',
			'button_border!'    => '',
		),
		'selectors' => array(
			'{{WRAPPER}} .price_button-elementor .bordered' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->end_controls_tab();

$widget->start_controls_tab( 'description_tab',
	array(
		'label' => esc_html__( 'Hover', 'gt3_themes_core' ),
	)
);


$widget->add_control(
	'btn_color_hover',
	array(
		'label'       => esc_html__( 'Button color', 'gt3_themes_core' ),
		'type'        => Controls_Manager::COLOR,
		'description' => esc_html__( 'Select custom color for button', 'gt3_themes_core' ),
		'selectors'   => array(
			'{{WRAPPER}} .price_button-elementor a:hover' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'btn_bg_color_hover',
	array(
		'label'       => esc_html__( 'Button Background Color', 'gt3_themes_core' ),
		'type'        => Controls_Manager::COLOR,
		'description' => esc_html__( 'Select custom color for button', 'gt3_themes_core' ),
		'selectors'   => array(
			'{{WRAPPER}} .price_button-elementor a:hover' => 'background-color: {{VALUE}};',
		),
	)
);

$widget->add_responsive_control(
	'button_border_color_hover',
	array(
		'label'     => esc_html__( 'Border Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'button_border_en!' => '',
			'button_border!'    => '',
		),
		'selectors' => array(
			'{{WRAPPER}} .price_button-elementor a.bordered:hover' => 'border-color: {{VALUE}};',
		),
	)
);

$widget->end_controls_tab();

$widget->end_controls_tabs();

$widget->end_controls_section();

$widget->start_controls_section(
	'label_style_section',
	array(
		'label' => esc_html__( 'Label', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'label_typography',
		'selector' => '{{WRAPPER}} .label_text',
	)
);

$widget->add_control(
	'label_color',
	array(
		'label'     => esc_html__( 'Label Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .label_text span' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'label_bgcolor',
	array(
		'label'     => esc_html__( 'Label Background Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .label_text span' => 'background-color: {{VALUE}};',
		),
	)
);

$widget->end_controls_section();

