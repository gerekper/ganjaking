<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Control_Media;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Text_Shadow;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;

use MasterAddons\Inc\Controls\MA_Group_Control_Transition;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/27/19
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.


class Gallery_Slider extends Widget_Base
{

	//use ElementsCommonFunctions;
	public function get_name()
	{
		return 'jltma-gallery-slider';
	}
	public function get_title()
	{
		return esc_html__('Gallery Slider', MELA_TD);
	}
	public function get_icon()
	{
		return 'ma-el-icon eicon-gallery-group';
	}
	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_script_depends()
	{
		return [
			'swiper',
			'imagesloaded',
			'font-awesome-4-shim',
			'elementor-waypoints',
			'master-addons-scripts'
		];
	}


	public function get_style_depends()
	{
		return [
			'swiper',
			'fancybox',
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}


	public function get_keywords()
	{
		return [
			'gallery',
			'image carousel',
			'image slider',
			'carousel gallery',
			'left gallery slider',
			'right gallery slider',
			'slider gallery'
		];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/gallery-slider/';
	}


	protected function _register_controls()
	{

		$this->start_controls_section(
			'jltma_gallery_slider_section_start',
			[
				'label' => esc_html__('Gallery', MELA_TD)
			]
		);

		$this->add_control(
			'jltma_gallery_slider',
			[
				'label' 	=> esc_html__('Add Images', MELA_TD),
				'type' 		=> Controls_Manager::GALLERY,
				'dynamic'	=> ['active' => true],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'jltma_gallery_slider_section_thumbnails',
			[
				'label' => esc_html__('Thumbnails', MELA_TD),
			]
		);


		$this->add_control(
			'jltma_gallery_slider_show_thumbnails',
			[
				'type' 					=> Controls_Manager::SWITCHER,
				'label' 				=> esc_html__('Thumbnails', MELA_TD),
				'default' 				=> 'yes',
				'label_off' 			=> esc_html__('Hide', MELA_TD),
				'label_on' 				=> esc_html__('Show', MELA_TD),
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumbnails_carousel',
			[
				'type' 					=> Controls_Manager::SWITCHER,
				'label' 				=> __('Enable Carousel', MELA_TD),
				'default' 				=> '',
				'label_off' 			=> __('Hide', MELA_TD),
				'label_on' 				=> __('Show', MELA_TD),
				'frontend_available' 	=> true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_thumbnail',
				'label'		=> esc_html__('Thumbnails Size', MELA_TD),
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes'
				],
			]
		);


		$this->add_responsive_control(
			'jltma_gallery_slider_columns',
			[
				'label' 	=> esc_html__('Columns', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '2',
				'tablet_default' 	=> '4',
				'mobile_default' 	=> '4',
				'options' 			=> [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
				],
				'prefix_class'	=> 'jltma-grid-columns%s-',
				'frontend_available' => true,
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_gallery_rand',
			[
				'label' 	=> esc_html__('Ordering', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'options' 	=> [
					'' 		=> esc_html__('Default', MELA_TD),
					'rand' 	=> esc_html__('Random', MELA_TD),
				],
				'default' 	=> '',
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumbnails_caption_type',
			[
				'label' 	=> esc_html__('Caption', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '',
				'options' 	=> [
					'' 				=> esc_html__('None', MELA_TD),
					'title' 		=> esc_html__('Title', MELA_TD),
					'caption' 		=> esc_html__('Caption', MELA_TD),
					'description' 	=> esc_html__('Description', MELA_TD),
				],
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes'
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_carousel_heading',
			[
				'label' 	=> __('Carousel', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				]
			]
		);


		$this->add_control(
			'carousel_orientation',
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
				'condition' 		=> [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_thumb_items',
			[
				'label' 			=> esc_html__('Slides Per View', MELA_TD),
				'type' 				=> Controls_Manager::SELECT,
				'default' 			=> '',
				'tablet_default' 	=> '',
				'mobile_default' 	=> '',
				'options' 			=> [
					'' => __('Default', MELA_TD),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
				],
				'frontend_available' => true,
				'condition' 		=> [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
			]
		);

		$slides_per_column = range(1, 6);
		$slides_per_column = array_combine($slides_per_column, $slides_per_column);

		$this->add_responsive_control(
			'carousel_slides_per_column',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Slides per Column', MELA_TD),
				'description' 	=> __('For Vertical direction this defines the number of slides per row.', MELA_TD),
				'options' 		=> ['' => __('Default', MELA_TD)] + $slides_per_column,
				'condition' 	=> [
					'jltma_gallery_slider_show_thumbnails!' 	=> '',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
					'carousel_orientation!' 					=> 'vertical',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'carousel_slides_to_scroll',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Slides to Scroll', MELA_TD),
				'options' 		=> ['' => __('Default', MELA_TD)] + $slides_per_column,
				'condition' 	=> [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'carousel_spacing',
			[
				'label' 			=> __('Spacing', MELA_TD),
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
					'size' => 6,
					'unit' => 'px',
				],
				'size_units' 		=> ['px'],
				'range' 			=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' 		=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'carousel_resistance',
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
				'condition' 	=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'jltma_gallery_slider_thumb_speed',
			[
				'label' 	=> esc_html__('Speed (ms)', MELA_TD),
				'description' => __('Duration of the effect transition.', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 500,
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
				'condition'				=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => ''
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_show_arrows',
			[
				'type' 		=> Controls_Manager::SWITCHER,
				'label' 	=> esc_html__('Arrows', MELA_TD),
				'default' 	=> '',
				'label_off' => esc_html__('Hide', MELA_TD),
				'label_on' 	=> esc_html__('Show', MELA_TD),
				'return_value'      => 'yes',
				'frontend_available' => true,
				'prefix_class' 	=> 'elementor-arrows-',
				'render_type' 	=> 'template',
				'condition' 	=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
			]
		);


		$this->add_control(
			'carousel_arrows_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Arrow Position', MELA_TD),
				'default'		=> 'middle',
				'options' 		=> [
					'top' 		=> __('Top', MELA_TD),
					'middle' 	=> __('Middle', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
				],
				'condition'		=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
			]
		);

		$this->add_control(
			'carousel_arrows_position_vertical',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Arrow Position', MELA_TD),
				'default'		=> 'center',
				'options' 		=> [
					'left' 	=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'eicon-h-align-center',
					],
					'right' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'eicon-h-align-right',
					],
				],
				'condition'		=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
					'jltma_gallery_slider_thumb_show_arrows!' 	=> '',
					'carousel_orientation' 						=> 'vertical',
				],
			]
		);


		$this->add_control(
			'jltma_gallery_slider_thumb_autoplay',
			[
				'label' 			=> esc_html__('Autoplay', MELA_TD),
				'type' 				=> Controls_Manager::SWITCHER,
				'default'           => '',
				'label_on'          => esc_html__('Yes', MELA_TD),
				'label_off'         => esc_html__('No', MELA_TD),
				'return_value'      => 'yes',
				'frontend_available' => true,
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => ''
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_autoplay_speed',
			[
				'label' 				=> esc_html__('Autoplay Speed', MELA_TD),
				'type' 					=> Controls_Manager::NUMBER,
				'default' 				=> 5000,
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 5000,
					'unit' 	=> 'px',
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 30000,
						'step'	=> 100,
					],
				],
				'frontend_available' 	=> true,
				'condition'				=> [
					'jltma_gallery_slider_thumb_autoplay' 		=> 'yes',
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => ''
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_pause_on_hover',
			[
				'label' 				=> esc_html__('Pause on Hover', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default'           	=> 'yes',
				'label_on'          	=> esc_html__('Yes', MELA_TD),
				'label_off'         	=> esc_html__('No', MELA_TD),
				'return_value'      	=> 'yes',
				'frontend_available' 	=> true,
				'condition'				=> [
					'jltma_gallery_slider_thumb_autoplay' 		=> 'yes',
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => ''
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_infinite',
			[
				'label' 				=> esc_html__('Infinite Loop', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default'           	=> '',
				'label_on'          	=> esc_html__('Yes', MELA_TD),
				'label_off'         	=> esc_html__('No', MELA_TD),
				'return_value'      	=> 'yes',
				'frontend_available' 	=> true,
				'condition'				=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => ''
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_direction',
			[
				'label' 		=> esc_html__('Direction', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'label_block' 	=> false,
				'options' 		=> [
					'ltr' 			=> [
						'title' 		=> esc_html__('Left to Right', MELA_TD),
						'icon' 			=> 'fa fa-arrow-right',
					],
					'rtl' 			=> [
						'title' 		=> esc_html__('Right to Left', MELA_TD),
						'icon' 			=> 'fa fa-arrow-left',
					],
				],
				'default' 		 => 'ltr',
				'style_transfer' => true,
				'condition'				=> [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => ''
				],
			]
		);
		$this->end_controls_section();



		/**
		 * Content Tab: Previews
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_preview',
			[
				'label' => esc_html__('Preview', MELA_TD),
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview',
				'label'		=> esc_html__('Preview Size', MELA_TD),
				'default'	=> 'full',
			]
		);

		$this->add_control(
			'jltma_gallery_slider_link_to',
			[
				'label' 	=> esc_html__('Link to', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'none',
				'options' 	=> [
					'none' 		=> esc_html__('None', MELA_TD),
					'file' 		=> esc_html__('Media File', MELA_TD),
					'custom' 	=> esc_html__('Custom URL', MELA_TD),
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_link',
			[
				'label' 		=> 'Link to',
				'type' 			=> Controls_Manager::URL,
				'placeholder' 	=> esc_html__('https://your-link.com', MELA_TD),
				'condition' 	=> [
					'jltma_gallery_slider_link_to' 	=> 'custom',
				],
				'show_label' 	=> false,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_open_lightbox',
			[
				'label' 			=> esc_html__('Lightbox', MELA_TD),
				'type' 				=> Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_on'          => esc_html__('Yes', MELA_TD),
				'label_off'         => esc_html__('No', MELA_TD),
				'return_value'      => 'yes',
				'condition' 		=> [
					'jltma_gallery_slider_link_to' => 'file',
				],
			]
		);

		// $this->add_control(
		// 	'jltma_gallery_slider_preview_stretch',
		// 	[
		// 		'label' 			=> esc_html__('Image Stretch', MELA_TD),
		// 		'type' 				=> Controls_Manager::SWITCHER,
		// 		'default'           => 'yes',
		// 		'label_on'          => esc_html__('Yes', MELA_TD),
		// 		'label_off'         => esc_html__('No', MELA_TD),
		// 		'return_value'      => 'yes'
		// 	]
		// );

		$this->add_control(
			'jltma_gallery_slider_caption_type',
			[
				'label' 	=> esc_html__('Caption', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'caption',
				'options' 	=> [
					'' 				=> esc_html__('None', MELA_TD),
					'title' 		=> esc_html__('Title', MELA_TD),
					'caption' 		=> esc_html__('Caption', MELA_TD),
					'description' 	=> esc_html__('Description', MELA_TD),
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_slider_heading',
			[
				'label' 	=> __('Slider', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_direction',
			[
				'label' 		=> esc_html__('Direction', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'label_block' 	=> false,
				'options' 		=> [
					'ltr' 			=> [
						'title' 		=> esc_html__('Left to Right', MELA_TD),
						'icon' 			=> 'fa fa-arrow-left',
					],
					'rtl' 			=> [
						'title' 		=> esc_html__('Right to Left', MELA_TD),
						'icon' 			=> 'fa fa-arrow-right',
					],
				],
				'default' 		 => 'ltr',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_infinite',
			[
				'label' 				=> esc_html__('Infinite Loop', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default'           	=> 'yes',
				'label_on'          	=> esc_html__('Yes', MELA_TD),
				'label_off'         	=> esc_html__('No', MELA_TD),
				'return_value'      	=> 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_adaptive_height',
			[
				'label' 				=> esc_html__('Auto Height', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default'           	=> 'yes',
				'label_on'          	=> esc_html__('Yes', MELA_TD),
				'label_off'         	=> esc_html__('No', MELA_TD),
				'return_value'      	=> 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_autoplay',
			[
				'label' 			=> esc_html__('Autoplay', MELA_TD),
				'type' 				=> Controls_Manager::POPOVER_TOGGLE,
				'default'           => 'yes',
				'label_on'          => esc_html__('Yes', MELA_TD),
				'label_off'         => esc_html__('No', MELA_TD),
				'return_value'      => 'yes',
				'frontend_available' => true,
			]
		);

		$this->start_popover();

		$this->add_control(
			'jltma_gallery_slider_autoplay_speed',
			[
				'label' 	=> esc_html__('Autoplay Speed', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 5000,
					'unit' 	=> 'px',
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 10000,
						'step'	=> 1000,
					],
				],
				'frontend_available' => true,
				'condition'	=> [
					'jltma_gallery_slider_autoplay' => 'yes',
				],
			]
		);


		$this->add_control(
			'autoplay_disable_on_interaction',
			[
				'label' 	=> __('Disable on Interaction', MELA_TD),
				'type' 		=> Controls_Manager::SWITCHER,
				'default' 	=> '',
				'frontend_available' => true,
				'condition'	=> [
					'jltma_gallery_slider_autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_pause_on_hover',
			[
				'label' 				=> esc_html__('Pause on Hover', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default'           	=> '',
				'label_on'          	=> esc_html__('Yes', MELA_TD),
				'label_off'         	=> esc_html__('No', MELA_TD),
				'return_value'      	=> 'yes',
				'frontend_available' 	=> true,
				'condition'				=> [
					'jltma_gallery_slider_autoplay' => 'yes',
				],
			]
		);

		$this->end_popover();




		$this->add_control(
			'jltma_gallery_slider_preview_effects_heading',
			[
				'label' 	=> __('Effects', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_effect',
			[
				'label' 	=> esc_html__('Effect', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'slide',
				'options' 	=> [
					'slide' 		=> esc_html__('Slide', MELA_TD),
					'fade' 			=> esc_html__('Fade', MELA_TD),
					'cube' 			=> esc_html__('Cube', MELA_TD),
					'flip' 			=> esc_html__('Flip', MELA_TD),
					'coverflow' 	=> esc_html__('Coverflow', MELA_TD)
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_speed',
			[
				'label' 	=> __('Speed (ms)', MELA_TD),
				'description' => __('How long should the effect transition last.', MELA_TD),
				'type' 		=> Controls_Manager::NUMBER,
				'default' 	=> 1000,
				'min' 		=> 0,
				'max' 		=> 2000,
				'step'		=> 100,
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'jltma_gallery_slider_ken_burns',
			[
				'label' 	=> __('Ken Burns', MELA_TD),
				'type' 		=> Controls_Manager::POPOVER_TOGGLE,
				'default' 	=> '',
				'condition' => [
					'jltma_gallery_slider_effect' => 'fade',
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'jltma_gallery_slider_ken_burns_scale',
			[
				'label' 	=> __('Scale', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 1,
						'max' 	=> 2,
						'step'	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-swiper__container--kenburns .jltma-swiper__slide img' => 'transform: scale({{SIZE}});',
				],
				'condition' => [
					'jltma_gallery_slider_effect' => 'fade',
					'jltma_gallery_slider_ken_burns!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_ken_burns_origin',
			[
				'label' 	=> __('Transform Origin', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'separator' => 'before',
				'label_block' => true,
				'options'	=> [
					'' => __('Default', MELA_TD),
					'random' => __('Random', MELA_TD),
					'custom' => __('Custom', MELA_TD),
				],
				'default' 	=> '',
				'condition' => [
					'jltma_gallery_slider_effect' => 'fade',
					'jltma_gallery_slider_ken_burns!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_ken_burns_origin_x',
			[
				'label' 	=> __('X Anchor Point', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'default' 	=> 'center',
				'options' 	=> [
					'left' 	=> [
						'title' => __('Left', 'elementor-pro'),
						'icon' 	=> 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor-pro'),
						'icon' 	=> 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'elementor-pro'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'jltma_gallery_slider_effect' => 'fade',
					'jltma_gallery_slider_ken_burns!' => '',
					'jltma_gallery_slider_ken_burns_origin' => 'custom',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_ken_burns_origin_y',
			[
				'label' 	=> __('Y Anchor Point', 'elementor-pro'),
				'type' 		=> Controls_Manager::CHOOSE,
				'default' 	=> 'center',
				'options' 	=> [
					'top' => [
						'title' => __('Top', 'elementor-pro'),
						'icon' 	=> 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'elementor-pro'),
						'icon' 	=> 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'elementor-pro'),
						'icon' 	=> 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-swiper__container--kenburns .jltma-swiper__slide img' => 'transform-origin: {{ken_burns_origin_x.VALUE}} {{VALUE}}',
				],
				'condition' => [
					'jltma_gallery_slider_effect' => 'fade',
					'jltma_gallery_slider_ken_burns!' => '',
					'jltma_gallery_slider_ken_burns_origin_x!' => '',
					'jltma_gallery_slider_ken_burns_origin' => 'custom',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_ken_burns_duration',
			[
				'label' 	=> __('Duration', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0.1,
						'max' 	=> 10,
						'step'	=> 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-swiper__container--kenburns .jltma-swiper__slide img' => 'transition-duration: {{SIZE}}s;',
				],
				'condition' => [
					'jltma_gallery_slider_effect' => 'fade',
					'jltma_gallery_slider_ken_burns!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_ken_burns_easing',
			[
				'label' 	=> __('Easing', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'options'	=> MA_Group_Control_Transition::get_easings(),
				'default' 	=> 'linear',
				'selectors' => [
					'{{WRAPPER}} .jltma-swiper__container--kenburns .jltma-swiper__slide img' => 'transition-timing-function: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_effect' 		=> 'fade',
					'jltma_gallery_slider_ken_burns!' 	=> '',
				],
			]
		);

		$this->end_popover();


		$this->add_control(
			'jltma_gallery_slider_preview_arrows_heading',
			[
				'label' 	=> __('Arrows', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_show_arrows',
			[
				'type' 		=> Controls_Manager::SWITCHER,
				'label' 	=> esc_html__('Arrows', MELA_TD),
				'default' 	=> 'yes',
				'label_off' => esc_html__('Hide', MELA_TD),
				'label_on' 	=> esc_html__('Show', MELA_TD),
				'return_value'      => 'yes',
				'frontend_available' => true,
				'prefix_class' 	=> 'elementor-arrows-',
				'render_type' 	=> 'template',
			]
		);


		$this->add_control(
			'jltma_gallery_slider_arrows_position',
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
					'jltma_gallery_slider_show_arrows' 	=> 'yes',
				],
			]
		);

		$this->end_controls_section();



		/**
		 * Style Tab: Layout
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_style_layout',
			[
				'label' 	=> __('Layout', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_position',
			[
				'label' 	=> esc_html__('Preview Position', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'left',
				'tablet_default' 	=> 'top',
				'mobile_default' 	=> 'top',
				'options' 	=> [
					'top' 		=> esc_html__('Top', MELA_TD),
					'right' 	=> esc_html__('Right', MELA_TD),
					'bottom' 	=> esc_html__('Bottom', MELA_TD),
					'left' 		=> esc_html__('Left', MELA_TD),
				],
				'prefix_class'	=> 'jltma-gallery-slider--',
				'frontend_available' => true,
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'layout_horizontal_align',
			[
				'label' 	=> __('Horizontal Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'flex-start' 	=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'eicon-h-align-center',
					],
					'flex-end' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'eicon-h-align-right',
					],
				],
				'default' 		=> 'flex-start',
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider--top .jltma-gallery-slider,
					{{WRAPPER}} .jltma-gallery-slider--bottom .jltma-gallery-slider' => 'align-items: {{VALUE}}',
				],
				'condition' 	=> [
					'jltma_gallery_slider_preview_position' => ['top', 'bottom']
				],
			]
		);


		$this->add_control(
			'jltma_gallery_slider_preview_stack',
			[
				'label' 	=> esc_html__('Stack on', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'tablet',
				'tablet_default' 	=> 'top',
				'mobile_default' 	=> 'top',
				'options' 	=> [
					'tablet' 	=> esc_html__('Tablet & Mobile', MELA_TD),
					'mobile' 	=> esc_html__('Mobile Only', MELA_TD),
				],
				'prefix_class'	=> 'jltma-gallery-slider--stack-',
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
			]
		);


		$this->add_responsive_control(
			'jltma_gallery_slider_preview_width',
			[
				'label' 	=> esc_html__('Preview Width (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' 	=> [
					'size' 	=> 70,
				],
				'condition'	=> [
					'jltma_gallery_slider_preview_position!' => 'top',
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
				'selectors'		=> [
					'{{WRAPPER}}.jltma-gallery-slider--left .jltma-gallery-slider__preview' => 'width: {{SIZE}}%',
					'{{WRAPPER}}.jltma-gallery-slider--right .jltma-gallery-slider__preview' => 'width: {{SIZE}}%',
					'{{WRAPPER}}.jltma-gallery-slider--left .jltma-gallery-slider__gallery' => 'width: calc(100% - {{SIZE}}%)',
					'{{WRAPPER}}.jltma-gallery-slider--right .jltma-gallery-slider__gallery' => 'width: calc(100% - {{SIZE}}%)',
				],
			]
		);


		$this->add_responsive_control(
			'carousel_width',
			[
				'label' 	=> __('Carousel Width', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' 	=> [
					'px' 	=> [
						'min' => 0,
						'max' => 1000,
					],
					'%' 	=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' 	=> [
					'size' 	=> 100,
					'unit' 	=> '%',
				],
				'condition'	=> [
					'jltma_gallery_slider_preview_position' => ['top', 'bottom'],
					'jltma_gallery_slider_show_thumbnails!' => '',
				],
				'selectors'		=> [
					'{{WRAPPER}}.jltma-gallery-slider--top .jltma-gallery-slider__gallery,
					{{WRAPPER}}.jltma-gallery-slider--bottom .jltma-gallery-slider__gallery' => 'max-width: {{SIZE}}{{unit}}',
				],
			]
		);

		$preview_horizontal_margin = is_rtl() ? 'margin-right' : 'margin-left';
		$preview_horizontal_padding = is_rtl() ? 'padding-right' : 'padding-left';

		$this->add_responsive_control(
			'jltma_gallery_slider_preview_spacing',
			[
				'label' 	=> esc_html__('Spacing', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' 	=> [
					'size' 	=> 24,
				],
				'selectors' => [
					'{{WRAPPER}}.jltma-gallery-slider--left .jltma-gallery-slider > *,
								 {{WRAPPER}}.jltma-gallery-slider--right .jltma-gallery-slider > *' => $preview_horizontal_padding . ': {{SIZE}}{{UNIT}};',

					'{{WRAPPER}}.jltma-gallery-slider--left .jltma-gallery-slider,
								 {{WRAPPER}}.jltma-gallery-slider--right .jltma-gallery-slider' => $preview_horizontal_margin . ': -{{SIZE}}{{UNIT}};',

					'{{WRAPPER}}.jltma-gallery-slider--top .jltma-gallery-slider__preview' => 'margin-bottom: {{SIZE}}{{UNIT}};',

					'(tablet){{WRAPPER}}.jltma-gallery-slider--stack-tablet .jltma-gallery-slider__preview' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}}.jltma-gallery-slider--stack-mobile .jltma-gallery-slider__preview' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Preview Styles
		 */


		$this->start_controls_section(
			'jltma_gallery_slider_section_style_preview',
			[
				'label' 	=> esc_html__('Preview', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slides_custom_height',
			[
				'label' 		=> __('Custom Height', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '',
			]
		);

		$this->add_control(
			'slides_image_fit',
			[
				'label' 		=> __('Image Fit', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'cover',
				'options'		=> [
					'cover' 	=> __('Cover', MELA_TD),
					'contain' 	=> __('Contain', MELA_TD),
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-slider__media__thumbnail img' => 'object-fit: {{VALUE}}',
				],
				'condition' 	=> [
					'slides_custom_height!'		 	=> '',
					'jltma_gallery_slider_effect' 	=> ['slide', 'fade'],
				],
			]
		);


		$this->add_control(
			'jltma_gallery_slider_preview_bg_color',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-gallery-slider__slider-wrapper' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				]
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_border',
				'label' 	=> esc_html__('Image Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-swiper__slide',
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-swiper__slide' 	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-swiper__slide',
				'separator'	=> '',
			]
		);

		$this->add_control(
			'jltma_gallery_slider_arrows_style_heading',
			[
				'label' 	=> esc_html__('Arrows', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_arrows_size',
			[
				'label' 		=> esc_html__('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 14,
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 12,
						'max' => 48,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow' => 'font-size: {{SIZE}}px;',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_arrows_padding',
			[
				'label' 		=> esc_html__('Padding', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow' => 'padding: {{SIZE}}em;',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_arrows_distance',
			[
				'label' 		=> esc_html__('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-swiper__navigation--inside.jltma-swiper__navigation--middle.jltma-arrows--horizontal .jltma-arrow' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--middle).jltma-arrows--horizontal .jltma-arrow' => 'margin: {{SIZE}}px;',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' 		=> '',
					'jltma_gallery_slider_thumb_show_arrows!' 	=> '',

				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_arrows_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow' => 'border-radius: {{SIZE}}%;',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'jltma_gallery_slider_arrows',
				'selector' 		=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow, {{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow:before',
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->start_controls_tabs('jltma_gallery_slider_arrows_tabs_hover');

		$this->start_controls_tab('jltma_gallery_slider_arrows_tab_default', [
			'label' => esc_html__('Default', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_show_arrows!' => '',
			]
		]);

		$this->add_control(
			'jltma_gallery_slider_arrows_color',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .jltma-gallery-slider__preview .jltma-arrow i:before' => 'color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_arrows_background_color',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .jltma-gallery-slider__preview .jltma-arrow' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_arrows_tab_hover', [
			'label' => esc_html__('Hover', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_show_arrows!' => '',
			]
		]);

		$this->add_control(
			'jltma_gallery_slider_arrows_color_hover',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow:hover i:before' => 'color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_arrows_background_color_hover',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-arrow:hover' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_arrows_tab_disabled', [
			'label' => esc_html__('Disabled', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_show_arrows!' => '',
			]
		]);

		$this->add_responsive_control(
			'jltma_gallery_slider_arrows_opacity_disabled',
			[
				'label' 		=> esc_html__('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview.swiper-button-disabled' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_show_arrows!' => '',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();




		/**
		 * Style Tab: Preview Captions
		 */
		$this->start_controls_section(
			'jltma_gallery_slider_section_style_preview_captions',
			[
				'label' 	=> esc_html__('Preview Captions', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_vertical_align',
			[
				'label' 	=> esc_html__('Vertical Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'top' 	=> [
						'title' 	=> esc_html__('Top', MELA_TD),
						'icon' 		=> 'eicon-v-align-top',
					],
					'middle' 		=> [
						'title' 	=> esc_html__('Middle', MELA_TD),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'bottom' 		=> [
						'title' 	=> esc_html__('Bottom', MELA_TD),
						'icon' 		=> 'eicon-v-align-bottom',
					],
				],
				'default' 		=> 'bottom',
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_horizontal_align',
			[
				'label' 	=> esc_html__('Horizontal Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'left' 	=> [
						'title' 	=> esc_html__('Left', MELA_TD),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 		=> [
						'title' 	=> esc_html__('Center', MELA_TD),
						'icon' 		=> 'eicon-h-align-center',
					],
					'right' 		=> [
						'title' 	=> esc_html__('Right', MELA_TD),
						'icon' 		=> 'eicon-h-align-right',
					],
					'justify' 		=> [
						'title' 	=> esc_html__('Justify', MELA_TD),
						'icon' 		=> 'eicon-h-align-stretch',
					],
				],
				'default' 		=> 'justify',
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_align',
			[
				'label' 	=> esc_html__('Text Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'left' 	=> [
						'title' 	=> esc_html__('Left', MELA_TD),
						'icon' 		=> 'fa fa-align-left',
					],
					'center' 		=> [
						'title' 	=> esc_html__('Center', MELA_TD),
						'icon' 		=> 'fa fa-align-center',
					],
					'right' 		=> [
						'title' 	=> esc_html__('Right', MELA_TD),
						'icon' 		=> 'fa fa-align-right',
					],
				],
				'default' 	=> 'center',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_typography',
				'label' 	=> esc_html__('Typography', MELA_TD),
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_4,
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption',
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_text_padding',
			[
				'label' 		=> esc_html__('Padding', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-media__content__caption' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_text_margin',
			[
				'label' 		=> esc_html__('Margin', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-media__content__caption' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_text_border',
				'label' 	=> esc_html__('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-media__content__caption',
				'separator' => '',
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_text_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-media__content__caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->end_controls_section();



		/**
		 * Style Tab: Preview Hover Effects
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_preview_hover_effects',
			[
				'label' 	=> esc_html__('Preview Hover Effects', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_hover_preview_captions_heading',
			[
				'label' 	=> esc_html__('Captions', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition' => [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'jltma_gallery_slider_preview_caption',
				'selector' 		=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-media__content,
										{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption',
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->update_control('jltma_gallery_slider_preview_caption_transition', array(
			'default' => 'custom',
		));

		$this->add_control(
			'jltma_gallery_slider_preview_caption_effect',
			[
				'label' 	=> esc_html__('Effect', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '',
				'options' => [
					''					=> esc_html__('None', MELA_TD),
					'fade-in'			=> esc_html__('Fade In', MELA_TD),
					'fade-out'			=> esc_html__('Fade Out', MELA_TD),
					'from-top'			=> esc_html__('From Top', MELA_TD),
					'from-right'		=> esc_html__('From Right', MELA_TD),
					'from-bottom'		=> esc_html__('From Bottom', MELA_TD),
					'from-left'			=> esc_html__('From Left', MELA_TD),
					'fade-from-top'		=> esc_html__('Fade From Top', MELA_TD),
					'fade-from-right'	=> esc_html__('Fade From Right', MELA_TD),
					'fade-from-bottom'	=> esc_html__('Fade From Bottom', MELA_TD),
					'fade-from-left'	=> esc_html__('Fade From Left', MELA_TD),
					'to-top'			=> esc_html__('To Top', MELA_TD),
					'to-right'			=> esc_html__('To Right', MELA_TD),
					'to-bottom'			=> esc_html__('To Bottom', MELA_TD),
					'to-left'			=> esc_html__('To Left', MELA_TD),
					'fade-to-top'		=> esc_html__('Fade To Top', MELA_TD),
					'fade-to-right'		=> esc_html__('Fade To Right', MELA_TD),
					'fade-to-bottom'	=> esc_html__('Fade To Bottom', MELA_TD),
					'fade-to-left'		=> esc_html__('Fade To Left', MELA_TD),
				],
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
					'jltma_gallery_slider_preview_caption_transition!' => '',
				],
			]
		);

		$this->start_controls_tabs('jltma_gallery_slider_preview_caption_style');

		$this->start_controls_tab('jltma_gallery_slider_preview_caption_style_default', [
			'label' 	=> esc_html__('Default', MELA_TD),
			'condition' 	=> [
				'jltma_gallery_slider_caption_type!' => '',
			],
		]);

		$this->add_control(
			'jltma_gallery_slider_preview_text_color',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption' => 'color: {{VALUE}};',
				],
				'condition' 	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_text_background',
				'types' 	=> ['classic', 'gradient'],
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption',
				'default'	=> 'classic',
				'condition' => [
					'jltma_gallery_slider_caption_type!' => '',
				],
				'exclude'	=> [
					'image',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_text_opacity',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__content__caption' => 'opacity: {{SIZE}}',
				],
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_text_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__content__caption',
				'separator'	=> '',
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_preview_caption_style_hover', [
			'label' 	=> esc_html__('Hover', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_caption_type!' => '',
			],
		]);

		$this->add_control(
			'jltma_gallery_slider_preview_text_color_hover',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media:hover .jltma-carousel__content__caption' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_text_background_hover',
				'types' 	=> ['classic', 'gradient'],
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media:hover .jltma-carousel__media__caption',
				'default'	=> 'classic',
				'condition' => [
					'jltma_gallery_slider_caption_type!' => '',
				],
				'exclude'	=> [
					'image',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_text_opacity_hover',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-carousel__media:hover .jltma-carousel__media__caption' => 'opacity: {{SIZE}}',
				],
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_preview_text_border_color_hover',
			[
				'label' 	=> esc_html__('Border Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-carousel__media:hover .jltma-carousel__media__caption' => 'border-color: {{VALUE}};',
				],
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_preview_text_box_shadow_hover',
				'selector' 	=> '{{WRAPPER}} .jltma-carousel__media:hover .jltma-carousel__media__caption',
				'separator'	=> '',
				'condition'	=> [
					'jltma_gallery_slider_caption_type!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		/**
		 * Style Tab: Docs Links
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_style_thumbnails',
			[
				'label' 	=> esc_html__('Thumbnails', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_image_align',
			[
				'label' 		=> esc_html__('Alignment', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> '',
				'options' 		=> [
					'left'    		=> [
						'title' 	=> esc_html__('Left', MELA_TD),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 		=> [
						'title' 	=> esc_html__('Center', MELA_TD),
						'icon' 		=> 'eicon-h-align-center',
					],
					'right' 		=> [
						'title' 	=> esc_html__('Right', MELA_TD),
						'icon' 		=> 'eicon-h-align-right',
					],
				],
				'prefix_class'		=> 'jltma-grid-halign--',
				'condition' 	=> [
					'jltma_gallery_slider_preview_position!' => ['left', 'right']
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_vertical_align',
			[
				'label' 	=> __('Vertical Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'flex-start' 	=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'eicon-v-align-top',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'flex-end' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'eicon-v-align-bottom',
					]
				],
				'default' 		=> 'flex-start',
				'selectors' 	=> [
					'{{WRAPPER}}:not(.jltma-gallery-slider--top) .jltma-gallery-slider,
					{{WRAPPER}}:not(.jltma-gallery-slider--bottom) .jltma-gallery-slider' => 'align-items: {{VALUE}}',
				],
				'condition' 	=> [
					'jltma_gallery_slider_preview_position!' => ['top', 'bottom']
				],
			]
		);

		$columns_horizontal_margin = is_rtl() ? 'margin-right' : 'margin-left';
		$columns_horizontal_padding = is_rtl() ? 'padding-right' : 'padding-left';

		$this->add_responsive_control(
			'jltma_gallery_slider_image_horizontal_spacing',
			[
				'label' 	=> esc_html__('Horizontal spacing', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' 	=> [
					'size' 	=> 0,
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item' => $columns_horizontal_padding . ': {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-gallery' 		=> $columns_horizontal_margin . ': -{{SIZE}}{{UNIT}};',

					'(desktop){{WRAPPER}} .jltma-gallery__item' 	=> 'max-width: calc( 100% / {{jltma_gallery_slider_columns.SIZE}} );',
					'(tablet){{WRAPPER}} .jltma-gallery__item' 	=> 'max-width: calc( 100% / {{jltma_gallery_slider_columns_tablet.SIZE}} );',
					'(mobile){{WRAPPER}} .jltma-gallery__item' 	=> 'max-width: calc( 100% / {{jltma_gallery_slider_columns_mobile.SIZE}} );',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_vertical_spacing',
			[
				'label' 	=> esc_html__('Vertical spacing', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' 	=> [
					'size' 	=> '',
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jltma-gallery' 		=> 'margin-bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_image_border',
				'label' 	=> esc_html__('Image Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media-wrapper img',
				'separator' => '',
			]
		);

		$this->add_control(
			'jltma_gallery_slider_image_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		/**
		 * Style Tab: Thumbnail Captions
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_style_captions',
			[
				'label' 	=> esc_html__('Thumbnails Captions', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_vertical_align',
			[
				'label' 	=> esc_html__('Vertical Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'top' 	=> [
						'title' 	=> esc_html__('Top', MELA_TD),
						'icon' 		=> 'eicon-v-align-top',
					],
					'middle' 		=> [
						'title' 	=> esc_html__('Middle', MELA_TD),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'bottom' 		=> [
						'title' 	=> esc_html__('Bottom', MELA_TD),
						'icon' 		=> 'eicon-v-align-bottom',
					],
				],
				'default' 		=> 'bottom',
				'condition' 	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_horizontal_align',
			[
				'label' 	=> esc_html__('Horizontal Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'left' 	=> [
						'title' 	=> esc_html__('Left', MELA_TD),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 		=> [
						'title' 	=> esc_html__('Center', MELA_TD),
						'icon' 		=> 'eicon-h-align-center',
					],
					'right' 		=> [
						'title' 	=> esc_html__('Right', MELA_TD),
						'icon' 		=> 'eicon-h-align-right',
					],
					'justify' 		=> [
						'title' 	=> esc_html__('Justify', MELA_TD),
						'icon' 		=> 'eicon-h-align-stretch',
					],
				],
				'default' 		=> 'justify',
				'condition' 	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_align',
			[
				'label' 	=> esc_html__('Text Align', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'left' 	=> [
						'title' 	=> esc_html__('Left', MELA_TD),
						'icon' 		=> 'fa fa-align-left',
					],
					'center' 		=> [
						'title' 	=> esc_html__('Center', MELA_TD),
						'icon' 		=> 'fa fa-align-center',
					],
					'right' 		=> [
						'title' 	=> esc_html__('Right', MELA_TD),
						'icon' 		=> 'fa fa-align-right',
					],
				],
				'default' 	=> 'center',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media__caption' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_typography',
				'label' 	=> esc_html__('Typography', MELA_TD),
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_4,
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media__caption',
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_padding',
			[
				'label' 		=> esc_html__('Padding', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media__caption' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_margin',
			[
				'label' 		=> esc_html__('Margin', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media__caption' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_text_border',
				'label' 	=> esc_html__('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media__caption',
				'separator' => '',
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media__caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->end_controls_section();



		/**
		 * Style Tab: Thumbnail Slide
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_thumbnails_slide',
			[
				'label' 	=> esc_html__('Thumbnail Slider', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 		=> 'yes',
					'jltma_gallery_slider_thumbnails_carousel!' => '',
				],
			]
		);


		$this->add_control(
			'jltma_gallery_thumb_slide_image_heading',
			[
				'label' 	=> esc_html__('Thumb Container', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);


		$this->add_control(
			'jltma_gallery_thumb_slide_bg_color',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-swiper__container' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_thumb_slide_border',
				'label' 	=> esc_html__('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-thumb-slide .jltma-swiper__container',
			]
		);

		$this->add_control(
			'jltma_gallery_thumb_slide_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-swiper__container' 	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_thumb_slide_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-thumb-slide .jltma-swiper__container',
				'separator'	=> '',
			]
		);



		$this->add_control(
			'jltma_gallery_thumb_slide_arrow_heading',
			[
				'label' 	=> esc_html__('Arrows', MELA_TD),
				'separator' => 'before',
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_thumb_arrows_width',
			[
				'label' 		=> esc_html__('Navigation Width', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 40,
						'max' => 500,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow' => 'width: {{SIZE}}px !important;',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_thumb_arrows_height',
			[
				'label' 		=> esc_html__('Navigation Height', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 55,
						'max' => 500,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow' => 'height: {{SIZE}}px !important;',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);


		$this->add_responsive_control(
			'jltma_gallery_slider_thumb_arrows_size',
			[
				'label' 		=> esc_html__('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 12,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow' => 'font-size: {{SIZE}}px;',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);


		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'jltma_gallery_slider_thumb_arrows',
				'selector' 		=> '{{WRAPPER}} .jltma-thumb-slide .jltma-arrow,
										{{WRAPPER}} .jltma-thumb-slide .jltma-arrow:before',
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);

		$this->start_controls_tabs('jltma_gallery_slider_thumb_tabs_hover');

		$this->start_controls_tab('jltma_gallery_slider_thumb_tab_default', [
			'label' => esc_html__('Default', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_thumb_show_arrows!' => '',
			]
		]);

		$this->add_control(
			'jltma_gallery_slider_thumb_color',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow i:before' => 'color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_background_color',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);


		$this->add_control(
			'jltma_gallery_thumb_slide_arrow_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow' 	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_thumb_arrows_tab_hover', [
			'label' => esc_html__('Hover', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_thumb_show_arrows!' => '',
			]
		]);

		$this->add_control(
			'jltma_gallery_slider_thumb_color_hover',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow:hover i:before' => 'color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_thumb_background_color_hover',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow:hover' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);


		$this->add_control(
			'jltma_gallery_thumb_slide_arrow_hover_border_radius',
			[
				'label' 		=> esc_html__('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow:hover' 	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab('jltma_gallery_slider_thumb_arrows_tab_disabled', [
			'label' => esc_html__('Disabled', MELA_TD),
			'condition'	=> [
				'jltma_gallery_slider_thumb_show_arrows!' => '',
			]
		]);

		$this->add_responsive_control(
			'jltma_gallery_slider_thumb_arrows_opacity_disabled',
			[
				'label' 		=> esc_html__('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-thumb-slide .jltma-arrow.slick-disabled' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'jltma_gallery_slider_thumb_show_arrows!' => '',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		/**
		 * Style Tab: Thumbnail Hover Effects
		 */

		$this->start_controls_section(
			'jltma_gallery_slider_section_thumbnails_hover_effects',
			[
				'label' 	=> esc_html__('Thumbnails Hover Effects', MELA_TD),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'jltma_gallery_slider_show_thumbnails' 	=> 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_hover_thubmanils_images_heading',
			[
				'label' 	=> esc_html__('Images', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_image',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media-wrapper:hover,
									{{WRAPPER}} .jltma-gallery__media__thumbnail:hover,
									{{WRAPPER}} .jltma-gallery__media__thumbnail img:hover',
				'separator'	=> '',
			]
		);

		$this->update_control('jltma_gallery_slider_image_transition', array(
			'default' => 'custom',
		));

		$this->start_controls_tabs('jltma_gallery_slider_image_style');

		$this->start_controls_tab('jltma_gallery_slider_image_style_default', ['label' => esc_html__('Default', MELA_TD),]);

		$this->add_control(
			'jltma_gallery_slider_image_background_color',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media__thumbnail' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_opacity',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 0.65,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_scale',
			[
				'label' 		=> esc_html__('Scale', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media__thumbnail' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_image_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media__thumbnail',
				'separator'	=> '',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'jltma_gallery_slider_image_css_filters',
				'selector' => '{{WRAPPER}} .jltma-gallery__media__thumbnail img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_image_style_hover', ['label' 	=> esc_html__('Hover', MELA_TD),]);

		$this->add_control(
			'jltma_gallery_slider_image_background_color_hover',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__thumbnail img' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_opacity_hover',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_scale_hover',
			[
				'label' 		=> esc_html__('Scale', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_image_box_shadow_hover',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__thumbnail',
				'separator'	=> '',
			]
		);

		$this->add_control(
			'jltma_gallery_slider_image_border_color_hover',
			[
				'label' 	=> esc_html__('Border Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__thumbnail' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'jltma_gallery_slider_image_css_filters_hover',
				'selector' => '{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__thumbnail img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_image_style_active', ['label' 	=> esc_html__('Active', MELA_TD),]);

		$this->add_control(
			'jltma_gallery_slider_image_background_color_active',
			[
				'label' 	=> esc_html__('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__thumbnail' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_opacity_active',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_image_scale_active',
			[
				'label' 		=> esc_html__('Scale', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_image_box_shadow_active',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__thumbnail',
				'separator'	=> '',
			]
		);

		$this->add_control(
			'jltma_gallery_slider_image_border_color_active',
			[
				'label' 	=> esc_html__('Border Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__thumbnail' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'jltma_gallery_slider_image_css_filters_active',
				'selector' => '{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__thumbnail img',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'jltma_gallery_slider_hover_thubmanils_captions_heading',
			[
				'label' 	=> esc_html__('Captions', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' 	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'jltma_gallery_slider_caption',
				'selector' 		=> '{{WRAPPER}} .jltma-gallery__media__content,
										{{WRAPPER}} .jltma-gallery__media__caption',
				'condition' 	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->update_control('jltma_gallery_slider_caption_transition', array(
			'default' => 'custom',
		));

		$this->add_control(
			'jltma_gallery_slider_caption_effect',
			[
				'label' 	=> esc_html__('Effect', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '',
				'options' => [
					''					=> esc_html__('None', MELA_TD),
					'fade-in'			=> esc_html__('Fade In', MELA_TD),
					'fade-out'			=> esc_html__('Fade Out', MELA_TD),
					'from-top'			=> esc_html__('From Top', MELA_TD),
					'from-right'		=> esc_html__('From Right', MELA_TD),
					'from-bottom'		=> esc_html__('From Bottom', MELA_TD),
					'from-left'			=> esc_html__('From Left', MELA_TD),
					'fade-from-top'		=> esc_html__('Fade From Top', MELA_TD),
					'fade-from-right'	=> esc_html__('Fade From Right', MELA_TD),
					'fade-from-bottom'	=> esc_html__('Fade From Bottom', MELA_TD),
					'fade-from-left'	=> esc_html__('Fade From Left', MELA_TD),
					'to-top'			=> esc_html__('To Top', MELA_TD),
					'to-right'			=> esc_html__('To Right', MELA_TD),
					'to-bottom'			=> esc_html__('To Bottom', MELA_TD),
					'to-left'			=> esc_html__('To Left', MELA_TD),
					'fade-to-top'		=> esc_html__('Fade To Top', MELA_TD),
					'fade-to-right'		=> esc_html__('Fade To Right', MELA_TD),
					'fade-to-bottom'	=> esc_html__('Fade To Bottom', MELA_TD),
					'fade-to-left'		=> esc_html__('Fade To Left', MELA_TD),
				],
				'condition' 	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' 	=> '',
					'jltma_gallery_slider_caption_transition!' 		=> '',
				],
			]
		);

		$this->start_controls_tabs('jltma_gallery_slider_caption_style');

		$this->start_controls_tab('jltma_gallery_slider_caption_style_default', [
			'label' 	=> esc_html__('Default', MELA_TD),
			'condition' => [
				'jltma_gallery_slider_thumbnails_caption_type!' => '',
			],
		]);

		$this->add_control(
			'jltma_gallery_slider_text_color',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_background_color',
			[
				'label' 	=> esc_html__('Background', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_opacity',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption' => 'opacity: {{SIZE}}',
				],
				'condition'	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
					'jltma_gallery_slider_tilt_enable' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_text_box_shadow',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery-slider__preview .jltma-carousel__media__caption',
				'separator'	=> '',
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_caption_style_hover', [
			'label' 	=> esc_html__('Hover', MELA_TD),
			'condition' => [
				'jltma_gallery_slider_thumbnails_caption_type!' => '',
			],
		]);

		$this->add_control(
			'jltma_gallery_slider_text_color_hover',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-carousel__media:hover .jltma-gallery__media__caption' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_background_color_hover',
			[
				'label' 	=> esc_html__('Background', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-carousel__media:hover .jltma-gallery__media__caption' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_opacity_hover',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-carousel__media:hover .jltma-gallery__media__caption' => 'opacity: {{SIZE}}',
				],
				'condition'	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
					'jltma_gallery_slider_tilt_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_border_color_hover',
			[
				'label' 	=> esc_html__('Border Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-carousel__media:hover .jltma-gallery__media__caption' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_text_box_shadow_hover',
				'selector' 	=> '{{WRAPPER}} .jltma-carousel__media:hover .jltma-gallery__media__caption',
				'separator'	=> '',
				'condition'	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
					'jltma_gallery_slider_tilt_enable' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_caption_style_active', [
			'label' 	=> esc_html__('Active', MELA_TD),
			'condition' => [
				'jltma_gallery_slider_thumbnails_caption_type!' => '',
			],
		]);

		$this->add_control(
			'jltma_gallery_slider_text_color_active',
			[
				'label' 	=> esc_html__('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__caption' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_background_color_active',
			[
				'label' 	=> esc_html__('Background', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__caption' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_opacity_active',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__caption' => 'opacity: {{SIZE}}',
				],
				'condition'	=> [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
					'jltma_gallery_slider_tilt_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_text_border_color_active',
			[
				'label' 	=> esc_html__('Border Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__caption' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_text_box_shadow_active',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__caption',
				'separator'	=> '',
				'condition' => [
					'jltma_gallery_slider_thumbnails_caption_type!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'jltma_gallery_slider_hover_thubmanils_overlay_heading',
			[
				'label' 	=> esc_html__('Overlay', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay',
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media__overlay',
				'separator'	=> 'after',
			]
		);

		$this->update_control('jltma_gallery_slider_overlay_transition', array(
			'default' => 'custom',
		));

		$this->start_controls_tabs('jltma_gallery_slider_overlay_style');

		$this->start_controls_tab('jltma_gallery_slider_overlay_style_default', ['label' => esc_html__('Default', MELA_TD)]);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay_background',
				'types' 	=> ['classic', 'gradient'],
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media__overlay',
				'default'	=> 'classic',
				'exclude'	=> [
					'image',
				]
			]
		);

		$this->add_control(
			'jltma_gallery_slider_overlay_blend',
			[
				'label' 		=> esc_html__('Blend mode', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'normal',
				'options' => [
					'normal'			=> esc_html__('Normal', MELA_TD),
					'multiply'			=> esc_html__('Multiply', MELA_TD),
					'screen'			=> esc_html__('Screen', MELA_TD),
					'overlay'			=> esc_html__('Overlay', MELA_TD),
					'darken'			=> esc_html__('Darken', MELA_TD),
					'lighten'			=> esc_html__('Lighten', MELA_TD),
					'color'				=> esc_html__('Color', MELA_TD),
					'color-dodge'		=> esc_html__('Color Dodge', MELA_TD),
					'hue'				=> esc_html__('Hue', MELA_TD),
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-gallery__media__overlay' => 'mix-blend-mode: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'jltma_gallery_slider_overlay_blend_notice',
			[
				'type' 				=> Controls_Manager::RAW_HTML,
				'raw' 				=> sprintf(esc_html__('Please check blend mode support for your browser %1$s here %2$s', MELA_TD), '<a href="https://caniuse.com/#search=mix-blend-mode" target="_blank">', '</a>'),
				'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' 		=> [
					'jltma_gallery_slider_overlay_blend!' => 'normal'
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_overlay_margin',
			[
				'label' 	=> esc_html__('Margin', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 48,
						'min' 	=> 0,
						'step' 	=> 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_overlay_opacity',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media__overlay' => 'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay_border',
				'label' 	=> esc_html__('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media__overlay',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_overlay_style_hover', ['label' => esc_html__('Hover', MELA_TD)]);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay_background_hover',
				'types' 	=> ['classic', 'gradient'],
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__overlay',
				'default'	=> 'classic',
				'exclude'	=> [
					'image',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_overlay_margin_hover',
			[
				'label' 	=> esc_html__('Margin', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 48,
						'min' 	=> 0,
						'step' 	=> 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_overlay_opacity_hover',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__overlay' => 'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay_border_hover',
				'label' 	=> esc_html__('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__media:hover .jltma-gallery__media__overlay',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('jltma_gallery_slider_overlay_style_active', ['label' => esc_html__('Active', MELA_TD)]);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay_background_active',
				'types' 	=> ['classic', 'gradient'],
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__overlay',
				'default'	=> 'classic',
				'exclude'	=> [
					'image',
				]
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_overlay_margin_active',
			[
				'label' 	=> esc_html__('Margin', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 48,
						'min' 	=> 0,
						'step' 	=> 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_gallery_slider_overlay_opacity_active',
			[
				'label' 	=> esc_html__('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__overlay' => 'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'jltma_gallery_slider_overlay_border_active',
				'label' 	=> esc_html__('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .jltma-gallery__item.is--active .jltma-gallery__media__overlay',
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/gallery-slider/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/gallery-slider-for-elementor/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=9amvO6p9kpM" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		//Upgrade to Pro

	}


	/**
	 * Render Content
	 *
	 * @return void
	 */
	protected function render()
	{


		$settings  = $this->get_settings_for_display();

		if (!$settings['jltma_gallery_slider'])
			return;

		// Get the Unique ID
		$unique_id 	= implode('-', [$this->get_id(), get_the_ID()]);

		$this->add_render_attribute([
			'thumbs-wrapper' => [
				'class' => [
					'jltma-gallery-slider',
					'elementor-jltma-element-' . $unique_id
				],
				'data-jltma-template-widget-id' => $unique_id
			],
			'preview' => [
				'class' => [
					'jltma-gallery-slider__preview',
					'jltma-swiper',
				],
			],
			'slider-container' => [
				'class' => [
					'swiper-container',
					'jltma-swiper__container',
					// 'jltma-gallery-slider__carousel',
					'jltma-gallery-slider__slider-wrapper'
				],
			],
			'gallery-wrapper' => [
				'class' => [
					'jltma-gallery-slider__gallery',
					// 'jltma-swiper',
				],
			],
			'swiper-wrapper' => [
				'class' => [
					'swiper-container',
					'jltma-swiper__container',
					'jltma-gallery-slider__slider',
					'jltma-media-align--' . $settings['jltma_gallery_slider_preview_vertical_align'],
					'jltma-media-align--' . $settings['jltma_gallery_slider_preview_horizontal_align'],
					'jltma-media-effect__content--' . $settings['jltma_gallery_slider_caption_effect'],
				],
			],
			'slider' => [
				'class' => [
					'swiper-wrapper',
					'jltma-swiper__wrapper'
				],
			],
			'swiper-container' => [
				'class' => [
					'jltma-gallery-slider__slider-wrapper'
				],
			],

			'gallery-thumbnail' => [
				'class' => [
					'jltma-media__thumbnail',
					'jltma-gallery__media__thumbnail',
				],
			],
			'gallery-overlay' => [
				'class' => [
					'jltma-media__overlay',
					'jltma-gallery__media__overlay',
				],
			],
			'gallery-content' => [
				'class' => [
					'jltma-media__content',
					'jltma-gallery__media__content',
				],
			],
			'gallery-caption' => [
				'class' => [
					'wp-caption-text',
					'jltma-media__content__caption',
					'jltma-gallery__media__caption',
				],
			],
			'gallery-item' => [
				'class' => [
					'jltma-gallery__item',
					'jltma-grid__item',
					'jltma-swiper__slide',
					'swiper-slide',
				],
			],
		]);

		if ($settings['jltma_gallery_slider_columns']) {
			$this->add_render_attribute('shortcode', 'columns', $settings['jltma_gallery_slider_columns']);
		}

		if (!empty($settings['jltma_gallery_slider_gallery_rand'])) {
			$this->add_render_attribute('shortcode', 'orderby', $settings['jltma_gallery_slider_gallery_rand']);
		}

		if (!empty($settings['jltma_gallery_slider_ken_burns']) && '' !== $settings['jltma_gallery_slider_ken_burns']) {
			$this->add_render_attribute('swiper-wrapper', 'class', 'jltma-swiper__container--kenburns');

			if ('random' === $settings['jltma_gallery_slider_ken_burns_origin']) {
				$this->add_render_attribute('swiper-wrapper', 'class', 'jltma-swiper__container--kenburns-random');
			}
		}
?>

		<div <?php echo $this->get_render_attribute_string('thumbs-wrapper'); ?>>

			<!--Preview Slider -->
			<div <?php echo $this->get_render_attribute_string('preview'); ?> dir="<?php echo $settings['jltma_gallery_slider_direction']; ?>">
				<div <?php echo $this->get_render_attribute_string('swiper-container'); ?>>
					<div <?php echo $this->get_render_attribute_string('swiper-wrapper'); ?>>
						<div <?php echo $this->get_render_attribute_string('slider'); ?>>
							<?php echo $this->jltma_render_preview(); ?>
						</div>
						<?php if ('' !== $settings['jltma_gallery_slider_show_arrows']) {
							$this->render_swiper_navigation(
								'slider',
								'horizontal',
								$settings['jltma_gallery_slider_arrows_position']
							);
						} ?>
					</div>
				</div>
			</div>

			<?php $this->render_thumb_gallery(); ?>

		</div>

	<?php
	}

	protected function render_thumb_gallery()
	{
		$settings = $this->get_settings_for_display();

		// Thumbnails Position for CSS class
		switch ($settings['jltma_gallery_slider_preview_position']) {
			case 'top':
				$thumb_position = "bottom";
				break;
			case 'right':
				$thumb_position = "left";
				break;
			case 'left':
				$thumb_position = "right";
				break;
			case 'bottom':
				$thumb_position = "top";
				break;
			default:
				$thumb_position = "bottom";
				break;
		}
		$is_carousel = '' !== $settings['jltma_gallery_slider_thumbnails_carousel'];

		$this->add_render_attribute([
			'gallery-wrapper' => [
				'class' => [
					'jltma-thumb-slide',
					'jltma-swiper',
					'jltma-thumb-position-' . esc_attr($thumb_position)
				],
			],
			'gallery' => [
				'class' => [
					'jltma-swiper__wrapper',
					'swiper-wrapper',
					'jltma-media-align--' . $settings['jltma_gallery_slider_thumb_vertical_align'],
					'jltma-media-align--' . $settings['jltma_gallery_slider_thumb_horizontal_align'],

				],
			],
			'gallery-item' => [
				'class' => [
					'jltma-swiper__slide',
					'swiper-slide',
				],
			]
		]);

		if ($is_carousel) {

			$this->add_render_attribute([
				'gallery-wrapper' => [
					'class' => [
						'jltma-swiper'
					],
				],
				'swiper-container-wrapper' => [
					'class' => [
						'jltma-gallery-slider__carousel-wrapper',
						'jltma-swiper__container-wrapper',
					],
				],
				'swiper-container' => [
					'class' => [
						'swiper-container',
						'jltma-swiper__container',
						'jltma-gallery-slider__carousel',
					],
				],

				'gallery-item' => [
					'class' => [
						'jltma-swiper__slide',
						'swiper-slide',
					],
				]
			]);
		}
	?>

		<!--Thumbnail Slider -->
		<?php if ('yes' === $settings['jltma_gallery_slider_show_thumbnails']) : ?>

			<div <?php echo $this->get_render_attribute_string('gallery-wrapper'); ?>>
				<div <?php echo $this->get_render_attribute_string('swiper-container-wrapper'); ?>>
					<div <?php echo $this->get_render_attribute_string('swiper-container'); ?> dir="<?php echo $settings['jltma_gallery_slider_thumb_direction']; ?>">
						<div <?php echo $this->get_render_attribute_string('gallery'); ?>>
							<?php echo $this->jltma_render_thumbs(); ?>
						</div>
						<?php

						if ($is_carousel && '' !== $settings['jltma_gallery_slider_thumb_show_arrows']) {
							$this->render_swiper_navigation(
								'carousel',
								$settings['carousel_orientation'],
								$settings['carousel_arrows_position'],
								$settings['carousel_arrows_position_vertical']
							);
						} ?>

					</div>
				</div>
			</div>

		<?php endif; ?>

	<?php
	}

	protected function render_swiper_navigation($key, $direction, $halign = 'center', $valign = 'middle')
	{

		$settings = $this->get_settings();
		$nav_key = $this->get_repeater_setting_key('navigation', 'swiper', $key);

		$this->add_render_attribute([
			$nav_key => [
				'class' => [
					'jltma-arrows',
					'jltma-arrows--' . $direction,
					'jltma-swiper__navigation',
					'jltma-swiper__navigation--inside',
					'jltma-swiper__navigation--' . $halign,
					'jltma-swiper__navigation--' . $valign,
				],
			],
		]);

	?>
		<div <?php echo $this->get_render_attribute_string($nav_key); ?>>
			<?php $this->render_swiper_arrows($key); ?>
		</div>
	<?php
	}

	protected function render_swiper_arrows($key)
	{

		$prev = is_rtl() ? 'right' : 'left';
		$next = is_rtl() ? 'left' : 'right';

		$prev_key 		= $this->get_repeater_setting_key('arrow', 'prev', $key);
		$prev_icon_key 	= $this->get_repeater_setting_key('arrow-icon', 'prev', $key);
		$next_key 		= $this->get_repeater_setting_key('arrow', 'next', $key);
		$next_icon_key 	= $this->get_repeater_setting_key('arrow-icon', 'next', $key);

		$this->add_render_attribute([
			$prev_key => [
				'class' => [
					'jltma-swiper__button',
					'jltma-swiper__button--prev',
					'jltma-arrow',
					'jltma-arrow--prev',
					'jltma-swiper__button--prev-' . $key,
				],
			],
			$prev_icon_key => [
				'class' => 'eicon-chevron-' . $prev,
			],
			$next_key => [
				'class' => [
					'jltma-swiper__button',
					'jltma-swiper__button--next',
					'jltma-arrow',
					'jltma-arrow--next',
					'jltma-swiper__button--next-' . $key,
				],
			],
			$next_icon_key => [
				'class' => 'eicon-chevron-' . $next,
			],
		]);

	?>
		<div <?php echo $this->get_render_attribute_string($prev_key); ?>>
			<i <?php echo $this->get_render_attribute_string($prev_icon_key); ?>></i>
		</div>
		<div <?php echo $this->get_render_attribute_string($next_key); ?>>
			<i <?php echo $this->get_render_attribute_string($next_icon_key); ?>></i>
		</div>
		<?php
	}


	public function jltma_get_link_url($attachment, $instance)
	{
		if ('none' === $instance['jltma_gallery_slider_link_to']) {
			return false;
		}

		if ('custom' === $instance['jltma_gallery_slider_link_to']) {
			if (empty($instance['jltma_gallery_slider_link']['url'])) {
				return false;
			}
			return $instance['jltma_gallery_slider_link'];
		}

		return [
			'url' => wp_get_attachment_url($attachment['id']),
		];
	}

	// Get Image Caption
	protected function jltma_get_image_caption($attachment, $type = 'caption')
	{

		if (empty($type)) {
			return '';
		}

		if (!is_a($attachment, 'WP_Post')) {
			if (is_numeric($attachment)) {
				$attachment = get_post($attachment);

				if (!$attachment) return '';
			}
		}

		if ('caption' === $type) {
			return $attachment->post_excerpt;
		}

		if ('title' === $type) {
			return $attachment->post_title;
		}

		return $attachment->post_content;
	}

	// Render Carousel
	protected function jltma_render_preview()
	{
		$settings 	= $this->get_settings_for_display();

		$gallery 	= $settings['jltma_gallery_slider'];
		$slides 	= [];

		foreach ($gallery as $index => $item) {

			$image_url = Group_Control_Image_Size::get_attachment_image_src($item['id'], 'jltma_gallery_slider_preview', $settings);
			$image_html = '<img class="swiper-slide-image" src="' . esc_attr($image_url) . '" alt="' . esc_attr(Control_Media::get_image_alt($item)) . '" />';

			$link = $this->jltma_get_link_url($item, $settings);
			$image_caption = $this->jltma_get_image_caption($item['id'], $settings['jltma_gallery_slider_caption_type']);

			$slide_key 			= $this->get_repeater_setting_key('slide', 'jltma_gallery_slider', $index);
			$slide_inner_key 	= $this->get_repeater_setting_key('slide-inner', 'jltma_gallery_slider', $index);
			$thumbnail_key 		= $this->get_repeater_setting_key('thumbnail', 'jltma_gallery_slider', $index);
			$slide_tag 			= 'div';

			$this->add_render_attribute([
				$slide_key => [
					'class' => [
						'jltma-slider__slide',
						'jltma-swiper__slide',
						'swiper-slide',
					]
				],
				$slide_inner_key => [
					'class' => [
						'swiper-slide-inner',
						'jltma-media',
						'jltma-carousel__media',
					],
				],
				$thumbnail_key => [
					'class' => [
						'jltma-media__thumbnail',
						'jltma-carousel__media__thumbnail',
					],
				]

			]);

			if ($link) {

				$slide_tag = 'a';

				$this->add_render_attribute($slide_key, [
					'href' 								=> $link['url'],
					'class' 							=> 'elementor-clickable',
					'data-elementor-open-lightbox' 		=> $settings['jltma_gallery_slider_open_lightbox'],
					'data-elementor-lightbox-slideshow' => $this->get_id(),
					'data-elementor-lightbox-index' 	=> $index
				]);

				if (!empty($link['is_external'])) {
					$this->add_render_attribute($slide_key, 'target', '_blank');
				}

				if (!empty($link['nofollow'])) {
					$this->add_render_attribute($slide_key, 'rel', 'nofollow');
				}
			}

			$slide_html = '<' . $slide_tag . ' ' . $this->get_render_attribute_string($slide_key) . '>';
			$slide_html .= '<figure ' . $this->get_render_attribute_string($slide_inner_key) . '>';
			$slide_html .= '<div ' . $this->get_render_attribute_string($thumbnail_key) . '>' . $image_html . '</div>';

			if (!empty($image_caption)) {

				$content_key = $this->get_repeater_setting_key('content', 'jltma_gallery_slider', $index);
				$caption_key = $this->get_repeater_setting_key('caption', 'jltma_gallery_slider', $index);

				$this->add_render_attribute([
					$content_key => [
						'class' => [
							'jltma-media__content',
							'jltma-carousel__media__content',
						],
					],
					$caption_key => [
						'class' => [
							'jltma-media__content__caption',
							'jltma-carousel__media__caption',
						],
					],
				]);

				$slide_html .= '<div ' . $this->get_render_attribute_string($content_key) . '>';
				$slide_html .= '<figcaption ' . $this->get_render_attribute_string($caption_key) . '>';
				$slide_html .= $image_caption;
				$slide_html .= '</figcaption>';
				$slide_html .= '</div>';
			}

			$slide_html .= '</figure>';
			$slide_html .= '</' . $slide_tag . '>';

			$slides[] = $slide_html;
		}

		echo implode('', $slides);
	}

	// Render Gallery Slider
	protected function jltma_render_thumbs()
	{

		$settings 			= $this->get_settings_for_display();
		$gallery 			= $settings['jltma_gallery_slider'];
		$media_tag 			= 'figure';

		foreach ($gallery as $index => $item) {

			$item_url = (in_array('url', $item)) ? $item['url'] : '';

			$image = $this->jltma_get_image_info($item['id'], $item_url, $settings['jltma_gallery_slider_thumbnail_size']);

			$gallery_media_key = $this->get_repeater_setting_key('gallery-media', 'jltma_gallery_slider', $index);
			$gallery_media_wrapper_key = $this->get_repeater_setting_key('gallery-media-wrapper', 'jltma_gallery_slider', $index);

			$this->add_render_attribute([
				$gallery_media_key => [
					'class' => [
						'jltma-media',
						'jltma-gallery__media',
					],
				],
				$gallery_media_wrapper_key => [
					'class' => [
						'jltma-media__wrapper',
						'jltma-gallery__media-wrapper',
					],
				],
			]);

			if (empty($image))
				continue;

		?>

			<div <?php echo $this->get_render_attribute_string('gallery-item'); ?>>

				<<?php echo $media_tag; ?> <?php echo $this->get_render_attribute_string($gallery_media_key); ?>>
					<div <?php echo $this->get_render_attribute_string($gallery_media_wrapper_key); ?>>
						<?php $this->render_image_thumbnail($image); ?>
						<?php $this->render_image_overlay(); ?>
						<?php $this->render_image_caption($item); ?>
					</div>
				</<?php echo $media_tag; ?>>

			</div>

		<?php }
	}


	protected function render_image_overlay()
	{ ?>
		<div <?php echo $this->get_render_attribute_string('gallery-overlay'); ?>></div>
	<?php }


	protected function render_image_thumbnail($image)
	{ ?>
		<div <?php echo $this->get_render_attribute_string('gallery-thumbnail'); ?>>
			<?php echo $image['image']; ?>
		</div>
		<?php
	}


	protected function render_image_caption($item)
	{
		if ($this->get_settings('jltma_gallery_slider_thumbnails_caption_type') !== "") { ?>

			<figcaption <?php echo $this->get_render_attribute_string('gallery-content'); ?>>
				<div <?php echo $this->get_render_attribute_string('gallery-caption'); ?>>
					<?php echo $this->jltma_get_image_caption($item['id'], $this->get_settings('jltma_gallery_slider_thumbnails_caption_type')); ?>
				</div>
			</figcaption>
<?php
		}
	}


	protected function jltma_get_image_info($image_id, $image_url = '', $image_size = '')
	{

		if (!$image_id)
			return false;

		$info = [];

		if (!empty($image_id)) { // Existing attachment

			$attachment = get_post($image_id);

			if (!$attachment)
				return;

			$info['id']			= $image_id;
			$info['url']		= $image_url;
			$info['image'] 		= wp_get_attachment_image($attachment->ID, $image_size, true);
			$info['caption'] 	= $attachment->post_excerpt;
		} else { // Placeholder image, most likely

			if (empty($image_url))
				return;

			$info['id']			= false;
			$info['url']		= $image_url;
			$info['image'] 		= '<img src="' . $image_url . '" />';
			$info['caption'] 	= '';
		}

		return $info;
	}

	// JS Template
	protected function _content_template()
	{
	}
}
