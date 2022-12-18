<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Add to Cart Sticky Widget
 *
 * Porto Elementor widget to display sticky add to cart content
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Addcart_sticky_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_addcart_sticky';
	}

	public function get_title() {
		return __( 'Sticky Add To Cart', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'sticky', 'cart', 'add' );
	}

	public function get_icon() {
		return 'eicon-cart';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_addcart_sticky',
			array(
				'label' => __( 'Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'pos',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Position', 'porto-functionality' ),
				'options' => array(
					''       => __( 'Top', 'porto-functionality' ),
					'bottom' => __( 'Bottom', 'porto-functionality' ),
				),
				'default' => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cp_addcart_sticky_style',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Product Title Font', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .product-name',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Product Title Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .product-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Product Price Font', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .price',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Product Price Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'av_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Availability Font', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .availability',
			)
		);

		$this->add_control(
			'av_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Availability Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .availability' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_font',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Button Font', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .button',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			$settings['page_builder'] = 'elementor';
			echo PortoCustomProduct::get_instance()->shortcode_single_product_addcart_sticky( $settings );
		}
	}
}
