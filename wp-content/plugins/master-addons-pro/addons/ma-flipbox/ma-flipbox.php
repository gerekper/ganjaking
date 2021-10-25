<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/26/19
 */

use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Scheme_Color;
use \Elementor\Modules\DynamicTags\Module as TagsModule;
use MasterAddons\Inc\Helper\Master_Addons_Helper;


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Flipbox extends Widget_Base
{

	public function get_name()
	{
		return 'ma-flipbox';
	}

	public function get_title()
	{
		return esc_html__('Flipbox', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-flip-box';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/flipbox/';
	}

	protected function _register_controls()
	{







		/*-----------------------------------------------------------------------------------*/
		/*	STYLE PRESETS
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_flipbox_style',
			[
				'label' => esc_html__('Style Preset', MELA_TD)
			]
		);



		// Premium Version Codes
		

			$this->add_control(
				'ma_flipbox_layout_style',
				[
					'label' => __('Design Variation', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'one'           => __('Default', MELA_TD),
						'two'           => __('Front Image', MELA_TD),
						'three'         => __('Diagnonal', MELA_TD),
						'four'          => __('Front Icon', MELA_TD)
					],
					'default' => 'one',
				]
			);

			//Free Codes
		




		// Premium Version Codes
		

			$this->add_control(
				'animation_style',
				[
					'label' => __('Animation Style', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'horizontal'                            => esc_html__('Flip Horizontal', MELA_TD),
						'vertical'                              => esc_html__('Flip Vertical', MELA_TD),
						'fade'                                  => esc_html__('Fade', MELA_TD),
						'flipcard flipcard-rotate-top-down'     => esc_html__('Cube - Top Down', MELA_TD),
						'flipcard flipcard-rotate-down-top'     => esc_html__('Cube - Down Top', MELA_TD),
						'flipcard flipcard-rotate-left-right'   => esc_html__('Cube - Left Right', MELA_TD),
						'flipcard flipcard-rotate-right-left'   => esc_html__('Cube - Right Left', MELA_TD),
						'flip box'                              => esc_html__('Flip Box', MELA_TD),
						'flip box fade'                         => esc_html__('Flip Box Fade', MELA_TD),
						'flip box fade up'                      => esc_html__('Fade Up', MELA_TD),
						'flip box fade hideback'                => esc_html__('Fade Hideback', MELA_TD),
						'flip box fade up hideback'             => esc_html__('Fade Up Hideback', MELA_TD),
						'nananana'                              => esc_html__('Nananana', MELA_TD),
						'rollover'                              => esc_html__('Rollover', MELA_TD),
						'flip3d'                                => esc_html__('3d Flip', MELA_TD),

						// New Styles
						'left-to-right'                         => esc_html__('Left to Right', MELA_TD),
						'right-to-left'                         => esc_html__('Right to Left', MELA_TD),
						'top-to-bottom'                         => esc_html__('Top to Bottom', MELA_TD),
						'bottom-to-top'                         => esc_html__('Bottom to Top', MELA_TD),
						'top-to-bottom-angle'                   => esc_html__('Diagonal (Top to Bottom)', MELA_TD),
						'bottom-to-top-angle'                   => esc_html__('Diagonal (Bottom to Top)', MELA_TD),
						'fade-in-out'                           => esc_html__('Fade In Out', MELA_TD),


					],
					'default' => 'vertical',
					'prefix_class' => 'ma-el-fb-animate-'
				]
			);


			//Free Codes

		

		$this->end_controls_section();



		/*-----------------------------------------------------------------------------------*/
		/*	Front Box
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_front_box',
			[
				'label' => esc_html__('Front Box', MELA_TD)
			]
		);

		$this->add_control(
			'front_icon_view',
			[
				'label' => esc_html__('Icon Style', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__('Default', MELA_TD),
					'stacked' => esc_html__('Stacked', MELA_TD),
					'framed' => esc_html__('Framed', MELA_TD),
				],
				'default' => 'default',

			]
		);

		$this->add_control(
			'front_icon_shape',
			[
				'label' => __('Shape', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => __('Circle', MELA_TD),
					'square' => __('Square', MELA_TD),
				],
				'default' => 'circle',
				'condition' => [
					'front_icon_view!' => 'default',
				],

			]
		);

		$this->add_control(
			'front_icon',
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


		$this->add_control(
			'front_title',
			[
				'label' => esc_html__('Title', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__('Enter text', MELA_TD),
				'default' => esc_html__('Front Title Here', MELA_TD),
			]
		);


		$this->add_control(
			'front_text',
			[
				'label' => esc_html__('Description', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__('Enter text', MELA_TD),
				'default' => esc_html__('Add some nice text here.', MELA_TD),
			]
		);

		$this->add_responsive_control(
			'front_box_front_text_align',
			[
				'label' => esc_html__('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'front_title_html_tag',
			[
				'label' => esc_html__('Title HTML Tag', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_back_box',
			[
				'label' => __('Back Box', MELA_TD)
			]
		);

		$this->add_control(
			'back_icon_view',
			[
				'label' => __('View', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', MELA_TD),
					'stacked' => __('Stacked', MELA_TD),
					'framed' => __('Framed', MELA_TD),
				],
				'default' => 'default',

			]
		);

		$this->add_control(
			'back_icon_shape',
			[
				'label' => __('Shape', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => __('Circle', MELA_TD),
					'square' => __('Square', MELA_TD),
				],
				'default' => 'circle',
				'condition' => [
					'back_icon_view!' => 'default',
				],

			]
		);


		$this->add_control(
			'back_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fab fa-wordpress',
					'library'   => 'brand',
				],
				'render_type'      => 'template'
			]
		);



		$this->add_control(
			'back_title',
			[
				'label' => __('Title', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter text', MELA_TD),
				'default' => __('Text Title', MELA_TD),
			]
		);

		$this->add_control(
			'back_text',
			[
				'label' => __('Description', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter text', MELA_TD),
				'default' => __('Add some nice text here.', MELA_TD),
			]
		);


		$this->add_responsive_control(
			'front_box_back_text_align',
			[
				'label' => esc_html__('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-back' => 'text-align: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'back_title_html_tag',
			[
				'label' => __('HTML Tag', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h3',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section-action-button',
			[
				'label' => __('Action Button', MELA_TD),
			]
		);

		$this->add_control(
			'action_text',
			[
				'label' => __('Button Text', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Buy', MELA_TD),
				'default' => __('Buy Now', MELA_TD),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link to', MELA_TD),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('http://your-link.com', MELA_TD),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();




		/*-----------------------------------------------------------------------------------*/
		/*	STYLE TAB
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section-general-style',
			[
				'label' => __('General', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);


		$this->add_control(
			'ma_el_flip_3d',
			[
				'label' => __('3d Flip Style', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'flip_3d_left'       => __('Slide Right to Left', MELA_TD),
					'flip_3d_right'      => __('Slide Left to Right', MELA_TD),
					'flip_3d_top'        => __('Slide Top to Bottom', MELA_TD),
					'flip_3d_bottom'     => __('Slide Bottom to Top', MELA_TD),
				],
				'default' => '3d_left',
				'condition' => [
					'animation_style'   => 'flip3d'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'flip_box_border',
				'label' => __('Box Border', MELA_TD),
				'selector' => '{{WRAPPER}} .ma-el-flip-box-inner > div',
			]
		);



		$this->add_control(
			'box_border_radius',
			[
				'label' => __('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-flip-box-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'box_height',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Flip Box Height', MELA_TD),
				'placeholder' => __('250', MELA_TD),
				'default' => __('250', MELA_TD),
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-inner' => 'height: {{VALUE}}px;',
					'{{WRAPPER}}.ma-el-fb-animate-flipcard .ma-el-flip-box-front' => 'transform-origin: center center calc(-{{VALUE}}px/2);-webkit-transform-origin:center center calc(-{{VALUE}}px/2);',
					'{{WRAPPER}}.ma-el-fb-animate-flipcard .ma-el-flip-box-back' => 'transform-origin: center center calc(-{{VALUE }}px/2);-webkit-transform-origin:center center calc(-{{VALUE}}px/2);'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section-front-box-style',
			[
				'label' => __('Front Box', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);



		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_box_bg_color',
				'label' => __('Background', MELA_TD),
				'types' => ['classic', 'gradient'],
				'default' => '#fff',
				'selector' => '{{WRAPPER}} .ma-el-flip-box-front',
				'condition' => [
					'ma_flipbox_layout_style' => ['one', 'three', 'four']
				]
			]
		);


		$this->add_control(
			'front_box_image',
			[
				'label' => __('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ma_flipbox_layout_style' => 'two',
				],
			]
		);


		$this->add_control(
			'front_box_background_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'front_box_title_color',
			[
				'label' => __('Title', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#393c3f',
				'selectors' => [
					'{{WRAPPER}} .front-icon-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'front_title!' => ''
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'front_box_title_typography',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .front-icon-title',
			]
		);

		$this->add_control(
			'front_box_text_color',
			[
				'label' => __('Description Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#78909c',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front p' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'front_box_text_typography',
				'label' => __('Description Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .ma-el-flip-box-front p',
			]
		);


		/**
		 *  Front Box icons styles
		 **/
		$this->add_control(
			'front_box_icon_color',
			[
				'label' => __('Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#4b00e7',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front .icon-wrapper i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'front_icon!' => ''
				],
			]
		);

		$this->add_control(
			'front_box_icon_fill_color',
			[
				'label' => __('Icon Fill Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#41dcab',
				'selectors' => [
					'{{WRAPPER}} .ma-el-fb-icon-view-stacked' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'front_icon_view' => 'stacked'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'front_box_icon_border',
				'label' => __('Box Border', MELA_TD),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .ma-el-flip-box-front .ma-el-fb-icon-view-framed, {{WRAPPER}} .ma-el-flip-box-front .ma-el-fb-icon-view-stacked',
				'label_block' => true,
				'condition' => [
					'front_icon_view!' => 'default'
				],
			]
		);

		$this->add_control(
			'front_icon_size',
			[
				'label' => __('Icon Size', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front .icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'front_icon_padding',
			[
				'label' => __('Icon Padding', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-front .icon-wrapper' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'default' => [
					'size' => 1.5,
					'unit' => 'em',
				],
				'range' => [
					'em' => [
						'min' => 0,
					],
				],
				'condition' => [
					'front_icon_view!' => 'default',
				],
			]
		);





		$this->end_controls_section();



		$this->start_controls_section(
			'section-back-box-style',
			[
				'label' => esc_html__('Back Box', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'back_box_background',
				'label' => __('Back Box Background', MELA_TD),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ma-el-flip-box-back',
			]
		);

		$this->add_control(
			'back_box_title_color',
			[
				'label' => __('Title', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .back-icon-title' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'back_box_title_typography',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .back-icon-title',
			]
		);

		$this->add_control(
			'back_box_text_color',
			[
				'label' => __('Description Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-back p' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'back_box_text_typography',
				'label' => __('Description Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .ma-el-flip-box-back p',
			]
		);


		/**
		 *  Back Box icons styles
		 **/
		$this->add_control(
			'back_box_icon_color',
			[
				'label' => __('Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-back .icon-wrapper i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'back_icon!' => ''
				],
			]
		);

		$this->add_control(
			'back_box_icon_fill_color',
			[
				'label' => __('Icon Fill Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-back .ma-el-fb-icon-view-stacked' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'front_icon_view' => 'stacked'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'back_box_icon_border',
				'label' => __('Box Border', MELA_TD),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .ma-el-flip-box-back .ma-el-fb-icon-view-framed, {{WRAPPER}} .ma-el-flip-box-back .ma-el-fb-icon-view-stacked',
				'label_block' => true,
				'condition' => [
					'back_icon_view!' => 'default'
				],
			]
		);

		$this->add_control(
			'back_icon_size',
			[
				'label' => __('Icon Size', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-back .icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'back_icon_padding',
			[
				'label' => __('Icon Padding', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-back .icon-wrapper' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'default' => [
					'size' => 1.5,
					'unit' => 'em',
				],
				'range' => [
					'em' => [
						'min' => 0,
					],
				],
				'condition' => [
					'back_icon_view!' => 'default',
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_action_button_style',
			[
				'label' => __('Action Button', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->start_controls_tabs('jltma_flipbox_action_btn_style');

		$this->start_controls_tab(
			'jltma_flipbox_action_btn_style_normal',
			[
				'label' => esc_html__('Normal', MELA_TD)
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'label' => __('Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button',
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'default' => '#4b00e7',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __('Border', MELA_TD),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label' => __('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_tab();

		$this->start_controls_tab(
			'jltma_flipbox_action_btn_style_hover',
			[
				'label' => esc_html__('Hover', MELA_TD)
			]
		);


		$this->add_control(
			'button_text_color_hover',
			[
				'label' => __('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name' => 'typography',
		// 		'label' => __( 'Typography', MELA_TD ),
		// 		'scheme' => Scheme_Typography::TYPOGRAPHY_4,
		// 		'selector' => '{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button:hover',
		// 	]
		// );

		$this->add_control(
			'background_color_hover',
			[
				'label' => __('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'default' => '#4b00e7',
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_hover',
				'label' => __('Border', MELA_TD),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button:hover',
			]
		);

		$this->add_control(
			'border_radius_hover',
			[
				'label' => __('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_padding_hover',
			[
				'label' => __('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-flip-box-wrapper .ma-el-flip-box-back .flipbox-content .ma-el-fb-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/flipbox/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/how-to-configure-flipbox-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=f-B35-xWqF0" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();


		//Upgrade to Pro
		

		
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'ma_el_flipbox',
			'class',
			[
				'ma-el-flip-box'
			]
		);


		$this->add_render_attribute('front-icon-wrapper', 'class', 'icon-wrapper');
		$this->add_render_attribute('front-icon-wrapper', 'class', 'ma-el-fb-icon-view-' . $settings['front_icon_view']);
		$this->add_render_attribute('front-icon-wrapper', 'class', 'ma-el-fb-icon-shape-' . $settings['front_icon_shape']);
		$this->add_render_attribute('front-icon-title', 'class', 'front-icon-title');
		$this->add_render_attribute('front-icon', 'class', $settings['front_icon']);


		$this->add_render_attribute('back-icon-wrapper', 'class', 'icon-wrapper');
		$this->add_render_attribute('back-icon-wrapper', 'class', 'ma-el-fb-icon-view-' . $settings['back_icon_view']);
		$this->add_render_attribute('back-icon-wrapper', 'class', 'ma-el-fb-icon-shape-' . $settings['back_icon_shape']);
		$this->add_render_attribute('back-icon-title', 'class', 'back-icon-title');
		$this->add_render_attribute('back-icon', 'class', $settings['back_icon']);

		$this->add_render_attribute('button', 'class', 'ma-el-fb-button');
		if (!empty($settings['link']['url'])) {
			$this->add_render_attribute('button', 'href', $settings['link']['url']);

			if (!empty($settings['link']['is_external'])) {
				$this->add_render_attribute('button', 'target', '_blank');
			}
		}



		$flip_box = $this->get_settings_for_display('front_box_image');

		if (isset($flip_box['id']) && $flip_box['id'] != "") {
			$flip_box_url_src = Group_Control_Image_Size::get_attachment_image_src(
				$flip_box['id'],
				'full',
				$settings
			);
		}

		if (!empty($flip_box['url'])) {
			$flip_box_url = $flip_box['url'];
		} else {
			$flip_box_url = isset($flip_box_url_src);
		}
?>

		<div class="ma-el-flip-box-wrapper <?php echo $settings['ma_flipbox_layout_style'] ?> <?php if ($settings['ma_el_flip_3d']) {
																									echo $settings['ma_el_flip_3d'];
																								}; ?>">

			<div class="ma-el-flip-box-inner">
				<div class="ma-el-flip-box-front">
					<div class="flipbox-content">

						<?php if ($settings['ma_flipbox_layout_style'] == "two") { ?>

							<?php if (isset($flip_box_url) && $flip_box_url != "") { ?>
								<img src="<?php echo esc_url($flip_box_url); ?>" alt="<?php echo get_post_meta($flip_box['id'], '_wp_attachment_image_alt', true); ?>">
							<?php } ?>

						<?php } else if (($settings['ma_flipbox_layout_style'] == "one") || ($settings['ma_flipbox_layout_style'] == "three")) { ?>


							<?php if ((!empty($settings['icon']) || !empty($settings['front_icon']['value']))) { ?>
								<div <?php echo $this->get_render_attribute_string('front-icon-wrapper'); ?>>
									<?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $settings['front_icon'], 'front-icon'); ?>
								</div>
							<?php } ?>

							<?php if (!empty($settings['front_title'])) { ?>
								<<?php echo $settings['front_title_html_tag']; ?> <?php echo $this->get_render_attribute_string('front-icon-title'); ?>>
									<?php echo $settings['front_title']; ?>
								</<?php echo $settings['front_title_html_tag']; ?>>
							<?php } ?>

							<?php //if(!empty($settings['front_text'])){
							?>
							<p>
								<?php echo $settings['front_text']; ?>
							</p>
							<?php //}
							?>

						<?php } ?>


						<?php //else if( $settings['ma_flipbox_layout_style'] == "three") {}
						?>



						<?php if ($settings['ma_flipbox_layout_style'] == "four") { ?>

							<?php if (!empty($settings['front_icon'])) { ?>
								<div <?php echo $this->get_render_attribute_string('front-icon-wrapper'); ?>>
									<?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $settings['front_icon'], 'front-icon'); ?>
								</div>
							<?php } ?>

						<?php } ?>


					</div>
				</div>

				<div class="ma-el-flip-box-back">
					<div class="flipbox-content">

						<?php if (!empty($settings['back_icon'])) { ?>
							<div <?php echo $this->get_render_attribute_string('back-icon-wrapper'); ?>>
								<?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $settings['back_icon'], 'back-icon'); ?>
							</div>
						<?php } ?>

						<?php if (!empty($settings['back_title'])) { ?>
							<<?php echo $settings['back_title_html_tag']; ?> <?php echo $this->get_render_attribute_string('back-icon-title'); ?>>
								<?php echo $settings['back_title']; ?>
							</<?php echo $settings['back_title_html_tag']; ?>>
						<?php } ?>

						<?php if (!empty($settings['back_text'])) { ?>
							<p>
								<?php echo $settings['back_text']; ?>
							</p>
						<?php } ?>

						<?php if (!empty($settings['action_text'])) { ?>
							<div class="ma-el-fb-button-wrapper">
								<a <?php echo $this->get_render_attribute_string('button'); ?>>
									<span class="elementor-button-text">
										<?php echo $settings['action_text']; ?>
									</span>
								</a>
							</div>
						<?php  }  ?>

					</div>
				</div>
			</div>
		</div>
<?php
	}

	protected function _content_template()
	{
	}
}
