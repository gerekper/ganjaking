<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Support extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-support';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_support_section',
			[
				'label' => __( 'PAFE Support', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);
		
		$element->add_control(
			'pafe_support',
			[
				'label' => __( 'Support', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<br><div>Email: <a href="mailto:support@piotnet.com">support@piotnet.com</a></div><br><div>Website: <a href="https://pafe.piotnet.com">https://pafe.piotnet.com</a></div>'
			]
		);

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout_container/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
