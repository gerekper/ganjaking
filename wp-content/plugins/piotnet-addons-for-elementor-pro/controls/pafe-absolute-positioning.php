<?php
class PAFE_Absolute_Positioning extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-absolute-positioning';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_absolute_positioning',
			[
				'label' => __( 'PAFE Absolute Positioning', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'pafe_absolute_positioning_enable',
			[
				'label' => __( 'Enable Absolute', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_absolute_positioning_top',
			[
				'label' =>'Top',
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'auto',
				'selectors' => [
					'{{WRAPPER}}' => 'position: absolute; top: {{pafe_absolute_positioning_top}};',
				],
				'condition' => [
					'pafe_absolute_positioning_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_absolute_positioning_right',
			[
				'label' =>'Right',
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'auto',
				'selectors' => [
					'{{WRAPPER}}' => 'position: absolute; right: {{pafe_absolute_positioning_right}};',
				],
				'condition' => [
					'pafe_absolute_positioning_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_absolute_positioning_bottom',
			[
				'label' =>'Bottom',
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'auto',
				'selectors' => [
					'{{WRAPPER}}' => 'position: absolute; bottom: {{pafe_absolute_positioning_bottom}};',
				],
				'condition' => [
					'pafe_absolute_positioning_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_absolute_positioning_left',
			[
				'label' =>'Left',
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => 'auto',
				'selectors' => [
					'{{WRAPPER}}' => 'position: absolute; left: {{pafe_absolute_positioning_left}};',
				],
				'condition' => [
					'pafe_absolute_positioning_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
