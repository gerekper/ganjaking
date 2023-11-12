<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Form_Builder_Repeater extends \Elementor\Widget_Base {
	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-form-builder-repeater';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_form_builder_repeater_section',
			[
				'label' => __( 'PAFE Form Builder Repeater', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_enable',
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
			'pafe_form_builder_repeater_form_id',
			[
				'label' => __( 'Form ID* (Required)', 'pafe' ),
				'type' => $pafe_forms ? \Elementor\Controls_Manager::HIDDEN : \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
				'condition' => [
					'pafe_form_builder_repeater_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_id',
			[
				'label' => __( 'Repeater ID* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter Repeater ID with latin character and no space, no comma. E.g products_repeater', 'pafe' ),
				'condition' => [
					'pafe_form_builder_repeater_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_label',
			[
				'label' => __( 'Repeater Label* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'pafe_form_builder_repeater_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_limit',
			[
				'label' => __( 'Limit number of elements', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'pafe_form_builder_repeater_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_form_builder_repeater_shortcode',
			[
				'label' => __( 'Click here to get the Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'classes' => 'pafe-forms-field-shortcode pafe-forms-field-shortcode--repeater',
				'condition' => [
					'pafe_form_builder_repeater_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();

	}

	public function before_render_element($element) {
		$settings = $element->get_settings_for_display();
		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
		$form_id = $pafe_forms ? get_the_ID() : $settings['pafe_form_builder_repeater_form_id'];
		$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;
        $form_id = !empty($form_id) ? $form_id : get_the_ID();

		if( !empty($settings['pafe_form_builder_repeater_enable']) && !empty($settings['pafe_form_builder_repeater_id']) && !empty($settings['pafe_form_builder_repeater_label']) ) { 
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-form-builder-repeater-form-id' => $form_id,
				'data-pafe-form-builder-repeater-id' => $settings['pafe_form_builder_repeater_id'],
				'data-pafe-form-builder-repeater-label' => $settings['pafe_form_builder_repeater_label'],
				'data-pafe-form-builder-repeater-limit' => $settings['pafe_form_builder_repeater_limit'],
			] );
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );

		add_action( 'elementor/element/column/layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
