<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Scheme_Color;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Css_Filter;

use MasterAddons\Inc\Controls\MA_Group_Control_Transition;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * MA Blog Post Grid Widget
 */
class Blog extends Widget_Base
{

	public function get_name()
	{
		return 'ma-blog-post';
	}

	public function get_title()
	{
		return esc_html__('Blog Posts', MELA_TD);
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-posts-grid';
	}

	public function get_keywords()
	{
		return ['post', 'layout', 'gallery', 'blog', 'images', 'videos', 'portfolio', 'visual', 'masonry'];
	}

	public function get_script_depends()
	{
		return [
			'isotope',
			'swiper',
			'masonry',
			'imagesloaded',
			'master-addons-scripts'
		];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/blog-element/';
	}


	protected function _register_controls()
	{

		/*
		* Display Options
		*/

		$this->start_controls_section(
			'ma_el_post_grid_section_filters',
			[
				'label' => __('Display Options', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_blog_skin',
			[
				'label'         => __('Blog Layout', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'classic'       => __('Classic', MELA_TD),
					'cards'         => __('Cards', MELA_TD)
				],
				'default'       => 'classic',
				'label_block'   => true
			]
		);




		$this->add_control(
			'ma_el_post_grid_layout',
			[
				'label'         => __('Blog Type', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'grid'          => __('Grid Layout', MELA_TD),
					'list'          => __('List Layout', MELA_TD),
					'masonry'       => __('Masonry Layout', MELA_TD),
				],
				'frontend_available' 	=> true,
				'default'       => 'grid',
				'label_block'   => true
			]
		);

		$this->add_control(
			'ma_el_blog_cards_skin',
			[
				'label'         => __('Cards Layout', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'default'                => __('Default', MELA_TD),
					'absolute_content'       => __('Content Overlap', MELA_TD),
					'absolute_content_two'   => __('Top Left Meta', MELA_TD),
					'cards_right'            => __('Right Align Cards', MELA_TD),
					'cards_center'           => __('Center Align Cards', MELA_TD),
					'gradient_bg'            => __('Center Align Gradient BG', MELA_TD),
					'full_banner'            => __('Banner Card', MELA_TD)
				],
				'default'       => 'default',
				'label_block'   => true,
				'condition'     => [
					'ma_el_blog_skin'           =>  'cards',
					'ma_el_post_grid_layout'    =>  'grid'
				]
			]
		);

		$this->add_control(
			'ma_el_post_list_layout',
			[
				'label'         => __('List Layout Type', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'classic'               => __('List Classic', MELA_TD),
					'meta_bg'               => __('List Meta Background', MELA_TD),
					'button_right'          => __('List Button Right', MELA_TD),
					'content_overlap'       => __('List Content Overlap', MELA_TD),
					'thumbnail_hover'       => __('List Thumbnail Hover', MELA_TD),
					'thumbnail_hover_nav'   => __('List Blur Content', MELA_TD),
					'thumbnail_bg'          => __('List Thumbnail Background', MELA_TD),

				],
				'default'       => 'classic',
				'label_block'   => true,
				'condition' => [
					'ma_el_post_grid_layout' => 'list',
				],
			]
		);

		$this->add_control(
			'ma_el_blog_order',
			[
				'label'         => __('Post Order', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'label_block'   => true,
				'options'       => [
					'asc'           => __('Ascending', MELA_TD),
					'desc'          => __('Descending', MELA_TD)
				],
				'default'       => 'desc'
			]
		);

		$this->add_control(
			'ma_el_blog_order_by',
			[
				'label'         => __('Order By', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'label_block'   => true,
				'options'       => [
					'none'  => __('None', MELA_TD),
					'ID'    => __('ID', MELA_TD),
					'author' => __('Author', MELA_TD),
					'title' => __('Title', MELA_TD),
					'name'  => __('Name', MELA_TD),
					'date'  => __('Date', MELA_TD),
					'modified' => __('Last Modified', MELA_TD),
					'rand'  => __('Random', MELA_TD),
					'comment_count' => __('Number of Comments', MELA_TD),
				],
				'default'       => 'date'
			]
		);


		$this->add_responsive_control(
			'ma_el_blog_cols',
			[
				'label'         => __('Number of Columns', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'100%'          => __('1 Column', MELA_TD),
					'50%'           => __('2 Columns', MELA_TD),
					'33.33%'        => __('3 Columns', MELA_TD),
					'25%'           => __('4 Columns', MELA_TD)
				],
				'default'       => '25%',
				'render_type'   => 'template',
				'label_block'   => true,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-outer-container'  => 'width: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'ma_el_blog_post_meta_separator',
			[
				'label'         => __('Post Meta Separator', MELA_TD),
				'type'          => Controls_Manager::TEXT,
				'default'       => '//',
				'selectors'     => [
					"{{WRAPPER}} .ma-el-post-entry-meta span:before"  => "content:'{{VALUE}}';"
				],
			]
		);


		$this->add_control(
			'title_html_tag',
			[
				'label'   => __('Title HTML Tag', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h2',
			]
		);

		$this->add_control(
			'ma_el_post_grid_type',
			[
				'label'         => __('Post Type', MELA_TD),
				'type'          => Controls_Manager::SELECT2,
				'options'       => Master_Addons_Helper::ma_el_get_post_types(),
				'default'       => 'post',

			]
		);

		$this->add_control(
			'ma_el_post_grid_taxonomy_type',
			[
				'label' => __('Select Taxonomy', MELA_TD),
				'type' => Controls_Manager::SELECT2,
				'options' => '',
				'condition' => [
					'post_type!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_post_grid_posts_columns_spacing',
			[
				'label'         => __('Rows Spacing', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', "em"],
				'range'         => [
					'px'    => [
						'min'   => 1,
						'max'   => 200,
					],
				],
				'condition'     => [
					'ma_el_post_grid_layout' =>  ['grid', 'list']
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-outer-container' => 'margin-bottom: {{SIZE}}{{UNIT}}'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_post_grid_posts_spacing',
			[
				'label'         => __('Columns Spacing', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', "em"],
				'range'         => [
					'px'    => [
						'min'   => 1,
						'max'   => 200,
					],
				],
				'render_type'   => 'template',
				'condition'     => [
					'ma_el_post_grid_layout' =>  ['grid', 'list']
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-outer-container' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_post_grid_flip_text_align',
			[
				'label'         => __('Content Alignment', MELA_TD),
				'type'          => Controls_Manager::CHOOSE,
				'options'       => [
					'left'      => [
						'title' => __('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center'    => [
						'title' => __('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right'     => [
						'title' => __('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default'       => 'left',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-content ' => 'text-align: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'ma_el_blog_total_posts_number',
			[
				'label'         => __('Total Number of Posts', MELA_TD),
				'type'          => Controls_Manager::NUMBER,
				'default'       => wp_count_posts()->publish
			]
		);

		$this->add_control(
			'ma_el_blog_posts_per_page',
			[
				'label'         => __('Posts Per Page', MELA_TD),
				'type'          => Controls_Manager::NUMBER,
				'min'			=> 1,
				'default'       => '4'
			]
		);


		$this->add_control(
			'ma_el_blog_pagination',
			[
				'label'         => __('Pagination', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'description'   => __('Pagination is the process of dividing the posts into discrete pages', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_blog_next_text',
			[
				'label'			=> __('Next Page Text', MELA_TD),
				'type'			=> Controls_Manager::TEXT,
				'default'       => __('Next Post', MELA_TD),
				'condition'     => [
					'ma_el_blog_pagination'      => 'yes',
				]
			]
		);


		$this->add_control(
			'ma_el_blog_prev_text',
			[
				'label'			=> __('Previous Page Text', MELA_TD),
				'type'			=> Controls_Manager::TEXT,
				'default'       => __('Previous Post', MELA_TD),
				'condition'     => [
					'ma_el_blog_pagination'      => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_pagination_alignment',
			[
				'label'         => __('Pagination Alignment', MELA_TD),
				'type'          => Controls_Manager::CHOOSE,
				'options'       => [
					'left'      => [
						'title' => __('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center'    => [
						'title' => __('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right'     => [
						'title' => __('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default'       => 'center',
				'condition'     => [
					'ma_el_blog_pagination'      => 'yes',
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination' => 'text-align: {{VALUE}};',
				],
			]
		);


		$this->end_controls_section();


		/*
			 * Carousel Settings
			 */
		$this->start_controls_section(
			'ma_el_blog_carousel_settings',
			[
				'label'         => __('Carousel', MELA_TD),
				'condition'     => [
					'ma_el_post_grid_layout' => 'grid'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel',
			[
				'label'         => __('Enable Carousel?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_autoheight',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Auto Height', MELA_TD),
				'default' 		=> '',
				'frontend_available' 	=> true,
				'condition' => [
					'ma_el_blog_carousel'	=> 'yes'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_height',
			[
				'label' 		=> __('Custom Height', MELA_TD),
				'description'	=> __('The carousel needs to have a fixed defined height to work in vertical mode.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'size_units' 	=> [
					'px', '%', 'vh'
				],
				'default' => [
					'size' => 500,
					'unit' => 'px',
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 200,
						'max' => 2000,
					],
					'%' 		=> [
						'min' => 0,
						'max' => 100,
					],
					'vh' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__container' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_autoheight!' => '',
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);


		$this->add_control(
			'ma_el_blog_carousel_effect',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Effect', MELA_TD),
				'default' 		=> 'slide',
				'options' 		=> [
					'slide' 	=> __('Slide', MELA_TD),
					'fade' 		=> __('Fade', MELA_TD),
				],
				'frontend_available' => true,
				'condition'		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_effect_fade_warning',
			[
				'type' 				=> Controls_Manager::RAW_HTML,
				'raw' 				=> __('The Fade effect ignores the Slides per View and Slides per Column settings', MELA_TD),
				'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				'condition' 		=> [
					'ma_el_blog_carousel_effect' => 'fade',
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_speed',
			[
				'label' 	=> __('Duration (ms)', MELA_TD),
				'description' => __('Duration of the effect transition.', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 300,
					'unit' 	=> 'px',
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 2000,
						'step'	=> 100,
					],
				],
				'frontend_available' => true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_fade',
			[
				'label'         => __('Fade', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'frontend_available' 	=> true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes',
					'ma_el_blog_cols' 				=> '100%'
				],
			]
		);


		$this->add_control(
			'resistance_ratio',
			[
				'label' 		=> __('Resistance', MELA_TD),
				'description'	=> __('Set the value for resistant bounds.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'size' 		=> 0.25,
					'unit' 		=> 'px',
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.05,
					],
				],
				'frontend_available' => true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);


		$this->add_control(
			'ma_el_blog_carousel_layout_heading',
			[
				'label' 			=> __('Layout', MELA_TD),
				'type' 				=> Controls_Manager::HEADING,
				'separator'			=> 'before',
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_direction',
			[
				'type' 				=> Controls_Manager::SELECT,
				'label' 			=> __('Orientation', MELA_TD),
				'default'			=> 'horizontal',
				'tablet_default'	=> 'horizontal',
				'mobile_default'	=> 'horizontal',
				'options' 			=> [
					'horizontal' 	=> __('Horizontal', MELA_TD),
					'vertical' 		=> __('Vertical', MELA_TD),
				],
				'frontend_available' 	=> true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$slides_per_column = range(1, 6);
		$slides_per_column = array_combine($slides_per_column, $slides_per_column);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'label' 			=> __('Slides Per View', MELA_TD),
				'type' 				=> Controls_Manager::SELECT,
				'default' 			=> '',
				'tablet_default' 	=> '',
				'mobile_default' 	=> '',
				'options' => [
					''	=> __('Default', MELA_TD),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __('Slides Per Column', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_column,
				'frontend_available' 	=> true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes',
					'ma_el_blog_carousel_direction' => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __('Slides to Scroll', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_column,
				'frontend_available' 	=> true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_grid_columns_spacing',
			[
				'label' 			=> __('Columns Spacing', MELA_TD),
				'type' 				=> Controls_Manager::SLIDER,
				'default'			=> [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default'	=> [
					'size' => 12,
					'unit' => 'px',
				],
				'mobile_default'	=> [
					'size' => 0,
					'unit' => 'px',
				],
				'size_units' 		=> ['px'],
				'range' 			=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'				=> [
					'ma_el_blog_carousel'			=> 'yes',
					'ma_el_blog_carousel_direction' => 'horizontal',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_loop',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Loop', MELA_TD),
				'default' 		=> '',
				'separator'		=> 'before',
				'condition'		=> [
					'ma_el_blog_carousel'           => 'yes'
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_slide_change_resize',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Trigger Resize on Slide', MELA_TD),
				'description'	=> __('Some widgets inside post skins templates might require triggering a window resize event when changing slides to display correctly.', MELA_TD),
				'default' 		=> '',
				'frontend_available' => true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_auto_play',
			[
				'label'         => __('Auto Play', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'condition'     => [
					'ma_el_blog_carousel'  => 'yes'
				],
				'frontend_available' 	=> true,

			]
		);

		$this->start_popover();

		$this->add_control(
			'ma_el_blog_carousel_autoplay_speed',
			[
				'label'			=> __('Autoplay Speed', MELA_TD),
				'description'	=> __('Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', MELA_TD),
				'type'			=> Controls_Manager::NUMBER,
				'default'		=> 5000,
				'condition'		=> [
					'ma_el_blog_carousel'           => 'yes',
					'ma_el_blog_carousel_auto_play' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' 		=> __('Disable on Interaction', MELA_TD),
				'description' 	=> __('Removes autoplay completely on the first interaction with the carousel.', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '',
				'condition' 	=> [
					'ma_el_blog_carousel'           => 'yes',
					'ma_el_blog_carousel_auto_play' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'stop_on_hover',
			[
				'label' 	=> __('Pause on Hover', MELA_TD),
				'type' 		=> Controls_Manager::SWITCHER,
				'default' 	=> '',
				'condition'	=> [
					'ma_el_blog_carousel'           => 'yes',
					'ma_el_blog_carousel_auto_play' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->end_popover();


		$this->add_control(
			'ma_el_blog_carousel_free_mode',
			[
				'type' 					=> Controls_Manager::POPOVER_TOGGLE,
				'label' 				=> __('Free Mode', MELA_TD),
				'description'			=> __('Disable fixed positions for slides.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true,
				'condition'	=> [
					'ma_el_blog_carousel'           => 'yes'
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'ma_el_blog_carousel_free_mode_sticky',
			[
				'type' 					=> Controls_Manager::SWITCHER,
				'label' 				=> __('Snap to position', MELA_TD),
				'description'			=> __('Enable to snap slides to positions in free mode.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true,
				'condition' 			=> [
					'ma_el_blog_carousel'           => 'yes',
					'ma_el_blog_carousel_free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_free_mode_momentum',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Momentum', MELA_TD),
				'description'	=> __('Enable to keep slide moving for a while after you release it.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'separator'		=> 'before',
				'frontend_available' => true,
				'condition' => [
					'ma_el_blog_carousel_free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_free_mode_momentum_ratio',
			[
				'label' 		=> __('Ratio', MELA_TD),
				'description'	=> __('Higher value produces larger momentum distance after you release slider.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'ma_el_blog_carousel_free_mode!' 			=> '',
					'ma_el_blog_carousel_free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_free_mode_momentum_velocity',
			[
				'label' 		=> __('Velocity', MELA_TD),
				'description'	=> __('Higher value produces larger momentum velocity after you release slider.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'ma_el_blog_carousel_free_mode!' 			=> '',
					'ma_el_blog_carousel_free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_free_mode_momentum_bounce',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Bounce', MELA_TD),
				'description'	=> __('Set to No if you want to disable momentum bounce in free mode.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true,
				'condition' => [
					'ma_el_blog_carousel_free_mode!' 			=> '',
					'ma_el_blog_carousel_free_mode_momentum!' 	=> '',
				],
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_free_mode_momentum_bounce_ratio',
			[
				'label' 		=> __('Bounce Ratio', MELA_TD),
				'description'	=> __('Higher value produces larger momentum bounce effect.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'ma_el_blog_carousel_free_mode!' => '',
					'ma_el_blog_carousel_free_mode_momentum!' => '',
					'ma_el_blog_carousel_free_mode_momentum_bounce!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->end_popover();

		$this->add_control(
			'ma_el_blog_carousel_arrows',
			[
				'label'         => __('Arrows', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'       => 'yes',
				'return_value' 	=> 'yes',
				'condition'     => [
					'ma_el_blog_carousel'  => 'yes'
				],
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'ma_el_blog_carousel_arrows_placement',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Placement', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 	=> __('Inside', MELA_TD),
					'outside' 	=> __('Outside', MELA_TD),
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' => 'yes',
				]
			]
		);

		$this->end_popover();

		$this->add_control(
			'ma_el_blog_carousel_pagination',
			[
				'label' 		=> __('Pagination', MELA_TD),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'default' 		=> 'on',
				'label_on' 		=> __('On', MELA_TD),
				'label_off' 	=> __('Off', MELA_TD),
				'return_value' 	=> 'on',
				'frontend_available' => true,
				'condition' 		=> [
					'ma_el_blog_carousel'			=> 'yes'
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'ma_el_blog_carousel_pagination_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 		=> __('Inside', MELA_TD),
					'outside' 		=> __('Outside', MELA_TD),
				],
				'frontend_available' 	=> true,
				'condition'		=> [
					'ma_el_blog_carousel_pagination!'         => '',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_pagination_type',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Type', MELA_TD),
				'default'		=> 'bullets',
				'options' 		=> [
					'bullets' 		=> __('Bullets', MELA_TD),
					'fraction' 		=> __('Fraction', MELA_TD),
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination!'         => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_pagination_clickable',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Clickable', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'condition' => [
					'ma_el_blog_carousel_pagination!'         	=> '',
					'ma_el_blog_carousel_pagination_type'       => 'bullets'
				],
				'frontend_available' 	=> true,
			]
		);
		$this->end_popover();
		$this->end_controls_section();


		/*
		    * Thumbnail Settings
		    */
		$this->start_controls_section(
			'ma_el_section_post_grid_thumbnail',
			[
				'label' => __('Thumbnail Settings', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_post_grid_thumbnail',
			[
				'label'         => __('Show Thumbnail?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'description'   => __('Show or Hide Thumbnail', MELA_TD),
				'default'       => 'yes',
			]
		);

		// Select Thumbnail Image Size
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full'
			]
		);

		$this->add_responsive_control(
			'ma_el_post_grid_thumbnail_fit',
			[
				'label'         => __('Thumbnail Fit', MELA_TD),
				'description'   => __('You need to set Height for work Thumbnail Fit ', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'landscape'     => __('Landscape', MELA_TD),
					'square'        => __('Square', MELA_TD),
					'cover'         => __('Cover', MELA_TD),
					'fill'          => __('Fill', MELA_TD),
					'contain'       => __('Contain', MELA_TD),
				],
				'default'       => 'cover',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-thumbnail img' => 'object-fit: {{VALUE}}'
				],
				'condition'     => [
					'ma_el_post_grid_thumbnail' =>  'yes'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_thumb_height',
			[
				'label'         => __('Custom Height?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'description'   => __('Show or Hide Thumbnail', MELA_TD),
				'default'       => 'no',
			]
		);

		$this->add_responsive_control(
			'ma_el_post_grid_thumb_min_height',
			[
				'label'         => __('Thumbnail Min Height', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', "em"],
				'range'         => [
					'px'    => [
						'min'   => 1,
						'max'   => 300,
					],
				],
				'condition'     => [
					'ma_el_post_grid_thumbnail' =>  'yes',
					'ma_el_blog_thumb_height' =>  'yes'
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-thumbnail img' => 'min-height: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_post_grid_thumb_max_height',
			[
				'label'         => __('Thumbnail Max Height', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', "em"],
				'range'         => [
					'px'    => [
						'min'   => 1,
						'max'   => 1000,
					],
				],
				'condition'     => [
					'ma_el_post_grid_thumbnail' =>  'yes',
					'ma_el_blog_thumb_height' =>  'yes'
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-thumbnail img' => 'max-height: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_thumbnail_position',
			[
				'label'         => __('Thumbnail Position', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'description'   => __('Thumbnail Image Position', MELA_TD),
				'options'       => [
					'default'   		=> __('Default', MELA_TD),
					'left'      		=> __('Left', MELA_TD),
					'thumb_top'     	=> __('Top Thumb, Bottom Title', MELA_TD),
					'thumb_bottom'     	=> __('Bottom Thumb, Title Top', MELA_TD),
				],
				'default'       => 'default',
				'label_block'   => true,
				'condition'     => [
					'ma_el_post_grid_layout' =>  'grid',
					'ma_el_post_grid_thumbnail' =>  'yes'
				]
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __('Hover Animation', MELA_TD),
				'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-thumbnail'
				]
			]
		);


		$this->add_control(
			'ma_el_blog_hover_color_effect',
			[
				'label'         => __('Color Effect', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'description'   => __('Choose an overlay color effect', MELA_TD),
				'options'       => [
					'none'                   => __('No Effect', MELA_TD),
					'zoom_in_one'            => __('Zoom In #1', MELA_TD),
					'zoom_in_two'            => __('Zoom In #2', MELA_TD),
					'zoom_out_one'           => __('Zoom Out #1', MELA_TD),
					'zoom_out_two'           => __('Zoom Out #2', MELA_TD),
					'rotate_zoomout'         => __('Rotate + Zoom Out', MELA_TD),
					'slide'                  => __('Slide', MELA_TD),
					'grayscale'              => __('Gray Scale', MELA_TD),
					'blur'                   => __('Blur', MELA_TD),
					'sepia'                  => __('Sepia', MELA_TD),
					'blur_sepia'             => __('Blur + Sepia', MELA_TD),
					'blur_grayscale'         => __('Blur + Gray Scale', MELA_TD),
					'opacity_one'            => __('Opacity #1', MELA_TD),
					'opacity_two'            => __('Opacity #2', MELA_TD),
					'flushing'               => __('Flushing', MELA_TD),
					'shine'                  => __('Shine', MELA_TD),
					'circle'                 => __('Circle', MELA_TD),

				],
				'default'       => 'none',
				'label_block'   => true
			]
		);

		$this->add_control(
			'ma_el_blog_image_shapes',
			[
				'label'         => __('Thumbnail Shapes', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'description'   => __('Choose an Shapes for Thumbnails', MELA_TD),
				'options'       => [
					'none'              => __('None', MELA_TD),
					'framed'            => __('Framed', MELA_TD),
					'diagonal'          => __('Diagonal', MELA_TD),
					'bordered'          => __('Bordered', MELA_TD),
					'gradient-border'   => __('Gradient Bordered', MELA_TD),
					'squares'           => __('Squares', MELA_TD)
				],
				'default'       => 'none',
				'label_block'   => true
			]
		);


		$this->end_controls_section();



		$this->start_controls_section(
			'ma_el_post_grid_posts_options',
			[
				'label'         => __('Posts Settings', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_blog_post_meta_icon',
			[
				'label'         => __('Post Meta Icon', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
				'return_value'  => 'yes'
			]
		);

		$this->add_control(
			'ma_el_blog_post_format_icon',
			[
				'label'         => __('Post Format Icon', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'No',
				'return_value'  => 'yes'
			]
		);

		$this->add_control(
			'ma_el_post_grid_ignore_sticky',
			[
				'label' => esc_html__('Ignore Sticky?', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ma_el_blog_show_content',
			[
				'label'         => __('Show Content?', MELA_TD),
				'description'   => __('Show/Hide Contents', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'ma_el_post_grid_excerpt',
			[
				'label'         => __('Show Excerpt ?', MELA_TD),
				'description'   => __('Default Except Content Length is 55', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
				'condition'     => [
					'ma_el_blog_show_content'  => 'yes',
				]
			]
		);


		$this->add_control(
			'ma_el_post_grid_excerpt_content',
			[
				'label'         => __('Excerpt from Content?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'description'   => __('Post content will be pulled from post content box', MELA_TD),
				'default'       => 'true',
				'return_value'  => 'true',
				'condition'     => [
					'ma_el_post_grid_excerpt'  => 'yes',
				]
			]
		);


		$this->add_control(
			'ma_el_blog_excerpt_length',
			[
				'label'         => __('Excerpt Length', MELA_TD),
				'type'          => Controls_Manager::NUMBER,
				'default'       => 55,
				'condition'     => [
					'ma_el_post_grid_excerpt'  => 'yes',
				]
			]
		);


		$this->add_control(
			'ma_el_post_grid_excerpt_type',
			[
				'label'         => __('Excerpt Type', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'three_dots'        => __('Three Dots', MELA_TD),
					'read_more_link'    => __('Read More Link', MELA_TD),
				],
				'default'       => 'read_more_link',
				'label_block'   => true,
				'condition'     => [
					'ma_el_post_grid_excerpt'  			=> 'yes'
				]
			]
		);

		$this->add_control(
			'ma_el_post_grid_excerpt_text',
			[
				'label'			=> __('Read More Text', MELA_TD),
				'type'			=> Controls_Manager::TEXT,
				'default'       => __('Read More', MELA_TD),
				'condition'     => [
					'ma_el_post_grid_excerpt'      		=> 'yes',
					'ma_el_post_grid_show_read_more'    => 'yes',
					'ma_el_post_grid_excerpt_type' 		=> 'read_more_link'
				]
			]
		);

		$this->add_control(
			'ma_el_post_grid_post_title',
			[
				'label'         => __('Display Post Title?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'ma_el_blog_author_avatar',
			[
				'label'         => __('Display Author Avatar?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'no',
				'return_value'  => 'yes'
			]
		);


		$this->add_control(
			'ma_el_post_grid_post_author_meta',
			[
				'label'         => __('Display Post Author?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'ma_el_post_grid_post_date_meta',
			[
				'label'         => __('Display Post Date?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'ma_el_post_grid_categories_meta',
			[
				'label'         => __('Display Categories?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'no',
			]
		);

		$this->add_control(
			'ma_el_post_grid_tags_meta',
			[
				'label'         => __('Display Tags?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'no',
			]
		);

		$this->add_control(
			'ma_el_post_grid_comments_meta',
			[
				'label'         => __('Display Comments Number?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'ma_el_post_grid_show_read_more',
			[
				'label'         => __('Show Read More?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'default'       => 'yes',
			]
		);



		$this->end_controls_section();


		/*
			 * Advanced Blog Settings
			 */
		$this->start_controls_section(
			'ma_el_blog_advanced_settings',
			[
				'label'         => __('Advanced Settings', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_blog_post_offset',
			[
				'label'         => __('Offset Post Count', MELA_TD),
				'description'   => __('The index of post to start with', MELA_TD),
				'type' 			=> Controls_Manager::NUMBER,
				'default' 		=> '0',
				'min' 			=> '0',
			]
		);

		$this->add_control(
			'ma_el_blog_cat_tabs',
			[
				'label'         => __('Category Filter Tabs', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'condition'     => [
					'ma_el_blog_carousel!'  => 'yes'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_cat_tabs_all_text',
			[
				'label'             => __('All Text', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'placeholder'       => __('All', MELA_TD),
				'default'           => __('All', MELA_TD),

			]
		);

		$this->add_control(
			'ma_el_blog_categories',
			[
				'label'         => __('Filter By Category', MELA_TD),
				'type'          => Controls_Manager::SELECT2,
				'description'   => __('Get posts for specific category(s)', MELA_TD),
				'label_block'   => true,
				'multiple'      => true,
				'options'       => Master_Addons_Helper::ma_el_blog_post_type_categories(),
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_filter_align',
			[
				'label'         => __('Alignment', MELA_TD),
				'type'          => Controls_Manager::CHOOSE,
				'options'       => [
					'flex-start'    => [
						'title' => __('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center'        => [
						'title' => __('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'flex-end'      => [
						'title' => __('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'       => 'center',
				'condition'     => [
					'ma_el_blog_cat_tabs'     => 'yes',
					'ma_el_blog_carousel!'    => 'yes'
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter' => 'justify-content: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'ma_el_blog_tags',
			[
				'label'         => __('Filter By Tag', MELA_TD),
				'type'          => Controls_Manager::SELECT2,
				'description'   => __('Get posts for specific tag(s)', MELA_TD),
				'label_block'   => true,
				'multiple'      => true,
				'options'       => Master_Addons_Helper::ma_el_blog_post_type_tags(),
			]
		);


		$this->add_control(
			'ma_el_blog_users',
			[
				'label'         => __('Filter By Author', MELA_TD),
				'type'          => Controls_Manager::SELECT2,
				'description'   => __('Get posts for specific author(s)', MELA_TD),
				'label_block'   => true,
				'multiple'      => true,
				'options'       => Master_Addons_Helper::ma_el_blog_post_type_users(),
			]
		);

		$this->add_control(
			'ma_el_blog_posts_exclude',
			[
				'label'         => __('Posts to Exclude', MELA_TD),
				'type'          => Controls_Manager::SELECT2,
				'description'   => __('Add post(s) to exclude', MELA_TD),
				'label_block'   => true,
				'multiple'      => true,
				'options'       => Master_Addons_Helper::ma_el_blog_posts_list(),
			]
		);

		$this->add_control(
			'ma_el_blog_new_tab',
			[
				'label'         => __('Links in New Tab', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'description'   => __('Enable links to be opened in a new tab', MELA_TD),
				'default'       => 'no',
			]
		);

		$this->end_controls_section();




		/*
			 * Style Settings
			 */

		$this->start_controls_section(
			'ma_el_blog_thumbnail_style_section',
			[
				'label'         => __('Thumbnail Image', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_post_grid_thumbnail'  => 'yes',
				],

			]
		);

		$this->add_control(
			'ma_el_blog_thumb_border_radius',
			[
				'label'         => __('Border Radius', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-thumbnail img' => 'border-radius: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_overlay_color',
			[
				'label'         => __('Overlay Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-thumbnail,
                        {{WRAPPER}} .ma-el-post-thumbnail img:hover' => 'background: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_border_effect_color',
			[
				'label'         => __('Border Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'condition'     => [
					'ma_el_blog_image_shapes'  => 'bordered',
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-img-shape-bordered' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .ma-el-post-thumbnail img',
			]
		);

		$this->end_controls_section();


		/*
			 * Title Styles
			 */

		$this->start_controls_section(
			'ma_el_blog_title_style_section',
			[
				'label'         => __('Title', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_blog_title_color',
			[
				'label'         => __('Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-entry-title a'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_blog_title_typo',
				'selector'      => '{{WRAPPER}} .ma-el-entry-title',
			]
		);

		$this->add_control(
			'ma_el_blog_title_hover_color',
			[
				'label'         => __('Hover Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-entry-title:hover a'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_title_padding',
			[
				'label'         => __('Title Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-entry-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_blog_title_margin',
			[
				'label'         => __('Title Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->end_controls_section();


		/*
			 * Meta Styles
			 */
		$this->start_controls_section(
			'ma_el_blog_meta_style_section',
			[
				'label'         => __('Meta', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_blog_meta_color',
			[
				'label'         => __('Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-entry-meta, {{WRAPPER}} .ma-el-post-entry-meta a, {{WRAPPER}} .ma-el-blog-post-tags-container, {{WRAPPER}} .ma-el-blog-post-tags-container a, {{WRAPPER}} .ma-el-blog-post-tags a'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_blog_meta_typo',
				'selector'      => '{{WRAPPER}} .ma-el-post-entry-meta, {{WRAPPER}} .ma-el-blog-post-tags-container',
			]
		);

		$this->add_control(
			'ma_el_blog_meta_hover_color',
			[
				'label'         => __('Hover Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-entry-meta a:hover, {{WRAPPER}} .ma-el-blog-post-tags-container a:hover'  => 'color: {{VALUE}};',
				]
			]
		);
		$this->end_controls_section();


		/*
			 * Content Styles
			 */
		$this->start_controls_section(
			'ma_el_blog_content_style_section',
			[
				'label'         => __('Content', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_blog_post_content_color',
			[
				'label'         => __('Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-content, {{WRAPPER}} .ma-el-post-content p'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_post_content_bg_color',
			[
				'label'         => __('Content Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-content'  => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_blog_post_content_typo',
				'selector'      => '{{WRAPPER}} .ma-el-post-content .ma-el-blog-post-content-wrap, {{WRAPPER}} .ma-el-post-content p'
			]
		);

		$this->add_control(
			'ma_el_blog_post_content_box_color',
			[
				'label'         => __('Box Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post'  => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'          => 'ma_el_blog_box_shadow',
				'selector'      => '{{WRAPPER}} .ma-el-blog-post',
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_box_padding',
			[
				'label'         => __('Content Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_content_margin',
			[
				'label'         => __('Content Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_blog_content_box_padding',
			[
				'label'         => __('Article Box Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-wrapper .ma-el-post-outer-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);



		$this->end_controls_section();




		/*
			 * Read More Settings
			 */
		$this->start_controls_section(
			'ma_el_excerpt_read_more_style_section',
			[
				'label'         => __('Read More', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_post_grid_excerpt'      			=> 'yes',
					'ma_el_post_grid_show_read_more'      	=> 'yes',
					'ma_el_post_grid_excerpt_type' 			=> 'read_more_link'
				]
			]
		);

		$this->add_control(
			'ma_el_excerpt_read_more_color',
			[
				'label'         => __('Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_excerpt_read_more_hover_color',
			[
				'label'         => __('Hover Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn:hover'  => 'color: {{VALUE}};',
				]
			]
		);


		// $this->add_control(
		// 	'ma_el_blog_read_more_icon',
		// 	[
		// 		'label'         	=> esc_html__( 'Icon', MELA_TD ),
		// 		'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
		// 		'type'          	=> Controls_Manager::ICONS,
		// 		'fa4compatibility' 	=> 'icon',
		// 		'default'       	=> [
		// 			'value'     => 'fas fa-chevron-right',
		// 			'library'   => 'solid',
		// 		],
		// 		'render_type'      => 'template',
		// 		'condition' => [
		// 			'ma_el_post_grid_excerpt_type' => 'read_more_link'
		// 		],
		// 	]
		// );


		// $this->add_responsive_control(
		// 	'ma_el_blog_read_more_icon_alignment',
		// 	[
		// 		'label' => esc_html__( 'Icon Alignment', MELA_TD ),
		// 		'type' => Controls_Manager::CHOOSE,
		// 		'label_block' => false,
		// 		'options' => [
		// 			'left' => [
		// 				'title' => esc_html__( 'Left', MELA_TD ),
		// 				'icon' => 'fa fa-chevron-left',
		// 			],
		// 			'right' => [
		// 				'title' => esc_html__( 'Right', MELA_TD ),
		// 				'icon' => 'fa fa-chevron-right',
		// 			],
		// 			'none' => [
		// 				'title' => esc_html__( 'None', MELA_TD ),
		// 				'icon' => 'fa fa-ban',
		// 			],
		// 		],
		// 		'default' => 'none',
		// 		'condition'     => [
		// 			'ma_el_post_grid_excerpt_type' => 'read_more_link'
		// 		]
		// 	]
		// );

		$this->add_responsive_control(
			'ma_el_excerpt_read_more_icon_padding',
			[
				'label'         => __('Icon Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'ma_el_excerpt_read_more_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn'  => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_excerpt_read_more_typo',
				'selector'      => '{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'          => 'ma_el_excerpt_read_more_box_shadow',
				'selector'      => '{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_excerpt_read_more_border',
				'separator'     => 'before',
				'selector'      => '{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn',
			]
		);

		$this->add_responsive_control(
			'ma_el_excerpt_read_more_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_excerpt_read_more_margin',
			[
				'label'         => __('Content Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-post-content-wrap .ma-el-post-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);
		$this->end_controls_section();



		/*
			 * Post Format Icon Styles
			 */
		$this->start_controls_section(
			'ma_el_blog_post_format_icon_style_section',
			[
				'label'         => __('Post Format Icon', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_blog_post_format_icon'  => 'yes',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_format_icon_size',
			[
				'label'         => __('Size', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'range'         => [
					'em'    => [
						'min'       => 1,
						'max'       => 10,
					],
				],
				'size_units'    => ['px', "em"],
				'label_block'   => true,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-format-link i' => 'font-size: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_post_format_icon_color',
			[
				'label'         => __('Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'scheme'        => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'default' => '#4b00e7',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-format-link i'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_p_f_trans_icon',
			[
				'label'         => __('Transparent Icon?', MELA_TD),
				'type'          => Controls_Manager::SWITCHER,
				'description'   => __('Show or Hide Thumbnail', MELA_TD),
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'margin',
			[
				'label' => __('Position', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'condition'     => [
					'ma_el_blog_p_f_trans_icon'  => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-blog-format-link i' => 'position: absolute;z-index:0;top: {{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}}; left:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_pf_rotate',
			[
				'label'         => __('Rotation', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'default' => [
					'unit' => 'deg',
					'size' => 360,
				],
				'range' => [
					'deg' => [
						'step' => 5,
					],
				],
				'condition'     => [
					'ma_el_blog_p_f_trans_icon' =>  'yes'
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-format-link i' => 'transform: rotateZ({{SIZE}}{{UNIT}});'
				]


			]
		);

		$this->end_controls_section();


		/*
			 * Pagination Styles
			 */
		$this->start_controls_section(
			'ma_el_blog_pagination_style_section',
			[
				'label'         => __('Pagination', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_blog_pagination_typography',
				'selector'      => '{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span,{{WRAPPER}} .ma-el-blog-pagination .page-numbers li a'
			]
		);

		/* Pagination Colors Tab */
		$this->start_controls_tabs('ma_el_blog_pagination_colors');

		$this->start_controls_tab(
			'ma_el_blog_pagination_nomral',
			[
				'label'         => __('Normal', MELA_TD),

			]
		);

		$this->add_control(
			'ma_el_blog_pagination_text_color',
			[
				'label'         => __('Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li *' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_pagination_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span,{{WRAPPER}} .ma-el-blog-pagination .page-numbers li a' => 'background: {{VALUE}};'
				]
			]
		);
		$this->end_controls_tab();


		$this->start_controls_tab(
			'ma_el_blog_pagination_hover',
			[
				'label'         => __('Hover', MELA_TD),

			]
		);

		$this->add_control(
			'ma_el_blog_pagination_text_hover_color',
			[
				'label'         => __('Hover Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span:hover,{{WRAPPER}} .ma-el-blog-pagination .page-numbers li a:hover'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_pagination_hover_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span:hover,{{WRAPPER}} .ma-el-blog-pagination .page-numbers li a:hover' => 'background: {{VALUE}};'
				]
			]
		);
		$this->end_controls_tab();


		$this->start_controls_tab(
			'ma_el_blog_pagination_active',
			[
				'label'         => __('Active', MELA_TD),

			]
		);

		$this->add_control(
			'ma_el_blog_pagination_text_active_color',
			[
				'label'         => __('Active Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span.current'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_pagination_active_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span.current' => 'background: {{VALUE}};'
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();



		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_pagination_border',
				'separator'     => 'before',
				'selector'      => '{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span,{{WRAPPER}} .ma-el-blog-pagination .page-numbers li a',
			]
		);

		$this->add_control(
			'ma_el_blog_pagination_border_radius',
			[
				'label'         => __('Border Radius', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span, {{WRAPPER}} .ma-el-blog-pagination .page-numbers li span.current, {{WRAPPER}} .ma-el-blog-pagination .page-numbers li a' => 'border-radius: {{SIZE}}{{UNIT}};'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'          => 'ma_el_blog_pagination_shadow',
				'selector'      => '{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span, {{WRAPPER}} .ma-el-blog-pagination .page-numbers li span.current, {{WRAPPER}} .ma-el-blog-pagination .page-numbers li a'
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_pagination_padding',
			[
				'label'         => __('Inner Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li span,
                        {{WRAPPER}} .ma-el-blog-pagination .page-numbers li span.current,
                        {{WRAPPER}} .ma-el-blog-pagination .page-numbers li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_pagination_item_spacing',
			[
				'label'         => __('Item Spacing', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination .page-numbers li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_pagination_margin',
			[
				'label'         => __('Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();



		/*
             * Category Filter Tabs
             */
		$this->start_controls_section(
			'ma_el_blog_cat_filter_tabs_style_section',
			[
				'label'         => __('Category Filter Tabs', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_blog_cat_tabs'         => 'yes',
					'ma_el_blog_carousel!'        => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_blog_cat_filter_typo',
				'selector'      => '{{WRAPPER}} .ma-el-blog-filter ul li a'
			]
		);

		/* Category Filter Tabs */
		$this->start_controls_tabs('ma_el_blog_cat_colors_style');

		// Normal Tab
		$this->start_controls_tab(
			'ma_el_blog_cat_nomral',
			[
				'label'         => __('Normal', MELA_TD),

			]
		);
		$this->add_control(
			'ma_el_blog_cat_filter_text_color',
			[
				'label'         => __('Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_cat_filter_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a' => 'background: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'ma_el_blog_cat_filter_border_color',
			[
				'label'         => __('Border Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'default'       => '#4b00e7',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a'  => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();



		// Hover Tab
		$this->start_controls_tab(
			'ma_el_blog_cat_hover',
			[
				'label'         => __('Hover', MELA_TD),

			]
		);
		$this->add_control(
			'ma_el_blog_cat_filter_text_hover_color',
			[
				'label'         => __('Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a:hover'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_cat_filter_hover_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a:hover' => 'background: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'ma_el_blog_cat_filter_border_hover_color',
			[
				'label'         => __('Border Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'default'       => '#4b00e7',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a:hover'  => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		// Active Tab
		$this->start_controls_tab(
			'ma_el_blog_cat_active_style',
			[
				'label'         => __('Active', MELA_TD),

			]
		);
		$this->add_control(
			'ma_el_blog_cat_filter_text_active_color',
			[
				'label'         => __('Text Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'default'       => '#fff',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a.active'  => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_cat_filter_active_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'default'       => '#4b00e7',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a.active' => 'background: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_cat_filter_border_active_color',
			[
				'label'         => __('Border Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'default'       => '#4b00e7',
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a.active'  => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'          => 'ma_el_blog_cat_filter_shadow',
				'selector'      => '{{WRAPPER}} .ma-el-blog-filter ul li a'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_blog_cat_border',
				'separator'     => 'before',
				'selector'      => '{{WRAPPER}} .ma-el-blog-filter ul li a',
			]
		);

		$this->add_control(
			'ma_el_blog_cat_filter_border_radius',
			[
				'label'         => __('Border Radius', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%', 'em'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a' => 'border-radius: {{SIZE}}{{UNIT}};'
				]
			]
		);



		$this->add_responsive_control(
			'ma_el_blog_cat_filter_padding',
			[
				'label'         => __('Inner Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_cat_filter_item_spacing',
			[
				'label'         => __('Item Spacing', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_cat_filter_margin',
			[
				'label'         => __('Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-blog-filter' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();


		/*
		* Style: Carousel Settings
		*/
		$this->start_controls_section(
			'ma_el_blog_carousel_style_section',
			[
				'label'         => __('Carousel', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_blog_carousel'         => 'yes'
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_arrows_style_heading',
			[
				'label' 	=> __('Arrows', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'ma_el_blog_carousel_arrows' => 'yes',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_arrows_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'middle',
				'options' 		=> [
					'top' 		=> __('Top', MELA_TD),
					'middle' 	=> __('Middle', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
					'ma_el_blog_carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_arrows_position_vertical',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'center',
				'options' 		=> [
					'left' 		=> __('Left', MELA_TD),
					'center' 	=> __('Center', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
					'ma_el_blog_carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_blog_carousel_arrows_size',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 12,
						'max' => 48,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'font-size: {{SIZE}}px;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_arrows_padding',
			[
				'label' 		=> __('Padding', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'padding: {{SIZE}}em;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
				]
			]
		);


		$this->add_responsive_control(
			'arrows_distance',
			[
				'label' 		=> __('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__navigation--inside.jltma-swiper__navigation--middle.jltma-arrows--horizontal .jltma-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--middle).jltma-arrows--horizontal .jltma-swiper__button' => 'margin: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--horizontal .jltma-swiper__button--prev' => 'left: -{{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--horizontal .jltma-swiper__button--next' => 'right: -{{SIZE}}px;',

					'{{WRAPPER}} .jltma-swiper__navigation--inside.jltma-swiper__navigation--center.jltma-arrows--vertical .jltma-swiper__button' => 'margin-top: {{SIZE}}px; margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--center).jltma-arrows--vertical .jltma-swiper__button' => 'margin: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--vertical .jltma-swiper__button--prev' => 'top: -{{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--vertical .jltma-swiper__button--next' => 'bottom: -{{SIZE}}px;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 100,
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'border-radius: {{SIZE}}%;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'arrows',
				'selector' 		=> '{{WRAPPER}} .jltma-swiper__button',
				'condition'		=> [
					'ma_el_blog_carousel_arrows' 	=> 'yes',
				]
			]
		);


		$this->start_controls_tabs('ma_el_blog_carousel_arrow_style_tabs');

		// Normal Tab
		$this->start_controls_tab(
			'ma_el_blog_carousel_arrow_style_tab',
			[
				'label'         => __('Normal', MELA_TD),

			]
		);
		$this->add_control(
			'ma_el_blog_arrow_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'ma_el_blog_arrow_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button' => 'background: {{VALUE}};',
				]
			]
		);
		$this->end_controls_tab();



		// Hover Tab
		$this->start_controls_tab(
			'ma_el_blog_carousel_arrow_hover_style_tab',
			[
				'label'         => __('Hover', MELA_TD),

			]
		);
		$this->add_control(
			'ma_el_blog_arrow_hover_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'ma_el_blog_arrow_hover_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover' => 'background: {{VALUE}};',
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_control(
			'ma_el_blog_carousel_pagination_style_heading',
			[
				'separator'	=> 'before',
				'label' 	=> __('Pagination', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on',
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_align',
			[
				'label' 		=> __('Align', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'center',
				'options' 		=> [
					'left'    		=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'fa fa-align-left',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'fa fa-align-center',
					],
					'right' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'fa fa-align-right',
					],
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-swiper__pagination.jltma-swiper__pagination--horizontal' => 'text-align: {{VALUE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on',
					'ma_el_blog_carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_align_vertical',
			[
				'label' 		=> __('Align', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'middle',
				'options' 		=> [
					'flex-start'    => [
						'title' 	=> __('Top', MELA_TD),
						'icon' 		=> 'eicon-v-align-top',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'flex-end' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'eicon-v-align-bottom',
					],
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-swiper__pagination.jltma-swiper__pagination--vertical' => 'justify-content: {{VALUE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on',
					'ma_el_blog_carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_distance',
			[
				'label' 		=> __('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__pagination--inside.jltma-swiper__pagination--horizontal' => 'padding: 0 {{SIZE}}px {{SIZE}}px {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__pagination--outside.jltma-swiper__pagination--horizontal' => 'padding: {{SIZE}}px 0 0 0;',
					'{{WRAPPER}} .jltma-swiper__pagination--inside.jltma-swiper__pagination--vertical' => 'padding: {{SIZE}}px {{SIZE}}px {{SIZE}}px 0;',
					'{{WRAPPER}} .jltma-swiper__pagination--outside.jltma-swiper__pagination--vertical' => 'padding: 0 0 0 {{SIZE}}px;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_spacing',
			[
				'label' 		=> __('Spacing', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__pagination--horizontal .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}px',
					'{{WRAPPER}} .jltma-swiper__pagination--vertical .swiper-pagination-bullet' => 'margin: {{SIZE}}px 0',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on',
					'ma_el_blog_carousel_pagination_type' => 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'pagination_bullets_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}px;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on',
					'ma_el_blog_carousel_pagination_type' => 'bullets',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'ma_el_blog_carousel_pagination_bullet',
				'selector' 		=> '{{WRAPPER}} .swiper-pagination-bullet',
				'condition'		=> [
					'ma_el_blog_carousel_pagination' => 'on'
				]
			]
		);


		$this->start_controls_tabs('ma_el_blog_carousel_pagination_bullets_tabs_hover');

		$this->start_controls_tab('ma_el_blog_carousel_pagination_bullets_tab_default', [
			'label' 		=> __('Default', MELA_TD),
			'condition'		=> [
				'ma_el_blog_carousel_pagination' 		=> 'on',
				'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_size',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 12,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_pagination_bullets_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_opacity',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_blog_carousel_pagination_bullets_tab_hover', [
			'label' 		=> __('Hover', MELA_TD),
			'condition'		=> [
				'ma_el_blog_carousel_pagination' 		=> 'on',
				'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_size_hover',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 1.5,
						'step' => 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'transform: scale({{SIZE}});',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_pagination_bullets_color_hover',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_opacity_hover',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_blog_carousel_pagination_bullets_tab_active', [
			'label' => __('Active', MELA_TD),
			'condition'	=> [
				'ma_el_blog_carousel_pagination' 		=> 'on',
				'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_size_active',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 1.5,
						'step' => 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'ma_el_blog_carousel_pagination_bullets_color_active',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_blog_carousel_pagination_bullets_opacity_active',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'ma_el_blog_carousel_pagination' 		=> 'on',
					'ma_el_blog_carousel_pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();


		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/blog-element/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/blog-element-customization/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=03AcgVEsTaA" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		//Upgrade to Pro
		
	}



	/*
		 * Renders Post Format Icon
		 * @since 1.1.5
		 */
	protected function ma_el_blog_post_format_icon()
	{

		$post_format = get_post_format();

		switch ($post_format) {
			case 'aside':
				$post_format = 'file-text-o';
				break;
			case 'audio':
				$post_format = 'music';
				break;
			case 'gallery':
				$post_format = 'file-image-o';
				break;
			case 'image':
				$post_format = 'picture-o';
				break;
			case 'link':
				$post_format = 'link';
				break;
			case 'quote':
				$post_format = 'quote-left';
				break;
			case 'video':
				$post_format = 'video-camera';
				break;
			default:
				$post_format = 'thumb-tack';
		}
?>
		<i class="ma-el-blog-post-format-icon fa fa-<?php echo $post_format; ?>"></i>
		<?php
	}




	/*
		 * Renders Post Title
		 * @since 1.1.5
		 */
	protected function ma_el_get_post_title($link_target)
	{

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('title', 'class', 'ma-el-entry-title');

		if ($settings['ma_el_post_grid_post_title'] == 'yes') { ?>

			<<?php echo $settings['title_html_tag'] . ' ' . $this->get_render_attribute_string('title'); ?>>
				<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($link_target); ?>"><?php the_title(); ?></a>
			</<?php echo $settings['title_html_tag']; ?>>

		<?php }
	}


	/*
		 * Renders Post Title
		 * @since 1.1.5
		 */
	protected function ma_el_get_post_content()
	{

		$settings = $this->get_settings();

		$excerpt_type = $settings['ma_el_post_grid_excerpt_type'];
		$excerpt_text = $settings['ma_el_post_grid_excerpt_text'];
		$excerpt_src  = $settings['ma_el_post_grid_excerpt_content'];
		$read_more_link  = $settings['ma_el_post_grid_show_read_more'];
		// $excerpt_icon  = ($settings['ma_el_blog_read_more_icon'])?$settings['ma_el_blog_read_more_icon']:"";
		// $excerpt_icon_align  = $settings['ma_el_blog_read_more_icon_alignment'];

		?>
		<div class="ma-el-blog-post-content-wrap" style="<?php if (
																$settings['ma_el_blog_post_format_icon'] !== 'yes'
															) : echo 'margin-left:0px;';
															endif; ?>">
			<?php if ($settings['ma_el_post_grid_excerpt'] === 'yes') {
				echo Master_Addons_Helper::ma_el_get_excerpt_by_id(get_the_ID(), $settings['ma_el_blog_excerpt_length'], $excerpt_type, $excerpt_text, $excerpt_src, $excerpt_icon = "", $excerpt_icon_align = "", $read_more_link);
			} else {
				if ($settings['ma_el_blog_show_content'] == 'yes') {
					the_content();
				}
			} ?>
		</div>
		<?php
	}



	/*
		 * Renders Post Title
		 * @since 1.1.5
		 */
	protected function ma_el_get_post_meta($link_target)
	{

		$settings = $this->get_settings();

		$date_format = get_option('date_format');

		if (
			$settings['ma_el_post_grid_post_author_meta'] === 'yes' ||
			$settings['ma_el_post_grid_post_date_meta'] === 'yes' ||
			$settings['ma_el_post_grid_categories_meta'] === 'yes' ||
			$settings['ma_el_post_grid_comments_meta'] === 'yes'
		) {
		?>

			<div class="ma-el-post-entry-meta" style="<?php if ($settings['ma_el_blog_post_format_icon'] !== 'yes') : echo 'margin-left:0px';
														endif; ?>">

				<?php if ($settings['ma_el_post_grid_post_author_meta'] === 'yes') : ?>
					<span class="ma-el-post-author">
						<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
							<i class="fa fa-user fa-fw"></i>
						<?php } ?>
						<?php the_author_posts_link(); ?>
					</span>
				<?php endif; ?>

				<?php if ($settings['ma_el_post_grid_post_date_meta'] === 'yes') : ?>
					<span class="ma-el-post-date">
						<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
							<i class="fa fa-calendar fa-fw"></i>
						<?php } ?>

						<?php

						if ($settings['ma_el_post_grid_layout'] == "list" && $settings['ma_el_post_list_layout'] == "thumbnail_bg") { ?>
							<time datetime="<?php echo get_the_modified_date('c'); ?>">
								<?php echo get_the_time('M d'); ?>
								<span>
									<?php echo get_the_time('Y'); ?>
								</span>
							</time>
						<?php } else { ?>
							<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($link_target); ?>"><?php the_time($date_format); ?></a>
						<?php } ?>
					</span>
				<?php endif; ?>

				<?php if ($settings['ma_el_post_grid_categories_meta'] === 'yes') : ?>
					<span class="ma-el-post-categories">
						<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
							<i class="fa fa-tags fa-fw"></i>
						<?php } ?>
						<?php the_category(', '); ?>
					</span>
				<?php endif; ?>

				<?php if ($settings['ma_el_post_grid_comments_meta'] === 'yes') : ?>
					<span class="ma-el-post-comments">
						<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
							<i class="fa fa-comments-o fa-fw"></i>
						<?php } ?>
						<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($link_target); ?>">
							<?php comments_number('0 Comment', '1 Comment', '% Comments'); ?>
						</a>
					</span>
				<?php endif; ?>

			</div>

		<?php
		}
	}


	/*
         * Renders Blog Layout
         * @since 1.1.5
         */
	public function ma_el_get_post_meta_media_format($link_target)
	{

		$settings = $this->get_settings();
		$date_format = get_option('date_format');

		if ($settings['ma_el_blog_author_avatar'] == "yes") { ?>

			<div class="ma-el-post-entry-meta media">
				<div class="ma-el-author-avatar">
					<?php echo get_avatar(get_the_author_meta('ID'), 64, '', get_the_author_meta('display_name'), array('class' => 'rounded-circle')); ?>
				</div>

				<div class="media-body">
					<?php if ($settings['ma_el_post_grid_post_author_meta'] === 'yes') : ?>
						<span class="ma-el-post-author">
							<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
								<i class="fa fa-user fa-fw"></i>
							<?php } ?>
							<?php the_author_posts_link(); ?>
						</span>
					<?php endif; ?>

					<?php if ($settings['ma_el_post_grid_post_date_meta'] === 'yes') : ?>
						<span class="ma-el-post-date">
							<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
								<i class="fa fa-calendar fa-fw"></i>
							<?php } ?>
							<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($link_target); ?>"><?php the_time($date_format); ?></a></span>
					<?php endif; ?>

					<?php if ($settings['ma_el_post_grid_categories_meta'] === 'yes') : ?>
						<span class="ma-el-post-categories">
							<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
								<i class="fa fa-tags fa-fw"></i>
							<?php } ?>
							<?php the_category(', '); ?>
						</span>
					<?php endif; ?>

					<?php if ($settings['ma_el_post_grid_comments_meta'] === 'yes') : ?>
						<span class="ma-el-post-comments">
							<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
								<i class="fa fa-comments-o fa-fw"></i>
							<?php } ?>
							<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($link_target);
																		?>"><?php comments_number('0 Comment', '1 Comment', '% Comments'); ?> </a></span>
					<?php endif; ?>
				</div>
			</div>


		<?php
		}
	}


	/*
		 * Renders Blog Layout
		 * @since 1.1.5
		 */
	protected function ma_el_blog_layout()
	{

		$settings = $this->get_settings();


		switch ($settings['ma_el_blog_cols']) {
			case '100%':
				$col_number = 'jltma-col-sm-12';
				break;
			case '50%':
				$col_number = 'jltma-col-sm-6';
				break;
			case '33.33%':
				$col_number = 'jltma-col-sm-4';
				break;
			case '25%':
				$col_number = 'jltma-col-sm-3';
				break;
		}


		$image_effect = $settings['ma_el_blog_hover_color_effect'];

		$post_effect = $settings['ma_el_blog_hover_color_effect'];

		if ($settings['ma_el_blog_new_tab'] == 'yes') {
			$target = '_blank';
		} else {
			$target = '_self';
		}

		$skin = $settings['ma_el_blog_skin'];

		$post_id = get_the_ID();

		$key = 'post_' . $post_id;

		$tax_key = sprintf('%s_tax', $key);

		$wrap_key = sprintf('%s_wrap', $key);

		$content_key = sprintf('%s_content', $key);

		$this->add_render_attribute($tax_key, 'class', [
			'ma-el-post-outer-container',
			('yes' === $settings['ma_el_blog_carousel']) ? "" : $col_number
		]);


		$this->add_render_attribute($wrap_key, 'class', [
			'ma-el-blog-post',
			($settings['ma_el_post_grid_layout'] == 'grid') ? 'ma-el-default-post' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'classic') ? 'ma-el-blog-list-default' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'meta_bg') ? 'ma-el-blog-list-meta-bg' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'button_right') ? 'ma-el-blog-list-button-right' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'content_overlap') ? 'ma-el-blog-list-content-slide' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'thumbnail_hover') ? 'ma-el-blog-list-thumbnail-hover' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'thumbnail_hover_nav') ? 'ma-el-blog-list-thumbnail-nav-hover' : "",
			($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'thumbnail_bg') ? 'ma-el-blog-list-thumbnail-bg' : "",
			($settings['ma_el_blog_author_avatar'] === 'yes') ? "ma-el-post-meta-with-avatar" : "",
			($settings['ma_el_blog_thumbnail_position'] == 'left' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-half-row" : "",
			($settings['ma_el_blog_cards_skin'] == 'absolute_content' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-absolute-bottom-content" : "",
			($settings['ma_el_blog_cards_skin'] == 'cards_right' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-content-right" : "",
			($settings['ma_el_blog_cards_skin'] == 'cards_center' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-meta-icon-with-details" : "",
			($settings['ma_el_blog_cards_skin'] == 'absolute_content_two' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-content-gradient-bg-02" : "",
			($settings['ma_el_blog_cards_skin'] == 'gradient_bg' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-content-gradient-bg" : "",
			($settings['ma_el_blog_cards_skin'] == 'full_banner' && $settings['ma_el_post_grid_layout'] == 'grid') ? "ma-el-post-corner-content" : "",
			$skin,
		]);

		$thumb = (!has_post_thumbnail()) ? 'empty-thumb' : '';

		if ('yes' === $settings['ma_el_blog_cat_tabs'] && 'yes' !== $settings['ma_el_blog_carousel']) {

			$categories = get_the_category($post_id);

			foreach ($categories as $index => $category) {

				$category = str_replace(' ', '-', $category->cat_name);

				$this->add_render_attribute($tax_key, 'class', strtolower($category));
			}
		}

		$this->add_render_attribute($content_key, 'class', [
			//				'ma-el-blog-content-wrapper',
			'ma-el-post-content',
			$thumb,
		]);


		if ($settings['hover_animation']) {
			$this->add_render_attribute('hover_animations', 'class', ['elementor-animation-' . $settings['hover_animation']]);
		}

		?>

		<?php if ($settings['ma_el_blog_thumbnail_position'] == 'left' && $settings['ma_el_post_grid_layout'] == 'grid') { ?>
			<div class="jltma-col-lg-6">
			<?php } else { ?>
				<div <?php echo $this->get_render_attribute_string($tax_key); ?>>
				<?php } ?>


				<div <?php echo $this->get_render_attribute_string($wrap_key); ?>>

					<?php if (
						($settings['ma_el_blog_thumbnail_position'] == 'left' && $settings['ma_el_post_grid_layout'] == 'grid') ||
						($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'button_right') ||
						($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'content_overlap')
					) { ?>
						<div class="jltma-row">
							<div class="jltma-col-md-6">
							<?php } ?>

							<?php if (
								($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'classic') ||
								($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'meta_bg')
							) { ?>
								<div class="jltma-row">
								<?php } ?>

								<?php if ($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'classic') { ?>
									<div class="jltma-col-md-4">
									<?php } ?>

									<?php if ($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'meta_bg') { ?>
										<div class="jltma-col-md-5">
										<?php } ?>

										<?php if ($settings['ma_el_blog_thumbnail_position'] !== "thumb_bottom") {
											$this->jltma_render_thumbnails();
										}  ?>

										<div class="ma-el-blog-effect-container <?php echo 'ma-el-blog-' . $post_effect .
																					'-effect'; ?>">
											<a class="ma-el-post-link" href="<?php the_permalink(); ?>" target="<?php echo esc_attr(
																													$target
																												); ?>"></a>
											<?php if ($settings['ma_el_blog_hover_color_effect'] === 'bordered') : ?>
												<div class="ma-el-blog-bordered-border-container"></div>
											<?php elseif ($settings['ma_el_blog_hover_color_effect'] === 'squares') : ?>
												<div class="ma-el-blog-squares-square-container"></div>
											<?php endif; ?>
										</div>



										<?php if (
											($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'classic') ||
											($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'meta_bg')
										) { ?>
										</div>
										<!--col-md-4-->
									<?php } ?>

									<?php if (
										($settings['ma_el_blog_thumbnail_position'] == 'left' && $settings['ma_el_post_grid_layout'] == 'grid') ||
										($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'button_right') ||
										($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'content_overlap')
									) { ?>
									</div>
									<div class="jltma-col-md-6">
									<?php } ?>

									<?php if ('cards' === $skin && $settings['ma_el_blog_author_avatar'] == "yes") : ?>
										<div class="ma-el-author-avatar">
											<?php echo get_avatar(get_the_author_meta('ID'), 64, '', get_the_author_meta('display_name'), array('class' => 'rounded-circle')); ?>
										</div>
									<?php endif; ?>



									<?php if ($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'classic') { ?>
										<div class="jltma-col-md-8">
										<?php } ?>

										<?php if ($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'meta_bg') { ?>
											<div class="jltma-col-md-7">
											<?php } ?>

											<?php if ($settings['ma_el_post_grid_layout'] == 'grid' && $settings['ma_el_blog_cards_skin'] == 'full_banner') { ?>
												<div class="jltma-container">
												<?php } ?>

												<?php
												//( $settings['ma_el_blog_cards_skin'] == 'full_banner' && $settings['ma_el_post_grid_layout'] =='grid' ) ? "ma-el-post-corner-content" : ""
												//						    if( $settings['ma_el_blog_cards_skin'] == 'full_banner' && $settings['ma_el_post_grid_layout'] =='grid' ){ echo '<div class="jltma-container">'; }
												?>
												<div <?php echo $this->get_render_attribute_string($content_key); ?>>

													<div class="ma-el-blog-inner-container">

														<?php if ($settings['ma_el_blog_post_format_icon'] === 'yes') : ?>
															<div class="ma-el-blog-format-container">
																<a class="ma-el-blog-format-link" href="<?php the_permalink(); ?>" title="<?php if (
																																				get_post_format() === ' '
																																			) : echo 'standard';
																																			else : echo get_post_format();
																																			endif; ?>" target="<?php echo esc_attr($target); ?>">

																	<?php $this->ma_el_blog_post_format_icon(); ?>
																</a>
															</div>
														<?php endif; ?>

														<div class="ma-el-blog-entry-container">
															<?php

															if (
																($settings['ma_el_post_grid_layout'] == "list" && $settings['ma_el_post_list_layout'] == "thumbnail_hover") ||
																($settings['ma_el_post_grid_layout'] == "list" && $settings['ma_el_post_list_layout'] == "thumbnail_bg")
															) {
																$this->ma_el_get_post_meta($target);
															}

															$this->ma_el_get_post_title($target);

															if ('classic' === $skin) {
																if ($settings['ma_el_blog_author_avatar'] === 'yes') {
																	$this->ma_el_get_post_meta_media_format($target);
																} elseif (
																	($settings['ma_el_post_grid_layout'] != "list" && $settings['ma_el_post_list_layout'] != "thumbnail_hover") ||
																	($settings['ma_el_post_grid_layout'] != "list" && $settings['ma_el_post_list_layout'] != "thumbnail_bg")
																) {
																	//                                            if( $settings['ma_el_post_list_layout'] !='thumbnail_hover'){
																	$this->ma_el_get_post_meta($target);
																	//                                            }
																}
															}

															?>
														</div>
													</div>


													<?php if ($settings['ma_el_blog_thumbnail_position'] === "thumb_bottom") {
														$this->jltma_render_thumbnails();
													}  ?>

													<?php

													$this->ma_el_get_post_content();

													if ('cards' === $skin) {
														if (
															($settings['ma_el_post_grid_layout'] != "list" && $settings['ma_el_post_list_layout'] != "thumbnail_hover") ||
															($settings['ma_el_post_grid_layout'] != "list" && $settings['ma_el_post_list_layout'] != "thumbnail_hover_nav") ||
															($settings['ma_el_post_grid_layout'] != "list" && $settings['ma_el_post_list_layout'] != "thumbnail_bg")
														) {
															$this->ma_el_get_post_meta($target);
														}
													}
													?>

													<?php if ($settings['ma_el_post_grid_tags_meta'] === 'yes' && has_tag()) : ?>
														<div class="ma-el-blog-post-tags-container" style="<?php if ($settings['ma_el_blog_post_format_icon'] !== 'yes') : echo 'margin-left:0px;';
																											endif; ?>">
															<span class="ma-el-blog-post-tags">

																<?php if ($settings['ma_el_blog_post_meta_icon'] === 'yes') { ?>
																	<i class="fa fa-tags fa-fw"></i>
																<?php } ?>

																<?php the_tags(' ', ', '); ?>
															</span>
														</div>
													<?php endif; ?>
												</div>


												<?php if ($settings['ma_el_post_grid_layout'] == 'grid' && $settings['ma_el_blog_cards_skin'] == 'full_banner') { ?>
												</div>
											<?php } ?>

											<?php if (
												($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'classic') ||
												($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'meta_bg')
											) { ?>
											</div> <!-- .col-md-8 -->
										</div>
										<!--.row-->
									<?php } ?>


									<?php if (
										($settings['ma_el_blog_thumbnail_position'] == 'left' && $settings['ma_el_post_grid_layout'] == 'grid') ||
										($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'button_right') ||
										($settings['ma_el_post_grid_layout'] == 'list' && $settings['ma_el_post_list_layout'] == 'content_overlap')
									) { ?>

									</div> <!-- .col-md-6 -->
								</div> <!-- .row -->
							<?php } ?>


							</div>
						</div>

					<?php }


				protected function render_swiper_navigation()
				{
					$settings = $this->get_settings_for_display();
					$this->add_render_attribute([
						'navigation' => [
							'class' => [
								'jltma-arrows',
								'jltma-arrows--' . $settings['ma_el_blog_carousel_direction'],
								'jltma-swiper__navigation',
								'jltma-swiper__navigation--' . $settings['ma_el_blog_carousel_arrows_placement'],
								'jltma-swiper__navigation--' . $settings['ma_el_blog_carousel_arrows_position'],
								'jltma-swiper__navigation--' . $settings['ma_el_blog_carousel_arrows_position_vertical']
							],
						],
					]);
					?>
						<div <?php echo $this->get_render_attribute_string('navigation'); ?>>
							<?php
							$this->render_swiper_arrows();
							?>
						</div>
					<?php
				}

				protected function jltma_render_thumbnails()
				{
					$settings = $this->get_settings_for_display();
					?>

						<?php if ($settings['ma_el_post_grid_thumbnail'] == 'yes') { ?>
							<div <?php echo $this->get_render_attribute_string('hover_animations'); ?>>
								<div class="ma-el-post-thumbnail ma-el-img-<?php echo $image_effect; ?> ma-el-img-shape-<?php echo $settings['ma_el_blog_image_shapes']; ?>">
									<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr($target); ?>">
										<?php the_post_thumbnail($settings['thumbnail_size']); ?>
									</a>
									<?php if ($settings['ma_el_blog_cards_skin'] === "absolute_content_two") { ?>
										<div class="ma-el-post-entry-meta">
											<span class="ma-el-post-date">
												<time datetime="<?php echo get_the_modified_date('c'); ?>">
													<?php echo get_the_time('d'); ?>
													<span>
														<?php echo get_the_time('M'); ?>
													</span>
												</time>
											</span>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>

					<?php }

				public function render_swiper_pagination()
				{
					$settings = $this->get_settings_for_display();

					if ('' === $settings['ma_el_blog_carousel_pagination'])
						return;

					$this->add_render_attribute('pagination', 'class', [
						'jltma-swiper__pagination',
						'jltma-swiper__pagination--' . $settings['ma_el_blog_carousel_direction'],
						'jltma-swiper__pagination--' . $settings['ma_el_blog_carousel_pagination_position'],
						'jltma-swiper__pagination-' . $this->get_id(),
						'swiper-pagination',
					]);

					?>
						<div <?php echo $this->get_render_attribute_string('pagination'); ?>>
						</div>
					<?php
				}
				protected function render_swiper_arrows()
				{
					$settings = $this->get_settings_for_display();
					if ('' === $settings['ma_el_blog_carousel_arrows'])
						return;

					$prev = is_rtl() ? 'right' : 'left';
					$next = is_rtl() ? 'left' : 'right';

					$this->add_render_attribute([
						'button-prev' => [
							'class' => [
								'jltma-swiper__button',
								'jltma-swiper__button--prev',
								'jltma-arrow',
								'jltma-arrow--prev',
								'jltma-swiper__button--prev-' . $this->get_id(),
							],
						],
						'button-prev-icon' => [
							'class' => 'eicon-chevron-' . $prev,
						],
						'button-next' => [
							'class' => [
								'jltma-swiper__button',
								'jltma-swiper__button--next',
								'jltma-arrow',
								'jltma-arrow--next',
								'jltma-swiper__button--next-' . $this->get_id(),
							],
						],
						'button-next-icon' => [
							'class' => 'eicon-chevron-' . $next,
						],
					]);

					?><div <?php echo $this->get_render_attribute_string('button-prev'); ?>>
							<i <?php echo $this->get_render_attribute_string('button-prev-icon'); ?>></i>
						</div>
						<div <?php echo $this->get_render_attribute_string('button-next'); ?>>
							<i <?php echo $this->get_render_attribute_string('button-next-icon'); ?>></i>
						</div><?php
							}



							protected function render()
							{

								// Query var for paged
								if (get_query_var('paged')) {
									$paged = get_query_var('paged');
								} elseif (get_query_var('page')) {
									$paged = get_query_var('page');
								} else {
									$paged = 1;
								}

								$settings = $this->get_settings_for_display();

								$offset = $settings['ma_el_blog_post_offset'];

								$post_per_page = $settings['ma_el_blog_posts_per_page'];

								$new_offset = $offset + (($paged - 1) * $post_per_page);

								$post_args = Master_Addons_Helper::ma_el_blog_get_post_settings($settings);

								$posts = Master_Addons_Helper::ma_el_blog_get_post_data($post_args, $paged, $new_offset);

								$posts_number = intval(100 / substr($settings['ma_el_blog_cols'], 0, strpos($settings['ma_el_blog_cols'], '%')));

								$carousel = 'yes' == $settings['ma_el_blog_carousel'] ? true : false;

								$unique_id 	= implode('-', [$this->get_id(), get_the_ID()]);

								if (!$carousel) {

									$this->add_render_attribute(
										'ma_el_blog',
										'class',
										[
											'ma-el-blog-wrapper',
											'ma-el-blog-' . $settings['ma_el_post_grid_layout'],
											'jltma-row'
										]
									);
								} else {

									$this->add_render_attribute([
										'ma_el_blog' => [
											'class' => [
												'ma-el-blog-wrapper',
												'jltma-swiper',
												'jltma-swiper__container',
												'swiper-container',
												'elementor-jltma-element-' . $unique_id
											],
											'data-jltma-template-widget-id' => $unique_id
										],

										'swiper-wrapper' => [
											'class' => [
												'jltma-blog-carousel',
												'jltma-swiper__wrapper',
												'swiper-wrapper',
											],
										],

										'swiper-item' => [
											'class' => [
												'jltma-slider__item',
												'jltma-swiper__slide',
												'swiper-slide'
											],
										],
									]);

									$this->add_render_attribute('ma_el_blog', 'class', ['elementor-swiper-slider']);
								}
								?>
						<div class="ma-el-blog">

							<?php if ('yes' === $settings['ma_el_blog_cat_tabs'] && 'yes' !== $settings['ma_el_blog_carousel']) { ?>
								<div class="ma-el-blog-filter">
									<ul class="ma-el-blog-cats-container">
										<li>
											<a href="javascript:;" class="category active" data-filter="*">
												<span><?php echo $settings['ma_el_blog_cat_tabs_all_text']; ?></span>
											</a>
										</li>
										<?php foreach ($settings['ma_el_blog_categories'] as $index => $id) {
											$cat_list_key = 'blog_category_' . $index;

											$name = get_cat_name($id);

											$name_filter = str_replace(' ', '-', $name);
											$name_lower = strtolower($name_filter);

											$this->add_render_attribute(
												$cat_list_key,
												'class',
												[
													'category'
												]
											);
										?>
											<li>
												<a href="javascript:;" <?php echo $this->get_render_attribute_string($cat_list_key); ?> data-filter=".<?php echo esc_attr($name_lower); ?>"><span><?php echo $name; ?></span>
												</a>
											</li>
										<?php } ?>
									</ul>
								</div>

							<?php } ?>


							<div <?php echo $this->get_render_attribute_string('ma_el_blog'); ?>>

								<?php if ($carousel) { ?>
									<div <?php echo $this->get_render_attribute_string('swiper-wrapper'); ?>>
										<?php }

									if (count($posts)) {
										global $post;
										foreach ($posts as $post) {
											setup_postdata($post);
											if ($carousel) {
												echo '<div ' . $this->get_render_attribute_string('swiper-item') . '>';
											}
											$this->ma_el_blog_layout();
											if ($carousel) {
												echo '</div>';
											}
										}
										if ($carousel) { ?>
									</div>

									<?php
											$this->render_swiper_navigation();
											$this->render_swiper_pagination();

									?>

							</div>

						<?php } ?>


						</div>

				</div>


				<?php if ($settings['ma_el_blog_pagination'] === 'yes') {
				?>
					<div class="ma-el-blog-pagination">
						<?php
											$count_posts = wp_count_posts();
											$published_posts = $count_posts->publish;

											$total_posts = !empty($settings['ma_el_blog_total_posts_number']) ? $settings['ma_el_blog_total_posts_number'] : $published_posts;

											$page_tot = ceil(($total_posts - $offset) / $settings['ma_el_blog_posts_per_page']);
											if ($page_tot > 1) {
												$big        = 999999999;
												echo paginate_links(
													array(
														'base'      => str_replace($big, '%#%', get_pagenum_link(999999999, false)),
														'format'    => '?paged=%#%',
														'current'   => max(1, $paged),
														'total'     => $page_tot,
														'prev_next' => true,
														'prev_text' => sprintf("&lsaquo; %s", $settings['ma_el_blog_prev_text']),
														'next_text' => sprintf("%s &rsaquo;", $settings['ma_el_blog_next_text']),
														'end_size'  => 1,
														'mid_size'  => 2,
														'type'      => 'list'
													)
												);
											}
						?>
					</div>
	<?php }
										wp_reset_postdata();
									}
								}
							}
