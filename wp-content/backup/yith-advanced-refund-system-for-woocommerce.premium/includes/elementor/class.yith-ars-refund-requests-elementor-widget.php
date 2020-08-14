<?php


use Elementor\Controls_Manager;

class YITH_ARS_Refund_Requests_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-ars-refund-requests';
	}

	public function get_title() {
		return esc_html__( 'YITH Advanced Refund System - Refund Requests', 'yith-advanced-refund-system-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'yith', 'woocommerce-elements-single' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'refund', 'request', 'myaccount' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'YITH Advanced Refund System - Refund Requests', 'yith-advanced-refund-system-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'This widget shows the current refund requests made by the customer', 'yith-advanced-refund-system-for-woocommerce' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		echo YITH_Advanced_Refund_System_My_Account::get_instance()->refund_requests_shortcode( false );
	}

}
