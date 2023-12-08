<?php 
/*
Widget Name: Dynamic Smart Showcase
Description: Different style of Dynamic Post Smart Showcase.
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
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Dynamic_Smart_Showcase extends Widget_Base {
		
	public function get_name() {
		return 'tp-dynamic-smart-showcase';
	}

    public function get_title() {
        return esc_html__('Dynamic Smart Showcase', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-slideshare theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-listing');
    }
	public function get_keywords() {
		return ['dynamic magazine filter', 'dynamic magazine slider', 'dynamic post ticker', 'post ticker', 'ticker', 'dynamic ticker', 'post magazine', 'magazine slider','magazine filter', 'blog slider', 'dynamic slider', 'dynamic filter', 'magazine', 'blog'];
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
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'magazine',
				'options' => [
					'magazine'  => esc_html__( 'Magazine Slider', 'theplus' ),					
					'news'  => esc_html__( 'Magazine Filter', 'theplus' ),
					'none'  => esc_html__( 'Post Ticker', 'theplus' ),
					
				],
			]
		);
		$this->add_control(
			'magazine_style',
			[
				'label' => esc_html__( 'Magazine Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mag_one_2_2',
				'options' => [
					'mag_one_2_2'  => esc_html__( 'Style-1', 'theplus' ),
					'mag_one_1_2_v'  => esc_html__( 'Style-2', 'theplus' ),
					'mag_one_1_2_h'  => esc_html__( 'Style-3', 'theplus' ),
					'mag_rows_2'  => esc_html__( 'Style-4', 'theplus' ),
					'mag_four_x_rows_1'  => esc_html__( 'Style-5', 'theplus' ),
					'mag_two_3_v'  => esc_html__( 'Style-6', 'theplus' ),
					'mag_two_1_2'  => esc_html__( 'Style-7', 'theplus' ),
					'mag_two_4'  => esc_html__( 'Style-8', 'theplus' ),					
				],
				'condition' => [
					'style' => 'magazine',
				],
			]
		);
		$this->add_control(
			'news_highlight_style',
			[
				'label' => esc_html__( 'Highlight', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hl_left',
				'options' => [
					'hl_left'  => esc_html__( 'Left', 'theplus' ),
					'hl_right'  => esc_html__( 'Right', 'theplus' ),
					'hl_top'  => esc_html__( 'Top', 'theplus' ),
				],
				'condition' => [
					'style' => 'news',
				],
			]
		);
		$this->add_control(
			'news_style',
			[
				'label' => esc_html__( 'News Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'news_style_1',
				'options' => [
					'news_style_1'  => esc_html__( 'News Layout 1', 'theplus' ),
					'news_style_2'  => esc_html__( 'News Layout 2', 'theplus' ),
				],
				'condition' => [
					'style' => 'news',
				],
			]
		);
		$this->add_control(
			'left_side_filter_text',
			[
				'label' => esc_html__( 'Heading', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'The Plus',
				'placeholder' => esc_html__( 'Enter Title', 'theplus' ),
				'condition' => [
					'style' => 'news',
				],
			]
		);
		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => [
					'carousel'  => esc_html__( 'Carousel', 'theplus' ),					
				],
				'condition' => [
					'style' => 'magazine',
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
			]
		);
		$this->add_responsive_control(
			'mag_st_6_8_min_height',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],					
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .bss-magazine.mag_two_3_v .bss-inner-extra>.bss-wrapper,
					{{WRAPPER}} .bss-magazine.mag_two_4 .bss-inner-extra>.bss-wrapper' => 'height: {{SIZE}}{{UNIT}};min-height:{{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [					
					'magazine_style' => ['mag_two_3_v','mag_two_4'],
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'content_source_section',
			[
				'label' => esc_html__( 'Content Source', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
				'condition' => [					
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
				'condition' => [
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
			]
		);
		$this->add_control(
			'exclude_posts',
			[
				'label'       => esc_html__( 'Exclude Post(s)', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder'     => 'exclude multiple posts use comma as separator',
				'label_block' => true,
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
			]
		);
		$this->add_control(
			'display_posts',
			[
				'label' => esc_html__( 'Maximum Posts Display', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 200,
				'step' => 1,
				'default' => -1,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'news_loop_page',
			[
				'label' => esc_html__( 'News Post Loop', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => '6',
				'condition' => [
					'style' => 'news',						
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
			]
		);		
		$this->add_control(
			'post_order_by',
			[
				'label' => esc_html__( 'Order By', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => theplus_orderby_arr(),
			]
		);
		$this->add_control(
			'post_order',
			[
				'label' => esc_html__( 'Order', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => theplus_order_arr(),
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
				'options' => theplus_get_columns_list(),
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
					'{{WRAPPER}} .blog-list .post-inner-loop .grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*columns*/
		
		/*Ticker  start*/
		$this->start_controls_section(
			'section_news_ticker',
			[
				'label' => esc_html__( 'Post Ticker', 'theplus' ),
				'condition' => [
					'style!' => 'news',						
				],		
			]
		);
		$this->add_control(
			'show_tricker',
			[
				'label'   => esc_html__( 'Post Ticker', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);
		$this->add_control(
			'show_label',
			[
				'label'   => esc_html__( 'Label', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'show_tricker' => 'yes'
				]
			]
		);
		$this->add_control(
			'news_label',
			[
				'label'       => esc_html__( 'Label', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'LATEST NEWS', 'theplus' ),
				'placeholder' => esc_html__( 'LATEST NEWS', 'theplus' ),
				'condition' => [
					'show_tricker' => 'yes',
					'show_label' => 'yes'
				]
			]
		);
		$this->add_control(
			'tricker_icon_fontawesome',
			[
				'label' => esc_html__( 'Label Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
				'condition' => [
					'show_tricker' => 'yes',
					'show_label' => 'yes'
				]
			]
		);
		$this->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Label Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '',
				],
				'condition' => [
					'show_tricker' => 'yes',
					'show_label' => 'yes'
				]
			]
		);	
		$this->add_control(
			'show_date',
			[
				'label'     => esc_html__( 'Date', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_tricker' => 'yes',					
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'show_author_name',
			[
				'label'     => esc_html__( 'Author Name', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_tricker' => 'yes',					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'show_author_img',
			[
				'label'     => esc_html__( 'Author Image', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_tricker' => 'yes',					
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*News Ticker  end*/
		
		/*Ticker responsive option start*/
		$this->start_controls_section(
			'section_news_ticker_responsive',
			[
				'label' => esc_html__( 'Post Ticker Responsive Option', 'theplus' ),
				'condition' => [
					'style!' => 'news',
					'show_tricker' => 'yes'
				],		
			]
		);
		$this->add_control(
			'label_res_tab', [
				'label'   => esc_html__( 'Label Disable in Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_label' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'label_res_mob', [
				'label'   => esc_html__( 'Label Disable in Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_label' => [ 'yes' ],
				],
			]
		);		
		$this->add_control(
			'date_res_tab', [
				'label'   => esc_html__( 'Date Disable in Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_date' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'date_res_mob', [
				'label'   => esc_html__( 'Date Disable in Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_date' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'an_res_tab', [
				'label'   => esc_html__( 'Author Name Disable in Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_author_name' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'an_res_mob', [
				'label'   => esc_html__( 'Author Name Disable in Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_author_name' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'ai_res_tab', [
				'label'   => esc_html__( 'Author Image Disable in Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_author_img' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'ai_res_mob', [
				'label'   => esc_html__( 'Author Image Disable in Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition'    => [
					'show_author_img' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_section();
		
		/*post Extra options*/
		$this->start_controls_section(
			'extra_option_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
					'style!' => 'none',					
				],
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
					'style!' => 'none',
				],
			]
		);
		$this->add_control(
			'post_category_style',
			[
				'label' => esc_html__( 'Post Category Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-2',
				'options' => theplus_get_style_list(2),
				'separator' => 'after',
				'condition'   => [					
					'style!' => 'none',
					'display_post_category' => 'yes',
				],
			]
		);
		$this->add_control(
			'post_title_count',
			[
				'label' => esc_html__( 'Title Word Limit', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 2,
				'max' => 500,
				'step' => 1,
				'default' => 10,
				'separator' => 'after',				
			]
		);
		/*ticker options start*/
		$this->add_control(
			'tricker_autoplay',
			[
				'label' => esc_html__( 'Ticker Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'true',
				'default' => 'no',
				'condition'   => [					
					'style' => ['none','magazine'],
				],
				
			]
		);
		$this->add_control(
            'tricker_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Ticker Speed', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 2000,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 10000,
						'step' => 100,
					],
				],				
				'condition' => [
					'style' => ['none','magazine'],
					'tricker_autoplay' => 'true',
				],
            ]
        );
		/*ticker options end*/
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
				'condition'   => [					
					'style!' => 'none',					
				],
				'separator' => 'before',				
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
				'separator' => 'before',	
				'condition'   => [					
					'style!' => 'none',					
				],
				
			]
		);		
		$this->add_control(
			'post_excerpt_count',
			[
				'label' => esc_html__( 'Excerpt/Content Word Limit', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 2,
				'max' => 500,
				'step' => 1,
				'default' => 30,
				'separator' => 'after',
				'condition'   => [
					'style!' => 'none',
					'display_excerpt'    => 'yes',					
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
					'style!'    => 'none',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
				'condition'   => [
					'style!'    => 'none',
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
					'style!' => 'none',
				],
			]
		);
		$this->add_control(
			'post_meta_tag_style',
			[
				'label' => esc_html__( 'Post Meta Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
				'separator' => 'after',
				'condition'   => [
					'style!' => 'none',
					'display_post_meta'    => 'yes',
				],
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
					'style' => 'news'
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
					'style' => 'news',
					'filter_category' => 'yes'
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'filter_category_nxt_prv',
			[
				'label' => esc_html__( 'Only Show Next Previous', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'style' => 'news',
					'filter_category' => 'yes'
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
					'style' => 'news'
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
					'style' => 'news'
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
					'style' => 'news'
				],
			]
		);
		$this->end_controls_section();
		
		/*Post Title start*/
		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style!' => 'none',					
				],
            ]
        );
		$this->add_control(
			'highlight_post_title',
			[
				'label' => esc_html__( 'Highlight Post Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'style' => 'news',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography_high',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-title,{{WRAPPER}} .bss-list.bss-news .bssfc .post-title a',
				'condition' => [
					'style' => 'news',					
				],
			]
		);
		$this->start_controls_tabs( 'tabs_title_style_high' , [
			'condition' => [
				'style' => 'news',
			],
		]);
		$this->start_controls_tab(
			'tab_title_normal_high',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'style' => 'news',					
				],
			]
		);
		$this->add_control(
			'title_color_high',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-title,{{WRAPPER}} .bss-list.bss-news .bssfc .post-title a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'style' => 'news',					
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_n_ts_high',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-title,{{WRAPPER}} .bss-list.bss-news .bssfc .post-title a',
				'condition' => [
					'style' => 'news',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover_high',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'style' => 'news',					
				],
			]
		);
		$this->add_control(
			'title_hover_color_high',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-title:hover,{{WRAPPER}} .bss-list.bss-news .bssfc .post-title:hover a' => 'color: {{VALUE}} !important',
				],
				'condition' => [
					'style' => 'news',					
				],
			]
		);				
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_h_ts_high',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-title:hover,{{WRAPPER}} .bss-list.bss-news .bssfc .post-title:hover a',
				'condition' => [
					'style' => 'news',					
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'normal_post_title',
			[
				'label' => esc_html__( 'Normal Post Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
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
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .post-title,{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .post-title a,
				{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title a',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .post-title,{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .post-title a,
					{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title a' => 'color: {{VALUE}}',
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_n_ts',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .post-title,{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .post-title a,
				{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title a',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .bss-wrap:hover .post-title,{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .bss-wrap:hover .post-title a,
					{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title:hover,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title:hover a' => 'color: {{VALUE}}',
				],
			]
		);				
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_h_ts',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .bss-wrap:hover .post-title,{{WRAPPER}} .bss-list.bss-magazine .bss-wrapper .bss-wrap:hover .post-title a,
				{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title:hover,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .grid-item:not(.bssfc) .post-title:hover a',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Title end*/		
		/*Post Excerpt*/
		$this->start_controls_section(
            'section_excerpt_style',
            [
                'label' => esc_html__('Excerpt/Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style!' => 'none',
					'display_excerpt'    => 'yes',
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
				'selector' => '{{WRAPPER}} .bss-list .bss-wrapper .entry-content,{{WRAPPER}} .bss-list .bss-wrapper .entry-content p',
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
					'{{WRAPPER}} .bss-list .bss-wrapper .entry-content,{{WRAPPER}} .bss-list .bss-wrapper .entry-content p' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .bss-list .bss-wrapper .bss-wrap:hover .entry-content,{{WRAPPER}} .bss-list .bss-wrapper .bss-wrap:hover .entry-content p,{{WRAPPER}} .bss-list .bss-wrapper .bss-content .entry-content:hover,{{WRAPPER}} .bss-list .bss-wrapper .bss-content .entry-content:hover p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Excerpt end*/		
		/*post meta tag*/
		$this->start_controls_section(
            'section_meta_tag_style',
            [
                'label' => esc_html__('Post Meta Tag', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style!' => 'none',
					'display_post_meta'    => 'yes',
				],
				
            ]
        );
		$this->add_control(
			'meta_tag_high_head',
			[
				'label' => esc_html__( 'Highlight Post Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_tag_high_typography',
				'label' => esc_html__( 'Highlight Post Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-meta-info span,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-meta-info a',
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_responsive_control(
            'high_icon',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Highlight Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info .post-author i,{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info' => 'font-size: {{SIZE}}{{UNIT}} !important;',					
				],
				'condition'   => [
					'style'    => 'news',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_post_meta_style_high' , [
			'condition' => [
				'style' => 'news',
			],
		] );
		$this->start_controls_tab(
			'tab_post_meta_high_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_control(
			'post_meta_color_high',
			[
				'label' => esc_html__( 'Post Meta Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info a,
					{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info i' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'post_meta_n_ts_high',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .bssfc .post-meta-info a',
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_post_meta_hover_high',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_control(
			'post_meta_color_hover_high',
			[
				'label' => esc_html__( 'Post Meta Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .bss-wrapper .post-meta-info:hover span,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .bss-wrapper .post-meta-info:hover span a,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .bss-wrapper .post-meta-info:hover  a,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .bss-wrapper .post-meta-info:hover i' => 'color: {{VALUE}}',
				], 
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'post_meta_h_ts_high',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop .bssfc .post-meta-info:hover span,{{WRAPPER}} .bss-list.bss-news .post-inner-loop  .bssfc .post-meta-info:hover a',
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		
		$this->add_control(
			'meta_tag_head',
			[
				'label' => esc_html__( 'Normal Post Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'style'    => 'news',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_tag_typography',
				'label' => esc_html__( 'Normal Post Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .bss-list .post-inner-loop .grid-item:not(.bssfc) .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .grid-item:not(.bssfc) .post-meta-info a',
				
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
					'{{WRAPPER}} .bss-list .post-inner-loop .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .post-meta-info a,
					{{WRAPPER}} .bss-list .post-inner-loop .post-meta-info i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'post_meta_n_ts',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list .post-inner-loop .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .post-meta-info a',
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
					'{{WRAPPER}} .bss-list .post-inner-loop .bss-wrap:hover .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .bss-wrap:hover .post-meta-info a,{{WRAPPER}} .bss-list .post-inner-loop .bss-wrap:hover .post-meta-info i,
					{{WRAPPER}} .bss-list.bss-news .post-inner-loop .post-meta-info:hover span,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .post-meta-info:hover span a,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .post-meta-info:hover i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'post_meta_h_ts',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list .post-inner-loop .bss-wrap:hover .post-meta-info span,{{WRAPPER}} .bss-list .post-inner-loop .bss-wrap:hover s.post-meta-info a,
				{{WRAPPER}} .bss-list.bss-news .post-inner-loop .post-meta-info:hover span,{{WRAPPER}} .bss-list.bss-news .post-inner-loop .post-meta-info:hover a',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*post meta tag*/
		/*Post category*/
		$this->start_controls_section(
            'section_post_category_style',
            [
                'label' => esc_html__('Category Post', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style!' => 'none',
					'display_post_category'    => 'yes',					
				],
            ]
        );
		$this->add_control(
            'post_category_bottom_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Offset', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop .post-category-list' => 'margin-bottom: {{SIZE}}{{UNIT}};',					
				],
				'condition'   => [
					'style' => 'news',		
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
				'selector' => '{{WRAPPER}} .bss-list .post-category-list span a',
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
					'{{WRAPPER}} .bss-list .post-category-list span a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .bss-list .post-category-list span:hover a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .bss-list .post-category-list span a:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'display_post_category' => 'yes',					
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
					'{{WRAPPER}} .bss-list .post-category-list span a' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',					
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
					'{{WRAPPER}} .bss-list .post-category-list span a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_border_style'  , [
			'condition' => [
				'category_border' => 'yes',
				'display_post_category' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'tab_category_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
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
					'{{WRAPPER}} .bss-list .post-category-list span a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
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
					'{{WRAPPER}} .bss-list .post-category-list span a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
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
					'{{WRAPPER}} .bss-list .post-category-list span:hover a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
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
					'{{WRAPPER}} .bss-list .post-category-list span:hover a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_border' => 'yes',
					'display_post_category' => 'yes',
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
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_background_style' , [
			'condition' => [
				'display_post_category' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'tab_category_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'display_post_category' => 'yes',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bss-list .post-category-list span a',
				'condition' => [
					'display_post_category' => 'yes',					
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
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'category_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bss-list .post-category-list span:hover a',
				'condition' => [
					'display_post_category' => 'yes',					
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
				],
			]
		);
		$this->start_controls_tabs( 'tabs_category_shadow_style' , [
			'condition' => [
				'display_post_category' => 'yes',
			],
		] );
		$this->start_controls_tab(
			'tab_category_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'display_post_category' => 'yes',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .bss-list .post-category-list span a',
				'condition' => [
					'display_post_category' => 'yes',					
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
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_hover_shadow',
				'selector' => '{{WRAPPER}} .bss-list .post-category-list span:hover a',
				'condition' => [
					'display_post_category' => 'yes',					
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
					'{{WRAPPER}} .bss-list .post-category-list span a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'display_post_category' => 'yes',					
				],
			]
		);
		$this->end_controls_section();
		/*Post category end*/
		/*News Title heading style start*/
		$this->start_controls_section(
            'section_news_heading_styling',
            [
                'label' => esc_html__('Left Side Heading', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				'condition' => [
					'style' => 'news',					
				],
			]
        );
		$this->add_responsive_control(
			'news_heading_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],								
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'news_heading_typography',
				'label' => esc_html__( 'Heading Typography', 'theplus' ),				
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label',
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_news_heading_style' );
		$this->start_controls_tab(
			'tab_news_heading_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'news_heading_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'news_heading_tab_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'news_heading_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label',
			]
		);
		$this->add_control(
			'sep_border_color',
			[
				'label' => esc_html__( 'Seprate Border Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'news_heading_border_top',
			[
				'label' => esc_html__( 'Top Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label' => 'border-top-color: {{VALUE}};',					
				],
				'condition'   => [
					'sep_border_color' => 'yes',
				],
			]
		);
		$this->add_control(
			'news_heading_border_bottom',
			[
				'label' => esc_html__( 'Bottom Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label' => 'border-bottom-color: {{VALUE}};',					
				],
				'condition'   => [
					'sep_border_color' => 'yes',
				],
			]
		);
		$this->add_control(
			'news_heading_border_right',
			[
				'label' => esc_html__( 'Right Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label' => 'border-right-color: {{VALUE}};',					
				],
				'condition'   => [					
					'sep_border_color' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'news_heading_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'news_heading_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_news_heading_hover',
			[
				'label' => esc_html__( 'HOver', 'theplus' ),
			]
		);
		$this->add_control(
			'news_heading_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'news_heading_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'news_heading_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover',
			]
		);
		$this->add_control(
			'news_heading_border_top_h',
			[
				'label' => esc_html__( 'Top Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover' => 'border-top-color: {{VALUE}};',					
				],
				'condition'   => [					
					'sep_border_color' => 'yes',
				],
			]
		);
		$this->add_control(
			'news_heading_border_bottom_h',
			[
				'label' => esc_html__( 'Bottom Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover' => 'border-bottom-color: {{VALUE}};',					
				],
				'condition'   => [					
					'sep_border_color' => 'yes',
				],
			]
		);
		$this->add_control(
			'news_heading_border_right_h',
			[
				'label' => esc_html__( 'Right Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover' => 'border-right-color: {{VALUE}};',					
				],
				'condition'   => [
					'sep_border_color' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'news_heading_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'news_heading_box_shadow_h',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-label:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*News Title heading style end*/
		
		/*Filter Category style*/
		$this->start_controls_section(
            'section_filter_category_styling',
            [
                'label' => esc_html__('Filter Category', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				'condition' => [
					'style' => 'news',
					'filter_category' => 'yes',
				],
			]
        );		
		$this->add_control(
            'nxt_prev_icon_nxt_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Next Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .bss-nxt-prv-icn .fa-chevron-right' => 'font-size: {{SIZE}}{{UNIT}};',					
				],
            ]
        );		
		$this->add_control(
			'nxt_prev_icon_nxt_color',
			[
				'label' => esc_html__( 'Next Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .bss-nxt-prv-icn .fa-chevron-right' => 'color: {{VALUE}};',
				],				
			]
		);
		$this->add_control(
            'nxt_prev_icon_prev_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Previous Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .bss-nxt-prv-icn .fa-chevron-left' => 'font-size: {{SIZE}}{{UNIT}};',					
				],
            ]
        );
		$this->add_control(
			'nxt_prev_icon_prev_color',
			[
				'label' => esc_html__( 'Previous Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .bss-nxt-prv-icn .fa-chevron-left' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_category_typography',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'separator' => 'before',
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
		$this->add_control(
			'filter_opt_heading',
			[
				'label' => esc_html__( 'Filter Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_news_filter_opt_style' );
		$this->start_controls_tab(
			'tab_news_filter_opt_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'news_filter_opt_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'news_filter_opt_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data',
			]
		);
		$this->add_responsive_control(
			'news_filter_opt_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'news_filter_optbox_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_news_filter_opt_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'news_filter_opt_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data:hover',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'news_filter_opt_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data:hover',
			]
		);
		$this->add_responsive_control(
			'news_filter_opt_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'news_filter_optbox_shadow_h',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-news.pt-plus-filter-post-category .post-filter-data:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Filter Category style*/
		/*highlight image  start*/
		$this->start_controls_section(
            'section_news_loop_content',
            [
                'label' => esc_html__('Top Content (Heading & Filter)', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style' => 'news',
				]
            ]
        );
		$this->add_responsive_control(
			'news_news_loop_content_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],								
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'news_loop_content_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news',
			]
		);
		$this->add_control(
			'news_loop_content_top_offset',
			[
				'label' => esc_html__( 'Bottom Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'style' => 'news',
				],
			]
		);
		
		$this->end_controls_section();
		/*highlight image  start*/
		
		/*highlight image  start*/
		$this->start_controls_section(
            'section_hl_image_style',
            [
                'label' => esc_html__('Highlight Section', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style' => 'news',					
				]
            ]
        );
		$this->add_responsive_control(
			'bss_hl_post_padding_outer',
			[
				'label' => esc_html__( 'Outer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'style' => 'news',
				],
			]
		);
		$this->add_control(
		'hl_overlay_color_normal',
			[
				'label' => esc_html__( 'Normal Overlay Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc .post-content-image-bg:before' => 'background-color: transparent;background-image: linear-gradient(0deg, {{VALUE}} 0%, {{VALUE}} 60%);',
				],
				'condition'   => [
					'style' => 'news',
					'news_style' => 'news_style_1',
				],
			]
		);
		$this->add_control(
		'hl_overlay_color_hover',
			[
				'label' => esc_html__( 'Hover Overlay Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc .post-content-image-bg:hover:before' => 'background-color: transparent;background-image: linear-gradient(0deg, {{VALUE}} 0%, {{VALUE}} 60%);',
				],
				'condition'   => [
					'style' => 'news',
					'news_style' => 'news_style_1',
				],
			]
		);
		$this->add_control(
			'hl_img_position',
			[
				'label' => esc_html__( 'Image Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => theplus_get_position_options(),
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc .post-content-image-bg' => 'background-position: {{VALUE}} !important',
				],
				'condition'   => [
					'style' => 'news',
					'news_style' => 'news_style_1',					
				],
			]
		);
		$this->add_control(
			'hl_img_size',
			[
				'label' => esc_html__( 'Image Size', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => theplus_get_image_size_options(),
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc .post-content-image-bg' => 'background-size: {{VALUE}} !important',
				],
				'condition'   => [
					'style' => 'news',
					'news_style' => 'news_style_1',					
				],
			]
		);
		$this->add_responsive_control(
            'hl_image_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 3000,
						'step' => 5,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc.hl_left .post-content-image-bg,
					{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc.hl_right .post-content-image-bg,
					{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show.bssfc.hl_top .post-content-image-bg' => 'min-height: {{SIZE}}{{UNIT}} !important',
				],
				'condition'   => [
					'style' => 'news',
					'news_style' => 'news_style_1',					
				],
            ]
        );
		$this->add_responsive_control(
            'hl_image_height_st2',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 3000,
						'step' => 5,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news.bss-layout-st2 .grid-item.show.active.bssfc.hl_left,
					{{WRAPPER}} .bss-list.bss-news.bss-layout-st2 .grid-item.show.active.bssfc.hl_right' => 'min-height: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}} .bss-list.bss-news.bss-layout-st2 .post-inner-loop > .grid-item.show.active.bssfc.hl_top .tagimg' => 'height: {{SIZE}}{{UNIT}} !important',
				],
				'condition'   => [
					'style' => 'news',
					'news_style' => 'news_style_2',
				],
            ]
        );
		$this->add_responsive_control(
			'bss_hl_post_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .grid-item.bssfc .bss-wrapper .post-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'style' => 'news',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'bss_hl_post_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .bss-list.bss-news .grid-item.bssfc .bss-wrapper',
					'separator' => 'before',
					'condition'   => [
						'style' => 'news',
					],
				]
			);
			$this->add_responsive_control(
				'bss_hl_post_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bss-list.bss-news .grid-item.bssfc .bss-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'   => [
						'style' => 'news',
					],
				]
			);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bss_hl_post_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news .grid-item.bssfc .bss-wrapper',
				'separator' => 'before',
				'condition'   => [
						'style' => 'news',
					],
			]
		);	
		$this->end_controls_section();
		/*highlight image  end*/
		/*normal image  start*/
		$this->start_controls_section(
            'section_norml_image_style',
            [
                'label' => esc_html__('Normal Section', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style' => 'news',					
				]
            ]
        );
		$this->add_control(
			'normal_content_head',
			[
				'label' => esc_html__( 'Normal Section Content Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
				'condition'   => [
						'style' => 'news',
					],
			]
		);
		$this->add_control(
			'normal_content_alignment',
			[
				'label' => esc_html__( 'Normal Content Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
						],									
				],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item:not(.bssfc) .bss-content' => 'justify-content:{{VALUE}};',
				],				
				'toggle' => true,
				'condition'   => [
						'style' => 'news',
				],				
			]
		);
		$this->add_responsive_control(
			'n_c_padding',
			[
				'label' => esc_html__( 'Content Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item:not(.bssfc) .bss-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
						'style' => 'news',
				],	
			]
		);
		$this->add_control(
			'n_s_normal_section_head',
			[
				'label' => esc_html__( 'Normal Section Box Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
						'style' => 'news',
					],
			]
		);
		$this->add_responsive_control(
			'n_s_normal_section_box_padding',
			[
				'label' => esc_html__( 'Box Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .grid-item:not(.bssfc)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
						'style' => 'news',
				],	
			]
		);
		$this->add_control(
            'n_s_normal_section_bootom_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 300,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.active.show' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
						'style' => 'news',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bss_normal_post_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bss-list.bss-news .grid-item:not(.bssfc)',
				'condition' => [
					'style' => 'news',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'bss_normal_post_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .bss-list.bss-news .grid-item:not(.bssfc)',
					'condition'   => [
						'style' => 'news',
					],
				]
			);
			$this->add_responsive_control(
				'bss_normal_post_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bss-list.bss-news .grid-item:not(.bssfc)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'   => [
						'style' => 'news',
					],
				]				
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bss_normal_post_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news .grid-item:not(.bssfc)',
				'separator' => 'before',
				'condition'   => [
						'style' => 'news',
					],
			]
		);
		$this->add_control(
			'n_s_feature_img_head',
			[
				'label' => esc_html__( 'Feature Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
						'style' => 'news',
				],
			]
		);
		
		$this->add_control(
            'feature_img_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 136,
					'unit' => 'px',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.show:not(:first-child) .bss-remain-img img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
						'style' => 'news',
				],
            ]
        );
		$this->add_control(
			'feature_img_overflow',
			[
				'label' => esc_html__( 'Feature Image Overflow', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hidden',
				'options' => [
					'hidden'  => esc_html__( 'Hidden', 'theplus' ),
					'visible'  => esc_html__( 'Visible', 'theplus' ),
				],
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .grid-item:not(.bssfc)' => 'overflow:{{VALUE}} !important;',
				], 
				'condition' => [
					'style' => 'news',
				],
			]
		);
		$this->add_control(
            'feature_img_max_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.show:not(:first-child) .bss-remain-img img' => 'max-height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
						'style' => 'news',
						'feature_img_overflow' => 'visible',						
				],
            ]
        );
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'n_s_feature_img_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.show:not(:first-child) .bss-remain-img img',					
					'condition'   => [
						'style' => 'news',
					],
				]
			);
			$this->add_responsive_control(
				'n_s_feature_img_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.show:not(:first-child) .bss-remain-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'   => [
						'style' => 'news',
					],
				]				
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'n_s_feature_img_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .bss-list.bss-news .post-inner-loop > .grid-item.show:not(:first-child) .bss-remain-img img',
				'separator' => 'before',
				'condition'   => [
						'style' => 'news',
					],
			]
		);
		$this->end_controls_section();
		/*normal image  end*/		
		
		/*ticker label style start*/
		$this->start_controls_section(
            'section_ticker_label_styling',
            [
                'label' => esc_html__('Label Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style!' => 'news',
					'show_tricker' => 'yes'
				]
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ticker_label_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .theplus-nt-label-inner,
				{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .theplus-nt-label-inner .tp-ticker-label-text',
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);
		$this->add_control(
			'ticker_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .theplus-nt-label-inner,
					{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .theplus-nt-label-inner .tp-ticker-label-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);
		$this->add_control(
			'ticker_label_bg_color',
			[
				'label' => esc_html__( 'Label Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .theplus-nt-label-inner::after' => 'border-left-color: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);
		
		$this->add_control(
			'ticker_label_icon_heading',
			[
				'label' => 'Label Icon',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]			
		);
		$this->add_responsive_control(
            'label_icon_right_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Right Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .ticker-label-icon' => 'padding-right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'label_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .ticker-label-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .ticker-label-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'label_icon_color_n',
			[
				'label' => esc_html__( 'Icon Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .ticker-label-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-label .ticker-label-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'label_icon_color_h',
			[
				'label' => esc_html__( 'Hover Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-news-ticker:hover .theplus-nt-label .ticker-label-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-news-ticker:hover .theplus-nt-label .ticker-label-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*ticker label style end*/
		
		/*ticker Content style start*/
		$this->start_controls_section(
            'section_ticker_content_styling',
            [
                'label' => esc_html__('Content Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style!' => 'news',
					'show_tricker' => 'yes'
				]
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ticker_content_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-news-ticker .theplus-nt-content a',
			]
		);
		$this->add_control(
			'ticker_content_color',
			[
				'label' => esc_html__( 'Content Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-content a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ticker_content_bg_color',
			[
				'label' => esc_html__( 'Content Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items' => 'background: {{VALUE}};',
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-content' => 'background-color: {{VALUE}};',
				],				
			]
		);
		$this->end_controls_section();
		/*ticker Content style end*/
		
		/*ticker Date style start*/
		$this->start_controls_section(
            'section_ticker_date_styling',
            [
                'label' => esc_html__('Date Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style!' => 'news',
					'show_tricker' => 'yes',
					'show_date' => 'yes',
				]
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ticker_content_date_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-news-ticker .theplus-nt-content .theplus-post-date,
				{{WRAPPER}} .theplus-news-ticker .theplus-nt-content .theplus-post-date a',				
			]
		);
		$this->add_control(
			'ticker_content_date_color',
			[
				'label' => esc_html__( 'Date Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-content .theplus-post-date,
					{{WRAPPER}} .theplus-news-ticker .theplus-nt-content .theplus-post-date a' => 'color: {{VALUE}};',
				],
			]
		);		
		$this->end_controls_section();
		/*ticker Date style end*/
		
		/*ticker Navigation style start*/
		$this->start_controls_section(
            'section_ticker_navigation_styling',
            [
                'label' => esc_html__('Navigation Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style!' => 'news',
					'show_tricker' => 'yes'
				]
			]
        );
		$this->add_responsive_control(
            'ticker_navigation_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items a.slick-prev.slick-arrow,{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items a.slick-next.slick-arrow' => 'font-size: {{SIZE}}{{UNIT}} ;line-height: {{SIZE}}{{UNIT}} ;width: {{SIZE}}{{UNIT}} ;height: {{SIZE}}{{UNIT}} ;',					
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_ticker_navigation' );
		$this->start_controls_tab(
			'tab_ticker_navigation',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'ticker_navigation_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items a.slick-prev.slick-arrow,{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items a.slick-next.slick-arrow' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_ticker_navigation_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'ticker_navigation_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items a.slick-prev.slick-arrow:hover,{{WRAPPER}} .theplus-news-ticker .theplus-nt-slideshow-items a.slick-next.slick-arrow:hover' => 'color: {{VALUE}};',
				],
			]
		);		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*ticker Navigation style end*/		
		
		
		/*ticker author style start*/
		$this->start_controls_section(
            'section_ticker_author_styling',
            [
                'label' => esc_html__('Author Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style!' => 'news',
					'show_tricker' => 'yes'
				]
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ticker_author_typo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-news-ticker .ticker-author-name',				
			]
		);
		$this->add_control(
			'ticker_author_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .ticker-author-name' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_responsive_control(
            'ticker_author_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-news-ticker .ticker-author-name i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'ticker_author_icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .ticker-author-name i' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_responsive_control(
			'ticker_author_border_radius',
			[
				'label'      => esc_html__( 'Author Image Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-news-ticker .ticker-author-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_section();
		/*ticker author style end*/
		
		/*ticker box_content style start*/
		$this->start_controls_section(
            'section_ticker_box_content_styling',
            [
                'label' => esc_html__('Box Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [				
					'style!' => 'news',
					'show_tricker' => 'yes'
				]
			]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ticker_whole_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-news-ticker',
			]
		);
		$this->add_responsive_control(
				'ticker_whole_border_r',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .theplus-news-ticker' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],				
				]
			);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ticker_whole_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-news-ticker',
			]
		);
		$this->end_controls_section();
		/*ticker box_content style end*/
		
		/*Box Loop style start*/
		$this->start_controls_section(
            'section_box_loop_styling',
            [
                'label' => esc_html__('Box Loop Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style!' => ['none','news'],					
				],
			]
        );
		$this->add_responsive_control(
			'content_outer_padding',
			[
				'label' => esc_html__( 'Outer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrapper .bss-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' , [
			'condition' => [
				'box_border' => 'yes',
			],
		]);
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap:hover' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->add_control(
			'box_loop_options',
			[
				'label' => esc_html__( 'Box loop Options', 'theplus' ),
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
		$this->add_control(
		'loop_bg_overlay_color',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .bss-list .bss-wrapper .bss-wrap:before' => 'background-color: transparent;background-image: linear-gradient(0deg, {{VALUE}} 0%, {{VALUE}} 60%);',
				],
			]
		);	
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'box_loop_css_normal',
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap',
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
		'loop_bg_overlay_h_color',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,			
				'selectors' => [
					'{{WRAPPER}} .bss-list .bss-wrapper .bss-wrap:hover:before' => 'background-color: transparent;background-image: linear-gradient(0deg, {{VALUE}} 0%, {{VALUE}} 60%);',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'box_loop_css_hover',
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap:hover',
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .bss-list.bss-magazine .bss-inner-extra .bss-wrap:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Box Loop style end*/
		/*carousel option start*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => ['none','news'],	
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
				'default' => '1',
				'options' => [
					'1'  => esc_html__( 'Column 1', 'theplus' ),					
				],
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
					'top' => '15',
					'right' => '15',
					'bottom' => '15',
					'left' => '15',					
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
				'default' => '1',
				'options' => [
					'1'  => esc_html__( 'Column 1', 'theplus' ),
				],
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
				'default' => '1',
				'options' => [
					'1'  => esc_html__( 'Column 1', 'theplus' ),
				],
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
				'default' => 'no',
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
		/*carousel option end*/	
		
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
		
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/

		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
	}
	
	protected function render_excerpt() {
		$settings = $this->get_settings_for_display();		
		$post_excerpt_count=!empty($settings["post_excerpt_count"]) ? $settings["post_excerpt_count"] : 30;
		
		return '<a href="'.esc_url(get_permalink()).'">'.theplus_excerpt($post_excerpt_count).'</a>';
	}
	
	protected function render_title() {
		$settings = $this->get_settings_for_display();
		$post_title_count= !empty($settings["post_title_count"]) ? $settings["post_title_count"] : 10;
		
		return '<a href="'.esc_url(get_permalink()).'">'.theplus_get_title($post_title_count).'</a>';
	}
	
	public function render_date() {
		if ( ! $this->get_settings('show_date') ) {
			return;
		}			
			
		return '<span class="theplus-post-date "><a href="'.esc_url(get_the_permalink()).'">'.esc_attr(get_the_date()).'</a></span>';		
	}
	public function render_author_image() {
		if ( ! $this->get_settings('show_author_img') ) {
			return;
		}
			
			return '<span class="ticker-author-img ">'.get_avatar( get_the_author_meta( 'ID' ), 32 ).'</span>';
		
	}
	public function render_author_name() {
		if ( ! $this->get_settings('show_author_name') ) {
			return;
		}			
		return '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'" rel="author" class="fn"><span class="ticker-author-name "><i class="far fa-user" aria-hidden="true"></i>'.get_the_author().'</a></span>';		
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$query_args = $this->get_query_args();
		$query = new \WP_Query( $query_args );
		$style=$settings["style"];
		$title_desc_word_break=$settings["title_desc_word_break"];
		$magazine_style=$settings["magazine_style"];
		$news_style=$settings["news_style"];
		$news_highlight_style=$settings["news_highlight_style"];
		$layout=$settings["layout"];
		$post_title_tag=$settings["post_title_tag"];
		$post_category=$settings['post_category'];
		$post_tags=$settings['post_tags'];
		$news_loop_page=$settings['news_loop_page'];
		$news_loop_page = ($settings['news_loop_page']!='') ? $settings['news_loop_page'] : '6';
		
		$display_thumbnail=$settings['display_thumbnail'];
		$thumbnail=$settings['thumbnail_size'];
		
		$display_post_meta=$settings["display_post_meta"];
		$post_meta_tag_style=$settings["post_meta_tag_style"];
		
		$display_excerpt=$settings["display_excerpt"];		
		$post_excerpt_count=!empty($settings["post_excerpt_count"]) ? $settings["post_excerpt_count"] : 30;
		$post_title_count=!empty($settings["post_title_count"]) ? $settings["post_title_count"] : 10;
		
		$display_post_category=$settings["display_post_category"];
		$post_category_style=$settings["post_category_style"];
		
		
		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_render_attribute( 'button', 'href', $settings['button_link']['url'] );
			if ( $settings['button_link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}
			if ( $settings['button_link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}

		/*--OnScroll View Animation ---*/
		$animated_columns='';
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
		$PlusExtra_Class='';
		include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		//layout
		$layout_attr=$data_class='';
		if($layout!=''){
			$data_class .=theplus_get_layout_list_class($layout);
			$layout_attr=theplus_get_layout_list_attr($layout);
		}
		
		$data_class .=' bss-'.$style;
		
		if($style=='news' && $news_style=='news_style_2'){
			$data_class .=' bss-layout-st2';
		}
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
		
		$ji=1;$ij='';
		$uid=uniqid("post");
		if(!empty($settings["carousel_unique_id"])){
			$uid="tpca_".$settings["carousel_unique_id"];
		}
		$data_attr .=' data-id="'.esc_attr($uid).'"';
		$data_attr .=' data-style="'.esc_attr($style).'"';
		if($style=='news' && ($news_style=='news_style_1' || $news_style=='news_style_2') && !empty($news_highlight_style)){
			$data_attr .=' data-highlight="'.esc_attr($news_highlight_style).'"';
			$data_attr .=' data-newslooppage="'.esc_attr($news_loop_page).'"';
		}
		$tablet_metro_class=$tablet_ij='';
		
		if ( ! $query->have_posts() ) {
			$output .='<h3 class="theplus-posts-not-found">'.esc_html__( "Posts not found", "theplus" ).'</h3>';
		}else{
			/*ticker start*/
			if( $settings['show_tricker'] == 'yes' && ($style == 'magazine' || $style == 'none')){			
				if(!empty($settings['tricker_icon_fontawesome'])){
							ob_start();
							\Elementor\Icons_Manager::render_icon( $settings['tricker_icon_fontawesome'], [ 'aria-hidden' => 'true' ]);
							$list_icon = ob_get_contents();
							ob_end_clean();						
				}
				
				$ai_res_tab_class=$ai_res_mob_class=$date_res_tab_class=$date_res_mob_class=$an_res_tab_class=$an_res_mob_class='';
				if(!empty($settings['ai_res_tab']) && $settings['ai_res_tab'] == 'yes'){
					$ai_res_tab_class = 'tp-tic-ai-t-hide';
				}
				if(!empty($settings['ai_res_mob']) && $settings['ai_res_mob'] == 'yes'){
					$ai_res_mob_class = 'tp-tic-ai-m-hide';
				}
				
				if(!empty($settings['date_res_tab']) && $settings['date_res_tab'] == 'yes'){
					$date_res_tab_class = 'tp-tic-date-t-hide';
				}
				if(!empty($settings['date_res_mob']) && $settings['date_res_mob'] == 'yes'){
					$date_res_mob_class = 'tp-tic-date-m-hide';
				}
				
				if(!empty($settings['an_res_tab']) && $settings['an_res_tab'] == 'yes'){
					$an_res_tab_class = 'tp-tic-an-t-hide';
				}
				if(!empty($settings['an_res_mob']) && $settings['an_res_mob'] == 'yes'){
					$an_res_mob_class = 'tp-tic-an-m-hide';
				}
				
				$tricker_autoplay = (!empty($settings['tricker_autoplay'])) ? $settings['tricker_autoplay'] : 'false';
				$tricker_speed = (!empty($settings['tricker_speed']['size'])) ? $settings['tricker_speed']['size'] : 2000;				
				$data_ticker_attr='';
				if(!empty($tricker_autoplay) && $tricker_autoplay=='true'){					
					$data_ticker_attr .= ' data-tricker_autoplay="'.esc_attr($tricker_autoplay).'" data-tricker_speed="'.esc_attr($tricker_speed).'"';
					
				}
				$output .='<div class="theplus-news-ticker '.$animated_class.' '.$ai_res_tab_class.' '.$ai_res_mob_class.' '.$date_res_tab_class.' '.$date_res_mob_class.' '.$an_res_tab_class.' '.$an_res_mob_class.'" '.$animation_attr.' '.$data_ticker_attr.'>';
					if ( 'yes' == $settings['show_label'] ){
							$label_res_tab_class=$label_res_mob_class='';
							if(!empty($settings['label_res_tab']) && $settings['label_res_tab'] == 'yes'){
								$label_res_tab_class = 'tp-tic-label-t-hide';
							}
							if(!empty($settings['label_res_mob']) && $settings['label_res_mob'] == 'yes'){
								$label_res_mob_class = 'tp-tic-label-m-hide';
							}
							
						$output .='<div class="theplus-nt-label '.$label_res_tab_class.' '.$label_res_mob_class.'"><div class="theplus-nt-label-inner">';
						$output .='<a class="tp-ticker-label-text" '.$this->get_render_attribute_string( "button" ).'>';
							if(!empty($list_icon) && !empty($settings['tricker_icon_fontawesome'])){
								$output .='<span class="ticker-label-icon">'.$list_icon.'</span>';
							}
							if(!empty($settings['news_label'])){
								$output .= $settings['news_label'];
							}
						$output .='</a>';	
						$output .='</div></div>';
					}
					
					$output .='<div class="theplus-nt-width-expand">';
						$output .='<div class="theplus-nt-width-expand theplus-first-column">';
							$output .='<ul class="theplus-nt-slideshow-items tp-row">';				
									while ( $query->have_posts() ) {
									$output .='<li class="theplus-nt-item tp-col-12">';
									$output .='<div class="theplus-nt-content">';										
											$query->the_post();
											$post = $query->post;
											$output .=$this->render_date();
											$output .=$this->render_author_image();											
											$output .=$this->render_title();
											$output .=$this->render_author_name();																		
									$output .='</div>';
									$output .='</li>';
									}
									
							$output .='</ul>';
						$output .='</div>';
					$output .='</div>';
					
				$output .='</div>';
			}
			/*ticker end*/
			
			$output .= '<div id="pt-plus-bss-list" class="bss-list '.esc_attr($uid).' '.esc_attr($data_class).' '.esc_attr($magazine_style).' '.$animated_class.'" '.$layout_attr.' '.$data_attr.' '.$animation_attr.' data-enable-isotope="1">';
			
				//category filter
				if($settings['filter_category']=='yes'){
					$output .= $this->get_filter_category();
				}
			
				if($style == 'magazine'){				
					$output .= '<div id="'.esc_attr($uid).'" class="tp-row post-inner-loop '.esc_attr($uid).'">';
				}else if($style == 'news'){
					$output .= '<div id="'.esc_attr($uid).'" class="post-inner-loop '.esc_attr($uid).'">';
				}
				
				while ( $query->have_posts() ) {
				
					$query->the_post();
					$post = $query->post;
					
					//category filter
					$category_filter='';
					if($settings['filter_category']=='yes'){				
						 
						 if($settings['query']=='post'){
							$terms = get_the_terms( $query->ID,'category');						
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
					
					
					if($style == 'magazine' && ($magazine_style=='mag_one_2_2' || $magazine_style=='mag_two_3_v' || $magazine_style=='mag_two_1_2')){
						if($ji%5==1){
						//grid item loop
							$output .= '<div class="grid-item metro-item'.esc_attr($ij).' '.esc_attr($tablet_metro_class).' '.$animated_columns.'">';
							$output .= '<div class="bss-inner-extra">';
						}
					}elseif($style == 'magazine'  && ( $magazine_style=='mag_one_1_2_h' || $magazine_style=='mag_one_1_2_v' || $magazine_style=='mag_rows_2' || $magazine_style=='mag_four_x_rows_1')){
						if($ji%4==1){
						//grid item loop
							$output .= '<div class="grid-item metro-item'.esc_attr($ij).' '.esc_attr($tablet_metro_class).' '.$animated_columns.'">';
							$output .= '<div class="bss-inner-extra">';
						}
					}elseif($style == 'magazine'  && $magazine_style=='mag_two_4'){					
						if($ji%6==1){
						//grid item loop
							$output .= '<div class="grid-item metro-item'.esc_attr($ij).' '.esc_attr($tablet_metro_class).' '.$animated_columns.'">';
							$output .= '<div class="bss-inner-extra">';
						}
					}
				
					
					if(!empty($style)){
						ob_start();
						if($style=='magazine'){
							include THEPLUS_PATH. 'includes/dynamic-smart-showcase/bss-'.esc_attr($magazine_style).'.php'; 
						}else if($style=='news'){
						$active=$bssfc='';
						if($ji<=$news_loop_page){
							$active='show animated fadeInEffect';
						}
						if($ji==1){
							$bssfc='bssfc';
						}
								echo '<div class="grid-item '.esc_attr( $active ).' '.esc_attr( $category_filter ).' active '.esc_attr( $bssfc ).' '.$news_highlight_style.'">';					
								include THEPLUS_PATH. 'includes/dynamic-smart-showcase/bss-'.esc_attr($news_style).'.php'; 
								echo '</div>';							
						}
						$output .= ob_get_contents();
						ob_end_clean();
					}
					if($style == 'magazine' && ($magazine_style=='mag_one_2_2' || $magazine_style=='mag_two_3_v' || $magazine_style=='mag_two_1_2')){
						if($ji%5==0 || $query->post_count==$ji){
							$output .='</div>';
							$output .='</div>';
						}
					}elseif($style == 'magazine'  && ( $magazine_style=='mag_one_1_2_h' || $magazine_style=='mag_one_1_2_v' || $magazine_style=='mag_rows_2' || $magazine_style=='mag_four_x_rows_1')){
						if($ji%4==0 || $query->post_count==$ji){
							$output .='</div>';
							$output .='</div>';
						}
					}elseif($style == 'magazine'  && $magazine_style=='mag_two_4'){
						if($ji%6==0 || $query->post_count==$ji){
							$output .='</div>';
							$output .='</div>';
						}
					}
					
					$ji++;
				}
			
				if($style == 'magazine'){
					$output .='</div>';
				}else if($style == 'news'){
					$output .='</div>';
				}
			
				if(!empty($post_category)){
					$post_category=implode(',', $post_category);
				}
				if(!empty($post_tags)){
					$post_tags=implode(',', $post_tags);
				}
			
			$output .='</div>';
		}
		
	
		echo $before_content.$output.$after_content;
		wp_reset_postdata();
	}
	
    protected function content_template() {
	
    }
	Protected function get_filter_category(){
		$settings = $this->get_settings_for_display();
		$query_args = $this->get_query_args();
		$query = new \WP_Query( $query_args );
		$post_category=$settings['post_category'];
		$post_taxonomies=$settings['post_taxonomies'];
		$include_slug=$settings['include_slug'];
		
		$category_filter='';
		if($settings['filter_category']=='yes' || $settings['filter_category_nxt_prv']=='yes'){
		
			$filter_style=$settings["filter_style"];
			$filter_hover_style=$settings["filter_hover_style"];
			
			if($settings['query']=='post'){
				$terms = get_terms( array('taxonomy' => 'category', 'hide_empty' => true) );
			}else{
				$terms = get_terms( array('taxonomy' => $settings['post_taxonomies'], 'hide_empty' => true) );
			}
			$all_category=$category_post_count='';
			
			if($filter_style=='style-1'){
				$count=$query->post_count;
				$all_category='<span class="all_post_count">'.esc_html($count).'</span>';
			}
			if($filter_style=='style-2' || $filter_style=='style-3'){
				$count=$query->post_count;
				$category_post_count='<span class="all_post_count">'.esc_attr($count).'</span>';
			}
			
			$count_cate = array();
			if($filter_style=='style-2' || $filter_style=='style-3'){
				if($settings['query']=='post'){
					$taxonomy = 'category';
				}else{
					$taxonomy = $settings['post_taxonomies'];
				}
				if($query->have_posts()){
					while ( $query->have_posts() ) {				
						$query->the_post();
						$categories = get_the_terms( $query->ID, $taxonomy );
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
			}
			
			if($settings['filter_category_nxt_prv']!=='yes'){
			$category_filter .='<div class="post-filter-label"><span>'.$settings['left_side_filter_text'].'</span></div>';
			$category_filter .='<div class="post-filter-data '.esc_attr($filter_style).' text-'.esc_attr($settings['filter_category_align']).'">';
				if($filter_style=='style-4'){
					$category_filter .= '<span class="filters-toggle-link">'.esc_html__('Filters','theplus').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
				}
				$category_filter .='<div class="showcase-filter"><ul class="category-filters '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
					$all_filter_category=(!empty($settings["all_filter_category"])) ? $settings["all_filter_category"] : esc_html__('All','theplus');
					$category_filter .= '<li><a href="#" class="filter-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr__($all_filter_category).'">'.esc_html__($all_filter_category).'</span>'.$all_category.'</a></li>';
					
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
							
							 if($settings['query']=='post'){
								if(!empty($post_category)){									
									if(in_array($term->term_id,$post_category)){											
										$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
										unset($term);
									}
								}else{
										$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
										unset($term);
								}
							}else{
								if(!empty($include_slug)){	
									$include_slug = ($settings['include_slug']) ? explode(',', $settings['include_slug']) : [];
									if(in_array($term->slug,$include_slug)){
										$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->slug).'">'.esc_html($term->name).'</span></a></li>';
										unset($term);
									}
								}else{
										$category_filter .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->slug).'">'.esc_html($term->name).'</span></a></li>';
										unset($term);
								}
							}
							
						}
					}
				$category_filter .= '</ul></div>';
				$category_filter .= '<div class="bss-nxt-prv-icn">';
				$category_filter .= '<div class="bss_np bssprv"> <i class="fa fa-chevron-left" aria-hidden="true"></i></div>';
				$category_filter .= '<div class="bss_np bssnext"> <i class="fa fa-chevron-right" aria-hidden="true"></i></div>';				
				$category_filter .= '</div>';
			$category_filter .= '</div>';
		}else if($settings['filter_category_nxt_prv']=='yes'){
				$category_filter .='<div class="post-filter-label"><span>'.$settings['left_side_filter_text'].'</span></div>';
				$category_filter .='<div class="post-filter-data '.esc_attr($filter_style).' text-'.esc_attr($settings['filter_category_align']).'">';
				$category_filter .= '<div class="bss-nxt-prv-icn">';
				$category_filter .= '<div class="bss_np bssprv"> <i class="fa fa-chevron-left" aria-hidden="true"></i></div>';
				$category_filter .= '<div class="bss_np bssnext"> <i class="fa fa-chevron-right" aria-hidden="true"></i></div>';				
				$category_filter .= '</div>';
			$category_filter .= '</div>';
			}
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
		$query_args['offset'] = $offset;
		
		if ( '' !== $settings['post_category'] ) {
			
			$query_args['category__in'] = $settings['post_category'];
		}
		if ( '' !== $settings['post_tags'] ) {
			$query_args['tag__in'] = $settings['post_tags'];
		}
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
		$query_args['paged'] = $paged;
		
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
			$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
			$slider_infinite= ($settings["slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
			$slider_pause_hover= ($settings["slider_pause_hover"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
			$slider_adaptive_height= ($settings["slider_adaptive_height"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';
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
