<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Description Widget
 *
 * Porto Elementor widget to display product description on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Description_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_description';
	}

	public function get_title() {
		return __( 'Product Description', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'content', 'description' );
	}

	public function get_icon() {
		return 'eicon-product-description';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_description',
			array(
				'label' => __( 'Product Description', 'porto-functionality' ),
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'description_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} p, {{WRAPPER}}',
				)
			);

			$this->add_control(
				'description_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} p, {{WRAPPER}}' => 'color: {{VALUE}};',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_description( $settings );
		}
	}
}
