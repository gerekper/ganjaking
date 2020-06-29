<?php


use Elementor\Controls_Manager;

class YITH_MAS_Custom_Addresses_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-mas-custom-addresses';
	}

	public function get_title() {
		return esc_html__( 'YITH Multiple Shipping Addresses - Show Custom Addresses', 'yith-multiple-shipping-addresses-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-site-identity';
	}

	public function get_categories() {
		return [ 'yith', 'woocommerce-elements-single' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'multiple', 'shipping', 'addresses', 'custom', 'customer' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'YITH Multiple Shipping Addresses - Show Custom Addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'This widget shows the custom addresses of the customer.', 'yith-multiple-shipping-addresses-for-woocommerce' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		wp_enqueue_script( 'ywcmas-frontend-my-account',
			YITH_WCMAS_ASSETS_JS_URL . yit_load_js_file( 'ywcmas-frontend-my-account.js' ),
			array( 'jquery' ),
			YITH_WCMAS_VERSION,
			'true'
		);
		//Localize scripts
		wp_localize_script( 'ywcmas-frontend-my-account', 'ywcmas_my_account_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
		wc_get_template( 'ywcmas-custom-addresses.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH . 'myaccount/' );
	}

}
