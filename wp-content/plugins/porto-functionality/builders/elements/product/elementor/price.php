<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Price Widget
 *
 * Porto Elementor widget to display product price on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Price_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_price';
	}

	public function get_title() {
		return __( 'Product Price', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'price', 'cost' );
	}

	public function get_icon() {
		return 'eicon-product-price';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_price',
			array(
				'label' => __( 'Product Price', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .price',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'old_price_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Old Price Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_price( $settings );
		}
	}
}
