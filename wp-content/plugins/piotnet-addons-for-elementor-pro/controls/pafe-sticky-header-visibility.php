<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Sticky_Header_Visibility extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-sticky-header-visibility';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_sticky_header_visibility_section',
			[
				'label' => __( 'PAFE Sticky Header Visibility', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_sticky_header_visibility_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->start_controls_tabs( 'pafe_sticky_header_visibility_tabs',
			[
				'condition' => [
					'pafe_sticky_header_visibility_enable!' => '',
				],
			]
		);

		$element->start_controls_tab( 'pafe_sticky_header_visibility_tab_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$element->add_control(
			'pafe_sticky_header_visibility_hidden',
			[
				'label' => __( 'Hidden', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'body:not(.elementor-editor-active):not(.pafe-sticky-header-on) {{WRAPPER}}' => 'display: none;',
				],
				'condition' => [
					'pafe_sticky_header_visibility_enable!' => '',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab( 'pafe_sticky_header_visibility_tab_sticky',
			[
				'label' => __( 'Sticky', 'pafe' ),
			]
		);

		$element->add_control(
			'pafe_sticky_header_visibility_hidden_sticky',
			[
				'label' => __( 'Hidden', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'body:not(.elementor-editor-active).pafe-sticky-header-on {{WRAPPER}}' => 'display: none;',
				],
				'condition' => [
					'pafe_sticky_header_visibility_enable!' => '',
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
