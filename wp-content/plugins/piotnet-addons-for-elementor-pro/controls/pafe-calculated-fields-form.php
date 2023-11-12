<?php

class PAFE_Calculated_Fields_Form extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-calculated-fields-form';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_calculated_fields_form',
			[
				'label' => __( 'PAFE Calculated Fields Form', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_calculated_fields_form_enable',
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

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_calculated_fields_form_typography',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .pafe-calculated-fields-form',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_calculated_fields_form_id',
			[
				'label' => __( 'Calculated Field Custom ID', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'pafe_calculated_fields_form_calculation',
			[
				'label' => __( 'Calculation', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g quantity*price+10', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_calculated_fields_form_before',
			[
				'label' => __( 'Before Content', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g $', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_calculated_fields_form_after',
			[
				'label' => __( 'After Content', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g $', 'pafe' ),
			]
		);

		$element->add_control(
			'pafe_calculated_fields_form_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pafe_calculated_fields_form_id }}} = {{{ pafe_calculated_fields_form_calculation }}}',
			)
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings();
		if (!empty($settings['pafe_calculated_fields_form_enable'])) {
			if ( array_key_exists( 'pafe_calculated_fields_form_list',$settings ) ) {
				$list = $settings['pafe_calculated_fields_form_list'];	
				if( !empty($list[0]['pafe_calculated_fields_form_id']) && !empty($list[0]['pafe_calculated_fields_form_calculation']) ) {

					$element->add_render_attribute( '_wrapper', [
						'data-pafe-calculated-fields-form' => json_encode($list),
					] );
				}
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/form/section_form_fields/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
