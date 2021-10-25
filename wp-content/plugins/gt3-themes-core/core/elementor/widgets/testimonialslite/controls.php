<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_TestimonialsLite $widget */

$widget->start_controls_section(
	'general',
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
		),
		'default' => '1',
		'prefix_class' => 'gt3-testimonials-perline',
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
			'70px'  => '70px',
		),
		'default'   => '30px',
		'selectors' => array(
			'{{WRAPPER}} .testimonials_rotator' => 'margin: 0 {{VALUE}} 0 {{VALUE}};margin-right:calc(-{{VALUE}}/2);margin-left:calc(-{{VALUE}}/2);',
			'{{WRAPPER}} .testimonials_item.slick-slide' => 'padding: 0 {{VALUE}} 0 {{VALUE}};padding-right:calc({{VALUE}}/2);padding-left:calc({{VALUE}}/2);',
		)
	)
);

$widget->add_control(
	'author_position',
	array(
		'label'   => esc_html__('Author Info Position', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'before'   => esc_html__('Before Content', 'gt3_themes_core'),
			'after'   => esc_html__('After Content', 'gt3_themes_core'),
			'around'   => esc_html__('Around Content', 'gt3_themes_core'),
		),
		'default' => 'after',
	)
);

$widget->add_control(
	'round_imgs',
	array(
		'label' => esc_html__('Circular Author Image?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'image_position',
	array(
		'label'   => esc_html__('Image Position', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'aside'   => esc_html__('Aside', 'gt3_themes_core'),
			'top'   => esc_html__('Top', 'gt3_themes_core'),
			'bottom'   => esc_html__('Bottom', 'gt3_themes_core'),
		),
		'default' => 'aside',
	)
);

$widget->add_control(
	'avatar_slider',
	array(
		'label' => esc_html__('Author Image Slider', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'condition' => array(
			'image_position' => array('top','bottom'),
			'items_per_line' => '1',
			'author_position!' => 'around'
		)
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
		'style_transfer' => true,
		'prefix_class' => 'gt3-testimonials-aligment-',
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
		'title_field' => '{{{ name }}}',
	)
);

$widget->end_controls_section();


$widget->start_controls_section(
	'section_style_testimonial_image',
	array(
		'label' => __( 'Image', 'gt3_themes_core' ),
		'tab' => Controls_Manager::TAB_STYLE,
	)
);
$widget->add_control(
	'image_size',
	array(
		'label' => __( 'Image Size', 'gt3_themes_core' ),
		'type' => Controls_Manager::SLIDER,
		'size_units' => array( 'px' ),
		'range' => array(
			'px' => array(
				'min' => 20,
				'max' => 200,
			),
		),
		'default' => array(
			'size' => 60
		),
		'selectors' => array(
			'{{WRAPPER}} .testimonials_author_wrapper .testimonials_photo img' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;',
			'{{WRAPPER}} .testimonials_author_wrapper .testimonials_photo' => 'height: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} .testimonials_avatar_slider .testimonials_avatar_item' => 'width: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} .testimonials_avatar_slider .testimonials_author_rotator' => 'width: calc({{SIZE}}{{UNIT}} * 3);'
		),
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'section_style_testimonial_content',
	array(
		'label' => __( 'Content', 'gt3_themes_core' ),
		'tab' => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'color_title',
	array(
		'label'     => esc_html__('Text Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .testimonials-text' => 'color: {{VALUE}};',
			'{{WRAPPER}} .slick-dots' => 'color: {{VALUE}};',
			'{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};'
		)
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}} .testimonials-text, {{WRAPPER}} .testimonials-text p'
	)
);

$widget->add_control(
	'item_wrap_bg',
	array(
		'label'   => esc_html__('Item Wrapper Background Color','gt3_themes_core'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-TestimonialsLite .testimonial_item_wrapper' => 'background-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-TestimonialsLite.has_items_bg.gt3-testimonials-aligment-left.gt3-testimonials-perline1:after' => 'color: {{VALUE}};',
		),
		'prefix_class' => 'has_items_bg color_',
	)
);

$widget->add_control(
	'quote_items_color',
	array(
		'label'   => esc_html__('Quote Icon Color','ewebot'),
		'type'    => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-TestimonialsLite .testimonials-text-quote' => 'color: {{VALUE}};',
			'{WRAPPER}} .testimonials-quote-icon-holder' => 'color: {{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-TestimonialsLite .testimonials-text-quote-holder' => 'color: {{VALUE}};',
		),
	)
);



$widget->end_controls_section();

$widget->start_controls_section(
	'section_style_testimonial_author',
	array(
		'label' => __( 'Author', 'gt3_themes_core' ),
		'tab' => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'color_author',
	array(
		'label'     => esc_html__('Color Author', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .testimonials_author_wrapper' => 'color: {{VALUE}};',
		)
	)
);

$widget->add_control(
	'color_author_position',
	array(
		'label'     => esc_html__('Color Author Position', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .testimonials_author_wrapper .testimonials-sub_name' => 'color: {{VALUE}};',
		)
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'author_typography',
		'selector' => '{{WRAPPER}} .testimonials_author_wrapper',
	)
);

$widget->end_controls_section();
$widget->start_controls_section(
	'section_style_testimonial_arrow',
	array(
		'label' => __( 'Arrow', 'gt3_themes_core' ),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => array(
			'nav!' => 'none',
		),
	)
);

$widget->add_control(
	'color_slider_arrow',
	array(
		'label'     => esc_html__('Slider Navigation Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .slick-dots' => 'color: {{VALUE}};',
			'{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};'
		),
		'condition' => array(
			'nav!' => 'none',
		),
	)
);

$widget->end_controls_section();
