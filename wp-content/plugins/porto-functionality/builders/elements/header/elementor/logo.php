<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Header Builder logo widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Logo_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_logo';
	}

	public function get_title() {
		return __( 'Logo', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'header', 'logo', 'brand' );
	}

	public function get_icon() {
		return 'porto-icon-circle-thin';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_logo',
			array(
				'label' => __( 'Logo', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo porto_logo();
	}
}
