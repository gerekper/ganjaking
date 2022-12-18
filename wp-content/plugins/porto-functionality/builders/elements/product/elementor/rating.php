<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Rating Widget
 *
 * Porto Elementor widget to display review ratings on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Rating_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_rating';
	}

	public function get_title() {
		return __( 'Product Rating', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'review', 'stars', 'feedback' );
	}

	public function get_icon() {
		return 'eicon-product-rating';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_rating',
			array(
				'label' => __( 'Product Rating', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'rating_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Rating Size', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 60,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .woocommerce-product-rating .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'rating_bgcolor',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Star Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .star-rating:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'rating_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Active Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .star-rating span:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'rating_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Review Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .review-link',
			)
		);

		$this->add_control(
			'review_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Review Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .review-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide Separator', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .woocommerce-product-rating:after' => 'content: none;',
				),
			)
		);

		$this->add_control(
			'flex_direction',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Direction', 'porto-functionality' ),
				'description' => __( 'Controls the direction: horizontal, vertical', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .review-link' => 'display: block;',
				),
			)
		);

		$this->add_control(
			'between_spacing',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Between Spacing', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 60,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .review-link' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'flex_direction' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_rating( $settings );
		}
	}
}
