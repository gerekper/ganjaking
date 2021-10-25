<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Blog $widget */

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
	'blog_post_listing_content_module',
	array(
		'label'       => esc_html__('Cut off text in blog listing', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, cut off text in blog listing', 'gt3_themes_core'),
		'default'     => 'yes',
	)
);

$widget->add_control(
	'symbol_count_descrt',
	array(
		'label'       => esc_html__('Symbol count', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => '',
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
			'blog_post_listing_content_module!' => '',
			'packery_en' => '',
		)
	)
);

$widget->add_control(
	'meta_author',
	array(
		'label'       => esc_html__('Show post-meta author?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have author', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_comments',
	array(
		'label'       => esc_html__('Show post-meta comments?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have comments', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_categories',
	array(
		'label'       => esc_html__('Show post-meta categories?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have categories', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_date',
	array(
		'label'       => esc_html__('Show post-meta date?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post-meta will have date', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'post_media_content',
	array(
		'label'       => esc_html__('Post Media Content?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked and post have media content (featured image, video, audio, gallery, etc.), post will have media content', 'gt3_themes_core'),
		'default'     => 'yes',
		'prefix_class' => 'post_has_media_content-',
		'condition' => array(
			'packery_en' => '',
		),
	)
);

/*
$show_share = gt3_option('blog_post_share');
if ($show_share == "1"){
	$widget->add_control(
		'share',
		array(
			'label'       => esc_html__('Show share?', 'gt3_themes_core'),
			'type'        => Controls_Manager::SWITCHER,
			'description' => esc_html__('If checked, post will have share', 'gt3_themes_core'),
		)
	);
}
*/

$widget->add_control(
	'items_per_line',
	array(
		'label'       => esc_html__('Items Per Line', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
		),
		'default'     => 3,
		'description' => esc_html__('Select the number of items per line', 'gt3_themes_core'),
		'condition'   => array(
			'packery_en' => '',
		)
	)
);

$widget->add_control(
	'items_type_line1_type',
	array(
		'label'       => esc_html__('Items View Type', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'type1' => 'Type 1',
			'type2' => 'Type 2',
		),
		'default'     => 'type1',
		'condition'   => array(
			'items_per_line' => '1',
			'packery_en' => '',
		)
	)
);

$widget->add_control(
	'meta_position',
	array(
		'label'       => esc_html__('Post meta position', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'before_title' => esc_html__('Before Title', 'gt3_themes_core'),
			'after_title' => esc_html__('After Title', 'gt3_themes_core'),
		),
		'default'     => 'after_title',
		'description' => esc_html__('Select post-meta position', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'spacing_beetween_items',
	array(
		'label'       => esc_html__('Spacing beetween items', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'5'  => '5px',
			'10' => '10px',
			'15' => '15px',
			'20' => '20px',
			'25' => '25px',
			'30' => '30px',
		),
		'default'     => '30',
		'description' => esc_html__('Select spacing beetween items', 'gt3_themes_core'),
		'condition'   => array(
			'items_per_line!' => '1',
			'packery_en'      => '',
		)
	)
);

$widget->add_control(
	'pagination_en',
	array(
		'label'       => esc_html__('Pagination', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, pagination will be enabled', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'packery_en',
	array(
		'label' => esc_html__('Packery', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_control(
	'packery_items_per_line',
	array(
		'label'       => esc_html__( 'Items Per Line', 'gt3_themes_core' ),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'theme_packery' => esc_html__( 'Theme Packery', 'gt3_themes_core' ),
			'1'             => '1',
			'2'             => '2',
			'3'             => '3',
			'4'             => '4',
		),
		'default'     => 'theme_packery',
		'description' => esc_html__( 'Select the number of items per line', 'gt3_themes_core' ),
		'condition'   => array(
			'packery_en!' => '',
		)
	)
);

$widget->add_control(
	'post_btn_link',
	array(
		'label'       => esc_html__('Show post button?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post will have button', 'gt3_themes_core'),
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
	'post_boxed_content',
	array(
		'label'       => esc_html__('Enable post boxed content?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post content will boxed', 'gt3_themes_core'),
		'condition' => array(
			'packery_en' => '',
			'items_type_line1_type!' => 'type2',
		),
	)
);

$widget->add_control(
	'blog_filter',
	array(
		'label'     => esc_html__('Filter', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'condition' => array(
			'packery_en!' => '',
		),
	)
);

$widget->add_responsive_control(
	'grid_gap',
	array(
		'label'     => esc_html__('Grid Gap', 'gt3_themes_core'),
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
			'2%'    => '2%',
			'4.95%' => '5%',
			'8%'    => '8%',
			'10%'   => '10%',
			'12%'   => '12%',
			'15%'   => '15%',
		),
		'default'   => '0',
		'condition' => array(
			'packery_en!' => '',
		),
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .packery_wrapper .isotope_blog_items' => 'margin-right:-{{VALUE}}; margin-bottom:-{{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .packery_wrapper .isotope-item'       => 'padding-right: {{VALUE}}; padding-bottom:{{VALUE}};',
		)
	)
);

$widget->add_control(
	'static_info_block',
	array(
		'label' => esc_html__('Enable Static Information Block?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);
/*$widget->add_control(
	'element_icon',
	array(
		'label'     => esc_html__('Button Icon:', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
			'enable_icon!'       => '',
		),
	)
);*/

$widget->add_control(
	'thumbs_size',
	array(
		'label'       => esc_html__('Image Size', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'default'  => esc_html__('Default', 'gt3_themes_core'),
			'large'  => esc_html__('Large (1024px)', 'gt3_themes_core'),
			'medium_large'  => esc_html__('Medium-Large (768px)', 'gt3_themes_core'),
			'medium'  => esc_html__('Medium (300px)', 'gt3_themes_core'),
		),
		'default'     => 'default',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'static_information',
	array(
		'label' => esc_html__('Static Information', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_SETTINGS,
		'condition' => array(
			'static_info_block!' => '',
		),
	)
);

/* Static Info Block Start */
$widget->add_control(
	'title',
	array(
		'label'     => esc_html__('Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('Title', 'gt3_themes_core'),
		'condition' => array(
			'static_info_block!' => '',
		),
	)
);
$widget->add_control(
	'sub_title',
	array(
		'label'     => esc_html__('Subtitle', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('Subtitle', 'gt3_themes_core'),
		'condition' => array(
			'static_info_block!' => '',
		),
	)
);
$widget->add_control(
	'content',
	array(
		'label'     => esc_html__('Content', 'gt3_themes_core'),
		'type'      => Controls_Manager::WYSIWYG,
		'default'   => esc_html__('Content', 'gt3_themes_core'),
		'condition' => array(
			'static_info_block!' => '',
		),
	)
);
$widget->add_control(
	'btn_block',
	array(
		'label'     => esc_html__('Enable Button?', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'condition' => array(
			'static_info_block!' => '',
		),
	)
);
$widget->add_control(
	'btn_title',
	array(
		'label'     => esc_html__('Button Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('Button Title', 'gt3_themes_core'),
		'condition' => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
		),
	)
);
$widget->add_control(
	'btn_link',
	array(
		'label'     => esc_html__('Button Link', 'gt3_themes_core'),
		'type'      => Controls_Manager::URL,
		'default'   => array(
			'url'         => '#',
			'is_external' => false,
			'nofollow'    => false,
		),
		'condition' => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
		),
	)
);
$widget->add_control(
	'enable_icon',
	array(
		'label'     => esc_html__('Enable Button Icon?', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'condition' => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
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
		'condition' => array(
			'static_info_block!' => '',
		),
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .title',
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'       => esc_html__('Title Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'condition'   => array(
			'static_info_block!' => '',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .title' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'subtitle_typography',
		'label'     => esc_html__('Subtitle Typography', 'gt3_themes_core'),
		'condition' => array(
			'static_info_block!' => '',
		),
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .sub_title',
	)
);

$widget->add_control(
	'subtitle_color',
	array(
		'label'       => esc_html__('Subtitle Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'condition'   => array(
			'static_info_block!' => '',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .sub_title' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'content_typography',
		'label'     => esc_html__('Content Typography', 'gt3_themes_core'),
		'condition' => array(
			'static_info_block!' => '',
		),
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .content',
	)
);

$widget->add_control(
	'content_color',
	array(
		'label'       => esc_html__('Content Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'condition'   => array(
			'static_info_block!' => '',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .content' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'btn_typography',
		'label'     => esc_html__('Button Typography', 'gt3_themes_core'),
		'condition' => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
		),
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .static_info_link',
	)
);

$widget->add_control(
	'btn_color',
	array(
		'label'       => esc_html__('Button Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'condition'   => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .static_info_link' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_control(
	'btn_color_hover',
	array(
		'label'       => esc_html__('Button Color (Hover State)', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'label_block' => true,
		'condition'   => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .static_info_link:hover' => 'color: {{VALUE}};',
		),
	)
);
/*$widget->add_control(
	'icon_size',
	array(
		'label'       => esc_html__('Icon Size', 'gt3_themes_core'),
		'type'        => Controls_Manager::SLIDER,
		'default'     => array(
			'size' => 20,
			'unit' => 'px',
		),
		'range'       => array(
			'px' => array(
				'min'  => 15,
				'max'  => 40,
				'step' => 1,
			),
		),
		'size_units'  => array( 'px' ),
		'label_block' => true,
		'condition'   => array(
			'btn_block!'         => '',
			'static_info_block!' => '',
			'enable_icon!'       => '',
		),
		'selectors'   => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-blog .static_info_text_block .static_info_link span' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
		),
	)
);*/

$widget->end_controls_section();


