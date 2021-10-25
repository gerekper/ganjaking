<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogPackery $widget */

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



// Packery start
$widget->add_control(
	'packery_type',
	array(
		'label'     => esc_html__('Packery Type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			1 	=> esc_html__('Type 1', 'gt3_themes_core'),
			2 	=> esc_html__('Type 2', 'gt3_themes_core'),
			3 	=> esc_html__('Type 3', 'gt3_themes_core'),
		),
		'default'   => 1,
		/*'prefix_class' => 'packery_type_',*/
	)
);

for($i = 1; $i <= 3; $i++) {
	$packery_img = esc_url(GT3_CORE_URL.'core/elementor/assets/image/packery/blog_packery_type_'.$i.'.png');
	$image       = esc_attr('background-image: url("'.$packery_img.'")');

	$widget->add_control(
		'packery_type'.$i.'_description',
		array(
			'type'      => Controls_Manager::RAW_HTML,
			'raw'       => '<div class="packery_preview" style="'.$image.'"></div>',
			'condition' => array(
				'packery_type' => ''.$i,
			)
		)
	);
}
$widget->add_control(
	'spacing_beetween_items',
	array(
		'label'       => esc_html__('Spacing beetween items', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
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
		'default'     => '30px',
		'description' => esc_html__('Select spacing beetween items', 'gt3_themes_core'),
		'selectors' => array(
			'{{WRAPPER}} .isotope_wrapper' => 'margin-right:-{{VALUE}}; margin-bottom:-{{VALUE}};',
			'{{WRAPPER}} .isotope_item'    => 'padding-right: {{VALUE}}; padding-bottom:{{VALUE}};',
			'{{WRAPPER}} .isotope_item.packery_extra_size-large_width .gt3_blog_packery__image-placeholder'    => 'padding-bottom:calc(60% - ({{VALUE}} - 10px)/10 -  {{VALUE}}/2) !important;'
		)
	)
);
$widget->add_control(
	'rounded',
	array(
		'label'       => esc_html__('Rounded Items?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => '',
		'prefix_class' => 'rounded_item_',
	)
);
// Packery end




$widget->add_control(
	'meta_author',
	array(
		'label'       => esc_html__('Show post-meta author?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'description' => esc_html__('If checked, post-meta will have author', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_comments',
	array(
		'label'       => esc_html__('Show post-meta comments?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'description' => esc_html__('If checked, post-meta will have comments', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_categories',
	array(
		'label'       => esc_html__('Show post-meta categories?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'description' => esc_html__('If checked, post-meta will have categories', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_date',
	array(
		'label'       => esc_html__('Show post-meta date?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'description' => esc_html__('If checked, post-meta will have date', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'meta_sharing',
	array(
		'label'       => esc_html__('Show Post Sharing?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => 'yes',
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
		'default'     => 'before_title',
		'description' => esc_html__('Select post-meta position', 'gt3_themes_core'),
	)
);

$widget->add_control(
	'post_btn_link',
	array(
		'label'       => esc_html__('Show post button?', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, post will have button', 'gt3_themes_core'),
		'default'   => 'yes',
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
	'lazyload',
	array(
		'label'       => esc_html__('Lazyload', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'default'   => 'yes',
		'separator' => 'before',
	)
);

$widget->add_control(
	'use_filter',
	array(
		'label'       => esc_html__('Use Filter', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'separator' => 'before',
	)
);

$widget->add_control(
	'all_title',
	array(
		'label'     => esc_html__('Filter Reset Label', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('All', 'gt3_themes_core'),
		'condition' => array(
			'use_filter!' => '',
		)
	)
);

$widget->add_responsive_control(
	'filter_align',
	array(
		'label'     => esc_html__('Filter Alignment', 'gt3_themes_core'),
		'type'      => Controls_Manager::CHOOSE,
		'options'   => array(
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
		'selectors' => array(
			'{{WRAPPER}} .isotope-filter' => 'text-align: {{VALUE}};',
		),
		'default'   => '',
		'condition' => array(
			'use_filter!' => '',
		)
	)
);

$widget->add_control(
	'filter_style',
	array(
		'label'       => esc_html__('Filter Style', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'links'  => esc_html__('Links', 'gt3_themes_core'),
			'isotope' => esc_html__('Isotope', 'gt3_themes_core'),
		),
		'default'     => 'links',
		'label_block' => true,
		'condition' => array(
			'use_filter!' => '',
		)
	)
);

$widget->add_control(
	'pagination_en',
	array(
		'label'       => esc_html__('Pagination', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('If checked, pagination will be enabled', 'gt3_themes_core'),
		'separator' => 'before',
		'condition' => array(
			'show_view_all' => '',
		)
	)
);

$widget->add_control(
	'show_view_all',
	array(
		'label' => esc_html__('Show "See More" Button', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'condition' => array(
			'pagination_en' => '',
		),
		'separator' => 'before',
	)
);

$widget->add_control(
	'load_items',
	array(
		'label'     => esc_html__('See Items', 'gt3_themes_core'),
		'type'      => Controls_Manager::NUMBER,
		'min'       => 1,
		'step'      => 1,
		'default'   => '4',
		'condition' => array(
			'pagination_en' => '',
			'show_view_all!' => ''
		)
	)
);

$widget->add_control(
	'button_type',
	array(
		'label'     => esc_html__('Button Type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'default' => esc_html__('Default', 'gt3_themes_core'),
			'icon'    => esc_html__('With Icon', 'gt3_themes_core'),
		),
		'default'   => 'default',
		'condition' => array(
			'pagination_en' => '',
			'show_view_all!' => '',
		)
	)
);

$widget->add_control(
	'button_icon',
	array(
		'label'     => esc_html__('Button Icon', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
			'pagination_en' => '',
			'button_type'    => 'icon',
			'show_view_all!' => '',
		)
	)
);

$widget->add_control(
	'button_title',
	array(
		'label'     => esc_html__('Button Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('See More', 'gt3_themes_core'),
		'condition' => array(
			'pagination_en' => '',
			'show_view_all!' => '',
		)
	)
);

$widget->add_responsive_control(
	'icon_space',
	array(
		'label'     => esc_html__('Icon Spacing', 'gt3_themes_core'),
		'type'      => Controls_Manager::SLIDER,
		'default'   => array(
			'size' => 16,
		),
		'range'     => array(
			'px' => array(
				'min' => 0,
				'max' => 100,
			),
		),
		'selectors' => array(
			'{{WRAPPER}} .blogpackery_view_more_link .elementor_btn_icon_container' => 'padding-left: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'pagination_en' => '',
			'button_type!'   => 'default',
			'show_view_all!' => '',
		)
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
		'selector'  => '{{WRAPPER}} .blog_post_preview .blog_item_description',
	)
);

$widget->add_control(
	'text_color',
	array(
		'label'       => esc_html__('Text Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .blog_post_preview .blog_item_description' => 'color: {{VALUE}};',
		),
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'meta_typography',
		'label'     => esc_html__('Meta Typography', 'gt3_themes_core'),
		'selector'  => '{{WRAPPER}} .gt3_blog_packery__text_wrap .listing_meta, {{WRAPPER}} .gt3_blog_packery__text_wrap .listing_meta > span, {{WRAPPER}} .gt3_blog_packery__text_wrap .listing_meta > span > a',
	)
);

$widget->add_control(
	'meta_color',
	array(
		'label'       => esc_html__('Meta Color', 'gt3_themes_core'),
		'type'        => Controls_Manager::COLOR,
		'selectors'   => array(
			'{{WRAPPER}} .gt3_blog_packery__text_wrap .listing_meta, {{WRAPPER}} .gt3_blog_packery__text_wrap .listing_meta > span, {{WRAPPER}} .gt3_blog_packery__text_wrap .listing_meta > span > a' => 'color: {{VALUE}};',
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
		'selector'  => '{{WRAPPER}} .blog_post_preview .gt3_module_button_list a',
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
			'{{WRAPPER}} .blog_post_preview .gt3_module_button_list a' => 'color: {{VALUE}};',
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
			'{{WRAPPER}} .blog_post_preview .gt3_module_button_list a:hover' => 'color: {{VALUE}};',
		),
	)
);

$widget->end_controls_section();
