<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor_Widget_Fancy_Divider extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 */

	public function get_name() {
		return 'mfn_fancy_divider';
	}

	/**
	 * Get widget title
	 */

	public function get_title() {
		return __( 'Be • Fancy divider', 'mfn-opts' );
	}

	/**
	 * Get widget icon
	 */

	public function get_icon() {
		return 'fas fa-water';
	}

	/**
	 * Get widget categories
	 */

	public function get_categories() {
		return [ 'mfn_builder' ];
	}

	/**
	 * Register widget controls
	 */

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'mfn-opts' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::SELECT,
        'options' => array(
          'circle up' => __('Circle Up', 'mfn-opts'),
          'circle down' => __('Circle Down', 'mfn-opts'),
          'curve up' => __('Curve Up', 'mfn-opts'),
          'curve down' => __('Curve Down', 'mfn-opts'),
          'stamp' => __('Stamp', 'mfn-opts'),
          'triangle up'	=> __('Triangle Up', 'mfn-opts'),
          'triangle down'	=> __('Triangle Down', 'mfn-opts'),
        ),
        'default' => 'circle up',
			]
		);

    $this->add_control(
			'color_top',
			[
				'label' => __( 'Color top', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#ffffff',
			]
		);

    $this->add_control(
			'color_bottom',
			[
				'label' => __( 'Color bottom', 'mfn-opts' ),
				'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#2991d6',
			]
		);

		$this->end_controls_section();

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend
	 */

	protected function render() {

		$settings = $this->get_settings_for_display();

		echo sc_fancy_divider( $settings );

	}

}
