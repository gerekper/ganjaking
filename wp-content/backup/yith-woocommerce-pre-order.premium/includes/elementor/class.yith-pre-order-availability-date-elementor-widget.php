<?php


use Elementor\Controls_Manager;

class YITH_Pre_Order_Availability_Date_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-pre-order-availability-date';
	}

	public function get_title() {
		return esc_html__( 'YITH Pre-Order - Availability Date', 'yith-pre-order-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-product-stock';
	}

	public function get_categories() {
		return [ 'yith', 'woocommerce-elements-single' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'pre-order', 'availability', 'stock', 'arrival', 'date' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'YITH Pre-Order - Availability Date', 'yith-pre-order-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Enter the Pre-Order product ID for displaying its availability date.', 'yith-pre-order-for-woocommerce' )
			)
		);

		$this->add_control(
			'product_id',
			array(
				'label' => esc_html__( 'Pre-Order product ID', 'yith-pre-order-for-woocommerce' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'ID', 'yith-pre-order-for-woocommerce' ),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		wp_enqueue_style( 'wcpo-frontend', YITH_WCPO_ASSETS_URL . 'css/frontend.css', array(), YITH_WCPO_VERSION );
		wp_enqueue_script( 'yith-wcpo-frontend-single-product' );
		if ( class_exists( 'YITH_Pre_Order_Frontend_Premium' ) ) {
			echo YITH_Pre_Order_Premium::instance()->frontend->availability_date_shortcode( $settings );
		}
	}

}
