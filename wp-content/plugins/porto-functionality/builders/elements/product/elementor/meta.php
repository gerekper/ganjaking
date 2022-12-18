<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Meta Widget
 *
 * Porto Elementor widget to display product meta on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_meta_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_meta';
	}

	public function get_title() {
		return __( 'Product Meta', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'meta' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	protected function register_controls() {

		$left = is_rtl() ? 'right' : 'left';

		$this->start_controls_section(
			'section_cp_meta',
			array(
				'label' => __( 'Product Meta', 'porto-functionality' ),
			)
		);
			$this->add_control(
				'view',
				array(
					'type'      => Controls_Manager::SELECT,
					'label'     => __( 'View Mode', 'porto-functionality' ),
					'options'   => array(
						'block' => __( 'Stacked', 'porto-functionality' ),
						'flex'  => __( 'Inline', 'porto-functionality' ),
					),
					'selectors' => array(
						'.elementor-element-{{ID}} .product_meta' => 'display: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'spacing1',
				array(
					'type'      => Controls_Manager::SLIDER,
					'label'     => __( 'Spacing', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .product_meta>*' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'view' => 'block',
					),
				)
			);

			$this->add_control(
				'spacing2',
				array(
					'type'      => Controls_Manager::SLIDER,
					'label'     => __( 'Spacing', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .product_meta>*+*' => "margin-{$left}: {{SIZE}}{{UNIT}};margin-bottom: 0;",
						'.elementor-element-{{ID}} .product_meta>*' => 'margin-bottom: 0;',
					),
					'condition' => array(
						'view!' => 'block',
					),
				)
			);

			$this->add_control(
				'text_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Text Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .product_meta' => 'color: {{VALUE}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'text_size',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Text Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .product_meta',
				)
			);

			$this->add_control(
				'link_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Link Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .product_meta a' => 'color: {{VALUE}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'link_hover_color',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Link Hover Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .product_meta a:hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'link_size',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Link Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .product_meta a',
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_meta( $settings );
		}
	}
}
