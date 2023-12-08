<?php

namespace ElementPack\Modules\WcElements\Widgets;

use Elementor\Controls_Manager;
use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Modules\WcElements\Module;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WC_Elements extends Module_Base {

	public function get_name() {
		return 'bdt-wc-elements';
	}

	public function get_title() {
		return BDTEP . esc_html__('WC - Elements', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-wc-elements';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['cart', 'woocommerce', 'single', 'product', 'checkout', 'order', 'tracking', 'form', 'account'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-wc-elements'];
		}
	}

	public function on_export($element) {
		unset($element['settings']['product_id']);

		return $element;
	}

	// public function get_custom_help_url() {
	// 	return 'https://youtu.be/SJuArqtnC1U';
	// }

	protected function register_controls() {
		$this->start_controls_section(
			'section_product',
			[
				'label' => esc_html__('Element', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'element',
			[
				'label' => esc_html__('Element', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					''                           => esc_html__('Select', 'bdthemes-element-pack'),
					'woocommerce_cart'           => esc_html__('Cart Page', 'bdthemes-element-pack'),
					'product_page'               => esc_html__('Single Product Page', 'bdthemes-element-pack'),
					'woocommerce_checkout'       => esc_html__('Checkout Page', 'bdthemes-element-pack'),
					'woocommerce_order_tracking' => esc_html__('Order Tracking Form', 'bdthemes-element-pack'),
					'woocommerce_my_account'     => esc_html__('My Account', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'product_id',
			[
				'label'       => esc_html__('Enter Product ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [
					'element' => ['product_page'],
				],
			]
		);



		$this->end_controls_section();




		$this->start_controls_section(
			'section_checkout_style_label',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_checkout'],
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce form .form-row label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'required_color',
			[
				'label'     => esc_html__('Required Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce form .form-row .required' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .woocommerce form .form-row label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkout_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_checkout'],
				],
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce select' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce textarea.input-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce select' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce textarea.input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label' => esc_html__('Textarea Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 125,
				],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce textarea.input-text' => 'height: {{SIZE}}{{UNIT}}; display: block;',
				],
				'separator' => 'before',

			]
		);

		$this->add_control(
			'input_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text,
					 {{WRAPPER}} .woocommerce textarea.input-text,
					 {{WRAPPER}} .select2-container--default .select2-selection--single,
					 {{WRAPPER}} .woocommerce select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-container--default .select2-selection--single' => 'height: auto; min-height: 37px;',
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered' => 'line-height: initial;',
				],
			]
		);

		$this->add_responsive_control(
			'input_space',
			[
				'label' => esc_html__('Element Space', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 25,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce form .form-row' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'input_border_show',
			[
				'label' => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'yes',
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
				'selector'    => '
					{{WRAPPER}} .woocommerce .input-text,
					{{WRAPPER}} .woocommerce select,
					{{WRAPPER}} .select2-container--default .select2-selection--single',
				'condition' => [
					'input_border_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'input_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text,
					 {{WRAPPER}} .select2-container--default .select2-selection--single,
					 {{WRAPPER}} .woocommerce select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_checkout_style_order_table',
			[
				'label' => esc_html__('Order Table', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_checkout'],
				],
			]
		);

		$this->add_control(
			'order_table_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table th,
					{{WRAPPER}} .woocommerce table.shop_table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'order_table_border_show',
			[
				'label' => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'order_table_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .woocommerce table.shop_table',

				'condition' => [
					'order_table_border_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'order_table_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();



		// Payment section
		$this->start_controls_section(
			'section_style_checkout_payment',
			[
				'label' => esc_html__('Payment', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_checkout'],
				],
			]
		);

		$this->add_control(
			'checkout_payment_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout #payment, {{WRAPPER}} .woocommerce-checkout #payment div.payment_box' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'checkout_payment_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-checkout #payment' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-checkout #payment div.payment_box' => 'opacity:0.5;',
					'{{WRAPPER}} .woocommerce-checkout #payment div.payment_box::before' => 'opacity:0.5;',
				],
			]
		);

		$this->add_control(
			'checkout_payment_button_heading',
			[
				'label' => esc_html__('Button Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs('tabs_payment_button_style');

		$this->start_controls_tab(
			'tab_payment_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'payment_button_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'payment_button_background_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'payment_button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .woocommerce input.button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'payment_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'payment_button_box_shadow',
				'selector' => '{{WRAPPER}} .wpcf7-submit',
			]
		);

		$this->add_control(
			'payment_button_text_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'payment_button_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .woocommerce input.button',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_payment_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'payment_button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'payment_button_background_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'payment_button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		// TRacking section
		$this->start_controls_section(
			'section_tracking_style_label',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_order_tracking'],
				],
			]
		);

		$this->add_control(
			'tracking_label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce form .form-row label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tracking_label_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .woocommerce form .form-row label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tracking_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_order_tracking'],
				],
			]
		);

		$this->add_control(
			'tracking_input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tracking_input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce select' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce textarea.input-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tracking_input_text_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce select' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce textarea.input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tracking_input_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text,
					 {{WRAPPER}} .woocommerce textarea.input-text,
					 {{WRAPPER}} .select2-container--default .select2-selection--single,
					 {{WRAPPER}} .woocommerce select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-container--default .select2-selection--single' => 'height: auto; min-height: 37px;',
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered' => 'line-height: initial;',
				],
			]
		);

		$this->add_responsive_control(
			'tracking_input_space',
			[
				'label' => esc_html__('Element Space', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 25,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce form .form-row' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tracking_input_border_show',
			[
				'label' => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'tracking_input_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '
					{{WRAPPER}} .woocommerce .input-text,
					{{WRAPPER}} .woocommerce select,
					{{WRAPPER}} .select2-container--default .select2-selection--single',
				'condition' => [
					'tracking_input_border_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'tracking_input_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text,
					 {{WRAPPER}} .select2-container--default .select2-selection--single,
					 {{WRAPPER}} .woocommerce select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_tracking',
			[
				'label' => esc_html__('Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_order_tracking'],
				],
			]
		);

		$this->add_control(
			'tracking_button_heading',
			[
				'label' => esc_html__('Button Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs('tabs_tracking_button_style');

		$this->start_controls_tab(
			'tab_tracking_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tracking_button_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tracking_button_background_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tracking_button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .woocommerce input.button, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tracking_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tracking_button_box_shadow',
				'selector' => '{{WRAPPER}} .wpcf7-submit, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button',
			]
		);

		$this->add_control(
			'tracking_button_text_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tracking_button_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .woocommerce input.button, {{WRAPPER}} .woocommerce button.button, {{WRAPPER}} .woocommerce a.button',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_tracking_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tracking_button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button:hover, {{WRAPPER}} .woocommerce button.button:hover, {{WRAPPER}} .woocommerce a.button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tracking_button_background_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button:hover, {{WRAPPER}} .woocommerce button.button:hover, {{WRAPPER}} .woocommerce a.button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tracking_button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'tracking_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce input.button:hover, {{WRAPPER}} .woocommerce button.button:hover, {{WRAPPER}} .woocommerce a.button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Cart style

		$this->start_controls_section(
			'section_cart_style_heading',
			[
				'label' => esc_html__('Table Heading', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_cart'],
				],
			]
		);

		$this->add_control(
			'cart_table_heading_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_heading_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_cart_style_table',
			[
				'label' => esc_html__('Table Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_cart'],
				],
			]
		);

		$this->add_control(
			'cart_table_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart td *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_table_border_show',
			[
				'label' => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);



		$this->add_control(
			'cart_table_border_width',
			[
				'label' => esc_html__('Border Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce table.shop_table.cart td' => 'border-top-width: {{TOP}}{{UNIT}};',
				],
				'condition'   => [
					'cart_table_border_show' => ['yes'],
				],
			]
		);

		$this->add_control(
			'cart_table_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce table.shop_table.cart td' => 'border-top-color: {{VALUE}};',
				],
				'condition'   => [
					'cart_table_border_show' => ['yes'],
				],
			]
		);

		$this->add_control(
			'cart_table_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .input-text,
					 {{WRAPPER}} .select2-container--default .select2-selection--single,
					 {{WRAPPER}} .woocommerce select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cart_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_cart'],
				],
			]
		);

		$this->add_control(
			'cart_input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.cart .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.cart .input-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_input_text_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} table.cart .input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_input_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} table.cart .input-text, {{WRAPPER}} table.cart td.actions .coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; box-sizing: content-box;',
				],
			]
		);


		$this->add_control(
			'cart_input_border_show',
			[
				'label' => esc_html__('Border Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => 'Hide',
				'label_off' => 'Show',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'cart_input_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '
					{{WRAPPER}} table.cart .input-text,
					{{WRAPPER}} table.cart td.actions .coupon .input-text',
				'condition' => [
					'cart_input_border_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_input_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} table.cart .input-text,
					 {{WRAPPER}} .select2-container--default .select2-selection--single,
					 {{WRAPPER}} .woocommerce select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		// Cart table button
		$this->start_controls_section(
			'section_style_cart_button',
			[
				'label' => esc_html__('Coupon/Update Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_cart'],
				],
			]
		);

		$this->add_control(
			'cart_button_heading',
			[
				'label' => esc_html__('Button Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs('tabs_cart_button_style');

		$this->start_controls_tab(
			'tab_cart_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'cart_button_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_button_background_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cart_button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .woocommerce table tr td button.button, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cart_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cart_button_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce table tr td button.button, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text',
			]
		);

		$this->add_control(
			'cart_button_text_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_button_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .woocommerce table tr td button.button',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_cart_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'cart_button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button:hover, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_button_background_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button:hover, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table tr td button.button:hover, {{WRAPPER}} .woocommerce table.cart td.actions .coupon .input-text:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		// Cart table button
		$this->start_controls_section(
			'section_style_cart_checkout_button',
			[
				'label' => esc_html__('Checkout Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_cart'],
				],
			]
		);

		$this->add_control(
			'cart_checkout_button_heading',
			[
				'label' => esc_html__('Button Style', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs('tabs_cart_checkout_button_style');

		$this->start_controls_tab(
			'tab_cart_checkout_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'cart_checkout_button_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_checkout_button_background_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cart_checkout_button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cart_checkout_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cart_checkout_button_box_shadow',
				'selector' => '{{WRAPPER}} .wpcf7-submit',
			]
		);

		$this->add_control(
			'cart_checkout_button_text_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_checkout_button_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_cart_checkout_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'cart_checkout_button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_checkout_button_background_hover_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_checkout_button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout a.checkout-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		// Account style
		$this->start_controls_section(
			'section_style_my_account',
			[
				'label' => esc_html__('My Account Style', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'element' => ['woocommerce_my_account'],
				],
			]
		);

		$this->add_control(
			'my_account_notice',
			[
				'label' => '<i>My Account does not support any style because my account others menu is dynamic part of My Account widget. We are sorry for it.</i>',
				'type' => Controls_Manager::RAW_HTML,


			]
		);

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		switch ($settings['element']) {
			case '':
				return '';
				break;

			case 'product_page':

				if (!empty($settings['product_id'])) {
					$product_data = get_post($settings['product_id']);
					$product = !empty($product_data) && in_array($product_data->post_type, array('product', 'product_variation')) ? wc_setup_product_data($product_data) : false;
				}

				if (empty($product) && current_user_can('manage_options')) {
					return esc_html__('Please set a valid product', 'bdthemes-element-pack');
				}

				$this->add_render_attribute('shortcode', 'id', $settings['product_id']);
				break;

			case 'woocommerce_cart':
			case 'woocommerce_checkout':
			case 'woocommerce_order_tracking':
				break;
		}

		$shortcode = sprintf('[%s %s]', $settings['element'], $this->get_render_attribute_string('shortcode'));

		return $shortcode;
	}

	protected function render() {
		$shortcode = $this->get_shortcode();

		if (empty($shortcode)) {
			return;
		}

		Module::instance()->add_products_post_class_filter();

		$html = do_shortcode($shortcode);

		if ('woocommerce_checkout' === $this->get_settings('element') && '<div class="woocommerce"></div>' === $html) {
			$html = '<div class="woocommerce">' . esc_html__('Your cart is currently empty.', 'bdthemes-element-pack') . '</div>';
		}

		echo  $html;

		Module::instance()->remove_products_post_class_filter();
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
