<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Control_Media;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/2/20
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Image_Comparison extends Widget_Base
{

	public function get_name()
	{
		return 'ma-el-image-comparison';
	}

	public function get_title()
	{
		return esc_html__('Image Comparison', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-image-before-after';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_script_depends()
	{
		return [
			'jquery-event-move',
			'twentytwenty',
			'imagesloaded',
			'master-addons-scripts'
		];
	}

	public function get_style_depends()
	{
		return [
			'twentytwenty'
		];
	}

	public function get_keywords()
	{
		return ['compare', 'image', 'before', 'after'];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/image-comparison/';
	}

	protected function _register_controls()
	{

		$this->start_controls_section(
			'jltma_image_comparison_section_start',
			[
				'label' => esc_html__('Images', MELA_TD)
			]
		);



		$this->start_controls_tabs('jltma_image_comparison_tab_images');
		$this->start_controls_tab(
			'jltma_image_comparison_tab_before_image',
			[
				'label' => esc_html__('Before', MELA_TD),
			]
		);


		$this->add_control(
			'jltma_image_comparison_before_image',
			array(
				'label'      => esc_html__('Before image', MELA_TD),
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false
			)
		);

		$this->add_control(
			'jltma_image_comparison_before_label',
			[
				'label' => esc_html__('Label', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Before', MELA_TD),
				'placeholder' => esc_html__('Before Label', MELA_TD),
				'description' => esc_html__('Show/Hide Labels on Overlay Settings', MELA_TD),
				'dynamic' => [
					'active' => true,
				]
			]
		);
		$this->end_controls_tab();


		$this->start_controls_tab(
			'jltma_image_comparison_tab_after_image',
			[
				'label' => esc_html__('After', MELA_TD),
			]
		);

		$this->add_control(
			'jltma_image_comparison_after_image',
			array(
				'label'      => esc_html__('After Image', MELA_TD),
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false
			)
		);

		$this->add_control(
			'jltma_image_comparison_after_label',
			[
				'label' 		=> esc_html__('Label', MELA_TD),
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> esc_html__('After', MELA_TD),
				'placeholder' 	=> esc_html__('After Label', MELA_TD),
				'description' 	=> esc_html__('Show/Hide Labels on Overlay Settings', MELA_TD),
				'dynamic' 		=> [
					'active' => true,
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'before',
				'exclude'    => array('custom'),
			]
		);

		$this->end_controls_section();



		/*-----------------------------------------------------------------------------------*/
		/*  Settings Section
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'jltma_image_comparison_section',
			array(
				'label' => esc_html__('Settings', MELA_TD)
			)
		);

		$this->add_control(
			'jltma_image_comparison_visible_ratio',
			[
				'label'   		=> esc_html__('Visible Ratio (%)', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'style_transfer' => true
			]
		);

		$this->add_control(
			'jltma_image_comparison_orientation',
			[
				'label' 		=> esc_html__('Orientation', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'label_block' 	=> false,
				'options' 		=> [
					'horizontal' 	=> [
						'title' 	=> esc_html__('Horizontal', MELA_TD),
						'icon' 		=> 'fa fa-arrows-h',
					],
					'vertical' 		=> [
						'title' 		=> esc_html__('Vertical', MELA_TD),
						'icon' 			=> 'fa fa-arrows-v',
					],
				],
				'default' 		 => 'horizontal',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'jltma_image_comparison_overlay',
			[
				'label' 		    => esc_html__('Overlay', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => esc_html__('Show', MELA_TD),
				'label_off'         => esc_html__('Hide', MELA_TD),
				'return_value'      => 'yes',
				'description' 	    => esc_html__('Show/Hide overlay with before and after label', MELA_TD),
				'style_transfer'    => true,
			]
		);


		
			$this->add_control(
				'jltma_image_comparison_move_handle',
				[
					'label' 		=> esc_html__('Move Handle', MELA_TD),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'drag',
					'options' 		=> [
						'drag'          	=> esc_html__('Mouse Drag/Swipe', MELA_TD),
						'mouse_move'    	=> esc_html__('Mouse Move', MELA_TD),
						'mouse_click'   	=> esc_html__('Mouse Click', MELA_TD),
					],
					'style_transfer' => true,
				]
			);
		

		$this->end_controls_section();


		/**
		 * General Style Section
		 */
		$this->start_controls_section(
			'jltma_image_comparison_general_style',
			array(
				'label'      => esc_html__('Layout Style', MELA_TD),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'jltma_image_comparison_container_border',
				'label'       => esc_html__('Border', MELA_TD),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .jltma-image-comparison',
			)
		);


		
			$this->add_responsive_control(
				'jltma_image_comparison_container_border_radius',
				array(
					'label'      => esc_html__('Border Radius', MELA_TD),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%'),
					'selectors'  => array(
						'{{WRAPPER}} .jltma-image-comparison' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => 'jltma_image_comparison_container_box_shadow',
					'exclude' => array(
						'box_shadow_position',
					),
					'selector' => '{{WRAPPER}} .jltma-image-comparison',
				)
			);
		



		$this->add_responsive_control(
			'jltma_image_comparison_container_padding',
			array(
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_container_margin',
			array(
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();




		/**
		 * Style Tab: Overlay
		 */
		$this->start_controls_section(
			'jltma_image_comparison_section_overlay_style',
			[
				'label'             => esc_html__('Overlay', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'         => [
					'jltma_image_comparison_overlay'  => 'yes',
				],
			]
		);

		$this->start_controls_tabs('jltma_image_comparison_tabs_overlay_style');

		$this->start_controls_tab(
			'jltma_image_comparison_tab_overlay_normal',
			[
				'label'             => esc_html__('Normal', MELA_TD),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'jltma_image_comparison_overlay_background',
				'types'             => ['classic', 'gradient'],
				'selector'          => '{{WRAPPER}} .twentytwenty-overlay',
				'condition'         => [
					'jltma_image_comparison_overlay'  => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'jltma_image_comparison_tab_overlay_hover',
			[
				'label'             => esc_html__('Hover', MELA_TD),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'jltma_image_comparison_overlay_background_hover',
				'types'             => ['classic', 'gradient'],
				'selector'          => '{{WRAPPER}} .twentytwenty-overlay:hover',
				'condition'         => [
					'jltma_image_comparison_overlay'  => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();





		/**
		 * Style Tab: Handle
		 */
		$this->start_controls_section(
			'jltma_image_comparison_section_handle_style',
			[
				'label'             => esc_html__('Handle', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'jltma_image_comparison_heading_bar',
			[
				'label' => esc_html__('Handle Bar', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'jltma_image_comparison_handle_color',
			[
				'label' => esc_html__('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-handle:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .twentytwenty-handle' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .twentytwenty-handle:before' =>
					'-webkit-box-shadow: 0 3px 0 {{VALUE}}, 0px 0px 12px rgba(51, 51, 51, 0.5);'
						. '-moz-box-shadow: 0 3px 0 {{VALUE}}, 0px 0px 12px rgba(51, 51, 51, 0.5);'
						. 'box-shadow: 0 3px 0 {{VALUE}}, 0px 0px 12px rgba(51, 51, 51, 0.5);',
					'{{WRAPPER}} .twentytwenty-handle:after' =>
					'-webkit-box-shadow: 0 -3px 0 {{VALUE}}, 0px 0px 12px rgba(51, 51, 51, 0.5);'
						. '-moz-box-shadow: 0 -3px 0 {{VALUE}}, 0px 0px 12px rgba(51, 51, 51, 0.5);'
						. 'box-shadow: 0 -3px 0 {{VALUE}}, 0px 0px 12px rgba(51, 51, 51, 0.5);',
				],
			]
		);

		$this->add_responsive_control(
			'jltma_image_comparison_handle_bar_size',
			[
				'label' => esc_html__('Size', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-0px - {{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'height: {{SIZE}}{{UNIT}}; margin-top: calc(-0px - {{SIZE}}{{UNIT}} / 2);',
				],
			]
		);




		$this->add_control(
			'jltma_image_comparison_heading_bar_indicator',
			[
				'label' => esc_html__('Handle Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'jltma_image_comparison_arrow_box_width',
			[
				'label' => esc_html__('Box Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twentytwenty-handle' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-1 * ({{SIZE}}{{UNIT}} / 2));',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before' => 'margin-left: calc(({{SIZE}}{{UNIT}} / 2) - 1px);',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'margin-right: calc(({{SIZE}}{{UNIT}} / 2) - 1px);',
				],
			]
		);
		$this->add_responsive_control(
			'jltma_image_comparison_arrow_box_height',
			[
				'label' => esc_html__('Box Height', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twentytwenty-handle' => 'height: {{SIZE}}{{UNIT}}; margin-top: calc(-1 * ({{SIZE}}{{UNIT}} / 2));',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before' => 'margin-bottom: calc(({{SIZE}}{{UNIT}} / 2) + 2px);',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after' => 'margin-top: calc(({{SIZE}}{{UNIT}} / 2) + 2px);',
				],
			]
		);



		$this->start_controls_tabs('jltma_image_comparison_tabs_handle_style');

		$this->start_controls_tab(
			'jltma_image_comparison_tab_handle_normal',
			[
				'label'             => esc_html__('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'jltma_image_comparison_handle_icon_color',
			[
				'label'             => esc_html__('Icon Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '#4b00e7',
				'selectors'         => [
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle .twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'jltma_image_comparison_handle_background',
				'types'             => ['classic', 'gradient'],
				'selector'          => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'jltma_image_comparison_handle_border',
				'label'             => esc_html__('Border', MELA_TD),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle',
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'jltma_image_comparison_handle_border_radius',
			[
				'label'             => esc_html__('Border Radius', MELA_TD),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', '%'],
				'selectors'         => [
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'jltma_image_comparison_handle_box_shadow',
				'selector'              => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'jltma_image_comparison_tab_handle_hover',
			[
				'label'             => esc_html__('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'jltma_image_comparison_handle_icon_color_hover',
			[
				'label'             => esc_html__('Icon Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle:hover .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle:hover .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'jltma_image_comparison_handle_background_hover',
				'types'             => ['classic', 'gradient'],
				'selector'          => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle:hover',
			]
		);

		$this->add_control(
			'jltma_image_comparison_handle_border_color_hover',
			[
				'label'             => esc_html__('Border Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-handle:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();





		/**
		 * Label Style Section
		 */
		$this->start_controls_section(
			'jltma_image_comparison_label_style',
			array(
				'label'      => esc_html__('Label', MELA_TD),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => ['jltma_image_comparison_overlay' => 'yes'],
			)
		);

		$this->start_controls_tabs('jltma_image_comparison_tabs_label_styles');

		$this->start_controls_tab(
			'jltma_image_comparison_tab_label_before',
			array(
				'label' => esc_html__('Before', MELA_TD),
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_before_label_color',
			array(
				'label' => esc_html__('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-before-label:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'jltma_image_comparison_before_label_typography_group',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-before-label:before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'jltma_image_comparison_before_label_background_group',
				'selector' => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-before-label:before',
			)
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'jltma_image_comparison_before_label_border',
				'label'       => esc_html__('Border', MELA_TD),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .twentytwenty-before-label:before',
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_before_label_border_radius',
			array(
				'label'      => esc_html__('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .twentytwenty-before-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);


		$this->add_responsive_control(
			'jltma_image_comparison_before_label_margin',
			array(
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-before-label:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_before_label_padding',
			array(
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-before-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'jltma_image_comparison_tab_label_after',
			array(
				'label' => esc_html__('After', MELA_TD),
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_after_label_color',
			array(
				'label' => esc_html__('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'jltma_image_comparison_after_label_typography_group',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'jltma_image_comparison_after_label_background_group',
				'selector' => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before',
			)
		);



		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'jltma_image_comparison_after_label_border',
				'label'       => esc_html__('Border', MELA_TD),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before',
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_after_label_border_radius',
			array(
				'label'      => esc_html__('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);


		$this->add_responsive_control(
			'jltma_image_comparison_after_label_margin',
			array(
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'jltma_image_comparison_after_label_padding',
			array(
				'label'      => esc_html__('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .jltma-image-comparison .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/image-comparison/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/image-comparison-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=3nqRRXSGk3M" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		//Upgrade to Pro
		
	}

	protected function render()
	{
		$settings     = $this->get_settings_for_display();
		$id       = 'jltma-image-comparison-' . $this->get_id();

		$this->add_render_attribute(
			[
				'jltma_image_comparison' => [
					'id'    => esc_attr($id),
					'class' => implode(' ', [
						'twentytwenty-container',
						'jltma-image-comparison',
						'jltma-image-comparison-' . esc_attr($this->get_id()),
					]),
					'data-image-comparison-settings' => [
						wp_json_encode(array_filter([
							"container_id"          =>  esc_attr($this->get_id()),
							'visible_ratio'         => ($settings['jltma_image_comparison_visible_ratio']['size'] != '' ? $settings['jltma_image_comparison_visible_ratio']['size'] / 100 : '0.5'),
							'orientation'           => ($settings['jltma_image_comparison_orientation'] != '' ? $settings['jltma_image_comparison_orientation'] : 'horizontal'),
							'before_label'          => ($settings['jltma_image_comparison_before_label'] != '' ? esc_attr($settings['jltma_image_comparison_before_label']) : ''),
							'after_label'           => ($settings['jltma_image_comparison_after_label'] != '' ? esc_attr($settings['jltma_image_comparison_after_label']) : ''),
							'slider_on_hover'       => ($settings['jltma_image_comparison_move_handle'] == 'mouse_move' ? true : false),
							'slider_with_handle'    => ($settings['jltma_image_comparison_move_handle'] == 'drag' ? true : false),
							'slider_with_click'     => ($settings['jltma_image_comparison_move_handle'] == 'mouse_click' ? true : false),
							'no_overlay'            => ($settings['jltma_image_comparison_overlay'] == 'yes' ? false : true)
						]))
					]
				]
			]
		); ?>

		<div <?php echo $this->get_render_attribute_string('jltma_image_comparison'); ?>>
			<?php if ($settings['jltma_image_comparison_before_image']['url'] || $settings['jltma_image_comparison_before_image']['id']) :
				$this->add_render_attribute('jltma_before_image', 'src', $settings['jltma_image_comparison_before_image']['url']);
				$this->add_render_attribute('jltma_before_image', 'alt', Control_Media::get_image_alt($settings['jltma_image_comparison_before_image']));
				$this->add_render_attribute('jltma_before_image', 'title', Control_Media::get_image_title($settings['jltma_image_comparison_before_image']));
				echo Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'jltma_image_comparison_before_image');
			endif;

			if ($settings['jltma_image_comparison_after_image']['url'] || $settings['jltma_image_comparison_after_image']['id']) :
				$this->add_render_attribute('jltma_after_image', 'src', $settings['jltma_image_comparison_after_image']['url']);
				$this->add_render_attribute('jltma_after_image', 'alt', Control_Media::get_image_alt($settings['jltma_image_comparison_after_image']));
				$this->add_render_attribute('jltma_after_image', 'title', Control_Media::get_image_title($settings['jltma_image_comparison_after_image']));
				echo Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'jltma_image_comparison_after_image');
			endif; ?>
		</div>

	<?php

	}

	protected function _content_template()
	{
	?>
		<# var visible_ratio=( settings.jltma_image_comparison_visible_ratio.size !='' ) ? settings.jltma_image_comparison_visible_ratio.size / 100 : '0.5' ; var slider_on_hover=( settings.jltma_image_comparison_move_handle=='mouse_move' ) ? true : false; var slider_with_handle=( settings.jltma_image_comparison_move_handle=='drag' ) ? true : false; var slider_with_click=( settings.jltma_image_comparison_move_handle=='mouse_click' ) ? true : false; var no_overlay=( settings.jltma_image_comparison_overlay=='yes' ) ? false : true; #>
			<div class="jltma-image-comparison twentytwenty-container" data-image-comparison-settings='{ "visible_ratio":{{ visible_ratio }},"orientation":"{{ settings.jltma_image_comparison_orientation }}","before_label":"{{ settings.jltma_image_comparison_before_label }}","after_label":"{{ settings.jltma_image_comparison_after_label }}","slider_on_hover":{{ slider_on_hover }},"slider_with_handle":{{ slider_with_handle }},"slider_with_click":{{ slider_with_click }},"no_overlay":{{ no_overlay }} }'>
				<# if ( settings.jltma_image_comparison_before_image.url !='' ) { #>
					<img src="{{ settings.jltma_image_comparison_before_image.url }}">
					<# } #>

						<# if ( settings.jltma_image_comparison_after_image.url !='' ) { #>
							<img src="{{ settings.jltma_image_comparison_after_image.url }}">
							<# } #>
			</div>
	<?php
	}
}
