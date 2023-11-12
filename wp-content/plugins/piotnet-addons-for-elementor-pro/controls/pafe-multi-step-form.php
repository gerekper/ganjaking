<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Multi_Step extends \Elementor\Widget_Base {
	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-multi-step';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_multi_step_form_section',
			[
				'label' => __( 'PAFE Multi Step Form', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);
		
		$element->add_control(
			'pafe_multi_step_form_step_title',
			[
				'label' => __( 'Step Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$element->end_controls_section();

	}

	public function before_render_section($element) {
		$settings = $element->get_settings_for_display();
		$step_title = $settings['pafe_multi_step_form_step_title'];
		if( !empty($step_title) ) { 
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-forms-multistep-title' => $step_title,
			] );
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout_container/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_section'], 10, 1 );
	}

}
