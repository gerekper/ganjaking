<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Group_Control_Typography;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Project $widget */

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
	'show_type',
	array(
		'label'   => esc_html__('Show as', 'gt3_themes_core'),
		'type'    => Controls_Manager::SELECT,
		'options' => array(
			'grid'    => esc_html__('Grid', 'gt3_themes_core'),
			'packery' => esc_html__('Packery', 'gt3_themes_core'),
			'masonry' => esc_html__('Masonry', 'gt3_themes_core'),
		),
		'default' => 'grid',
	)
);

// Grid start
$widget->add_control(
	'grid_type',
	array(
		'label'     => esc_html__('Grid Type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'vertical'  => esc_html__('Vertical Align', 'gt3_themes_core'),
			'square'    => esc_html__('Square', 'gt3_themes_core'),
			'rectangle' => esc_html__('Rectangle', 'gt3_themes_core'),
		),
		'default'   => 'square',
		'condition' => array(
			'show_type' => 'grid',
		),
	)
);
// Grid end

// Packery start
$widget->add_control(
	'packery_type',
	array(
		'label'     => esc_html__('Type', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'' => esc_html__('Select type', 'gt3_themes_core'),
			1  => esc_html__('Type 1', 'gt3_themes_core'),
			2  => esc_html__('Type 2', 'gt3_themes_core'),
			3  => esc_html__('Type 3', 'gt3_themes_core'),
			4  => esc_html__('Type 4', 'gt3_themes_core'),
		),
		'default'   => '',
		'condition' => array(
			'show_type' => 'packery',
		),
	)
);

for($i = 1; $i <= 4; $i++) {
	$packery_img = esc_url(GT3_CORE_URL.'core/elementor/assets/image/packery/type'.$i.'.png');
	$image       = esc_attr('background-image: url("'.$packery_img.'")');

	$widget->add_control(
		'packery_type'.$i.'_description',
		array(
			'type'      => Controls_Manager::RAW_HTML,
			'raw'       => '<div class="packery_preview" style="'.$image.'"></div>',
			'condition' => array(
				'packery_type' => ''.$i,
				'show_type'    => 'packery',
			)
		)
	);
}
// Packery end

$widget->add_control(
	'cols',
	array(
		'label'     => esc_html__('Cols', 'gt3_themes_core'),
		'type'      => Controls_Manager::SELECT,
		'options'   => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
		),
		'default'   => 4,
		'condition' => array(
			'show_type' => array( 'grid', 'masonry' ),
		),
	)
);

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
			'type7' => esc_html__('Type 7', 'gt3_themes_core'),
		),
		'default'     => 'type1',
		'label_block' => true,
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
		'selectors' => array(
			'{{WRAPPER}}.elementor-widget-gt3-core-project .isotope_wrapper' => 'margin-right:-{{VALUE}}; margin-bottom:-{{VALUE}};',
			'{{WRAPPER}}.elementor-widget-gt3-core-project .isotope_item'    => 'padding-right: {{VALUE}}; padding-bottom:{{VALUE}};',
		)
	)
);

$widget->add_control(
	'use_filter',
	array(
		'label'       => esc_html__('Use Filter', 'gt3_themes_core'),
		'type'        => Controls_Manager::SWITCHER,
		'description' => esc_html__('Please select min. 2 categories to display filter (Settings -> Query -> Taxonomies)', 'gt3_themes_core'),
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .isotope-filter' => 'text-align: {{VALUE}};',
		),
		'default'   => '',
		'condition' => array(
			'use_filter!' => '',
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
			'use_filter!' => '',
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
	'show_description',
	array(
		'label' => esc_html__('Show Description', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
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
	'show_view_all',
	array(
		'label' => esc_html__('Show "See More" Button', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
		'condition' => array(
			'pagination_en' => '',
		)
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

/*$widget->add_control(
	'button_border',
	array(
		'label'     => esc_html__('Button Border', 'gt3_themes_core'),
		'type'      => Controls_Manager::SWITCHER,
		'condition' => array(
			'show_view_all!' => '',
		),
		'default'   => 'yes',
	)
);*/

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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .project_view_more_link .elementor_btn_icon_container' => 'padding-left: {{SIZE}}{{UNIT}};',
		),
		'condition' => array(
			'pagination_en' => '',
			'button_type!'   => 'none',
			'show_view_all!' => '',
		)
	)
);

/* Static Info Block Start */
$widget->add_control(
	'static_info_block',
	array(
		'label' => esc_html__('Enable Static Information Block?', 'gt3_themes_core'),
		'type'  => Controls_Manager::SWITCHER,
	)
);
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
$widget->add_group_control(
	Group_Control_Typography::get_type(),
	array(
		'name'      => 'title_typography',
		'label'     => esc_html__('Title Typography', 'gt3_themes_core'),
		'condition' => array(
			'static_info_block!' => '',
		),
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .title',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .title' => 'color: {{VALUE}};',
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
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .sub_title',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .sub_title' => 'color: {{VALUE}};',
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
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .content',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .content' => 'color: {{VALUE}};',
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
		'selector'  => '{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .static_info_link',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .static_info_link' => 'color: {{VALUE}};',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .static_info_link:hover' => 'color: {{VALUE}};',
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
			'{{WRAPPER}}.elementor-widget-gt3-core-project .static_info_text_block .static_info_link span' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
		),
	)
);*/
/* Static Info Block End */

$widget->end_controls_section();



