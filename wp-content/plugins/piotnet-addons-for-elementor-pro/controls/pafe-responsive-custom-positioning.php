<?php
class PAFE_Responsive_Custom_Positioning extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-custom-positioning';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_responsive_custom_positioning',
			[
				'label' => __( 'PAFE Responsive Custom Positioning', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_responsive_control(
			'pafe_position',
			[
				'label' => __( 'Position', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'relative' => __( 'Default', 'elementor' ),
					'absolute' => __( 'Absolute', 'elementor' ),
					'fixed' => __( 'Fixed', 'elementor' ),
				],
				'prefix_class' => 'elementor-',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => 'position: {{pafe_position}} !important;',
				],
			]
		);

		$start = is_rtl() ? __( 'Right', 'elementor' ) : __( 'Left', 'elementor' );
		$end = ! is_rtl() ? __( 'Right', 'elementor' ) : __( 'Left', 'elementor' );

		$element->add_control(
			'pafe_offset_orientation_h',
			[
				'label' => __( 'Horizontal Orientation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => $start,
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => $end,
						'icon' => 'eicon-h-align-right',
					],
				],
				'classes' => 'elementor-control-start-end',
				'render_type' => 'ui',
				'condition' => [
					'pafe_position!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_offset_x',
			[
				'label' => __( 'Offset', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}} !important;',
					'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'pafe_offset_orientation_h!' => 'end',
					'pafe_position!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_offset_x_end',
			[
				'label' => __( 'Offset', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 0.1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}} !important;',
					'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'pafe_offset_orientation_h' => 'end',
					'pafe_position!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_offset_orientation_v',
			[
				'label' => __( 'Vertical Orientation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => __( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'end' => [
						'title' => __( 'Bottom', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'pafe_position!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_offset_y',
			[
				'label' => __( 'Offset', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'pafe_offset_orientation_v!' => 'end',
					'pafe_position!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_offset_y_end',
			[
				'label' => __( 'Offset', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'pafe_offset_orientation_v' => 'end',
					'pafe_position!' => '',
				],
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_position/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
