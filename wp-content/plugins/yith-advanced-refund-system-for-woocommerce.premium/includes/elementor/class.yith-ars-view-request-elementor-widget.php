<?php


use Elementor\Controls_Manager;

class YITH_ARS_View_Request_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-ars-view-request';
	}

	public function get_title() {
		return esc_html__( 'YITH Advanced Refund System - View Request', 'yith-advanced-refund-system-for-woocommerce' );
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
				'label' => esc_html__( 'YITH Advanced Refund System - View Request', 'yith-advanced-refund-system-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Enter the ID of the refund request to be displayed.', 'yith-advanced-refund-system-for-woocommerce' )
			)
		);

		$this->add_control(
			'request-id',
			array(
				'label' => esc_html__( 'Refund Request ID', 'yith-advanced-refund-system-for-woocommerce' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Request ID', 'yith-advanced-refund-system-for-woocommerce' ),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['request-id'] ) ) {
			// Enqueue styles and scripts
			wp_enqueue_style( 'ywcars-frontend',
				YITH_WCARS_ASSETS_URL . 'css/ywcars-frontend.css',
				array(),
				YITH_WCARS_VERSION
			);
			wp_enqueue_style( 'ywcars-common',
				YITH_WCARS_ASSETS_URL . 'css/ywcars-common.css',
				array(),
				YITH_WCARS_VERSION
			);
			wp_enqueue_script( 'ywcars-frontend',
				YITH_WCARS_ASSETS_JS_URL . yit_load_js_file( 'ywcars-frontend.js' ),
				array( 'jquery' ),
				YITH_WCARS_VERSION,
				'true'
			);
			wp_localize_script( 'ywcars-frontend', 'localize_js_ywcars_frontend',
				array(
					'ajax_url'               => admin_url( 'admin-ajax.php', apply_filters( 'ywcars_ajax_url_scheme_frontend', '' ) ),
					'ywcars_submit_request'  => wp_create_nonce( 'ywcars-submit-request' ),
					'ywcars_submit_message'  => wp_create_nonce( 'ywcars-submit-message' ),
					'ywcars_update_messages' => wp_create_nonce( 'ywcars-update-messages' ),
					'reloading'              => esc_html__( 'Reloading...', 'yith-advanced-refund-system-for-woocommerce' ),
					'success_message'        => esc_html__( 'Message submitted successfully', 'yith-advanced-refund-system-for-woocommerce' ),
					'fill_fields'            => esc_html__( 'Please fill in with all required information',
						'yith-advanced-refund-system-for-woocommerce' ),
					'redirect_url'           => apply_filters( 'ywcars_submit_request_redirect_url', 'current-url' )
				)
			);

			YITH_Advanced_Refund_System_My_Account::get_instance()->view_request_shortcode( array( 'id' => $settings['request-id'] ) );
		}

	}

}
