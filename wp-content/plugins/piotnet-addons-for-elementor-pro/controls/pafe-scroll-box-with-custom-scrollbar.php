<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Scroll_Box_With_Custom_Scrollbar extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-scroll-box-with-custom-scrollbar';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_scroll_box_with_custom_scrollbar_section',
			[
				'label' => __( 'PAFE Scroll Box With Custom Scrollbar', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_scroll_box_with_custom_scrollbar_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}' => 'margin-right: 2px;',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_scroll_box_with_custom_scrollbar_max_height',
			[
				'label' => __( 'Max Height', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'selectors' => [
					'{{WRAPPER}}' => 'max-height: {{VALUE}}; overflow-y: scroll; oveflow-x: hidden;',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_scroll_box_with_custom_scrollbar_width',
			[
				'label' => __( 'Scrollbar Width (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .scroll-element.scroll-y' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .scroll-content.scroll-scrolly_visible' => 'left: -{{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_scroll_box_with_custom_scrollbar_thumb_background',
			[
				'label' => __( 'Scrollbar Thumb Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#d9d9d9',
				'selectors' => [
					'{{WRAPPER}} .scroll-element .scroll-bar' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_scroll_box_with_custom_scrollbar_track_background',
			[
				'label' => __( 'Scrollbar Track Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#eeeeee',
				'selectors' => [
					'{{WRAPPER}} .scroll-element .scroll-element_track' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_scroll_box_with_custom_scrollbar_track_border_type',
			[
				'label' => __( 'Scrollbar Track Border Type', 'pafe' ),
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
					'{{WRAPPER}} .scroll-element .scroll-element_track' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_scroll_box_with_custom_scrollbar_track_border_width',
			[
				'label' => __( 'Scrollbar Track Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .scroll-element .scroll-element_track' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_track_border_type!' => '',
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_scroll_box_with_custom_scrollbar_track_border_color',
			[
				'label' => __( 'Scrollbar Track Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .scroll-element .scroll-element_track' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_track_border_type!' => '',
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_scroll_box_with_custom_scrollbar_border_radius',
			[
				'label' => __( 'Border Radius (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .scroll-element .scroll-bar' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .scroll-element .scroll-element_track' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .scroll-element .scroll-element_outer' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_scroll_box_with_custom_scrollbar_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function after_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_scroll_box_with_custom_scrollbar_enable'] ) ) {

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-scroll-box-with-custom-scrollbar' => '',
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'after_render_element'], 10, 1 );
	}

}
