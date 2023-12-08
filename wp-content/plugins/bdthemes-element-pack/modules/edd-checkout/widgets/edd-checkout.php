<?php

namespace ElementPack\Modules\EddCheckout\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;


if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class EDD_Checkout extends Module_Base {

	public function get_name() {
		return 'bdt-edd-checkout';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Checkout', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-checkout bdt-new';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['edd', 'easy', 'digital', 'downlaod', 'checkout', 'purchase'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-edd-checkout'];
		}
	}

	protected function register_controls() {
		$this->register_form_controls_layout();
		$this->register_checkout_table_header();
		$this->register_checkout_table_body();
		$this->register_checkout_table_subtotal();
		$this->register_form_controls_label();
		$this->register_form_controls_fields();
		$this->register_checkout_purchase_total();
		$this->register_form_submit_button();
	}

	protected function register_checkout_table_header() {
		$this->start_controls_section(
			'section_checkout_table_header_style',
			[
				'label' => __('Table Header', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_checkout_table_header_style');

		$this->start_controls_tab(
			'tab_checkout_table_header_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'checkout_header_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_header_row th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_header_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_checkout_cart .edd_cart_header_row th',
			]
		);
		$this->add_responsive_control(
			'header_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_header_row th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_cart .edd_cart_header_row th',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_checkout_header_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'checkout_header_hover_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_header_row th:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_header_hover_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_checkout_cart .edd_cart_header_row th:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}
	protected function register_checkout_table_body() {
		$this->start_controls_section(
			'section_style_body',
			[
				'label' => __('Table Body', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'checkout_cell_border_style',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_checkout_cart .edd_cart_item td',
			]
		);
		$this->add_responsive_control(
			'cell_padding',
			[
				'label'      => __('Cell Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'    => 0.5,
					'bottom' => 0.5,
					'left'   => 1,
					'right'  => 1,
					'unit'   => 'em'
				],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_body_style');

		$this->start_controls_tab(
			'tab_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'normal_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'normal_action_btn_color',
			[
				'label'     => __('Action Button Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'normal_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'row_hover_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'row_action_btn_hover_color',
			[
				'label'     => __('Action Button Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'row_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item td:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}
	protected function register_checkout_table_subtotal() {
		$this->start_controls_section(
			'checkout_section_total',
			[
				'label' => __('Table Footer', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'checkout_total_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart th.edd_cart_total' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_total_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_checkout_cart th.edd_cart_total',
			]
		);
		$this->add_responsive_control(
			'checkout_total_padding',
			[
				'label'                 => __('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} #edd_checkout_cart th.edd_cart_total'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'checkout_total_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_checkout_cart th.edd_cart_total',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'checkout_total_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_checkout_cart th.edd_cart_total',
			]
		);
		$this->end_controls_section();
	}
	protected function register_checkout_purchase_total() {
		$this->start_controls_section(
			'checkout_section_purchase_total',
			[
				'label' => __('Purchase Total', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'checkout_purchase_total_label_color',
			[
				'label'     => __('Label Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap strong' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'checkout_purchase_total_color',
			[
				'label'     => __('Price Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_purchase_total_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap',
			]
		);
		$this->add_responsive_control(
			'checkout_purchase_total_padding',
			[
				'label'                 => __('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'checkout_purchase_total_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'checkout_purchase_total_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap > *',
			]
		);
		$this->end_controls_section();
	}



	protected function register_form_controls_layout() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'checkout_action_button_type',
			[
				'label'      => __('Action Button Type', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'icon'   => __('Icon', 'bdthemes-element-pack'),
					'text'   => __('Text', 'bdthemes-element-pack'),
				],
				'default'    => 'icon',
				'dynamic'    => ['active' => true],
			]
		);
		$this->add_control(
			'checkout_action_button_text',
			[
				'label'       => __('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default' => __('Remove', 'bdthemes-element-pack'),
				'condition' => [
					'checkout_action_button_type' => 'text'
				]
			]
		);
		$this->add_control(
			'checkout_action_button_icon',
			[
				'label'         => __('Select Icon', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::ICONS,
				'default'       => [
					'value'     => 'eicon-close',
					'library'   => 'solid',
				],
				'condition' => [
					'checkout_action_button_type' => 'icon'
				]
			]
		);
		$this->add_control(
			'edd_register_form_input_fullwidth',
			[
				'label' => esc_html__('Fullwidth Input', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="text"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="email"]'    => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="url"]'      => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="number"]'   => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="tel"]'      => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="date"]'     => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap input[type*="password"]' => 'width: 100%;',
					'{{WRAPPER}} #edd_checkout_form_wrap .select.edd-select'      => 'width: 100%;',
				],
				'separator' => 'before'
			]
		);
		$this->add_control(
			'edd_register_form_button_fullwidth',
			[
				'label' => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('On', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Off', 'bdthemes-element-pack'),
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap .edd-submit' => 'width: 100%;',
				],
			]
		);
		$this->end_controls_section();
	}
	protected function register_form_controls_label() {
		$this->start_controls_section(
			'section_style_labels',
			[
				'label'      => esc_html__('Form Label', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_checkout_profile_form_title_label',
			[
				'label'     => __('P R O F I L E    I N F O', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'profile_label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .bdt-edd-checkout #edd_checkout_form_wrap legend' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'profile_label_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-checkout #edd_checkout_form_wrap fieldset' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'profile_label_border_width',
			[
				'label'     => esc_html__('Border Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-checkout #edd_checkout_form_wrap fieldset' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'profile_label_typography',
				'selector' => '{{WRAPPER}} .bdt-edd-checkout #edd_checkout_form_wrap legend',
			]
		);
		$this->add_control(
			'heading_checkout_profile_form_label',
			[
				'label'     => __('L A B E L', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #edd_checkout_form_wrap label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'label_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap label',
			]
		);
		$this->add_control(
			'heading_checkout_profile_form_sub_label',
			[
				'label'     => __('S U B   L A B E L', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'sub_label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #edd_checkout_form_wrap span.edd-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'sub_label_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap span.edd-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_label_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap span.edd-description',
			]
		);

		$this->end_controls_section();
	}
	protected function register_form_controls_fields() {
		$this->start_controls_section(
			'section_field_style',
			[
				'label' => esc_html__('Form Fields', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_field_style');

		$this->start_controls_tab(
			'tab_field_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input::placeholder'      => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'field_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#edd_checkout_form_wrap input.edd-input',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'field_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} #edd_checkout_form_wrap input.edd-input',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'field_box_shadow',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap input.edd-input',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_field_hover',
			[
				'label' => esc_html__('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_text_color_focus',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color_focus',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input:focus::placeholder'      => 'color: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input:focus::-moz-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_background_color_focus',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'field_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	protected function register_form_submit_button() {
		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label' => esc_html__('Form Submit Button', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button',
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
			'button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-purchase-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} #edd_register_form #edd-purchase-button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_register_form #edd-purchase-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #edd_register_form #edd-purchase-button:hover',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$payment_mode = edd_get_chosen_gateway();
		$form_action  = esc_url(edd_get_checkout_uri('payment-mode=' . $payment_mode));
		if (!edd_is_ajax_disabled()) {
			$this->add_render_attribute('edd_checkout_cart', 'class', ['ajaxed'], true);
		}
		global $post; ?>
		<div class="bdt-edd-checkout">
			<table id="edd_checkout_cart" <?php $this->print_render_attribute_string('edd_checkout_cart'); ?>>
				<thead>
					<tr class="edd_cart_header_row">
						<?php do_action('edd_checkout_table_header_first'); ?>
						<th class="edd_cart_item_name"><?php _e('Item Name', 'bdthemes-element-pack'); ?></th>
						<th class="edd_cart_item_price"><?php _e('Item Price', 'bdthemes-element-pack'); ?></th>
						<th class="edd_cart_actions"><?php _e('Actions', 'bdthemes-element-pack'); ?></th>
						<?php do_action('edd_checkout_table_header_last'); ?>
					</tr>
				</thead>
				<tbody>
					<?php $cart_items = edd_get_cart_contents(); ?>
					<?php do_action('edd_cart_items_before'); ?>
					<?php if ($cart_items) : ?>
						<?php foreach ($cart_items as $key => $item) : ?>
							<tr class="edd_cart_item" id="edd_cart_item_<?php echo esc_attr($key) . '_' . esc_attr($item['id']); ?>" data-download-id="<?php echo esc_attr($item['id']); ?>">
								<?php do_action('edd_checkout_table_body_first', $item); ?>
								<td class="edd_cart_item_name">
									<?php
									if (current_theme_supports('post-thumbnails') && has_post_thumbnail($item['id'])) {
										echo '<div class="edd_cart_item_image">';
										echo get_the_post_thumbnail($item['id'], apply_filters('edd_checkout_image_size', array(25, 25)));
										echo '</div>';
									}
									$item_title = edd_get_cart_item_name($item);
									echo '<span class="edd_checkout_cart_item_title">' . esc_html($item_title) . '</span>';

									/**
									 * Runs after the item in cart's title is echoed
									 * @since 2.6
									 *
									 * @param array $item Cart Item
									 * @param int $key Cart key
									 */
									do_action('edd_checkout_cart_item_title_after', $item, $key);
									?>
								</td>
								<td class="edd_cart_item_price">
									<?php
									echo edd_cart_item_price($item['id'], $item['options']);
									do_action('edd_checkout_cart_item_price_after', $item);
									?>
								</td>
								<td class="edd_cart_actions">
									<?php if (edd_item_quantities_enabled() && !edd_download_quantities_disabled($item['id'])) : ?>
										<input type="number" min="1" step="1" name="edd-cart-download-<?php echo $key; ?>-quantity" data-key="<?php echo $key; ?>" class="edd-input edd-item-quantity" value="<?php echo edd_get_cart_item_quantity($item['id'], $item['options']); ?>" />
										<input type="hidden" name="edd-cart-downloads[]" value="<?php echo $item['id']; ?>" />
										<input type="hidden" name="edd-cart-download-<?php echo $key; ?>-options" value="<?php echo esc_attr(json_encode($item['options'])); ?>" />
									<?php endif; ?>
									<?php do_action('edd_cart_actions', $item, $key); ?>
									<a class="edd_cart_remove_item_btn" href="<?php echo esc_url(wp_nonce_url(edd_remove_item_url($key), 'edd-remove-from-cart-' . $key, 'edd_remove_from_cart_nonce')); ?>">
										<?php if (($settings['checkout_action_button_type'] === 'text')) {
											echo '<span class="edd-action-btn-remove-text">' . esc_html($settings['checkout_action_button_text']) . '</span>';
										} else { ?>
											<span class="edd-action-btn-remove-icon">
												<?php Icons_Manager::render_icon($settings['checkout_action_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
											</span>
										<?php
										} ?>

									</a>
								</td>
								<?php do_action('edd_checkout_table_body_last', $item); ?>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php do_action('edd_cart_items_middle'); ?>
					<!-- Show any cart fees, both positive and negative fees -->
					<?php if (edd_cart_has_fees()) : ?>
						<?php foreach (edd_get_cart_fees() as $fee_id => $fee) : ?>
							<tr class="edd_cart_fee" id="edd_cart_fee_<?php echo $fee_id; ?>">

								<?php do_action('edd_cart_fee_rows_before', $fee_id, $fee); ?>

								<td class="edd_cart_fee_label"><?php echo esc_html($fee['label']); ?></td>
								<td class="edd_cart_fee_amount"><?php echo esc_html(edd_currency_filter(edd_format_amount($fee['amount']))); ?></td>
								<td>
									<?php if (!empty($fee['type']) && 'item' == $fee['type']) : ?>
										<a href="<?php echo esc_url(edd_remove_cart_fee_url($fee_id)); ?>">

											<?php _e('Remove', 'bdthemes-element-pack'); ?>

										</a>
									<?php endif; ?>

								</td>

								<?php do_action('edd_cart_fee_rows_after', $fee_id, $fee); ?>

							</tr>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php do_action('edd_cart_items_after'); ?>
				</tbody>
				<tfoot>

					<?php if (has_action('edd_cart_footer_buttons')) : ?>
						<tr class="edd_cart_footer_row<?php if (edd_is_cart_saving_disabled()) {
															echo ' edd-no-js';
														} ?>">
							<th colspan="<?php echo edd_checkout_cart_columns(); ?>">
								<?php do_action('edd_cart_footer_buttons'); ?>
							</th>
						</tr>
					<?php endif; ?>
					<?php if (edd_use_taxes() && !edd_prices_include_tax()) : ?>
						<tr class="edd_cart_footer_row edd_cart_subtotal_row" <?php if (!edd_is_cart_taxed()) echo ' style="display:none;"'; ?>>
							<?php do_action('edd_checkout_table_subtotal_first'); ?>
							<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_subtotal">
								<?php _e('Subtotal', 'bdthemes-element-pack'); ?>:&nbsp;<span class="edd_cart_subtotal_amount"><?php echo edd_cart_subtotal(); ?></span>
							</th>
							<?php do_action('edd_checkout_table_subtotal_last'); ?>
						</tr>
					<?php endif; ?>
					<tr class="edd_cart_footer_row edd_cart_discount_row" <?php if (!edd_cart_has_discounts())  echo ' style="display:none;"'; ?>>
						<?php do_action('edd_checkout_table_discount_first'); ?>
						<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_discount">
							<?php edd_cart_discounts_html(); ?>
						</th>
						<?php do_action('edd_checkout_table_discount_last'); ?>
					</tr>
					<?php if (edd_use_taxes()) : ?>
						<tr class="edd_cart_footer_row edd_cart_tax_row" <?php if (!edd_is_cart_taxed()) echo ' style="display:none;"'; ?>>
							<?php do_action('edd_checkout_table_tax_first'); ?>
							<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_tax">
								<?php _e('Tax', 'bdthemes-element-pack'); ?>:&nbsp;<span class="edd_cart_tax_amount" data-tax="<?php echo edd_get_cart_tax(false); ?>"><?php echo esc_html(edd_cart_tax()); ?></span>
							</th>
							<?php do_action('edd_checkout_table_tax_last'); ?>
						</tr>
					<?php endif; ?>
					<tr class="edd_cart_footer_row">
						<?php do_action('edd_checkout_table_footer_first'); ?>
						<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_total"><?php _e('Total', 'bdthemes-element-pack'); ?>: <span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></span></th>
						<?php do_action('edd_checkout_table_footer_last'); ?>
					</tr>
				</tfoot>
			</table>
			<div id="edd_checkout_form_wrap" class="edd_clearfix">
				<?php do_action('edd_before_purchase_form'); ?>
				<form id="edd_purchase_form" class="edd_form" action="<?php echo $form_action; ?>" method="POST">
					<?php
					/**
					 * Hooks in at the top of the checkout form
					 *
					 * @since 1.0
					 */
					do_action('edd_checkout_form_top');

					if (edd_is_ajax_disabled() && !empty($_REQUEST['payment-mode'])) {
						do_action('edd_purchase_form');
					} elseif (edd_show_gateways()) {
						do_action('edd_payment_mode_select');
					} else {
						do_action('edd_purchase_form');
					}

					/**
					 * Hooks in at the bottom of the checkout form
					 *
					 * @since 1.0
					 */
					do_action('edd_checkout_form_bottom')
					?>
				</form>
				<?php do_action('edd_after_purchase_form'); ?>
			</div>
		</div>
		<!--end #edd_checkout_form_wrap-->

<?php
	}
}
