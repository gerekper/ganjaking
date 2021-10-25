<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PortfolioCarousel $widget */

$widget->start_controls_section(
	'query',
	array(
		'label' => esc_html__('Query', 'gt3_themes_core'),
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
	'section',
	array(
		'label' => esc_html__('General', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'nav',
	array(
		'label'   => esc_html__('Navigation', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'none'   => esc_html__('None', 'gt3_themes_core'),
			'arrows' => esc_html__('Arrows', 'gt3_themes_core'),
			'dots'   => esc_html__('Dots', 'gt3_themes_core'),
		),
		'default' => 'arrows',
	)
);

$widget->add_control(
	'items_per_line',
	array(
		'label'   => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1'   => esc_html__('1', 'gt3_themes_core'),
			'2'   => esc_html__('2', 'gt3_themes_core'),
			'3'   => esc_html__('3', 'gt3_themes_core'),
			'4'   => esc_html__('4', 'gt3_themes_core'),
		),
		'default' => '1',
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
	'center_mode',
	array(
		'label' => esc_html__('Center Mode', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_responsive_control(
	'space',
	array(
		'label'     => esc_html__('Space Between Items', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'0'     => '0',
			'1px'   => '1px',
			'2px'   => '2px',
			'3px'   => '3px',
			'4px'   => '4px',
			'5px'   => '5px',
			'10px'  => '10px',
			'15px'  => '15px',
			'20px'  => '20px',
			'25px'  => '25px',
			'30px'  => '30px',
			'35px'  => '35px',
			'40px'  => '40px',
			'50px'  => '50px',
			'60px'  => '60px',
		),
		'default'   => '60px',
		'selectors' => array(
			'{{WRAPPER}} .items_list' => 'margin-right:-{{VALUE}};',
			'{{WRAPPER}} .portfolio_item' => 'padding-right:{{VALUE}};',
			'{{WRAPPER}} .portfolio_carousel_wrapper .slick-arrow.slick-next' => '
    margin-right: {{VALUE}};',
		)
	)
);


$widget->add_control(
	'show_title',
	array(
		'label' => esc_html__('Show Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'show_category',
	array(
		'label' => esc_html__('Show Category', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'show_text',
	array(
		'label' => esc_html__('Show Text', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'portfolio_btn_link',
	array(
		'label'   => esc_html__( 'Show "Read More" button?', 'gt3_themes_core' ),
		'type'    => Controls_Manager::SWITCHER,
		'default' => ''
	)
);

$widget->add_control(
	'portfolio_btn_link_title',
	array(
		'label'     => esc_html__('"Read More" Button Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('Read More', 'gt3_themes_core'),
		'condition' => array(
			'portfolio_btn_link!' => '',
		),
	)
);

$widget->add_control(
	'item_align',
	array(
		'label'   => esc_html__('Alignment', 'gt3_themes_core'),
		'type'    => Controls_Manager::CHOOSE,
		'options' => array(
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
		'label_block' => false,
		'prefix_class' => 'text_align-',
	)
);

$widget->end_controls_section();


