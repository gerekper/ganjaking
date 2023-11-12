<?php

class PAFE_Advanced_Dots_Styling extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-advanced-dots-styling';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_advanced_dots_styling_section',
			[
				'label' => __( 'PAFE Advanced Dots Styling', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li' => 'width: auto; height: auto;',
					'{{WRAPPER}} .slick-dots li button' => 'padding: 0;',
					'{{WRAPPER}} .slick-dots li button:before' => 'content: "";display: block;',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_width',
			[
				'label' => __( 'Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-dots li button' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_width_active',
			[
				'label' => __( 'Width Active', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-dots li.slick-active button' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_height',
			[
				'label' => __( 'Height', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-dots li button' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_height_active',
			[
				'label' => __( 'Height Active', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-dots li.slick-active button' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_spacing',
			[
				'label' => __( 'Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-dots li:last-child button' => 'margin-right: 0;',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_bottom_position',
			[
				'label' => __( 'Bottom Position', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g 20px, -20px, 10%',
				'selectors' => [
					'{{WRAPPER}} .slick-dots' => 'bottom: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullets' => 'bottom: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_background_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_background_color_active',
			[
				'label' => __( 'Background Color Active', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_opacity',
			[
				'label' => __( 'Opacity', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0.7,
				'min' => 0.1,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_opacity_active',
			[
				'label' => __( 'Opacity Active', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0.7,
				'min' => 0.1,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'opacity: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_border_radius',
			[
				'label' => __( 'Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_border',
			[
				'label' => __( 'Border Type', 'pafe' ),
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
					'{{WRAPPER}} .slick-dots li button:before' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_dots_styling_border_width',
			[
				'label' => __( 'Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_border!' => '',
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_border_color',
			[
				'label' => __( 'Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li button:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_border!' => '',
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_dots_styling_border_color_active',
			[
				'label' => __( 'Border Color Active', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .slick-dots li.slick-active button:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_advanced_dots_styling_border!' => '',
					'pafe_advanced_dots_styling_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/image-carousel/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/slides/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/posts/custom_section_design_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-slider-builder/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/media-carousel/section_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/testimonial-carousel/section_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
