<?php

class PAFE_Advanced_Form_Styling extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-advanced-form-styling';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_advanced_form_styling_section',
			[
				'label' => __( 'PAFE Advanced Form Styling', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_advanced_form_styling_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_form_styling_input_padding',
			[
				'label' => __( 'Input Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_form_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_form_styling_input_placeholder_color',
			[
				'label' => __( 'Input Placeholder Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::-webkit-input-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)::-moz-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):-ms-input-placeholder' => 'color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):-moz-placeholder' => 'color: {{VALUE}}; opacity: 1;',
				],
				'condition' => [
					'pafe_advanced_form_styling_enable' => 'yes',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_advanced_form_styling_input_box_shadow',
				'label' => __( 'Input Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)',
				'condition' => [
					'pafe_advanced_form_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_form_styling_button_margin',
			[
				'label' => __( 'Button Margin', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_form_styling_enable' => 'yes',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_advanced_form_styling_button_box_shadow',
				'label' => __( 'Button Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}} .elementor-button',
				'condition' => [
					'pafe_advanced_form_styling_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_messages_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
