<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Next Prev navigation Widget
 *
 * Porto Elementor widget to display next and prev product navigation on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Next_prev_nav_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_next_prev_nav';
	}

	public function get_title() {
		return __( 'Prev and Next Navigation', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'nav', 'next', 'prev' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_next_prev_nav',
			array(
				'label' => __( 'Prev and Next Navigation', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'nav_color',
				array(
					'label'     => __( 'Nav Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .product-link' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'nav_bg_color',
				array(
					'label'     => __( 'Background Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .product-link' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'nav_border_color',
				array(
					'label'     => __( 'Border Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .product-link' => 'border-color: {{VALUE}}',
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'dropdown_padding',
				array(
					'label'     => __( 'Dropdown Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .featured-box .box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_next_prev_nav( $settings );
		}
	}
}
