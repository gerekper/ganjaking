<?php

class PAFE_Close_First_Accordion extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-close-first-accordion';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_close_first_accordion_section',
			[
				'label' => __( 'PAFE Close First Accordion', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_close_first_accordion_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_close_first_accordion_enable'] ) ) {

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-close-first-accordion' => '',
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/accordion/section_title/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
