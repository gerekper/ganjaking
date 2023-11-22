<?php

namespace ElementPack\Modules\Mailchimp\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Mailchimp extends Module_Base {

	public function get_name() {
		return 'bdt-mailchimp';
	}

	public function get_title() {
		return BDTEP . esc_html__('Mailchimp', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-mailchimp';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['mailchimp', 'email', 'marketing', 'newsletter'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-mailchimp'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-mailchimp'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/AVqliwiyMLg';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_before_icon',
			[
				'label' => esc_html__('Show Before Icon', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'before_icon_inline',
			[
				'label' => esc_html__('Inline Before Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-before-icon-inline--',
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'mailchimp_before_icon',
			[
				'label'       => __('Choose Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'before_icon',
				'default' => [
					'value' => 'far fa-envelope-open',
					'library' => 'fa-regular',
				],
				'condition'   => [
					'show_before_icon' => 'yes'
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'before_text',
			[
				'label'       => esc_html__('Before Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('Before Text', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'email_field_placeholder',
			[
				'label'       => esc_html__('Email Field Placeholder', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'default'     => esc_html__('Email *', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Email *', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'after_text',
			[
				'label'       => esc_html__('After Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('After Text', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __('Alignment', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
				'options'      => [
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
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'before_text',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'after_text',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'space',
			[
				'label'   => __('Space Between', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''         => __('Default', 'bdthemes-element-pack'),
					'small'    => __('Small', 'bdthemes-element-pack'),
					'medium'   => __('Medium', 'bdthemes-element-pack'),
					'large'    => __('Large', 'bdthemes-element-pack'),
					'collapse' => __('Collapse', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'show_fname',
			[
				'label' => esc_html__('Show Name', 'ultimate-post-kit'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'fname_field_placeholder',
			[
				'label'       => esc_html__('Name Field Placeholder', 'ultimate-post-kit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'default'     => esc_html__('Name ', 'ultimate-post-kit'),
				'placeholder' => esc_html__('Name ', 'ultimate-post-kit'),
				'condition'	=> [
					'show_fname' => 'yes',
				]
			]
		);


		$this->add_control(
			'fullwidth_input',
			[
				'label' => esc_html__('Fullwidth Fields', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-fullwidth--',
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'fullwidth_button',
			[
				'label'     => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-signup-wrapper' => 'width: 100%;',
				],
				'condition' => [
					'fullwidth_input' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'button_alignment',
			[
				'label'   => __('Button Alignment', 'bdthemes-element-pack') . BDTEP_NC,
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
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-mailchimp' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'fullwidth_input' => 'yes',
					'fullwidth_button' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button',
			[
				'label' => esc_html__('Signup Button', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => esc_html__('SIGNUP', 'bdthemes-element-pack'),
				'default'     => esc_html__('SIGNUP', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'mailchimp_button_icon',
			[
				'label'       => __('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => __('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => __('Left', 'bdthemes-element-pack'),
					'right'  => __('Right', 'bdthemes-element-pack'),
					'top'    => __('Top', 'bdthemes-element-pack'),
					'bottom' => __('Bottom', 'bdthemes-element-pack'),
				],
				'condition' => [
					'mailchimp_button_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => __('Icon Spacing', 'bdthemes-element-pack'),
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
					'mailchimp_button_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-flex-align-right'  => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-flex-align-left'   => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-flex-align-top'    => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-flex-align-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_before_icon',
			[
				'label'     => __('Before Icon', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_before_icon' => 'yes',
					'mailchimp_before_icon[value]!'     => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_before_icon_style');

		$this->start_controls_tab(
			'tab_before_icon_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'before_icon_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-before-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-newsletter-before-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'before_icon_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-newsletter-before-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'before_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-newsletter-before-icon',
			]
		);

		$this->add_responsive_control(
			'before_icon_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-before-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_icon_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-before-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_icon_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-before-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'before_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-newsletter-before-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'before_icon_typography',
				'selector'  => '{{WRAPPER}} .bdt-newsletter-before-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_before_icon_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'before_icon_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-before-icon:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-newsletter-before-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'before_icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-newsletter-before-icon:hover',
			]
		);

		$this->add_control(
			'before_icon_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'before_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-before-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Field', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper input[type*="email"]::placeholder, {{WRAPPER}} .bdt-newsletter-wrapper input[type*="text"]::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'placeholder_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-input',
			]
		);

		$this->add_control(
			'input_border_show',
			[
				'label'     => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'input_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-input',
				'condition' => [
					'input_border_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'input_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__('Sign Up Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary',
			]
		);

		$this->add_responsive_control(
			'radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-button.bdt-button-primary:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __('Hover Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label'     => __('Signup Button Icon', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'mailchimp_button_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_signup_btn_icon_style');

		$this->start_controls_tab(
			'tab_signup_btn_icon_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'signup_btn_icon_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'signup_btn_icon_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'signup_btn_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon',
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'signup_btn_icon_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'signup_btn_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'signup_btn_icon_typography',
				'selector'  => '{{WRAPPER}} .bdt-newsletter-btn .bdt-newsletter-btn-icon',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_signup_btn_icon_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'signup_btn_icon_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-btn:hover .bdt-newsletter-btn-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-newsletter-btn:hover .bdt-newsletter-btn-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'signup_btn_icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-newsletter-btn:hover .bdt-newsletter-btn-icon',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'signup_btn_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-btn:hover .bdt-newsletter-btn-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_before_text',
			[
				'label'     => __('Before Text', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'before_text!' => '',
				],
			]
		);

		$this->add_control(
			'before_text_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-before-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'before_text_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-before-text'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'before_text_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-before-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_after_text',
			[
				'label'     => __('After Text', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'after_text!' => '',
				],
			]
		);

		$this->add_control(
			'after_text_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-after-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'after_text_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-after-text'   => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'after_text_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-newsletter-wrapper .bdt-newsletter-after-text',
			]
		);

		$this->end_controls_section();
	}

	public function render_text($settings) {

		$this->add_render_attribute('content-wrapper', 'class', 'bdt-newsletter-btn-content-wrapper');

		if ('left' == $settings['icon_align'] or 'right' == $settings['icon_align']) {
			$this->add_render_attribute('content-wrapper', 'class', 'bdt-flex bdt-flex-middle bdt-flex-center');
		}

		$this->add_render_attribute('content-wrapper', 'class', ('top' == $settings['icon_align']) ? 'bdt-flex bdt-flex-column bdt-flex-center' : '');
		$this->add_render_attribute('content-wrapper', 'class', ('bottom' == $settings['icon_align']) ? 'bdt-flex bdt-flex-column-reverse bdt-flex-center' : '');

		$this->add_render_attribute('icon-align', 'class', 'elementor-align-icon-' . $settings['icon_align']);
		$this->add_render_attribute('icon-align', 'class', 'bdt-newsletter-btn-icon');

		$this->add_render_attribute('text', 'class', ['bdt-newsletter-btn-text', 'bdt-display-inline-block']);
		$this->add_inline_editing_attributes('text', 'none');

		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset($settings['__fa4_migrated']['mailchimp_button_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

?>
		<div <?php echo $this->get_render_attribute_string('content-wrapper'); ?>>
			<?php if (!empty($settings['mailchimp_button_icon']['value'])) : ?>
				<div class="bdt-newsletter-btn-icon bdt-flex-align-<?php echo esc_attr($settings['icon_align']); ?>">

					<?php if ($is_new || $migrated) :
						Icons_Manager::render_icon($settings['mailchimp_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
					else : ?>
						<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</div>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string('text'); ?>><?php echo wp_kses($settings['button_text'], element_pack_allow_tags('title')); ?></div>
		</div>
	<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-mailchimp-' . $this->get_id();

		$space = ('' !== $settings['space']) ? ' bdt-grid-' . $settings['space'] : '';

		if ($settings['button_text']) {
			$button_text = $settings['button_text'];
		} else {
			$button_text = esc_html__('Subscribe', 'bdthemes-element-pack');
		}

		$this->add_render_attribute('input-wrapper', 'class', 'bdt-newsletter-input-wrapper');

		if ($settings['fullwidth_input']) {
			$this->add_render_attribute('input-wrapper', 'class', 'bdt-width-1-1');
		} else {
			$this->add_render_attribute('input-wrapper', 'class', 'bdt-width-expand');
		}

		if (!isset($settings['before_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['before_icon'] = 'fas fa-envelope-open';
		}

		$migrated  = isset($settings['__fa4_migrated']['mailchimp_before_icon']);
		$is_new    = empty($settings['before_icon']) && Icons_Manager::is_migration_allowed();

		$form_id  = !empty($settings['_element_id']) ? 'bdt-sf-' . $settings['_element_id'] :  'bdt-sf-' . $id;

	?>
		<div class="bdt-newsletter-wrapper">

			<?php if (!empty($settings['before_text'])) : ?>
				<div class="bdt-newsletter-before-text"><?php echo esc_attr($settings['before_text']); ?></div>
			<?php endif; ?>

			<form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" class="bdt-mailchimp bdt-grid<?php echo esc_attr($space); ?> bdt-flex-middle" bdt-grid>

				<?php if ($settings['show_before_icon'] and !empty($settings['mailchimp_before_icon']['value'])) : ?>
					<div class="bdt-width-auto bdt-before-icon">
						<div class="bdt-newsletter-before-icon">

							<?php if ($is_new || $migrated) :
								Icons_Manager::render_icon($settings['mailchimp_before_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
							else : ?>
								<i class="<?php echo esc_attr($settings['before_icon']); ?>" aria-hidden="true"></i>
							<?php endif; ?>

						</div>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_fname'] == 'yes') : ?>
					<div <?php $this->print_render_attribute_string('input-wrapper'); ?>>
						<input type="text" name="fname" placeholder="<?php echo esc_attr($settings['fname_field_placeholder']); ?>" class="bdt-input" />
					</div>
				<?php endif; ?>

				<div <?php echo $this->get_render_attribute_string('input-wrapper'); ?>>
					<input type="email" name="email" placeholder="<?php echo esc_attr($settings['email_field_placeholder']); ?>" required class="bdt-input" />
					<input type="hidden" name="action" value="element_pack_mailchimp_subscribe" />
					<input type="hidden" name="<?php echo esc_attr($form_id); ?>" value="true" />
					<!-- we need action parameter to receive ajax request in WordPress -->
				</div>
				<?php


				$this->add_render_attribute('signup_button', 'class', ['bdt-newsletter-btn', 'bdt-button', 'bdt-button-primary', 'bdt-width-1-1']);

				if ($settings['hover_animation']) {
					$this->add_render_attribute('signup_button', 'class', 'elementor-animation-' . $settings['hover_animation']);
				}

				?>
				<div class="bdt-newsletter-signup-wrapper bdt-width-auto">
					<button type="submit" <?php echo $this->get_render_attribute_string('signup_button'); ?>>
						<?php $this->render_text($settings); ?>
					</button>
				</div>
			</form>

			<!-- after text -->
			<?php if (!empty($settings['after_text'])) : ?>
				<div class="bdt-newsletter-after-text"><?php echo esc_attr($settings['after_text']); ?></div>
			<?php endif; ?>

		</div><!-- end newsletter-signup -->


<?php
	}
}
