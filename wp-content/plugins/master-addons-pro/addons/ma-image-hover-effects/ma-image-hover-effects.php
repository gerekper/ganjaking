<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager as Controls_Manager;
use \Elementor\Group_Control_Border as Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Scheme_Typography as Scheme_Typography;
use \Elementor\Utils;
use \Elementor\Repeater;
use \Elementor\Control_Media;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Scheme_Color;
use \Elementor\Group_Control_Text_Shadow;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 8/28/19
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Image_Hover_Effects extends Widget_Base
{

	public static $ma_el_image_hover_effects;


	public function get_name()
	{
		return 'ma-image-hover-effects';
	}

	public function get_title()
	{
		return esc_html__('Image Hover Effects', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-image-rollover';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_keywords()
	{
		return ['hover', 'image', 'effects', 'image hover', 'banner', 'banner image'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/image-hover-effects/';
	}

	public function get_style_depends()
	{
		return [
			'ma-image-hover-effects',
			'font-awesome-5-all',
			'font-awesome-4-shim',
			'fancybox'
		];
	}

	public function get_script_depends()
	{
		return [
			'imagesloaded',
			'fancybox',
			'font-awesome-4-shim'
		];
	}

	public static function ma_el_image_hover_effects()
	{

		return self::$ma_el_image_hover_effects =
			[
				'lily' 	            => __('Lily', 	                            MELA_TD),
				'sadie' 	        => __('Sadie', 	                        MELA_TD),
				'roxy'              => __('Roxy', 	                            MELA_TD),
				'bubba'             => __('Bubba', 	                        MELA_TD),
				'romeo'             => __('Romeo', 	                        MELA_TD),
				'layla'             => __('Layla', 	                        MELA_TD),
				'honey'             => __('Honey', 	                        MELA_TD),
				'oscar'             => __('Oscar', 	                        MELA_TD),
				'marley'            => __('Marley', 	                        MELA_TD),
				'ruby'              => __('Ruby', 	                            MELA_TD),
				'milo'              => __('Milo', 	                            MELA_TD),
				'dexter'            => __('Dexter', 	                        MELA_TD),
				'sarah'             => __('Sarah', 	                        MELA_TD),
				'zoe'               => __('Zoe', 	                            MELA_TD),
				'chico'             => __('Chico', 	                        MELA_TD),
				'julia'             => __('Julia', 	                        MELA_TD),
				'goliath'           => __('Goliath', 	                        MELA_TD),
				'hera'              => __('Hera', 	                            MELA_TD),
				'winston'           => __('Winston', 	                        MELA_TD),
				'selena'            => __('Selena', 	                        MELA_TD),
				'terry'             => __('Terry', 	                        MELA_TD),
				'phoebe'            => __('Phoebe', 	                        MELA_TD),
				'apollo'            => __('Apollo', 	                        MELA_TD),
				'kira'              => __('Kira', 	                            MELA_TD),
				'steve'             => __('Steve', 	                        MELA_TD),
				'moses'             => __('Moses', 	                        MELA_TD),
				'jazz'              => __('Jazz', 	                            MELA_TD),
				'ming'              => __('Ming', 	                            MELA_TD),
				'lexi'              => __('Lexi', 	                            MELA_TD),
				'duke'              => __('Duke', 	                            MELA_TD),
			];
	}

	protected function _register_controls()
	{


		/*
			* Master Addons: Effects Controls & Image Hover Effects Section Start
			*/

		$this->start_controls_section(
			'ma-image-hover-effect-section',
			[
				'label' => __('Effects & Image', MELA_TD),
			]
		);


		// Premium Version Codes
		

			$this->add_control(
				'ma_el_main_image_effect',
				[
					'label'       => esc_html__('Hover Effect', MELA_TD),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'sadie',
					'options'     => self::ma_el_image_hover_effects()
				]
			);

			//Free Version Codes

		


		$this->add_control(
			'ma_el_main_image',
			[
				'label'			=> __('Upload Image', MELA_TD),
				'description'	=> __('Select an Image', MELA_TD),
				'type'			=> Controls_Manager::MEDIA,
				'default'		=> [
					'url'	=> Utils::get_placeholder_image_src()
				],
			]
		);

		$this->add_control(
			'ma_el_main_image_size',
			[
				'label' 	=> esc_html__('Image Size', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'options' 	=> [
					'main' 				=> esc_html__('Default', MELA_TD),
					'custom' 			=> esc_html__('Custom', MELA_TD),
				],
				'default' 	=> 'main'
			]
		);


		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_thumbnail_size',
				'default' => 'full',
				'condition' => [
					'ma_el_main_image[url]!' => '',
				],
				'exclude'	=> ['custom'],
				'condition' => [
					'ma_el_main_image_size' => 'main',
				],
			]
		);


		$this->add_control(
			'ma_el_main_image_width',
			[
				'label' => __('Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 1
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect figure, {{WRAPPER}} .ma-el-image-hover-effect figure img' => 'width: {{SIZE}}px;'
				],
				'condition' => [
					'ma_el_main_image_size' => 'custom',
				],
			]
		);

		$this->add_control(
			'ma_el_main_image_height',
			[
				'label' => __('Height', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 1
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect figure, {{WRAPPER}} .ma-el-image-hover-effect figure img' => 'height: {{SIZE}}px;'
				],
				'condition' => [
					'ma_el_main_image_size' => 'custom',
				],
			]
		);



		$this->add_control(
			'ma_el_image_hover_link_type',
			[
				'label'        => esc_html__('Links or Popup?', MELA_TD),
				'type'         => Controls_Manager::CHOOSE,
				'options' => [
					'none' => [
						'title' => esc_html__('None', MELA_TD),
						'icon' => 'eicon-close-circle',
					],
					'popup' => [
						'title' => esc_html__('Popup', MELA_TD),
						'icon' => 'eicon-search',
					],
					'links' => [
						'title' => esc_html__('External Links', MELA_TD),
						'icon' => 'eicon-editor-external-link',
					],
				],
				'default' => 'none'
			]
		);


		$this->add_control(
			'ma_el_main_image_more_link_url',
			[
				'label'       => esc_html__('Link URL', MELA_TD),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url'         => '#',
					'is_external' => '',
				],
				'condition'		=> [
					'ma_el_image_hover_link_type'    => 'links'
				],
				'show_external' => true,
			]
		);




		
			$this->add_control(
				'ma_el_image_popup_type',
				[
					'label'                 => esc_html__('Content Type', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'label_block'           => false,
					'options'               => [
						'image'   	=> esc_html__('Image', MELA_TD),
						'content'   => esc_html__('Content', MELA_TD),
						'section'   => esc_html__('Saved Section', MELA_TD),
						'widget'    => esc_html__('Saved Widget', MELA_TD),
						'template'  => esc_html__('Saved Page Template', MELA_TD),
					],
					'default'               => 'content',
					'condition'             => [
						'ma_el_image_hover_link_type'	=> 'popup'
					]
				]
			);
		


		$this->add_control(
			'popup_image',
			[
				'label'                 => esc_html__('Image', MELA_TD),
				'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'ma_el_image_popup_type'		=> 'image',
					'ma_el_image_hover_link_type'	=> 'popup'
				],
				'conditions'            => [
					'terms' => [
						[
							'name'      => 'ma_el_image_popup_type',
							'operator'  => '==',
							'value'     => 'image',
						],
					],
				],
			]
		);

		$this->add_control(
			'popup_content',
			[
				'label'                 => esc_html__('Content', MELA_TD),
				'type'                  => Controls_Manager::WYSIWYG,
				'default'               => esc_html__('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', MELA_TD),
				'condition'             => [
					'ma_el_image_popup_type'	=> 'content',
					'ma_el_image_hover_link_type'	=> 'popup'
				],
			]
		);

		$this->add_control(
			'popup_saved_widget',
			[
				'label'                 => __('Choose Widget', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('widget'),
				'default'               => '-1',
				'condition'             => [
					'ma_el_image_popup_type'    => 'widget',
					'ma_el_image_hover_link_type'	=> 'popup'
				],
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'ma_el_image_popup_type',
							'operator'  => '==',
							'value'     => 'widget',
						],
					],
				],
			]
		);

		$this->add_control(
			'popup_saved_section',
			[
				'label'                 => __('Choose Section', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('section'),
				'default'               => '-1',
				'condition'             => [
					'ma_el_image_popup_type'    => 'section',
					'ma_el_image_hover_link_type'	=> 'popup'
				],
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'ma_el_image_popup_type',
							'operator'  => '==',
							'value'     => 'section',
						],
					],
				],
			]
		);

		$this->add_control(
			'popup_templates',
			[
				'label'                 => __('Choose Template', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Master_Addons_Helper::get_page_template_options('page'),
				'default'               => '-1',
				'condition'             => [
					'ma_el_image_popup_type'    => 'template',
					'ma_el_image_hover_link_type'	=> 'popup'
				],
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'ma_el_image_popup_type',
							'operator'  => '==',
							'value'     => 'template',
						],
					],
				],
			]
		);



		$this->add_responsive_control(
			'ma_el_main_image_vertical_align',
			[
				'label'			=> __('Vertical Align', MELA_TD),
				'type'			=> Controls_Manager::SELECT,
				'condition'		=> [
					'ma_el_main_image_height' => 'custom'
				],
				'options'		=> [
					'flex-start'	=> __('Top', MELA_TD),
					'center'		=> __('Middle', MELA_TD),
					'flex-end'		=> __('Bottom', MELA_TD),
					'inherit'		=> __('Full', MELA_TD)
				],
				'default'       => 'flex-start',
				'selectors'		=> [
					'{{WRAPPER}} .ma-el-image-hover-effect figure' => 'align-items: {{VALUE}}; -webkit-align-items: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();






		/*
			 *  Master Addons: Style Controls
			 */
		$this->start_controls_section(
			'ma_el_main_image_content_heading_section',
			[
				'label' => esc_html__('Heading', MELA_TD)
			]
		);

		$this->add_control(
			'ma_el_main_image_title',
			[
				'label'			=> __('Title', MELA_TD),
				'placeholder'	=> __('Title for this Image', MELA_TD),
				'type'			=> Controls_Manager::TEXTAREA,
				'dynamic'       => ['active' => true],
				'default'		=> __('Master <span>Addons</span>', MELA_TD),
				'label_block'	=> false
			]
		);


		$this->add_control(
			'title_html_tag',
			[
				'label'   => __('HTML Tag', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h2',
			]
		);

		$this->end_controls_section();





		/*
			 *  Master Addons: Sub Heading
			 */
		$this->start_controls_section(
			'ma_el_main_image_content_subheading_section',
			[
				'label' => __('Sub Heading', MELA_TD),
				'condition'     => [
					"ma_el_main_image_effect"   => [
						"honey",
					]
				]
			]
		);

		$this->add_control(
			'ma_el_main_image_sub_title',
			[
				'label'			=> __('Sub Title', MELA_TD),
				'placeholder'	=> __('Sub Title for this Image', MELA_TD),
				'type'			=> Controls_Manager::TEXT,
				'default'		=> __('Elementor', MELA_TD),
				'label_block'	=> false
			]
		);


		$this->end_controls_section();





		/*
             *  Master Addons: Image Descriptions
             */
		$this->start_controls_section(
			'ma_el_main_image_desc_section',
			[
				'label'			=> __('Description', MELA_TD),
				'type'			=> Controls_Manager::HEADING,
				'condition'     => [
					"ma_el_main_image_effect"   => [
						"lily",
						"zoe",
						"sadie",
						"layla",
						"oscar",
						"marley",
						"dexter",
						"sarah",
						"chico",
						"kira",
						"apollo",
						"steve",
						"moses",
						"jazz",
						"ming",
						"lexi",
						"duke",
						"milo",
						"bubba",
						"goliath",
						"selena",
						"roxy",
						"bubba",
						"romeo",
						"ruby"
					]
				]
			]
		);

		$this->add_control(
			'ma_el_main_image_desc',
			[
				'label'			=> __('Description', MELA_TD),
				'description'	=> __('Give the description to this banner', MELA_TD),
				'type'			=> Controls_Manager::TEXTAREA,
				'dynamic'       => ['active' => true],
				'default'		=> __('Master Addons gives your website a vibrant and lively style, you would love.', MELA_TD),
				'label_block'	=> true,
				'condition'     => [
					'ma_el_main_image_effect!'   => ['julia']
				]

			]
		);


		$this->end_controls_section();


		/*
             *  Master Addons: Set 2 Image Descriptions
             */
		$this->start_controls_section(
			'ma_el_main_image_desc_set2_heading',
			[
				'label'			=> __('Description', MELA_TD),
				'type'			=> Controls_Manager::HEADING,
				'description'   => __('Write Description Each line', MELA_TD),
				'condition'     => [
					'ma_el_main_image_effect'   => ['julia']
				]
			]
		);

		$repeater = new Repeater();


		$repeater->add_control(
			'ma_el_main_image_desc_set2',
			[
				'label'         => __('Read More Text', MELA_TD),
				'type'          => Controls_Manager::TEXTAREA,
				'dynamic'       => ['active' => true],
				'default'       => 'Julia dances in the deep dark',
			]
		);


		$this->add_control(
			'ma_el_main_image_desc_set2_tabs',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					['ma_el_main_image_desc_set2' => 'Julia dances in the deep dark'],
					['ma_el_main_image_desc_set2' => 'She loves the smell of the ocean'],
					['ma_el_main_image_desc_set2' => 'And dives into the morning light']
				],
				'fields' 				=> $repeater->get_controls(),
				'title_field'           => '{{ma_el_main_image_desc_set2}}'
			]
		);


		$this->end_controls_section();







		/*
			 *  Master Addons: Image Hover Social Links
			 */
		$this->start_controls_section(
			'ma_el_main_image_social_link_section',
			[
				'label' => esc_html__('Social Links', MELA_TD),
				'condition'     => [
					'ma_el_main_image_effect' => ['zoe', 'hera', 'winston', 'terry', 'phoebe', 'kira']
				]
			]
		);


		/* Icons Dependencies for Styles */

		$this->add_control(
			'ma_el_main_image_icon_heading',
			[
				'label'			=> __('Social Icons', MELA_TD),
				'type'			=> Controls_Manager::HEADING,
				'description'   => __('Select Social Icons', MELA_TD)
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'ma_el_main_image_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fab fa-elementor',
					'library'   => 'brand',
				],
				'render_type'      => 'template'
			]
		);


		$repeater->add_control(
			'ma_el_main_image_icon_link',
			[
				'label' => __('Icon Link', MELA_TD),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://master-addons.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '#',
					'is_external' => true,
				]
			]
		);

		$this->add_control(
			'ma_el_main_image_icon_tabs',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					['ma_el_main_image_icon' => 'fab fa-wordpress'],
					['ma_el_main_image_icon' => 'fab fa-facebook'],
					['ma_el_main_image_icon' => 'fab fa-twitter'],
					['ma_el_main_image_icon' => 'fab fa-instagram'],
				],
				'fields' 				=> $repeater->get_controls(),
				'title_field'           => 'Social Icon'
			]
		);


		$this->end_controls_section();



		/*
			 * Image Hover Style Section
			 */
		$this->start_controls_section(
			'ma_el_main_image_hover_style_section',
			[
				'label' 		=> __('Image', MELA_TD),
				'tab' 			=> Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_main_image_bg_color',
			[
				'label' 		=> __('Background Color', MELA_TD),
				'type' 			=> Controls_Manager::COLOR,
				'default'       => '',
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-image-hover-effect figure' => 'background: {{VALUE}};'
				]
			]
		);


		$this->add_control(
			'ma_el_main_image_opacity',
			[
				'label' => __('Image Opacity', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .8
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => .1
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect figure img' => 'opacity: {{SIZE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_main_image_hover_opacity',
			[
				'label' => __('Hover Opacity', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => .1
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect figure:hover img' => 'opacity: {{SIZE}};'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_filters',
				'label'     => __('Image Filter', MELA_TD),
				'selector' => '{{WRAPPER}} .ma-el-image-hover-effect figure img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'hover_image_filters',
				'label'     => __('Hover Image Filter', MELA_TD),
				'selector'  => '{{WRAPPER}} .ma-el-image-hover-effect figure:hover img'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_main_image_border',
				'selector'      => '{{WRAPPER}} .ma-el-image-hover-effect figure'
			]
		);

		
			$this->add_responsive_control(
				'ma_el_main_image_border_radius',
				[
					'label' => __('Border Radius', MELA_TD),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%', 'em'],
					'selectors' => [
						'{{WRAPPER}} .ma-el-image-hover-effect figure' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		

		
			$this->add_responsive_control(
				'ma_el_main_image_pading',
				[
					'label'         => __('Padding', MELA_TD),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em', '%'],
					'selectors'     => [
						'{{WRAPPER}} .ma-el-image-hover-effect figure' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		


		
			$this->add_responsive_control(
				'ma_el_main_image_margin',
				[
					'label'         => __('Margin', MELA_TD),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em', '%'],
					'selectors'     => [
						'{{WRAPPER}} .ma-el-image-hover-effect figure' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'label'             => __('Box Shadow', MELA_TD),
				'name'              => 'ma_el_main_image_shadow',
				'selector'          => '{{WRAPPER}} .ma-el-image-hover-effect figure'
			]
		);
		$this->end_controls_section();



		/*
             * Title Color
             */
		$this->start_controls_section(
			'ma_el_main_image_title_style',
			[
				'label' 		=> __('Title', MELA_TD),
				'tab' 			=> Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_main_image_title_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1
				],
				'default' => "#fff",
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect .ma-el-image-hover-title' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'jltma_main_image_border_color',
			[
				'label'			=> __('Border Color', MELA_TD),
				'type'			=> Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .ma-el-image-hover-effect figcaption::before'    => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-image-hover-effect figcaption::after'    => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_main_image_title_typography',
				'selector' => '{{WRAPPER}} .ma-el-image-hover-effect .ma-el-image-hover-title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label'             => __('Box Shadow', MELA_TD),
				'name'              => 'ma_el_main_image_title_shadow',
				'selector'          => '{{WRAPPER}} .ma-el-image-hover-title'
			]
		);



		
			$this->add_responsive_control(
				'ma_el_main_image_title_pading',
				[
					'label'         => __('Padding', MELA_TD),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em', '%'],
					'selectors'     => [
						'{{WRAPPER}} .ma-el-image-hover-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		
		
			$this->add_responsive_control(
				'ma_el_main_image_title_margin',
				[
					'label'         => __('Margin', MELA_TD),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em', '%'],
					'selectors'     => [
						'{{WRAPPER}} .ma-el-image-hover-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		

		$this->end_controls_section();


		/*
			 * Description Style
			 */


		$this->start_controls_section(
			'ma_el_main_image_desc_style_section',
			[
				'label' 		=> __('Description', MELA_TD),
				'tab' 			=> Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_main_image_desc_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3
				],
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect p' => 'color: {{VALUE}};'
				],
			]
		);



		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'ma_el_main_image_desc_typography',
				'selector'      => '{{WRAPPER}} .ma-el-image-hover-effect p',
				'scheme'        => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label'             => __('Text Shadow', MELA_TD),
				'name'              => 'ma_el_main_image_desc_text_shadow',
				'selector'          => '{{WRAPPER}} .ma-el-image-hover-effect p',
			]
		);

		
			$this->add_responsive_control(
				'ma_el_main_image_desc_pading',
				[
					'label'         => __('Padding', MELA_TD),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em', '%'],
					'selectors'     => [
						'{{WRAPPER}} .ma-el-image-hover-effect p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		


		
			$this->add_responsive_control(
				'ma_el_main_image_desc_margin',
				[
					'label'         => __('Margin', MELA_TD),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em', '%'],
					'selectors'     => [
						'{{WRAPPER}} .ma-el-image-hover-effect p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);
		

		$this->end_controls_section();



		/*
			 * Social Icons Style
			 */

		$this->start_controls_section(
			'ma_el_main_image_icon_hover_style_section',
			[
				'label' 		=> __('Social Icons', MELA_TD),
				'tab' 			=> Controls_Manager::TAB_STYLE,
				'condition'     => [
					'ma_el_main_image_effect' => ['zoe', 'hera']
				]
			]
		);

		$this->start_controls_tabs('ma_el_main_image_icon_style_tabs');

		$this->start_controls_tab(
			'ma_el_main_image_icon_style_tab_normal',
			[
				'label' => esc_html__('Normal', MELA_TD)
			]
		);

		$this->add_control(
			'ma_el_main_image_icon_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2
				],
				//					'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect .icon-links a' => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_main_image_icon_style_tab_hover',
			[
				'label' => esc_html__('Hover', MELA_TD)
			]
		);

		$this->add_control(
			'ma_el_main_image_icon_hover_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3
				],
				//					'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-image-hover-effect .icon-links a:hover' => 'color: {{VALUE}};'
				],
			]
		);

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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/image-hover-effects/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/image-hover-effects-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=vWGWzuRKIss" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		
	}


	private function render_image($image_id, $settings)
	{
		$image_thumbnail_size = $settings['image_thumbnail_size_size'];
		if ('custom_size' === $image_thumbnail_size) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src($image_id, $image_thumbnail_size, $settings);
		} else {
			$image_src = wp_get_attachment_image_src($image_id, $image_thumbnail_size);
			$image_src = $image_src[0];
		}

		return sprintf('<img src="%s"  class="circled" alt="%s" />', esc_url($image_src), esc_html(get_post_meta($image_id, '_wp_attachment_image_alt', true)));
	}



	protected function render()
	{

		$settings = $this->get_settings_for_display();

		// First 15 Effects
		foreach (array_slice(self::ma_el_image_hover_effects(), 0, 15) as $key => $ma_el_image_hover_value) {
			$ma_el_image_hover_sets = "one";
		}

		// Last 15 Effects
		foreach (array_slice(self::ma_el_image_hover_effects(), 15, 30) as $key => $ma_el_image_hover_value) {
			$ma_el_image_hover_sets = "two";
		}


		$this->add_render_attribute('ma_el_image_hover_effect_wrapper', [
			'class'	=> [
				'ma-el-image-hover-effect',
				'ma-el-image-hover-effect-' . $ma_el_image_hover_sets,
				'ma-el-image-hover-effect-' . esc_attr($settings['ma_el_main_image_effect'])
			],
			'id' => 'ma-el-image-hover-effect-' . $this->get_id()
		]);


		$this->add_render_attribute('ma_el_image_hover_effect_heading', ['class'	=> 'ma-el-image-hover-title']);


		$ma_el_main_image = $this->get_settings_for_display('ma_el_main_image');

		$ma_el_main_image_effect = $settings['ma_el_main_image_effect'];
		$ma_el_main_image_alt = Control_Media::get_image_alt($settings['ma_el_main_image']);


		// Image Link
		if ($settings['ma_el_image_hover_link_type'] == "links") {
			$this->add_render_attribute('ma_el_image_hover_link', [
				'class'	=> ['ma-image-hover-read-more'],
				'href'	=> esc_url($settings['ma_el_main_image_more_link_url']['url']),
			]);

			if ($settings['ma_el_main_image_more_link_url']['is_external']) {
				$this->add_render_attribute('ma_el_image_hover_link', 'target', '_blank');
			}

			if ($settings['ma_el_main_image_more_link_url']['nofollow']) {
				$this->add_render_attribute('ma_el_image_hover_link', 'rel', 'nofollow');
			}
		}

		// if ($settings['ma_el_main_image']['id'] == "") {
		// 	// echo esc_html__('No Image Selected, Please Upload/Select an Image', MELA_TD);
		// 	echo Master_Addons_Helper::jltma_custom_message('No Image Selected', 'Please Upload/Select an Image');
		// }

		$hover_effects_main_image = $settings['ma_el_main_image'];
		$hover_effects_main_image_url = Group_Control_Image_Size::get_attachment_image_src($hover_effects_main_image['id'], 'thumbnail', $settings);
		if (empty($hover_effects_main_image_url)) {
			$hover_effects_main_image_url = $hover_effects_main_image['url'];
		} else {
			$hover_effects_main_image_url = $hover_effects_main_image_url;
		}
?>

		<div <?php echo $this->get_render_attribute_string('ma_el_image_hover_effect_wrapper'); ?>>

			<figure class="effect-<?php echo esc_attr($settings['ma_el_main_image_effect']); ?>">

				<?php if (isset($settings['ma_el_main_image']['id']) && $settings['ma_el_main_image']['id'] != "") {
					echo $this->render_image($settings['ma_el_main_image']['id'], $settings);
				} else {
					echo '<img src="' . esc_url($hover_effects_main_image_url) . '" >';
				} ?>

				<figcaption>
					<div class="ma-image-hover-content">
						<<?php echo $settings['title_html_tag']; ?> <?php echo $this->get_render_attribute_string('ma_el_image_hover_effect_heading'); ?>>

							<?php echo $this->parse_text_editor($settings['ma_el_main_image_title']); ?>

							<?php $ma_el_main_image_sub_title = array("honey");
							if (in_array($ma_el_main_image_effect, $ma_el_main_image_sub_title)) { ?>
								<i><?php echo $settings['ma_el_main_image_sub_title']; ?></i>
							<?php } ?>

						</<?php echo $settings['title_html_tag']; ?>>


						<?php
						// Social Icons Sets
						$ma_el_main_image_socials_array = array("hera", "zoe", "winston", "terry", "phoebe", "kira");
						if (in_array($ma_el_main_image_effect, $ma_el_main_image_socials_array)) { ?>
							<p class="icon-links">
								<?php foreach ($settings['ma_el_main_image_icon_tabs'] as $index => $tab) { ?>
									<a href="<?php echo esc_url_raw($tab['ma_el_main_image_icon_link']['url']); ?>">
										<span><?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $tab['ma_el_main_image_icon'], 'ma_el_main_image_icon'); ?></span>
									</a>
								<?php } ?>
							</p>
						<?php } ?>

						<?php
						// Design Specific Descriptions for Set 1
						//if( $settings['ma_el_main_image_effect'] == "julia" ){
						?>
						<?php if (isset($settings['ma_el_main_image_desc_set2_tabs'])) {
							foreach ($settings['ma_el_main_image_desc_set2_tabs'] as $index => $tab) {
								$ma_el_main_image_effect_one_array = array("julia");
								if (in_array($ma_el_main_image_effect, $ma_el_main_image_effect_one_array)) {
						?>
									<p class="ma-el-image-hover-desc"><?php echo $tab['ma_el_main_image_desc_set2']; ?></p>
							<?php }
							}
						}
						//		}


						// Design Specific Descriptions for Set 1
						$ma_el_main_image_effect_array = array(
							"zoe", "goliath", "selena", "apollo", "steve", "moses", "jazz", "ming", "lexi", "duke",
							"lily", "sadie", "oscar", "layla", "marley", "dexter", "sarah", "chico", "hera", "kira", "milo", "roxy", "bubba", "romeo", "ruby"
						);
						if (in_array($ma_el_main_image_effect, $ma_el_main_image_effect_array)) { ?>
							<p class="ma-el-image-hover-desc">
								<?php echo htmlspecialchars_decode($settings['ma_el_main_image_desc']); ?>
							</p>
						<?php } ?>

					</div>

					<?php if ($settings['ma_el_image_hover_link_type'] == "links" && $settings['ma_el_main_image_more_link_url']['url'] != "") { ?>

						<a <?php echo $this->get_render_attribute_string('ma_el_image_hover_link'); ?>></a>

					<?php } elseif ($settings['ma_el_image_hover_link_type'] == "popup") { ?>

						<a data-fancybox data-src="#jltma-image-hover-<?php echo $this->get_id(); ?>" href="javascript:;" data-animation-duration="700" data-animation="fade" data-modal="false" <?php if ($settings['ma_el_image_popup_type'] == 'image') {
																																																		echo 'href="' . esc_url(
																																																			$settings['popup_image']['url']
																																																		) . '" data-fancybox="images"';
																																																	} ?> class="ma-el-fancybox elementor-clickable" aria-label="Fancybox Popup"></a>

						<div style="display: none;" id="jltma-image-hover-<?php echo $this->get_id(); ?>">

							<div class="card p-4">
								<div class="card-body">
									<?php
									if ($settings['ma_el_image_popup_type'] == 'content') {

										echo do_shortcode($settings['popup_content']);
									} else if ($settings['ma_el_image_popup_type'] == 'image' && !empty($settings['popup_image']['url'])) {

										echo $this->render_image($settings['popup_image']['id'], $settings);
									} else if ($settings['ma_el_image_popup_type'] == 'section' && !empty($settings['popup_saved_section'])) {

										echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['popup_saved_section']);
									} else if ($settings['ma_el_image_popup_type'] == 'template' && !empty($settings['popup_templates'])) {

										echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['popup_templates']);
									} else if ($settings['ma_el_image_popup_type'] == 'widget' && !empty($settings['popup_saved_widget'])) {

										echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['popup_saved_widget']);
									} ?>
								</div>
							</div>

						</div> <!-- jltma-image-hover -->


					<?php } ?>


				</figcaption>

			</figure>



		</div>
<?php
	}

	protected function _content_template()
	{
	}
}
