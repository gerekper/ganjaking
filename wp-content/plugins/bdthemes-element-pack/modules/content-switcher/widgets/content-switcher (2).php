<?php

namespace ElementPack\Modules\ContentSwitcher\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;
use Elementor\Repeater;

use ElementPack\Element_Pack_Loader;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Content_Switcher extends Module_Base {

	public function get_name() {
		return 'bdt-content-switcher';
	}

	public function get_title() {
		return BDTEP . esc_html__('Content Switcher', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-content-switcher';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['switcher', 'tab', 'toggle', 'content', 'switch', 'switcher', 'content switcher', 'element pack'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-content-switcher'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			// return ['ep-scripts'];
			return ['ep-content-switcher'];
		} else {
			return ['ep-content-switcher'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/4NjUGf9EY0U';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_switcher_layout',
			[
				'label' => esc_html__('Switcher', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switcher_style',
			[
				'label'   => esc_html__('Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'      => '01',
					'2'      => '02',
					'3'      => '03',
					'4'      => '04',
					'5'      => '05',
					'6'      => '06',
					'7'      => '07',
					'8'      => '08',
					'9'      => '09',
					'button' => 'Button',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Switcher Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label'   => esc_html__('Content Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content' => esc_html__('Content', 'bdthemes-element-pack'),
					'template' => esc_html__('Saved Templates', 'bdthemes-element-pack'),
				],
			]
		);

		$repeater->add_control(
			'content',
			[
				'label'       => esc_html__('Content', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => esc_html__('Switcher Content', 'bdthemes-element-pack'),
				'label_block' => true,
				'condition'   => [
					'content_type' => 'content',
				],
			]
		);

		$repeater->add_control(
			'saved_templates',
			[
				'label'       => esc_html__('Choose Template', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select template', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'elementor_template',
				],
				'condition'   => [
					'content_type' => 'template',
				],
			]
		);

		$repeater->add_control(
			'switcher_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
			]
		);

		$repeater->add_control(
			'switcher_active',
			[
				'label'        => esc_html__('Active', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'bdthemes-element-pack'),
				'label_off'    => esc_html__('No', 'bdthemes-element-pack'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'switcher_items',
			[
				'label'   => esc_html__('Switcher Items', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::REPEATER,
				'default' => [
					[
						'content_type'    => 'content',
						'title'           => esc_html__('Primary', 'bdthemes-element-pack'),
						'content'         => esc_html__('Switcher Content Primary', 'bdthemes-element-pack'),
						'switcher_active' => 'yes',
					],
					[
						'content_type' => 'content',
						'title'        => esc_html__('Secondary', 'bdthemes-element-pack'),
						'content'      => esc_html__('Switcher Content Secondary', 'bdthemes-element-pack'),
					],
					[
						'content_type' => 'content',
						'title'        => esc_html__('Others', 'bdthemes-element-pack'),
						'content'      => esc_html__('Switcher Content Others', 'bdthemes-element-pack'),
					],
				],
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_switcher_additional_options',
			[
				'label' => esc_html__('Additional Options', 'bdthemes-element-pack'),
			]
		);

		//text align
		$this->add_responsive_control(
			'content_switcher_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge',
			[
				'label' => __('Badge', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'badge_text',
			[
				'label' => __('Badge Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Hot',
				'placeholder' => 'Type Step Here',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		//badge left right align
		$this->add_control(
			'badge_align',
			[
				'label'   => esc_html__('Badge Align', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'badge' => 'yes',
				],
				'selectors_dictionary' => [
					'left' => 'left: 0;',
					'right' => 'right: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher-badge' => '{{VALUE}};',
				],
				'toggle' => false,
				'default' => 'left',
				'render_type' => 'template',
			]
		);

		//arrows style select	
		$this->add_control(
			'arrows_style',
			[
				'label'   => esc_html__('Arrow Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'       => '01',
					'2'       => '02',
					'3'       => '03',
					'4'       => '04',
				],
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label' => __('Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('None', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->start_popover();

		$this->start_controls_tabs('tabs_offset_badge_controls');

		$this->start_controls_tab(
			'tab_offset_badge_controls',
			[
				'label' => __('Badge', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'badge_horizontal_offset',
			[
				'label' => __('Badge Horizontal Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
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
						'min' => -300,
						'step' => 1,
						'max' => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-badge-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_vertical_offset',
			[
				'label' => __('Badge Vertical Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -40,
				],
				'tablet_default' => [
					'size' => -40,
				],
				'mobile_default' => [
					'size' => -40,
				],
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 1,
						'max' => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-badge-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_rotate',
			[
				'label' => esc_html__('Badge Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
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
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-badge-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_offset_arrows_controls',
			[
				'label' => __('Arrow', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'arrows_horizontal_offset_left',
			[
				'label' => __('Arrow Horizontal Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -35,
				],
				'tablet_default' => [
					'size' => -35,
				],
				'mobile_default' => [
					'size' => -35,
				],
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 1,
						'max' => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
					'badge_align' => 'left',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-arrows-h-offset-left: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_horizontal_offset_right',
			[
				'label' => __('Arrow Horizontal Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 1,
						'max' => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
					'badge_align' => 'right',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-arrows-h-offset-right: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_vertical_offset',
			[
				'label' => __('Arrow Vertical Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -26,
				],
				'tablet_default' => [
					'size' => -26,
				],
				'mobile_default' => [
					'size' => -26,
				],
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 1,
						'max' => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-arrows-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_rotate',
			[
				'label' => esc_html__('Arrow Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
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
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes',
					'badge' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-content-switcher-arrows-rotate: {{SIZE}}deg;'
				],
			]
		);


		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_popover();

		$this->end_controls_section();

		// switcher style

		$this->start_controls_section(
			'section_style_switch',
			[
				'label' => esc_html__('Switch', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//spacing
		$this->add_responsive_control(
			'switch_spacing',
			[
				'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'step' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-switch-container-wrap' => 'gap: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'switch_icon_spacing',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'step' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text, {{WRAPPER}} .bdt-content-switcher-tab' => 'gap: {{SIZE}}px;',
				],
			]
		);

		//typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'switch_typography',
				'selector'  => '{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text, {{WRAPPER}} .bdt-content-switcher-icon, {{WRAPPER}} .bdt-content-switcher-tab',
			]
		);

		$this->start_controls_tabs('tabs_switch_style');

		$this->start_controls_tab(
			'tab_switch_style_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		//switch color
		$this->add_control(
			'switch_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text, {{WRAPPER}} .bdt-content-switcher-icon i, {{WRAPPER}} .bdt-content-switcher-tab' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-content-switcher-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		//button style start
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'switcher_button_background',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-tab',
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'switcher_button_border',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-tab',
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-content-switcher-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_button_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-content-switcher-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		// text shadow
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'switch_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text, {{WRAPPER}} .bdt-content-switcher-tab',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switcher_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-tab',
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		// text stroke
		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'switch_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text, {{WRAPPER}} .bdt-content-switcher-tab',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_switch_style_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switch_active_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text.bdt-active, {{WRAPPER}} .bdt-content-switcher-icon.bdt-active i, {{WRAPPER}} .bdt-content-switcher-tab.bdt-active .bdt-content-switcher-icon i, {{WRAPPER}} .bdt-content-switcher-tab.bdt-active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-content-switcher-icon.bdt-active svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'switcher_button_active_background',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-tab.bdt-active',
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		$this->add_control(
			'switcher_button_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'ultimate-post-kit'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'switcher_style' => 'button',
					'switcher_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher-tab.bdt-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'switch_active_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text.bdt-active, {{WRAPPER}} .bdt-content-switcher-tab.bdt-active',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switcher_button_shadow_active',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-tab.bdt-active',
				'condition' => [
					'switcher_style' => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'switch_active_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap .bdt-package-text, {{WRAPPER}} .bdt-content-switcher-tab.bdt-active',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_switcher',
			[
				'label' => esc_html__('Switcher', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'switcher_style!' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 60,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher .button' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'switcher_style!' => '9',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher .button' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'switcher_style!' => '9',
				],
			]
		);

		//border radius
		$this->add_responsive_control(
			'switcher_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher .bdt-layer, {{WRAPPER}} .bdt-content-switcher .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		//margin
		$this->add_responsive_control(
			'switcher_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// alignment
		// $this->add_responsive_control(
		// 	'switcher_alignment',
		// 	[
		// 		'label' => esc_html__('Alignment', 'bdthemes-element-pack'),
		// 		'type' => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'flex-start'    => [
		// 				'title' => esc_html__('Left', 'bdthemes-element-pack'),
		// 				'icon' => 'eicon-h-align-left',
		// 			],
		// 			'center' => [
		// 				'title' => esc_html__('Center', 'bdthemes-element-pack'),
		// 				'icon' => 'eicon-h-align-center',
		// 			],
		// 			'flex-end' => [
		// 				'title' => esc_html__('Right', 'bdthemes-element-pack'),
		// 				'icon' => 'eicon-h-align-right',
		// 			],
		// 			'space-between' => [
		// 				'title' => esc_html__('Justified', 'bdthemes-element-pack'),
		// 				'icon' => 'eicon-h-align-stretch',
		// 			],
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-switch-container-wrap' => 'justify-content: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->start_controls_tabs('tabs_switcher_style');

		$this->start_controls_tab(
			'tab_switcher_style_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switcher_knob_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-1 .bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-2 .bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-2 .bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-3 .bdt-knobs::before, {{WRAPPER}} .bdt-toggle-button-4 .bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-4 .bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-5 .bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-6 .bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-7 .bdt-knobs span, {{WRAPPER}} .bdt-toggle-button-8 .bdt-knobs span, {{WRAPPER}} .bdt-toggle-button-9 .bdt-knobs span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'switcher_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher .bdt-layer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_switcher_style_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'switcher_knob_checked_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-1 .checkbox:checked+.bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-2 .checkbox:checked+.bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-2 .checkbox:checked+.bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-3 .checkbox:checked+.bdt-knobs::before, {{WRAPPER}} .bdt-toggle-button-4 .checkbox:checked+.bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-4 .checkbox:checked+.bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-5 .checkbox:checked+.bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-6 .checkbox:checked+.bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-7 .bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-8 .bdt-knobs:after, {{WRAPPER}} .bdt-toggle-button-9 .checkbox:checked+.bdt-knobs span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'switcher_checked_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-1 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-2 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-3 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-4 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-5 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-6 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-7 .checkbox:checked~.bdt-layer, {{WRAPPER}} .bdt-toggle-button-7 .bdt-knobs:before, {{WRAPPER}} .bdt-toggle-button-8 .checkbox:checked+.bdt-knobs span, {{WRAPPER}} .bdt-toggle-button-9 .checkbox:checked~.bdt-layer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'switcher_checked_knob_color',
			[
				'label' => esc_html__('Knob Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-toggle-button-7 .checkbox:checked+.bdt-knobs span' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'switcher_style' => '7',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_switcher_bar',
			[
				'label' => esc_html__('Switcher Bar', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'switcher_bar_background',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'switcher_bar_border',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap',
			]
		);

		$this->add_responsive_control(
			'switcher_bar_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-switch-container-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_bar_padding',
			[
				'label'      => esc_html__('Padding', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-switch-container-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_bar_margin',
			[
				'label'      => esc_html__('Margin', 'ultimate-post-kit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-switch-container-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'switcher_bar_shadow',
				'selector' => '{{WRAPPER}} .bdt-switch-container-wrap',
			]
		);

		$this->end_controls_section();

		// switcher content style
		$this->start_controls_section(
			'section_switcher_content_style',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//text color
		$this->add_control(
			'switcher_content_text_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-switcher-content' => 'color: {{VALUE}};',
				],
			]
		);

		//background type
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'switcher_content_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-switcher-content',
			]
		);

		//border
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'switcher_content_border',
				'label'     => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-switcher-content',
				'separator' => 'before',
			]
		);

		//border radius
		$this->add_responsive_control(
			'switcher_content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-switcher-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-switcher-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'switcher_content_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-switcher-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'switcher_content_box_shadow',
				'selector'  => '{{WRAPPER}} .bdt-switcher-content',
			]
		);

		// $this->add_responsive_control(
		// 	'switcher_content_align',
		// 	[
		// 		'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
		// 		'type'    => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'left'    => [
		// 				'title' => esc_html__('Left', 'bdthemes-element-pack'),
		// 				'icon'  => 'fa fa-align-left',
		// 			],
		// 			'center' => [
		// 				'title' => esc_html__('Center', 'bdthemes-element-pack'),
		// 				'icon'  => 'fa fa-align-center',
		// 			],
		// 			'right' => [
		// 				'title' => esc_html__('Right', 'bdthemes-element-pack'),
		// 				'icon'  => 'fa fa-align-right',
		// 			],
		// 			'justify' => [
		// 				'title' => esc_html__('Justified', 'bdthemes-element-pack'),
		// 				'icon'  => 'fa fa-align-justify',
		// 			],
		// 		],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .bdt-switcher-content' => 'text-align: {{VALUE}};',
		// 		],
		// 		'separator' => 'before',
		// 	]
		// );

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label' => __('Badge', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label' => __('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_arrows_color',
			[
				'label' => __('Arrow Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-switcher-arrows svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_background_color',
			[
				'label' => __('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher-badge' => 'background: {{VALUE}};',
					'{{WRAPPER}} .bdt-content-switcher-badge:before' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'placeholder' => '1px',
				'separator' => 'before',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-badge',
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-content-switcher-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-badge',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'selector' => '{{WRAPPER}} .bdt-content-switcher-badge',
			]
		);

		//arrow size
		$this->add_responsive_control(
			'badge_arrows_size',
			[
				'label' => __('Arrow Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-switcher-arrows' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

	}

	public function render_badge(){
		$settings = $this->get_settings_for_display();

		if ( 'yes' !== $settings['badge'] ) {
			return;
		}

		?>
		<?php if ($settings['badge'] and '' != $settings['badge_text']) : ?>
			<?php if($settings['badge_align'] == 'left') : ?>
			<div class="bdt-switcher-arrows bdt-arrows-left">
				<?php echo element_pack_svg_icon('left-badge-arrows-'.$settings['arrows_style']);?>
			</div>
			<?php endif; ?>

			<?php if($settings['badge_align'] == 'right') : ?>
			<div class="bdt-switcher-arrows bdt-arrows-right">
				<?php echo element_pack_svg_icon('right-badge-arrows-'.$settings['arrows_style']);?>
			</div>
			<?php endif; ?>

			<div class="bdt-content-switcher-badge">
				<?php echo esc_html($settings['badge_text']); ?>
			</div>
		<?php endif; ?>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$primary   = (isset($settings['switcher_items'][0]) ? $settings['switcher_items'][0] : '');
		$secondary = (isset($settings['switcher_items'][1]) ? $settings['switcher_items'][1] : '');

		$this->add_render_attribute('content-switcher', 'class', 'bdt-content-switcher');
		$this->add_render_attribute('content-switcher', [
			'data-settings' => [
				wp_json_encode(array_filter([
					'id' => '#bdt-content-switcher-' . $this->get_id(),
					'switcherStyle' => $settings['switcher_style'],
				]))
			]
		]);

		?>

		<div <?php $this->print_render_attribute_string('content-switcher'); ?>>
			<div class="bdt-switch-container-wrap">

				<?php if ('button' !== $settings['switcher_style']) : ?>

					<?php $this->render_badge(); ?>

					<?php if (!empty($primary['title']) or !empty($primary['switcher_icon']['value'])) : ?>
					<div class="bdt-package-text bdt-primary-text <?php echo esc_attr(($primary['switcher_active'] == 'yes') ? 'bdt-active' : '') ?>">

						<?php if (!empty($primary['switcher_icon']['value'])) : ?>
						<span class="bdt-content-switcher-icon bdt-primary-icon <?php echo esc_attr(($primary['switcher_active'] == 'yes') ? 'bdt-active' : ''); ?>">
							<?php Icons_Manager::render_icon($primary['switcher_icon'], ['aria-hidden' => 'true']); ?>
						</span>
						<?php endif; ?>

						<?php if (!empty($primary['title'])) : ?>
							<?php echo esc_html($primary['title']); ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>


					

					<div class="bdt-switch-container button bdt-toggle-button-<?php esc_html_e($settings['switcher_style']); ?>">
						<input type="checkbox" class="checkbox" <?php echo esc_attr(($secondary['switcher_active'] == 'yes') ? 'checked' : ''); ?>>
						<div class="bdt-knobs">
							<span></span>
						</div>
						<div class="bdt-layer"></div>
					</div>

					<?php if (!empty($secondary['title']) or !empty($secondary['switcher_icon']['value'])) : ?>
					<div class="bdt-package-text bdt-secondary-text <?php echo esc_attr(($secondary['switcher_active'] == 'yes') ? 'bdt-active' : '') ?>">

						<?php if (!empty($secondary['switcher_icon']['value'])) : ?>
						<span class="bdt-content-switcher-icon bdt-secondary-icon <?php echo esc_attr(($secondary['switcher_active'] == 'yes') ? 'bdt-active' : ''); ?>">
							<?php Icons_Manager::render_icon($secondary['switcher_icon'], ['aria-hidden' => 'true']); ?>
						</span>
						<?php endif; ?>

						<?php if (!empty($secondary['title'])) : ?>
							<?php echo esc_html($secondary['title']); ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ('button' == $settings['switcher_style']) :
					foreach ($settings['switcher_items'] as $item) :
						$this->add_render_attribute('button', 'class', esc_attr(($item['switcher_active'] == 'yes') ? 'bdt-content-switcher-tab bdt-active' : 'bdt-content-switcher-tab'), true);

						if (!empty($item['_id'])) {
							$this->add_render_attribute('button', 'id', $this->get_id() . esc_attr($item['_id']), true);
						}

						?>
						
						<a href="javascript:void(0);" <?php echo $this->get_render_attribute_string('button'); ?>>
							<?php if (!empty($item['switcher_icon']['value'])) : ?>
								<span class="bdt-content-switcher-icon bdt-item-icon">
									<?php Icons_Manager::render_icon($item['switcher_icon'], ['aria-hidden' => 'true']); ?>
								</span>
							<?php endif; ?>
							<?php echo esc_html($item['title']); ?>
						</a>
					<?php endforeach; ?>

					<?php $this->render_badge(); ?>

				<?php endif; ?>

			</div>

			<!-- Content Switcher Content -->
			<div class="bdt-switcher-content-wrapper">

				<?php if ('button' !== $settings['switcher_style']) : ?>

					<div class="bdt-switcher-content bdt-primary <?php echo esc_attr(($primary['switcher_active'] == 'yes') ? 'bdt-active' : ''); ?>">
						<?php
						if ($primary['content_type'] == 'content') {
							echo wp_kses_post($primary['content']);
						} else {
							echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($primary['saved_templates']);
						}
						?>
					</div>
					<div class="bdt-switcher-content bdt-secondary <?php echo esc_attr(($secondary['switcher_active'] == 'yes') ? 'bdt-active' : ''); ?>">
						<?php
						if ($secondary['content_type'] == 'content') {
							echo wp_kses_post($secondary['content']);
						} else {
							echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($secondary['saved_templates']);
						}
						?>
					</div>
			

				<?php endif; ?>

				<?php if ('button' == $settings['switcher_style']) :
					foreach ($settings['switcher_items'] as $item) :
						$this->add_render_attribute('switcher_content', 'class', esc_attr(($item['switcher_active'] == 'yes') ? 'bdt-switcher-content bdt-active' : 'bdt-switcher-content'), true);

						if (!empty($item['_id'])) {
							$this->add_render_attribute('switcher_content', 'data-content-id', $this->get_id() . esc_attr($item['_id']), true);
						}
						?>

						<div <?php echo $this->get_render_attribute_string('switcher_content'); ?>>
							<?php
							if ($item['content_type'] == 'content') {
								echo wp_kses_post($item['content']);
							} else {
								echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($item['saved_templates']);
							}
							?>
						</div>

					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
