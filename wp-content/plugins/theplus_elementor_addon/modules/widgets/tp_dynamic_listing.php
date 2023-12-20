<?php 
/*
Widget Name: Dynamic Listing
Description: Different style of Dynamic listing layouts.
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class ThePlus_Dynamic_Listing extends Widget_Base {
		
	public function get_name() {
		return 'tp-dynamic-listing';
		}

    public function get_title() {
        return esc_html__('Dynamic Listing', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-list-alt theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-listing');
    }
	
	public function get_keywords() {
		return ['post', 'custom query', 'acf', 'WooCommerce', 'Products', 'tp', 'theplus'];
	}

	public function is_reload_preview_required() {
		return true;
	}

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'blogs_post_listing',
			[
				'label' => esc_html__( 'Post Listing Types', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'page_listing',
				'options' => [
					"page_listing" => esc_html__("Normal Page", 'theplus'),
					"archive_listing" => esc_html__("Archive Page", 'theplus'),
					"related_post" => esc_html__("Single Page Related Posts", 'theplus'),
					"acf_repeater" => esc_html__("ACF Repeater", 'theplus'),
					"custom_query" => esc_html__("Custom Query", 'theplus'),
					"search_list" => esc_html__("Search List", 'theplus'),
				],
			]
		);			
		$this->add_control(
			'search_list_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : This feature not works in Carousel Layout.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'blogs_post_listing' => 'search_list',
					'layout' => 'carousel',
				],	
			]
		);	
		$this->add_control(
			'extra_query_id',
			[
				'label' => esc_html__( 'Query ID', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'blogs_post_listing' => 'custom_query',
				],
			]
		);
		$this->add_control(
			'query',
			[
				'label' => esc_html__( 'Post Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => theplus_get_post_type(),
				'condition' => [
					'blogs_post_listing!' => ['acf_repeater','custom_query'],
				],
			]
		);
		$this->add_control('related_post_by',
			[
				'label' => esc_html__( 'Related Post Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [					
					"category" => esc_html__("Based on Category", 'theplus'),
					"tags" => esc_html__("Based on Tags", 'theplus'),
					"taxonomy" => esc_html__("Based on taxonomy", 'theplus'),
					"both" => esc_html__("Both (Category, Tags)", 'theplus'),
				],
				'condition' => [
					'blogs_post_listing' => 'related_post',
				],
			]
		);
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list_custom(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
		);
		$this->add_control(
			'skin_template',
			[
				'label' => esc_html__( 'Select a template', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],				
				'options' => theplus_get_templates(),				
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
							[
									'name'     => 'blogs_post_listing',
									'operator' => '==',
									'value'    => 'acf_repeater',
								],
								[
									'name'     => 'blogs_post_listing',
									'operator' => '!=',
									'value'    => 'acf_repeater',
									'name'     => 'style',
									'operator' => '==',
									'value'    => 'custom',
								],
							],
						],
					],
				],
			]
		);
		
		$get_repeater_fields[''] = __('Select Field','theplus');
        $get_repeater_fields = $get_repeater_fields + get_acf_repeater_field();
		
		$this->add_control('acf_repeater_field',
            [
                'label' => __('ACF Field Type','theplus'),
                'type'  => Controls_Manager::SELECT,
				'default' => 'default',
                'options' => [					
					"default" => esc_html__("Default", 'theplus'),
					"custom" => esc_html__("Custom", 'theplus'),
				],
				'condition' => [
					'blogs_post_listing' => 'acf_repeater',
				],
            ]
        );
		$this->add_control(
            'acf_repeater_field_list',
            [
                'label' => __('ACF Repeater Field','theplus'),
                'type'  => Controls_Manager::SELECT,
                'options'   =>  $get_repeater_fields,
				'condition' => [
					'blogs_post_listing' => 'acf_repeater',
					'acf_repeater_field' => 'default',
				],
            ]
        );
		$this->add_control('acf_filed_name',
            [
                'label' => __('ACF Repeater Field', 'theplus'),
                'type'  => Controls_Manager::TEXT,
				'condition' => [
					'blogs_post_listing' => 'acf_repeater',
					'acf_repeater_field' => 'custom',
				],
            ]
        );
		$this->add_control(
			'acf_repeater_field_conditions',
			[
				'label' => esc_html__( 'ACF Repeater Boolean Conditions', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'blogs_post_listing' => 'acf_repeater',
				],
			]
		);
		$this->add_control(
            'acf_rf_name',
            [
                'label' => __('Boolean Field Name', 'theplus'),
                'type'  => Controls_Manager::TEXT,
				'condition' => [
					'blogs_post_listing' => 'acf_repeater',
					'acf_repeater_field_conditions' => 'yes',
				],
            ]
        );
		$this->add_control(
			'multiple_skin_enable',
			[
				'label' => esc_html__( 'Multiple Loops', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
				],
			]
		);
		$this->add_control(
			'skin_template2',
			[
				'label' => esc_html__( 'Loop Template 2', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],				
				'options'     => theplus_get_templates(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
					'multiple_skin_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'skin_template3',
			[
				'label' => esc_html__( 'Loop Template 3', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],				
				'options'     => theplus_get_templates(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
					'multiple_skin_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'skin_template4',
			[
				'label' => esc_html__( 'Loop Template 4', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'options'     => theplus_get_templates(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
					'multiple_skin_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'skin_template5',
			[
				'label' => esc_html__( 'Loop Template 5', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'options'     => theplus_get_templates(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
					'multiple_skin_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => theplus_get_list_layout_style(),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'layout_custom_heading',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Metro and Masonry Layouts needs extra care when you design them in custom skin.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
				],	
			]
		);
		$this->add_control(
			'template_order',
			[
				'label' => esc_html__( 'Template Order', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'default', 'theplus' ),					
					'reverse'  => esc_html__( 'reverse', 'theplus' ),
					'random'  => esc_html__( 'random', 'theplus' ),
				],
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'custom',
				],
			]
		);
		$this->add_control(
			'content_alignment',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
				],
				'default' => 'center',
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'style-2',
					'layout!' => 'metro',
				],
				'label_block' => false,
				'toggle' => true,
			]
		);
		$this->add_control(
			'content_alignment_3',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					'left-right' => [
						'title' => esc_html__( 'Left/Right', 'theplus' ),
						'icon' => 'eicon-exchange',
					],
				],
				'default' => 'left',
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'style-3',
					'layout!' => 'metro',
				],
				'label_block' => false,
				'toggle' => true,
			]
		);
		$this->add_control(
			'style_layout',
			[
				'label' => esc_html__( 'Style Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => ['style-2','style-3'],
				],
			]
		);
		$this->add_responsive_control(
            'column_min_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Minimum Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 1000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 350,
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .blog-list.dynamic-listing-style-4:not(.list-isotope-metro) .post-content-bottom ' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'style-4',
					'layout!' => 'metro',
				],
            ]
        );
		$this->add_control('search_list_customQ',
			[
				'label'=>__('Custom Query','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
				'condition'=>[
					'blogs_post_listing'=>'search_list',
				],
			]
		);
		$this->start_popover();
		$this->add_control('Hadding_CQ',
			[
				'label'=>__('Custom Query','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'after'
			]
		);
		$this->add_control('extra_query_id_search',
			[
				'label' => esc_html__('Custom Query ID','theplus'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'blogs_post_listing' => 'search_list',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		
		$this->start_controls_section(
			'content_source_section',
			[
				'label' => esc_html__( 'Content Source', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
		);
		$this->add_control(
			'post_category',
			[
				'type' => Controls_Manager::SELECT2,
				'label'      => esc_html__( 'Select Category', 'theplus' ),
				'default'    => '',
				'multiple'   => true,
				'label_block' => true,
				'options' => theplus_get_categories(),
				'separator' => 'before',
				'condition' => [
					'blogs_post_listing!' => ['archive_listing','related_post','acf_repeater','custom_query'],
					'query' => ['post'],
				],				
			]
		);
		$this->add_control(
			'post_tags',
			[
				'type' => Controls_Manager::SELECT2,
				'label'      => esc_html__( 'Select Tags', 'theplus' ),
				'default'    => '',
				'label_block' => true,
				'multiple'   => true,
				'options' => theplus_get_tags(),
				'separator' => 'before',
				'condition' => [
					'blogs_post_listing!' => ['archive_listing','related_post','acf_repeater','custom_query'],
					'query' => ['post'],
				],
			]
		);
		$this->add_control(
			'post_taxonomies',
			[
				'label' => esc_html__( 'Taxonomies', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => theplus_get_post_taxonomies(),
				'default' => 'category',
				'dynamic' => ['active' => true,],
				'condition' => [
					'blogs_post_listing!' => ['acf_repeater','custom_query'],
					'query!' => ['post'],
				],
			]
		);
		$this->add_control(
			'include_slug',
			[
				'label'       => esc_html__( 'Taxonomies Slug', 'theplus' ),
				'type'        => Controls_Manager::TEXTAREA,				
				'label_block' => true,
				'placeholder'     => 'Use Slug,if you want to use multiple slug so use comma as separator.',
				'dynamic' => ['active' => true,],
				'condition' => [					
					'blogs_post_listing!' => ['archive_listing','related_product','acf_repeater','custom_query'],
					'query!' => ['post'],
					'post_taxonomies!' => '',
				],
			]
		);		
		$this->add_control(
			'include_posts',
			[
				'label'       => esc_html__( 'Include Post(s)', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder'     => 'include multiple posts use comma as separator',
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'condition' => [					
					'blogs_post_listing!' => ['archive_listing','related_product','acf_repeater','custom_query'],
				],
			]
		);
		$this->add_control(
			'exclude_posts',
			[
				'label'       => esc_html__( 'Exclude Post(s)', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder'     => 'exclude multiple posts use comma as separator',
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'condition' => [					
					'blogs_post_listing!' => ['archive_listing','related_product','acf_repeater','custom_query'],
				],
			]
		);
		$this->add_control(
			'display_posts',
			[
				'label' => esc_html__( 'Maximum Posts Display', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 300,
				'step' => 1,
				'default' => 8,
				'separator' => 'before',
				'dynamic' => ['active' => true,],
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
		);
		$this->add_control(
			'post_offset',
			[
				'label' => esc_html__( 'Offset Posts', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 50,
				'step' => 1,
				'default' => '',
				'description' => esc_html__('Hide posts from the beginning of listing.','theplus'),
				'dynamic' => ['active' => true,],
				'condition' => [
					'blogs_post_listing!' => ['archive_listing','related_post','acf_repeater'],
				],
			]
		);
		$this->add_control(
			'post_order_by',
			[
				'label' => esc_html__( 'Order By', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => theplus_orderby_arr(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
		);
		$this->add_control(
			'post_order',
			[
				'label' => esc_html__( 'Order', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => theplus_order_arr(),
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
		);
		
		$this->end_controls_section();
		/*columns*/
		$this->start_controls_section(
			'columns_section',
			[
				'label' => esc_html__( 'Columns Manage', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'layout!' => ['carousel']
				],
			]
		);
		$this->add_control(
			'desktop_column',
			[
				'label' => esc_html__( 'Desktop Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_get_columns_list_desk(),
				'condition' => [
					'layout!' => ['metro','carousel']
				],
			]
		);
		$this->add_control(
			'tablet_column',
			[
				'label' => esc_html__( 'Tablet Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => ['metro','carousel']
				],
			]
		);
		$this->add_control(
			'mobile_column',
			[
				'label' => esc_html__( 'Mobile Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => ['metro','carousel']
				],
			]
		);
		$this->add_control(
			'metro_column',
			[
				'label' => esc_html__( 'Metro Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					"3" => esc_html__("Column 3", 'theplus'),
					"4" => esc_html__("Column 4", 'theplus'),
					"5" => esc_html__("Column 5", 'theplus'),
					"6" => esc_html__("Column 6", 'theplus'),
				],
				'condition' => [
					'layout' => ['metro']
				],
			]
		);
		$this->add_control(
			'metro_style_3',
			[
				'label' => esc_html__( 'Metro Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
				'condition' => [					
					'metro_column' => '3',
					'layout' => ['metro']
				],
			]
		);
		$this->add_control(
			'metro_style_4',
			[
				'label' => esc_html__( 'Metro Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(3),
				'condition' => [
					'metro_column' => '4',
					'layout' => ['metro']
				],
			]
		);
		$this->add_control(
			'metro_style_5',
			[
				'label' => esc_html__( 'Metro Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(1),
				'condition' => [
					'metro_column' => '5',
					'layout' => ['metro']
				],
			]
		);
		$this->add_control(
			'metro_style_6',
			[
				'label' => esc_html__( 'Metro Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(1),
				'condition' => [
					'metro_column' => '6',
					'layout' => ['metro']
				],
			]
		);
		$this->add_control(
			'responsive_tablet_metro',
			[
				'label' => esc_html__( 'Tablet Responsive', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'no', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'layout' => ['metro']
				],
			]
		);
		$this->add_control(
			'tablet_metro_column',
			[
				'label' => esc_html__( 'Metro Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					"3" => esc_html__("Column 3", 'theplus'),
					"4" => esc_html__("Column 4", 'theplus'),
					"5" => esc_html__("Column 5", 'theplus'),
					"6" => esc_html__("Column 6", 'theplus'),
				],
				'condition' => [
					'responsive_tablet_metro' => 'yes',
					'layout' => ['metro'],
				],
			]
		);
		$this->add_control(
			'tablet_metro_style_3',
			[
				'label' => esc_html__( 'Tablet Metro Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
				'condition' => [
					'responsive_tablet_metro' => 'yes',
					'tablet_metro_column' => '3',
					'layout' => ['metro']
				],
			]
		);
		$this->add_responsive_control(
			'columns_gap',
			[
				'label' => esc_html__( 'Columns Gap/Space Between', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' =>[
					'top' => "15",
					'right' => "15",
					'bottom' => "15",
					'left' => "15",				
				],
				'separator' => 'before',
				'condition' => [
					'layout!' => ['carousel']
				],
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*columns*/
		/*post Extra options*/
		$this->start_controls_section(
			'extra_option_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
		);
		$this->add_control(
			'post_title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => theplus_get_tags_options(),
				'separator' => 'after',
				'condition' => [
					'style!' => 'custom',
				],
			]
		);
		$this->add_control(
			'display_title_limit',
			[
				'label' => esc_html__( 'Title Limit', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',							
			]
		);
		$this->add_control(
            'display_title_by', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Limit on', 'theplus'),
                'default' => 'char',
                'options' => [
                    'char' => esc_html__('Character', 'theplus'),
                    'word' => esc_html__('Word', 'theplus'),                    
                ],
				'condition'   => [					
					'display_title_limit'    => 'yes',
				],
            ]
        );
		$this->add_control(
			'display_title_input',
			[
				'label' => esc_html__( 'Title Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,				
				'condition'   => [
					'display_title_limit'    => 'yes',
				],
			]
		);
		$this->add_control(
			'display_title_3_dots',
			[
				'label' => esc_html__( 'Display Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'after',
				'condition'   => [					
					'display_title_limit'    => 'yes',
				],
			]
		);
		$this->add_control(
			'featured_image_type',
			[
				'label'   => esc_html__( 'Featured Image Type', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					"full" => esc_html__("Full Image", 'theplus'),
					"grid" => esc_html__("Grid Image", 'theplus'),
					"custom" => esc_html__("Custom", 'theplus'),
				],
				'separator' => 'after',
				'condition' => [
					'layout' => ['carousel'],					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_car',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
				'condition' => [
					'layout' => ['carousel'],
					'featured_image_type' => ['custom']
				],
			]
		);
		$this->add_control(
			'feature_image',
			[
				'label' => esc_html__( 'Display Featured Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'style' => ['style-2'],
					'layout!' => ['metro'],
				],
			]
		);
		$this->add_control(
			'title_desc_word_break',		
			[		
				'label'   => esc_html__( 'Title & Description Word Break', 'theplus' ),		
				'type'    => Controls_Manager::SELECT,		
				'default' => 'normal',		
				'options' => [		
					"normal" => esc_html__("Normal", 'theplus'),		
					"keep-all" => esc_html__("Keep All", 'theplus'),		
					"break-all" => esc_html__("Break All", 'theplus'),		
				],		
				'separator' => 'after',						
			]		
		);
		$this->add_control(
			'display_post_category',
			[
				'label' => esc_html__( 'Display Category Post', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',				
				'condition' => [
					'style!' => ['style-1','custom']
				],
			]
		);
		$this->add_control(
			'post_category_style',
			[
				'label' => esc_html__( 'Post Category Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
				'condition'   => [
					'style!' => ['style-1','custom'],
					'display_post_category' => 'yes',
				],
			]
		);
		$this->add_control(
			'display_post_category_all',
			[
				'label' => esc_html__( 'Display All Category', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'after',
				'condition' => [
					'style!' => ['style-1','custom'],
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'display_excerpt',
			[
				'label' => esc_html__( 'Display Excerpt/Content', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition'   => [
					'style!' => ['custom'],					
				],
			]
		);		
		$this->add_control(
			'post_excerpt_count',
			[
				'label' => esc_html__( 'Excerpt/Content Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 500,
				'step' => 2,
				'default' => 30,
				'separator' => 'after',
				'condition'   => [
					'display_excerpt'    => 'yes',
					'style!' => ['custom'],	
				],
			]
		);
		$this->add_control(
			'display_thumbnail',
			[
				'label' => esc_html__( 'Display Image Size', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition'   => [
					'layout!'    => 'carousel',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'exclude' => [ 'custom' ],
				'condition'   => [
					'layout!'    => 'carousel',
					'display_thumbnail'    => 'yes',
				],
			]
		);
		$this->add_control(
			'display_post_meta',
			[
				'label' => esc_html__( 'Display Post Meta', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition'   => [
					'style!' => ['custom'],					
				],
			]
		);
		$this->add_control(
			'post_meta_tag_style',
			[
				'label' => esc_html__( 'Post Meta Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(3),				
				'condition'   => [
					'display_post_meta'    => 'yes',
					'style!' => ['custom'],	
				],
			]
		);
		$this->add_control(
			'author_prefix',
			[
				'label' => esc_html__( 'Author Prefix', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'By', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Prefix Text', 'theplus' ),
				'condition'   => [
					'display_post_meta'    => 'yes',
					'style!' => ['custom'],
					'post_meta_tag_style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'display_post_meta_date',
			[
				'label' => esc_html__( 'Display Date', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition'   => [
					'display_post_meta'    => 'yes',
					'style!' => ['custom'],	
				],
			]
		);
		$this->add_control(
			'display_post_meta_author',
			[
				'label' => esc_html__( 'Display Author', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'separator' => 'after',
				'condition'   => [
					'display_post_meta'    => 'yes',
					'style!' => ['custom'],	
				],
			]
		);
		$this->add_control(
			'display_post_meta_author_pic',
			[
				'label' => esc_html__( 'Display Author Picture', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'separator' => 'after',
				'condition'   => [
					'display_post_meta'    => 'yes',
					'style!' => ['custom'],
					'post_meta_tag_style'    => 'style-3',
				],
			]
		);
		$this->add_control(
			'display_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_control(
            'button_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'style-7',
                'options' => [
                    'style-7' => esc_html__('Style 1', 'theplus'),
                    'style-8' => esc_html__('Style 2', 'theplus'),
                    'style-9' => esc_html__('Style 3', 'theplus'),                    
                ],
				'condition' => [
					'style' => 'style-3',
					'display_button' => 'yes',
				],
            ]
        );
		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Read More', 'theplus' ),
				'condition' => [
					'style' => 'style-3',
					'display_button' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'style' => 'style-3',
					'button_style!' => ['style-7','style-9'],
					'display_button' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => true,
				'default' => 'fa fa-chevron-right',
				'condition' => [
					'style' => 'style-3',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'button_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'style' => 'style-3',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'before_after',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After', 'theplus' ),
					'before' => esc_html__( 'Before', 'theplus' ),
				],
				'condition' => [
					'style' => 'style-3',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_style!' => '',
				],
			]
		);
		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'style' => 'style-3',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button-link-wrap i.button-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-link-wrap i.button-before' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'display_theplus_quickview',
			[
				'label' => esc_html__( 'Display TP Quickview', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
				],
			]
		);
		$this->add_control(
			'tpqc',
			[
				'label'   => esc_html__( 'Quickview', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					"default" => esc_html__("Default", 'theplus'),
					"custom_template" => esc_html__("Custom Template", 'theplus'),
				],
				'condition' => [
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
					'display_theplus_quickview' => 'yes',
				],
			]
		);
		$this->add_control(
			'custom_template_select',
			[
				'label' => esc_html__( 'Template', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],				
				'options' => theplus_get_templates(),
				'condition' => [
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
					'display_theplus_quickview' => 'yes',
					'tpqc' => 'custom_template',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'filter_category',
			[
				'label' => esc_html__( 'Category Wise Filter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					//'style!' => 'custom',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
				],
			]
		);
		$this->add_control(
			'filter_category_image',
			[
				'label' => esc_html__( 'Feature Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'filter_category' => 'yes',
					'query!' => 'post',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
				],
			]
		);
		$this->add_control(
			'child_filter_category',
			[
				'label' => esc_html__( 'Child Category Filter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'filter_category' => 'yes',
					'query!' => 'post',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
				],
			]
		);
		$this->add_control(
			'filter_category_image_child',
			[
				'label' => esc_html__( 'Child Feature Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'filter_category' => 'yes',
					'child_filter_category' => 'yes',
					'query!' => 'post',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
				],
			]
		);		
		$this->add_control(
            'filter_by', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Filter by', 'theplus'),
                'default' => 'filter_by_category',
                'options' => [
                    'filter_by_category' => esc_html__('Category', 'theplus'),                    
                    'filter_by_tag' => esc_html__('Tag', 'theplus'),
                ],
				'condition' => [					
					'query'    => 'post',
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
				],
            ]
        );
		$this->add_control(
			'all_filter_category_switch',
			[
				'label' => esc_html__( 'All Filter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
					'filter_style!' => 'style-4',
				],
			]
		);
		$this->add_control(
			'all_filter_category',
			[
				'label' => esc_html__( 'All Filter Category Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All', 'theplus' ),
				'condition'   => [
					'filter_category'    => 'yes',
					'all_filter_category_switch'    => 'yes',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
					'filter_style!' => 'style-4',
				],
			]
		);
		$this->add_control(
			'all_filter_category_filter',
			[
				'label' => esc_html__( 'Filters Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Filters', 'theplus' ),
				'condition'   => [
					'filter_category'    => 'yes',					
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
					'filter_style' => 'style-4',
				],
			]
		);
		$this->add_control(
			'filter_style',
			[
				'label' => esc_html__( 'Category Filter Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
				'condition'   => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
				],
			]
		);
		$this->add_control(
			'filter_hover_style',
			[
				'label' => esc_html__( 'Filter Hover Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
				'condition'   => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
				],
			]
		);
		
		$this->add_control(
			'filter_category_align',
			[
				'label' => esc_html__( 'Filter Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,
				'condition'   => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post'],
				],
			]
		);
		$this->add_control(
			'post_extra_option',
			[
				'label' => esc_html__( 'More Post Loading Options', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_post_loading_option(),
				'separator' => 'before',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post','custom_query'],
				],
			]
		);
		$this->add_control('paginationType',
			[
				'label' => esc_html__( 'Pagination Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard' => esc_html__('Standard', 'theplus'),
					'ajaxbased' => esc_html__('Ajax Based', 'theplus'),
				],
				'separator' => 'before',
				'condition' => [
					'post_extra_option' => 'pagination',
				],
			]
		);
		//pagination style
		$this->add_control(
			'pagination_next',
			[
				'label' => esc_html__( 'Pagination Next', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true,],
				'default' => esc_html__( 'Next', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),	
				'label_block' => true,
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'pagination',
				],
			]
		);
		$this->add_control(
			'pagination_prev',
			[
				'label' => esc_html__( 'Pagination Previous', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true,],
				'default' => esc_html__( 'PREV', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),	
				'label_block' => true,
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'pagination',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .theplus-pagination' => 'justify-content: {{VALUE}};display:inline-flex;align-items:center;width:100%;',
				],
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'pagination',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'label' => esc_html__( 'Pagination Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-pagination a,{{WRAPPER}} .theplus-pagination span',
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'pagination',
				],
			]
		);
		$this->add_control(
			'pagination_color',
			[
				'label' => esc_html__( 'Pagination Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theplus-pagination a,{{WRAPPER}} .theplus-pagination span' => 'color: {{VALUE}}',
				],
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'pagination',
				],
			]
		);
		$this->add_control(
			'pagination_hover_color',
			[
				'label' => esc_html__( 'Pagination Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theplus-pagination  a:hover,{{WRAPPER}} .theplus-pagination  a:focus,{{WRAPPER}} .theplus-pagination span.current,{{WRAPPER}} .theplus-pagination a.current' => 'color: {{VALUE}};border-bottom-color: {{VALUE}}',
				],
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'pagination',
				],
			]
		);
		//load more style
		
		/*pagination for cutom query start*/
		$this->add_control(
            'cqid_pagination',
            [
				'label'   => esc_html__( 'Pagination', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition' => [
					'blogs_post_listing' => 'custom_query',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cqid_pagination_typography',
				'label' => esc_html__( 'Pagination Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-pagination a,{{WRAPPER}} .theplus-pagination span',
				'condition'   => [
					'blogs_post_listing' => 'custom_query',
					'cqid_pagination' => 'yes',
				],
			]
		);
		$this->add_control(
			'cqid_pagination_color',
			[
				'label' => esc_html__( 'Pagination Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theplus-pagination a,{{WRAPPER}} .theplus-pagination span' => 'color: {{VALUE}}',
				],
				'condition'   => [
					'blogs_post_listing' => 'custom_query',
					'cqid_pagination' => 'yes',
				],
			]
		);
		$this->add_control(
			'cqid_pagination_hover_color',
			[
				'label' => esc_html__( 'Pagination Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theplus-pagination > a:hover,{{WRAPPER}} .theplus-pagination > a:focus,{{WRAPPER}} .theplus-pagination span.current' => 'color: {{VALUE}};border-bottom-color: {{VALUE}}',
				],
				'condition'   => [
					'blogs_post_listing' => 'custom_query',
					'cqid_pagination' => 'yes',
				],
			]
		);
		/*pagination for cutom query end*/
		
		$this->add_control(
			'load_more_btn_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Load More', 'theplus' ),
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_control(
			'tp_loading_text',
			[
				'label' => esc_html__( 'Loading Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Loading...', 'theplus' ),
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => ['load_more','lazy_load']
				],
			]
		);
		$this->add_control(
			'loaded_posts_text',
			[
				'label' => esc_html__( 'All Posts Loaded Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All done!', 'theplus' ),
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => ['load_more','lazy_load']
				],
			]
		);
		$this->add_control(
			'load_more_post',
			[
				'label' => esc_html__( 'More posts on click/scroll', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 30,
				'step' => 1,
				'default' => 4,
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => ['load_more','lazy_load'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'load_more_typography',
				'label' => esc_html__( 'Load More Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter',
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'loaded_posts_typo',
				'label' => esc_html__( 'Loaded All Posts Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .plus-all-posts-loaded',
				'separator' => 'before',
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => ['load_more','lazy_load'],
				],
			]
		);
		$this->add_control(
			'load_more_border',
			[
				'label' => esc_html__( 'Load More Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'   => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		
		$this->add_control(
			'load_more_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'load_more_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_load_more_border_style' );
		$this->start_controls_tab(
			'tab_load_more_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'load_more_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'load_more_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_load_more_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'load_more_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'load_more_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
					'load_more_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'tabs_load_more_style' );
		$this->start_controls_tab(
			'tab_load_more_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_control(
			'load_more_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter' => 'color: {{VALUE}}',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_control(
			'loaded_posts_color',
			[
				'label' => esc_html__( 'Loaded Posts Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-all-posts-loaded' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => ['load_more','lazy_load'],
				],
			]
		);
		$this->add_control(
			'loading_spin_heading',
			[
				'label' => esc_html__( 'Loading Spinner ', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'lazy_load',
				],
			]
		);
		$this->add_control(
			'loading_spin_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'border-color: {{VALUE}} transparent transparent transparent',
				],				
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'lazy_load',
				],
			]
		);
		$this->add_responsive_control(
            'loading_spin_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'lazy_load',
				],
            ]
        );
		$this->add_responsive_control(
            'loading_spin_border_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'lazy_load',
				],
            ]
        );
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'load_more_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);		
		$this->add_control(
			'load_more_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'load_more_shadow',
				'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_load_more_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_control(
			'load_more_color_hover',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'load_more_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover',
				'separator' => 'after',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_control(
			'load_more_shadow_hover_options',
			[
				'label' => esc_html__( 'Hover Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'load_more_hover_shadow',
				'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover',
				'condition' => [
					'layout!' => ['carousel'],
					'blogs_post_listing!' => ['related_post'],
					'post_extra_option'    => 'load_more',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tp_list_preloader',
			[
				'label' => esc_html__( 'Preloader', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'layout' => ['grid','masonry'],
				],
			]
		);
		$this->add_responsive_control(
            'tp_list_preloader_hw',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-listing-preloader.post-inner-loop:before' => 'width:{{SIZE}}{{UNIT}} !important;height:{{SIZE}}{{UNIT}} !important;',
				],
				'render_type' => 'ui',
				'condition' => [
					'tp_list_preloader' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'tp_list_preloader_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 5,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'render_type' => 'ui',
				'condition' => [
					'tp_list_preloader' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tp_list_preloader_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-listing-preloader.post-inner-loop:before' => 'border-top: {{tp_list_preloader_size.SIZE}}{{tp_list_preloader_size.UNIT}} solid {{VALUE}}',
				],
				'condition' => [
					'tp_list_preloader' => 'yes',
				],
				
			]
		);
		$this->add_control(
            'empty_posts_message',
            [
                'label' => __('No Posts Message', 'theplus'),
                'type'  => Controls_Manager::TEXTAREA,
                'separator' => 'before',
				'default' => esc_html__( 'No Post Found', 'theplus' ),
                'description'   => '',
            ]
        );
		$this->end_controls_section();
		/*post Extra options*/
		/*post meta tag*/
		$this->start_controls_section(
            'section_meta_tag_style',
            [
                'label' => esc_html__('Post Meta Tag', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'blogs_post_listing!' => 'acf_repeater',
					'display_post_meta'    => 'yes',
					'style!' => 'custom',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_tag_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .post-meta-info span',
			]
		);
		$this->start_controls_tabs( 'tabs_post_meta_style' );
		$this->start_controls_tab(
			'tab_post_meta_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'post_meta_color',
			[
				'label' => esc_html__( 'Post Meta Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-meta-info span,{{WRAPPER}} .dynamic-listing .post-inner-loop .post-meta-info span a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_post_meta_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'post_meta_color_hover',
			[
				'label' => esc_html__( 'Post Meta Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .post-meta-info span,{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .post-meta-info span a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'post_meta_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing.dynamic-listing-style-2 .post-meta-info' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'style' => 'style-2'
				],
			]
		);
		$this->end_controls_section();
		/*post meta tag*/
		/*Post category*/
		$this->start_controls_section(
            'section_post_category_style',
            [
                'label' => esc_html__('Category Post', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'blogs_post_listing!' => 'acf_repeater',
					'display_post_category'    => 'yes',
					'style!' => ['style-1','custom'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'category_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .dynamic-listing .post-category-list span a',
				'condition'   => [
					'display_post_category' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_style' );
		$this->start_controls_tab(
			'tab_category_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'category_color',
			[
				'label' => esc_html__( 'Category Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-category-list span a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_category_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'category_hover_color',
			[
				'label' => esc_html__( 'Category Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .post-category-list span:hover a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'category_2_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff214f',
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'category_border',
			[
				'label' => esc_html__( 'Category Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'   => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		
		$this->add_control(
			'category_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_responsive_control(
			'box_category_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_border_style' );
		$this->start_controls_tab(
			'tab_category_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'category_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		
		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_category_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'category_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span:hover a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_responsive_control(
			'category_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span:hover a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'category_bg_options',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_background_style');
		$this->start_controls_tab(
			'tab_category_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_category_background_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span:hover a',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'category_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_shadow_style' );
		$this->start_controls_tab(
			'tab_category_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span a',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_category_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_hover_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .post-category-list span:hover a',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'category_inner_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-category-list.style-1 span a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'display_post_category' => 'yes',
					'post_category_style' => 'style-1',
				],
			]
		);
		$this->end_controls_section();
		/*Post category*/
		/*Post Title*/
		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style!' => 'custom',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .post-title,{{WRAPPER}} .dynamic-listing .post-inner-loop .post-title a',
			]
		);
		$this->start_controls_tabs( 'tabs_title_style' );
		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .post-title,{{WRAPPER}} .dynamic-listing .post-inner-loop .post-title a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .post-title,{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .post-title a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Title*/		
		/*Post Excerpt*/
		$this->start_controls_section(
            'section_excerpt_style',
            [
                'label' => esc_html__('Excerpt/Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'blogs_post_listing!' => 'acf_repeater',
					'display_excerpt'    => 'yes',
					'style!' => 'custom',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .entry-content,{{WRAPPER}} .dynamic-listing .post-inner-loop .entry-content p',
			]
		);
		$this->start_controls_tabs( 'tabs_excerpt_style' );
		$this->start_controls_tab(
			'tab_excerpt_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'excerpt_color',
			[
				'label' => esc_html__( 'Content Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .entry-content,{{WRAPPER}} .dynamic-listing .post-inner-loop .entry-content p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_excerpt_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'excerpt_hover_color',
			[
				'label' => esc_html__( 'Content Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .entry-content,{{WRAPPER}} .dynamic-listing .post-inner-loop .blog-list-content:hover .entry-content p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Excerpt*/
		/*Content Background*/
		$this->start_controls_section(
            'section_content_bg_style',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style!' => 'custom',
				],
            ]
        );
		$this->add_responsive_control(
            'content_between_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Content Space', 'theplus'),
				'size_units' => [ 'px' , '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3:not(.list-isotope-metro) .content-left .post-content-bottom,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3:not(.list-isotope-metro) .content-left-right .grid-item:nth-child(odd) .post-content-bottom' => 'padding-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3:not(.list-isotope-metro) .content-right .post-content-bottom,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3:not(.list-isotope-metro) .content-left-right .grid-item:nth-child(even) .post-content-bottom' => 'padding-right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'style' => 'style-3',
					'layout!' => 'metro',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_content_bg_style' );
		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'contnet_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .dynamic-listing.dynamic-listing-style-1 .post-content-bottom,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-2 .post-content-bottom,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_hover_background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .dynamic-listing.dynamic-listing-style-1 .blog-list-content:hover .post-content-bottom,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-2 .blog-list-content:hover .post-content-bottom,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'content_box_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_content_shadow_style' );
		$this->start_controls_tab(
			'tab_content_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'   => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_hover_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content:hover',
				'condition' => [					
					'style' => 'style-3',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Content Background*/
		/*Post Featured Image*/
		$this->start_controls_section(
            'section_post_image_style',
            [
                'label' => esc_html__('Featured Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style!' => 'custom',
				],
            ]
        );
		$this->add_control(
			'hover_image_style',
			[
				'label' => esc_html__( 'Image Hover Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(1,'yes'),
			]
		);
		$this->start_controls_tabs( 'tabs_image_style' );
		$this->start_controls_tab(
			'tab_image_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'overlay_image_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .dynamic-listing .blog-list-content .blog-featured-image:before,{{WRAPPER}} .dynamic-listing.list-isotope-metro .blog-list-content .blog-bg-image-metro:before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_image_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_image_hover_background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .dynamic-listing .blog-list-content:hover .blog-featured-image:before,{{WRAPPER}} .dynamic-listing.list-isotope-metro .blog-list-content:hover .blog-bg-image-metro:before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'featured_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-featured-image,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-2 .blog-featured-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'style' => ['style-2','style-3'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_image_shadow_style' );
		$this->start_controls_tab(
			'tab_image_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'style' => ['style-2','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content .blog-featured-image,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-2 .blog-list-content:hover .blog-featured-image',
				'condition' => [
					'style' => ['style-2','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_image_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'style' => ['style-2','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing.dynamic-listing-style-3 .blog-list-content:hover .blog-featured-image,{{WRAPPER}} .dynamic-listing.dynamic-listing-style-2 .blog-list-content:hover .blog-featured-image',
				'condition' => [					
					'style' => ['style-2','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'full_image_size',
			[
				'label' => esc_html__( 'Full Image', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'style' => 'style-2',
				],
			]
		);
		$this->add_control(
            'full_image_size_min_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Height', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 700,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .blog-featured-image img' => 'min-height: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}};',					
				],				
				'condition'    => [
					'style' => 'style-2',
					'full_image_size' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		/*Post Featured Image*/
		
		/*Post Featured Image*/
		$this->start_controls_section(
            'section_quickview_styling',
            [
                'label' => esc_html__('Quickview', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout!' => 'carousel',
					'blogs_post_listing!' => ['archive_listing','related_post','custom_query'],
					'display_theplus_quickview' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(	
		'qv_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-dl-quickview' => 'display:flex;justify-content:{{VALUE}};',
				],				
				'toggle' => true,
			]
		);
		$this->start_controls_tabs( 'tabs_qv_style' );
		$this->start_controls_tab(
			'tab_qv_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'qv_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} a.tp-quick-view-wrap-dl' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'qv_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} a.tp-quick-view-wrap-dl',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'qv_b',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} a.tp-quick-view-wrap-dl',
			]
		);
		$this->add_responsive_control(
			'qv_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} a.tp-quick-view-wrap-dl' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'qv_Shadow',
				'selector' => '{{WRAPPER}} a.tp-quick-view-wrap-dl',				
			]
		);
		$this->end_controls_tab();		
		$this->start_controls_tab(
			'tab_qv_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'qv_hover_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} a.tp-quick-view-wrap-dl:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'qv_bg_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} a.tp-quick-view-wrap-dl:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'qv_b_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} a.tp-quick-view-wrap-dl:hover',
			]
		);
		$this->add_responsive_control(
			'qv_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} a.tp-quick-view-wrap-dl:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'qv_Shadow_h',
				'selector' => '{{WRAPPER}} a.tp-quick-view-wrap-dl:hover',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*Post Featured Image*/
		
		/*button style*/
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Button Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'style' => 'style-3',
					'display_button' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '15',
							'right' => '30',
							'bottom' => '15',
							'left' => '30',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .pt_plus_button .button-link-wrap',
			]
		);
		
		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		
		$this->add_control(
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-7 .button-link-wrap:after' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'separator' => 'after',
				'condition' => [
					'button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);

		$this->add_responsive_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none',
				]
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'btn_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'separator' => 'after',
				'condition' => [
					'button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'button_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*button style*/
		/*Filter Category style*/
		$this->start_controls_section(
            'section_filter_category_styling',
            [
                'label' => esc_html__('Filter Category', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'filter_category' => 'yes',
				],
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_category_typography',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count,
				{{WRAPPER}} .pt-plus-filter-post-category .filters-toggle-link',
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
            'filter_category_4_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .filters-toggle-link svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'filter_style' => 'style-4',
				],				
            ]
        );
		$this->add_responsive_control(
			'filter_category_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-1 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-3 li a,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'filter_category_marign',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'filters_text_color',
			[
				'label' => esc_html__( 'Filters Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link line,{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link circle,{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link polyline' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'filter_style' => ['style-4'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_filter_color_style' );
		$this->start_controls_tab(
			'tab_filter_category_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'filter_category_color',
			[
				'label' => esc_html__( 'Category Filter Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'filter_category_4_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a:before' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'filter_hover_style' => ['style-4'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'filter_category_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a:after',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'filter_category_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'filter_category_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_category_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'filter_category_hover_color',
			[
				'label' => esc_html__( 'Category Filter Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters:not(.hover-style-2) li a:hover,{{WRAPPER}}  .pt-plus-filter-post-category .category-filters:not(.hover-style-2) li a:focus,{{WRAPPER}}  .pt-plus-filter-post-category .category-filters:not(.hover-style-2) li a.active,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'filter_category_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],				
			]
		);
		$this->add_responsive_control(
			'filter_category_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'filter_category_hover_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'filter_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-1 li a::after' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'count_filter_category_options',
			[
				'label' => esc_html__( 'Count Filter Category', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'category_count_color',
			[
				'label' => esc_html__( 'Category Count Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a span.all_post_count' => 'color: {{VALUE}}',
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_count_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'condition' => [
					'filter_style' => ['style-1'],
				],
			]
		);
		$this->add_control(
			'category_count_bg_color',
			[
				'label' => esc_html__( 'Count Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-3 a span.all_post_count' => 'background: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-3 a span.all_post_count:before' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'filter_style' => ['style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'filter_category_count_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'separator' => 'before',
				'condition' => [
					'filter_style' => ['style-1'],
				],
			]
		);
		$this->end_controls_section();
		/*Filter Category style*/
		
		/*Filter Category Image style*/
		$this->start_controls_section(
            'section_fc_image_styling',
            [
                'label' => esc_html__('Filter Category Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'filter_category' => 'yes',
					'filter_category_image' => 'yes',
				],
			]
        );
		$this->add_responsive_control(
            'fc_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .category-filters .filter-category-list img' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'fc_margin_bottom',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .category-filters .filter-category-list img' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'fc__bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .category-filters .filter-category-list img',
			]
		);		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fc__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .category-filters .filter-category-list img',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'fc__br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .category-filters .filter-category-list img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'fc__shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .category-filters .filter-category-list img',
			]
		);
		$this->end_controls_section();
		/*Filter Category Image style*/
		
		/*Child Filter Category Image style*/
		$this->start_controls_section(
            'section_fcc_image_styling',
            [
                'label' => esc_html__('Filter Child Category', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
					'filter_category' => 'yes',
					'child_filter_category' => 'yes',
				],
			]
        );
		$this->add_responsive_control(
			'fcc__margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .post-filter-data .category-filters-child' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'fcc__padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'fcc__feature_image',
			[
				'label' => esc_html__( 'Feature Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filter_category_image_child' => 'yes',
				],				
			]
		);
		$this->add_responsive_control(
            'fcc_fi_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a img' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'filter_category_image_child' => 'yes',
				],	
            ]
        );
		$this->add_responsive_control(
            'fcc_fi_margin_bottom',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a img' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'filter_category_image_child' => 'yes',
				],	
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'fcc_fi__bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a img',
				'condition' => [
					'filter_category_image_child' => 'yes',
				],	
			]
		);		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fcc_fi__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a img',
				'separator' => 'before',
				'condition' => [
					'filter_category_image_child' => 'yes',
				],	
			]
		);
		$this->add_responsive_control(
			'fcc_fi__br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filter_category_image_child' => 'yes',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'fcc_fi__shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a img',
				'condition' => [
					'filter_category_image_child' => 'yes',
				],
			]
		);
		$this->add_control(
			'fcc__cat',
			[
				'label' => esc_html__( 'Child Category', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'fcc__cat_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a',
			]
		);
		$this->start_controls_tabs( 'fcc__cat_tabs' );
		$this->start_controls_tab(
			'fcc__cat_n_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'fcc__cat_n_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a' => 'color: {{VALUE}}',
				],					
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'fcc__cat_n__bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a',
			]
		);		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fcc__cat_n__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'fcc__cat_n__br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'fcc__cat_n__shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'fcc__cat_ha_tab',
			[
				'label' => esc_html__( 'Hover/Active', 'theplus' ),					
			]
		);
		$this->add_control(
			'fcc__cat_ha_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a:hover,{{WRAPPER}} .post-filter-data .category-filters-child li a.active' => 'color: {{VALUE}}',
				],					
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'fcc__cat_ha__bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a:hover,{{WRAPPER}} .post-filter-data .category-filters-child li a.active',
			]
		);		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fcc__cat_ha__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a:hover,{{WRAPPER}} .post-filter-data .category-filters-child li a.active',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'fcc__cat_ha__br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-filter-data .category-filters-child li a:hover,{{WRAPPER}} .post-filter-data .category-filters-child li a.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'fcc__cat_ha__shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .post-filter-data .category-filters-child li a:hover,{{WRAPPER}} .post-filter-data .category-filters-child li a.active',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Child Filter Category Image style*/
		
		/*Box Loop style*/
		$this->start_controls_section(
            'section_box_loop_styling',
            [
                'label' => esc_html__('Box Loop Background Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blogs_post_listing!' => 'acf_repeater',
				],
			]
        );
		$this->add_responsive_control(
			'content_inner_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'tabs_background_style' );
		$this->start_controls_tab(
			'tab_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_background_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_active_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_shadow_style' );
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .dynamic-listing .post-inner-loop .grid-item .blog-list-content:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Box Loop style*/
		/*carousel option*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => 'carousel',
				],
            ]
        );
		$this->add_control(
			'carousel_unique_id',
			[
				'label' => esc_html__( 'Unique Carousel ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'separator' => 'after',
				'description' => esc_html__('Keep this blank or Setup Unique id for carousel which you can use with "Carousel Remote" widget.','theplus'),
			]
		);
		$this->add_control(
			'slider_direction',
			[
				'label'   => esc_html__( 'Slider Mode', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'carousel_direction',
			[
				'label' => esc_html__( 'Slide Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rtl',
				'options' => [
					'rtl'  => esc_html__( 'Right to Left', 'theplus' ),
					'ltr' => esc_html__( 'Left to Right', 'theplus' ),
				],
			]
		);	
		$this->add_control(
            'slide_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
            ]
        );
		
		$this->start_controls_tabs( 'tabs_carousel_style' );
		$this->start_controls_tab(
			'tab_carousel_desktop',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_desktop_column',
			[
				'label'   => esc_html__( 'Desktop Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_carousel_desktop_columns(),
			]
		);
		$this->add_control(
			'steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
			'slider_padding',
			[
				'label' => esc_html__( 'Slide Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '10',
					'bottom' => '',
					'left' => '10',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-initialized .slick-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'multi_drag',
			[
				'label'   => esc_html__( 'Multi Drag', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_draggable' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_pause_hover',
			[
				'label'   => esc_html__( 'Pause On Hover', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_adaptive_height',
			[
				'label'   => esc_html__( 'Adaptive Height', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slide_fade_inout',
			[
				'label' => esc_html__( 'Slide Animation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'Default', 'theplus' ),
					'fadeinout' => esc_html__( 'Fade in/Fade out', 'theplus' ),
				],
				'condition' => [
					'slider_direction' => 'horizontal',
				],
			]
		);
		$this->add_control(
			'slide_fade_inout_notice',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Just for single column layout.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'slider_direction' => 'horizontal',
					'slide_fade_inout' => 'fadeinout',
				],
			]
		);
		$this->add_control(
			'slider_animation',
			[
				'label'   => esc_html__( 'Animation Type', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ease',
				'options' => [
					'ease' => esc_html__( 'With Hold', 'theplus' ),
					'linear' => esc_html__( 'Continuous', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
		'autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3000,
				],
				'condition' => [
					'slider_autoplay' => 'yes',
				],
            ]
        );
		
		$this->add_control(
			'slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_dots_style',
			[
				'label'   => esc_html__( 'Dots Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
					'style-7' => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition'    => [
					'slider_dots' => ['yes'],
				],
			]
		);
		$this->add_control(
			'dots_border_color',
			[
				'label' => esc_html__( 'Dots Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 li button' => '-webkit-box-shadow:inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li.slick-active button' => '-webkit-box-shadow:inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button' => 'border-color:{{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-3 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-4 li button' => '-webkit-box-shadow: inset 0 0 0 0px {{VALUE}};-moz-box-shadow: inset 0 0 0 0px {{VALUE}};box-shadow: inset 0 0 0 0px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-1','style-2','style-3','style-5'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_bg_color',
			[
				'label' => esc_html__( 'Dots Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button,{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 button' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-3','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_border_color',
			[
				'label' => esc_html__( 'Dots Active Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 .slick-active button:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-6'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_bg_color',
			[
				'label' => esc_html__( 'Dots Active Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 .slick-active button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 .slick-active button' => 'background: {{VALUE}};',					
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
            'dots_top_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Dots Top Padding', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slider.slick-dotted' => 'padding-bottom: {{SIZE}}{{UNIT}};',					
				],				
				'condition'    => [
					'slider_dots' => 'yes',
				],
            ]
        );
		$this->add_control(
			'hover_show_dots',
			[
				'label'   => esc_html__( 'On Hover Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_arrows_style',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'condition'    => [
					'slider_arrows' => ['yes'],
				],
			]
		);
		$this->add_control(
			'arrows_position',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right' => esc_html__( 'Top-Right', 'theplus' ),
					'bottm-left' => esc_html__( 'Bottom-Left', 'theplus' ),
					'bottom-center' => esc_html__( 'Bottom-Center', 'theplus' ),
					'bottom-right' => esc_html__( 'Bottom-Right', 'theplus' ),
				],				
				'condition'    => [
					'slider_arrows' => ['yes'],
					'slider_arrows_style' => ['style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'arrow_bg_color',
			[
				'label' => esc_html__( 'Arrow Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-6:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-6:before' => 'background: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-3','style-4','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_icon_color',
			[
				'label' => esc_html__( 'Arrow Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6 .icon-wrap' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => esc_html__( 'Arrow Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_icon_color',
			[
				'label' => esc_html__( 'Arrow Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6:hover .icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_section_arrow',
			[
				'label'   => esc_html__( 'Outer Content Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
					'slider_arrows_style' => ['style-1','style-2','style-5','style-6'],
				],
			]
		);
		$this->add_control(
			'hover_show_arrow',
			[
				'label'   => esc_html__( 'On Hover Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
            ]
        );
		$this->add_control(
			'slider_center_effects',
			[
				'label'   => esc_html__( 'Center Slide Effects', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_carousel_center_effects(),
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
			]
		);
		$this->add_control(
            'scale_center_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center,
					{{WRAPPER}} .list-carousel-slick .slick-slide.scc-animate' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});opacity:1;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_control(
            'scale_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.8,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});transition: .3s all linear;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'shadow_active_slide',
				'selector' => '{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center .blog-list-content',
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'shadow',
				],
			]
		);
		$this->add_control(
            'opacity_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Opacity', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.7,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => 'opacity:{{SIZE}}',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects!' => 'none',
				],
            ]
        );
		$this->add_control(
			'slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'slide_row_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Row Top Space', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick[data-slider_rows="2"] .slick-slide > div:last-child,{{WRAPPER}} .list-carousel-slick[data-slider_rows="3"] .slick-slide > div:nth-last-child(-n+2)' => 'padding-top:{{SIZE}}px',
				],
				'condition'    => [
					'slider_rows' => ['2','3'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_tablet_column',
			[
				'label'   => esc_html__( 'Tablet Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_tablet_columns(),
			]
		);
		$this->add_control(
			'tablet_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		
		$this->add_control(
			'slider_responsive_tablet',
			[
				'label'   => esc_html__( 'Responsive Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tablet_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_tablet' => 'yes',
					'tablet_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tablet_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
					'tablet_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_mobile_column',
			[
				'label'   => esc_html__( 'Mobile Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_carousel_mobile_columns(),
			]
		);
		$this->add_control(
			'mobile_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		
		$this->add_control(
			'slider_responsive_mobile',
			[
				'label'   => esc_html__( 'Responsive Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'mobile_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_mobile' => 'yes',
					'mobile_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'mobile_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
					'mobile_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*carousel option*/
		
		/*post not found options*/
		$this->start_controls_section(
            'section_post_not_found_styling',
            [
                'label' => esc_html__('Post Not Found Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'pnf_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-posts-not-found' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pnf_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .theplus-posts-not-found',
				
			]
		);
		$this->add_control(
			'pnf_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theplus-posts-not-found' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pnf_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-posts-not-found',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pnf_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-posts-not-found',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pnf_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-posts-not-found' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'pnf_shadow',
			'label' => esc_html__( 'Box Shadow', 'theplus' ),
			'selector' => '{{WRAPPER}} .theplus-posts-not-found',
			'separator' => 'before',
		]
		);	
		$this->end_controls_section();
		/*post not found options*/
		
		/*Extra options*/
		$this->start_controls_section(
            'section_extra_options_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'messy_column',
			[
				'label' => esc_html__( 'Messy Columns', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->start_controls_tabs( 'tabs_extra_option_style' );
		$this->start_controls_tab(
			'tab_column_1',
			[
				'label' => esc_html__( '1', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_1',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 1', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_2',
			[
				'label' => esc_html__( '2', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_2',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 2', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_3',
			[
				'label' => esc_html__( '3', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_3',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 3', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_4',
			[
				'label' => esc_html__( '4', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_4',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 4', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_5',
			[
				'label' => esc_html__( '5', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_5',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 5', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_6',
			[
				'label' => esc_html__( '6', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_6',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 6', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*Extra options*/

		/*--On Scroll View Animation ---*/
		$Plus_Listing_block = "Plus_Listing_block";
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
	}
	
	protected function render() {
        $settings = $this->get_settings_for_display();
		$query_args = $this->get_query_args();
		$query = new \WP_Query( $query_args );

		$style=$settings["style"];
		$layout=$settings["layout"];
		$post_title_tag=$settings["post_title_tag"];
		$ListingType = !empty($settings['blogs_post_listing']) ? $settings['blogs_post_listing'] : 'page_listing';
		$acf_repeater_field = !empty($settings['acf_repeater_field']) ? $settings['acf_repeater_field'] : '';
		$acfFieldData = '';
		$paginationType = !empty($settings["paginationType"]) ? $settings["paginationType"] : 'standard';

		if($acf_repeater_field == 'default'){
			$acfFieldData = !empty($settings['acf_repeater_field_list']) ? $settings['acf_repeater_field_list'] : '';
		}else if($acf_repeater_field == 'custom'){
			$acfFieldData = !empty($settings['acf_filed_name']) ? $settings['acf_filed_name'] : '';
		}

		$author_prefix = !empty($settings["author_prefix"]) ? $settings["author_prefix"] : 'By';

		$display_title_limit=$settings["display_title_limit"];
		$display_title_by=$settings["display_title_by"];
		$display_title_input=$settings["display_title_input"];
		$display_title_3_dots=$settings["display_title_3_dots"];
		$dpc_all=$settings["display_post_category_all"];
		
		$display_theplus_quickview=isset($settings['display_theplus_quickview']) ? $settings['display_theplus_quickview'] : 'no';
		
		$title_desc_word_break=(!empty($settings["title_desc_word_break"])) ? $settings["title_desc_word_break"] : '';
		
		$post_category=$settings['post_category'];
		$post_tags=$settings['post_tags'];
		$filter_by=$settings['filter_by'];

		$include_posts = !empty($settings['include_posts']) ? $settings['include_posts'] : '';
		$exclude_posts = !empty($settings['exclude_posts']) ? $settings['exclude_posts'] : '';
		$MorePostOptions = !empty($settings['post_extra_option']) ? $settings['post_extra_option'] : "none";
		
		$temp_array=array();
		if($style=='custom' && !empty($settings['skin_template'])){
			$temp_array[]=$settings['skin_template'];
		}
		if(!empty($settings['multiple_skin_enable']) && $settings['multiple_skin_enable'] == 'yes'){			
			if($style=='custom' && !empty($settings['skin_template2'])){
				$temp_array[]=$settings['skin_template2'];
			}
			if($style=='custom' && !empty($settings['skin_template3'])){
				$temp_array[]=$settings['skin_template3'];
			}
			if($style=='custom' && !empty($settings['skin_template4'])){
				$temp_array[]=$settings['skin_template4'];
			}
			if($style=='custom' && !empty($settings['skin_template5'])){
				$temp_array[]=$settings['skin_template5'];
			}
		}
		$array_key = array_keys($temp_array);
		$array_value = array_values($temp_array);
		if($settings['template_order']=='default'){
			$rv = $array_value;
			$temp_array = array_combine($array_key, $rv);
		}else if($settings['template_order']=='reverse'){
			$rv = array_reverse($array_value);
			$temp_array = array_combine($array_key, $rv);
		}else if($settings['template_order']=='random'){
			$temp_array = $array_value;
			shuffle($temp_array);
		}
		
		$display_thumbnail=$settings['display_thumbnail'];
		$thumbnail=$settings['thumbnail_size'];
		$thumbnail_car=$settings['thumbnail_car_size'];	
		
		$full_image_size=isset($settings['full_image_size']) ? $settings['full_image_size'] : 'yes';
		$featured_image_type=(!empty($settings["featured_image_type"])) ? $settings["featured_image_type"] : 'full';
		
		$display_post_meta=$settings["display_post_meta"];
		$post_meta_tag_style=$settings["post_meta_tag_style"];
		$display_post_meta_date=!empty($settings["display_post_meta_date"]) ? $settings["display_post_meta_date"] : 'no';
		$display_post_meta_author=!empty($settings["display_post_meta_author"]) ? $settings["display_post_meta_author"] : 'no';		
		$display_post_meta_author_pic=!empty($settings["display_post_meta_author_pic"]) ? $settings["display_post_meta_author_pic"] : 'no';
		
		$display_excerpt=$settings["display_excerpt"];
		$post_excerpt_count=!empty($settings["post_excerpt_count"]) ? $settings["post_excerpt_count"] : 30;
		
		$feature_image=$settings["feature_image"];
		
		$display_post_category=$settings["display_post_category"];
		$post_category_style=$settings["post_category_style"];
		
		$content_alignment_3=($settings["content_alignment_3"]!='') ? 'content-'.$settings["content_alignment_3"] : '';
		
		$content_alignment=($settings["content_alignment"]!='') ? 'text-'.$settings["content_alignment"] : '';
		$style_layout=($settings["style_layout"]!='') ? 'layout-'.$settings["style_layout"] : '';
		
		$button_style = $settings['button_style'];
		$before_after = $settings['before_after'];
		$button_text = $settings['button_text'];
		$button_icon_style = $settings['button_icon_style'];
		$button_icon = $settings['button_icon'];
		$button_icons_mind = $settings['button_icons_mind'];

		$carousel_direction=$carousel_slider='';
		if($layout=='carousel'){
			$carousel_direction = !empty($settings['carousel_direction']) ? $settings['carousel_direction'] : 'rtl';
		
			if ( !empty($carousel_direction) ) {
				$carousel_data = array(
					'carousel_direction' => $carousel_direction,
				);
	
				$carousel_slider = 'data-result="' . htmlspecialchars(wp_json_encode($carousel_data, true), ENT_QUOTES, 'UTF-8') . '"';
			}
		}

		//animation load
		/*--On Scroll View Animation ---*/
		$Plus_Listing_block = "Plus_Listing_block";
		$animated_columns='';
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
		
		//columns
		$desktop_class=$tablet_class=$mobile_class='';
		if($layout!='carousel' && $layout!='metro'){
			if($settings['desktop_column']=='5'){
				$desktop_class='theplus-col-5';
			}else{
				$desktop_class='tp-col-lg-'.esc_attr($settings['desktop_column']);
			}
			$tablet_class='tp-col-md-'.esc_attr($settings['tablet_column']);
			$mobile_class='tp-col-sm-'.esc_attr($settings['mobile_column']);
			$mobile_class .=' tp-col-'.esc_attr($settings['mobile_column']);
		}
		
		//layout
		$layout_attr=$data_class='';
		if($layout!=''){
			$data_class .=theplus_get_layout_list_class($layout);
			$layout_attr=theplus_get_layout_list_attr($layout);
		}else{
			$data_class .=' list-isotope';
		}
		if($layout=='metro'){
			$metro_columns=$settings['metro_column'];
			$layout_attr .=' data-metro-columns="'.esc_attr($metro_columns).'" ';
			if(isset($settings["metro_style_".$metro_columns]) && !empty($settings["metro_style_".$metro_columns])){
				$layout_attr .=' data-metro-style="'.esc_attr($settings["metro_style_".$metro_columns]).'" ';
			}
			if(!empty($settings["responsive_tablet_metro"]) && $settings["responsive_tablet_metro"]=='yes'){
				$tablet_metro_column=$settings["tablet_metro_column"];
				$layout_attr .=' data-tablet-metro-columns="'.esc_attr($tablet_metro_column).'" ';
				if(isset($settings["tablet_metro_style_".$tablet_metro_column]) && !empty($settings["tablet_metro_style_".$tablet_metro_column])){
					$layout_attr .=' data-tablet-metro-style="'.esc_attr($settings["tablet_metro_style_".$tablet_metro_column]).'" ';
				}
			}
		}
		
		$data_class .=' dynamic-listing-'.$style;
		$data_class .=' hover-image-'.$settings["hover_image_style"];
		
		$output=$data_attr='';
		
		//carousel
		if($layout=='carousel'){
			if(!empty($settings["hover_show_dots"]) && $settings["hover_show_dots"]=='yes'){
				$data_class .=' hover-slider-dots';
			}
			if(!empty($settings["hover_show_arrow"]) && $settings["hover_show_arrow"]=='yes'){
				$data_class .=' hover-slider-arrow';
			}
			if(!empty($settings["outer_section_arrow"]) && $settings["outer_section_arrow"]=='yes' && ($settings["slider_arrows_style"]=='style-1' || $settings["slider_arrows_style"]=='style-2' || $settings["slider_arrows_style"]=='style-5' || $settings["slider_arrows_style"]=='style-6')){
				$data_class .=' outer-slider-arrow';
			}
			$data_attr .=$this->get_carousel_options();
			if($settings["slider_arrows_style"]=='style-3' || $settings["slider_arrows_style"]=='style-4'){
				$data_class .=' '.$settings["arrows_position"];
			}
			if(($settings["slider_rows"] > 1) || ($settings["tablet_slider_rows"] > 1) || ($settings["mobile_slider_rows"] > 1)){
				$data_class .= ' multi-row';
			}
		}
		if($settings['filter_category']=='yes'){
			$data_class .=' pt-plus-filter-post-category ';
		}
		
		$kij=0;$ji=1;$ij='';
		$uid=uniqid("post");		
		if(!empty($settings["carousel_unique_id"])){
			$uid="tpca_".$settings["carousel_unique_id"];
			$data_attr .= ' data-carousel-bg-conn="bgcarousel'.esc_attr($settings["carousel_unique_id"]).'"';
		}
		$data_attr .=' data-id="'.esc_attr($uid).'"';
		$data_attr .=' data-style="'.esc_attr($style).'"';
		$tablet_metro_class=$tablet_ij='';
		
		if ( ! $query->have_posts() && $settings["blogs_post_listing"] != 'acf_repeater' ) {			
			$output .= '<h3 class="theplus-posts-not-found">'.$settings['empty_posts_message'].'</h3>';
		}else{
			$child_filter_class='';
			if(!empty($settings['child_filter_category']) && $settings['child_filter_category']=='yes'){
				$child_filter_class=' tp-child-filter-enable';
			}
			
			//Extra Options pagination/load more/lazy load
			$metro_style=$tablet_metro_style=$tablet_metro_column=$responsive_tablet_metro='no';
			if($layout=='metro'){
				$metro_columns=$settings['metro_column'];
				if(!empty($settings["metro_style_".$metro_columns])){
					$metro_style=$settings["metro_style_".$metro_columns];
				}
				$responsive_tablet_metro=(!empty($settings["responsive_tablet_metro"])) ? $settings["responsive_tablet_metro"] : 'no';
				$tablet_metro_column=$settings["tablet_metro_column"];
				if(!empty($settings["tablet_metro_style_".$tablet_metro_column])){
					$tablet_metro_style=$settings["tablet_metro_style_".$tablet_metro_column];
				}
			}
	
			$button_attr = [];
			if($settings['display_button'] == 'yes'){
				$button_attr = [
					'display_button' => $settings['display_button'],
					'button_style' => $settings['button_style'],
					'before_after' => $settings['before_after'],
					'button_text' => $settings['button_text'],
					'button_icon_style' => $settings['button_icon_style'],
					'button_icon' => $settings['button_icon'],
					'button_icons_mind' => $settings['button_icons_mind'],
				];
			}
			
			if(!empty($post_category) && is_array($post_category)){
				$post_category=implode(',', $post_category);
			}else{
				$post_category='';
			}

			if(!empty($post_tags)  && is_array($post_tags)){
				$post_tags=implode(',', $post_tags);
			}else{
				$post_tags='';
			}
			
			$post_authors ='';
			if(!empty($settings["blogs_post_listing"]) && $settings["blogs_post_listing"]=='archive_listing'){
				global $wp_query;
				$query_var = $wp_query->query_vars;
				$post_category= $query_var['cat'];
				$post_tags= $query_var['tag_id'];
				$post_authors = $query_var["author"];
				
				if(isset($query_var[$settings['post_taxonomies']]) && $settings['query']!=='post'){		
					$post_category =$query_var[$settings['post_taxonomies']];
				}
				
			}
			
			if($query->found_posts !=''){
				$total_posts=$query->found_posts;
				$post_offset = ($settings['post_offset']!='') ? $settings['post_offset'] : 0;
				$display_posts = ($settings['display_posts']!='') ? $settings['display_posts'] : 0;
				$offset_posts=intval($display_posts + $post_offset);
				$total_posts= intval($total_posts - $offset_posts);				
				if($total_posts!=0 && $settings['load_more_post']!=0){
					$load_page= ceil($total_posts/$settings['load_more_post']);
				}else{
					$load_page=1;
				}
				$load_page=$load_page+1;
			}else{
				$load_page=1;
			}
				
			$dynamic_cat=$settings['post_category']='';
			if($settings['query']=='post'){
				$dynamic_cat=$post_category;
			}else{
				$dynamic_cat=$settings['include_slug'];
			}
			
			if($settings['query']=='post'){		
				$get_taxonomy = 'cat';
				if($filter_by == 'filter_by_category'){
					$get_taxonomy = 'cat';
				}else if($filter_by == 'filter_by_tag'){
					$get_taxonomy = 'post_tag';
				}
			}else{
				$get_taxonomy = $settings['post_taxonomies'];
			}

			$CategoryType = !empty($settings['post_category']) ? 'true' : 'false';
			$FilterType = !empty($settings["blogs_post_listing"]) ? $settings["blogs_post_listing"] : '';
			
			$Custom_QID=$SearchGrid='';
			$is_archive=$is_search=0;
			$ArchivePage=$SearchPage=[];
			if(!empty($ListingType) && ($ListingType == 'search_list') ){
				$Custom_QID = !empty($settings["extra_query_id_search"]) ? $settings["extra_query_id_search"] : '';
				if(is_archive()){
					global $wp_query;
					$query_var = $wp_query->query_vars;
					$post_category = $query_var['cat'];
					$post_tags = $query_var['tag_id'];
					$post_authors = $query_var["author"];

					if(isset($query_var[$settings['post_taxonomies']]) && $settings['query'] !== 'post'){		
						$post_category = $query_var[$settings['post_taxonomies']];
					}
					$GetId=0;
					$PostName=$GetType='';
					if( !empty($post_category) ){
						$GetId = $post_category;
						$PostName = 'category';
					}else if( !empty($post_tags) ){
						$GetId = $post_tags;
						$PostName = 'post_tag';
					}else{
						$GetType = (!empty($wp_query) && !empty($wp_query->queried_object) && !empty($wp_query->queried_object->taxonomy) ) ? $wp_query->queried_object->taxonomy : ''; 
						$PostName = (!empty($wp_query) && !empty($wp_query->queried_object) && !empty($wp_query->queried_object->slug) ) ? $wp_query->queried_object->slug : ''; 
						$GetId = (!empty($wp_query) && !empty($wp_query->queried_object) && !empty($wp_query->queried_object->term_id) ) ? $wp_query->queried_object->term_id : ''; 			
					}

					if( !empty($GetId) ){
						if( !empty($dynamic_cat) ){
							/** features */
						}else{
							$dynamic_cat = $GetId;
							$CategoryType = 'true';
						}
					}

					$is_archive=1;
					$ArchivePage = array(
						'archive_Type' => ($GetType) ? $GetType : '',
						'archive_Name' => ($PostName) ? $PostName : 'category',
						'archive_Id' => ($GetId) ? $GetId : 0,
					);
					
				}
				
				if( is_search() ){
					$is_search=1;
					$search = get_query_var('s');
					$SearchPage = array(
						'is_search_value' => ($search) ? $search : '',
					);
					
				}

				$SearchGrid = "tp-searchlist";
			}

			$postattr=[];
				$data_loadkey=$serchAttr=$TotProduct=$TotCount='';			
			if( (($MorePostOptions == 'load_more' || $MorePostOptions == 'lazy_load' ) && $layout != 'carousel') || ( $FilterType == 'search_list' ) || ( $paginationType == 'ajaxbased' ) ){
				
				$postattr =[
					'load' => 'dynamiclisting',
					'layout' => esc_attr($layout),
					'offset-posts' => esc_attr($settings['post_offset']),
					'offset_posts' => esc_attr($settings['post_offset']),
					'display_post' => esc_attr($settings['display_posts']),
					'post_load_more' => esc_attr($settings['load_more_post']),
					'post_type' => esc_attr($settings['query']),
					'texonomy_category' => esc_attr($get_taxonomy),
					'post_title_tag' => esc_attr($post_title_tag),
					'author_prefix' => esc_attr($author_prefix),
					'title_desc_word_break' => esc_attr($title_desc_word_break),
					'include_posts' => esc_attr($include_posts),
					'exclude_posts' => esc_attr($exclude_posts),
					'style' => esc_attr($style),
					'style_layout' => esc_attr($style_layout),
					'desktop-column' => esc_attr($settings['desktop_column']),
					'tablet-column' => esc_attr($settings['tablet_column']),
					'mobile-column' => esc_attr($settings['mobile_column']),
					'metro_column' => esc_attr($settings['metro_column']),
					'metro_style' => esc_attr($metro_style),
					'responsive_tablet_metro' => esc_attr($responsive_tablet_metro),
					'tablet_metro_column' => esc_attr($tablet_metro_column),
					'tablet_metro_style' => esc_attr($tablet_metro_style),
					'category_type' => esc_attr($CategoryType),
					'category' => esc_attr($dynamic_cat),
					'post_tags' => esc_attr($post_tags),
					'post_authors' => esc_attr($post_authors),
					'order_by' => esc_attr($settings['post_order_by']),
					'post_order' => esc_attr($settings['post_order']),
					'filter_category' => esc_attr($settings['filter_category']),
					'animated_columns' => esc_attr($animated_columns),
					'display_post_meta' => esc_attr($display_post_meta),
					'post_meta_tag_style' => esc_attr($post_meta_tag_style),
					'display_post_meta_date' => esc_attr($display_post_meta_date),
					'display_post_meta_author' => esc_attr($display_post_meta_author),
					'display_post_meta_author_pic' => esc_attr($display_post_meta_author_pic),
					'display_excerpt' => esc_attr($display_excerpt),
					'post_excerpt_count' => esc_attr($post_excerpt_count),
					'display_post_category' => esc_attr($display_post_category),
					'dpc_all' => esc_attr($dpc_all),
					'post_category_style' => esc_attr($post_category_style),
					'featured_image_type' => esc_attr($featured_image_type),
					'display_thumbnail' => esc_attr($display_thumbnail),
					'thumbnail' => esc_attr($thumbnail),
					'thumbnail_car' => esc_attr($thumbnail_car),
					'display_title_limit' => esc_attr($display_title_limit),
					'display_title_by' => esc_attr($display_title_by),
					'display_title_input' => esc_attr($display_title_input),
					'display_title_3_dots' => esc_attr($display_title_3_dots),
					'feature_image' => esc_attr($feature_image),
					'display_theplus_quickview' => esc_attr($display_theplus_quickview),
					'full_image_size' => esc_attr($full_image_size),
					'theplus_nonce' => wp_create_nonce("theplus-addons"),
					'paginationType' => esc_attr($paginationType),
					
					'is_search' => $is_search,
					'is_search_page' => $SearchPage,
					
					'is_archive' => $is_archive,
					'archive_page' => $ArchivePage,
					'custon_query' => $Custom_QID,
					
					'skin_template' => $temp_array,
					'loadmoretxt' => $settings['load_more_btn_text'],					
					'No_PostFound' => $settings['empty_posts_message'],
					'listing_type'=> $ListingType,
				];
				$postattr = array_merge($postattr, $button_attr);

				if( $MorePostOptions == "pagination" ){
					$PaginationNext = !empty($settings['pagination_next']) ? $settings['pagination_next'] : 'NEXT';
					$PaginationPrev = !empty($settings['pagination_prev']) ? $settings['pagination_prev'] : 'PREV';

					$PageArray = array(
						'page_next' => $PaginationNext,
						'page_prev' => $PaginationPrev,
					);
					$postattr = array_merge($postattr, $PageArray);
					
					if( 'ajaxbased' === $paginationType ) {
						$PostType = !empty($settings['query']) ? $settings['query'] : 'post';
						$PostTaxonomies = !empty($settings['post_taxonomies']) ? $settings['post_taxonomies'] : '';
						$IncludeSlug = !empty($settings['include_slug']) ? $settings['include_slug'] : '';

						if( 'post' === $PostType ){
							if( !empty($post_category) ){
								$postattr = array_merge($postattr, array('texo_category' => "category"));
							}

							if( !empty($post_tags) ){
								$postattr = array_merge($postattr, array('texo_post_tag' => "post_tag"));
							}
						}else{
							if( !empty($PostTaxonomies) ){
								$postattr = array_merge($postattr, array('texo_post_taxonomies' => $PostTaxonomies));
							}

							if( !empty($IncludeSlug) ){
								$postattr = array_merge($postattr, array('texo_include_slug' => $IncludeSlug));
							}
						}
					}
				}

				$data_loadkey= tp_plus_simple_decrypt( json_encode($postattr), 'ey' );
				$postOffset = !empty($settings['post_offset']) ? $settings['post_offset'] : 0;
				if($FilterType == 'search_list' || $paginationType == 'ajaxbased' ){
					$serchAttr = 'data-searchAttr= \''.json_encode($postattr).'\' ';

					$TotProductQr = wp_count_posts($type = $settings['query']);
					$TTProduct = json_decode(json_encode($TotProductQr), true);
					$TotProduct = 'data-total-result="'.$TTProduct['publish'].'"';
					$proCount = $query->found_posts - $postOffset;
					$TotCount = 'data-total-count="'.$proCount.'"';
				}
			}
			
			if(!empty($settings['display_theplus_quickview']) && $settings['display_theplus_quickview']=='yes'){
				$data_attr .=' data-qvquery="'.$settings['query'].'"';
				if(!empty($settings['tpqc']) && $settings['tpqc']=='custom_template' && !empty($settings['custom_template_select'])){
					$data_attr .=' data-customtemplateqcw="yes"';
					$data_attr .=' data-templateqcw="'.$settings['custom_template_select'].'"';								
				}
			}
			$output .= '<div id="pt-plus-dynamic-listing" class="dynamic-listing '.esc_attr($uid).' '.esc_attr($data_class).' '.esc_attr($style_layout).' '.$animated_class.' '.$child_filter_class.' tp-dy-l-type-'.esc_attr($settings["blogs_post_listing"]).' '.esc_attr($SearchGrid).'" '.$layout_attr.' '.$data_attr.' '.$animation_attr.' '.$carousel_slider.' dir='.esc_attr($carousel_direction).' data-enable-isotope="1">';			
			//category filter
			if($settings['filter_category']=='yes'){
				$output .= $this->get_filter_category();
			}
			$preloader_cls='';
			if(($settings['layout']=='grid' || $settings['layout']=='masonry') && (!empty($settings['tp_list_preloader']) && $settings['tp_list_preloader']=='yes')){
				$preloader_cls = 'tp-listing-preloader';
			}

			$ajaxclass = '';
			if('ajaxbased' === $paginationType){
				$ajaxclass = 'tp-ajax-paginate-wrapper';
			}
			$output .= '<div id="'.esc_attr($uid).'" class="tp-row post-inner-loop  '.esc_attr($ajaxclass).' '.esc_attr($uid).' '.esc_attr($content_alignment).' '.esc_attr($content_alignment_3).' '.$preloader_cls.' tp_list" '.$serchAttr.' '.$TotProduct.' '.$TotCount.'"  data-widgetId = '.esc_attr($this->get_id()).'>';
				
			if( $settings["blogs_post_listing"] != 'acf_repeater' ){
				while ( $query->have_posts() ) {
				
					$query->the_post();
					$post = $query->post;
					
					//read more button
					$the_button=$button_attr='';
					if($settings['display_button'] == 'yes'){
						$button_attr='button'.$ji;
						if ( ! empty( get_the_permalink() ) ) {
							$this->add_render_attribute( $button_attr, 'href', get_the_permalink() );
							$this->add_render_attribute( $button_attr, 'rel', 'nofollow' );						
						}
						
						$this->add_render_attribute( $button_attr, 'class', 'button-link-wrap' );
						$this->add_render_attribute( $button_attr, 'role', 'button' );
						
						$button_style = $settings['button_style'];
						$button_text = $settings['button_text'];
						$btn_uid=uniqid('btn');
						$data_class= $btn_uid;
						$data_class .=' button-'.$button_style.' ';
						
						$the_button ='<div class="pt-plus-button-wrapper">';
							$the_button .='<div class="button_parallax">';
								$the_button .='<div class="ts-button">';
									$the_button .='<div class="pt_plus_button '.$data_class.'">';
										$the_button .= '<div class="animted-content-inner">';
											$the_button .='<a '.$this->get_render_attribute_string( $button_attr ).'>';
											$the_button .= include THEPLUS_PATH. 'includes/dynamic-listing/post-button.php'; 
											$the_button .='</a>';
										$the_button .='</div>';
									$the_button .='</div>';
								$the_button .='</div>';
							$the_button .='</div>';
						$the_button .='</div>';	
					}
					
					if($layout=='metro'){
						$metro_columns=$settings['metro_column'];
						if(!empty($settings["metro_style_".$metro_columns])){
							$ij=theplus_metro_style_layout($ji,$settings['metro_column'],$settings["metro_style_".$metro_columns]);
						}
						if(!empty($settings["responsive_tablet_metro"]) && $settings["responsive_tablet_metro"]=='yes'){
							$tablet_metro_column=$settings["tablet_metro_column"];
							if(!empty($settings["tablet_metro_style_".$tablet_metro_column])){
								$tablet_ij=theplus_metro_style_layout($ji,$settings['tablet_metro_column'],$settings["tablet_metro_style_".$tablet_metro_column]);
								$tablet_metro_class ='tb-metro-item'.esc_attr($tablet_ij);
							}
						}
					}
					
					//category filter
					$category_filter='';
					if($settings['filter_category']=='yes'){
						if($settings['query']=='post'){	
							if($filter_by == 'filter_by_category'){
								$terms = get_the_terms( $query->ID,'category');
							}else if($filter_by == 'filter_by_tag'){
								$terms = get_the_terms( $query->ID,'post_tag');
							}
						}else{
							$terms = get_the_terms( $query->ID,$settings['post_taxonomies']);
						}

						if ( $terms != null ){
							foreach( $terms as $term ) {
								$category_filter .=' '.esc_attr($term->slug).' ';							
								unset($term);
							}
						}
					}
					
					$template_id='';				
					if(!empty($temp_array)){
						$count=count($temp_array);					
						$value = $kij%$count;					
						$template_id=$temp_array[$value];
					}
					$post_type = $settings['query'];
					$texonomy_category = $settings['post_taxonomies'];
				
					//grid item loop
					$output .= '<div class="grid-item metro-item'.esc_attr($ij).' '.esc_attr($tablet_metro_class).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$category_filter.' '.$animated_columns.'">';				
					if(!empty($style)){
						ob_start();
						include THEPLUS_PATH. 'includes/dynamic-listing/dl-' . sanitize_file_name($style) . '.php'; 
						$output .= ob_get_contents();
						ob_end_clean();
					}
					$output .='</div>';

					$ji++;$kij++;
				}
			}else if(class_exists('acf') && $settings["blogs_post_listing"] == 'acf_repeater' && !empty($settings['skin_template'])){
				//ACF Repeater Fields
				$post_data = array();
				if(!isset($GLOBALS['post'])){
					return $post_data;
				}
				
				$template_with_css = false;
				if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
					$template_with_css = true;
				}
				
				$preview_id = '';
				if(class_exists('ElementorPro\Modules\ThemeBuilder\Module') && $GLOBALS['post']->post_type == 'elementor_library'){
					$post_id = $GLOBALS['post']->ID;
					$preview_id = get_post_meta($post_id,'tp_preview_post',true);
					if ($preview_id != '' && $preview_id != 0):
						$post_data = get_post($preview_id);
					else:
						$args = array(
							'post_type' => 'post',
							'post_status' => 'publish',
							'posts_per_page' => 1
						);
						$get_data = get_posts( $args );
						$post_data = $get_data[0];
					endif;
				}else{
					$post_data = $GLOBALS['post'];
				}
				
				if(empty($post_data)){
					$post_data = get_post(0);
				}

				if( have_rows($acfFieldData , $post_data->ID)){
					while( have_rows($acfFieldData , $post_data->ID) ) {
						the_row();

						if($layout=='metro'){
							$metro_columns=$settings['metro_column'];
							if(!empty($settings["metro_style_".$metro_columns])){
								$ij=theplus_metro_style_layout($ji,$settings['metro_column'],$settings["metro_style_".$metro_columns]);
							}
							if(!empty($settings["responsive_tablet_metro"]) && $settings["responsive_tablet_metro"]=='yes'){
								$tablet_metro_column=$settings["tablet_metro_column"];
								if(!empty($settings["tablet_metro_style_".$tablet_metro_column])){
									$tablet_ij=theplus_metro_style_layout($ji,$settings['tablet_metro_column'],$settings["tablet_metro_style_".$tablet_metro_column]);
									$tablet_metro_class ='tb-metro-item'.esc_attr($tablet_ij);
								}
							}
						}
						
						$field_check = true;
						if((!empty($settings['acf_repeater_field_conditions']) && $settings['acf_repeater_field_conditions']=='yes') && !empty($settings['acf_rf_name'])){							
							$field_value = get_sub_field($settings['acf_rf_name']);
							if ( !$field_value ) {
								$field_check = false;
							}
						}
						if(!empty($field_check)){
							$output .= '<div class="grid-item metro-item'.esc_attr($ij).' '.esc_attr($tablet_metro_class).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$animated_columns.'">';
								$output .= Theplus_Element_Load::elementor()->frontend->get_builder_content( $settings['skin_template'],$template_with_css );
							$output .='</div>';
						}
						$ji++;$kij++;
					}
				}else{
					if(!empty($settings['empty_posts_message'])){
						$output .= '<h3 class="theplus-posts-not-found">'.$settings['empty_posts_message'].'</h3>';
					}else{
						$output .='';
					}
				}
			}else{
				$output .='<h3 class="theplus-posts-not-found">'.esc_html__( "Please select a template", "theplus" ).'</h3>';
			}
			$output .='</div>';
			//$output .='</div>';
			
			$loaded_posts_text = (!empty($settings['loaded_posts_text'])) ? $settings['loaded_posts_text'] : 'All done!';
			$tp_loading_text = (!empty($settings['tp_loading_text'])) ? $settings['tp_loading_text'] : 'Loading...';
			
			if(($settings['post_extra_option']=='pagination' || ($settings['cqid_pagination'] && $settings['cqid_pagination']=='yes')) && $layout!='carousel'){
				$pagination_next = !empty($settings['pagination_next']) ? $settings['pagination_next'] : 'NEXT';
				$pagination_prev = !empty($settings['pagination_prev']) ? $settings['pagination_prev'] : 'PREV';
				
				if(($paginationType == 'ajaxbased')){
					$totalPost = $query->found_posts;
					$paginatePgae = ceil($totalPost/$display_posts);
					if($display_posts < $totalPost){
						$output .= '<div class="theplus-pagination">';
							$output .= '<a class="paginate-prev tp-ajax-paginate tp-page-hide" href="#"><i class="fas fa-long-arrow-alt-left" aria-hidden="true"></i>'.esc_attr($PaginationNext).'</a>';
							for($i = 1; $i <= $paginatePgae; $i++){
								if($i == 1){
									$output .= '<a href="#" class="tp-ajax-paginate tp-number current" data-page="'.esc_attr($i).'">'.$i.'</a>';
								}else if($i > 1 && $i <= 3){
									$output .= '<a href="#" class="tp-ajax-paginate tp-number" data-page="'.esc_attr($i).'">'.$i.'</a>';
								}else{
									$output .= '<a href="#" class="tp-ajax-paginate tp-number tp-page-hide" data-page="'.esc_attr($i).'">'.$i.'</a>';
								}
							}
							$output .= '<a class="paginate-next tp-ajax-paginate" href="#">'.esc_attr($PaginationNext).'<i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i></a>';
						$output .= '</div>';
					}
				}else{
					$output .= theplus_pagination($query->max_num_pages,'2',$pagination_next,$pagination_prev);			
				}
							
			}else if($settings['post_extra_option']=='load_more' && $layout!='carousel'){
				if(!empty($total_posts) && $total_posts>0){
					$output .= '<div class="ajax_load_more">';
						$output .= '<a class="post-load-more" data-load-class="'.esc_attr($uid).'" data-layout="'.esc_attr($layout).'"    data-offset-posts="'.($settings['post_offset']).'" data-display_post="'.esc_attr($settings['display_posts']).'"  data-post_load_more="'.esc_attr($settings['load_more_post']).'" data-page="1" data-total_page="'.esc_attr($load_page).'" data-loaded_posts="'.esc_attr($loaded_posts_text).'" data-tp_loading_text="'.esc_attr($tp_loading_text).'" data-loadattr= \'' . $data_loadkey . '\'>'.esc_html($settings['load_more_btn_text']).'</a>';$output .= '</div>';	
				}
			}else if($settings['post_extra_option']=='lazy_load' && $layout!='carousel'){
				if(!empty($total_posts) && $total_posts>0){	
					$output .= '<div class="ajax_lazy_load">';
						$output .= '<a class="post-lazy-load" data-load-class="'.esc_attr($uid).'" data-layout="'.esc_attr($layout).'" data-offset-posts="'.($settings['post_offset']).'" data-display_post="'.esc_attr($settings['display_posts']).'"  data-post_load_more="'.esc_attr($settings['load_more_post']).'" data-page="1" data-total_page="'.esc_attr($load_page).'" data-loaded_posts="'.esc_attr($loaded_posts_text).'" data-tp_loading_text="'.esc_attr($tp_loading_text).'" data-loadattr= \'' . $data_loadkey . '\'><div class="tp-spin-ring"><div></div><div></div><div></div><div></div></div></a>';
						$output .= '</div>';
				}			
			}
			$output .='</div>';
		}
		
		$css_rule =$css_messy='';
		if($settings['messy_column']=='yes'){
			if($layout=='grid' || $layout=='masonry'){
				$desktop_column=$settings['desktop_column'];
				$tablet_column=$settings['tablet_column'];
				$mobile_column=$settings['mobile_column'];
			}else if($layout=='carousel'){
				$desktop_column=$settings['slider_desktop_column'];
				$tablet_column=$settings['slider_tablet_column'];
				$mobile_column=$settings['slider_mobile_column'];
			}
			if($layout!='metro'){
				for($x = 1; $x <= 6; $x++){
					if(!empty($settings["desktop_column_".$x])){
						$desktop=!empty($settings["desktop_column_".$x]["size"]) ? $settings["desktop_column_".$x]["size"].$settings["desktop_column_".$x]["unit"] : '';
						$tablet=!empty($settings["desktop_column_".$x."_tablet"]["size"]) ? $settings["desktop_column_".$x."_tablet"]["size"].$settings["desktop_column_".$x."_tablet"]["unit"] : '';	
						$mobile=!empty($settings["desktop_column_".$x."_mobile"]["size"]) ? $settings["desktop_column_".$x."_mobile"]["size"].$settings["desktop_column_".$x."_mobile"]["unit"] : '';
						$css_messy .= theplus_messy_columns($x,$layout,$uid,$desktop,$tablet,$mobile,$desktop_column,$tablet_column,$mobile_column);
					}
				}
				$css_rule ='<style>'.$css_messy.'</style>';
			}
		}
		echo $output.$css_rule;
		wp_reset_postdata();
	}
	
    protected function content_template() {
    }
	
	Protected function get_filter_category(){
		$settings = $this->get_settings_for_display();
		$query_args = $this->get_query_args();
		$query = new \WP_Query( $query_args );
		$filter_by=$settings['filter_by'];
		$post_category=$settings['post_category'];
		$post_tags=$settings['post_tags'];
		$post_taxonomies=$settings['post_taxonomies'];
		$include_slug=$settings['include_slug'];
		
		$category_filter='';
		if($settings['filter_category']=='yes'){
			$filter_style=$settings["filter_style"];
			$filter_hover_style=$settings["filter_hover_style"];
			$all_filter_category=(!empty($settings["all_filter_category"])) ? $settings["all_filter_category"] : esc_html__('All','theplus');
			$all_filter_category_filter=(!empty($settings["all_filter_category_filter"])) ? $settings["all_filter_category_filter"] : esc_html__('Filters','theplus');
			
			if($settings['query']=='post'){					
				if($filter_by == 'filter_by_category'){
					$terms = get_terms( array('taxonomy' => 'category', 'hide_empty' => true) );
				}else if($filter_by == 'filter_by_tag'){
					$terms = get_terms( array('taxonomy' => 'post_tag','hide_empty' => true,));	
				}
			}else{				
				if(!empty($settings['child_filter_category']) && $settings['child_filter_category']=='yes'){					
					$terms = get_terms( array('taxonomy' => $settings['post_taxonomies'], 'hide_empty' => true ,'parent' => 0) );
				}else{
					$terms = get_terms( array('taxonomy' => $settings['post_taxonomies'], 'hide_empty' => true) );
				}				
			}
			
			$all_category=$category_post_count='';
			$count=$query->post_count;
			if($filter_style=='style-1'){				
				$all_category='<span class="all_post_count">'.esc_html($count).'</span>';
			}
			if($filter_style=='style-2' || $filter_style=='style-3'){				
				$category_post_count='<span class="all_post_count">'.esc_attr($count).'</span>';
			}
			
			$count_cate = array();
			//if($filter_style=='style-2' || $filter_style=='style-3'){
				
				if($query->have_posts()){
					while ( $query->have_posts() ) {				
						$query->the_post();

						if($settings['query']=='post'){					
							if($filter_by == 'filter_by_category'){						
								$categories = get_the_terms( $query->ID, 'category' );
							}else if($filter_by == 'filter_by_tag'){
								$categories = get_the_terms( $query->ID, 'post_tag' );								
							}							
						}else{
							$taxonomy = $settings['post_taxonomies'];
							$categories = get_the_terms( $query->ID, $taxonomy );							
						}
						if($categories){
							foreach( $categories as $category ) {
								if(isset($count_cate[$category->slug])){
									$count_cate[$category->slug]= $count_cate[$category->slug] +1;
								}else{
									$count_cate[$category->slug]= 1;
								}
							}
						}
					}
				}
				wp_reset_postdata();
			//}
			
			$category_filter .='<div class="post-filter-data '.esc_attr($filter_style).' text-'.esc_attr($settings['filter_category_align']).'">';
				if($filter_style=='style-4'){
					$category_filter .= '<span class="filters-toggle-link">'.esc_html($all_filter_category_filter).'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
				}
				$category_filter .='<ul class="category-filters '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
					if(!empty($settings['all_filter_category_switch']) && $settings['all_filter_category_switch']=='yes'){
						$category_filter .= '<li><a href="#" class="filter-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></li>';
					}
					if(!empty($settings['child_filter_category']) && $settings['child_filter_category']=='yes'){
						$parent = array();
						$cateindex = 0;
					}
					if ( $terms != null ){
						foreach( $terms as $term ) {
							$category_post_count='';
							if($filter_style=='style-2' || $filter_style=='style-3'){
								if(isset($count_cate[$term->slug])){
									$count=	$count_cate[$term->slug];
								}else{
									$count = 0;
								}
								$category_post_count='<span class="all_post_count">'.esc_html($count).'</span>';
							}
							
							if(!empty($settings['child_filter_category']) && $settings['child_filter_category']=='yes'){
								if($term->parent != 0) :
									$parent[$cateindex]['id'] = $term->term_id;
									$parent[$cateindex]['slug'] = $term->slug;
								else:
									$parent[$cateindex]['id'] = $term->term_id;
									$parent[$cateindex]['slug'] = $term->slug;
								endif;
								$cateindex++;
							}
							
							/*filter*/
							if($settings['query']=='post'){
								if($filter_by=='filter_by_category'){	
									if(!empty($post_category)){									
										if(in_array($term->term_id,$post_category)){
											if($count > 0 ){
												$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
												unset($term);
											}
										}
									}else{
										if($count > 0 ){
											$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
											unset($term);
										}
									}
								}else if($filter_by=='filter_by_tag'){
									if(!empty($post_tags)){									
										if(in_array($term->term_id,$post_tags)){
											if($count > 0 ){
												$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
												unset($term);
											}
										}
										}else{
											if($count > 0 ){
												$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
												unset($term);
											}
										}
								}
							}else{
								$cat_thumb_id=$featured_image='';
								if(!empty($settings['filter_category_image']) && $settings['filter_category_image']=='yes'){
									$cat_thumb_id = get_term_meta($term->term_id, 'tp_taxonomy_image', true);
									if(!empty($cat_thumb_id)){
										$featured_image='<img src="'.esc_url($cat_thumb_id).'" alt="'.esc_attr(get_the_title()).'">';
									}
								}	
								if(!empty($include_slug)){	
									$include_slug = ($settings['include_slug']) ? explode(',', $settings['include_slug']) : [];
									if(in_array($term->slug,$include_slug)){
										if($count > 0 ){
											$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.$featured_image.'<span data-hover="'.esc_attr($term->slug).'">'.esc_html($term->name).'</span></a></li>';
											unset($term);
										}
									}
								}else{
									if($count > 0 ){
										$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.$featured_image.'<span data-hover="'.esc_attr($term->slug).'">'.esc_html($term->name).'</span></a></li>';
										unset($term);
									}
								}
							}
							/*filter*/
						}
					}
				$category_filter .= '</ul>';
				
				/*get child*/
				if(!empty($settings['child_filter_category']) && $settings['child_filter_category']=='yes'){
					if($parent):
						foreach ($parent as $par):
                            $child_categories= get_term_children($par['id'], $post_taxonomies);
                            if(!empty($child_categories)){
                                $category_filter .= '<ul class="category-filters-child cate-parent-'.$par['slug'].'">';
                                    foreach($child_categories as $child):
                                        $term = get_term_by( 'id', $child, $post_taxonomies );
                                        $cat_thumb_id_child=$featured_image_child='';

										if(!empty($settings['filter_category_image_child']) && $settings['filter_category_image_child']=='yes'){
                                            $cat_thumb_id_child = get_term_meta($term->term_id, 'tp_taxonomy_image', true);
                                            if(!empty($cat_thumb_id_child)){
                                                $featured_image_child='<img src="'.esc_url($cat_thumb_id_child).'" alt="'.esc_attr(get_the_title()).'">';
                                            }
                                        }

                                        $getLink = get_term_link( (int) $child, $post_taxonomies );
                                        if( !is_wp_error($getLink) ){
                                            $category_filter .= '<li><a href="' . $getLink . '" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">' .$featured_image_child. $term->name . '</a></li>';
                                        }
                                    endforeach;
                                $category_filter .= '</ul>';
                            }
                        endforeach;
					endif;				
				}
				/*get child*/
			$category_filter .= '</div>';
		}
		return $category_filter;
	}
	protected function get_query_args() {
		$settings = $this->get_settings_for_display();
		$include_posts = ($settings['include_posts']) ? explode(',', $settings['include_posts']) : '';
		$exclude_posts = ($settings['exclude_posts']) ? explode(',', $settings['exclude_posts']) : '';
	
		$inc_slug_array =$settings['include_slug'];
		$query=$settings['query'];
		
		$post_taxonomies=$settings['post_taxonomies'];
		$query_args = array(
			'post_type'           => $query,
			'post_status'         => 'publish',
			$post_taxonomies	 => $inc_slug_array,
			'ignore_sticky_posts' => true,
			'posts_per_page'      => intval( $settings['display_posts'] ),
			'orderby'      =>  $settings['post_order_by'],
			'order'      => $settings['post_order'],
			'post__not_in'        => $exclude_posts,
			'post__in'        => $include_posts,
		);

		$offset = $settings['post_offset'];
		$offset = ! empty( $offset ) ? absint( $offset ) : 0;

		global $paged;
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		}
		elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}
		else {
			$paged = 1;
		}	
		
		if ($settings['post_extra_option']!='pagination') {
			$query_args['offset'] = $offset;
		}else if(($settings['post_extra_option']=='pagination' || ($settings['cqid_pagination'] && $settings['cqid_pagination']=='yes'))){
			$query_args['paged'] = $paged;
			$page = max( 1, $paged );
			$offset = ( $page - 1 ) * intval( $settings['display_posts'] ) + $offset;
			$query_args['offset'] = $offset;
		}
		
		if ( '' !== $settings['post_category'] ) {			
			$query_args['category__in'] = $settings['post_category'];
		}
		if ( '' !== $settings['post_tags'] ) {
			$query_args['tag__in'] = $settings['post_tags'];
		}
		
		//version 3.3.4
		if($query !='post' && $post_taxonomies=='post_tag' && !empty($inc_slug_array)){
			$new_qu = explode(',', $inc_slug_array);
			$query_args['tax_query'][] = array(
			  array(		
				'taxonomy' => 'post_tag',
				'field' => 'slug',		
				'terms' => $new_qu,		
			  ),		
			);
			unset($query_args['post_tag']);
		}
		if($query !='post' && $post_taxonomies=='category' && !empty($inc_slug_array)){
			$new_qu = explode(',', $inc_slug_array);
			$query_args['tax_query'][] = array(
			  array(		
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $new_qu,		
			  ),		
			);
			unset($query_args['category']);
		}
		
		if(($query !='post' && $query !='product') && $post_taxonomies=='categories' && !empty($inc_slug_array)){
			$new_qu = explode(',', $inc_slug_array);
			$query_args['tax_query'][] = array(
			  array(		
				'taxonomy' => 'categories',
				'field' => 'slug',
				'terms' => $new_qu,		
			  ),		
			);
			unset($query_args['categories']);
		}
		
		//Related Posts
		if(!empty($settings["blogs_post_listing"]) && $settings["blogs_post_listing"]=='related_post'){
			global $post;

			$RelatedPostBy = !empty($settings["related_post_by"]) ? $settings["related_post_by"] : 'both';

			/**Tags*/
			if($post->post_type =='product'){
				$tag_slug = 'slug';
				$tags = wp_get_post_terms($post->ID,'product_tag');
			}else if($post->post_type =='post'){
				$tag_slug = 'term_id';
				$tags = wp_get_post_tags($post->ID);
			}else{
				$tag_slug = 'slug';
				$tags = wp_get_post_terms($post->ID,$post_taxonomies);
			}
			
			if ( $tags && !empty($RelatedPostBy) && ($RelatedPostBy == 'both' || $settings["related_post_by"] == 'tags') ) {	
				$tag_ids = array();
				foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->$tag_slug;
				
				$query_args['post__not_in'] = array($post->ID);
				if($post->post_type =='product'){
					$query_args['tax_query'] = array(						
					  array(		
						'taxonomy' => 'product_tag',		
						'field' => 'slug',		
						'terms' => $tag_ids,		
					  ),		
					);
				}else if($post->post_type =='post'){
					$query_args['tag__in'] = $tag_ids;
				}else{
					$query_args['tax_query'] = array(						
					  array(		
						'taxonomy' => $post_taxonomies,		
						'field' => 'slug',		
						'terms' => $tag_ids,		
					  ),		
					);
				}
			}
			
			/**Category*/
			if($post->post_type =='product'){
				$categories_slug = 'slug';
				$categories = wp_get_post_terms($post->ID,'product_cat');
			}else if($post->post_type =='post'){
				$categories_slug = 'cat_ID';
				$categories = get_the_category($post->ID);
			}else{
				$categories_slug = 'slug';
				$categories = wp_get_post_terms($post->ID,$post_taxonomies);
			}
			
			if ( $categories && !empty($RelatedPostBy) && ($RelatedPostBy == 'both' || $RelatedPostBy == 'category')) {	
				$category_ids = array();
				foreach($categories as $category) $category_ids[] = $category->$categories_slug;
				
				$query_args['post__not_in'] = array($post->ID);
				if($post->post_type =='product'){
					$query_args['tax_query'][] = array(						
					  array(		
						'taxonomy' => 'product_cat',		
						'field' => 'slug',		
						'terms' => $category_ids,		
					  ),		
					);
				}else if($post->post_type =='post'){
					$query_args['category__in'] = $category_ids;
				}else{
					$query_args['tax_query'] = array(						
					  array(		
						'taxonomy' => $post_taxonomies,		
						'field' => 'slug',		
						'terms' => $category_ids,		
					  ),		
					);
				}
			}

			/**Custom Taxonomy*/
			if($post->post_type =='product'){
				$taxonomy_slug = 'slug';
				$tags = wp_get_post_terms($post->ID, $post_taxonomies);
			}else if($post->post_type == 'post'){
				$taxonomy_slug = 'term_id';
				$tags = wp_get_post_tags($post->ID);
			}else{
				$taxonomy_slug = 'slug';
				$tags = wp_get_post_terms($post->ID,$post_taxonomies);
			}

			if ($tags && !empty($RelatedPostBy) &&  $RelatedPostBy == 'taxonomy') {	
				$tag_ids = array();
				foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->$taxonomy_slug;
				
				$query_args['post__not_in'] = array($post->ID);
				if($post->post_type =='product'){
					$query_args['tax_query'] = array(						
					  array(		
						'taxonomy' => $post_taxonomies,		
						'field' => 'slug',		
						'terms' => $tag_ids,		
					  ),		
					);
				}else if($post->post_type =='post'){
					$query_args['tag__in'] = $tag_ids;
				}else{
					$query_args['tax_query'] = array(						
					  array(		
						'taxonomy' => $post_taxonomies,		
						'field' => 'slug',		
						'terms' => $tag_ids,		
					  ),		
					);
				}
			}

		}

		//Archive Posts
		if(!empty($settings["blogs_post_listing"]) && $settings["blogs_post_listing"]=='archive_listing'){
			global $wp_query;
			$query_var = $wp_query->query_vars;
			if(isset($query_var['cat']) && isset($wp_query->tax_query->queries)){
				$query_args['tax_query'] = $wp_query->tax_query->queries;
			}
			if(isset($query_var[$post_taxonomies]) && $query!=='post'){		
						
				$query_args['tax_query'] = array(						
				  array(		
					'taxonomy' => $post_taxonomies,		
					'field' => 'slug',		
					'terms' => $query_var[$post_taxonomies],		
				  ),		
				);		
			}
			if(isset($query_var['tag_id']) && isset($wp_query->tax_query->queries)){
				$query_args['tax_query'] = $wp_query->tax_query->queries;
			}
			if(isset($query_var["author"])){
				$query_args['author'] = $query_var["author"];
			}
			if(is_search()){
				$search = get_query_var('s');
				$query_args['s'] = $search;
				$query_args['exact'] = false;
			}
			if( is_date() ){
				$year=$month=$day=[];
				if( !empty($query_var['year']) ){
					$year = ['year' => $query_var['year'] ];
				}
				if( !empty($query_var['monthnum']) ){
					$month = ['month' => $query_var['monthnum'] ];
				}
				if( !empty($query_var['day']) ){
					$day = ['day' => $query_var['day'] ];
				}
				$query_args['date_query'] = [$year,$month,$day];
			}
		}else if(!empty($settings["blogs_post_listing"]) && $settings["blogs_post_listing"]=='search_list'){
			if(is_archive()){
				global $wp_query;
				$query_var = $wp_query->query_vars;
				if(isset($query_var['cat']) && isset($wp_query->tax_query->queries)){
					$query_args['tax_query'] = $wp_query->tax_query->queries;
				}
				if(isset($query_var[$post_taxonomies]) && $query!=='post'){		
					$query_args['tax_query'] = array(						
					array(		
						'taxonomy' => $post_taxonomies,		
						'field' => 'slug',		
						'terms' => $query_var[$post_taxonomies],		
					),		
					);		
				}
				if(isset($query_var['tag_id']) && isset($wp_query->tax_query->queries)){
					$query_args['tax_query'] = $wp_query->tax_query->queries;
				}
				if(isset($query_var["author"])){
					$query_args['author'] = $query_var["author"];
				}
			}
			if(is_search()){
				$search = get_query_var('s');
				$query_args['s'] = $search;
				$query_args['exact'] = false;
			}
			if(!empty($settings['extra_query_id_search'])){
				$query_ids = $settings['extra_query_id_search'];					
				if(has_filter($query_ids)) {						
					$query_args = apply_filters($query_ids, $query_args);
				}					
			}
		}
		/*query id*/
		$query_id =$settings['extra_query_id'];
		if((!empty($settings['blogs_post_listing']) && $settings['blogs_post_listing']=='custom_query')&& !empty($query_id)){
			if(has_filter($query_id)) {
				$query_args = apply_filters($query_id, $query_args);
			}
		}
		/*query id*/
		return $query_args;
	}
	
	protected function get_carousel_options() {
		$settings = $this->get_settings_for_display();
		$data_slider ='';
			$slider_direction = ($settings['slider_direction']=='vertical') ? 'true' : 'false';
			$data_slider .=' data-slider_direction="'.esc_attr($slider_direction).'"';
			$data_slider .=' data-slide_speed="'.esc_attr($settings["slide_speed"]["size"]).'"';
			
			$data_slider .=' data-slider_desktop_column="'.esc_attr($settings['slider_desktop_column']).'"';
			$data_slider .=' data-steps_slide="'.esc_attr($settings['steps_slide']).'"';
			
			$slider_draggable= ($settings["slider_draggable"]=='yes') ? 'true' : 'false';
			$multi_drag= ($settings["multi_drag"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
			$data_slider .=' data-multi_drag="'.esc_attr($multi_drag).'"';
			$slider_infinite= ($settings["slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
			$slider_pause_hover= ($settings["slider_pause_hover"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
			$slider_adaptive_height= ($settings["slider_adaptive_height"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';

			$slide_fade_inout= ($settings['slider_direction']=='horizontal' && $settings["slide_fade_inout"]=='fadeinout') ? 'true' : 'false';		
			$data_slider .=' data-slide_fade_inout="'.esc_attr($slide_fade_inout).'"';

			$slider_animation=$settings['slider_animation'];
			$data_slider .=' data-slider_animation="'.esc_attr($slider_animation).'"';	
			$slider_autoplay= ($settings["slider_autoplay"]=='yes') ? 'true' : 'false';
			$autoplay_speed= !empty($settings["autoplay_speed"]["size"]) ? $settings["autoplay_speed"]["size"] : '1500';
			$data_slider .=' data-slider_autoplay="'.esc_attr($slider_autoplay).'"';
			$data_slider .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
			
			//tablet
			$data_slider .=' data-slider_tablet_column="'.esc_attr($settings['slider_tablet_column']).'"';
			$data_slider .=' data-tablet_steps_slide="'.esc_attr($settings['tablet_steps_slide']).'"';
			$slider_responsive_tablet=$settings['slider_responsive_tablet'];
			$data_slider .=' data-slider_responsive_tablet="'.esc_attr($slider_responsive_tablet).'"';
			if(!empty($settings['slider_responsive_tablet']) && $settings['slider_responsive_tablet']=='yes'){				
				$tablet_slider_draggable= ($settings["tablet_slider_draggable"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_draggable="'.esc_attr($tablet_slider_draggable).'"';
				$tablet_slider_infinite= ($settings["tablet_slider_infinite"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_infinite="'.esc_attr($tablet_slider_infinite).'"';
				$tablet_slider_autoplay= ($settings["tablet_slider_autoplay"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_autoplay="'.esc_attr($tablet_slider_autoplay).'"';
				$data_slider .=' data-tablet_autoplay_speed="'.(isset($settings["tablet_autoplay_speed"]["size"]) ? esc_attr($settings["tablet_autoplay_speed"]["size"]) : '1500').'"';
				$tablet_slider_dots= ($settings["tablet_slider_dots"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_dots="'.esc_attr($tablet_slider_dots).'"';
				$tablet_slider_arrows= ($settings["tablet_slider_arrows"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_arrows="'.esc_attr($tablet_slider_arrows).'"';
				$data_slider .=' data-tablet_slider_rows="'.esc_attr($settings["tablet_slider_rows"]).'"';
				$tablet_center_mode= ($settings["tablet_center_mode"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_center_mode="'.esc_attr($tablet_center_mode).'" ';
				$data_slider .=' data-tablet_center_padding="'.esc_attr(!empty($settings["tablet_center_padding"]["size"]) ? $settings["tablet_center_padding"]["size"] : 0).'" ';
			}
			
			//mobile 
			$data_slider .=' data-slider_mobile_column="'.esc_attr($settings['slider_mobile_column']).'"';
			$data_slider .=' data-mobile_steps_slide="'.esc_attr($settings['mobile_steps_slide']).'"';
			$slider_responsive_mobile=$settings['slider_responsive_mobile'];			
			$data_slider .=' data-slider_responsive_mobile="'.esc_attr($slider_responsive_mobile).'"';
			if(!empty($settings['slider_responsive_mobile']) && $settings['slider_responsive_mobile']=='yes'){
				$mobile_slider_draggable= ($settings["mobile_slider_draggable"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_draggable="'.esc_attr($mobile_slider_draggable).'"';
				$mobile_slider_infinite= ($settings["mobile_slider_infinite"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_infinite="'.esc_attr($mobile_slider_infinite).'"';
				$mobile_slider_autoplay= ($settings["mobile_slider_autoplay"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_autoplay="'.esc_attr($mobile_slider_autoplay).'"';
				$data_slider .=' data-mobile_autoplay_speed="'.(isset($settings["mobile_autoplay_speed"]["size"]) ? esc_attr($settings["mobile_autoplay_speed"]["size"]) : '1500').'"';
				$mobile_slider_dots= ($settings["mobile_slider_dots"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_dots="'.esc_attr($mobile_slider_dots).'"';
				$mobile_slider_arrows= ($settings["mobile_slider_arrows"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_arrows="'.esc_attr($mobile_slider_arrows).'"';
				$data_slider .=' data-mobile_slider_rows="'.esc_attr($settings["mobile_slider_rows"]).'"';
				$mobile_center_mode= ($settings["mobile_center_mode"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_center_mode="'.esc_attr($mobile_center_mode).'" ';
				$data_slider .=' data-mobile_center_padding="'.(isset($settings["mobile_center_padding"]["size"]) ? esc_attr($settings["mobile_center_padding"]["size"]) : '0').'"';
			}
			
			$slider_dots= ($settings["slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_dots="'.esc_attr($slider_dots).'"';
			$data_slider .=' data-slider_dots_style="slick-dots '.esc_attr($settings["slider_dots_style"]).'"';
			
			
			$slider_arrows= ($settings["slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_arrows="'.esc_attr($slider_arrows).'"';
			$data_slider .=' data-slider_arrows_style="'.esc_attr($settings["slider_arrows_style"]).'" ';
			$data_slider .=' data-arrows_position="'.esc_attr($settings["arrows_position"]).'" ';
			$data_slider .=' data-arrow_bg_color="'.esc_attr($settings["arrow_bg_color"]).'" ';
			$data_slider .=' data-arrow_icon_color="'.esc_attr($settings["arrow_icon_color"]).'" ';
			$data_slider .=' data-arrow_hover_bg_color="'.esc_attr($settings["arrow_hover_bg_color"]).'" ';
			$data_slider .=' data-arrow_hover_icon_color="'.esc_attr($settings["arrow_hover_icon_color"]).'" ';
			
			$slider_center_mode= ($settings["slider_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_center_mode="'.esc_attr($slider_center_mode).'" ';
			$data_slider .=' data-center_padding="'.esc_attr((!empty($settings["center_padding"]["size"])) ? $settings["center_padding"]["size"] : 0).'" ';
			$data_slider .=' data-scale_center_slide="'.esc_attr((!empty($settings["scale_center_slide"]["size"])) ? $settings["scale_center_slide"]["size"] : 1).'" ';
			$data_slider .=' data-scale_normal_slide="'.esc_attr((!empty($settings["scale_normal_slide"]["size"])) ? $settings["scale_normal_slide"]["size"] : 0.8).'" ';
			$data_slider .=' data-opacity_normal_slide="'.esc_attr((!empty($settings["opacity_normal_slide"]["size"])) ? $settings["opacity_normal_slide"]["size"] : 0.7).'" ';
			
			$data_slider .=' data-slider_rows="'.esc_attr($settings["slider_rows"]).'" ';
		return $data_slider;
	}	
}