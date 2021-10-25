<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Frontend;
use Elementor\Utils;
use Elementor\GT3_Core_Elementor_Control_Gallery;

/**
 * @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageCarousel $widget
 */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('General', 'gt3_themes_core')
	)
);
$widget->add_control(
	'slides',
	array(
		'type' => GT3_Core_Elementor_Control_Gallery::type(),
	)
);

$widget->add_control(
	'slider_style',
	array(
		'label'   => esc_html__('Slider Style', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'default' => '',
		'options' => array(
			''            => esc_html__('Regular', 'gt3_themes_core'),
			'iphone_view' => esc_html__('iPhone View', 'gt3_themes_core'),
		),
	)
);

$widget->add_control(
	'img_size',
	array(
		'label'       => esc_html__('Image size', 'gt3_themes_core'),
		'type'        => Controls_Manager::TEXT,
		'default'     => 'thumbnail',
		'description' => esc_html__('Enter image size. Example: thumbnail, medium, large or full. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'crop_img_size_for_iphone',
	array(
		'label'       => esc_html__('Enable crop image for recommended size?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('Recommended image size is 314x670 pixels.', 'gt3_themes_core'),
		'default' => 'yes',
		'condition'  => array(
			'slider_style' => 'iphone_view',
		),
	)
);

$widget->add_control(
	'margin_between_slides',
	array(
		'label'       => esc_html__('Margin between slides', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'default'     => '0',
		'options'     => array(
			'0'  => __('0px', 'gt3_themes_core'),
			'5'  => __('5px', 'gt3_themes_core'),
			'10' => __('10px', 'gt3_themes_core'),
			'15' => __('15px', 'gt3_themes_core'),
			'20' => __('20px', 'gt3_themes_core'),
			'25' => __('25px', 'gt3_themes_core'),
			'30' => __('30px', 'gt3_themes_core'),
			'35' => __('35px', 'gt3_themes_core'),
			'40' => __('40px', 'gt3_themes_core'),
			'45' => __('45px', 'gt3_themes_core'),
			'50' => __('50px', 'gt3_themes_core'),
			'55' => __('55px', 'gt3_themes_core'),
			'60' => __('60px', 'gt3_themes_core'),
		),
		'description' => __('Select margin between slides.', 'gt3_themes_core'),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'settings',
	array(
		'label' => esc_html__('Settings', 'gt3_themes_core')
	)
);

$widget->add_control(
	'autoplay_carousel',
	array(
		'label'   => esc_html__('Autoplay carousel', 'gt3_themes_core'),
		'type'    => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->add_control(
	'auto_play_time',
	array(
		'label'     => esc_html__('Autoplay time', 'gt3_themes_core'),
		'type'      => Controls_Manager::NUMBER,
		'default'   => 3000,
		'min'       => '0',
		'step'      => 100,
		'condition' => array(
			'autoplay_carousel!' => ''
		),
	)
);

$widget->add_control(
	'use_pagination_carousel',
	array(
		'label' => esc_html__('Hide Pagination control', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default' => 'yes',
	)
);

$widget->end_controls_section();