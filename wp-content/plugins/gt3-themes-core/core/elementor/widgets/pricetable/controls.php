<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Repeater;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PriceTable $widget */







$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__( 'General', 'gt3_themes_core' )
	)
);


$widget->add_control(
	'content_item_title_1',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'content_item_title_2',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);


$widget->add_control(
	'content_item_title_3',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);


$widget->add_control(
	'content_item_title_4',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'content_item_title_5',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);


$widget->add_control(
	'content_item_title_6',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'content_item_title_7',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$widget->add_control(
	'content_item_title_8',
	array(
		'label'       => esc_html__( 'Content Item Title', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);




$repeater = new Repeater();
$repeater->add_control(
	'package_is_active',
	array(
		'label'        => esc_html__( 'Active Package', 'gt3_themes_core' ),
		'type'         => Controls_Manager::SWITCHER,
	)
);
$repeater->add_control(
	'title',
	array(
		'label' => esc_html__('Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'price',
	array(
		'label'       => esc_html__( 'Package Price', 'gt3_themes_core' ),
		'type'        => Controls_Manager::NUMBER,
		'min'         => '0',
	)
);

$repeater->add_control(
	'price_prefix',
	array(
		'label'       => esc_html__( 'Price Prefix ($)', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'price_suffix',
	array(
		'label'       => esc_html__( 'Price Suffix', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__( 'Enter the price suffix for this package. e.g. "/ person"', 'gt3_themes_core' ),
	)
);

$repeater->add_control(
	'content_item_content_1',
	array(
		'label'       => esc_html__( 'Content Item 1', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
		'description' => esc_html__( 'Use "gt3_price_item_color" class to add Item Color to Content Icons', 'gt3_themes_core' ),
		'separator' => 'before',
	)
);

$repeater->add_control(
	'content_item_content_2',
	array(
		'label'       => esc_html__( 'Content Item 2', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'content_item_content_3',
	array(
		'label'       => esc_html__( 'Content Item 3', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'content_item_content_4',
	array(
		'label'       => esc_html__( 'Content Item 4', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'content_item_content_5',
	array(
		'label'       => esc_html__( 'Content Item 5', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'content_item_content_6',
	array(
		'label'       => esc_html__( 'Content Item 6', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'content_item_content_7',
	array(
		'label'       => esc_html__( 'Content Item 7', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
	)
);

$repeater->add_control(
	'content_item_content_8',
	array(
		'label'       => esc_html__( 'Content Item 8', 'gt3_themes_core' ),
		'type'        => Controls_Manager::TEXT,
		'separator' => 'after',
	)
);

$repeater->add_control(
	'button_text',
	array(
		'label' => esc_html__( 'Button Text', 'gt3_themes_core' ),
		'type'  => Controls_Manager::TEXT,
		'default' => esc_html__( 'Get Started', 'gt3_themes_core' ),
	)
);

$repeater->add_control(
	'button_link',
	array(
		'label'   => esc_html__( 'Link to', 'gt3_themes_core' ),
		'type'    => Controls_Manager::URL,
		'default' => array(
			'url'         => '#',
			'is_external' => false,
			'nofollow'    => false,
		),
		'separator' => 'after',
	)
);

$repeater->add_control(
	'label_text',
	array(
		'label' => esc_html__( 'Label Text', 'gt3_themes_core' ),
		'type'  => Controls_Manager::TEXT,
	)
);


$widget->add_control(
	'items',
	array(
		'label'       => esc_html__('Price Tables', 'gt3_themes_core'),
		'type'        => Controls_Manager::REPEATER,
		'default'     => array(
			array(
				'title'   => esc_html__('Price Table #1', 'gt3_themes_core'),
				'price'   => esc_html__('59', 'gt3_themes_core'),
				'price_prefix'  => esc_html__('$', 'gt3_themes_core'),
				'price_suffix'  => esc_html__('99', 'gt3_themes_core'),
				'button_text'	=> esc_html__( 'Get Started', 'gt3_themes_core' ),
			),
			array(
				'title'   => esc_html__('Price Table #1', 'gt3_themes_core'),
				'price'   => esc_html__('99', 'gt3_themes_core'),
				'price_prefix'   => esc_html__('$', 'gt3_themes_core'),
				'price_suffix'   => esc_html__('99', 'gt3_themes_core'),
				'button_text'	=> esc_html__( 'Get Started', 'gt3_themes_core' ),
			),
			array(
				'title'   => esc_html__('Price Table #1', 'gt3_themes_core'),
				'price'   => esc_html__('129', 'gt3_themes_core'),
				'price_prefix'   => esc_html__('$', 'gt3_themes_core'),
				'price_suffix'   => esc_html__('99', 'gt3_themes_core'),
				'button_text'	=> esc_html__( 'Get Started', 'gt3_themes_core' ),
			),
		),
		'fields'      => array_values($repeater->get_controls()),
		'title_field' => '{{{ title }}}',
	)
);





$widget->start_controls_section(
	'price_item_style_section',
	array(
		'label' => esc_html__( 'Item', 'gt3_themes_core' ),
		'tab'   => Controls_Manager::TAB_STYLE
	)
);

$widget->add_control(
    'price_item_align',
    array(
        'label' => __('Image/Icon Alignment', 'gt3_themes_core'),
        'type' => Controls_Manager::CHOOSE,
        'default' => 'center',
        'options' => [
            'left' => [
                'title' => __('Left', 'gt3_themes_core'),
                'icon' => 'fa fa-align-left',
            ],
            'center' => [
                'title' => __('Center', 'gt3_themes_core'),
                'icon' => 'fa fa-align-center',
            ],
            'right' => [
                'title' => __('Right', 'gt3_themes_core'),
                'icon' => 'fa fa-align-right',
            ],
        ],
        'prefix_class' => 'elementor-position-',
        'toggle' => false,
    )
);

$widget->add_control(
	'item_color',
	array(
		'label'     => esc_html__( 'Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array( 
			'{{WRAPPER}} .gt3_price_item_color, {{WRAPPER}} tbody td .fa-check' => 'color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable_header tr th:before' => 'background-color: {{VALUE}};',
			'{{WRAPPER}} .gt3_price_item-cost-elementor' => 'color: {{VALUE}};',
			'{{WRAPPER}} .price_button-elementor a' => 'border-color: {{VALUE}};color: {{VALUE}};',
			'{{WRAPPER}} .price_button-elementor a:hover' => 'background-color: {{VALUE}}; color: #ffffff;',
			'{{WRAPPER}} .gt3_pricetable__active .price_button-elementor a' => 'background-color: {{VALUE}};border-color: {{VALUE}};color: #ffffff;',
			'{{WRAPPER}} .gt3_pricetable__active .price_button-elementor a:hover' => 'color: {{VALUE}};background-color: #ffffff;',
			
		),
		'separator' => 'none',
	)
);

$widget->add_control(
	'item_item_color',
	array(
		'label'     => esc_html__( 'Active Color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .gt3_pricetable__active .gt3_price_item_color, {{WRAPPER}} tbody td.gt3_pricetable__active .fa-check' => 'color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable__active .gt3_price_item_color' => 'color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable_header tr th.gt3_pricetable__active .gt3_price_item_color' => 'color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable_header tr th.gt3_pricetable__active:before' => 'background-color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable__active .gt3_price_item-cost-elementor' => 'color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable__active .price_button-elementor a' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
			'{{WRAPPER}} .gt3_pricetable__active .price_button-elementor a:hover' => 'color: {{VALUE}};',
			
		),
		'separator' => 'none',
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
			'{{WRAPPER}} .gt3_pricetable tbody tr td' => 'color: {{VALUE}};',
		),
		'separator' => 'none',
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'content_typography',
		'selector' => '{{WRAPPER}} .gt3_price_item_body-elementor .items_text-price p',
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
		'size_units' => array( 'px', '%' ),
		'selectors'  => array(
			'{{WRAPPER}} .price_button-elementor > a' => 'border-radius: {{SIZE}}{{UNIT}};',
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
			'{{WRAPPER}} .gt3_pricetable__lavel' => 'color: {{VALUE}};',
		),
	)
);

$widget->end_controls_section();

