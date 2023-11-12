<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Form_Builder_Repeater_Trigger extends \Elementor\Widget_Base {
	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-form-builder-repeater-trigger';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_form_builder_repeater_trigger_section',
			[
				'label' => __( 'PAFE Form Builder Repeater Trigger', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_enable_trigger',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;

		$element->add_control(
			'pafe_form_builder_repeater_form_id_trigger',
			[
				'label' => __( 'Form ID* (Required)', 'pafe' ),
				'type' => $pafe_forms ? \Elementor\Controls_Manager::HIDDEN : \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
				'condition' => [
					'pafe_form_builder_repeater_enable_trigger' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_id_trigger',
			[
				'label' => __( 'Repeater ID* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter Repeater ID with latin character and no space. E.g products_repeater', 'pafe' ),
				'condition' => [
					'pafe_form_builder_repeater_enable_trigger' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_trigger_action',
			[
				'label' => __( 'Action', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'add' => __( 'Add', 'pafe' ),
					'remove' => __( 'Remove', 'pafe' ),
				],
				'default' => 'add',
				'condition' => [
					'pafe_form_builder_repeater_enable_trigger' => 'yes',
				],
			]
		);

		$element->end_controls_section();

	}

	public function before_render_element($element) {
		$settings = $element->get_settings_for_display();
		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
		$form_id = $pafe_forms ? get_the_ID() : $settings['pafe_form_builder_repeater_form_id_trigger'];
		$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;
        $form_id = !empty($form_id) ? $form_id : get_the_ID();

		if( !empty($settings['pafe_form_builder_repeater_enable_trigger']) && !empty($settings['pafe_form_builder_repeater_id_trigger']) && !empty($settings['pafe_form_builder_repeater_trigger_action']) ) { 
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-form-builder-repeater-form-id-trigger' => $form_id,
				'data-pafe-form-builder-repeater-id-trigger' => $settings['pafe_form_builder_repeater_id_trigger'],
				'data-pafe-form-builder-repeater-trigger-action' => $settings['pafe_form_builder_repeater_trigger_action'],
			] );

			wp_enqueue_script( 'pafe-form-builder-advanced-script' );
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
