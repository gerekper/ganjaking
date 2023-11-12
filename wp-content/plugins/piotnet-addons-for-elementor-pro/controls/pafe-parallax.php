<?php

class PAFE_Parallax extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-parallax';
	}

	public function get_script_depends() {
		return [ 
			'pafe-parallax'
		];
	}

	// public function ext_enqueue_scripts() {
	// 	$post_id = get_the_ID();
	// 	$elementor_data = get_post_meta( $post_id, '_elementor_data', true);
	// 	if (strpos($elementor_data, 'pafe_parallax_enable') !== false) {
	// 		wp_enqueue_script( 'pafe-parallax-library' );
	// 		wp_enqueue_script( 'pafe-parallax' );
	// 	}
	// }

	public function pafe_register_controls( $element, $args ) {

		$description = __( 'Note that currently parallax are not visible in edit/preview mode & can only be viewed on the frontend.', 'pafe' );

		$element->add_control(
			'pafe_parallax_enable',
			[
				'label' => __( 'Enable PAFE Parallax', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => $description,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_parallax_enable_new_version',
			[
				'label' => __( 'Enable PAFE Parallax New Version', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'Parallax effect will work on iPhone / iPad but it does not look good', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_parallax_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_parallax_speed',
			[
				'label' => __( 'Speed', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Speed of the parallax. You can use any floating point number. But practically, give any fractional number between 0 and 1. Example: 0.2 or 0.5', 'pafe' ),
				'default' => 0.2,
				'min' => 0,
				'max' => 1,
				'step' => 0.1,
				'condition' => [
					'pafe_parallax_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_parallax_bleed',
			[
				'label' => __( 'Offset', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'The parallax offset. Possible values: Any integer.', 'pafe' ),
				'default' => 0,
				'condition' => [
					'pafe_parallax_enable' => 'yes',
				],
			]
		);
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_parallax_enable'] ) ) {

			$class = 'pafe-parallax-section';
			$type = $element->get_type();

			if( $type == 'widget' ) {
				$class = 'pafe-parallax-widget';
			}
			if( $type == 'column' ) {
				$class = 'pafe-parallax-column';
			}

			$element->add_render_attribute( '_wrapper', [
				'class' => $class . ' pafe-parallax',
				'data-pafe-parallax-speed' => $settings['pafe_parallax_speed'],
				'data-pafe-parallax-bleed' => $settings['pafe_parallax_bleed'],
			] );

			if ( ! empty( $settings['pafe_parallax_enable_new_version'] ) ) {
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-parallax-new-version' => '',
				] );
			}

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_background/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_background/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_style/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/before_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
		
		//add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'ext_enqueue_scripts' ), 9 );
	}

}
