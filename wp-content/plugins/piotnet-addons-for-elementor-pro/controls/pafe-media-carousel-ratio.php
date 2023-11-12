<?php

class PAFE_Media_Carousel_Ratio extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-media-carousel-ratio';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_media_carousel_ratio_section',
			[
				'label' => __( 'PAFE Media Carousel Aspect Ratio', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_media_carousel_ratio_enable',
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
			'pafe_media_carousel_ratio',
			[
				'label' => __( 'Ratio', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => 'Aspect Ratio = Width / Height * 100. E.g Width = 100, Height = 100 => Ratio = 1; Width = 100, Height = 50 => Ratio = 50',
				'default' => 100,
				'selectors' => [
					'{{WRAPPER}} .elementor-carousel-image::before' => 'content: ""; display: block; padding-top: {{VALUE}}%',
					'{{WRAPPER}} .elementor-main-swiper' => 'height: auto',
				],
				'condition' => [
					'pafe_media_carousel_ratio_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/media-carousel/section_additional_options/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
