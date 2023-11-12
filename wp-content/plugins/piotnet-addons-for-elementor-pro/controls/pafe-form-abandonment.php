<?php

class PAFE_Form_Abandonment extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-form-abandonment';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_form_abandonment_section',
			[
				'label' => __( 'PAFE Form Abandonment', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_form_abandonment_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);
		$element->add_control(
			'pafe_form_abandonment_webhook_enable',
			[
				'label' => __( 'Enable Webhook', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_form_abandonment_enable' => 'yes'
				]
			]
		);
		$element->add_control(
			'pafe_form_abandonment_webhook_url',
			[
				'label' => __( 'Webhook URL', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Type your url here', 'pafe' ),
				'condition' => [
					'pafe_form_abandonment_enable' => 'yes',
					'pafe_form_abandonment_webhook_enable' => 'yes',
				]
			]
		);


		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_form_abandonment_enable'])) {
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-form-abandonment' => '',
			] );
			if(!empty($settings['pafe_form_abandonment_webhook_enable']) && !empty($settings['pafe_form_abandonment_webhook_url'])){
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-form-abandonment-webhook' => $settings['pafe_form_abandonment_webhook_url'],
				] );
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_form_fields/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-form-builder-submit/section_conditional_logic/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-multi-step-form/section_conditional_logic/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
