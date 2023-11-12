<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Equal_Height_For_Woocommerce_Products extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-equal-height-for-woocommerce-products';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_equal_height_for_woocommerce_products_section',
			[
				'label' => __( 'PAFE Equal Height For Woocommerce Products', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_equal_height_for_woocommerce_products_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => 'This feature only works on the frontend.',
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_equal_height_for_woocommerce_products_enable'] ) ) {

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-equal-height-for-woocommerce-products' => '',
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
