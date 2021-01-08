<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Add to Cart Widget
 *
 * Porto Elementor widget to display "add to cart" button on the single product page when using custom product layout
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Add_to_cart_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_add_to_cart';
	}

	public function get_title() {
		return __( 'Product Add To Cart', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'cart', 'add_to_cart' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_cp_add_to_cart',
			array(
				'label' => __( 'Product Add To Cart', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_add_to_cart( $settings );
		}
	}
}
