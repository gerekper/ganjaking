<?php

class PAFE_Responsive_Gallery_Column_Width extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-responsive-gallery-column-width';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_responsive_gallery_column_width_section',
			[
				'label' => __( 'PAFE Responsive Gallery Column Width', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_responsive_gallery_column_width_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_responsive_gallery_column_width',
			[
				'label' => __( 'Column Width (%)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 50,
				'min' => 1,
				'max' => 100,
				'selectors' => [
					'{{WRAPPER}} .gallery-item' => 'width: {{VALUE}}%; max-width: 100%;',
				],
				'condition' => [
					'pafe_responsive_gallery_column_width_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/image-gallery/section_gallery/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-lightbox-gallery/section_gallery/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
