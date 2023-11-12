<?php

class PAFE_Convert_Image_To_Black_Or_White extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-convert-image-to-black-or-white';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_convert_image_to_black_or_white_section',
			[
				'label' => __( 'PAFE Convert Image To Black Or White', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_convert_image_to_black',
			[
				'label' => __( 'Black', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .elementor-image img,{{WRAPPER}} .elementor-widget-container img' => '-webkit-filter: brightness(0) invert(0); filter: brightness(0) invert(0);',
                ],
			]
		);

		$element->add_control(
			'pafe_convert_image_to_white',
			[
				'label' => __( 'White', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .elementor-image img,{{WRAPPER}} .elementor-widget-container img' => '-webkit-filter: brightness(0) invert(1); filter: brightness(0) invert(1);',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/image/section_style_image/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
