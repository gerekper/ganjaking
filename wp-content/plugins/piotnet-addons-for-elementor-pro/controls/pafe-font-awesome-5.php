<?php

class PAFE_Font_Awesome_5 extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-font-awesome-5';
	}

	public function pafe_register_controls( $element, $args ) {

		$description = '';

		$element->start_controls_section(
			'pafe_font_awesome_5_section',
			[
				'label' => __( 'PAFE Font Awesome 5', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_font_awesome_5_enable',
			[
				'label' => __( 'Enable Font Awesome 5', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_font_awesome_5',
			[
				'label' => __( 'Font Awesome 5 Icon Class', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'E.g fas fa-address-book <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">View all free icons</a>',
				'condition' => [
					'pafe_font_awesome_5_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_font_awesome_5_enable'] ) ) {

			if ( ! empty( $settings['pafe_font_awesome_5'] ) ) {

				$element->add_render_attribute( '_wrapper', [
					'data-pafe-font-awesome-5' => $settings['pafe_font_awesome_5'],
				] );

			}

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/icon/section_icon/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/icon-box/section_icon/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
