<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team_Carousel $widget */

$widget->start_controls_section(
	'query',
	array(
		'label' => esc_html__('Build Query', 'gt3_themes_core'),
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
	'general',
	array(
		'label' => esc_html__('Main Settings', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
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
    'dots_position',
    array(
        'label'   => esc_html__('Dots Position', 'gt3_themes_core'),
        'type'    => Controls_Manager::SELECT,
        'options' => array(
            'outside'   => esc_html__('Outside', 'gt3_themes_core'),
            'inside' => esc_html__('Inside', 'gt3_themes_core'),
        ),
        'default' => 'outside',
        'prefix_class' => 'dots_position-',
        'condition' => array(
            'nav' => 'dots'
        ),
    )
);

$widget->add_control(
    'dots_color',
    array(
        'label'     => esc_html__('Dots Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} ul.slick-dots li' => '
                color: {{VALUE}};',
        ),
        'condition' => array(
            'nav' => 'dots'
        ),
    )
);

$widget->add_control(
    'arrows_position',
    array(
        'label'   => esc_html__('Arrows Position', 'gt3_themes_core'),
        'type'    => Controls_Manager::SELECT,
        'options' => array(
            'outside'   => esc_html__('Outside', 'gt3_themes_core'),
            'inside' => esc_html__('Inside', 'gt3_themes_core'),
        ),
        'default' => 'inside',
        'condition' => array(
            'nav' => 'arrows'
        ),
        'prefix_class' => 'arrow_position-',
    )
);

$widget->add_control(
    'arrows_color',
    array(
        'label'     => esc_html__('Arrows Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .item_list .slick-arrow .slick_arrow_icon' => '
                color: {{VALUE}};',
        ),
        'condition' => array(
            'nav' => 'arrows'
        ),
    )
);

$widget->add_control(
    'arrows_bg_color',
    array(
        'label'     => esc_html__('Arrows Background Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .item_list .slick-arrow' => '
                background-color: {{VALUE}};',
        ),
        'condition' => array(
            'nav' => 'arrows'
        ),
    )
);

$widget->add_control(
   	'arrows_shadow',
    array(
        'label' => esc_html__('Arrows Shadow', 'gt3_themes_core'),
        'type'  => Controls_Manager::SWITCHER,
        'default'   => '',
        'prefix_class' => 'arrow_shadow-',
        'condition' => array(
            'nav' => 'arrows'
        ),
        'separator' => 'after',
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

$widget->add_control(
	'type',
	array(
		'label'   => esc_html__('Type', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'type1' => esc_html__('Type 1', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2', 'gt3_themes_core'),
			'type3' => esc_html__('Type 3', 'gt3_themes_core'),
			'type4' => esc_html__('Type 4', 'gt3_themes_core'),
		),
		'default' => 'type1',
		'separator' => 'before',
	)
);


$widget->add_control(
	'posts_per_line',
	array(
		'label'   => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
		),
		'default' => '1',
	)
);

$widget->add_responsive_control(
	'grid_gap',
	array(
		'label'     => esc_html__('Grid Gap', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'0'  => '0',
			'1px'  => '1px',
			'2px'  => '2px',
			'3px'  => '3px',
			'4px'  => '4px',
			'5px'  => '5px',
			'10px' => '10px',
			'15px' => '15px',
			'20px' => '20px',
			'25px' => '25px',
			'30px' => '30px',
			'35px' => '35px',
		),
		'default'   => '0',
		'prefix_class' => 'grid_gap-',
		'selectors' => array(
                '{{WRAPPER}} .shortcode_team' => 'margin-right:calc(-{{VALUE}} / 2); margin-left:calc(-{{VALUE}} / 2);width: calc(100% + {{VALUE}});',
                '{{WRAPPER}} .shortcode_team .slick-list > .slick-track > .item-team-member' => 'margin-right:calc({{VALUE}} / 2); margin-left:calc({{VALUE}} / 2);',
                '{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-dots' => 'margin-right:calc({{VALUE}} / 2); margin-left:calc({{VALUE}} / 2);',
                '{{WRAPPER}} .shortcode_team .slick-next' => '
                    -webkit-transform: translateX(calc(-{{VALUE}} / 2));
                    -ms-transform: translateX(calc(-{{VALUE}} / 2));
                    transform: translateX(calc(-{{VALUE}} / 2));',
                '{{WRAPPER}} .shortcode_team .slick-prev' => '
                    -webkit-transform: translateX(calc({{VALUE}} / 2));
                    -ms-transform: translateX(calc({{VALUE}} / 2));
                    transform: translateX(calc({{VALUE}} / 2));',
            ),
	)
);

$widget->add_control(
	'link_post',
	array(
		'label' => esc_html__('Enable Link to Post', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'show_title',
	array(
		'label' => esc_html__('Show Title', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'separator' => 'before',
	)
);

$widget->add_control(
	'show_position',
	array(
		'label' => esc_html__('Show Position', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'show_description',
	array(
		'label' => esc_html__('Show Description', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'show_social',
	array(
		'label' => esc_html__('Show Social', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'default'   => 'yes'
	)
);

$widget->add_control(
	'custom_item_height',
	array(
		'label' => esc_html__('Enable Custom Item Height?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_responsive_control(
	'item_img_height',
	array(
		'label'       => esc_html__('Item Image Height', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 440,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 300,
				'max'  => 600,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'description' => esc_html__('Enter item image height in pixels.', 'gt3_themes_core'),
		'label_block' => true,
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-team .team_image_cover' => 'height: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'custom_item_height!' => '',
		)
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
	'title_color',
	array(
		'label'     => esc_html__( 'Title color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .team_title__text' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'job_color',
	array(
		'label'     => esc_html__( 'Member Job color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .team-positions' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'desc_color',
	array(
		'label'     => esc_html__( 'Short Description color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .member-short-desc' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color',
	array(
		'label'     => esc_html__( 'Icons color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper .member-icon' => 'color: {{VALUE}} !important;',
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
	'title_color_hover',
	array(
		'label'     => esc_html__( 'Title color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .team_title__text' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'job_color_hover',
	array(
		'label'     => esc_html__( 'Member Job color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .team-positions' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'desc_color_hover',
	array(
		'label'     => esc_html__( 'Short Description color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .member-short-desc' => 'color: {{VALUE}};',
		),
	)
);
$widget->add_control(
	'icon_color_hover',
	array(
		'label'     => esc_html__( 'Icons color', 'gt3_themes_core' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}} div.elementor-widget-container .shortcode_team .item_wrapper:hover .member-icon' => 'color: {{VALUE}} !important;',
		),
	)
);

$widget->end_controls_tab();

$widget->end_controls_section();
