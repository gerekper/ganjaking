<?php

class PAFE_Advanced_Tabs_Styling extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-advanced-tabs-styling';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_advanced_tabs_styling_section',
			[
				'label' => __( 'PAFE Advanced Tabs Styling', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_enable',
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
			'pafe_advanced_tabs_styling_title_padding',
			[
				'label' => __( 'Title Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title a' => 'display: block; padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-desktop-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-mobile-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_title_text_align',
			[
				'label' => __( 'Title Text Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_title_background_color',
			[
				'label' => __( 'Title Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-mobile-title' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_title_background_color_active',
			[
				'label' => __( 'Title Active Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title.elementor-active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-mobile-title.elementor-active' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_title_border',
			[
				'label' => __( 'Title Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title a' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-mobile-title' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_title_border_width',
			[
				'label' => __( 'Title Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-mobile-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_title_border!' => '',
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_title_border_color',
			[
				'label' => __( 'Title Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title a' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-mobile-title' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_title_border!' => '',
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_title_border_color_active',
			[
				'label' => __( 'Title Border Color Active', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active a' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-title.elementor-active' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-tab-mobile-title.elementor-active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_title_border!' => '',
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_title_border_radius',
			[
				'label' => __( 'Title Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-tab-mobile-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_content_margin',
			[
				'label' => __( 'Content Margin', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_content_padding',
			[
				'label' => __( 'Content Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_content_background_color',
			[
				'label' => __( 'Content Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_content_border',
			[
				'label' => __( 'Content Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_content_border_width',
			[
				'label' => __( 'Content Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_content_border!' => '',
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_tabs_styling_content_border_color',
			[
				'label' => __( 'Content Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_content_border!' => '',
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_tabs_styling_content_border_radius',
			[
				'label' => __( 'Content Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_tabs_styling_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/tabs/section_tabs_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
