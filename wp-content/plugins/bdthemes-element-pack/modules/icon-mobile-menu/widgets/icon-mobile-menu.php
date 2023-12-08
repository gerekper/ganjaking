<?php

namespace ElementPack\Modules\IconMobileMenu\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Utils;

use ElementPack\Traits\Global_Mask_Controls;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Icon_Mobile_Menu extends Module_Base {

	use Global_Mask_Controls;

	public function get_name() {
		return 'bdt-icon-mobile-menu';
	}

	public function get_title() {
		return BDTEP . esc_html__('Icon Mobile Menu', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-icon-mobile-menu';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['icon', 'mobile', 'menu', 'nav', 'navbar'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-icon-mobile-menu', 'tippy'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['popper', 'tippyjs', 'ep-scripts'];
		} else {
			return ['popper', 'tippyjs', 'ep-icon-mobile-menu'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/lJxkFDzrDeY';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ep_section_menu',
			[
				'label' => __('Menu Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu_style',
			[
				'label'   => __('Menu Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => '01',
					'style-2' => '02',
					'style-3' => '03',
					'style-4' => '04',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'menu_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',

			]
		);

		$repeater->add_control(
			'menu_text',
			[
				'label'   => __('Menu Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __('Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default'     => ['url' => '#'],
				'description' => __('Add your section id WITH the # key. e.g: #my-id also you can add internal/external URL', 'bdthemes-element-pack'),
				'label_block' => true,
				'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'menu_items',
			[
				'show_label'  => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'menu_text' => __('Home', 'bdthemes-element-pack'),
						'menu_icon'  => ['value' => 'fas fa-home', 'library' => 'fa-solid'],
					],
					[
						'menu_text' => __('Cart', 'bdthemes-element-pack'),
						'menu_icon'  => ['value' => 'fas fa-shopping-cart', 'library' => 'fa-solid'],
					],
					[
						'menu_text' => __('Account', 'bdthemes-element-pack'),
						'menu_icon'  => ['value' => 'fas fa-user', 'library' => 'fa-solid'],
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, menu_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} {{{ menu_text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltip_settings',
			[
				'label' => __('Tooltip Settings', 'bdthemes-element-pack'),
				'condition' => [
					'menu_style' => ['style-3', 'style-4'],
				],
			]
		);

		$this->add_control(
			'menu_tooltip',
			[
				'label'              => __('Tooltip', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'menu_tooltip_placement',
			[
				'label'     => esc_html__('Placement', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => [
					'top-start'    => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top'          => esc_html__('Top', 'bdthemes-element-pack'),
					'top-end'      => esc_html__('Top Right', 'bdthemes-element-pack'),
					'bottom-start' => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom'       => esc_html__('Bottom', 'bdthemes-element-pack'),
					'bottom-end'   => esc_html__('Bottom Right', 'bdthemes-element-pack'),
					'left'         => esc_html__('Left', 'bdthemes-element-pack'),
					'right'        => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'menu_tooltip' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'menu_tooltip_animation',
			[
				'label'   => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shift-toward',
				'options' => [
					'shift-away'   => esc_html__('Shift-Away', 'bdthemes-element-pack'),
					'shift-toward' => esc_html__('Shift-Toward', 'bdthemes-element-pack'),
					'fade'         => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale'        => esc_html__('Scale', 'bdthemes-element-pack'),
					'perspective'  => esc_html__('Perspective', 'bdthemes-element-pack'),
				],
				'condition' => [
					'menu_tooltip' => 'yes',
				],
			]
		);

		$this->add_control(
			'menu_tooltip_x_offset',
			[
				'label'   => esc_html__('X Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'menu_tooltip' => 'yes',
				],
			]
		);

		$this->add_control(
			'menu_tooltip_y_offset',
			[
				'label'   => esc_html__('Y Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'menu_tooltip' => 'yes',
				],
			]
		);

		$this->add_control(
			'menu_tooltip_arrow',
			[
				'label' => esc_html__('Arrow', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'menu_tooltip' => 'yes',
				],
			]
		);

		$this->add_control(
			'menu_tooltip_trigger',
			[
				'label'       => __('Trigger on Click', 'bdthemes-element-pack'),
				'description' => __('Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'condition' => [
					'menu_tooltip' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'ep_section_style_menu',
			[
				'label' => __('Menu Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'selector'  => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link',
			]
		);

		$this->add_control(
			'item_border_type',
			[
				'label' => esc_html_x('Border Type', 'Border Control', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__('Default', 'elementor'),
					'none' => esc_html__('None', 'elementor'),
					'solid' => esc_html_x('Solid', 'Border Control', 'elementor'),
					'double' => esc_html_x('Double', 'Border Control', 'elementor'),
					'dotted' => esc_html_x('Dotted', 'Border Control', 'elementor'),
					'dashed' => esc_html_x('Dashed', 'Border Control', 'elementor'),
					'groove' => esc_html_x('Groove', 'Border Control', 'elementor'),
				],
				'selectors' => [
					'{{SELECTOR}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'menu_style' => 'style-2',
				],
			]
		);

		$this->add_responsive_control(
			'item_border_width',
			[
				'label'      => __('Border Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}}' => '--ep-border-width: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'item_border_type!' => ['none'],
					'menu_style' => 'style-2',
				],
			]
		);

		$this->add_control(
			'item_border_color',
			[
				'label'      => __('Border Color', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link' => 'border-color: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'item_border_type!' => ['none'],
					'menu_style' => 'style-2',
				],
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		//margin
		$this->add_responsive_control(
			'item_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover',
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_type!' => ['none'],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ep_section_style_menu_icon',
			[
				'label' => __('Menu Icon', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_icon_style');

		$this->start_controls_tab(
			'tab_item_icon_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_icon_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_icon_background',
				'selector'  => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_icon_border',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_icon_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_icon_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu',
			]
		);

		$this->add_responsive_control(
			'item_icon_size',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-icon-mobile-menu' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_icon_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_icon_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-icon-mobile-menu' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-icon-mobile-menu svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-icon-mobile-menu',
			]
		);

		$this->add_control(
			'item_icon_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_icon_border_border!' => ['none'],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-icon-mobile-menu' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-icon-mobile-menu',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ep_section_style_menu_text',
			[
				'label' => __('Menu Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_text_style');

		$this->start_controls_tab(
			'tab_item_text_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_text_background',
				'selector'  => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_text_border',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_text_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_text_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu',
			]
		);

		//typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_text_typography',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap span.bdt-text-mobile-menu',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_text_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_text_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-text-mobile-menu' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_text_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-text-mobile-menu',
			]
		);

		$this->add_control(
			'item_text_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_text_border_border!' => ['none'],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-text-mobile-menu' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_text_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-mobile-menu-wrap .bdt-icon-mobile-menu-link:hover span.bdt-text-mobile-menu',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//tooltip style
		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => esc_html__('Tooltip', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_style' => ['style-3', 'style-4'],
					'menu_tooltip' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'menu_tooltip_width',
			[
				'label'       => esc_html__('Width', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [
					'px',
					'em',
				],
				'range'       => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'   => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'max-width: calc({{SIZE}}{{UNIT}} - 10px) !important;',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'menu_tooltip_title_color',
			[
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .bdt-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'menu_tooltip_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'menu_tooltip_text_align',
			[
				'label'     => esc_html__('Text Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'menu_tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_control(
			'menu_tooltip_arrow_color',
			[
				'label'     => esc_html__('Arrow Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'menu_tooltip_padding',
			[
				'label'       => __('Padding', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => ['px', '%'],
				'selectors'   => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'menu_tooltip_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'menu_tooltip_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'menu_tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('icon-mobile-menu', 'class', 'bdt-icon-mobile-menu-wrap bdt-icon-mobile-menu-' . $settings['menu_style']);

		if (empty($settings['menu_items'])) {
			return;
		}

?>

		<div <?php $this->print_render_attribute_string('icon-mobile-menu'); ?>>
			<ul>
				<?php
				foreach ($settings['menu_items'] as $index => $item) :
					$repeater_key    = 'menu_item' . $index;
					$tag             = 'div ';
					$tooltip_content = '<span class="bdt-title">' . $item['menu_text'] . '</span>';
					$this->add_render_attribute($repeater_key, 'class', 'bdt-icon-mobile-menu-link', true);
					$this->add_render_attribute($repeater_key, 'data-tippy-content', $tooltip_content, true);

					if ($item['link']['url']) {
						$tag = 'a ';
						$this->add_render_attribute($repeater_key, 'class', 'bdt-icon-mobile-menu-link', true);

						$this->add_link_attributes($repeater_key, $item['link']);
					}

					if ($item['menu_text'] and $settings['menu_tooltip']) {
						// Tooltip settings
						$this->add_render_attribute($repeater_key, 'class', 'bdt-tippy-tooltip');
						$this->add_render_attribute($repeater_key, 'data-tippy', '', true);

						if ($settings['menu_tooltip_placement']) {
							$this->add_render_attribute($repeater_key, 'data-tippy-placement', $settings['menu_tooltip_placement'], true);
						}

						if ($settings['menu_tooltip_animation']) {
							$this->add_render_attribute($repeater_key, 'data-tippy-animation', $settings['menu_tooltip_animation'], true);
						}

						if ($settings['menu_tooltip_x_offset']['size'] or $settings['menu_tooltip_y_offset']['size']) {
							$this->add_render_attribute($repeater_key, 'data-tippy-offset', '[' . $settings['menu_tooltip_x_offset']['size'] . ',' . $settings['menu_tooltip_y_offset']['size'] . ']', true);
						}

						if ('yes' == $settings['menu_tooltip_arrow']) {
							$this->add_render_attribute($repeater_key, 'data-tippy-arrow', 'true', true);
						} else {
							$this->add_render_attribute($repeater_key, 'data-tippy-arrow', 'false', true);
						}

						if ('yes' == $settings['menu_tooltip_trigger']) {
							$this->add_render_attribute($repeater_key, 'data-tippy-trigger', 'click', true);
						}
					}

				?>
					<li class="bdt-icon-mobile-menu-list">
						<<?php echo esc_attr($tag) . ' '; ?> <?php $this->print_render_attribute_string($repeater_key); ?>>

							<?php if (!empty($item['menu_icon']['value'])) : ?>
								<span class="bdt-icon-mobile-menu">
									<?php Icons_Manager::render_icon($item['menu_icon'], ['aria-hidden' => 'true']); ?>
								</span>
							<?php endif; ?>

							<span class="bdt-text-mobile-menu"><?php echo wp_kses_post($item['menu_text']); ?></span>

						</<?php echo esc_attr($tag); ?>>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

<?php
	}
}
