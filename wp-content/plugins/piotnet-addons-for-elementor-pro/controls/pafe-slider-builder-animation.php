<?php

class PAFE_Slider_Builder_Animation extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-slider-builder-animation';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_slider_builder_animation_section',
			[
				'label' => __( 'PAFE Slider Builder Animation', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'pafe_slider_builder_animation_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'Please remove Default Entrance Animation in Advanced Tab', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_slider_builder_animation',
			[
				'label' => __( 'Entrance Animation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::ANIMATION,
				'prefix_class' => 'animated ',
				'label_block' => false,
				'frontend_available' => true,
				'condition' => [
					'pafe_slider_builder_animation_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_slider_builder_animation_duration',
			[
				'label' => __( 'Animation Duration', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'slow' => __( 'Slow', 'elementor' ),
					'' => __( 'Normal', 'elementor' ),
					'fast' => __( 'Fast', 'elementor' ),
				],
				'prefix_class' => 'animated-',
				'condition' => [
					'pafe_slider_builder_animation!' => '',
					'pafe_slider_builder_animation_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_slider_builder_animation_delay',
			[
				'label' => __( 'Animation Delay', 'elementor' ) . ' (ms)',
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'condition' => [
					'pafe_slider_builder_animation!' => '',
					'pafe_slider_builder_animation_enable' => 'yes',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_slider_builder_animation_enable'] ) ) {

			$class = 'pafe-slider-builder-animation';

			// $element->add_render_attribute( '_wrapper', [
			// 	'class' => $class,
			// 	'data-pafe-slider-builder-animation' => $settings['pafe_slider_builder_animation'],
			// 	'data-pafe-slider-builder-animation-duration' => $settings['pafe_slider_builder_animation_duration'],
			// 	'data-pafe-slider-builder-animation-delay' => $settings['pafe_slider_builder_animation_delay'],
			// ] );

			$element->add_render_attribute( '_wrapper', [
				'class' => $class,
				'data-animation-in' => $settings['pafe_slider_builder_animation'],
				'data-delay-in' => intval($settings['pafe_slider_builder_animation_delay'])/1000,
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
