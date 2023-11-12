<?php

class PAFE_Lightbox extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-lightbox';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_lightbox',
			[
				'label' => __( 'PAFE Lightbox', 'pafe' ),
				'tab' => Plugin::$instance->kits_manager->get_active_kit_for_frontend()::PANEL_TAB_LIGHTBOX,
			]
		);

		$element->add_control(
			'elementor_pafe_lightbox_title',
			[
				'label' => __( 'Image Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'title' => __( 'Image Title', 'pafe' ),
					'hide' => __( 'Hide', 'pafe' ),
				],
				'default' => 'title',
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/global-settings/lightbox/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
