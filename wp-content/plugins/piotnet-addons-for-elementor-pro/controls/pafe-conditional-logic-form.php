<?php

class PAFE_Conditional_Logic_Form extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-conditional-logic-form';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_conditional_logic_form_section',
			[
				'label' => __( 'PAFE Conditional Logic Form', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_conditional_logic_form_enable',
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
			'pafe_conditional_logic_form_speed',
			[
				'label' => __( 'Speed', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 100, 1000, slow, fast' ),
				'default' => 400,
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_conditional_logic_form_easing',
			[
				'label' => __( 'Easing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g swing, linear' ),
				'default' => 'swing',
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_conditional_logic_form_show',
			[
				'label' => __( 'Show', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Field Custom ID', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_if',
			[
				'label' => __( 'If', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Field Custom ID', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_comparison_operators',
			[
				'label' => __( 'Comparison Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'not-empty' => __( 'not empty', 'pafe' ),
					'empty' => __( 'empty', 'pafe' ),
					'=' => __( 'equals', 'pafe' ),
					'!=' => __( 'not equals', 'pafe' ),
					'>' => __( '>', 'pafe' ),
					'>=' => __( '>=', 'pafe' ),
					'<' => __( '<', 'pafe' ),
					'<=' => __( '<=', 'pafe' ),
					'checked' => __( 'checked', 'pafe' ),
					'unchecked' => __( 'unchecked', 'pafe' ),
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_type',
			[
				'label' => __( 'Type Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'string' => __( 'String', 'pafe' ),
					'number' => __( 'Number', 'pafe' ),
				],
				'default' => 'string',
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<='],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( '50', 'pafe' ),
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<='],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_and_or_operators',
			[
				'label' => __( 'OR, AND Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'or' => __( 'OR', 'pafe' ),
					'and' => __( 'AND', 'pafe' ),
				],
				'default' => 'or',
			]
		);

		if ($element->get_name() == 'form') {
			$repeater->add_control(
				'pafe_conditional_logic_form_required_field',
				[
					'label' => __( 'Required Field', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
                    'description' => __( 'Please do not enable "Required" in Form Fields Setting if this field is mandatory', 'pafe' ),
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);
		}

		$element->add_control(
			'pafe_conditional_logic_form_list',
			[
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => 'Show {{{ pafe_conditional_logic_form_show }}} If {{{ pafe_conditional_logic_form_if }}} {{{ pafe_conditional_logic_form_comparison_operators }}} {{{ pafe_conditional_logic_form_value }}}',
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				]
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_conditional_logic_form_enable'])) {
			if ( array_key_exists( 'pafe_conditional_logic_form_list',$settings ) ) {
				$list = $settings['pafe_conditional_logic_form_list'];	
				if( !empty($list[0]['pafe_conditional_logic_form_show']) && !empty($list[0]['pafe_conditional_logic_form_if']) && !empty($list[0]['pafe_conditional_logic_form_comparison_operators']) ) {

					$element->add_render_attribute( '_wrapper', [
						'data-pafe-conditional-logic-form' => json_encode($list),
						'data-pafe-conditional-logic-form-speed' => $settings['pafe_conditional_logic_form_speed'],
						'data-pafe-conditional-logic-form-easing' => $settings['pafe_conditional_logic_form_easing'],
					] );

					if (!empty($settings['mark_required'])) {
						$element->add_render_attribute( '_wrapper', [
							'data-pafe-conditional-logic-form-mark-required' => '',
						] );
					}
				}

				wp_enqueue_script( 'pafe-form-builder-advanced-script' );
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_form_fields/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
