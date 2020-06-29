<?php


use Elementor\Controls_Manager;

class YITH_Request_Quote_Button_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-request-a-quote-button';
	}

	public function get_title() {
		return __( 'YITH Request a Quote Button', 'yith-woocommerce-request-a-quote' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'yith', 'woocommerce-elements-single' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'quote', 'request', 'add to cart' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'YITH Request a Quote Button', 'yith-woocommerce-request-a-quote' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( '%s [<a href="%s">%s</a>].', __( 'This widget inherits the style from the settings of YITH Request a Quote plugin that you can edit', 'yith-woocommerce-request-a-quote' ),
					get_admin_url( null, 'admin.php?page=yith_woocommerce_request_a_quote&tab=button' ), __('here', 'yith-woocommerce-request-a-quote' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'product',
			array(
				'label' => __( 'Product ID', 'yith-woocommerce-request-a-quote' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => defined('ELEMENTOR_PRO_VERSION'),
				),
				'default' => __( '', 'yith-woocommerce-request-a-quote' ),
				'placeholder' => __( 'Leave empty for single product page', 'yith-woocommerce-request-a-quote' ),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['product'] ) || $settings['product'] == '' ) {
			global $product;
		} else {
			$product = wc_get_product( $settings['product'] );
		}

		if( $product ){
			yith_ywraq_render_button( $product->get_id() );
		}

	}

}
