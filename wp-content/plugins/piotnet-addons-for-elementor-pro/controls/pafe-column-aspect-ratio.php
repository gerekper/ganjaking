<?php

class PAFE_Column_Aspect_Ratio extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-column-aspect-ratio';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_column_aspect_ratio_section',
			[
				'label' => __( 'PAFE Column Aspect Ratio', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_column_aspect_ratio_enable',
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
			'pafe_column_aspect_ratio',
			[
				'label' => __( 'Aspect Ratio', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => 'Aspect Ratio = Height / Width * 100. E.g Width = 100, Height = 100 => Ratio = 1; Width = 100, Height = 50 => Ratio = 50',
				'default' => 100,
				'selectors' => [
                    '{{WRAPPER}} .elementor-column-wrap::before,{{WRAPPER}} .elementor-widget-wrap::before' => 'content: ""; display: block; padding-top: {{VALUE}}%',
				],
				'condition' => [
					'pafe_column_aspect_ratio_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/column/section_typo/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
