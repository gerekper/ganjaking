<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Repeater;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Css_Filter;

// Master Addons Classes
use MasterAddons\Inc\Controls\MA_Group_Control_Transition;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/26/19
 */


// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Image_Carousel extends Widget_Base
{

	public function get_name()
	{
		return 'ma-image-carousel';
	}

	public function get_title()
	{
		return __('Image Carousel', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-media-carousel';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_script_depends()
	{
		return ['swiper', 'fancybox', 'master-addons-scripts'];
	}

	public function get_style_depends()
	{
		return ['fancybox', 'master-addons-main-style'];
	}

	public function get_keywords()
	{
		return ['image', 'image carousel', 'image slider', 'carousel text', 'Image Carousel with Text'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/team-member/';
	}

	protected function _register_controls()
	{

		$this->start_controls_section(
			'ma_el_image_carousel',
			[
				'label' => __('Images', MELA_TD),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'jltma_image_carousel_img',
			[
				'label'   => __('Image', MELA_TD),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'show_label' => false,
				'render'    =>  'template',
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'type'          => Controls_Manager::TEXT,
				'label_block'   => true,
				'label'         => __('Title', MELA_TD),
				'default'       => __('Item Title', MELA_TD)
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'type'          => Controls_Manager::TEXTAREA,
				'label_block'   => true,
				'label'         => __('Subtitle', MELA_TD),
				'default'       => __('item sub-title', MELA_TD)
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'        => __('Link', MELA_TD),
				'type'         => Controls_Manager::URL,
				'default'     => [
					'url' => ''
				],
				'show_external' => true
			]
		);



		$this->add_control(
			'jltma_image_carousel_items',
			[
				'label'             => __('Carousel Items', MELA_TD),
				'type'              => Controls_Manager::REPEATER,
				'default'           => [

					[
						'title'                                  => __('Image Carousel Title 1', MELA_TD),
						'subtitle'                               => __('Image Carousel Sub Title 1', MELA_TD),
						'link'                               	 => __('', MELA_TD),
					],
					[
						'title'                                  => __('Image Carousel Title 2', MELA_TD),
						'subtitle'                               => __('Image Carousel Sub Title 2', MELA_TD),
						'link'                               	 => __('', MELA_TD),
					],
					[
						'title'                                  => __('Image Carousel Title 3', MELA_TD),
						'subtitle'                               => __('Image Carousel Sub Title 3', MELA_TD),
						'link'                               	 => __('', MELA_TD),
					],
					[
						'title'                                  => __('Image Carousel Title 4', MELA_TD),
						'subtitle'                               => __('Image Carousel Sub Title 4', MELA_TD),
						'link'                               	 => __('', MELA_TD),
					],
					[
						'title'                                  => __('Image Carousel Title 5', MELA_TD),
						'subtitle'                               => __('Image Carousel Sub Title 5', MELA_TD),
						'link'                               	 => __('', MELA_TD),
					]
				],
				'fields' 	  		=> $repeater->get_controls(),
				'title_field' => '{{title}}'
			]
		);


		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'          => 'jltma_image_carousel_image',
				'default'       => 'medium',
				'separator'     => 'before'
			]
		);

		$this->add_control(
			'jltma_image_carousel_image_fit',
			[
				'label'   => esc_html__('Image Fit', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => esc_html__('Cover', MELA_TD),
					'contain' => esc_html__('Contain', MELA_TD),
					'auto'    => esc_html__('Auto', MELA_TD),
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-container .jltma-image-carousel-figure img' => 'background-size: {{VALUE}}',
				]
			]
		);



		$this->add_control(
			'jltma_image_carousel_enable_lightbox',
			[
				'type' 				=> Controls_Manager::SWITCHER,
				'label_off' 		=> esc_html__('No', MELA_TD),
				'label_on' 			=> esc_html__('Yes', MELA_TD),
				'return_value' 		=> 'yes',
				'default' 			=> 'yes',
				'label' 			=> esc_html__('Enable Lightbox Gallery?', MELA_TD),
				'frontend_available' 	=> true
			]
		);

		$this->add_control(
			'jltma_image_carousel_lightbox_library',
			[
				'type' 				=> Controls_Manager::SELECT,
				'label' 			=> esc_html__('Lightbox Library', MELA_TD),
				'description' 		=> esc_html__('Choose the preferred library for the lightbox', MELA_TD),
				'options' 			=> array(
					'fancybox' 		=> esc_html__('Fancybox', MELA_TD),
					'elementor' 	=> esc_html__('Elementor', MELA_TD),
				),
				'default' 			=> 'fancybox',
				'condition' 		=> [
					'jltma_image_carousel_enable_lightbox' => 'yes',
				],
			]
		);


		$this->add_control(
			'title_html_tag',
			[
				'label'   => __('Title HTML Tag', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h3',
			]
		);

		$this->end_controls_section();


		/* Carousel Settings */
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => esc_html__('Carousel Settings', MELA_TD),
			]
		);

		$this->add_control(
			'autoheight',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Auto Height', MELA_TD),
				'default' 		=> 'yes',
				'frontend_available' 	=> true
			]
		);

		$this->add_control(
			'carousel_height',
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
					'autoheight!' => 'yes'
				],
			]
		);

		$this->add_control(
			'slide_effect',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Effect', MELA_TD),
				'default' 		=> 'slide',
				'options' 		=> [
					'slide' 	=> __('Slide', MELA_TD),
					'fade' 		=> __('Fade', MELA_TD),
				],
				'frontend_available' => true
			]
		);

		$this->add_control(
			'slide_effect_fade_warning',
			[
				'type' 				=> Controls_Manager::RAW_HTML,
				'raw' 				=> __('The Fade effect ignores the Slides per View and Slides per Column settings', MELA_TD),
				'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				'condition' 		=> [
					'slide_effect' => 'fade'
				],
			]
		);


		$this->add_control(
			'duration_speed',
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
				'frontend_available' => true
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
				'frontend_available' => true
			]
		);


		$this->add_control(
			'carousel_layout_heading',
			[
				'label' 			=> __('Layout', MELA_TD),
				'type' 				=> Controls_Manager::HEADING,
				'separator'			=> 'before'
			]
		);

		$this->add_responsive_control(
			'carousel_direction',
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
				'frontend_available' 	=> true
			]
		);




		$slides_per_view = range(1, 6);
		$slides_per_view = array_combine($slides_per_view, $slides_per_view);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type'           		=> Controls_Manager::SELECT,
				'label'          		=> esc_html__('Slides Per View', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'        		=> '4',
				'tablet_default' 		=> '3',
				'mobile_default' 		=> '2',
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __('Slides Per Column', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'        		=> '4',
				'tablet_default' 		=> '3',
				'mobile_default' 		=> '2',
				'frontend_available' 	=> true,
				'condition' 			=> [
					'carousel_direction' 	=> 'horizontal',
				],
			]
		);


		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => esc_html__('Slides to Scroll', MELA_TD),
				'options' 	=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'   => '1',
				'frontend_available' 	=> true,
			]
		);


		$this->add_responsive_control(
			'columns_spacing',
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
				'frontend_available' => true,
				'condition'				=> [
					'carousel_direction' => 'horizontal',
				],
			]
		);


		$this->add_control(
			'autoplay',
			[
				'label'     	=> esc_html__('Autoplay', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'   	=> 'yes',
				'separator'   	=> 'before',
				'return_value' 	=> 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->start_popover();

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', MELA_TD),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' 	=> true,
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
					'autoplay'           => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause',
			[
				'label'     => esc_html__('Pause on Hover', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->end_popover();




		$this->add_control(
			'free_mode',
			[
				'type' 					=> Controls_Manager::POPOVER_TOGGLE,
				'label' 				=> __('Free Mode', MELA_TD),
				'description'			=> __('Disable fixed positions for slides.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true
			]
		);

		$this->start_popover();

		$this->add_control(
			'free_mode_sticky',
			[
				'type' 					=> Controls_Manager::SWITCHER,
				'label' 				=> __('Snap to position', MELA_TD),
				'description'			=> __('Enable to snap slides to positions in free mode.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true,
				'condition' 			=> [
					'free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Momentum', MELA_TD),
				'description'	=> __('Enable to keep slide moving for a while after you release it.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'separator'		=> 'before',
				'frontend_available' => true,
				'condition' => [
					'free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum_ratio',
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
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode_momentum_velocity',
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
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode_momentum_bounce',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Bounce', MELA_TD),
				'description'	=> __('Set to No if you want to disable momentum bounce in free mode.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true,
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum_bounce_ratio',
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
					'free_mode!' => '',
					'free_mode_momentum!' => '',
					'free_mode_momentum_bounce!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->end_popover();



		$this->add_control(
			'carousel_arrows',
			[
				'label'         => __('Arrows', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'       => 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'arrows_placement',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Placement', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 	=> __('Inside', MELA_TD),
					'outside' 	=> __('Outside', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' => 'yes',
				]
			]
		);

		$this->add_control(
			'arrows_position',
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
					'carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->add_control(
			'arrows_position_vertical',
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
					'carousel_arrows' 	=> 'yes',
					'arrows_placement!' 	=> 'inside',
				]
			]
		);

		$this->end_popover();



		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Infinite Loop', MELA_TD),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'slide_change_resize',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Trigger Resize on Slide', MELA_TD),
				'description'	=> __('Some widgets inside post skins templates might require triggering a window resize event when changing slides to display correctly.', MELA_TD),
				'default' 		=> '',
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'carousel_pagination',
			[
				'label' 		=> __('Pagination', MELA_TD),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'pagination_position',
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
					'carousel_pagination'         => 'yes',
				]
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Type', MELA_TD),
				'default'		=> 'bullets',
				'options' 		=> [
					'bullets' 		=> __('Bullets', MELA_TD),
					'fraction' 		=> __('Fraction', MELA_TD),
				],
				'condition'		=> [
					'carousel_pagination'         => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'carousel_pagination_clickable',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Clickable', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'condition' => [
					'carousel_pagination'         => 'yes',
					'pagination_type'       		=> 'bullets'
				],
				'frontend_available' 	=> true,
			]
		);
		$this->end_popover();


		$this->end_controls_section();




		/*
		Style Tab: Carousel Settings
		*/

		$this->start_controls_section(
			'carousel_style_section',
			[
				'label'         => __('Carousel', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'carousel_arrows_style_heading',
			[
				'label' 	=> __('Arrows', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'     => [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_control(
			'carousel_arrows_position',
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
					'carousel_arrows'         => 'yes',
					'carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_control(
			'carousel_arrows_position_vertical',
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
					'carousel_arrows'         => 'yes',
					'carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'carousel_arrows_size',
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
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'carousel_arrows_padding',
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
					'carousel_arrows'         => 'yes'
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
					'carousel_arrows'         => 'yes'
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
				'separator'		=> 'after',
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'arrows',
				'selector' 		=> '{{WRAPPER}} .jltma-swiper__button',
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);


		$this->start_controls_tabs('carousel_arrow_style_tabs');

		// Normal Tab
		$this->start_controls_tab(
			'carousel_arrow_style_tab',
			[
				'label'         => __('Normal', MELA_TD),
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]

			]
		);
		$this->add_control(
			'arrow_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'arrow_bg_color',
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
			'carousel_arrow_hover_style_tab',
			[
				'label'         => __('Hover', MELA_TD),
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]

			]
		);
		$this->add_control(
			'arrow_hover_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
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
			'carousel_pagination_style_heading',
			[
				'separator'	=> 'before',
				'label' 	=> __('Pagination', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'carousel_pagination' => 'yes',
				]
			]
		);


		$this->add_responsive_control(
			'carousel_pagination_align',
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
					'carousel_pagination' => 'yes',
					'carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_align_vertical',
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
					'carousel_pagination' => 'yes',
					'carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'carousel_pagination_distance',
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
					'carousel_pagination' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_spacing',
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
					'carousel_pagination' => 'yes',
					'pagination_type' => 'bullets',
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
					'carousel_pagination' => 'yes',
					'pagination_type' => 'bullets',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'carousel_pagination_bullet',
				'selector' 		=> '{{WRAPPER}} .swiper-pagination-bullet',
				'condition'		=> [
					'carousel_pagination' => 'yes'
				]
			]
		);


		$this->start_controls_tabs('carousel_pagination_bullets_tabs_hover');

		$this->start_controls_tab('carousel_pagination_bullets_tab_default', [
			'label' 		=> __('Default', MELA_TD),
			'condition'		=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'carousel_pagination_bullets_size',
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
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'carousel_pagination_bullets_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_opacity',
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
					'carousel_pagination' 		=> 'on',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('carousel_pagination_bullets_tab_hover', [
			'label' 		=> __('Hover', MELA_TD),
			'condition'		=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'carousel_pagination_bullets_size_hover',
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
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'carousel_pagination_bullets_color_hover',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_opacity_hover',
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
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('carousel_pagination_bullets_tab_active', [
			'label' => __('Active', MELA_TD),
			'condition'	=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'carousel_pagination_bullets_size_active',
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
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'carousel_pagination_bullets_color_active',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_opacity_active',
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
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/image-carousel/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/image-carousel/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=wXPEl93_UBw" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		
	}


	// Render Function
	protected function render()
	{
		$settings       = $this->get_settings_for_display();

		$this->jltma_render_image_carousel_header($settings);
		$this->jltma_render_image_carousel_loop_item($settings);
		$this->jltma_render_image_carousel_footer($settings);
	}


	// Render Header
	private function jltma_render_image_carousel_header($settings)
	{
		$settings = $this->get_settings_for_display();

		$unique_id 	= implode('-', [$this->get_id(), get_the_ID()]);

		$this->add_render_attribute([
			'jltma-img-carousel-wrapper' => [
				'class' => [
					'jltma-image-carousel-wrapper',
					'jltma-swiper',
					'jltma-swiper__container',
					'swiper-container',
					'elementor-jltma-element-' . $unique_id
				],
				'data-image-carousel-template-widget-id' => $unique_id
			],
			'swiper-wrapper' => [
				'class' => [
					'jltma-image-carousel',
					'jltma-swiper__wrapper',
					'swiper-wrapper',
				],
			],

			'swiper-item' => [
				'class' => [
					'jltma-slider__item',
					'jltma-swiper__slide',
					'swiper-slide',
				],
			],
		]);
?>

		<div <?php echo $this->get_render_attribute_string('jltma-img-carousel-wrapper'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper-wrapper'); ?>>

				<?php
			}



			// Render Header
			private function jltma_render_image_carousel_loop_item($settings)
			{
				$settings = $this->get_settings_for_display();

				$slider_items = $settings['jltma_image_carousel_items'];

				if (empty($slider_items)) {
					return;
				}


				if (count($slider_items) > 1) {
					$demo_images = [];

					if (empty($slider_items[0]['jltma_image_carousel_img']) && empty($slider_items[1]['jltma_image_carousel_img']) && empty($slider_items[0]['jltma_image_carousel_img'])) {
						$demo_images[] = Master_Addons_Helper::jltma_placeholder_images();
					}

					foreach ($slider_items as $index => $item) {

						$images = $item['jltma_image_carousel_img'];
						if (empty($images)) {
							$images = $demo_images;
						}

						// $repeater_key = 'carousel_item' . $index;
						$tag = 'div';
						$image_alt = esc_html($item['title']) . ' : ' . esc_html($item['subtitle']);

						// $repeater_key 				= $this->get_repeater_setting_key('title', 'jltma_image_carousel_items', $index);
						$repeater_key = 'carousel_item' . $index;
						$this->add_render_attribute([
							$repeater_key => [
								'class' => [
									'jltma-slider__item',
									'jltma-swiper__slide',
									'swiper-slide',
								],
							]
						]);


						$tag = 'div';
						$image_alt = esc_html($item['title']) . esc_html($item['subtitle']);
						$title_html_tag = ($settings['title_html_tag']) ? $settings['title_html_tag'] : 'h3';
						$this->add_render_attribute($repeater_key, 'class', 'jltma-logo-slider-item');

						// Website Links
						if ((isset($item['link']['url']) && $item['link']['url']) || $settings['jltma_image_carousel_enable_lightbox'] == "yes") {
							$tag = 'a';
							$this->add_render_attribute($repeater_key, 'class', 'jltma-image-slider-link');
							$this->add_render_attribute($repeater_key, 'target', '_blank');
							$this->add_render_attribute($repeater_key, 'rel', 'noopener');
							$this->add_render_attribute($repeater_key, 'href', esc_url($item['link']['url']));
							$this->add_render_attribute($repeater_key, 'title', $item['title']);
						}

						// Lightbox Conditions
						if ($settings['jltma_image_carousel_enable_lightbox'] == "yes") {


							$anchor_type = (empty($item['link']['url']) ? 'jltma-click-anywhere' : 'jltma-click-icon');

							$thumbnail_src = wp_get_attachment_image_src($item['jltma_image_carousel_img']['id'], 'full');

							if ($thumbnail_src)
								$thumbnail_src = $thumbnail_src[0];

							if ($settings['jltma_image_carousel_lightbox_library'] === 'fancybox') {

								$this->add_render_attribute([
									$repeater_key => [
										'class' => [
											'jltma-lightbox-item ' . $anchor_type,
											'elementor-clickable',
											'ma-el-fancybox'
										],
										'href' => $item['jltma_image_carousel_img']['url'],
										'data-elementor-open-lightbox' => "yes",
										'data-elementor-lightbox-slideshow' => esc_attr($this->get_id()),
										'title' => esc_html($item['title']),
										'data-description' => wp_kses_post($item['subtitle'])
									]
								]);
							} elseif ($settings['jltma_image_carousel_lightbox_library'] === 'elementor') {

								$this->add_render_attribute([
									$repeater_key => [
										'class' => [
											'jltma-lightbox-item-' . isset($anchor_type) ? $anchor_type : ""
										],
										'data-thumb' => $thumbnail_src,
										'href' => $item['jltma_image_carousel_img']['url'],
										'data-elementor-open-lightbox' => "no",
										'title' =>  esc_html($item['title']),
										'data-description' => wp_kses_post($item['subtitle'])
									]
								]);
							}
						}
				?>
						<<?php echo $tag; ?> <?php echo $this->get_render_attribute_string($repeater_key); ?>>
							<figure class="jltma-image-carousel-figure">

								<?php
								if ($settings['jltma_image_carousel_enable_lightbox'] == "yes") {
									echo '<i class="eicon eicon-slider-full-screen"></i>';
								}


								if (isset($item['jltma_image_carousel_img']['id']) && $item['jltma_image_carousel_img']['id']) {
									echo wp_get_attachment_image(
										$item['jltma_image_carousel_img']['id'],
										$item['jltma_image_carousel_image_size'],
										false,
										[
											'class' => 'jltma-carousel-img elementor-animation-',
											'alt' => esc_attr($image_alt),
										]
									);
								} else {
									echo "<img src=" . $images['url'] . ">";
								}

								$this->jltma_image_carousel_title_subtitle();
								?>
							</figure>

						</<?php echo $tag; ?>>

				<?php

					}  // end of foreach

				}
			}

			protected function jltma_image_carousel_title_subtitle()
			{
				$settings = $this->get_settings_for_display();
				?>
				<?php if (isset($item['title']) && $item['title']) { ?>
					<<?php echo $settings['title_html_tag']; ?> class="jltma-image-carousel-title">
						<?php echo $item['title']; ?>
					</<?php echo $settings['title_html_tag']; ?>>
				<?php } ?>

				<?php if (isset($item['subtitle']) && $item['subtitle']) { ?>
					<span class="jltma-image-carousel-subtitle">
						<?php echo $item['subtitle']; ?>
					</span>
				<?php } ?>

			<?php
			}

			// Render Header
			private function jltma_render_image_carousel_footer($settings)
			{

				$settings = $this->get_settings_for_display(); ?>

			</div> <!-- swiper-wrapper -->

		</div>
		<!--/.jltma-logo-slider-->


		<?php
				$this->render_swiper_navigation();
				$this->render_swiper_pagination();
		?>

	<?php
			}


			protected function render_swiper_navigation()
			{
				$settings = $this->get_settings_for_display();
				$this->add_render_attribute([
					'navigation' => [
						'class' => [
							'jltma-arrows',
							'jltma-swiper__navigation',
							'jltma-swiper__navigation--' . $settings['arrows_placement'],
							'jltma-swiper__navigation--' . $settings['arrows_position'],
							'jltma-swiper__navigation--' . $settings['arrows_position_vertical']
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



			public function render_swiper_pagination()
			{
				$settings = $this->get_settings_for_display();
				if ('yes' !== $settings['carousel_pagination'])
					return;

				$this->add_render_attribute('pagination', 'class', [
					'jltma-swiper__pagination',
					'jltma-swiper__pagination--' . $settings['carousel_direction'],
					'jltma-swiper__pagination--' . $settings['pagination_position'],
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
				if ('yes' !== $settings['carousel_arrows'])
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




			private function render_image($image_id, $settings)
			{
				$jltma_image_carousel_image = $settings['jltma_image_carousel_image_size'];
				if ('custom' === $jltma_image_carousel_image) {
					$image_src = Group_Control_Image_Size::get_attachment_image_src($image_id, 'jltma_image_carousel_image', $settings);
				} else {
					$image_src = wp_get_attachment_image_src($image_id, $jltma_image_carousel_image);
					$image_src = $image_src[0];
				}

				return sprintf('<img src="%s" alt="%s" />', esc_url($image_src), esc_html(get_post_meta($image_id, '_wp_attachment_image_alt', true)));
			}


			protected function _content_template()
			{
			}
		}
