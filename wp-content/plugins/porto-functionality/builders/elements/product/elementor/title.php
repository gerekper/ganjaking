<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Title Widget
 *
 * Porto Elementor widget to display product title on the single product page when using custom product layout
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Title_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_title';
	}

	public function get_title() {
		return __( 'Product Title', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'title' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_cp_title',
			array(
				'label' => __( 'Product Title', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_font',
				'scheme'   => Elementor\Scheme_Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .product_title',
			)
		);

		$this->add_control(
			'title_font_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .product_title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_title( $settings );
		}
	}
}
