<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Next_Prev_Multi_Step_Form extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-next-prev-multi-step-form';
	}

	public function pafe_register_controls( $element, $args ) {

		$description = '';

		$element->start_controls_section(
			'pafe_next_prev_multi_step_form_section',
			[
				'label' => __( 'PAFE Next/Previous Multi Step Form', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_next_prev_multi_step_form',
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
			'pafe_next_prev_multi_step_form_action',
			[
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => __( 'Action', 'pafe' ),
				'options'     => [
					'next'    => __( 'Next', 'pafe' ),
					'prev'    => __( 'Previous', 'pafe' ),
				],
				'condition' => [
					'pafe_next_prev_multi_step_form' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_next_prev_multi_step_form'] ) && ! empty( $settings['pafe_next_prev_multi_step_form_action'] ) ) {

			$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
			$form_id = $pafe_forms ? get_the_ID() : '';
			$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;
		
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-form-builder-nav' => $settings['pafe_next_prev_multi_step_form_action'],
				'data-pafe-form-builder-nav-form-id' => $form_id,
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}