<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Tabs Widget
 *
 * Porto Elementor widget to display product tabs on the single product page when using custom product layout
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Tabs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_tabs';
	}

	public function get_title() {
		return __( 'Product Tabs', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'tabs' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_cp_tabs',
			array(
				'label' => __( 'Product Tabs', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_tabs( $settings );
		}
	}
}
