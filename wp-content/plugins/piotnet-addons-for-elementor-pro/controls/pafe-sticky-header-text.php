<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Sticky_Header_Text extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-sticky-header-text';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_sticky_header_text_section',
			[
				'label' => __( 'PAFE Sticky Header For Text ', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_sticky_header_text_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_sticky_header_text_color',
			[
				'label' => __( 'Text Color When Header Sticky', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}.pafe-sticky-header-active-element *' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'pafe_sticky_header_text_enable!' => '',
				],
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
