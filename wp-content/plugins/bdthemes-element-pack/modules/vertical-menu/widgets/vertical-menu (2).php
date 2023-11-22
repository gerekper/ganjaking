<?php

namespace ElementPack\Modules\VerticalMenu\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

use ElementPack\Modules\VerticalMenu\ep_vertical_menu_walker;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Vertical_Menu extends Module_Base {

	public function get_name() {
		return 'bdt-vertical-menu';
	}

	public function get_title() {
		return BDTEP . esc_html__('Vertical Menu', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-vertical-menu';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['navbar', 'menu', 'vertical'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-vertical-menu'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['metis-menu', 'ep-scripts'];
		} else {
			return ['metis-menu', 'ep-vertical-menu'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/ezZBOistuF4';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_static_menu',
			[
				'label'     => __('Vertical Menu', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'dynamic_menu',
			[
				'label'   => esc_html__('Dynamic Menu', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'navbar',
			[
				'label'   => esc_html__('Select Menu', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_get_menu(),
				'default' => 0,
				'condition' => ['dynamic_menu' => 'yes'],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'menu_title',
			[
				'label'       => __('Menu Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'condition' => [
					'menu_type!' => 'child_end'
				]
			]
		);

		$repeater->add_control(
			'menu_type',
			[
				'label'       => __('Select Item Type', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'options' 	  => [
					'item'      => 'Item',
					'child_start' => 'Child Start',
					'child_end'   => 'Child End',
				],
				'default' => 'item',
			]
		);

		$repeater->add_control(
			'menu_link',
			[
				'label'       => __('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'default' => [
					'url' => '#',
				],
				'label_block' => true,
				'condition' => [
					'menu_type!' => 'child_end'
				]
			]
		);

		$repeater->add_control(
			'menu_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'condition' => [
					'menu_type!' => 'child_end'
				]
			]
		);

		$this->add_control(
			'menus',
			[
				'label'   => __('Menu Items', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'condition' => ['dynamic_menu' => ''],
				'separator' => 'before',
				'default' => [
					[
						'menu_title'   => __('About', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Gallery', 'bdthemes-element-pack'),
						'menu_link'    => '#',
						'menu_type' => 'child_start'
					],
					[
						'menu_title'   => __('Gallery 01', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Gallery 02', 'bdthemes-element-pack'),
						'menu_link'    => '#',
						'menu_type' => 'child_start'
					],
					[
						'menu_title'   => __('Sub Gallery 01', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Sub Gallery 02', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Sub Gallery 03', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_type' => 'child_end'
					],
					[
						'menu_title'   => __('Gallery 03', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_type' => 'child_end'
					],
					[
						'menu_title'   => __('Contacts', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, menu_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} <# print( (menu_type == "child_start" ) ? "<b>[ Child Start:</b> " + menu_title : menu_title ) #><# print( (menu_type == "child_end" ) ? "<b>Child End ]</b>" : "" ) #>',
			]
		);

		$this->add_control(
			'hr_divider',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'additional_heading',
			[
				'label'   => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'hr2_divider',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_sticky',
			[
				'label'   => esc_html__('Show Sticky', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'submenu_type',
			[
				'label'   => esc_html__('Submenu Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'outer',
				'options' => [
					'outer'    => esc_html__('Outer', 'bdthemes-element-pack'),
					'inner'   => esc_html__('Inner', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-submenu-type-',
			]
		);

		$this->add_control(
			'columns',
			[
				'label'          => esc_html__('Sub Menu Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'description' => esc_html__('It\'s just work for Desktop Device.', 'bdthemes-element-pack'),
				'default'        => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'condition' => [
					'submenu_type' => 'outer'
				],
				'prefix_class' => 'bdt-submenu-column-',
			]
		);

		$this->add_responsive_control(
			'menu_width',
			[
				'label'   => esc_html__('Menu Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 1200,
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'sub_menu_width',
			[
				'label'   => esc_html__('Sub Menu Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 1000,
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-vertical-submenu-width: {{SIZE}}px;'
				],
				'condition' => [
					'submenu_type' => 'outer'
				],
			]
		);

		$this->add_responsive_control(
			'menu_alignment',
			[
				'label'   => __('Menu Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu' => 'justify-content: {{VALUE}}; display: flex;',
				],
			]
		);

		$this->add_responsive_control(
			'menu_text_alignment',
			[
				'label'   => __('Text Alignemnt', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'remove_parent_link',
			[
				'label'     => esc_html__('Remove Parent Link', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'vertical_menu_style',
			[
				'label'     => __('Main Menu', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'main_menu_background',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'main_menu_bg_border',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu',
			]
		);

		$this->add_responsive_control(
			'main_menu_bg_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'main_menu_bg_link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('menu_link_styles');

		$this->start_controls_tab(
			'menu_link_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'menu_link_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a .bdt-menu-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a .bdt-menu-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_control(
			'menu_link_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a' => 'background-color: {{VALUE}};',
				],
			]
		);



		$this->add_control(
			'menu_parent_arrow_color',
			[
				'label'     => esc_html__('Parent Indicator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .metismenu > li > .has-arrow::after' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'menu_border',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a',
			]
		);

		$this->add_responsive_control(
			'menu_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_spacing',
			[
				'label' => esc_html__('Item Gap', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_icon_spacing',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a .bdt-menu-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography_normal',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'menu_link_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_background_hover',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'menu_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'menu_icon_hover_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a:hover .bdt-menu-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > a:hover .bdt-menu-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_control(
			'menu_parent_arrow_hover_color',
			[
				'label'     => esc_html__('Parent Indicator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .metismenu > li > .has-arrow:hover:after' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'menu_hover_color_active',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li.mm-active > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_hover_background_color_active',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li.mm-active > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_border_color_active',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li.mm-active > a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'menu_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'menu_icon_active_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li.mm-active > a .bdt-menu-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_control(
			'menu_parent_arrow_active_color',
			[
				'label'     => esc_html__('Parent Indicator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .metismenu > li.mm-active > .has-arrow:after' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Submenu Style
		$this->start_controls_section(
			'vertical_sub_menu_style',
			[
				'label'     => __('Sub Menu', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sub_menu_background',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'sub_menu_bg_border',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul',
			]
		);

		$this->add_responsive_control(
			'sub_menu_bg_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_menu_bg_link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('sub_menu_link_styles');

		$this->start_controls_tab(
			'sub_menu_link_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'sub_menu_link_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_link_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a .bdt-menu-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_control(
			'sub_menu_parent_arrow_color',
			[
				'label'     => esc_html__('Parent Indicator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li .has-arrow::after' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'sub_menu_border',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a',
			]
		);

		$this->add_responsive_control(
			'sub_menu_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_menu_link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_menu_spacing',
			[
				'label' => esc_html__('Item Gap', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_menu_icon_spacing',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a .bdt-menu-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_menu_typography_normal',
				'selector' => '{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_menu_link_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'sub_menu_link_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_link_background_hover',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'sub_menu_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_hover_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li  a:hover .bdt-menu-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_control(
			'sub_menu_parent_arrow_hover_color',
			[
				'label'     => esc_html__('Parent Indicator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li .has-arrow:hover:after' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sub_menu_link_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'sub_menu_hover_color_active',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li.mm-active > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_hover_background_color_active',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li.mm-active > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_menu_border_color_active',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li.mm-active > a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'sub_menu_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_active_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li.mm-active a .bdt-menu-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'dynamic_menu' => ''
				]
			]
		);

		$this->add_control(
			'sub_menu_parent_arrow_active_color',
			[
				'label'     => esc_html__('Parent Indicator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-vertical-menu .sidebar-nav .metismenu > li > ul > li.mm-active .has-arrow:after' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('vertical_menu', 'class', 'bdt-vertical-menu');

		if ('yes' == $settings['show_sticky']) {
			$this->add_render_attribute('vertical_menu', 'data-bdt-sticky', "bottom: #offset;");
		}

		$this->add_render_attribute(
			[
				'vertical_menu' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'id'                 => 'bdt-metismenu-' . $this->get_id(),
							'removeParentLink' => ('yes' == $settings['remove_parent_link']) ? 'yes' : 'no',
						]))
					]
				]
			]
		);

?>
		<div <?php echo ($this->get_render_attribute_string('vertical_menu')); ?>>

			<?php if ('yes' == $settings['dynamic_menu']) : ?>
				<?php $this->dynamic_menu(); ?>
			<?php else : ?>
				<?php $this->static_menu(); ?>
			<?php endif; ?>

		</div>
	<?php
	}

	protected function static_menu() {
		$settings = $this->get_settings_for_display();


	?>
		<nav class="sidebar-nav">
			<ul class="metismenu" id="<?php echo 'bdt-metismenu-' . $this->get_id(); ?>">

				<?php foreach ($settings['menus'] as $item) : ?>

					<?php
					$target = (!empty($item['menu_link']['is_external'])) ? 'target="_blank"' : '';
					$nofollow = (!empty($item['menu_link']['nofollow'])) ? ' rel="nofollow"' : '';

					if ($item['menu_type'] == 'child_start') {
						$item_class = 'has-arrow';
					} else {
						$item_class = '';
					}

					?>

					<?php if ($item['menu_type'] !== 'child_end') : ?>
						<li class="bdt-menu-item">
							<a class="<?php echo $item_class; ?>" href="<?php echo esc_url($item['menu_link']['url']); ?>" <?php echo wp_kses_post($target);
																															echo wp_kses_post($nofollow); ?>>
								<?php if (!empty($item['menu_icon']['value'])) : ?>
									<span class="bdt-menu-icon">
										<?php Icons_Manager::render_icon($item['menu_icon'], ['aria-hidden' => 'true']); ?>
									</span>
								<?php endif; ?>
								<?php echo wp_kses($item['menu_title'], element_pack_allow_tags('title')); ?>
							</a>
						<?php endif; ?>

						<?php if ($item['menu_type'] == 'child_start') : ?>
							<ul>
							<?php endif; ?>

							<?php if ($item['menu_type'] == 'child_end') : ?>
							</ul>
						</li>
					<?php endif; ?>

					<?php if ($item['menu_type'] == 'item') : ?>
						</li>
					<?php endif; ?>

				<?php endforeach; ?>
			</ul>
		</nav>
	<?php
	}

	protected function dynamic_menu() {

		$settings = $this->get_settings_for_display();
		$id       = 'bdt-metismenu-' . $this->get_id();

		if (!$settings['navbar']) {
			element_pack_alert(__('Please select a Menu From Setting!', 'bdthemes-element-pack'));
		}

		$nav_menu = !empty($settings['navbar']) ? wp_get_nav_menu_object($settings['navbar']) : false;

		if (!$nav_menu) {
			return;
		}

		$nav_menu_args = array(
			'fallback_cb'    => false,
			'container'      => false,
			'menu_id'        => $id,
			'menu_class'     => 'metismenu',
			'theme_location' => 'default_navmenu', // creating a fake location for better functional control
			'menu'           => $nav_menu,
			'echo'           => true,
			'depth'          => 0,
			'walker'         => new ep_vertical_menu_walker
		);

	?>

		<nav class="sidebar-nav">
			<?php wp_nav_menu(apply_filters('widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings)); ?>
		</nav>

<?php
	}
}
