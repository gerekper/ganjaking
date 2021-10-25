<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogSimple $widget */

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
		'label' => esc_html__('General', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
	)
);

$widget->add_control(
	'content_cut',
	array(
		'label'       => esc_html__('Cut off text in blog listing', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, cut off text in blog listing', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'symbol_count',
	array(
		'label'       => esc_html__('Symbol count', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 110,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 0,
				'max'  => 500,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'condition'   => array(
			'content_cut!' => '',
		)
	)
);

$widget->add_control(
	'post_featured_image',
	array(
		'label'       => esc_html__('Featured image?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked and post have featured image, post will show featured image', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'post_btn_link',
	array(
		'label'       => esc_html__('Show post button?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post will have button', 'gt3_themes_core'),
		'default'   => 'yes'
	)
);

$widget->add_control(
	'post_btn_link_title',
	array(
		'label'     => esc_html__('Post Button Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('Read More', 'gt3_themes_core'),
		'condition' => array(
			'post_btn_link!' => '',
		),
	)
);

$widget->add_control(
	'pagination_en',
	array(
		'label'       => esc_html__('Pagination', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, pagination will be enabled', 'gt3_themes_core'),
		'condition' => array(
			'carousel' => '',
		),
	)
);

$widget->add_control(
	'carousel',
	array(
		'label'       => esc_html__('Enable Carousel', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, Carousel will be enabled', 'gt3_themes_core'),
		'separator' => 'before',
	)
);

$widget->add_control(
	'posts_per_column',
	array(
		'label'   => esc_html__('Items Per Column', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
		),
		'default' => '3',
		'condition' => array(
			'carousel!' => '',
		),
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
		'condition' => array(
			'carousel!' => '',
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
            'nav' => 'dots',
            'carousel!' => '',
        ),
    )
);

$widget->add_control(
    'arrows_position',
    array(
        'label'   => esc_html__('Arrows Position', 'gt3_themes_core'),
        'type'    => Controls_Manager::SELECT,
        'options' => array(
            'inside' => esc_html__('Inside', 'gt3_themes_core'),
            'top' => esc_html__('Top', 'gt3_themes_core'),
        ),
        'default' => 'inside',
        'condition' => array(
            'nav' => 'arrows',
            'carousel!' => '',
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
            '{{WRAPPER}} .slick-arrow .slick_arrow_icon' => '
                color: {{VALUE}};',
        ),
        'condition' => array(
            'nav' => 'arrows',
            'carousel!' => '',
        ),
    )
);

$widget->add_control(
    'arrows_bg_color',
    array(
        'label'     => esc_html__('Arrows Background Color', 'gt3_themes_core'),
        'type'      => Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .gt3_module_blog_simple.gt3_carousel-elementor .slick-arrow' => '
                background-color: {{VALUE}};',
        ),
        'condition' => array(
            'nav' => 'arrows',
            'carousel!' => '',
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
            'nav' => 'arrows',
            'carousel!' => '',
        ),
        'separator' => 'after',
    )
);

$widget->add_control(
	'autoplay',
	array(
		'label' => esc_html__('Autoplay', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'condition' => array(
			'carousel!' => '',
		),
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
			'autoplay' => 'yes',
			'carousel!' => '',
		),
	)
);


$widget->end_controls_section();

$widget->start_controls_section(
	'style',
	array(
		'label' => esc_html__('Style', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'title_typography',
		'label'     => esc_html__('Title Typography', 'gt3_themes_core'),
		'selector'  => '{{WRAPPER}} .blog_post_preview .blogpost_title',
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'       => esc_html__('Title Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .blog_post_preview .blogpost_title' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'text_typography',
		'label'     => esc_html__('Text Typography', 'gt3_themes_core'),
		'selector'  => '{{WRAPPER}} .gt3_module_blog_simple .blog_post_preview .blog_item_description',
	)
);

$widget->add_control(
	'text_color',
	array(
		'label'       => esc_html__('Text Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .gt3_module_blog_simple .blog_post_preview .blog_item_description' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'date_typography',
		'label'     => esc_html__('Date Typography', 'gt3_themes_core'),
		'selector'  => '{{WRAPPER}} .blog_post_preview .gt3_blogsimple_header .listing_meta .post_date',
	)
);

$widget->add_control(
	'content_color',
	array(
		'label'       => esc_html__('Date Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .blog_post_preview .gt3_blogsimple_header .listing_meta .post_date' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'btn_typography',
		'label'     => esc_html__('Button Typography', 'gt3_themes_core'),
		'condition' => array(
			'post_btn_link!'         => '',
		),
		'selector'  => '{{WRAPPER}} .gt3_module_blog_simple .blog_post_preview .gt3_module_button_list a',
	)
);

$widget->add_control(
	'btn_color',
	array(
		'label'       => esc_html__('Button Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition' => array(
			'post_btn_link!'         => '',
		),
		'selectors'   => array(
			'{{WRAPPER}} .gt3_module_blog_simple .blog_post_preview .gt3_module_button_list a' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'btn_color_hover',
	array(
		'label'       => esc_html__('Button Color (Hover State)', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'condition' => array(
			'post_btn_link!'         => '',
		),
		'selectors'   => array(
			'{{WRAPPER}} .gt3_module_blog_simple .blog_post_preview .gt3_module_button_list a:hover' => 'color: {{VALUE}};',
		),
	)
);

$widget->end_controls_section();
