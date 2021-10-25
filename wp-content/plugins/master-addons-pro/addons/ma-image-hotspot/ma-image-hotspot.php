<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Icons_Manager;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Css_Filter;



use MasterAddons\Inc\Helper\Master_Addons_Helper;
use MasterAddons\Inc\Controls\MA_Group_Control_Transition;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 10/12/19
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * News Ticker Widget
 */
class Image_Hotspot extends Widget_Base
{

	public function get_name()
	{
		return 'ma-image-hotspot';
	}

	public function get_title()
	{
		return __('Image Hotspot', MELA_TD);
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-image-hotspot';
	}

	public function get_keywords()
	{
		return ['image', 'tooltips', 'image tooltips', 'hotspot', 'marker', 'image hotspot', 'content', 'index'];
	}


	public function get_help_url()
	{
		return 'https://master-addons.com/demos/image-hotspot/';
	}


	protected function _register_controls()
	{
		/*
		 * MA Hotspots: Image
		 */
		$this->start_controls_section(
			'ma_el_hotspot_image_section',
			[
				'label' => __('Image', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_hotspot_image',
			[
				'label' => __('Choose Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Actually its `image_size`
				'label' => __('Image Size', MELA_TD),
				'default' => 'large',
			]
		);

		$this->add_responsive_control(
			'ma_el_hotspot_align',
			[
				'label' => __('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', MELA_TD),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', MELA_TD),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', MELA_TD),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspot_view',
			[
				'label' => __('View', MELA_TD),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);
		$this->end_controls_section();



		/*
		 * MA Hotspots Section
		 */
		$this->start_controls_section(
			'ma_el_hotspots_section',
			[
				'label' => __('Hotspots', MELA_TD),
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				]
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('ma_el_hotspots_repeater_section');

		$repeater->start_controls_tab('tab_content', ['label' => __('Content', MELA_TD)]);

		$repeater->add_control(
			'ma_el_hotspot_type',
			[
				'label'		=> __('Type', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'text',
				'options' 	=> [
					'text' 		=> __('Text', MELA_TD),
					'icon' 		=> __('Icon', MELA_TD),
				],
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_text',
			[
				'default'	=> __('X', MELA_TD),
				'type'		=> Controls_Manager::TEXT,
				'label' 	=> __('Text', MELA_TD),
				'separator' => 'none',
				'dynamic' => [
					'active' => true,
				],
				'condition'		=> [
					'ma_el_hotspot_type'	=> 'text'
				]
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_selected_icon',
			array(
				'label'       => __('Icon', MELA_TD),
				'description' => __('Please choose an icon from the list.', MELA_TD),
				'type'    => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fa fa-search',
					'library' => 'fa-solid',
				],
				'render_type'      => 'template',
				'condition' => array(
					'ma_el_hotspot_type' => 'icon'
				)
			)
		);

		$repeater->add_control(
			'ma_el_hotspot_link',
			[
				'label' 		=> __('Link', MELA_TD),
				'description' 	=> __('Active only when tolltips\' Trigger is set to Hover or if tooltip is disabled responsively, below a certain breakpoint.', MELA_TD),
				'type' 			=> Controls_Manager::URL,
				'label_block' 	=> false,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' 	=> esc_url(home_url('/')),
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_content',
			[
				'label' 	=> __('Tooltip Content', MELA_TD),
				'type' 		=> Controls_Manager::WYSIWYG,
				'dynamic' 	=> [
					'active' => true,
				],
				'default' 	=> __('I am a tooltip for a hotspot', MELA_TD),
			]
		);

		//			$repeater->add_control(
		//				'ma_el_hotspot_item_id',
		//				[
		//					'label' 		=> __( 'CSS ID', MELA_TD ),
		//					'type' 			=> Controls_Manager::TEXT,
		//					'default' 		=> '',
		//					'dynamic' 		=> [ 'active' => true ],
		//					'label_block' 	=> true,
		//					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', MELA_TD ),
		//				]
		//			);
		//			$repeater->add_control(
		//				'ma_el_hotspot_css_classes',
		//				[
		//					'label' 		=> __( 'CSS Classes', MELA_TD ),
		//					'type' 			=> Controls_Manager::TEXT,
		//					'default' 		=> '',
		//					'prefix_class' 	=> '',
		//					'dynamic' 		=> [ 'active' => true ],
		//					'label_block' 	=> true,
		//					'title' 		=> __( 'Add your custom class WITHOUT the dot. e.g: my-class', MELA_TD ),
		//				]
		//			);
		$repeater->end_controls_tab();



		$repeater->start_controls_tab('ma_el_hotspot_tab_style', ['label' => __('Style', MELA_TD)]);

		$repeater->add_control(
			'ma_el_hotspot_default',
			[
				'label' => __('Default', MELA_TD),
				'type' => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-hotspots__wrapper' => 'color: {{VALUE}};',
				],
			]
		);


		$repeater->add_control(
			'ma_el_hotspot_background_color',
			[
				'label' 	=> __('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-hotspots__wrapper' 		=> 'background-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-hotspots__wrapper:before' 	=> 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'ma_el_hotspot_opacity',
			[
				'label' 	=> __('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.1,
					],
				],
				'separator' => 'after',
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-container {{CURRENT_ITEM}} .ma-el-hotspots__wrapper' => 'opacity: {{SIZE}};',
				],
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_hover',
			[
				'label' => __('Hover', MELA_TD),
				'type' => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_color_hover',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspots-container {{CURRENT_ITEM}} .ma-el-hotspots__wrapper:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_background_color_hover',
			[
				'label' 	=> __('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-hotspots__wrapper:hover' 			=> 'background-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-hotspots__wrapper:hover:before' 	=> 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'ma_el_hotspot_opacity_hover',
			[
				'label' 	=> __('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0,
						'step' 	=> 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-container {{CURRENT_ITEM}} .ma-el-hotspots__wrapper:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$repeater->end_controls_tab();


		$repeater->start_controls_tab('ma_el_hotspot_tab_position', ['label' => __('Position', MELA_TD)]);

		$repeater->add_control(
			'ma_el_hotspot_position_horizontal',
			[
				'label' 	=> __('Horizontal position (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default'	=> [
					'size'	=> 50,
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 100,
						'step'	=> 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%;',
				],
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_position_vertical',
			[
				'label' 	=> __('Vertical position (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default'	=> [
					'size'	=> 50,
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 100,
						'step'	=> 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%;',
				],
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_tooltips_heading',
			[
				'label' => __('Tooltips', MELA_TD),
				'type' => Controls_Manager::HEADING,
			]
		);

		$repeater->add_control(
			'ma_el_hotspot_tooltip_position',
			[
				'label'		=> __('Show to', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '',
				'options' 	=> [
					'' 			=> __('Global', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
					'left' 		=> __('Left', MELA_TD),
					'top' 		=> __('Top', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
			]
		);


		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'ma_el_hotspots',
			[
				'label' 	=> __('Hotspots', MELA_TD),
				'type' 		=> Controls_Manager::REPEATER,
				'default' 	=> [
					[
						'text' 	=> '1',
					],
					[
						'text' 	=> '2',
					],
				],
				'fields' 		=> $repeater->get_controls(),
				'title_field' 	=> '{{{ ma_el_hotspot_text }}}',
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				]
			]
		);


		$this->end_controls_section();



		/*
		 * Tooltips
		 */

		$this->start_controls_section(
			'ma_el_hotspot_section_tooltips',
			[
				'label' => __('Tooltips', MELA_TD),
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				]
			]
		);

		$this->add_control(
			'ma_el_hotspot_position',
			[
				'label'		=> __('Show Position', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'top',
				'options' 	=> [
					'bottom' 	=> __('Bottom', MELA_TD),
					'left' 		=> __('Left', MELA_TD),
					'top' 		=> __('Top', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				],
				'frontend_available' => true
			]
		);


		$this->add_control(
			'ma_el_hotspot_disable',
			[
				'label'		=> esc_html__('Disable On', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '',
				'options' 	=> [
					'' 			=> esc_html__('None', MELA_TD),
					'tablet' 	=> esc_html__('Tablet & Mobile', MELA_TD),
					'mobile' 	=> esc_html__('Mobile', MELA_TD),
				],
				'frontend_available' => true,
				'selectors' => [
					'(tablet){{WRAPPER}} .ma-el-hotspot .ma-el-tooltip-text' => 'display: none;',
					'(mobile){{WRAPPER}} .ma-el-hotspot .ma-el-tooltip-text' => 'display: none;',
				],

			]
		);


		$this->add_control(
			'ma_el_tooltip_visible_hover',
			[
				'label' 		=> esc_html__('Visible on Hover', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'label_on' 		=> esc_html__('Yes', MELA_TD),
				'label_off' 	=> esc_html__('No', MELA_TD),
				'return_value' 	=> 'yes',
				'default' 		=> 'no',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item:hover .ma-el-tooltip-text' => 'visibility: visible;opacity: 1; display:block;',
				]

			]
		);

		$this->add_control(
			'ma_el_hotspot_arrow',
			[
				'label'		=> __('Arrow', MELA_TD),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> '""',
				'options' 	=> [
					'""' 	=> __('Show', MELA_TD),
					'none' 	=> __('Hide', MELA_TD),
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text:after' => 'content: {{VALUE}};',
				],
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				]
			]
		);



		$this->add_control(
			'ma_el_hotspot_margin',
			[
				'label' 		=> __('Margin', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%', 'em'],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_hotspot_width',
			[
				'label' 		=> __('Maximum Width', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 200,
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 500,
					],
				],
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				],
				'selectors'		=> [
					'{{WRAPPER}}  .ma-el-hotspot .ma-el-tooltip-item .ma-el-tooltip-text' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ma_el_hotspot_zindex',
			[
				'label'			=> __('zIndex', MELA_TD),
				'description'   => __('Adjust the z-index of the tooltips. Defaults to 999', MELA_TD),
				'type'			=> Controls_Manager::NUMBER,
				'default'		=> '999',
				'min'			=> -9999999,
				'step'			=> 1,
				'condition'		=> [
					'ma_el_hotspot_image[url]!' => '',
				],
				'selectors'		=> [
					'{{WRAPPER}}  .ma-el-hotspot .ma-el-tooltip-item .ma-el-tooltip-text' => 'z-index: {{SIZE}};',
				]
			]
		);

		$this->end_controls_section();




		/*
		 * MA Image Hotspot: Image Style Tab
		 */

		$this->start_controls_section(
			'ma_el_hotspot_section_style_image',
			[
				'label' => __('Image', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_hotspot_opacity',
			[
				'label' 	=> __('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0.10,
						'step' 	=> 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'ma_el_hotspot_image_border',
				'label' 	=> __('Image Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper img',
			]
		);

		$this->add_control(
			'ma_el_hotspot_image_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'ma_el_hotspot_image_box_shadow',
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper img',
				'separator'	=> '',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'ma_el_hotspot_image_css_filters',
				'selector' => '{{WRAPPER}} .ma-el-hotspots-wrapper img',
			]
		);

		$this->end_controls_section();


		/*
		 * MA Hotspots: Hotspots
		 */

		$this->start_controls_section(
			'ma_el_hotspots_section_style',
			[
				'label' => __('Hotspots', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_hotspot_pulse',
			[
				'label' 		=> __('Disable Pulse Effect', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '""',
				'return_value' 	=> 'none',
				'selectors'		=> [
					'{{WRAPPER}} .ma-el-hotspots__wrapper:before' => 'content: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'ma_el_hotspots_padding',
			[
				'label' 		=> __('Text Padding', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', 'em', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_hotspots_border_radius',
			[
				'label' 	=> __('Border Radius', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 100,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 100,
						'min' 	=> 0,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper' => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper:before' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'ma_el_hotspots_typography',
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
				'separator'	=> 'before',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'ma_el_hotspots',
				'selector' 		=> '{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper,
									{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper:before',
			]
		);

		$this->start_controls_tabs('tabs_ma_el_hotspots_style');

		$this->start_controls_tab(
			'tab_ma_el_hotspots_default',
			[
				'label' => __('Default', MELA_TD),
			]
		);

		$this->add_responsive_control(
			'ma_el_hotspots_opacity',
			[
				'label' 	=> __('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0.10,
						'step' 	=> 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_hotspots_size',
			[
				'label' 	=> __('Size', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 2,
						'min' 	=> 0.5,
						'step'	=> 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-tooltip-content' => 'transform: scale({{SIZE}})',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspots_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspot__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspots_background_color',
			[
				'label' 	=> __('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				//					'scheme' 	=> [
				//						'type' 	=> Scheme_Color::get_type(),
				//						'value' => Scheme_Color::COLOR_1,
				//					],
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper'
					=> 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper .ma-el-hotspots__wrapper:before'
					=> 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'ma_el_hotspots_border',
				'label' 	=> __('Text Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'ma_el_hotspots_box_shadow',
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper .ma-el-hotspots__wrapper',
				'separator'	=> ''
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_ma_el_hotspots_hover',
			[
				'label' => __('Hover', MELA_TD),
			]
		);

		$this->add_responsive_control(
			'ma_el_hotspots_hover_opacity',
			[
				'label' 	=> __('Opacity (%)', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 1,
						'min' 	=> 0.10,
						'step' 	=> 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspots__wrapper' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_hotspots_hover_size',
			[
				'label' 	=> __('Size', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 1,
				],
				'range' 	=> [
					'px' 	=> [
						'max' 	=> 2,
						'min' 	=> 0.5,
						'step'	=> 0.01,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspots__wrapper' =>
					'transform: scale({{SIZE}})',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspots_hover_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspot__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspots_hover_background_color',
			[
				'label' 	=> __('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'default'	=> '',
				//					'scheme' 	=> [
				//						'type' 	=> Scheme_Color::get_type(),
				//						'value' => Scheme_Color::COLOR_4,
				//					],
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspots__wrapper' 		=> 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspots__wrapper:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'ma_el_hotspots_hover_border',
				'label' 	=> __('Text Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspots__wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'ma_el_hotspots_hover_box_shadow',
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-wrapper:hover .ma-el-hotspots__wrapper',
				'separator'	=> ''
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		/*
		 * MA Hotspots: Tooltips
		 */


		$this->start_controls_section(
			'ma_el_hotspot_tooltips_style',
			[
				'label' => __('Tooltips', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'ma_el_hotspot_tooltips_align',
			[
				'label' 	=> __('Alignment', MELA_TD),
				'type' 		=> Controls_Manager::CHOOSE,
				'options' 	=> [
					'left' 	=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'fa fa-align-left',
					],
					'center' 	=> [
						'title' => __('Center', MELA_TD),
						'icon' 	=> 'fa fa-align-center',
					],
					'right' 	=> [
						'title' => __('Right', MELA_TD),
						'icon'	=> 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspot-tooltip.ma-el-hotspot-tooltip-{{ID}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspot_tooltips_padding',
			[
				'label' 		=> __('Padding', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', 'em', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-container .ma-el-tooltip-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspot_tooltips_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::DIMENSIONS,
				'size_units' 	=> ['px', '%'],
				'selectors' 	=> [
					'{{WRAPPER}} .ma-el-hotspots-container .ma-el-tooltip-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_el_hotspot_tooltips_background_color',
			[
				'label' 	=> __('Background Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspot .ma-el-tooltip-text' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_hotspot_tooltips_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-hotspots-container .ma-el-tooltip-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'ma_el_hotspot_tooltips_border',
				'label' 	=> __('Border', MELA_TD),
				'selector' 	=> '{{WRAPPER}} .ma-el-hotspots-container .ma-el-tooltip-text',
			]
		);


		$this->add_control(
			'ma_el_hotspot_tooltips_arrow_color',
			[
				'label' 	=> __('Arrow Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-top .ma-el-tooltip-text:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-right .ma-el-tooltip-text:after' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-bottom .ma-el-tooltip-text:after' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-left .ma-el-tooltip-text:after' => 'border-left-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'ma_el_hotspot_tooltips_typography',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 		=> 'ma_el_hotspot_tooltips_box_shadow',
				'selector'	=> '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text',
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/image-hotspot/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/image-hotspot/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=IDAd_d986Hg" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		//Upgrade to Pro
		
	}




	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if (empty($settings['ma_el_hotspot_image']['url']))
			return;

		$this->add_render_attribute('ma_el_hotspot_wrapper', 'class', 'ma-el-hotspots-wrapper');
		$this->add_render_attribute('ma_el_hotspot_container', 'class', 'ma-el-hotspots-container');
		$this->add_render_attribute('ma_el_tooltip_wrapper', ['class' => 'ma-el-tooltip']);
?>

		<div <?php echo $this->get_render_attribute_string('ma_el_hotspot_wrapper'); ?>>
			<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'ma_el_hotspot_image'); ?>



			<?php if ($settings['ma_el_hotspots']) { ?>
				<div <?php echo $this->get_render_attribute_string('ma_el_hotspot_container'); ?>>

					<?php foreach ($settings['ma_el_hotspots'] as $index => $item) {
						//								print_r($item);

						$has_icon 				= false;
						$hotspot_tag 			= 'div';
						$hotspot_key 			= $this->get_repeater_setting_key('hotspot', 'ma_el_hotspots', $index);
						$wrapper_key 			= $this->get_repeater_setting_key('wrapper', 'ma_el_hotspots', $index);
						$icon_key 				= $this->get_repeater_setting_key('icon', 'ma_el_hotspots', $index);
						$icon_wrapper_key		= $this->get_repeater_setting_key('icon-wrapper', 'ma_el_hotspots', $index);
						$text_key 				= $this->get_repeater_setting_key('text', 'ma_el_hotspots', $index);
						$tooltip_key 			= $this->get_repeater_setting_key('content', 'ma_el_hotspots', $index);

						$content_id 			= $this->get_id() . '_' . $item['_id'];


						$this->add_render_attribute([
							$wrapper_key => [
								'class' => 'ma-el-hotspots__wrapper',
							],
							$text_key => [
								'class' => 'ma-el-hotspot__text',
							],
							$tooltip_key => [
								'class' => 'ma-el-tooltip-text',
								'id'	=> 'ma-el-tooltip-text-' . $content_id,
							],
							$hotspot_key => [
								'class' => [
									'elementor-repeater-item-' . $item['_id'],
									'ma-el-hotspot',
								],
								'data-tooltips-content' 			=> '#tooltip-content-' . $content_id,
								'data-tooltips-position' 			=> $item['ma_el_hotspot_tooltip_position'],
								// 'data-tooltips-arrow-position-h' 	=> $item['ma_el_hotspot_item_id'],
								// 'data-tooltips-arrow-position-v' 	=> $item['ma_el_hotspot_tooltip_arrow_position_v'],
								'data-tooltips-class' 			=> [
									'ma-el-global',
									'ma-el-hotspot-tooltip',
									'ma-el-hotspot-tooltip-' . $this->get_id(),
								]
							],
						]);



						if ('icon' === $item['ma_el_hotspot_type'] && (!empty($item['icon']) || !empty($item['ma_el_hotspot_selected_icon']['value']))) {
							$migrated = isset($item['__fa4_migrated']['ma_el_hotspot_selected_icon']);
							$is_new = empty($item['icon']) && Icons_Manager::is_migration_allowed();

							$has_icon = true;

							$this->add_render_attribute($icon_wrapper_key, 'class', [
								'ma-el-hotspot__icon',
								'ma-el-icon',
								'ma-el-icon-support--svg',
							]);

							if (!empty($item['icon'])) {
								$this->add_render_attribute($icon_key, [
									'class' => esc_attr($item['icon']),
									'aria-hidden' => 'true',
								]);
							}
						}

						//								if ( $item['_item_id'] ) {
						//									$this->add_render_attribute( $hotspot_key, 'id', $item['ma_el_hotspot_item_id'] );
						//								}
						//
						//								if ( $item['css_classes'] ) {
						//									$this->add_render_attribute( $hotspot_key, 'class', $item['ma_el_hotspot_css_classes'] );
						//								}

						if (!empty(trim($item['ma_el_hotspot_link']['url']))) {

							$hotspot_tag = 'a';

							$this->add_render_attribute($hotspot_key, 'href', $item['ma_el_hotspot_link']['url']);

							if ($item['ma_el_hotspot_link']['is_external']) {
								$this->add_render_attribute($hotspot_key, 'target', '_blank');
							}

							if (!empty($item['ma_el_hotspot_link']['nofollow'])) {
								$this->add_render_attribute($hotspot_key, 'rel', 'nofollow');
							}
						}

					?><<?php echo $hotspot_tag; ?> <?php echo $this->get_render_attribute_string($hotspot_key); ?>>


							<div <?php echo $this->get_render_attribute_string('ma_el_tooltip_wrapper'); ?>>
								<div class="ma-el-tooltip-item tooltip-<?php echo ($item['ma_el_hotspot_tooltip_position']) ? $item['ma_el_hotspot_tooltip_position'] : $settings['ma_el_hotspot_position']; ?>">
									<div class="ma-el-tooltip-content">

										<span <?php echo $this->get_render_attribute_string($wrapper_key); ?>>
											<span <?php echo $this->get_render_attribute_string($text_key); ?>><?php
																												if ($has_icon) {
																												?><span <?php echo $this->get_render_attribute_string($icon_wrapper_key); ?>><?php
																																																if ($is_new || $migrated) {
																																																	Icons_Manager::render_icon($item['ma_el_hotspot_selected_icon'], ['aria-hidden' => 'true']);
																																																} else {
																																																?><i <?php echo $this->get_render_attribute_string($icon_key); ?>></i><?php
																																																																	}
																																																																		?></span><?php
																																																																				} else {
																																																																					echo $item['ma_el_hotspot_text'];
																																																																				}
																																																																					?></span>
										</span>
									</div>

									<div <?php echo $this->get_render_attribute_string($tooltip_key); ?>>
										<?php echo $this->parse_text_editor($item['ma_el_hotspot_content']); ?>
									</div>
								</div>
							</div>

						</<?php echo $hotspot_tag; ?>>


					<?php } ?>

				</div>

			<?php } ?>

		</div>

<?php
	}
}
