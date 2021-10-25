<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_VideoPopup $widget */


$widget->start_controls_section(
	'basic_section',
	array(
		'label' => esc_html__('General', 'gt3_moone_core'),
	)
);

$widget->add_control(
	'video_title',
	array(
		'label' => esc_html__('Title', 'gt3_moone_core'),
		'type'  => Controls_Manager::TEXT,
		'description' => esc_html__('Enter title', 'gt3_moone_core'),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}} .video-popup__title',
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'     => esc_html__('Title Color', 'gt3_moone_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .video-popup__title' => 'color: {{VALUE}};'
		),
	)
);

$widget->add_control(
	'video_link',
	array(
		'label'       => esc_html__('Video Link', 'gt3_moone_core'),
		'type'        => Controls_Manager::TEXT,
		'label_block' => true,
		'default'     => '#',
		'description' => esc_html__('Put a link to your video (YouTube or Vimeo)', 'gt3_moone_core'),
	)
);

$widget->add_control(
	'autoplay',
	array(
		'label'        => esc_html__('Autoplay', 'gt3_moone_core'),
		'type'         => Controls_Manager::SWITCHER,
		'return_value' => true,
	)
);

$widget->add_responsive_control(

	'align',
	array(
		'label'        => esc_html__('Alignment', 'gt3_moone_core'),
		'type'         => Controls_Manager::CHOOSE,
		'options'      => array(
			'left'   => array(
				'title' => esc_html__('Left', 'gt3_moone_core'),
				'icon'  => 'fa fa-align-left',
			),
			'center' => array(
				'title' => esc_html__('Default', 'gt3_moone_core'),
				'icon'  => 'fa fa-align-center',
			),
			'right'  => array(
				'title' => esc_html__('Right', 'gt3_moone_core'),
				'icon'  => 'fa fa-align-right',
			),
		),
		'prefix_class' => 'gt3-elementor%s-align-',
		'default'      => '',
	)
);

$widget->add_responsive_control(
	'align_button',
	array(
		'label'        => esc_html__('Align the Button with the text', 'gt3_moone_core'),
		'type'         => Controls_Manager::CHOOSE,
		'options'      => array(
			'left'   => array(
				'title' => esc_html__('Left', 'gt3_moone_core'),
				'icon'  => 'fa fa-align-left',
			),
			'center' => array(
				'title' => esc_html__('Default', 'gt3_moone_core'),
				'icon'  => 'fa fa-align-center',
			),
			'right'  => array(
				'title' => esc_html__('Right', 'gt3_moone_core'),
				'icon'  => 'fa fa-align-right',
			),
		),
		'prefix_class' => 'gt3-elementor%s-align-button-',
		'default'      => '',
	)
);


$widget->add_control(
	'btn_color',
	array(
		'label'     => esc_html__('Button icon color', 'gt3_moone_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .video-popup__link' => 'border-color: {{VALUE}};',
			'{{WRAPPER}} polygon'            => 'fill: {{VALUE}}; stroke: {{VALUE}}',
		),
		'description' => esc_html__('Select custom color for button.', 'gt3_moone_core'),
	)
);

$widget->add_control(
	'btn_background_color',
	array(
		'label'     => esc_html__('Button background color', 'gt3_moone_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} .video-popup__link' => 'color: {{VALUE}};',
		),
		'description' => esc_html__('Select custom color for button.', 'gt3_moone_core'),
	)
);

$widget->add_control(
    'item_size',
    array(
        'label' => __( 'Video Popup Size', 'gt3_themes_core' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => array( 'px' ),
        'range' => array(
            'px' => array(
                'min' => 30,
                'max' => 400,
            ),
        ),
        'selectors' => array(
            '{{WRAPPER}} .video-popup__link' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
        ),
    )
);

$widget->add_control(
	'button_animation',
	array(
		'label'   => esc_html__('Animation type', 'gt3_moone_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'none'  => esc_html__('None', 'gt3_moone_core'),
			'type1' => esc_html__('Type 1', 'gt3_moone_core'),
			'type2' => esc_html__('Type 2', 'gt3_moone_core'),
		),
		'default' => 'type2',
	)
);

$widget->add_control(
	'color_lines',
	array(
		'label'     => esc_html__('Color lines', 'gt3_moone_core'),
		'type'      => Controls_Manager::COLOR,
		'condition' => array(
			'button_animation!' => 'none'
		),
		'description' => esc_html__('Select the color lines.', 'gt3_moone_core'),
	)
);

$widget->add_control(
	'diameter_lines',
	array(
		'label'     => esc_html__('Diameter for the animation', 'gt3_moone_core'),
		'type'      => Controls_Manager::NUMBER,
		'min'       => 40,
		'max'       => 1000,
		'default'   => 126,
		'step'      => 2,
		'condition' => array(
			'button_animation!' => 'none'
		),
		'description' => __('Enter the diameter for the animation in px (pixels)','gt3_moone_core'),
		'selectors' => array(
			'{{WRAPPER}} .video-popup-animation' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
		),
	)
);

$widget->add_control(
	'lines_width',
	array(
		'label'     => esc_html__('Line width', 'gt3_moone_core'),
		'type'      => Controls_Manager::NUMBER,
		'default'   => '3',
		'min'       => 0,
		'max'       => 6,
		'condition' => array(
			'button_animation' => 'type1'
		),
		'description' => esc_html__('Enter the line width for the animation in px (pixels)', 'gt3_moone_core'),
	)
);

$widget->add_control(
	'shadow_lines_width',
	array(
		'label'     => esc_html__('Shadow Line width', 'gt3_moone_core'),
		'type'      => Controls_Manager::NUMBER,
		'default'   => '0',
		'min'       => 0,
		'condition' => array(
			'button_animation' => 'type1'
		),
		'description' => esc_html__('Enter the shadow width for the animation in px (pixels)', 'gt3_moone_core'),
	)
);

$widget->add_control(
	'lines_delay',
	array(
		'label'     => esc_html__('Transition delay between appearances', 'gt3_moone_core'),
		'type'      => Controls_Manager::NUMBER,
		'min'       => 0,
		'step'      => 50,
		'default'   => 2500,
		'condition' => array(
			'button_animation!' => 'none'
		),
		'description' => esc_html__('Enter transition delay in miliseconds. Element will be animated when it "enters" the browsers viewport', 'gt3_moone_core'),
	)
);

$widget->end_controls_section();
