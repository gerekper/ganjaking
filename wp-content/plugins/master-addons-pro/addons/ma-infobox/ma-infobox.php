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
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Infobox extends Widget_Base
{

	public function get_name()
	{
		return 'ma-el-infobox';
	}
	public function get_title()
	{
		return esc_html__('Info Box', MELA_TD);
	}
	public function get_icon()
	{
		return 'ma-el-icon eicon-info-box';
	}
	public function get_categories()
	{
		return ['master-addons'];
	}
	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/infobox/';
	}

	protected function _register_controls()
	{

		/*
			* Master Addons: Infobox Image
			*/
		$this->start_controls_section(
			'ma_el_section_infobox_content',
			[
				'label' => esc_html__('Content', MELA_TD)
			]
		);


		// Premium Version Codes
		


			$this->add_control(
				'ma_el_infobox_preset',
				[
					'label' => esc_html__('Style Preset', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'default' => 'one',
					'options' => [
						'one'           => esc_html__('Variation One', MELA_TD),
						'two'           => esc_html__('Variation Two', MELA_TD),
						'three'         => esc_html__('Variation Three', MELA_TD),
						'four'          => esc_html__('Variation Four', MELA_TD),
						'five'          => esc_html__('Variation Five', MELA_TD),
						'six'           => esc_html__('Variation Six', MELA_TD),
						'seven'         => esc_html__('Variation Seven', MELA_TD),
						'eight'         => esc_html__('Variation Eight', MELA_TD),
						'nine'          => esc_html__('Variation Nine', MELA_TD),
						'ten'           => esc_html__('Variation Ten', MELA_TD),
					],
				]
			);


			$this->add_control(
				'ma_el_infobox_gradient_bg_heading',
				[
					'label' => __('Icon Color', MELA_TD),
					'type' => Controls_Manager::HEADING,
					'separator' => 'after',
					'condition' => [
						'ma_el_infobox_preset' => 'six'
					],

				]
			);



			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_el_infobox_gradient_color_scheme',
					'label' => __('Background Color', MELA_TD),
					'types' => ['gradient'],
					'selector' => '{{WRAPPER}} .ma-el-infobox.six .ma-el-infobox-item .ma-el-infobox-icon',
					'condition' => [
						'ma_el_infobox_preset' => 'six'
					],
				]
			);

			//Free Version Codes
		


		$this->add_control(
			'ma_el_infobox_img_or_icon',
			[
				'label' => esc_html__('Image or Icon', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'none' => [
						'title' => esc_html__('None', MELA_TD),
						'icon' => 'fa fa-ban',
					],
					'icon' => [
						'title' => esc_html__('Icon', MELA_TD),
						'icon' => 'fa fa-info-circle',
					],
					'img' => [
						'title' => esc_html__('Image', MELA_TD),
						'icon' => 'fa fa-picture-o',
					]
				],
				'default' => 'icon',
			]
		);


		$this->add_control(
			'ma_el_infobox_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-tag',
					'library'   => 'solid',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'icon'
				]
			]
		);

		$this->add_control(
			'ma_el_infobox_image',
			[
				'label' => esc_html__('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'img'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'condition' => [
					'ma_el_infobox_img_or_icon' => 'img'
				]
			]
		);


		$this->add_control(
			'ma_el_infobox_title',
			[
				'label' => esc_html__('Title', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Infobox Title', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_infobox_title_link',
			[
				'label' => __('Title URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_description',
			[
				'label' 		=> esc_html__('Description', MELA_TD),
				'type' 			=> Controls_Manager::TEXTAREA,
				'default' 		=> esc_html__('Basic description about the Infobox', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_infobox_readmore_text',
			[
				'label' 		=> esc_html__('Read More Text', MELA_TD),
				'type' 			=> Controls_Manager::TEXT,
				'label_block' 	=> true,
				'default' 		=> esc_html__('Learn More', MELA_TD),
				'condition' 	=> [
					'ma_el_infobox_preset' => 'six'
				]
			]
		);

		$this->add_control(
			'ma_el_infobox_readmore_link',
			[
				'label' 		=> __('Read More Link', MELA_TD),
				'type' 			=> Controls_Manager::URL,
				'placeholder' 	=> __('https://master-addons.com/demo', MELA_TD),
				'label_block' 	=> true,
				'default' 		=> [
					'url' 			=> '#',
					'is_external' 	=> true,
				],
				'condition' 	=> [
					'ma_el_infobox_preset' => 'six'
				]
			]
		);

		$this->end_controls_section();



		/*
			* Infobox Styling Section
			*/
		$this->start_controls_section(
			'ma_el_section_infobox_styles_preset',
			[
				'label' => esc_html__('General Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'info_box_icon_border',
				'label' => __('Box Border', MELA_TD),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item',
				'label_block' => true,
			]
		);

		$this->add_control(
			'info_box_border_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'info_box_padding',
			[
				'label'                 => esc_html__('Padding', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'info_box_margin',
			[
				'label'                 => esc_html__('Margin', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_bg_heading',
			[
				'label' => __('Background', MELA_TD),
				'type' => Controls_Manager::HEADING,
			]
		);


		$this->start_controls_tabs('ma_el_infobox_style');

		$this->start_controls_tab(
			'ma_el_infobox_normal',
			[
				'label'                 => __('Normal', MELA_TD),
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_el_infobox_gradient_bg',
				'label' => __('Background Color', MELA_TD),
				'types' => ['gradient'],
				'selector' => '{{WRAPPER}} .ma-el-infobox.six .ma-el-infobox-item',
				'condition' => [
					'ma_el_infobox_preset' => 'six'
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_bg',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-infobox.four .ma-el-infobox-item,
						{{WRAPPER}} .ma-el-infobox.five .ma-el-infobox-item,
						{{WRAPPER}} .ma-el-infobox.seven .ma-el-infobox-item,
						{{WRAPPER}} .ma-el-infobox.eight .ma-el-infobox-item,
						{{WRAPPER}} .ma-el-infobox.nine .ma-el-infobox-item .ma-el-infobox-content,
						{{WRAPPER}} .ma-el-infobox.nine .ma-el-infobox-item .ma-el-infobox-icon'
					=> 'background-color: {{VALUE}}'
				],
				'condition' => [
					'ma_el_infobox_preset!' => 'six'
				],
				'default' => '#fff',
			]
		);


		$this->end_controls_tab();


		$this->start_controls_tab(
			'ma_el_infobox_hover',
			[
				'label'                 => __('Hover', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_infobox_hover_bg',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-infobox.four .ma-el-infobox-item:hover,
					        {{WRAPPER}} .ma-el-infobox.five .ma-el-infobox-item:hover,
					        {{WRAPPER}} .ma-el-infobox.seven .ma-el-infobox-item:hover,
					        {{WRAPPER}} .ma-el-infobox.eight .ma-el-infobox-item:hover,
					        {{WRAPPER}} .ma-el-infobox.nine .ma-el-infobox-item .ma-el-infobox-content,
					        {{WRAPPER}} .ma-el-infobox.ten .ma-el-infobox-item:hover .ma-el-infobox-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-infobox.ten .ma-el-infobox-item:hover .ma-el-infobox-icon' => 'border-top-color:{{VALUE}};border-bottom-color:{{VALUE}};',
				],
				'default'               => '#4b00e7',
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_control(
			'ma_el_infobox_hover_animation',
			[
				'label' => __('Hover Animation', MELA_TD),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'separator' => 'before',
				'prefix_class' => 'elementor-animation-',

			]
		);


		$this->end_controls_section();






		// Icon Style
		$this->start_controls_section(
			'section_infobox_icon',
			[
				'label' => __('Icon Style', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('ma_el_infobox_icon_color_style');

		$this->start_controls_tab(
			'ma_el_infobox_icon_color_normal',
			[
				'label'                 => __('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_color_scheme',
			[
				'label' => __('Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-infobox.three .ma-el-infobox-item .ma-el-infobox-icon,
						{{WRAPPER}} .ma-el-infobox.five .ma-el-infobox-item .ma-el-infobox-icon' => 'color: #fff;',
				],
				'condition' => [
					'ma_el_infobox_preset!' => 'six'
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_bg',
			[
				'label'                 => __('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-icon' => 'background-color:{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_icon_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-icon' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_icon_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_infobox_icon_hover',
			[
				'label'                 => __('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_hover_color_scheme',
			[
				'label' => __('Icon Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item:hover .ma-el-infobox-icon' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ma_el_infobox_preset!' => 'six'
				],
			]
		);

		$this->add_control(
			'ma_el_infobox_icon_hover_bg',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item:hover .ma-el-infobox-icon' => 'background-color:{{VALUE}};',
				]
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_icon_hover_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item:hover .ma-el-infobox-icon' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_icon_hover_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-item:hover .ma-el-infobox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();




		$this->end_controls_section();






		// Title , Description Font Color and Typography
		$this->start_controls_section(
			'section_infobox_title',
			[
				'label' => __('Title', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_title_typography',
				'selector' => '{{WRAPPER}} .ma-el-infobox-content-title',
			]
		);


		$this->start_controls_tabs('ma_el_infobox_title_color_style');

		$this->start_controls_tab(
			'ma_el_infobox_title_color_normal',
			[
				'label'                 => __('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_title_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#132c47',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox-content-title' => 'color: {{VALUE}};',

				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_title_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox-content-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_title_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-infobox-content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_infobox_title_hover',
			[
				'label'                 => __('Hover', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_infobox_title_color_hover',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox-item:hover .ma-el-infobox-content-title' => 'color: {{VALUE}};',

				],
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_title_hover_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox-item:hover .ma-el-infobox-content-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_title_hover_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-infobox-item:hover .ma-el-infobox-content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		// Description Style
		$this->start_controls_section(
			'section_infobox_description',
			[
				'label' => __('Description', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_description_typography',
				'selector' => '{{WRAPPER}} .ma-el-infobox-content-description',
			]
		);

		$this->start_controls_tabs('ma_el_infobox_desc_color_style');

		$this->start_controls_tab(
			'ma_el_infobox_desc_color_normal',
			[
				'label'                 => __('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_description_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#797c80',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-content-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_desc_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-content-description' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_desc_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-infobox .ma-el-infobox-content-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_infobox_desc_hover',
			[
				'label'                 => __('Hover', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_infobox_desc_color_hover',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox-item:hover .ma-el-infobox-content-description' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-infobox.four .ma-el-infobox-item:hover .ma-el-infobox-content-description,
							{{WRAPPER}} .ma-el-infobox.five .ma-el-infobox-item:hover .ma-el-infobox-content-description,
							{{WRAPPER}} .ma-el-infobox.seven .ma-el-infobox-item:hover .ma-el-infobox-content-description,
							{{WRAPPER}} .ma-el-infobox.eight .ma-el-infobox-item:hover .ma-el-infobox-content-description,
							{{WRAPPER}} .ma-el-infobox.nine .ma-el-infobox-item:hover .ma-el-infobox-content-description' =>
					'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_infobox_desc_hover_alignment',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-infobox-item:hover .ma-el-infobox-content-description' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_infobox_desc_hover_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-infobox-item:hover .ma-el-infobox-content-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/infobox/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/infobox-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=2-ymXAZfrF0" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();


		
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();


		// Read more Link
		// if( $settings['ma_el_infobox_readmore_link']['is_external'] ) {
		// 	$this->add_render_attribute( 'ma_el_infobox_readmore', 'target', '_blank' );
		// }

		// if( $settings['ma_el_infobox_readmore_link']['nofollow'] ) {
		// 	$this->add_render_attribute( 'ma_el_infobox_readmore', 'rel', 'nofollow' );
		// }

		if (!empty($settings['ma_el_infobox_readmore_link']['url'])) {
			$this->add_render_attribute('ma_el_infobox_readmore_link', 'href', $settings['ma_el_infobox_readmore_link']['url']);

			if (!empty($settings['ma_el_infobox_readmore_link']['is_external'])) {
				$this->add_render_attribute('ma_el_infobox_readmore_link', 'target', '_blank');
			}

			if ($settings['ma_el_infobox_readmore_link']['nofollow']) {
				$this->add_render_attribute('ma_el_infobox_readmore_link', 'rel', 'nofollow');
			}
		}


		// Infobox Link
		if ($settings['ma_el_infobox_title_link']['is_external']) {
			$this->add_render_attribute('ma_el_infobox_title_link_attr', 'target', '_blank');
		}

		if ($settings['ma_el_infobox_title_link']['nofollow']) {
			$this->add_render_attribute('ma_el_infobox_title_link_attr', 'rel', 'nofollow');
		}


		$infobox_image = $this->get_settings_for_display('ma_el_infobox_image');
		if (!empty($infobox_image['url'])) {
			$infobox_image_url = Group_Control_Image_Size::get_attachment_image_src($infobox_image['id'], 'thumbnail', $settings);

			if (empty($infobox_image_url)) {
				$infobox_image_url = $infobox_image['url'];
			} else {
				$infobox_image_url = $infobox_image_url;
			}
		}


?>

		<div id="ma-el-infobox-<?php echo esc_attr($this->get_id()); ?>" class="ma-el-infobox <?php echo esc_attr($settings['ma_el_infobox_preset']); ?>">
			<div class="ma-el-infobox-item">

				<?php if ($settings['ma_el_infobox_img_or_icon'] != 'none') : ?>

					<?php if ($settings['ma_el_infobox_preset'] === "two") { ?>
						<div class="bg-fade-icon">
							<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-tag', 'icon', $settings['ma_el_infobox_icon'], 'ma_el_infobox_icon'); ?>
						</div>
					<?php } ?>

					<?php if ($settings['ma_el_infobox_preset'] == "three") { ?>
						<div class="bg-fade-icon">
							<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-tag', 'icon', $settings['ma_el_infobox_icon'], 'ma_el_infobox_icon'); ?>
						</div>

					<?php } ?>



					<div class="ma-el-infobox-icon">
						<?php if ($settings['ma_el_infobox_preset'] == "five") { ?>
							<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-tag', 'icon', $settings['ma_el_infobox_icon'], 'ma_el_infobox_icon'); ?>
						<?php } ?>

						<div class="inner-content">

							<?php if ('icon' == $settings['ma_el_infobox_img_or_icon']) { ?>
								<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-tag', 'icon', $settings['ma_el_infobox_icon'], 'ma_el_infobox_icon'); ?>
							<?php } ?>

							<?php if ('img' == $settings['ma_el_infobox_img_or_icon']) : ?>
								<img src="<?php echo esc_url($infobox_image_url); ?>" alt="<?php echo get_post_meta($infobox_image['id'], '_wp_attachment_image_alt', true); ?>">
							<?php endif; ?>

							<?php if ($settings['ma_el_infobox_preset'] == "seven") { ?>

								<?php if ($settings['ma_el_infobox_title_link']['url']) { ?>
									<a href="<?php echo esc_url_raw($settings['ma_el_infobox_title_link']['url']); ?>" <?php echo $this->get_render_attribute_string('ma_el_infobox_title_link_attr'); ?>>
										<h3 class="ma-el-infobox-content-title"><?php echo $settings['ma_el_infobox_title']; ?></h3>
									</a>
								<?php } else { ?>
									<h3 class="ma-el-infobox-content-title"><?php echo $settings['ma_el_infobox_title']; ?></h3>
								<?php } ?>

							<?php } ?>

							<?php if ($settings['ma_el_infobox_preset'] == "nine") { ?>

								<?php if ($settings['ma_el_infobox_title_link']['url']) { ?>
									<a href="<?php echo esc_url_raw($settings['ma_el_infobox_title_link']['url']); ?>" <?php echo $this->get_render_attribute_string('ma_el_infobox_title_link_attr'); ?>>
										<h3 class="ma-el-infobox-content-title"><?php echo $settings['ma_el_infobox_title']; ?></h3>
									</a>
								<?php } else { ?>
									<h3 class="ma-el-infobox-content-title"><?php echo $settings['ma_el_infobox_title']; ?></h3>
								<?php } ?>

							<?php } ?>

							<?php if ($settings['ma_el_infobox_preset'] == "five") { ?>
								<div class="hexagon-shape">
									<div class="shape-inner"></div>
								</div>
							<?php } ?>
						</div><!-- /.inner-content -->
					</div>
				<?php endif; ?>

				<div class="ma-el-infobox-content">
					<div class="inner-content">
						<?php if ($settings['ma_el_infobox_preset'] !== "seven") { ?>

							<?php if ($settings['ma_el_infobox_title_link']['url']) { ?>
								<a href="<?php echo esc_url_raw($settings['ma_el_infobox_title_link']['url']); ?>" <?php echo $this->get_render_attribute_string('ma_el_infobox_title_link_attr'); ?>>
									<h3 class="ma-el-infobox-content-title"><?php echo $settings['ma_el_infobox_title']; ?></h3>
								</a>
							<?php } else { ?>
								<h3 class="ma-el-infobox-content-title"><?php echo $settings['ma_el_infobox_title']; ?></h3>
							<?php } ?>

						<?php } ?>
						<p class="ma-el-infobox-content-description">
							<?php echo $settings['ma_el_infobox_description']; ?>
						</p>

						<?php if ($settings['ma_el_infobox_preset'] == "six") { ?>
							<a <?php echo $this->get_render_attribute_string('ma_el_infobox_readmore_link'); ?> class="ma-el-btn-learn" <?php echo $this->get_render_attribute_string('ma_el_infobox_readmore'); ?>>
								<?php echo esc_html($settings['ma_el_infobox_readmore_text']); ?>
								<i class="ti-arrow-right"></i>
							</a>
						<?php } ?>
					</div><!-- /.inner-content -->
				</div>
			</div>
		</div>

	<?php
	}

	protected function _content_template()
	{ ?>

		<div id="ma-el-infobox" class="ma-el-infobox {{ settings.ma_el_infobox_preset }}">
			<div class="ma-el-infobox-item">

				<# if( settings.ma_el_infobox_img_or_icon !='none' ) { #>
					<div class="ma-el-infobox-icon">

						<# if( 'icon'==settings.ma_el_infobox_img_or_icon ) { #>
							<i class="{{{ settings.ma_el_infobox_icon.value }}}"></i>
							<# } #>

								<# if( 'img'==settings.ma_el_infobox_img_or_icon ) { #>
									<img src="{{{ settings.ma_el_infobox_image.url }}}" alt="Icon Image">
									<# } #>

										<# if( 'ten'==settings.ma_el_infobox_preset ) { #>
											<h3 class="ma-el-infobox-content-title">{{{ settings.ma_el_infobox_title }}}</h3>
											<# } #>


					</div>
					<# } #>

						<div class="ma-el-infobox-content">
							<# if( 'ten' !==settings.ma_el_infobox_preset ) { #>
								<h3 class="ma-el-infobox-content-title">{{{ settings.ma_el_infobox_title }}}</h3>
								<# } #>
									<p class="ma-el-infobox-content-description">{{{ settings.ma_el_infobox_description }}}
									</p>
						</div>
			</div>
		</div>
<?php
	}
}
