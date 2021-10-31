<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Excerpt Widget
 *
 * Porto Elementor widget to display product excerpt on the single product page when using custom product layout
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Excerpt_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_excerpt';
	}

	public function get_title() {
		return __( 'Product Excerpt', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'short', 'description' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_cp_excerpt',
			array(
				'label' => __( 'Product Excerpt', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} p',
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_excerpt( $settings );
		}
	}
}
