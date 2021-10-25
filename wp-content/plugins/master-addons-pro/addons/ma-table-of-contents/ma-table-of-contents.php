<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/6/19
 */


// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Master Addons: Table of Contents
 */
class Table_of_Contents extends Widget_Base
{
	public function get_name()
	{
		return 'ma-table-of-contents';
	}
	public function get_title()
	{
		return __('Table of Contents', MELA_TD);
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-post-list';
	}

	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_script_depends()
	{
		return ['tocbot', 'master-addons-scripts'];
	}

	public function get_keywords()
	{
		return ['toc', 'tocplus', 'contentlist', 'markcontent', 'marker', 'table', 'content', 'index'];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/100-best-elementor-addons/';
	}

	protected function _register_controls()
	{

		$this->start_controls_section(
			'ma_el_toc_section_start',
			[
				'label' => __('MA - Table of Content', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_toc_design_type',
			[
				'label'    => __('Design Variations', MELA_TD),
				'type'     => Controls_Manager::SELECT,
				'default'  => 'offcanvas',
				'options'  => [
					'offcanvas'     => __('Offcanvas', MELA_TD),
					'fixed'         => __('Fixed', MELA_TD),
					'static'        => __('Static', MELA_TD),
					'dropdown'      => __('Dropdown', MELA_TD),
				],
			]
		);

		$this->add_control(
			'ma_el_toc_position',
			[
				'label'    => __('Position', MELA_TD),
				'type'     => Controls_Manager::SELECT,
				'default'  => 'left',
				'options'  => [
					'left'     => __('Left', MELA_TD),
					'right'    => __('Right', MELA_TD),
				],
				'condition' => [
					'ma_el_toc_design_type' => 'offcanvas',
				]
			]
		);


		$this->add_control(
			'ma_el_toc_fixed_position',
			[
				'label'    => __('Position', MELA_TD),
				'type'     => Controls_Manager::SELECT,
				'default'  => 'top-left',
				'options'  => [
					'top-left'     => __('Top-Left', MELA_TD),
					'top-right'    => __('Top-Right', MELA_TD),
					'bottom-left'  => __('Bottom-Left', MELA_TD),
					'bottom-right' => __('Bottom-Right', MELA_TD),
				],
				'condition' => [
					'ma_el_toc_design_type' => 'fixed',
				]
			]
		);

		$this->add_control(
			'ma_el_toc_heading_tags',
			[
				'label'    => __('Heading Tags', MELA_TD),
				'description'    => __('Want to ignore any specific heading? Go to that heading advanced tab and enter <b>ignore-this-tag</b> class in <a href="http://prntscr.com/lvw4iy" target="_blank">CSS Classes</a> input field.', MELA_TD),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => ['h2', 'h3', 'h4'],
				'options'  => Master_Addons_Helper::ma_el_heading_tags(),
			]
		);

		$this->add_responsive_control(
			'ma_el_toc_fixed_index_horizontal_offset',
			[
				'label'     => __('Horizontal Offset', MELA_TD),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default'   => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -4000,
						'step' => 2,
						'max'  => 4000,
					],
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ma-el-table-of-content' => 'left: {{ma_el_toc_fixed_index_horizontal_offset.SIZE}}px;',
					'(tablet){{WRAPPER}} .ma-el-table-of-content'  => 'left: {{ma_el_toc_fixed_index_horizontal_offset_tablet.SIZE}}px;',
					'(mobile){{WRAPPER}} .ma-el-table-of-content'  => 'left: {{ma_el_toc_fixed_index_horizontal_offset_mobile.SIZE}}px;',
				],
				'condition' => [
					'ma_el_toc_design_type' => ['fixed', 'offcanvas', 'dropdown']
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_toc_fixed_index_vertical_offset',
			[
				'label'   => __('Vertical Offset', MELA_TD),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -4000,
						'step' => 2,
						'max'  => 4000,
					],
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ma-el-table-of-content' => 'top: {{ma_el_toc_fixed_index_vertical_offset.SIZE}}px',
					'(tablet){{WRAPPER}} .ma-el-table-of-content'  => 'top: {{ma_el_toc_fixed_index_vertical_offset_tablet.SIZE}}px',
					'(mobile){{WRAPPER}} .ma-el-table-of-content'  => 'top: {{ma_el_toc_fixed_index_vertical_offset_mobile.SIZE}}px',
				],
				'condition' => [
					'ma_el_toc_design_type' => ['fixed', 'offcanvas', 'dropdown']
				]
			]
		);
		//
		//			$this->add_control(
		//				'ma_el_toc_horizontal_scroll_offset',
		//				[
		//					'label'     => __( 'Horizontal Scroll Offset', MELA_TD ),
		//					'type'      => Controls_Manager::SLIDER,
		//					'separator' => 'before',
		//					'default'   => [
		//						'size' => 10,
		//					],
		//					'range' => [
		//						'px' => [
		//							'min' => 0,
		//							'max' => 4000,
		//						],
		//					],
		//					'selectors' => [
		//						'#ma-el-toc-{{ID}} .ma-el-table-of-content-layout-fixed.is-position-fixed' => 'margin-left: {{SIZE}}{{UNIT}};',
		//					],
		//					'condition' => [
		//						'ma_el_toc_design_type' => ['fixed', 'offcanvas']
		//					]
		//				]
		//			);
		//
		//			$this->add_control(
		//				'ma_el_toc_vertical_scroll_offset',
		//				[
		//					'label'     => __( 'Vertical Scroll Offset', MELA_TD ),
		//					'type'      => Controls_Manager::SLIDER,
		//					'separator' => 'before',
		//					'default'   => [
		//						'size' => 10,
		//					],
		//					'range' => [
		//						'px' => [
		//							'min' => 0,
		//							'max' => 1080,
		//						],
		//					],
		//					'selectors' => [
		//						'#ma-el-toc-{{ID}} .ma-el-table-of-content-layout-fixed.is-position-fixed' => 'margin-top: {{SIZE}}{{UNIT}};',
		//					],
		//					'condition' => [
		//						'ma_el_toc_design_type' => ['fixed', 'offcanvas']
		//					]
		//				]
		//			);

		$this->add_responsive_control(
			'ma_el_toc_width',
			[
				'label'      => __('Width', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range'      => [
					'px' => [
						'min' => 240,
						'max' => 1200,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					]
				],
				'selectors' => [
					'#ma-el-toc-{{ID}} .ma-el-table-of-content' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-table-of-content'    => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();



		/*
			 * Fixed layout Button
			 */
		$this->start_controls_section(
			'ma_el_toc_offset_button_section',
			[
				'label'     => __('Button', MELA_TD),
				'condition' => [
					'ma_el_toc_design_type!' => ['fixed', 'static']
				]
			]
		);

		$this->add_control(
			'ma_el_toc_button_text',
			[
				'label'       => __('Button Text', MELA_TD),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Table Of Content', MELA_TD),
				'placeholder' => __('Table of Content', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_toc_button_icon',
			[
				'label'       		=> __('Button Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-book',
					'library'   => 'solid',
				],
				'render_type'      => 'template'
			]
		);


		$this->add_control(
			'ma_el_toc_button_close_icon',
			[
				'label'       => __('Button Close Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-times',
					'library'   => 'solid',
				],
				'render_type'      => 'template',
			]
		);


		$this->add_control(
			'ma_el_toc_button_icon_align',
			[
				'label'   => __('Icon Position', MELA_TD),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'      => [
						'title' => __('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'right'     => [
						'title' => __('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'right',
				'condition' => [
					'ma_el_toc_button_icon!' => '',
				],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-toggle-button .ma-el-toc-toggle-button-icon ' => 'float: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'ma_el_toc_button_icon_indent',
			[
				'label' => __('Icon Spacing', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'condition' => [
					'ma_el_toc_button_icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-toggle-button .ma-el-toc-button-icon-align-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-toggle-button .ma-el-toc-button-icon-align-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_toc_button_position',
			[
				'label'   => __('Button Position', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_content_positions(),
				'default' => 'top-left',
				'condition' => [
					'ma_el_toc_design_type' => ['offcanvas', 'dropdown'],
				]
			]
		);

		$this->add_responsive_control(
			'ma_el_toc_btn_horizontal_offset',
			[
				'label' => __('Horizontal Offset', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -4000,
						'step' => 2,
						'max'  => 4000,
					],
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'margin-left: {{ma_el_toc_btn_horizontal_offset.SIZE}}px;',
					'(tablet){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'margin-left: {{ma_el_toc_btn_horizontal_offset_tablet.SIZE}}px;',
					'(mobile){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'margin-left: {{ma_el_toc_btn_horizontal_offset_mobile.SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_toc_btn_vertical_offset',
			[
				'label' => __('Vertical Offset', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -1400,
						'step' => 2,
						'max'  => 1400,
					],
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'margin-top: {{ma_el_toc_btn_vertical_offset.SIZE}}px;',
					'(tablet){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'margin-top: {{ma_el_toc_btn_vertical_offset_tablet.SIZE}}px;',
					'(mobile){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'margin-top: {{ma_el_toc_btn_vertical_offset_mobile.SIZE}}px;',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_toc_button_rotate',
			[
				'label'   => __('Rotate', MELA_TD),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
						'step' => 5,
					],
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'transform: rotate({{SIZE}}deg);',
					'(tablet){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'transform: rotate({{SIZE}}deg);',
					'(mobile){{WRAPPER}} .ma-el-toggle-button-wrapper' => 'transform: rotate({{SIZE}}deg);',
				],
			]
		);

		$this->end_controls_section();


		/*
			 * TOC: Dropdown Options
			 */
		$this->start_controls_section(
			'ma_el_toc_section_dropdown_option',
			[
				'label'     => __('Dropdown Options', MELA_TD),
				'condition' => [
					'ma_el_toc_design_type' => 'dropdown',
				]
			]
		);


		$this->add_control(
			'ma_el_toc_dropdown_position',
			[
				'label'   => __('Dropdown Position', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_content_positions(),
				'default' => 'top-left'
			]
		);



		$this->add_control(
			'ma_el_toc_drop_mode',
			[
				'label'   => __('Mode', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'click'    => __('Click', MELA_TD),
					'hover'  => __('Hover', MELA_TD),
				],
			]
		);


		//			$this->add_control(
		//				'ma_el_toc_drop_animation',
		//				[
		//					'label'     => __( 'Animation', MELA_TD ),
		//					'type'      => Controls_Manager::SELECT,
		//					'default'   => 'fade',
		//					'options'   => Master_Addons_Helper::ma_el_transition_options(),
		//					'separator' => 'before',
		//				]
		//			);


		$this->add_control(
			'ma_el_toc_drop_animation',
			[
				'label' => __('Animation', MELA_TD),
				'type' => \Elementor\Controls_Manager::ANIMATION,
				//					'prefix_class' => 'animated ',
				'selectors' => [
					'{{WRAPPER}} .table-of-content-layout-dropdown .ma-el-table-of-content'
				],
			]
		);


		$this->add_control(
			'ma_el_toc_drop_duration',
			[
				'label'   => __('Animation Duration', MELA_TD),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => .3,
				],
				'range' => [
					'px' => [
						'max' => 10,
						'step' => .1,
					],
				],
				'condition' => [
					'ma_el_toc_drop_animation!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .table-of-content-layout-dropdown .ma-el-table-of-content' => 'transition: all {{ma_el_toc_drop_duration.SIZE}}s ease;',
				],
			]
		);


		$this->end_controls_section();


		/*
			 * Extra Settings
			 */

		$this->start_controls_section(
			'ma_el_toc_section_additional_table_of_content',
			[
				'label' => __('Additional', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_toc_context',
			[
				'label'       => __('Index Area (any class/id selector)', MELA_TD),
				'description'       => __('Any class or ID selector accept here for your table of content.', MELA_TD),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '.elementor',
				'placeholder' => '.elementor / #container',
			]
		);

		$this->add_control(
			'ma_el_toc_auto_collapse',
			[
				'label'     => __('Auto Collapse Sub Index', MELA_TD),
				'separator' => 'before',
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'ma_el_toc_history',
			[
				'label' => __('Index Click History', MELA_TD),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();





		/*
			 * Style Tab
			 */

		$this->start_controls_section(
			'ma_el_toc_section_style_ofc_btn',
			[
				'label'     => __('Button', MELA_TD),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_toc_design_type!' => 'fixed'
				]
			]
		);

		$this->start_controls_tabs('ma_el_toc_tabs_ofc_btn_style');

		$this->start_controls_tab(
			'ma_el_toc_tab_ofc_btn_normal',
			[
				'label' => __('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_toc_button_background',
			[
				'label'     => __('Background', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-toggle-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_toc_button_color',
			[
				'label'     => __('Text/Icon Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-toggle-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_el_toc_button_shadow',
				'selector' => '{{WRAPPER}} .ma-el-toggle-button-wrapper a.ma-el-toggle-button'
			]
		);

		$this->add_responsive_control(
			'ma_el_toc_button_padding',
			[
				'label'      => __('Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-toggle-button-wrapper a.ma-el-toggle-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ma_el_toc_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ma-el-toggle-button-wrapper a.ma-el-toggle-button'
			]
		);

		$this->add_control(
			'ma_el_toc_button_radius',
			[
				'label'      => __('Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-toggle-button-wrapper a.ma-el-toggle-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_toc_button_typography',
				'selector' => '{{WRAPPER}} .ma-el-toggle-button-wrapper a.ma-el-toggle-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_toc_tab_ofc_btn_hover',
			[
				'label' => __('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_toc_ofc_btn_hover_color',
			[
				'label'     => __('Text/Icon Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-toggle-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_toc_ofc_btn_hover_bg',
			[
				'label'     => __('Background', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-toggle-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_toc_ofc_btn_hover_border_color',
			[
				'label'     => __('Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ofc_btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-toggle-button-wrapper a.ma-el-toggle-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ma_el_toc_section_style_offcanvas',
			[
				'label' => __('Index', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_toc_index_background',
			[
				'label'     => __('Background', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => "#4b00e7",
				'selectors' => [
					//						'#ma-el-toc-{{ID}} .ma-el-offcanvas-bar' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-table-of-content'    => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_toc_title_color',
			[
				'label'     => __('Title Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => "#8c8c8c",
				'selectors' => [
					'#ma-el-toc-{{ID}} .toc-list li' => 'color: {{VALUE}};',
					'{{WRAPPER}} .toc-list li'    => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label'     => __('Active Title Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => "#54BC4B",
				'selectors' => [
					'#ma-el-toc-{{ID}} .toc-list .is-active-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .toc-list-item .is-active-link'    => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_border_color',
			[
				'label'     => __('Active Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => "#54BC4B",
				'selectors' => [
					'#ma-el-toc-{{ID}} .is-active-link::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_toc_index_typography',
				'selector' => '#ma-el-toc-{{ID}} .toc-list li, {{WRAPPER}} .toc-list li',
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/100-best-elementor-addons/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);


		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/dynamic-table-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=bn0TvaGf9l8" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->end_controls_section();



		//Upgrade to Pro
		
	}


	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if ($settings['ma_el_toc_design_type'] == 'dropdown') {
			$this->ma_el_layout_dropdown();
		} elseif ($settings['ma_el_toc_design_type'] == 'static') {
			$this->ma_el_layout_static();
		} elseif ($settings['ma_el_toc_design_type'] == 'fixed') {
			$this->ma_el_layout_fixed();
		} else {
			$this->ma_el_layout_offcanvas();
		}
	}

	private function ma_el_layout_static()
	{
		$settings    = $this->get_settings();

?>
		<div class="ma-el-table-of-content-layout-static is-position-static
            ma-el-position-<?php echo esc_attr($settings['ma_el_toc_design_type']); ?>">
			<?php $this->ma_el_toc_table_of_content(); ?>
		</div>
	<?php
	}



	private function ma_el_layout_fixed()
	{
		$settings    = $this->get_settings();

	?>
		<div class="ma-el-table-of-content-layout-fixed is-position-fixed ma-el-position-<?php echo esc_attr(
																								$settings['ma_el_toc_fixed_position']
																							); ?>">
			<div class="ma-el-card ma-el-card-secondary ma-el-card-body">
				<?php $this->ma_el_toc_table_of_content(); ?>
			</div>
		</div>
	<?php
	}


	public function ma_el_layout_offcanvas()
	{
		$settings = $this->get_settings_for_display();
		$ma_el_toc_id          = 'ma-el-toc-' . $this->get_id();
		$index_align = $settings['ma_el_toc_position'] ?: 'right';

		$this->add_render_attribute('offcanvas', 'id',  $ma_el_toc_id);
		$this->add_render_attribute('offcanvas', 'class',  [
			'ma-el-offcanvas', 'ma-el-ofc-table-of-content', 'ma-el-flex',
			'ma-el-flex-middle', 'ma-el-toc-content-' . $index_align
		]);
	?>

		<div class="table-of-content-layout-offcanvas">
			<div class="ma-el-toggle-button-wrapper ma-el-position-fixed ma-el-toc-position-<?php echo esc_attr($settings['ma_el_toc_button_position']); ?>">
				<a class="ma-el-toggle-button elementor-button elementor-size-sm" ma-el-toggle="target: #<?php echo
																											esc_attr($ma_el_toc_id); ?>" href="#">
					<?php $this->ma_el_toc_render_toggle_button_content(); ?>
				</a>
			</div>

			<div <?php echo $this->get_render_attribute_string('offcanvas'); ?>>
				<div class="ma-el-offcanvas-bar ma-el-offcanvas-push">
					<button class="ma-el-offcanvas-close ma-el-close" type="button">
						<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-times', 'icon', $settings['ma_el_toc_button_close_icon'], 'ma_el_toc_button_close_icon'); ?>
					</button>
					<?php $this->ma_el_toc_table_of_content(); ?>
				</div>
			</div>
		</div>


	<?php
	}


	public function ma_el_toc_render_toggle_button_content()
	{
		$settings    = $this->get_settings();

		if ($settings['ma_el_toc_button_icon']) {
			$ma_el_toc_button_icon = $settings['ma_el_toc_button_icon'];
		} else {
			if ($settings['ma_el_toc_button_text']) {
				$ma_el_toc_button_icon = '';
			} else {
				$ma_el_toc_button_icon = 'fa fa-bars';
			}
		}

	?>
		<span class="elementor-button-content-wrapper">

			<?php if ($settings['ma_el_toc_button_text']) : ?>
				<span class="ma-el-toc-toggle-button-text">
					<?php echo esc_html($settings['ma_el_toc_button_text']); ?>
				</span>
			<?php endif; ?>

			<?php if ($ma_el_toc_button_icon) : ?>
				<span class="ma-el-toc-toggle-button-icon elementor-button-icon ma-el-toc-button-icon-align-<?php
																											echo esc_attr($settings['ma_el_toc_button_icon_align']); ?>">
					<?php Master_Addons_Helper::jltma_fa_icon_picker('fa fa-bars', 'icon', $settings['ma_el_toc_button_icon'], 'ma_el_toc_button_icon'); ?>
				</span>
			<?php endif; ?>

		</span>
	<?php
	}


	private function ma_el_layout_dropdown()
	{
		$settings = $this->get_settings();
		$id       = 'ma-el-toc-' . $this->get_id();



		$this->add_render_attribute('ma_el_dropdown_table_of_contents', 'class', 'table-of-content-layout-dropdown');

		$this->add_render_attribute(
			[
				'drop-settings' => [
					'class'    => [
						'ma-el-drop',
						'ma-el-card',
						'ma-el-card-secondary',
						'ma-el-toc-drop-' . $settings['ma_el_toc_dropdown_position'],
						'animated ' . $settings['ma_el_toc_drop_animation']
					],
					'data-ma-el-drop' => [
						"animated_class"     => $settings['ma_el_toc_drop_animation'],
					],
				],
			]
		);
	?>


		<div <?php echo $this->get_render_attribute_string('ma_el_dropdown_table_of_contents'); ?>>
			<div class="ma-el-toggle-button-wrapper ma-el-position-fixed ma-el-toc-position-<?php echo
																							esc_attr($settings['ma_el_toc_button_position']); ?>">
				<a id="<?php echo $id; ?>" class="ma-el-toggle-button elementor-button elementor-size-sm" href="#">
					<?php $this->ma_el_toc_render_toggle_button_content(); ?>
				</a>
			</div>
			<div <?php echo $this->get_render_attribute_string('drop-settings'); ?>>
				<div class="ma-el-card-body">
					<?php $this->ma_el_toc_table_of_content(); ?>
				</div>
			</div>
		</div>
	<?php
	}




	public function ma_el_toc_table_of_content()
	{
		$settings    = $this->get_settings();
		//			 = $settings['dropdown'] ?
		$dropdown_mode = 'click' == $settings['ma_el_toc_drop_mode'] ? 'click' : 'hover';
		$this->add_render_attribute(
			[
				'ma_el_toc' => [
					'data-design' => $settings['ma_el_toc_design_type'],
					'data-dropdown-mode' => $dropdown_mode,
					'data-contentselctor' => $settings["ma_el_toc_context"],
					'data-ma_el_toc_heading_tags' => implode(",", $settings["ma_el_toc_heading_tags"]),
					'data-settings' => [
						wp_json_encode(array_filter([
							"contentSelector"           => $settings["ma_el_toc_context"],
							"headingSelector"           => implode(",", $settings["ma_el_toc_heading_tags"]),
							// Where to render the table of contents.
							'tocSelector' => '.js-toc',
							// Where to grab the headings to build the table of contents.
							'collapseDepth' => 1,
							'ignoreSelector' => ".js-toc-ignore",
							'linkClass' => 'toc-link',
							'activeLinkClass' => 'is-active-link',
							'extraListClasses' => '',
							// Class that gets added when a list should be collapsed.
							'isCollapsedClass' => 'is-collapsed',
							// Class that gets added when a list should be able
							// to be collapsed but isn't necessarily collapsed.
							'collapsibleClass' => 'is-collapsible',
							// Class to add to list items.
							'listItemClass' => 'toc-list-item',
							// How many heading levels should not be collapsed.
							// For example, number 6 will show everything since
							// there are only 6 heading levels and number 0 will collapse them all.
							// The sections that are hidden will open
							// and close as you scroll to headings within them.
							//                                'collapseDepth'=> 0,
							// Smooth scrolling enabled.
							'scrollSmooth' => true,
							// Smooth scroll duration.
							'scrollSmoothDuration' =>  420,
							// Callback for scroll end.
							//                                'scrollEndCallback'=> function (e) { },
							// Headings offset between the headings and the top of the document (this is meant for minor adjustments).
							'headingsOffset' => 1,
							// Timeout between events firing to make sure it's
							// not too rapid (for performance reasons).
							'throttleTimeout' =>  50,
							// Element to add the positionFixedClass to.
							'positionFixedSelector' =>  null,
							// Fixed position class to add to make sidebar fixed after scrolling
							// down past the fixedSidebarOffset.
							'positionFixedClass' => 'is-position-fixed',
							// fixedSidebarOffset can be any number but by default is set
							// to auto which sets the fixedSidebarOffset to the sidebar
							// element's offsetTop from the top of the document on init.
							'fixedSidebarOffset' => 'auto',
							// includeHtml can be set to true to include the HTML markup from the
							// heading node instead of just including the textContent.
							'includeHtml' =>  false,
							// onclick function to apply to all links in toc. will be called with
							// the event as the first parameter, and this can be used to stop,
							// propagation, prevent default or perform action
							'onClick' =>  false,
							// orderedList can be set to false to generate unordered lists (ul)
							// instead of ordered lists (ol)
							'orderedList' =>  false,
							// If there is a fixed article scroll container, set to calculate titles' offset
							'scrollContainer' =>  null,
							// prevent ToC DOM rendering if it's already rendered by an external system
							'skipRendering' =>  false
						]))
					]
				]
			]
		);

	?>

		<div class="js-toc ma-el-table-of-content" <?php echo $this->get_render_attribute_string(
														'ma_el_toc'
													); ?>></div>
<?php
	}
}
