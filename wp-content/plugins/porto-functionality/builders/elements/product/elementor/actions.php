<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Custom Product Hooks Widget
 *
 * Porto Elementor widget to run default hooks on the single product page when using custom product layout
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;

class Porto_Elementor_CP_Actions_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cp_actions';
	}

	public function get_title() {
		return __( 'Product Hooks', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'custom-product' );
	}

	public function get_keywords() {
		return array( 'product', 'action', 'hook', 'count per page', 'pagination', 'grid', 'toggle' );
	}

	public function get_icon() {
		return 'eicon-product-info';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/single-product-builder-elements/';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js', 'easy-responsive-tabs' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cp_actions',
			array(
				'label' => __( 'Product Hooks', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'action',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Select an Action', 'porto-functionality' ),
				'options' => array(
					'woocommerce_before_single_product_summary' => 'woocommerce_before_single_product_summary',
					'woocommerce_single_product_summary' => 'woocommerce_single_product_summary',
					'woocommerce_after_single_product_summary' => 'woocommerce_after_single_product_summary',
					'porto_woocommerce_before_single_product_summary' => 'porto_woocommerce_before_single_product_summary',
					'porto_woocommerce_single_product_summary2' => 'porto_woocommerce_single_product_summary2',
					'woocommerce_share'                  => 'woocommerce_share',
					'porto_woocommerce_product_sticky_addcart' => 'porto_woocommerce_product_sticky_addcart',
				),
				'default' => 'woocommerce_single_product_summary',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'PortoCustomProduct' ) ) {
			echo PortoCustomProduct::get_instance()->shortcode_single_product_actions( $settings );
		}
	}
}
