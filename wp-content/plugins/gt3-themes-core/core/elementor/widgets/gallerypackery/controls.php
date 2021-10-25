<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\GT3_Core_Elementor_Control_Gallery;



/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_GalleryPackery $widget */

$widget->start_controls_section(
	'basic',
	array(
		'label' => esc_html__('Basic', 'gt3_themes_core'),
	)
);

if(post_type_exists('gt3_gallery')) {
	$widget->add_control(
		'select_source',
		array(
			'label'       => esc_html__('Select Source', 'gt3_themes_core'),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'module'     => esc_html__('Module Images', 'gt3_themes_core'),
				'gallery'    => esc_html__('Gallery', 'gt3_themes_core'),
				'categories' => esc_html__('Categories', 'gt3_themes_core'),
			),
			'default'     => 'module',
			'description' => esc_html__('For use Filter select "categories" source'),
		)
	);

	$widget->add_control(
		'gallery',
		array(
			'label'     => esc_html__('Select Gallery', 'gt3_themes_core'),
			'type'      => Controls_Manager::SELECT2,
			'options'   => GT3_Post_Type_Gallery::get_galleries(),
			'condition' => array(
				'select_source' => 'gallery',
			),
		)
	);

	$widget->add_control(
		'categories',
		array(
			'label'       => esc_html__('Select Categories', 'gt3_themes_core'),
			'type'        => Controls_Manager::SELECT2,
			'options'     => GT3_Post_Type_Gallery::get_galleries_categories(),
			'multiple'    => true,
			'condition'   => array(
				'select_source' => 'categories',
			),
			'label_block' => true
		)
	);

	$widget->add_control(
		'slides',
		array(
			'type'      => GT3_Core_Elementor_Control_Gallery::type(),
			'condition' => array(
				'select_source' => 'module',
			),
		)
	);
} else {
	$widget->add_control(
		'slides',
		array(
			'type' => GT3_Core_Elementor_Control_Gallery::type(),
		)
	);
}

$widget->add_control(
	'type',
	array(
		'label'   => esc_html__('Type', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'' => esc_html__('Select type', 'gt3_themes_core'),
			1  => esc_html__('Type 1', 'gt3_themes_core'),
			2  => esc_html__('Type 2', 'gt3_themes_core'),
			3  => esc_html__('Type 3', 'gt3_themes_core'),
			4  => esc_html__('Type 4', 'gt3_themes_core'),
			5  => esc_html__('Type 5', 'gt3_themes_core'),
			6  => esc_html__('Type 6', 'gt3_themes_core'),
			7  => esc_html__('Type 7', 'gt3_themes_core'),
		),
		'default' => '',
	)
);

for($i = 1; $i <= 7; $i++) {
	$packery_img = esc_url(GT3_CORE_URL.'core/elementor/assets/image/packery/type'.$i.'.png');
	$image       = esc_attr('background-image: url("'.$packery_img.'")');

	$widget->add_control(
		'type'.$i.'_description',
		array(
			'type'      => Controls_Manager::RAW_HTML,
			'raw'       => '<div class="packery_preview" style="'.$image.'"></div>',
			'condition' => array(
				'type' => ''.$i,
			)
		)
	);
}

$widget->add_control(
	'hover',
	array(
		'label'       => esc_html__('Hover Effect', 'gt3_themes_core'),
		'type'        => Controls_Manager::SELECT,
		'options'     => array(
			'none'  => esc_html__('None', 'gt3_themes_core'),
			'type1' => esc_html__('Type 1', 'gt3_themes_core'),
			'type2' => esc_html__('Type 2', 'gt3_themes_core'),
			'type3' => esc_html__('Type 3', 'gt3_themes_core'),
			'type4' => esc_html__('Type 4', 'gt3_themes_core'),
			'type5' => esc_html__('Type 5', 'gt3_themes_core'),
			'type6' => esc_html__('Type 6', 'gt3_themes_core'),
		),
		'default'     => 'type1',
		'label_block' => true,
	)
);

$widget->add_control(
	'lightbox',
	array(
		'label' => esc_html__('Lightbox', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);

$widget->add_responsive_control(
	'grid_gap',
	array(
		'label'     => esc_html__('Grid Gap', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'0'    => '0',
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
			'2%'    => '2%',
			'4.95%' => '5%',
			'8%'    => '8%',
			'10%'   => '10%',
			'12%'   => '12%',
			'15%'   => '15%',
		),
		'default'   => '0',
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope_wrapper' => 'margin-right:-{{VALUE}}; margin-bottom:-{{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope_item'    => 'padding-right: {{VALUE}}; padding-bottom:{{VALUE}};',
		)
	)
);

$widget->add_control(
	'raw_filter_description',
	array(
		'type'      => Controls_Manager::RAW_HTML,
		'raw'       => sprintf('<span>%s</span>', esc_html__('For use Filter select "categories" source')),
		'condition' => array(
			'select_source!' => 'categories',
		)
	)
);

$widget->add_control(
	'use_filter',
	array(
		'label'       => esc_html__('Use Filter', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('Please select min. 2 categories to display filter', 'gt3_themes_core'),
		'condition'   => array(
			'select_source' => 'categories',
		)
	)
);

$widget->add_responsive_control(
	'filter_align',
	array(
		'label'     => esc_html__('Alignment', 'gt3_themes_core'),
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
			'{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope-filter' => 'text-align: {{VALUE}};',
		),
		'default'   => '',
		'condition' => array(
			'select_source' => 'categories',
			'use_filter!'   => '',
		)
	)
);

$widget->add_control(
	'all_title',
	array(
		'label'     => esc_html__('All Title', 'gt3_themes_core'),
		'type'      => Controls_Manager::TEXT,
		'default'   => esc_html__('All', 'gt3_themes_core'),
		'condition' => array(
			'select_source' => 'categories',
			'use_filter!'   => '',
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
		'label'     => esc_html__('Show Category', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'condition' => array(
			'select_source' => 'categories',
		)
	)
);

$widget->add_control(
	'post_per_load',
	array(
		'label'   => esc_html__('Post Per Load', 'gt3_themes_core'),
		'type'    => Controls_Manager::NUMBER,
		'min'     => 1,
		'step'    => 1,
		'default' => 12,
	)
);

$widget->add_control(
	'show_view_all',
	array(
		'label' => esc_html__('Show "See More" Button', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
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
			'none'    => esc_html__('None', 'gt3_themes_core'),
			'default' => esc_html__('Default', 'gt3_themes_core'),
			'icon'    => esc_html__('Icon', 'gt3_themes_core'),
		),
		'default'   => 'default',
		'condition' => array(
			'show_view_all!' => '',
		)
	)
);

$widget->add_control(
	'button_border',
	array(
		'label'     => esc_html__('Button Border', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'condition' => array(
			'show_view_all!' => '',
		),
		'default'   => 'yes',
	)
);

$widget->add_control(
	'button_icon',
	array(
		'label'     => esc_html__('Button Icon', 'gt3_themes_core'),
		'type'      => Controls_Manager::ICON,
		'condition' => array(
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
			'{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .widget-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'button_type!'   => 'none',
			'show_view_all!' => '',
		)
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'title_style',
	array(
		'label' => esc_html__('Title', 'gt3_themes_core'),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

$widget->add_control(
	'title_color',
	array(
		'label'     => esc_html__('Title Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope_item .title' => 'color: {{VALUE}};'
		)
	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'title_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope_item .title',
	)
);

$widget->end_controls_section();

$widget->start_controls_section(
	'category_style',
	array(
		'label'     => esc_html__('Category', 'gt3_themes_core'),
		'tab'       => Controls_Manager::TAB_STYLE,
		'condition' => array(
			'select_source'  => 'categories',
			'show_category!' => '',
		),
	)
);

$widget->add_control(
	'category_color',
	array(
		'label'     => esc_html__('Title Color', 'gt3_themes_core'),
		'type'      => Controls_Manager::COLOR,
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope_item .categories' => 'color: {{VALUE}};'
		),

	)
);

$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'     => 'category_typography',
		'selector' => '{{WRAPPER}}.elementor-widget-gt3-core-gallerypackery .isotope_item .categories',

	)
);

$widget->end_controls_section();