<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Tabs extends Widget_Base
{

	public function get_name()
	{
		return 'ma-tabs';
	}

	public function get_title()
	{
		return esc_html__('Advanced Tabs', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-tabs';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_style_depends()
	{
		return [
			'jltma-bootstrap',
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_keywords()
	{
		return [
			'tab',
			'hover tabs',
			'click tabs',
			'horizontal tab',
			'vertical tabs',
			'columns',
			'tabbed',
			'panel',
			'tabular content',
			'left right',
			'left right content',
			'push content'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/tabs/';
	}

	protected function _register_controls()
	{

		/**
		 * -------------------------------------------
		 * Tab Style MA Tabs Generel Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'ma_el_section_tabs_style_preset_settings',
			[
				'label' => esc_html__('Presets', MELA_TD)
			]
		);

		
			$this->add_control(
				'ma_el_tabs_preset',
				[
					'label'       	=> esc_html__('Style Preset', MELA_TD),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'two',
					'label_block' 	=> false,
					'options' 		=> [
						'two'       => esc_html__('Horizontal Tabs', MELA_TD),
						'three'     => esc_html__('Vertical Tabs', MELA_TD),
						'four'      => esc_html__('Left Active Border', MELA_TD),
						'five'      => esc_html__('Tabular Content', MELA_TD),
					]
				]
			);
		


		$this->add_control(
			'ma_el_tabs_icon_show',
			[
				'label' => esc_html__('Enable Icon', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
			]
		);



			$this->add_responsive_control(
				'ma_el_tabs_left_cols',
				[
					'label'                 => esc_html__('Column Layout', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'label_block'           => false,
					'default'               => '7-5',
					'tablet_default'        => '7-5',
					'mobile_default'        => '12-12',
					'options'               => [
						'6-6'              => esc_html__('Col 6/6', MELA_TD),
						'7-5'              => esc_html__('Col 7/5', MELA_TD),
						'5-7'              => esc_html__('Col 5/7', MELA_TD),
						'8-4'              => esc_html__('Col 8/4', MELA_TD),
						'4-8'              => esc_html__('Col 4/8', MELA_TD),
						'9-3'              => esc_html__('Col 9/3', MELA_TD),
						'3-9'              => esc_html__('Col 3/9', MELA_TD),
						'10-2'             => esc_html__('Col 10/2', MELA_TD),
						'2-10'             => esc_html__('Col 2/10', MELA_TD),
						'11-1'             => esc_html__('Col 12/1', MELA_TD),
						'1-11'             => esc_html__('Col 1/12', MELA_TD),
						'12-12'            => esc_html__('Col 12(Full Width)', MELA_TD),
					],
					'condition' => [
						'ma_el_tabs_preset' => 'five',
					],
				]
			);
		


		
			$this->add_responsive_control(
				'ma_el_tabs_icons_style',
				[
					'label'                 => esc_html__('Icon & Tabs Style', MELA_TD),
					'type'                  => Controls_Manager::CHOOSE,
					'label_block'           => false,
					'default'               => 'block',
					'options' 		=> [
						'inline-block' 	=> [
							'title' 	=> esc_html__('Inline', MELA_TD),
							'icon' 		=> 'eicon-editor-list-ul',
						],
						'block' 		=> [
							'title' 		=> esc_html__('Boxed', MELA_TD),
							'icon' 			=> 'eicon-info-box',
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .ma-el-advance-tab.five .ma-el-advance-tab-nav li i' => 'display:{{VALUE}};',
					],
					'condition' => [
						'ma_el_tabs_icon_show' 		=> 'yes',
						'ma_el_tabs_preset' 		=> 'five'
					],
				]
			);
	



		

			$this->add_responsive_control(
				'ma_el_tabs_content_style',
				[
					'label'                 => esc_html__('Tabs & Content Style', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'label_block'           => false,
					'default'               => 'float-left',
					'options'               => [
						'float-left'              	=> esc_html__('Left Tabs - Right Content', MELA_TD),
						'float-right'               => esc_html__('Right Tabs - Left Content', MELA_TD)
					],
					'condition' => [
						'ma_el_tabs_preset' 		=> 'five',
						'ma_el_tabs_left_cols!' 	=> '12-12',
					],
				]
			);
		


		

			$this->add_control(
				'ma_el_tabular_tabs_style',
				[
					'label' 		=> esc_html__('Tabs Orientation', MELA_TD),
					'type' 			=> Controls_Manager::CHOOSE,
					'label_block' 	=> false,
					'options' 		=> [
						'inline-block' 	=> [
							'title' 	=> esc_html__('Grid', MELA_TD),
							'icon' 		=> 'eicon-gallery-grid',
						],
						'block' 		=> [
							'title' 		=> esc_html__('Vertical', MELA_TD),
							'icon' 			=> 'eicon-post-list',
						],
					],
					'default' 		 => 'inline-block',
					'condition' => [
						'ma_el_tabs_preset' => 'five',
					],
					'selectors'  => [
						'{{WRAPPER}} .ma-el-advance-tab.five .ma-el-advance-tab-nav li' => 'display:{{VALUE}};',
					],
					'style_transfer' => true,
				]
			);
		

		$this->end_controls_section();


		/**
		 * MA Tabs Content Settings
		 */
		$this->start_controls_section(
			'ma_el_section_tabs_content_settings',
			[
				'label' => esc_html__('Content', MELA_TD),
				'seperator' => 'before',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'ma_el_tab_show_as_default',
			[
				// 'name' => 'ma_el_tab_show_as_default',
				'label' => __('Set as Default', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'active',
			]
		);


		$repeater->add_control(
			'ma_el_tabs_icon_type',
			[
				'label'       => esc_html__('Icon Type', MELA_TD),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'none' => [
						'title' => esc_html__('None', MELA_TD),
						'icon'  => 'fa fa-ban',
					],
					'icon' => [
						'title' => esc_html__('Icon', MELA_TD),
						'icon'  => 'fa fa-gear',
					],
					'image' => [
						'title' => esc_html__('Image', MELA_TD),
						'icon'  => 'fa fa-picture-o',
					],
				],
				'default'       => 'icon',
			]
		);

		$repeater->add_control(
			'ma_el_tab_title_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-home',
					'library'   => 'solid',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_tabs_icon_type' => 'icon'
				]
			]
		);

		$repeater->add_control(
			'ma_el_tab_title_image',
			[
				'label' => esc_html__('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ma_el_tabs_icon_type' => 'image'
				]
			]
		);

		$repeater->add_control(
			'ma_el_tab_title',
			[
				'label' => esc_html__('Tab Title', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Tab Title', MELA_TD),
				'dynamic' => ['active' => true]
			]
		);



		// Premium Version Codes
		

			$repeater->add_control(
				'ma_tabs_content_type',
				[
					'label'                 => esc_html__('Content Type', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'label_block'           => false,
					'options'               => [
						'content'   => __('Content', MELA_TD),
						'section'   => __('Saved Section', MELA_TD),
						'widget'    => __('Saved Widget', MELA_TD),
						'template'  => __('Saved Page Template', MELA_TD),
					],
					'default'               => 'content',
				]
			);

			$repeater->add_control(
				'ma_el_tab_content',
				[
					'label'                 => esc_html__('Tab Content', MELA_TD),
					'type'                  => Controls_Manager::WYSIWYG,
					'default'               => esc_html__('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio, neque qui velit. Magni dolorum quidem ipsam eligendi, totam, facilis laudantium cum accusamus ullam voluptatibus commodi numquam, error, est. Ea, consequatur.', MELA_TD),
					'condition'             => [
						'ma_tabs_content_type'	=> 'content',
					],
				]
			);


			$repeater->add_control(
				'saved_widget',
				[
					'label'                 => __('Choose Widget', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'options'               => $this->get_page_template_options('widget'),
					'default'               => '-1',
					'condition'             => [
						'ma_tabs_content_type'    => 'widget',
					],
					'conditions'        => [
						'terms' => [
							[
								'name'      => 'ma_tabs_content_type',
								'operator'  => '==',
								'value'     => 'widget',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'saved_section',
				[
					'label'                 => __('Choose Section', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'options'               => $this->get_page_template_options('section'),
					'default'               => '-1',
					'conditions'        => [
						'terms' => [
							[
								'name'      => 'ma_tabs_content_type',
								'operator'  => '==',
								'value'     => 'section',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'templates',
				[
					'label'                 => __('Choose Template', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'options'               => $this->get_page_template_options('page'),
					'default'               => '-1',
					'conditions'        => [
						'terms' => [
							[
								'name'      => 'ma_tabs_content_type',
								'operator'  => '==',
								'value'     => 'template',
							],
						],
					],
				]
			);
		


		$this->add_control(
			'ma_el_tabs',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					['ma_el_tab_title' => esc_html__('Tab Title One', MELA_TD)],
					['ma_el_tab_title' => esc_html__('Tab Title Two', MELA_TD)],
					['ma_el_tab_title' => esc_html__('Tab Title Three', MELA_TD)],
				],
				'fields' 				=> $repeater->get_controls(),
				'title_field'           => '{{ma_el_tab_title}}',
			]
		);


		$this->add_control(
			'ma_el_tabs_effect',
			[
				'label'       => esc_html__('Tab Effect', MELA_TD),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'options' 		=> [
					'hover'       	=> esc_html__('Hover', MELA_TD),
					'click'     	=> esc_html__('Click', MELA_TD)
				],
				'default'       => 'hover',
			]
		);

		$this->end_controls_section();



		/**
		 * -------------------------------------------
		 * Tab Style MA Tabs Heading Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'ma_el_section_tabs_heading_style_settings',
			[
				'label' => esc_html__('Tabs', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_tab_heading_typography',
				'selector' => '{{WRAPPER}} .ma-el-advance-tab .ma-el-tab-title',
			]
		);


		$this->start_controls_tabs('ma_el_tabs_header_tabs');
		// Normal State Tab
		$this->start_controls_tab('ma_el_tabs_header_normal', [
			'label' => esc_html__(
				'Normal',
				MELA_TD
			)
		]);

		$this->add_control(
			'ma_el_tab_text_color',
			[
				'label' => esc_html__('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#8a8d91',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li span, {{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li i' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'ma_el_tab_bg_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li' => 'background: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'ma_el_tab_border_color',
			[
				'label' => esc_html__('Bottom Border Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#e5e5e5',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab.two .ma-el-advance-tab-nav li' => 'border-bottom: 1px solid {{VALUE}};'
				],
				'condition' => [
					'ma_el_tabs_preset' => 'two'
				]
			]
		);


		$this->add_control(
			'ma_el_tabs_heading_padding',
			[
				'label' => esc_html__('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'ma_el_tabs_heading_margin',
			[
				'label' => esc_html__('Margin', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'          => 'ma_el_tabs_heading_box_shadow',
				'selector'      => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li',
			]
		);

		$this->add_control(
			'ma_el_tabs_icon_size',
			[
				'label'   		=> esc_html__('Icon Font Size (px)', MELA_TD),
				'type'          => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors'  => array(
					'.ma-el-advance-tab .ma-el-advance-tab-nav li i' => 'font-size:{{SIZE}}{{UNIT}} !important;',
				),
				'style_transfer' => true
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_tabs_heading_box_border',
				'separator'     => 'before',
				'selector'      => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li',
			]
		);

		$this->add_control(
			'ma_el_tabs_heading_border_radius',
			[
				'label'         => __('Border Radius', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', '%', 'em'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_tab();



		// Active State Tab
		$this->start_controls_tab('ma_el_tabs_header_active', [
			'label' => esc_html__(
				'Active',
				MELA_TD
			)
		]);
		$this->add_control(
			'ma_el_tab_text_color_active',
			[
				'label' => esc_html__('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#0a1724',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active span, {{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active i' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'ma_el_tab_bg_color_active',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active, {{WRAPPER}} .ma-el-advance-tab.four .ma-el-advance-tab-nav li::before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ma-el-advance-tab.three .ma-el-advance-tab-nav li::before' => 'border-left-color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'ma_el_tab_border_color_active',
			[
				'label' => esc_html__('Bottom Border Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#704aff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab.two .ma-el-advance-tab-nav li.active' => 'border-bottom: 1px solid {{VALUE}};',
					'{{WRAPPER}} .ma-el-advance-tab.four .ma-el-advance-tab-nav li::after' => 'background: {{VALUE}};'
				],
				'condition' => [
					'ma_el_tabs_preset' => 'two'
				]
			]
		);

		$this->add_control(
			'ma_el_tab_border_left_color_active',
			[
				'label' => esc_html__('Bottom Left Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#704aff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab.four .ma-el-advance-tab-nav li::after' => 'background: {{VALUE}};'
				],
				'condition' => [
					'ma_el_tabs_preset' => 'four'
				]
			]
		);


		$this->add_control(
			'ma_el_tabs_heading_active_padding',
			[
				'label' => esc_html__('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'ma_el_tabs_heading_active_margin',
			[
				'label' => esc_html__('Margin', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'          => 'ma_el_tabs_heading_active_box_shadow',
				'selector'      => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_tabs_heading_active_box_border',
				'separator'     => 'before',
				'selector'      => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active',
			]
		);

		$this->add_control(
			'ma_el_tabs_heading_active_border_radius',
			[
				'label'         => __('Border Radius', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', '%', 'em'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-nav li.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]

			]
		);




		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style MA Tabs Content Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'ma_el_section_tabs_tab_content_style_settings',
			[
				'label' => esc_html__('Content', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'exclusive_tabs_content_title_color',
			[
				'label' => esc_html__('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#0a1724',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content .ma-el-advance-tab-content-title' =>
					'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_tabs_content_title_typography',
				'label' => esc_html__('Title Typography', MELA_TD),
				'selector' => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content-title,
					{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content h1,
					{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content h2,
					{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content h3,
					{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content h4,
					{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content h5,
					{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content h6'
			]
		);
		$this->add_control(
			'exclusive_tabs_content_bg_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content ' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'exclusive_tabs_content_text_color',
			[
				'label' => esc_html__('Content Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#333',
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content ' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_tabs_content_typography',
				'label' => esc_html__('Content Typography', MELA_TD),
				'selector' => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content',
			]
		);

		$this->add_responsive_control(
			'ma_el_tabs_content_border_radius',
			array(
				'label'      => esc_html__('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'ma_el_tabs_content_box_shadow',
				'exclude' => array(
					'box_shadow_position',
				),
				'selector' => '{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content',
			)
		);


		$this->add_responsive_control(
			'ma_el_tabs_content_padding',
			[
				'label' => esc_html__('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default' => [
					'top' => 40,
					'right' => 40,
					'bottom' => 40,
					'left' => 40,
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_tabs_content_margin',
			array(
				'label'      => esc_html__('Margin', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .ma-el-advance-tab .ma-el-advance-tab-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/tabs/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/tabs-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=lsqGmIrdahw" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$column_order = (isset($settings['ma_el_tabs_left_cols'])) ? 'jltma-row d-flex' : '';
		$this->add_render_attribute(
			'ma_el_tab_wrapper',
			[
				'id'     => "ma-el-advance-tabs-{$this->get_id()}",
				'class'	 => [
					'ma-el-advance-tab',
					$settings['ma_el_tabs_preset'],
					$column_order
				],
				'data-tab-effect' => $settings['ma_el_tabs_effect']
			]
		);

		if (isset($settings['ma_el_tabs_left_cols'])) {
			$ma_el_tabs_left_cols = explode('-',  $settings['ma_el_tabs_left_cols']);
		}
		$column_order = isset($settings['ma_el_tabs_content_style']) ? $settings['ma_el_tabs_content_style'] : "";
?>


		<div <?php echo $this->get_render_attribute_string('ma_el_tab_wrapper'); ?> data-tabs>

			<?php if (isset($settings['ma_el_tabs_preset']) && $settings['ma_el_tabs_preset'] == "five") { ?>
				<div class="jltma-col-md-<?php echo esc_attr($ma_el_tabs_left_cols[0]); ?> <?php
																							if ($column_order == "float-left") {
																								echo "order-1";
																							} elseif ($settings['ma_el_tabs_left_cols'] == "12-12") {
																								# code...
																							} else {
																								echo "order-2";
																							} ?>">
				<?php } ?>

				<ul class="ma-el-advance-tab-nav">
					<?php foreach ($settings['ma_el_tabs'] as $key => $tab) : ?>
						<li class="<?php echo esc_attr($tab['ma_el_tab_show_as_default']); ?>" data-tab data-tab-id="jltma-tab-<?php echo $this->get_id() . $key; ?>">
							<?php if ($settings['ma_el_tabs_icon_show'] === 'yes') :
								if ($tab['ma_el_tabs_icon_type'] === 'icon') : ?>
									<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-home', 'icon', $tab['ma_el_tab_title_icon'], 'ma_el_tab_title_icon'); ?>
								<?php elseif ($tab['ma_el_tabs_icon_type'] === 'image') : ?>
									<img src="<?php echo esc_attr($tab['ma_el_tab_title_image']['url']);
												?>">
								<?php endif; ?>
							<?php endif; ?>
							<span class="ma-el-tab-title"><?php echo $tab['ma_el_tab_title']; ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ($settings['ma_el_tabs_preset'] == "five") { ?>
				</div>

				<div class="jltma-col-md-<?php echo esc_attr($ma_el_tabs_left_cols[1]); ?> <?php if ($column_order == "float-left") {
																								echo "order-2";
																							} else {
																								echo "order-1";
																							} ?>">
				<?php } ?>

				<div class="tab-content">
					<?php foreach ($settings['ma_el_tabs'] as $key => $tab) : $ma_el_find_default_tab[] = $tab['ma_el_tab_show_as_default']; ?>
						<div id="jltma-tab-<?php echo $this->get_id() . $key; ?>" class="ma-el-advance-tab-content tab-pane <?php echo esc_attr(
																																$tab['ma_el_tab_show_as_default']
																															); ?>">
							<?php
							// Nested Accordion Available for Premium Version
							

								if ($tab['ma_tabs_content_type'] == 'content') {

									echo do_shortcode($tab['ma_el_tab_content']);
								} else if ($tab['ma_tabs_content_type'] == 'section' && !empty($tab['saved_section'])) {

									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tab['saved_section']);
								} else if ($tab['ma_tabs_content_type'] == 'template' && !empty($tab['templates'])) {

									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tab['templates']);
								} else if ($tab['ma_tabs_content_type'] == 'widget' && !empty($tab['saved_widget'])) {

									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tab['saved_widget']);
								}

								// Free Version Code
							 ?>
						</div><!-- ma-el-advance-tab-content -->
					<?php endforeach; ?>
				</div> <!-- tab-content -->


				<?php if ($settings['ma_el_tabs_preset'] == "five") { ?>
				</div> <!-- col-md-5 -->
			<?php } ?>

		</div>
<?php
	}



	public function get_page_template_options($type = '')
	{

		$page_templates = Master_Addons_Helper::ma_get_page_templates($type);

		$options[-1]   = esc_html__('Select', MELA_TD);

		if (count($page_templates)) {
			foreach ($page_templates as $id => $name) {
				$options[$id] = $name;
			}
		} else {
			$options['no_template'] = esc_html__('No saved templates found!', MELA_TD);
		}

		return $options;
	}
}
