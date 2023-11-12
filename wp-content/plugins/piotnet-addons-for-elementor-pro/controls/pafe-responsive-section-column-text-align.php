<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Responsive_Section_Column_Text_Align extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-section-column-text-align';
	}

	public function pafe_register_controls( $element, $args ) {

		$element_name = $element->get_name();

		$element->start_controls_section(
			'pafe_responsive_section_column_text_align_section',
			[
				'label' => __( 'PAFE Responsive Section Column Text Align', 'pafe' ),
				'tab' => $element_name != 'container' ? \Elementor\Controls_Manager::TAB_STYLE : PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_section_column_text_align',
			[
				'label' => __( 'Text Align', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} > .elementor-container' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} > .elementor-element-populated' => 'text-align: {{VALUE}};',
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_typo/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_typo/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/pafe_support_section/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
